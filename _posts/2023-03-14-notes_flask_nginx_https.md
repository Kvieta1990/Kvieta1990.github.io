---
layout: post
title: Notes Flask+Nginx+HTTPS setup
subtitle:
tags: [programming, web development]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/flask.png"
   style="border:none;"
   width="500"
   alt="flask"
   title="flask" />
</p>

This blog posts contains some miscellaneous notes about the Flask setup with
Nginx and HTTPS. This setup applies specifically to my Centos server.

- The Ref. [1] contains a gist of the nginx configuration file as a minimal
working example.

- Specifically on Centos, it seems that by default the port 443 is not open.
Therefore, even though the reverse proxy is configured properly with Nginx, still
one might not be able to connect to the Flask service. If it is indeed a port
issue, we can refer to the Ref. [2] for the method to open port 443 on Centos
(refer to the https relevant section there).

References
===

[1] [https://gist.github.com/Kvieta1990/f30795b55fc17600568abc514748c606](https://gist.github.com/Kvieta1990/f30795b55fc17600568abc514748c606)

[2] [https://linuxconfig.org/redhat-8-open-http-port-80-and-https-port-443-with-firewalld](https://linuxconfig.org/redhat-8-open-http-port-80-and-https-port-443-with-firewalld)