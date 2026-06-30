---
layout: post
title: Setting up 1panel with Cloudflare
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
<img src="/assets/img/posts/1panel_cf.png"
   style="border:none;"
   width="500"
   alt="wordpress"
   title="wordpress" />
</p>

`1Panel` is an open-source web-based control panel for Linux server management. With 1panel, we can manager our server in a visual and convenient manner. Also, it provides application store to install a wide range of web-based applications (with `docker` as the backend engine). I have my own server hosted on Oracle cloud and I have several blog posts regarding the setup of the server and those running web-based applications [1, 2].

Setting up the `1panel` service on our server is as simple as running the command below,

```bash
curl -sSL https://resource.1panel.hk/quick_start.sh -o quick_start.sh && bash quick_start.sh
```

After the initial setup, we can visit the service through the preset port, like this `http://localhost:<PORT>/<RANDOM_ENTRY_POINT>`, where `<PORT>` refers to the port number and `<RANDOM_ENTRY_POINT>` is a random string for the entrance. Both of them would be with some default value when initially setting up `1panel`. After the setup, we can obtain the information by running,

```bash
sudo 1pctl user-info
```

With the default setup, we can visit the service through `http` like above, which is not secure. To set up a secure connection, we can use the free `cloudflare` service (running remotely) together with the `nginx` service (running locally on the server). Notes about setting up `cloudflare` with our own registered domain can be found [here](https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/). The local setup for `nginx` is very similar to `Issue #3` in Ref. [1] and we only need to replace the `server_name` with the sub-domain that we registered on `cloudflare` and replace the port number that `1panel` service is running on. The only special setup for `1panel` is about the entrance. By default, `1panel` enables the secure entrance, i.e., `<RANDOM_ENTRY_POINT>` above. This means every time when we try to access `1panel`, we need to append `/<RANDOM_ENTRY_POINT>` to the domain name and this is not so convenient. To solve the issue, my solution is to remove the entrance, by running [3],

```bash
sudo 1pctl reset entrance
```

With this, no `<RANDOM_ENTRY_POINT>` will be needed for the visiting url.

<br>

References
===

[1] [https://iris2020.net/2023-09-08-docker_nginx/](https://iris2020.net/2023-09-08-docker_nginx/)

[2] [https://iris2020.net/2024-01-28-oracle_vps_vol_expand/](https://iris2020.net/2024-01-28-oracle_vps_vol_expand/)

[3] [https://docs.1panel.hk/user_manual/settings/#security](https://docs.1panel.hk/user_manual/settings/#security)