---
layout: post
title: Copy of code snippet in Jekyll
subtitle: Notes on inclusion of a button for copy code snippet in GitHub pages
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
Trying to follow several resources [1, 2] for setting up the code snippet copy capability in Jekyll site, but for some reason, I could not get it working. I suppose this may be something to do with theme I am using (the `beautiful-jekyll` theme), and I am not what to tune to make it working with the code snippet copy function posted by others (see Ref. [1, 2]). Instead of struggling with those minor tunes, I am using a separate routine for such a purpose, without any involvement with CSS styling, etc.

<br />

To enable the code snippet copy in GitHub pages using my current way, we need to include the corresponding Javascript in our page header. Taking my current blog as an example (one can go to `MORE LINKS` $$\rightarrow$$ `BLOG REPO` to visit the GitHub repo for current blog), in the header part of each post, it is specified the `layout` is `post`. We then can find `post.html` in the `_layouts` directory which includes the definition for the outlook of a post. There, in the header part, one can find it is further pointing to the `base` layout which then refers to the `base.html` file under `_layouts`. Opening the `base.html` file, we can find that it includes the `head.html` file (which fundamentally defines the `<header>` section in the rendered HTML file). The `head.html` file can be found under `_includes` directory.

<br />

First, we need to put the following Javascript codes into the `<header>` section of the `head.html` file,
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp1')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.0em .0em;"><table><tr><td><pre style="margin: 0; line-height: 125%"> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25</pre></td><td id="csp1"><pre style="margin: 0; line-height: 125%"><span style="color: #333333">&lt;</span>script async<span style="color: #333333">=</span><span style="background-color: #fff0f0">&#39;async&#39;</span> src<span style="color: #333333">=</span><span style="background-color: #fff0f0">&#39;https://www.gstatic.com/external_hosted/clipboardjs/clipboard.min.js&#39;</span><span style="color: #333333">&gt;&lt;</span><span style="color: #FF0000; background-color: #FFAAAA">/script&gt;</span>
<span style="color: #333333">&lt;</span>script src<span style="color: #333333">=</span><span style="background-color: #fff0f0">&#39;https://unpkg.com/sweetalert@2.1.2/dist/sweetalert.min.js&#39;</span><span style="color: #333333">&gt;&lt;</span><span style="color: #FF0000; background-color: #FFAAAA">/script&gt;</span>
<span style="color: #333333">&lt;</span>script language<span style="color: #333333">=</span><span style="background-color: #fff0f0">&#39;JavaScript&#39;</span><span style="color: #333333">&gt;</span>
  <span style="color: #008800; font-weight: bold">function</span> copytoclipboard(containerid){
    <span style="color: #008800; font-weight: bold">if</span> (<span style="color: #007020">document</span>.selection) {
      <span style="color: #008800; font-weight: bold">var</span> range <span style="color: #333333">=</span> <span style="color: #007020">document</span>.body.createTextRange();
      range.moveToElementText(<span style="color: #007020">document</span>.getElementById(containerid));
      range.select().createTextRange();
      <span style="color: #007020">document</span>.execCommand(<span style="background-color: #fff0f0">&quot;copy&quot;</span>);
    } <span style="color: #008800; font-weight: bold">else</span> <span style="color: #008800; font-weight: bold">if</span> (<span style="color: #007020">window</span>.getSelection) {
      <span style="color: #008800; font-weight: bold">var</span> range <span style="color: #333333">=</span> <span style="color: #007020">document</span>.createRange();
      range.selectNode(<span style="color: #007020">document</span>.getElementById(containerid));
      <span style="color: #008800; font-weight: bold">var</span> selection <span style="color: #333333">=</span> <span style="color: #007020">window</span>.getSelection() <span style="color: #888888">// get Selection object from currently user selected text</span>
      selection.removeAllRanges() <span style="color: #888888">// unselect any user selected text (if any)</span>
      selection.addRange(range) <span style="color: #888888">// add range to Selection object to select it</span>
      <span style="color: #007020">document</span>.execCommand(<span style="background-color: #fff0f0">&quot;copy&quot;</span>);
      <span style="color: #888888">//alert(&quot;Codes snippet copied to clipboard!&quot;)</span>
      swal({
      title<span style="color: #333333">:</span> <span style="background-color: #fff0f0">&quot;&quot;</span>,
      text<span style="color: #333333">:</span> <span style="background-color: #fff0f0">&quot;Codes snippet copied to clipboard!&quot;</span>,
      icon<span style="color: #333333">:</span> <span style="background-color: #fff0f0">&quot;success&quot;</span>,
      });
    }
  }
<span style="color: #333333">&lt;</span><span style="color: #FF0000; background-color: #FFAAAA">/script&gt;</span>
</pre></td></tr></table></div>

<p style='text-align: justify'>
Here is where I put those lines in the `head.html` file, <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/868f94cd02b16275f674c7f4bc3a0d2a215614c5/_includes/head.html#L162-L186">Click Me</a>.

<br />

Then, as what I have above in current blog, we need to put down a button to link to the `copytoclipboard` function defined in the Javascript we just included. Here follows is the code for the button, which we can directly grab and put into our blog post (see <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L93-L95">here</a> for the implementation in current blog)
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp2')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.0em .0em;"><table><tr><td><pre style="margin: 0; line-height: 125%">1
2
3</pre></td><td id="csp2"><pre style="margin: 0; line-height: 125%"><span style="color: #007700">&lt;div</span> <span style="color: #0000CC">align=</span><span style="background-color: #fff0f0">&quot;right&quot;</span><span style="color: #007700">&gt;</span>
<span style="color: #007700">&lt;button</span> <span style="color: #0000CC">onclick=</span><span style="background-color: #fff0f0">&quot;javascript:copytoclipboard(&#39;csp2&#39;)&quot;</span> <span style="color: #0000CC">style=</span><span style="background-color: #fff0f0">&quot;border: none&quot;</span><span style="color: #007700">&gt;</span>Copy snippet to clipboard!<span style="color: #007700">&lt;/button&gt;</span>
<span style="color: #007700">&lt;/div&gt;</span>
</pre></td></tr></table></div>

<p style='text-align: justify'>
Then, we need to go to the <a target="_blank" href="http://hilite.me/">hilite.me</a> website, where we can paste in our code snippet and the website will help us turning that into HTML codes with language specific grammar highlighting. From there, we can then copy those HTML codes and directly paste them into our blog post, like what I have <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L34-L83">here</a> for the current blog (the first Javascript code snippet above).

<br />

Finally, to link the `copytoclipboard` function associated with a certain button to copy a specific block of codes, we need to add in a unique ID into the pasted HTML codes from <a target="_blank" href="http://hilite.me/">hilite.me</a>, like what I have <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L58">here</a> for the current blog (again, the first Javascript code snippet above). Then the function parameter for `copytoclipboard` needs to be consistent with the code block that we want to copy by clicking on the button - see <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L32">here</a>, again, for the the first Javascript code snippet above.
</p>

<blockquote cite="">
<b>N. B.</b> The ID for the pasted code from <a target="_blank" href="http://hilite.me/">hilite.me</a> should go to <i>EXACTLY</i> the `<td>` section as presented in the link <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L58">here</a>.

<b>N. B.</b> Those pasted HTML from <a target="_blank" href="http://hilite.me/">hilite.me</a> was changed a bit across all posts in my blog to remove the padding space of the code block - specifically it is this bit `padding:.0em .0em;` in <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/093b170b88a7a6ee1309ea882d8777c604439d22/_posts/2022-10-09-jekyll_code_snippet_copy.md?plain=1#L34">here</a>.
</blockquote>