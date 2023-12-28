---
layout: post
title: Notes on implementing search in Jekyll site
subtitle:
tags: [programming, web development]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/jekyll.png"
   style="border:none;"
   width="300"
   alt="jekyll"
   title="jekyll" />
</p>

The commonly used method for implementing search capability in jekyll site with
lunr does not seem to be working specifically in my case (namely the current
website that we are staring at now). Trying to Google a bit and tring a few
suggested solutions such as changing the version of jekyll, etc., and still the
issue is there. Coming across the Ref. [1], there are listed three options to
encode the search capability in jekyll site -- the lunr method is the first one
in the list. It seems the third option going through Google is working properly.

- We need to go to Ref. [2] to create a new search engine for our site. We need
to specify a name for the search engine and put in our domain name (that we
the Google search engine to search from). That's it.

- Clicking on `Create` there, we will be prompted with a short piece of
javascript code which we then need to copy and paste into, e.g., `_includes/search_google.html`.
Then we need to edit the `search.html` file to include that file, like this `{ % include search-google.html % }`.

  > Refer to Ref. [3] (the github page repo for current blog) for code structure
which the above instructions is based on.

- Here follows is the piece of code implemented for current website,

  ```javascript
  <script async src="https://cse.google.com/cse.js?cx=477bd17655d3640c5">
  </script>
  <div class="gcse-search"></div>
  ```

---

If none of the lunr or Google search implementation works, we can refer to the 
instruction for an alternative method with Google search implementation as
provided below [4] (cited as is),

- Download the file [search-google.html](https://raw.githubusercontent.com/jhvanderschee/jekyllcodex/gh-pages/_includes/search-google.html) and change the domain name
- Save the file in the `_includes` directory of your project
- Add the following statement to your layout where you want the search box to appear,

  ```
  {% raw %}
  {% include search-google.html %}
  {% endraw %}
  ```

  <br>

  > Refer to the source code of current post <a href="https://raw.githubusercontent.com/Kvieta1990/Kvieta1990.github.io/master/_posts/2023-03-14-notes_jekyll_search.md" target="_blank">here</a> for the way to include Jekyll Liquid
  codes explicitly in the web page without rendering its contents.

Submitting the search will lead us to a Google search page where all the results
will be presented.

References
===

[1] [https://longlovemyu.com/search_bar/](https://longlovemyu.com/search_bar/)

[2] [https://cse.google.com/cse/create/new](https://cse.google.com/cse/create/new)

[3] [https://github.com/Kvieta1990/Kvieta1990.github.io](https://github.com/Kvieta1990/Kvieta1990.github.io)

[4] [https://jekyllcodex.org/without-plugin/search-google/](https://jekyllcodex.org/without-plugin/search-google/)