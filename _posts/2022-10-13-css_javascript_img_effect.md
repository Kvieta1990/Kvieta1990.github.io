---
layout: post
title: Image effect in web page
subtitle: CSS + Javascript for basic image effect in web page
tags: [programming, web development]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
In the index page of current blog, we have a picture shown as below - if we hover the mouse over the image, it will change to another picture and also as we move our mouse, the image will tilt along with the mouse moving.
</p>

<div align='center' class='card' data-tilt data-tilt-scale="0.9">
<img src="/assets/img/ORNL.png"
   alt="SNS"
   title="SNS" />
   <img src="/assets/img/SNS.jpg" class="img-top" alt="ORNL">
<div class="centered-text"><h1>Spallation Neutron Source</h1></div>
<div class="centered-text-1"><h1>Oak Ridge National Laboratory</h1></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>

<p style='text-align: justify'>
The codes for displaying the effect above is presented below,
</p>

```html
<div align='center' class='card' data-tilt data-tilt-scale="0.9">
<img src="/assets/img/ORNL.png"
   alt="SNS"
   title="SNS" />
   <img src="/assets/img/SNS.jpg" class="img-top" alt="ORNL">
<div class="centered-text"><h1>Spallation Neutron Source</h1></div>
<div class="centered-text-1"><h1>Oak Ridge National Laboratory</h1></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
```

<p style='text-align: justify'>
Here is its implementation in the `index.md` file for current blog, <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/94915e3e50cbb22986fd666b64cc4a2d7c88f29d/index.md?plain=1#L10-L19">Click Me!</a>
</p>

<p style='text-align: justify'>
Accordingly, the CSS configuration relevant to such an image effect is shown below,
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp1')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;height: 500px;border-width:.1em .1em .1em .8em;padding:.0em .0em;"><table><tr><td><pre style="margin: 0; line-height: 125%"> 1
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
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74
75
76
77
78
79
80
81
82
83
84
85
86
87
88</pre></td><td id='csp1'><pre style="margin: 0; line-height: 125%"><span style="color: #BB0066; font-weight: bold">.card</span> {
    <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">relative</span>;
    <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span><span style="color: #333333">-</span><span style="color: #008800; font-weight: bold">block</span>;
    <span style="color: #008800; font-weight: bold">border</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
    <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> flex;
    flex<span style="color: #333333">-</span><span style="color: #008800; font-weight: bold">direction</span><span style="color: #333333">:</span> solumn;
    align<span style="color: #333333">-</span>items<span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">center</span>;
    <span style="color: #008800; font-weight: bold">justify</span><span style="color: #333333">-</span><span style="color: #008800; font-weight: bold">content</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">center</span>;
    <span style="color: #008800; font-weight: bold">color</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">#fff</span>;
    transform<span style="color: #333333">-</span>style<span style="color: #333333">:</span> preserve<span style="color: #6600EE; font-weight: bold">-3</span>d;
    transform<span style="color: #333333">:</span> perspective(<span style="color: #6600EE; font-weight: bold">1000px</span>);
}

<span style="color: #BB0066; font-weight: bold">.card</span> <span style="color: #BB0066; font-weight: bold">.img-top</span> {
    <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
    <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">absolute</span>;
    <span style="color: #008800; font-weight: bold">top</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">0</span>;
    <span style="color: #008800; font-weight: bold">left</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">0</span>;
    <span style="color: #008800; font-weight: bold">z-index</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">99</span>;
}

<span style="color: #BB0066; font-weight: bold">.card</span><span style="color: #555555; font-weight: bold">:hover</span> <span style="color: #BB0066; font-weight: bold">.img-top</span> {
    <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span>;
}

<span style="color: #008800; font-weight: bold">@media</span> <span style="color: #007700">screen</span> <span style="color: #007700">and</span> <span style="color: #333333">(</span><span style="color: #007700">min-width</span><span style="color: #333333">:</span> <span style="color: #007700">500px</span><span style="color: #333333">)</span> {
    <span style="color: #BB0066; font-weight: bold">.card</span> <span style="color: #BB0066; font-weight: bold">.centered-text</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
        <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">absolute</span>;
        <span style="color: #008800; font-weight: bold">top</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">5</span><span style="color: #333333">%</span>;
        <span style="color: #008800; font-weight: bold">left</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">27</span><span style="color: #333333">%</span>;
        transform<span style="color: #333333">:</span> translateZ(<span style="color: #6600EE; font-weight: bold">50px</span>);
        <span style="color: #008800; font-weight: bold">z-index</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">99</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span><span style="color: #555555; font-weight: bold">:hover</span> <span style="color: #BB0066; font-weight: bold">.centered-text</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span> <span style="color: #BB0066; font-weight: bold">.centered-text-1</span> {
    <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
    <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">absolute</span>;
    <span style="color: #008800; font-weight: bold">top</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">18</span><span style="color: #333333">%</span>;
    <span style="color: #008800; font-weight: bold">left</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">23</span><span style="color: #333333">%</span>;
    transform<span style="color: #333333">:</span> translateZ(<span style="color: #6600EE; font-weight: bold">50px</span>);
          <span style="color: #008800; font-weight: bold">z-index</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">99</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span><span style="color: #555555; font-weight: bold">:hover</span> <span style="color: #BB0066; font-weight: bold">.centered-text-1</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span>;
    }

    <span style="color: #007700">h1</span> {
        <span style="color: #008800; font-weight: bold">font-size</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">35px</span>;
    }
}

<span style="color: #008800; font-weight: bold">@media</span> <span style="color: #007700">screen</span> <span style="color: #007700">and</span> <span style="color: #333333">(</span><span style="color: #007700">max-width</span><span style="color: #333333">:</span> <span style="color: #007700">500px</span><span style="color: #333333">)</span> {
    <span style="color: #BB0066; font-weight: bold">.card</span> <span style="color: #BB0066; font-weight: bold">.centered-text</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
        <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">absolute</span>;
        <span style="color: #008800; font-weight: bold">top</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">5</span><span style="color: #333333">%</span>;
        <span style="color: #008800; font-weight: bold">left</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">35</span><span style="color: #333333">%</span>;
        transform<span style="color: #333333">:</span> translateZ(<span style="color: #6600EE; font-weight: bold">50px</span>);
        <span style="color: #008800; font-weight: bold">z-index</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">99</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span><span style="color: #555555; font-weight: bold">:hover</span> <span style="color: #BB0066; font-weight: bold">.centered-text</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span> <span style="color: #BB0066; font-weight: bold">.centered-text-1</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">none</span>;
        <span style="color: #008800; font-weight: bold">position</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">absolute</span>;
        <span style="color: #008800; font-weight: bold">top</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">18</span><span style="color: #333333">%</span>;
        <span style="color: #008800; font-weight: bold">left</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">32</span><span style="color: #333333">%</span>;
        transform<span style="color: #333333">:</span> translateZ(<span style="color: #6600EE; font-weight: bold">50px</span>);
        <span style="color: #008800; font-weight: bold">z-index</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">99</span>;
    }

    <span style="color: #BB0066; font-weight: bold">.card</span><span style="color: #555555; font-weight: bold">:hover</span> <span style="color: #BB0066; font-weight: bold">.centered-text-1</span> {
        <span style="color: #008800; font-weight: bold">display</span><span style="color: #333333">:</span> <span style="color: #008800; font-weight: bold">inline</span>;
    }

    <span style="color: #007700">h1</span> {
        <span style="color: #008800; font-weight: bold">font-size</span><span style="color: #333333">:</span> <span style="color: #6600EE; font-weight: bold">10px</span>;
    }
}
</pre></td></tr></table></div>

<p style='text-align: justify'>
Here, to realize the tilting effect, we rely on the `vanilla-tilt` Javascript. The actual code is just the last line in the HTNL code snippet presented above. Detailed information about this library can be found <a target="_blank" href="https://micku7zu.github.io/vanilla-tilt.js/">here</a>. Apart from that sinle line of code, we then need to put `data-tilt` in the HTML `<div>` section that contains the image we want to tilt (see the first line in the HTML code snippet above). We can realize that the text and the image is tilted differently so that it seems they are not on the same layer. That effect is realized by referring to the `Creating a parallax effect` section of <a target="_blank" href="https://micku7zu.github.io/vanilla-tilt.js/">this website</a>. From the website instruction, we will see that this requires some special CSS configuration, which, in our case of current blog implementation, can be found <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/94915e3e50cbb22986fd666b64cc4a2d7c88f29d/assets/css/beautifuljekyll.css#L1076-L1077">here</a> and <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/94915e3e50cbb22986fd666b64cc4a2d7c88f29d/assets/css/beautifuljekyll.css#L1097">here</a>. To realize the showing and hiding effect for both the image and text on mouse hovering, we will rely on the definition of a parent CSS class `card` and its children class `img-top`, as presented in the CSS snippet above. Then another definition to account for the mouse hovering effect should be defined - see the `.card:hover img-top` presented above. Then we know that when mouse is not hovering, the image associated with the `img-top` class will not be shown (`display: none`). When mouse hovers on the default image, `display: inline` will then bring up the image associated with the `img-top` class on top of the default image.

<br />
<br />

<b>N. B.</b> The `@media screen and (min-width: 500px)` section in the CSS snippet above is enabled for browser with the width of at least 500px and `@media screen and (max-width: 500px)` is for browser with the width of at most 500px. This is the so-called media query to display in different manner for different browsers to realize the optimal display effect on various platforms (computer, ipad, or phone).
</p>