---
layout: post
title: Set up a Budget VPS on CloudCone
subtitle:
tags: [Linux, server, vps]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/ccone_logo.png"
   style="border:none;"
   width="300"
   alt="cloudcone"
   title="cloudcone" />
</p>

<br />

CloudCone is one of the companies providing cheap-access VPS for personal usage to host web services that do not require high-end computations. Here in this note, I will put down some setup notes on configuring a budget VPS on CloudCone and use it for hosting some web services. First, we visit the front page of the CloudCone platform and we will see the interface as below (register an account if we don't already have one),

<p align='center'>
<img src="/assets/img/posts/cloudcone_opts.png"
   style="border:none;"
   width="800"
   alt="cloudcone_opt"
   title="cloudcone_opt" />
</p>

<br />

We can choose the `Budget VPS` from the list as shown in the picture above and follow the instructions to set up the VPS. In my case, I am running a Ubuntu 24.04 OS on the VPS with 4 CPUs and 4 GB memeory. This should be enough for hosting several web services, and in my case, I am running,

- `StirlingPDF`
- `Dash`
- `Homarr`
- `LinkWarden`
- `homepage`
- `Glances`
- `Teable`
- `CallMe`

And roughly they are altogether taking ~70-80% of the memory.

Once we set up the VPS, we grab the IP address and want to set up a domain name for it. CloudCone provides its own domain name registration service and for sure we can register a domain there. However, in my case, I always prefer to manage the domain on `CloudFlare` so those web services can be proxied through `CloudFlare`. If we register the domain through CloudCone and want to use `CloudFlare` to manage the domain, we want to go to `Domains` in CloudCone and further go to `Manage Domains`. Then click on the `Manage` button for the domain we want to configure. In the `Nameservers` setting, we want to use the nameservers from `CloudFlare` like below,

<p align='center'>
<img src="/assets/img/posts/ccone_dns.png"
   style="border:none;"
   width="800"
   alt="ccone_dns"
   title="ccone_dns" />
</p>

<br />

Then we can configure the DNS records in `CloudFlare`. To do the configuration, we go to `CloudFlare` dashboard, click on the `Onboard a domain` and follow the instructions to add the registered domain in CloudCone into `CloudFlare`. Notes for a similar situation can be found in Ref. [1].

References
===

[1] [https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/](https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/)