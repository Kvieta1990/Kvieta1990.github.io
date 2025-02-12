---
layout: post
title: Notes on Jekyll
subtitle: building site with Jekyll locally
tags: [programming, web development,]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/jekyll.png"
   style="border:none;"
   alt="jekyll"
   title="jekyll" />
<br />
</p>

<p style='text-align: justify'>
One can follow Ref. [1] to build up site with Jekyll - it contains quite a few steps concerning the set-up of sites through GitHub pages. However, this is not necessary if one just wants to set up site locally and test. Therefore, after installing Ruby and Bundle, one can directly create a local folder and go into it. From there, one needs to follow the steps given below,

<br />
<br />

1. Run `bundle init` - this will create the Gemfile.

<br />

2. According to Ref. [1], GitHub pages depends on certain version of packages (a link can be found there in Ref. [1] about details) and therefore one may need to install certain version of Jekyll with Bundle (though, again this may not be necessary if one only wants to do it locally). To install a certain version of Jekyll with Bundle, one need to put in the following line into Gemfile,
</p>

<blockquote cite="">
gem 'jekyll', '3.9.0'
</blockquote>

<p style='text-align: justify'>
3. Then one needs to execute bundle install to install the required version of Jekyll.

<br />

4. At this stage if one continues, as instructed in Ref. [1], to execute `bundle exec jekyll new .`, most likely error will occur, complaining that the directory is not empty (since we created the Gemfile in previous step for installing the right version of Jekyll). In this case, one just executes the following command, `bundle exec jekyll new --force .`, which will by force overwrite the already existing Gemfile.

<br />

5. To test locally, one simply executes the following command, `bundle exec jekyll serve` to start the service, where one should be able to see the localhost that we need to open in browser to check our template site!

<br />

More reference materials can be found in Refs. [2-4].
</p>

<br />

<b>Typical issues - 1</b>

```
Load error: cannot load such file – webrick
```

<u>Solution</u>

Run the command, `bundle add webrick`.

---

<b>Google search indexing for our website</b>

- First we need to generate a sitemap.xml for our website. To do this, we can
following the instruction provided in Ref. [5]. Basically, we need to manually
generate an `sitemap.xml` file under the root directory of the Jekyll site. Then
either running `bundle exec jekyll serve` to build the website locally or pushing
the codes to GitHub where the whole website will be generated automatically, the
full `sitemap.xml` file will be automatically generated under the `_site` directory,
according to the template we provided in the original `sitemap.xml` file (under
the root directory). As a reference, we could refer to template `sitemap.xml` available <a href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/sitemap.xml" target="_blank">here</a> for
current website (built with Jekyll and hosted on GitHub pages).

- Once the `sitemap.xml` file is available on the website, e.g., at `https://iris2020.net/sitemap.xml`
for current website, we can go to the <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a>, and add a property
for our website (if it is not already existing),

<p align='center'>
<img src="/assets/img/posts/gsc_ap.png"
   style="border:none;"
   alt="gsc1"
   title=gsc1 />
<br />
</p>

- Once the property for our website is created, select it from the top-left corner
dropdown menu as presented in the image above. Then we go to `Sitemaps` under
`Indexing` in the option list on the left,

<p align='center'>
<img src="/assets/img/posts/gsc_smap.png"
   style="border:none;"
   alt="gsc2"
   title=gsc2/>
<br />
</p>

- Here we can add in the full URL to the sitemap of our website, e.g., `https://iris2020.net/sitemap.xml` in
the input box on the top, if the sitemap is not already added (we can see a full list of submitted sitemaps),

<p align='center'>
<img src="/assets/img/posts/gsc_smap1.png"
   style="border:none;"
   alt="gsc3"
   title=gsc3/>
<br />
</p>

- Then, we want to submit the request for Google to (re)crawl our website, by going to
`URL inspection` (on the left of the console) and put in the URL of our website in the search box on the top,

<p align='center'>
<img src="/assets/img/posts/gsc_ui.png"
   style="border:none;"
   alt="gsc4"
   title=gsc4/>
<br />
</p>

- Finally, we want to click on the `REQUEST INDEXING` to submit the (re)crawling
request to Google and it will take several days or weeks for Google go (re)index
our website so that it can be better searched with Google,

<p align='center'>
<img src="/assets/img/posts/gsc_indexing.png"
   style="border:none;"
   alt="gsc4"
   title=gsc4/>
<br />
</p>

---

<br />

<b>References<b/>

[1] [https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/creating-a-github-pages-site-with-jekyll](https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/creating-a-github-pages-site-with-jekyll)

[2] [https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/testing-your-github-pages-site-locally-with-jekyll](https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/testing-your-github-pages-site-locally-with-jekyll)

[3] [https://pages.github.com/versions/](https://pages.github.com/versions/)

[4] [https://bundler.io/gemfile.html](https://bundler.io/gemfile.html)

[5] [https://www.independent-software.com/generating-a-sitemap-xml-with-jekyll-without-a-plugin.html](https://www.independent-software.com/generating-a-sitemap-xml-with-jekyll-without-a-plugin.html)