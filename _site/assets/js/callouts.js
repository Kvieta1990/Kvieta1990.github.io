// Turns ":::type ... :::" fenced blocks in posts into colored callout boxes.
// Supported types: info, warning, dangerous. Usage in markdown:
//
//   :::info
//   Body text, **markdown**, lists, math, code blocks...
//   :::
//
// kramdown renders the fences as plain text inside paragraphs, so we find the
// opening/closing markers in the rendered HTML and wrap everything in between.
document.addEventListener('DOMContentLoaded', function () {
  var OPEN_RE = /^\s*:::(info|warning|dangerous)\b[ \t]*\n?/;
  var CLOSE_RE = /\n?[ \t]*:::\s*$/;

  function textNodes(el) {
    var nodes = [];
    var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      if (walker.currentNode.nodeValue.trim() !== '') nodes.push(walker.currentNode);
    }
    return nodes;
  }

  function isEmpty(el) {
    return el.textContent.trim() === '' && !el.querySelector('img, svg, iframe, video');
  }

  function openingNode(el) {
    var first = el.tagName === 'P' ? textNodes(el)[0] : null;
    return first && first.parentNode === el && OPEN_RE.test(first.nodeValue) ? first : null;
  }

  function isOpening(el) {
    return openingNode(el) !== null;
  }

  // A "$$...$$" block directly followed by ":::" shares a paragraph with the
  // marker, so kramdown emits it as inline math \(...\). Once the marker is
  // gone and the math is alone in its paragraph, make it display math again.
  function restoreDisplayMath(el) {
    if (el.tagName !== 'P' || el.children.length) return;
    var m = /^\s*\\\(([\s\S]*)\\\)\s*$/.exec(el.textContent);
    if (m) el.textContent = '\\[' + m[1] + '\\]';
  }

  document.querySelectorAll('.blog-post').forEach(function (post) {
    var el = post.firstElementChild;
    while (el) {
      var next = el.nextElementSibling;
      var first = openingNode(el);
      if (!first) { el = next; continue; }
      var match = OPEN_RE.exec(first.nodeValue);

      // Strip the opening marker.
      first.nodeValue = first.nodeValue.replace(OPEN_RE, '');

      var box = document.createElement('div');
      box.className = 'callout callout-' + match[1];
      post.insertBefore(box, el);

      // Move nodes into the box until an element ends with the closing marker.
      // The marker may land inside the last list item when there is no blank
      // line after a list, so check the last text of any non-code element.
      // Bare text nodes (kramdown emits display math as \[...\] text) move too.
      var cur = el;
      while (cur) {
        var after = cur.nextSibling;
        if (cur.nodeType !== Node.ELEMENT_NODE) {
          box.appendChild(cur);
          cur = after;
          continue;
        }
        // Never swallow the next callout if this one was left unclosed.
        if (cur !== el && isOpening(cur)) break;
        var nodes = /^(PRE|DIV)$/.test(cur.tagName) ? [] : textNodes(cur);
        var last = nodes[nodes.length - 1];
        var closes = last && CLOSE_RE.test(last.nodeValue);
        if (closes) {
          last.nodeValue = last.nodeValue.replace(CLOSE_RE, '');
          restoreDisplayMath(cur);
        }
        if (isEmpty(cur)) cur.remove(); else box.appendChild(cur);
        if (closes) break;
        cur = after;
      }
      el = box.nextElementSibling;
    }
  });
});
