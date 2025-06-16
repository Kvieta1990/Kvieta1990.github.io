---
layout: post
title: Windows in Docker
tags: [dev, technical]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Yes, now we can run Windows in a docker container. That means we can access the Windows operating system from the web, thanks to the Windows docker image provided in Ref. [1]. Based on the instructions provided in Ref. [1], here follows is my docker compose file to start up the Windows service,

```yaml
services:
  windows:
    image: dockurr/windows
    container_name: windows
    environment:
      VERSION: "2025"
      DISK_SIZE: "80G"
      RAM_SIZE: "16G"
      CPU_CORES: "4"
      USERNAME: "USER_NAME"
      PASSWORD: "PASSWORD"
      REGION: "en-US"
      KEYBOARD: "en-US"
      EDITION: "desktop"
      USER: "USER_NAME_VNC"
      PASS: "PASSWORD_VNC"
    devices:
      - /dev/kvm
      - /dev/net/tun
    cap_add:
      - NET_ADMIN
    ports:
      - 8006:8006
      - 3389:3389/tcp
      - 3389:3389/udp
    volumes:
      - ./windows:/storage
    restart: always
    stop_grace_period: 2m
```

Here follows are serveral things to specifically pay attention,

- The `VERSION` entry should be one of those available versions of Windows OS given in Ref. [1].

- The `EDITION` entry specifies what specific version of Windows to install. For example, the configuration above specifies we want to install Windows server 2025 and the `EDITION` then specifies that we want to install the desktop edition for which we have the GUI interface.

- The `USERNAME` and `PASSWORD` is the user name and password for the Windows OS itself.

- The `USER` and `PASS` is the user name and password for the VNC connection, since the frontend interface is provided by the VNC (Virtual Network Computing) technique.

- The operating system on which we will be running the Windows docker image should support `KVM` (Kernel-based Virtual Machine) [2] and have it enabled. If it is not supported or disabled, when running the Windows docker container, we would encounter errors. I am not familiar with the `KVM` technique and not sure generally how we enable it. When trying to enable it on my Google Cloud computing instance, I got instructions in Ref. [3, 4] provided by Google.

Once the docker container is up running, we should be able to access the Windows OS from the web by visiting the port of our server, e.g., `8006` in the example above. If we are using `nginx` for the reverse proxy, we can refer to the following server configuration to point a secondary domain name to the local `8006` port so that we can always use the externally facing `https` port (the `443` port) for a secure connection,

```
server {
    listen       443 ssl;
    server_name  vwindows.ornl.gov;
    root         /usr/share/nginx/html;

    add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
    add_header X-Frame-Options "DENY";

    ssl_certificate "/home/cloud/packages/windows/windows.pem";
    ssl_certificate_key "/home/cloud/packages/windows/windows.key";

    location / {
        proxy_pass http://localhost:8006;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_pass_request_headers on;
        add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
        add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate" always;
        add_header Pragma "no-cache" always;
        add_header Expires "0" always;
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Frame-Options "DENY" always;
        proxy_set_header X-Frame-Options "SAMEORIGIN";
        client_max_body_size 50M;
        proxy_set_header Connection "";
        proxy_set_header Host $http_host;
        proxy_buffers 256 16k;
        proxy_buffer_size 16k;
        proxy_read_timeout 600s;
        proxy_cache_revalidate on;
        proxy_cache_min_uses 2;
        proxy_cache_use_stale timeout;
        proxy_cache_lock on;
        proxy_http_version 1.1;
    }

    location /websockify {
        proxy_http_version 1.1;
        proxy_pass http://localhost:8006;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";

        proxy_read_timeout 61s;
        proxy_buffering off;
    }

    error_page 404 /404.html;
        location = /404.html {
    }

    error_page 500 502 503 504 /50x.html;
        location = /50x.html {
    }
}
```

The `websockify` section is provided by the `noVNC` reference [5] for the configuration with `nginx` -- `noVNC` is a web-based client for running the VNC application so that we don't have to install the VNC client like `realVNC` for connecting to the `VNC` service.

After the Windows container starting up, the installed Windows OS will be the evaluation version, and we can follow the instructions provided in Ref. [6] for the activation (using the activation key provided by Microsoft).

References
===

[1] [https://github.com/dockur/windows](https://github.com/dockur/windows)

[2] [https://www.redhat.com/en/topics/virtualization/what-is-KVM](https://www.redhat.com/en/topics/virtualization/what-is-KVM)

[3] [https://cloud.google.com/compute/docs/instances/nested-virtualization/managing-constraint](https://cloud.google.com/compute/docs/instances/nested-virtualization/managing-constraint)

[4] [https://cloud.google.com/compute/docs/instances/nested-virtualization/enabling](https://cloud.google.com/compute/docs/instances/nested-virtualization/enabling)

[5] [https://github.com/novnc/noVNC/wiki/Proxying-with-nginx](https://github.com/novnc/noVNC/wiki/Proxying-with-nginx)

[6] [https://www.freedidi.com/19562.html](https://www.freedidi.com/19562.html)