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

Issue #6
===

Docker services backup

Solution (#6)
===

All the docker services installed via the `1Panel` interface were not backed up here. Those manually
installed services were backed up to include their docker compose files -- click [here](/assets/files/oraclel_docker_backups_01222024.zip) to download the backup package.

Here follows are the domain names corresponding to the services,

| Service      | Domain Name |
| ----------- | ----------- |
| Homepage      | me.iris-home.net      |
| Dify   | dify.iris-home.net        |
| Gemini   | gm.iris-home.net        |
| SFTPGo   | gg.iris-home.net        |
| KODCloud   | kd.iris-home.net        |
| nextCloud   | nc.iris-home.net        |
| Pingvin   | pv.iris-home.net        |
| Memos   | mm.iris-home.net        |
| UptimeKuma   | ut.iris-home.net        |
| VPSScheduler   | ql.iris-home.net        |
| ITTools   | it.iris-home.net        |
| Bitwarden   | bw.iris-home.net        |
| Umami   | umm.iris-home.net        |
| PhotoPrism   | pg.iris-home.net        |
| FlatNotes   | fn.iris-home.net        |
| StirlingPDF   | sp.iris-home.net        |
| Grampsy   | gs.iris-home.net        |
| TeamMapper   | tm.iris-home.net        |
| Drawio   | di.iris-home.net        |
| YOURLS   | yr.iris-home.net        |

The `nginx` configuration files were backed up [here](/assets/files/oracle_nginx_backups_01222024.zip).

Issue #7
===

`YOURLS` installation.

Solution (#7)
===

When installing `YOURLS` (e.g., with `1Panel` interface), we need to follow up the installation by visiting the `domain_name/admin` to finish up the installation. Here we just need to replace `domain_name` with our own domain name and in my case, it is `yr.iris-home.net`. Also, after the installation, the domain name, e.g., `yr.iris-home.net` cannot be directly visited -- we still need to visit the `admin` page to define new short URLs.

Issue #8
===

Restart docker services manually after VPS host machine restart.

Solution (#8)
===

For the moment, there are several docker services that have to be started manually after the host
VPS machine is restarted. Here below is a summary for the service folder where we need to to to start the corresponding docker service manually.

| Service      | Directory |
| ----------- | ----------- |
| Dify   | /home/ubuntu/packages/dify/docker        |
| Pingvin   | /home/ubuntu/packages/pingvin        |
| PhotoPrism   | /home/ubuntu/packages/photoprism       |
| TeamMapper   | /home/ubuntu/packages/teammapper        |
| HRConvert2   | /home/ubuntu/packages/HRConvert2-Docker        |
| Yacy   | /home/ubuntu/packages/yacy        |
| WBO   | /home/ubuntu/packages/wbo        |
| Paperless   | /home/ubuntu/packages/paperless-ngx_docker        |
| Wikijs   | /home/ubuntu/packages/wikijs        |
| yopass   | /home/ubuntu/packages/yopass/deploy/docker-compose/insecure        |

The command we need to run is `sudo docker-compose up -d`, assuming we have changed directory to those listed in the table above, respectively.

> For `Yacy` and `wbo`, we need to run the `start.sh` script in their corresponding directories.

Issue #9
===

Installation of `dashy` as a self-hosted web-based personal dashboard.

Solution (#9)
===

1. The docker compose file can be found in the backup file [here](/assets/files/oraclel_docker_backups_01222024.zip).

2. We also need a configuration file which is the backend control of the frontend dashboard layout. The configuration can also be found in the backup file above.

3. In the configuration, I also included the configuration for widgets and we can just use such a configuration as the starting template to continue.

    > Information for widgets installation can also be found [here](https://wiki.opensourceisawesome.com/books/self-hosted-dashboards/page/adding-widgets-to-the-dashy-dashboard) and [here](https://github.com/Lissy93/dashy/blob/master/docs/widgets.md).

4. By default, after installation, the dashboard does not come with any authentication mechanism and everyone can change the configuration. To add in the authentication mechanism, refer to the configuration backup file mentioned above, or the link [here](https://dashy.to/docs/authentication/).

5. There are quite a few resources for icons that can be used in `dashy`,

    - [https://github.com/Lissy93/dashy/wiki/icons/12b2d497821e3849c50bbe982950659c887b298b](https://github.com/Lissy93/dashy/wiki/icons/12b2d497821e3849c50bbe982950659c887b298b)

    - [https://github.com/walkxcode/dashboard-icons/blob/main/ICONS.md](https://github.com/walkxcode/dashboard-icons/blob/main/ICONS.md)

    - [https://github.com/NX211/homer-icons?tab=readme-ov-file](https://github.com/NX211/homer-icons?tab=readme-ov-file)

    - [https://github.com/walkxcode/dashboard-icons](https://github.com/walkxcode/dashboard-icons)

6. We can configure to use the `Code::stats` widgets to track the coding history on various machines. To do this, we need to configure `Code:stats` at [https://codestats.net/](https://codestats.net/). We need to add in our machines in the `Machines` menu to obtain the API for a certain machine. Then we need to install editor plugins to enable the connection to the `Code::stats` to track our coding within certain editors. Specially,

    > If we are using `neovim` and are using `LazyVim` for managing the plugins, we need to refer to Ref. [6] for a usable plugin. For the installation of the plugin, we need to create a plugin file under `~/.config/nvim/lua/plugins/` and give it a name, e.g., `codestats.lua`. Then put can put in the following contents in the file,


    ```lua
    return {
        "YannickFricke/codestats.nvim",
        config = function()
            require("codestats-nvim").setup({
                token = "CODESTATS_API",
            })
        end,
        requires = { { "nvim-lua/plenary.nvim" } },
    }
    ```

    where we want to replace `CODESTATS_API` with our `Code::stats` API.

    > We can also configure `zsh` to update to `Code::stats`. Information can be found in Ref. [7]. We can use the manual method mentioned there to install the plugin, by first downloading the plugin file [here](https://gitlab.com/code-stats/code-stats-zsh/-/raw/master/codestats.plugin.zsh?ref_type=heads). We can put the plugin file somewhere in our system, e.g., `~/.zsh/codestats.plugin.zsh` and change directory there to execute `source codestats.plugin.zsh` to install the plugin. We need to put `export CODESTATS_API_KEY="<api key here>"` in our `.zshrc` file where we can populate the place holder with our own `Code::stats` API. To make sure each time the plugin will be sourced when launching `neovim`, we can add the source command such as `source ~/.zsh/codestats.plugin.zsh` to our `.zshrc` (or `.zshenv`) file.

Issue #10
===

Installation of HRConvert2, which is an open source online service for file format conversion.

Solution (#10)
===

The source codes of `HRConvert2` are available [here](https://github.com/zelon88/HRConvert2) on GitHub. There, we have the link to the docker way of installation. We can specifically follow the instruction for [Docker Compose Setup](https://github.com/dwaaan/HRConvert2-Docker?tab=readme-ov-file#docker-compose-setup) for details. A simple input would be the configuration for the `config.php` file, where on the top we need to fill in several randomly generated strings, with. e.g., BitWarded generator tool. Normally, this will work out. However, in some cases, we may need to build our own image -- typically in my case, I was trying to deploy the service on my oracle VPS with `ARM64` architecture and the existing docker compose file which would use the existing docker image for `HRConvert2` does not support the `ARM64` architecture. So, I had to build my own image. To do this, I could use the following commands,

```bash
git clone https://github.com/dwaaan/HRConvert2-Docker
cd HRConvert2-Docker
vim config.php
vim Dockerfile
sudo docker image build -t hrconvert2 .
vim docker-compose.yml
sudo docker-compose up -d
```

Instructions for editing the `config.php` file has been given above. For the `Dockerfile` file, we need to remove `rar` and `unrar` from the docker `RUN apt-get` list as `rar` is not available on ARM64 architecture -- `unrar` can be installed but I would remove the support for `rar`, though, I did not spend efforts in getting rid of the `rar` part in the web GUI, which requires dealing with those PHP files. For the `docker-compose.yml` file, we want to change docker image to `image: hrconvert2:latest` to use our locally built version. After that, actually we could log in docker and push our local image to the `dockerhub`.

```bash
docker login --username=apw247
docker tag IMAGE_ID apw247/hrconvert2:latest
docker push apw247/hrconvert2
```

after which we could then change the image to `apw247/hrconvert2:latest` in the docker compose file.

Issue #11
===

Hosting a QR code generator.

Solution (#11)
===

There are multiple available choices for hosting a web based QR code generator, as listed in Ref. [8]. However, on ARM64 architecture, we need to rebuild the docker image on our ARM64 machine. As such, I tried several of them and had no luck. The one that I ended up with working is this one in Ref. [9]. In their GitHub repo, the `Dockerfile` is provided from which we can build a docker image on our ARM64 machine,

```bash
cd <GITHUB_REPO>
sudo docker image build -t qrcode .
sudo docker login --username=apw247
sudo docker tag IMAGE_ID apw247/qrcode:latest
sudo docker push apw247/qrcode
vim docker-compose.yml
```

where `<GITHUB_REPO>` refers to the GitHub repo directory for `bizzycola/qrcode-generator` in Ref. [9]. `IMAGE_ID` refers to the ID of the docker image that was built in previous step. At the last step, we need to edit the `docker-compose.yml` file to change the image to be used to the one we just built and pushed. Also, we need to change the port if necessary, e.g., when the default port is already in use. Here is my version,

```
version: "3.9"
services:
  web:
    image: apw247/qrcode:latest
    ports:
      - "5580:80"
```

Issue #12
===

Installation of `Wiki.js` -- a self-hosted Wiki.

Solution (#12)
===

Through the `1Panel` interface, we could easily install the `Wiki.js` Wiki system. However, I found that after the installation, from time to time, we would encounter errors here and there. Not sure what exactly the reason is, though. Alternatively, we could install the service directly using the docker compose approach, following the instruction provided in their official documentation [10].

Issue #13
===

Website set up with `Wordpress` stuck in the `maintenance` mode after upgrading.

Solution (#13)
===

In case of the `Wordpress` hosted website is stuck in the `maintenance` mode, we may need to manually remove the `.maintenance` file under the root directory of the `Wordpress` web server. In case of the `Wordpress` is hosted via `Docker`, we need to launch the running `Wordpress` docker container in the interactive mode, by running,

```bash
sudo docker exec -it [CONTAINER_ID] /bin/bash
```

where `[CONTAINER_ID]` refers to the ID of the running `Wordpress` container, which can be obtained by running `sudo docker ps -a | grep "wordpress"`. Once in the interactive container, we can go to the root directory of the web service which is usually `/var/www/html` and remove the `.maintenance` file manually and exit the container by running `exit`. We may then need to restart the container, via `sudo docker container restart [CONTAINER_ID]`.

Issue #14
===

Set up `Affine` service, which is a workspace with fully merged docs, whiteboards and databases [11].

Solution (#14)
===

Here, I am using `1panel` [12] to set up the `Affine` service and the installation is just a click-and-go operation, very straightforward -- just find the app in the app store in the `1panel` interface and install the app by filling in some necessary parameters. After the successful installation, we need to do some post configuration to enable the email verification and the AI features. By default, such two features would not be enabled when following the normal way of installation, e.g., using `1panel`. In my case, my `Affine` app will be installed to the `1panel` app list, which can be found here, `/opt/1panel/apps/affine/affine`. To enable the email verification feature, we need to edit the `.env` file in the app folder to put in the email service configuration like below,

```
AFFINE_ADMIN_EMAIL="******"
AFFINE_ADMIN_PASSWORD="******"
CONTAINER_NAME="1Panel-affine-X7oq"
CPUS=0
HOST_IP=""
MEMORY_LIMIT=0
PANEL_APP_PORT_HTTP=3010
PANEL_APP_PORT_HTTP_5555=5555
PANEL_DB_HOST="postgresql"
PANEL_DB_HOST_NAME="postgresql"
PANEL_DB_NAME="affine_rtkwcm"
PANEL_DB_PORT=5432
PANEL_DB_USER="affine_4kaPn4"
PANEL_DB_USER_PASSWORD="******"
PANEL_REDIS_HOST="redis"
PANEL_REDIS_ROOT_PASSWORD="******"
AFFINE_SERVER_HOST="aff.iris-home.net"
MAILER_HOST="smtp.gmail.com"
MAILER_PORT=587
MAILER_USER="******"
MAILER_PASSWORD="******"
```

where all the email address and password relevant entries were left out. The normal installation via `1panel` should already take care of the configuration up until the `PANEL_REDIS_ROOT_PASSWORD` entry. The `AFFINE_SERVER_HOST` entry then should be added manually to be consistent with exact the host name where the `Affine` service can be reached. Here for the mail service, I am using `Gmail` and the `MAILER_USER` entry is just our `Gmail` email address. The `MAILER_PASSWORD` needs to be the Google application password, which can be obtained following the instruction in Ref. [13].

To enable the AI feature, we need to first obtain the `OpenAI` API at [14]. Then we need to edit the `data/config/affine.js` file under the `Affine` application directory mentioned above [15]. Scrolling to the bottom of the file, we can fine the `Copilot` section and we just need to uncomment the section and fill in the `OpenAI` API key, like this,

```
// /* Copilot Plugin */
AFFiNE.use('copilot', {
  openai: {
    apiKey: '<OpenAI_API_Key>',
  },
  // fal: {
  //   apiKey: 'your-key',
  // },
  // unsplashKey: 'your-key',
  // storage: {
  //   provider: 'cloudflare-r2',
  //   bucket: 'copilot',
  // }
})
```

After the manual configuration, we may need to stop the running container by running `sudo docker stop <CONTAINER_ID>` and the `<CONTAINER_ID>` can be obtained by running `sudo docker ps -a | grep "affine"`. Then we can do `sudo docker rm <CONTAINER_ID>` to remove the container and run `sudo docker compose up -d` in the `Affine` application directory mentioned above, namely `/opt/1panel/apps/affine/affine`.

Issue #15
===

The `nextcloud` server sometimes may be locked in the `maintenance` mode.

Solution (#15)
===

In this case, we may need to manually disable the `maintenance` mode by going into the running docker container,

```bash
sudo docker exec -it [CONTAINER_ID] /bin/bash
```

where `[CONTAINER_ID]` refers to the ID of the running `nextcloud` container, which can be obtained by running `sudo docker ps -a | grep "nextcloud"`. Once in the interactive container, we can edit the file `/config/www/nextcloud/config/config.php` and change `'maintenance' => true` to `'maintenance' => false`. The running service should be able to pick up the change automatically. If not, we may need to restart the docker container,

```bash
sudo docker restart [CONTAINER_ID]
```

Issue #16
===

Sometimes, deleting trashed files permanently from the web interface would lead to server crash, in which we may want to delete all trashed files in the `nextcloud` server from the command line.

Solution (#16)
===

We first need to go into the running docker container,

```bash
sudo docker exec -it [CONTAINER_ID] /bin/bash
```

where `[CONTAINER_ID]` refers to the ID of the running `nextcloud` container, which can be obtained by running `sudo docker ps -a | grep "nextcloud"`. Once in the interactive container, we can execute the following command,

```bash
occ trashbin:cleanup --all-users
```

which will empty the trash bin for all users. For more options, we can do,

```bash
occ help trashbin:cleanup
```

<br>

References
===

[1] [https://www.youtube.com/watch?v=5a5tdJh8mKY](https://www.youtube.com/watch?v=5a5tdJh8mKY)

[2] [https://www.freedidi.com/6075.html](https://www.freedidi.com/6075.html)

[3] [https://www.freedidi.com/10167.html](https://www.freedidi.com/10167.html)

[4] [https://github.com/1Panel-dev/1Panel](https://github.com/1Panel-dev/1Panel)

[5] [https://github.com/langgenius/dify](https://github.com/langgenius/dify)

[6] [https://github.com/YannickFricke/codestats.nvim](https://github.com/YannickFricke/codestats.nvim)

[7] [https://gitlab.com/code-stats/code-stats-zsh](https://gitlab.com/code-stats/code-stats-zsh)

[8] [https://noted.lol/qr-code-generators/](https://noted.lol/qr-code-generators/)

[9] [https://github.com/bizzycola/qrcode-generator](https://github.com/bizzycola/qrcode-generator)

[10] [https://docs.requarks.io/install/docker#using-docker-compose](https://docs.requarks.io/install/docker#using-docker-compose)

[11] [https://affine.pro/](https://affine.pro/)

[12] [https://github.com/1Panel-dev/1Panel](https://github.com/1Panel-dev/1Panel)

[13] [https://support.google.com/accounts/answer/185833?hl=en](https://support.google.com/accounts/answer/185833?hl=en)

[14] [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)

[15] [https://github.com/toeverything/AFFiNE/discussions/6996#discussioncomment-9877437](https://github.com/toeverything/AFFiNE/discussions/6996#discussioncomment-9877437)