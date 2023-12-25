---
layout: post
title: Setting up services on VPS using docker
subtitle:
tags: [web, docker]
author: Yuanpeng Zhang
comments: true
use_math: true
---

To set up services on VPS, one could follow the conventional routine to configure the server and
set up the services. However, such services would then live natively on the VPS operating system,
which is not completely self-contained (e.g., one may need to configure multiple services in the
same configuration file when trying to fire up the server), nor is it transferable to other hosts.
With this regard, using docker for deploying services on VPS is provided by many developers today.

Here in this blog, I will try to record my notes when trying to set up several typical services on
my own VPS hosted at Oracle cloud with the ARM architecture. The first service I want to mention is
the Gemini Pro Chat service which is built on top of the Google Gemini Pro large-language model (LLM)
API [1] which was released very recently. The service repo can be found in Ref. [2], where the option
of deploying the service using docker is provided. Although, using the provided docker command can
successfully fire up the server without problems, it seems I could not figure out the way to
customize the docker image. Simply, I just want to change the footer to replace the default link
with my own homepage. However, trying to duplicate the image, make the necessary file changes and
push the image to my own docker hub, turns out to be not working, for some reason. Not sure what
magic is hindering the server from running properly even though I just made some changes to the
HTML files in the source. Also, later on when I tested it out, I even tried to just duplicate the
image without any changes but still it did not work. So, I had to take the alternative way. Checking
out the repo [2], I found they did include the `docker-compose.yml` and the `.env` files, with which
we could then execute `sudo docker compose up -d` which will then read in the two files mentioned
here -- the `docker-compose.yml` file contains the docker configuration and the `.env` file contains
some necessary environmental variables needed for running the docker image. With this way, I could
make any changes I want and run the docker image without problems.

Another service I want to mention here is `dify` [3], which is a web-based service for
wrapping up the GPT models to provide web interface for it. They also provided the docker way of
setting up the web service. One thing to notice is that to upgrade the service to a certain version,
we have to tune in the `docker-compose.yml` file and edit every single spot where the version info
is explicitly specified -- the reason is the service is running several docker images and their
versions have to be consistent with each other.

Another typical example is the setup of the `photoprism` service [4]. Again, following the instructions
provided on their official website can guarantee a successful setup of the server. However, in my
case, I had to use the CloudFlare for the proxy (defining a sub-domain `pg.iris-home.net` pointing
to the IP address of my VPS, with proxy enabled), combined with the `nginx` configuration, like what
follows,

```
server {
    listen 80;
    server_name pg.iris-home.net;
    location / {
        proxy_pass         https://localhost:2342;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "upgrade";
        proxy_set_header   Host $host;
    }
}
```

In this way, CloudFlare will secure the traffic through `pg.iris-home.net` with HTTPS, which, through
the reverse proxy configured on my VPS using `nginx`, will point to the local service running on
`https://localhost:2342`. The `2342` port on the main VPS operating system will then point to the
local port `2342` on the docker container. If we are following the normal procedure for composing the
docker container as detailed in Ref. [4], things should work out automatically without too much special
care, except two aspects concerning my specific system configuration. One is on the `nginx` side, I
found that I had to use `https://localhost:2342` in the `proxy_pass` entry since otherwise it will
complain about `http service is redirected to https` or something similar. This is not necessary for
other docker containers running on my VPS, though. Another aspect is on the `photoprism` side -- I
had to specify the site URL as `PHOTOPRISM_SITE_URL: "https://pg.iris-home.net"` in the `docker-compose.yml` file.
Otherwise, though the server could run without problems, when trying to share an image, the generated
URL will use the default site URL as the base -- the default base URL `localhost` will then need
to be replaced with the public server domain every time.

References
===

[1] [https://blog.google/technology/ai/gemini-api-developers-cloud/](https://blog.google/technology/ai/gemini-api-developers-cloud/)

[2] [https://github.com/babaohuang/GeminiProChat](https://github.com/babaohuang/GeminiProChat)

[3] [https://github.com/langgenius/dify](https://github.com/langgenius/dify)

[4] [https://docs.photoprism.app/getting-started/docker-compose/](https://docs.photoprism.app/getting-started/docker-compose/)