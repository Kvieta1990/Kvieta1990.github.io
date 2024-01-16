---
layout: post
title: Set up YOURLS on VPS with docker
subtitle:
tags: [web, docker, yourls]
author: Yuanpeng Zhang
comments: true
use_math: true
---

YOURLS is an URL shortening service [1] and we can deploy a local version of the service to our own
VPS. Here, I am using the `1Panel` service [2] to set up YOURLS -- `1Panel` is a web-based Linux system
management interface with which we can manage our VPS and set up applications in a very convenient way.
The latest version ([v1.9.4](https://github.com/1Panel-dev/1Panel/releases/tag/v1.9.4)) of `1Panel` does
provide YOURLS in its application store and we can install it quickly and easily.

> The default github page and the documentation for `1Panel` is in Chinese. They should have English support.

Though the installation of `YOURLS` via `1Panel` is made easy, the practical setup does need some care.

1. We need to specify the external URL in the installation configuration page, as shown below,

    > Here, I am using `nginx+CloudFlare` for setting up the web traffic and the external URL is from the CloudFlare service.

    <p align='center'>
    <img src="/assets/img/posts/yourls.png"
       style="border:none;"
       width="600"
       alt="yourls"
       title="yourls" />
    </p>

2. We don't need to expose the port for serving the YOURLS service to public. With `CloudFlare`, we
can assign a sub-domain (e.g., yr.iris-home.net in my case) to the IP address of our VPS. Then, we
can configure nginx in the following way to let the external URL redirect to `localhost` at the port
specified for `YOURLS`,

    ```
    server {
            listen 80;
            server_name yr.iris-home.net;
            location / {
                    proxy_pass         http://localhost:40037;
                    proxy_http_version 1.1;
                    proxy_set_header   Upgrade $http_upgrade;
                    proxy_set_header   Connection "upgrade";
                    proxy_set_header   Host $host;
            }
            location /admin {
                    proxy_pass         http://localhost:40037/admin;
                    proxy_http_version 1.1;
                    proxy_set_header   Upgrade $http_upgrade;
                    proxy_set_header   Connection "upgrade";
                    proxy_set_header   Host $host;
            }
    }
    ```

3. The following chunk in the `nginx` configuration is necessary,

    ```
    location /admin {
        proxy_pass         http://localhost:40037/admin;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "upgrade";
        proxy_set_header   Host $host;
    }
    ```

    as we can only access the YOURLS service via the admin page, meaning that if we visit the domain
    `yr.iris-home.net` directly without appending the directory `/admin`, it will complain about
    `site access forbidden`. In practice, we would visit the admin page, add in the URL to be shortened,
    and then it will go into the database with the shortened URL created.

References
===

[1] [https://github.com/YOURLS/YOURLS](https://github.com/YOURLS/YOURLS)
[2] [https://github.com/1Panel-dev/1Panel/tree/dev](https://github.com/1Panel-dev/1Panel/tree/dev)