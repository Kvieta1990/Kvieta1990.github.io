---
layout: post
title: Tips on Wordpress Setup
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
<img src="/assets/img/posts/wordpress.jpg"
   style="border:none;"
   width="200"
   alt="wordpress"
   title="wordpress" />
</p>

`Wordpress` is commonly used framework for setting up website without directly and heavily working with HTML or CSS. Here in this blog, I will note down tips for using `Wordpress` -- it keeps record of my problem solving along the way so the whole blog does not come in a well organized way.

<br>

## <a id="n1">📌 Using the `Variations` theme</a> <a href="#n1">#</a>

- To change the style of the button(s) in the header, we can go to `Patterns` in the `Variations` theme customization. Then we select `Header` and click on the header we are currently using to customize. Clicking on the button we want to customize, we can choose to `Edit as HTML`, as shown below,

    <p align='center'>
    <img src="/assets/img/posts/var_header_button_s1.png"
    style="border:none;"
    width="1000"
    alt="var_header_button_s1"
    title="var_header_button_s1" />
    </p>

    This will turns the button into a chunk of HTML codes from which we can see the class name for the button -- in my case, it is `wp-block-button`. Then we can go to `Styles` in the top right corner of the page as shown below,

    <p align='center'>
    <img src="/assets/img/posts/var_header_button_s2.png"
    style="border:none;"
    width="1000"
    alt="var_header_button_s2"
    title="var_header_button_s2" />
    </p>

    From there, we can go to `Additional CSS` and put in, e.g., the following CSS to control the behavior of the button on mouse hovering,

    ```
.wp-block-button a:hover {
    background: #039458;
    color: white;
}
    ```

- For `Footer` settings, again, we go to `Patterns` and select `Footer`. Then clicking each of the element in the footer, we can go to the `Settings` button in the top right corner of the page -- the option is just to the left of the `Styles` option mentioned above. Then we can configure the settings for each of the elements in the footer individually.

- It turns out that if we configure the CSS for the button in the header, then the general CSS applied to the `button` block across the whole website is not applied to the button in the header, and vice versa. This is actually what we need -- as the surrounding background is different between the header button and those buttons in the main text and therefore we need separate controls over each.

- To customize the links in the header, we can refer to the following steps,

    - Step-1

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s1.png"
        style="border:none;"
        width="1000"
        alt="nav_links_s1"
        title="nav_links_s1" />
        </p>

    - Step-2

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s2.png"
        style="border:none;"
        width="500"
        alt="nav_links_s2"
        title="nav_links_s2" />
        </p>
    
    - Step-3

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s3.png"
        style="border:none;"
        width="500"
        alt="nav_links_s3"
        title="nav_links_s3" />
        </p>

    - Step-4

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s4.png"
        style="border:none;"
        width="1000"
        alt="nav_links_s4"
        title="nav_links_s4" />
        </p>

    - Step-5

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s5.png"
        style="border:none;"
        width="300"
        alt="nav_links_s5"
        title="nav_links_s5" />
        </p>

    - Step-6

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s6.png"
        style="border:none;"
        width="300"
        alt="nav_links_s6"
        title="nav_links_s6" />
        </p>

    - Step-7

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s7.png"
        style="border:none;"
        width="300"
        alt="nav_links_s7"
        title="nav_links_s7" />
        </p>

    - Step-8

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s8.png"
        style="border:none;"
        width="1000"
        alt="nav_links_s8"
        title="nav_links_s8" />
        </p>

    - Step-9

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s9.png"
        style="border:none;"
        width="300"
        alt="nav_links_s9"
        title="nav_links_s9" />
        </p>

    - Step-10

        <p align='center'>
        <img src="/assets/img/posts/nav_links_s10.png"
        style="border:none;"
        width="300"
        alt="nav_links_s10"
        title="nav_links_s10" />
        </p>