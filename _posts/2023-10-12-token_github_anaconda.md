---
layout: post
title: Anaconda token setup for GitHub action
subtitle:
tags: [programming, technical, package]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Conda package building and deploying (to Anaconda) can be configured in GitHub action so that
when a certain action triggers the GitHub action, the conda package will be built and deployed
automatically. As part of the setup, the Anaconda token is required in the GitHub settings so that
the secure connection could be established for the automatic deploying the package to Anaconda.
Here follows is a general procedure that one could follow for the token setup.

<ol>
  <li>Go to Anaconda project web page, e.g., <a href="https://anaconda.org/neutrons/mantid-total-scattering/settings/collaborators" target="_blank">https://anaconda.org/neutrons/mantid-total-scattering/settings/collaborators</a> and check whether one is in the admin group -- refer to the image below,</li>
</ol>

<p align='center'>
<img src="/assets/img/posts/anaconda_owner_check.png"
   style="border:none;"
   width="800"
   alt="anaconda_check"
   title="anaconda_check" />
</p>

<br/>

<blockquote style="margin-left: 59px">
If one is in the admin group, that means we can generate Anaconda token in the Anaconda user settings page as detailed in next step. Otherwise, one need to request for the admin access first.
</blockquote>

<ol start="2">
  <li>Once making sure one is in the admin list, go to the user settings following the image below,</li>
</ol>

<p align='center'>
<img src="/assets/img/posts/anaconda_user_settings.png"
   style="border:none;"
   width="800"
   alt="anaconda_user_setting"
   title="anaconda_user_setting" />
</p>

<ol start="3">
  <li>Go to 'Access' in user settings,</li>
</ol>

<p align='center'>
<img src="/assets/img/posts/anaconda_token_set.png"
   style="border:none;"
   width="800"
   alt="anaconda_token_setting"
   title="anaconda_token_setting" />
</p>

<p style="margin-left: 57px">Then fill in a token name, select relevant scopes (which controls what one can do with the token) and give it an expiration date.</p>

<ol start="4">
  <li>The resulted token will be displayed on top of the web page and one can select and copy it for the next step,</li>
</ol>

<p align='center'>
<img src="/assets/img/posts/anaconda_token_copy.png"
   style="border:none;"
   width="800"
   alt="anaconda_token_copy"
   title="anaconda_token_copy" />
</p>

<br/>

<blockquote style="margin-left: 59px">
The displayed token above was created for this demo only and has already been revoked (thus no longer valid).
</blockquote>

<ol start="5">
  <li>With the copied token from previous step, one should go to the GitHub settings page of the repository to be configured, and then
follow the instruction below to put in the copied Anaconda token. Either one can create a new repository token or one can change
an existing one.</li>
</ol>

<p align='center'>
<img src="/assets/img/posts/github_token_settings.png"
   style="border:none;"
   width="800"
   alt="github_token_settings"
   title="github_token_settings" />
</p>

<p style="margin-left: 57px">The token change can be done at any time but will usually be needed when the existing token has expired.</p>