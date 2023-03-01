---
layout: post
title: Notes on Forum Setup with MyBB
subtitle: Side notes for the setup along the way of configuration
tags: [tool, technical, forum, PHP]
author: Yuanpeng Zhang
comments: true
use_math: true
---

This post is for putting down some random sites notes as I struggle through the
process of setting up a forum hosted with MyBB.

<p align='center'>
<img src="/assets/img/posts/mybb.png"
   style="border:none;"
   width="100"
   alt="mybb"
   title="mybb" />
</p>

Here is a very nice tutorial for running through the initial installation and
setup,

<iframe width="840" height="472" src="https://www.youtube.com/embed/0Solyc0Jwgo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

> In this tutorial, the necessary modules were installed and configured during the
setup. We don't have to follow this practice. A better way may be to configure
all the necessary preparations beforehand, according to the [MyBB doc](https://docs.mybb.com/1.8/install/).

- `nginx` configuration

    > For the `nginx` configuration, the this [gist](https://gist.github.com/Kvieta1990/57887cf7d9e828dca382ce02ef1bb890) is a minimal
    working example on my host to host the reverse proxy for both the MyBB and JupyterHub service.

    - SSL setup

        > We need to generate a key file and use that for generating an SSL certificate file. For most SSL certificate, we
        need to pay. In my case, I was using ORNL service located at [here](https://certmanager.ornl.gov/index.php) (which
        can only be accessed from within ORNL firewall).

- LDAP authentication setup

    > To set up the LDAP authentication, we can use the `MyBB LDAP Authentication`
    plugin [1]. The setup within MyBB admin control panel can be found <a href="/assets/img/posts/mybb_ldap_plugin_config.png" target="_blank">here</a>.
    It should be pointed out that,

    - This is for configuring against the ORNL LDAP server. For other servers, the configuration should be as simple as
    replacing those inputs with information relevant to a other specific servers.

    - The protocol version does matter -- typically, if we set it to `2`, it won't work, at least in my case for
    the configuration against the ORNL LDAP server (for which we have to set it to `3`).

    - However, the `LDAP Protocol Version` setting in the admin control panel above does not seem to be working properly.
    As a temporary solution, I just hard coded the setting in the `{MyBB_Main_Directory}/inc/plugins/` file, which is
    attached <a href="/assets/files/mybbldap.php" target="_blank">here</a> (refer to line 728-734).

    - Also, it seems that the `Connection Confidentiality` setting in the admin control panel is not working properly
    either. Again, temporarily, the setting was hard coded into the attached file above.

    - The attached file <a href="/assets/files/index.php" target="_blank">here</a> is a minimal working example of using LDAP in PHP.

    - The LDAP configuration file can be found at `/etc/ldap/ldap.conf`,

        ```
        BASE    dc=xcams,dc=ornl,dc=gov
        URI     ldaps://ldapx.ornl.gov
        TLS_CACERT      /etc/ssl/certs/ca-certificates.crt
        ```
- Announcement Templates

    > There is a plugin for posting announcements on MyBB forum, at the top of
    the index landing page, and the official web site is Ref. [2]. With the
    plugin successfully installed, one can follow the route down below to edit
    the announcements for different groups of users.

    `AdminCP` -> `Templates & Style` -> `Templates` -> `Default Templates` ->
    `DNT Announcements Templates`

- Sometimes when installing some plugins, the installation may fail due to some
  internal errors. To make the error message printout more useful, we can
  change the level of error logging following the route below,

    `Configurations` -> `Settings` -> `Server and Optimization Options` ->
    `Error Logging Medium` -> `Log errors`

<b>References</b>
[1] [https://community.mybb.com/mods.php?action=view&pid=1043](https://community.mybb.com/mods.php?action=view&pid=1043)
[2] [https://community.mybb.com/mods.php?action=view&pid=659](https://community.mybb.com/mods.php?action=view&pid=659)
