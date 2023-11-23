---
layout: post
title: Set up a self-hosted web server on Oracle VPS
subtitle:
tags: [programming, web, VPS]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/vps_1panel_docker_nginx_setup.png"
   style="border:none;"
   width="600"
   alt="setup"
   title="setup" />
</p>

<br />

It is possible to get a always-free VPS service on Oracle cloud and especially for the ARM architecture,
Oracle is providing a very good deal with which one can set up an always-free VPS with 4 CPUs and 24 Gb
RAM. We can refer to Ref. [1-3] for more information (in Chinese only). Once the VPS is up
running, we can use the `1Panel` tool [4] to set up an admin service available through the web interface
for setting up and managing web services on our VPS to make it really useful, taking full advantage of the
always-free resource.

With `1Panle`, all the web services will be deployed using `Docker`, making it very easy to set up and
manage. It is possible to deploy our own chatgpt service using docker as well -- it is not included in
the `1Panle` app store, but one can refer to Ref. [5] for detailed instructions about how-to.

Here follows are listed several issues and the corresponding solution while I was setting up those services.

**Issue #1**
===

For some reason, the docker for hosting the `sftpgo` service would stop running from time to time, making
the service not available frequently, in which case we need to manually restart the docker container all
the time.

**Solution (#1):**
===

We can write a bash script like this,

```bash
#!/bin/bash

sudo docker container ps -a -f "id=652ff545ce6c" | grep -q "Up" && a=1 || a=0

if [ $a = 0 ] ; then
    sudo docker container start 652ff545ce6c
fi
```

and make it executable (`chmod a+x check_sftpgo.sh`) and add it to the cron job to run it, e.g.,
every minute so that the system will check the status of the docker container and restart it automatically
if not running.

**Issue #2**
===

To make the connection to our web server secure, we need to set up SSL certificate and enable the HTTPS
connection. We can do this by buying some SSL certificate and set up the HTTPS connection using the certificate
using. e.g., `nginx` reverse proxy service. But we do need to pay for the certificate and it is probably not
cheap.

**Solution (#2):**
===

To get a free, secure and easy-to-configure certificate service, we can use `CloudFlare` with which we can even
use the domain name from other DNS provider (I am using Google domain and it was working perfectly). We can also use
the `Page Rules` service provided by CloudFlare to force the visit to our web server following a certain rule, e.g.,
always to use the HTTPS connection instead of HTTP -- however, we can set up three free rules and beyond that we need
to pay.

**Issue #3**
===

Let's say we have a domain name `example.net` and we followed the `CloudFlare` way of setting up a secure connection via
HTTPS. We are now facing the problem of having multiple services on a single VPS. For sure, we can directly use the port
number like `example.net:9999` to point to the corresponding service on the server but this is probably not optimal in
terms of both security and convenience.

**Solution (#3):**
===

An alternative option is to use nginx and specify a `location` for a server which then points to a certain port number
via local connection -- something like follows,

```
server {
    listen 80;
    server_name example.net;

    location / {
            proxy_pass         http://localhost:9999;
            proxy_http_version 1.1;
            proxy_set_header   Upgrade $http_upgrade;
            proxy_set_header   Connection "upgrade";
            proxy_set_header   Host $host;
    }
}
```

This will work, but only for a single instance. For example, if I have another service hosted vai port 8888 and I tried
to assign another location to it, it won't work (not sure why, but in practice it did not work out).

So, in this case, we can create subdomains, e.g., `sd.example.net` where `sd` can be with any arbitrary value and all
those domains are considered as the subdomain of `example.net` and we are free to use them all -- sometimes they are
also called aliases but referring to the same thing. Then using `nginx`, we can create multiple configuration files,
put them under `sites-enabled` directory (under `/etc/nginx`) and include them in the main `nginx` configuration file
(`/etc/nginx/nginx.conf`), like this `include /etc/nginx/sites-enabled/*;` (within the `http` section). Here follows is
a typical example file of such a configuration,

```
server {
    listen 80;
    server_name mm.iris-home.net;
    location / {
        proxy_pass         http://localhost:5230;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "upgrade";
        proxy_set_header   Host $host;
    }
}
```

Two things to note down,

1. For any of the configuration above for `nginx`, we only need to specify the `80` port (which is actually the
http port) since the security part will be taken care of by `CloudFlare` following the notes above.

2. Quite often, people prefers to put the extra configuration files into the `sites-available` (also under
`/etc/nginx`) and create soft link in the `sites-enabled` just for those to be enabled. 

**Issue #4**
===

When hosting file sharing service such as `SFTPGo` or `KOD Cloud`, etc., the backend web interface will highly likely
be configured via `nginx`. With the default settings of `nginx`, there will be upper limit of `1 Mb` for the file
uploading. Then if the file to be uploaded exceeds the size limit, we will encounter with server error. 

**Solution (#4):**
===

Open the `/etc/nginx/nginx.conf` file (as `root`) and add in the following configuration in the `http`, `server`, and
`location` section,

    ```
    client_max_body_size 100M;
    ```

Then restart the `nginx` server, e.g., via `sudo systemctl restart nginx`.

**Issue #5**
===

When setting up the `nextcloud` app with `1Panel`, there is a small thing we should
pay attention to.

**Solution (#5):**
===

Following the setup of the nginx+cloudflare proxy step, the `nginx` configuration should look like,

```
server {
    listen 80;
    server_name nc.iris-home.net;
    location / {
        proxy_pass         https://localhost:40069;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection "upgrade";
        proxy_set_header   Host $host;
    }
}
```

The subdomain `nc.iris-home.net` should be configured in `Cloudflare` and it should be noticed that
the local `https` connection is used instead of `http` -- see the `proxy_pass` setting above.

<b>References</b>

[1] [https://www.youtube.com/watch?v=5a5tdJh8mKY](https://www.youtube.com/watch?v=5a5tdJh8mKY)

[2] [https://www.freedidi.com/6075.html](https://www.freedidi.com/6075.html)

[3] [https://www.freedidi.com/10167.html](https://www.freedidi.com/10167.html)

[4] [https://github.com/1Panel-dev/1Panel](https://github.com/1Panel-dev/1Panel)

[5] [https://github.com/langgenius/dify](https://github.com/langgenius/dify)
