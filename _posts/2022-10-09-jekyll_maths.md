---
layout: post
title: Math equation in Jekyll
subtitle: Notes on inclusion of equation in GitHub pages
tags: [programming, web development, jekyll]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/Jekyll_logo.png"
   style="border:none;"
   alt="jl"
   title="jl" />
<br />
</p>

<p style='text-align: justify'>
To enable the display of math equations in GitHub pages, we need to include the corresponding Javascript in our page header. Taking my current blog as an example (one can go to `MORE LINKS` $$\rightarrow$$ `BLOG REPO` to visit the GitHub repo for current blog), in the header part of each post, it is specified the `layout` is `post`. We then can find `post.html` in the `_layouts` directory which includes the definition for the outlook of a post. There, in the header part, one can find it is further pointing to the `base` layout which then refers to the `base.html` file under `_layouts`. Opening the `base.html` file, we can find that it includes the `head.html` file (which fundamentally defines the `<header>` section in the rendered HTML file). The `head.html` file can be found under `_includes` directory.

<br />

We then need to include the following codes in the `head.html` file,
</p>

```javascript
<script type="text/javascript" async
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML">
</script>
```

<p style='text-align: justify'>
Then for both inline and display equation, we can just use '\$\$' symbol to claim an equation environment to write out equations.
</p>