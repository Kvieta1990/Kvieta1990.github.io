---
layout: post
title: Block the annoying ads on slashdot
subtitle:
tags: [web, tool]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<style>
    .faq-container {
        margin: 0 auto;
    }
    .faq-question {
        margin-bottom: 10px;
        font-weight: bold;
        cursor: pointer;
    }
    .faq-answer {
        display: none;
        margin-bottom: 20px;
    }
    .callout {
        background-color: #e8f4fd; /* Light blue background */
        border-left: 5px solid #007BFF; /* Blue accent on the left */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
        font-family: Arial, sans-serif; /* Ensuring the font is consistent */
    }
    .multiline-span {
        display: block; /* or display: inline-block; */
    }
</style>

<p align='center'>
<img src="/assets/img/posts/Slashdot_logo.png"
   style="border:none;"
   width="200"
   alt="slashdot"
   title="slashdot" />
</p>

`Slashdot` is by far my favourite news collection and sharing platform where people were posting a lot of interesting news covering all sorts of areas, from tech to science, from politics to history, etc. I have been checking the daily feeds on `Slashdot` literatively on a daily base for quite a while and have been enjoying it, until recently I found the website is showing a lot of pop-up ads, which is pretty much annoying. Well, I believe it has the mechanism of delivering ads to readers for a long time but because I have been using ads blocker extension like `AdblockPlus`, I rarely see those annoying and distracting ads. However, just very recently, the `AdblockPlus` no longer works specifically for `Slashdot` even I subscribed the premium of `AdblockPlus`. `Slashdot` does provide its own subscription as well but I never got it working -- anytime I visit its subscription page, the checkbox for subscription and the submit button would always be greyed out. Come on...I just need a way to block those ads and I really don't want to give up `Slashdot`.

Fortunately, I came across a not-too-bad solution and I wil put it down in this blog post [1-3]. First, we still need `AdblockPlus` installed. Then, we need to install an extension called `Tampermonkey` extension. Next, we need to follow the instructions posted in Ref. [1] to install the `tinyShield userscript`. Here, I steal the detailed instructions and put it below,

- Open settings of Tampermonkey.

- Go to `Utilities` tab.

- Input the following URL into `Install from URL`:

    ```
    https://cdn.jsdelivr.net/npm/@list-kr/tinyshield@latest/dist/tinyShield.user.js
    ```

- Click `Install` button.

- Confirm metadata of the userscript and click `Install`.

- Return to a tab opening the website protected by `AdblockPlus` and reload the tab by pressing `F5` or `Ctrl + R` or clicking the reload button.

With this, it seems all the ads on `Slashdot` can be blocked, giving us a fresh reading environment. Nice!

<br />

References
===

[1] [https://github.com/List-KR/tinyShield](https://github.com/List-KR/tinyShield)

[2] [https://forum.vivaldi.net/post/800134](https://forum.vivaldi.net/post/800134)

[3] [https://github.com/AdguardTeam/AdguardFilters/issues/193633](https://github.com/AdguardTeam/AdguardFilters/issues/193633)