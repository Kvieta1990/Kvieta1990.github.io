'use strict';

/**
 * Indexes all Jekyll _posts into a Meilisearch instance.
 *
 * Required environment variables:
 *   MEILISEARCH_HOST    – URL of the Meilisearch instance
 *                         e.g. https://meili.iris-2020.us
 *   MEILISEARCH_API_KEY – An API key with write access to the 'posts' index
 *
 * Usage:
 *   node _scripts/index_meilisearch.js
 */

const fs   = require('fs');
const path = require('path');
const fm   = require('front-matter');
const { MeiliSearch } = require('meilisearch');

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------
const SITE_BASE_URL = 'https://iris2020.net';
const POSTS_DIR     = path.join(__dirname, '..', '_posts');
const INDEX_NAME    = 'posts';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Derives a post URL from its filename.
 * Jekyll permalink format: /:year-:month-:day-:title/
 * Filename format:         YYYY-MM-DD-title-slug.md
 */
function filenameToUrl(filename) {
  const slug = path.basename(filename, '.md');
  return SITE_BASE_URL + '/' + slug + '/';
}

/**
 * Strips Liquid tags, HTML, and common Markdown syntax so that only
 * plain readable text is sent to Meilisearch.
 */
function stripMarkdown(raw) {
  return raw
    // Liquid block/inline tags  {% ... %}  {{ ... }}
    .replace(/\{%-?[\s\S]*?-?%\}/g, ' ')
    .replace(/\{\{[\s\S]*?\}\}/g, ' ')
    // HTML tags
    .replace(/<[^>]+>/g, ' ')
    // Fenced code blocks  ``` ... ```
    .replace(/```[\s\S]*?```/g, ' ')
    // Inline code  `...`
    .replace(/`[^`\n]+`/g, ' ')
    // Display math  $$ ... $$
    .replace(/\$\$[\s\S]*?\$\$/g, ' ')
    // Inline math  $ ... $
    .replace(/\$[^\n$]+\$/g, ' ')
    // ATX headings  # Heading
    .replace(/^#{1,6}\s+/gm, '')
    // Bold / italic  **text**  *text*  __text__  _text_
    .replace(/[*_]{1,3}([^*_\n]+)[*_]{1,3}/g, '$1')
    // Images  ![alt](url)
    .replace(/!\[[^\]]*\]\([^)]*\)/g, '')
    // Links  [text](url)
    .replace(/\[([^\]]+)\]\([^)]*\)/g, '$1')
    // Horizontal rules
    .replace(/^[-*_]{3,}\s*$/gm, '')
    // Blockquote markers
    .replace(/^>\s?/gm, '')
    // Collapse whitespace
    .replace(/[ \t]+/g, ' ')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------
async function main() {
  const host   = process.env.MEILISEARCH_HOST;
  const apiKey = process.env.MEILISEARCH_API_KEY;

  if (!host || !apiKey) {
    console.error(
      'Error: MEILISEARCH_HOST and MEILISEARCH_API_KEY environment variables are required.'
    );
    process.exit(1);
  }

  const client = new MeiliSearch({ host, apiKey });

  // -- Parse posts ----------------------------------------------------------
  const files     = fs.readdirSync(POSTS_DIR).filter(f => f.endsWith('.md'));
  const documents = [];

  for (const file of files) {
    const fullPath = path.join(POSTS_DIR, file);
    let parsed;
    try {
      const raw = fs.readFileSync(fullPath, 'utf8');
      parsed    = fm(raw);
    } catch (e) {
      console.warn(`Skipping ${file}: ${e.message}`);
      continue;
    }

    const { title, date, tags, subtitle } = parsed.attributes;
    if (!title) {
      console.warn(`Skipping ${file}: missing title in front matter`);
      continue;
    }

    documents.push({
      id:       path.basename(file, '.md'),            // unique, stable
      title:    title,
      subtitle: subtitle || '',
      url:      filenameToUrl(file),
      date:     date ? new Date(date).toISOString().split('T')[0] : '',
      tags:     Array.isArray(tags) ? tags : (tags ? [String(tags)] : []),
      content:  stripMarkdown(parsed.body).substring(0, 8000),
    });
  }

  console.log(`Parsed ${documents.length} post(s) from ${POSTS_DIR}`);

  // -- Configure index ------------------------------------------------------
  const index = client.index(INDEX_NAME);

  await index.updateSettings({
    searchableAttributes: ['title', 'subtitle', 'content', 'tags'],
    displayedAttributes:  ['id', 'title', 'subtitle', 'url', 'date', 'tags', 'content'],
    sortableAttributes:   ['date'],
    rankingRules: [
      'words',
      'typo',
      'proximity',
      'attribute',
      'sort',
      'exactness',
    ],
  });

  // -- Upload documents -----------------------------------------------------
  const task = await index.addDocuments(documents, { primaryKey: 'id' });
  console.log(
    `Task enqueued (uid: ${task.taskUid}). Indexed ${documents.length} documents into '${INDEX_NAME}'.`
  );
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
