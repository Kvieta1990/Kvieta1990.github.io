---
layout: post
title: Set up a Flarum forum on VPS
subtitle:
tags: [web, docker]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/logo_flarum.png"
   style="border:none;"
   width="300"
   alt="flarum"
   title="flarum" />
</p>

Set up a VPS and getting a domain for the VPS will not be covered in this blog. Here, I am assuming we have already got our VPS up running. I am using the `1Panel` control panel to install the `Flarum` forum web service, which is one of the most popular forum these days on the market, together `MyBB`, `Discourse`, etc. The installation instruction for `1Panel` can be found in Ref. [1]. Once `1Panel` is installed successfully, we can go to its web interface, log in and install applications such as `Flarum` and quite a few other apps from its app store. In the backend, it is running docker for the installation so later on, we can go to the command line, connect to our VPS and manage the running docker container for the `Flarum` service from the command line.

By default, when installing `Flarum` from `1Panel`, a few extensions may have already been installed. However, to make our forum more rich in functionalities, we could go to Ref. [2] to obtain the plugins to install on our `Flarum`. For the installation, we can go into our running `Flarum` docker container and execute the command as suggested on the extension web page. To go into the running docker container, we do can something like this,

```bash
sudo docker ps -a
sudo docker exec -it c48274cdba79 /bin/bash
```

where the first line of the command is to obtain the ID of the running docker container and the second line runs the docker container in the interactive mode with the ID obtained in the first step. Then we can execute the installation command for the extension within the docker container,

```dash
composer require sycho/flarum-private-facade:"*"
```

Sometimes, the installation may fail due to version incompatibility and it seems we don't have a good solution in such a situation. Also, it can happen that after the installation and activation of a certain extension, the whole server may break down, due to some PhP error. In such a situation, if the docker container interactive shell is still alive, we can try to remove the just installed extension by running something like,

```bash
composer remove sycho/flarum-private-facade:"*"
```

In case of bad luck where there is no way to revert, we may have to rebuild the app within the `1Panel` interface, in which case we will lose all the installed extensions -- other configurations for the forum seems to persist.

<br>

References
===

[1] [https://github.com/1Panel-dev/1Panel/blob/dev/docs/README_EN.md](https://github.com/1Panel-dev/1Panel/blob/dev/docs/README_EN.md)

[2] [https://discuss.flarum.org/t/extensions](https://discuss.flarum.org/t/extensions)

[3] [https://www.stopforumspam.com/](https://www.stopforumspam.com/)
