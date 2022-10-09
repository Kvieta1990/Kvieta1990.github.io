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

1. Run bundle init - this will create the Gemfile.

<br />

2. According to Ref. [1], GitHub pages depends on certain version of packages (a link can be found there in Ref. [1] about details) and therefore one may need to install certain version of Jekyll with Bundle (though, again this may not be necessary if one only wants to do it locally). To install a certain version of Jekyll with Bundle, one need to put in the following line into Gemfile,
</p>

<blockquote cite="">
gem 'jekyll', '3.9.0'
</blockquote>

<p style='text-align: justify'>
3. Then one needs to execute bundle install to install the required version of Jekyll.

<br />

4. At this stage if one continues, as instructed in Ref. [1], to execute bundle exec jekyll new ., most likely error will occur, complaining that the directory is not empty (since we created the Gemfile in previous step for installing the right version of Jekyll). In this case, one just executes the following command, bundle exec jekyll new --force ., which will by force overwrite the already existing Gemfile.

<br />

5. To test locally, one simply executes the following command, bundle exec jekyll serve to start the service, where one should be able to see the localhost that we need to open in browser to check our template site!

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

<br />

<b>References<b/>

[1] [https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/creating-a-github-pages-site-with-jekyll](https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/creating-a-github-pages-site-with-jekyll)

[2] [https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/testing-your-github-pages-site-locally-with-jekyll](https://docs.github.com/en/free-pro-team@latest/github/working-with-github-pages/testing-your-github-pages-site-locally-with-jekyll)

[3] [https://pages.github.com/versions/](https://pages.github.com/versions/)

[4] [https://bundler.io/gemfile.html](https://bundler.io/gemfile.html)