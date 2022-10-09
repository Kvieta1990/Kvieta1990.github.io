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
To enable the display of math equations in GitHub pages, we need to include the corresponding Javascript in our page header. Taking my current blog as an example (one can go to `MORE LINKS` $$\rightarrow$$ `BLOG REPO` to visit the GitHub repo for current blog), in the header part of each post, it is specified the `layout` is `post`. We then can find `post.html` in the `docs/_layouts` directory which includes the definition for the outlook of a post. There, in the header part, one can find it is further pointing to the `base` layout which then refers to the `base.html` file under `docs/_layouts`. Opening the `base.html` file, we can find that it includes the `head.html` file (which fundamentally defines the `<header>` section in the rendered HTML file). The `head.html` file can be found under `docs/_includes` directory.

<br />

First, we need to include the following codes in the `head.html` file,
</p>

```html
{% raw %}{% if page.use_math %}
   {% include mathjax_support.html %}
{% endif %}{% endraw %}
```

<p style='text-align: justify'>
Here is where I put those lines in the `head.html` file, <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/5f16789664b6c19f9f15f75cd4eee20c4787720a/docs/_includes/head.html#L14-L16">Click Me</a>.

<br />

The `page.use_math` variable is defined in the header of a post. Say, we want to enable the math display in a certain post, we just put `use_math: true` in the header of the post, like this, <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/5f16789664b6c19f9f15f75cd4eee20c4787720a/docs/_posts/2020-03-08-coriolis_force.md?plain=1#L8">Click Me</a>.

<br />

The `mathjax_support.html` file is a file located in `docs/_includes` directory, with the following contents,
</p>

```html
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
   TeX: { equationNumbers: { autoNumber: "AMS" } }
  });
</script>
<script type="text/javascript" async src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
```

<p style='text-align: justify'>
Then for both inline and display equation, we can just use '\$\$' symbol to claim an equation environment to write out equations.
</p>