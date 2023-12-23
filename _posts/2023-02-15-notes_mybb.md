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

        TLS_CACERT      /etc/ssl/certs/ldapserver.pem

        SSL start_tls
        SSL on
        TLS_REQCERT allow
        ```

    - In the MyBB LDAP configuration page (see the link above for a screenshot of the page), if we choose
    the SSL connection, we should notice the notes above the selection drop-down menu which states that if
    we do want to use the SSL connection, we need to save the LDAP server's certificate to the trusted
    store properly. To do this, we need to,

        - First, obtain the certificate from the LDAP server

            > To do this, we can use the following command,

            ```
            openssl s_client -showcerts -connect ldapx.ornl.gov:636 </dev/null | sed -ne '/-BEGIN CERTIFICATE-/,/-END CERTIFICATE-/p' > ldapserver.crt
            ```

            where `ldapx.ornl.gov` is the ORNL LDAP server in my case. This will save the LDAP server's certificate to the local file
            named with `ldapserver.crt`. In practice, we could have multiple certificates in the same certificate file which gives
            the so-called PEM (i.e., Privacy Enhanced Mail) format.

        - Add the obtained certificate to the trusted store on the client side

            > To add the certificate to the trusted store on the client side, we can do the following steps,

            - `sudo cp /path/to/certificate.crt /usr/local/share/ca-certificates/certificate.crt`

            - `sudo update-ca-certificates`

            > Attention that we should replace `/path/to/certificate.crt` to whatever specific to our case.

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

- Secure connection

    > Specifically concerning the secure connection requirement by ORNL, on top
    of the setting up of the SSL certificate to enable HTTPS connection, we
    also need to pass the security scanning. During the configuration process,
    we did come across multiple issues concerning the secure connection such as
    the HTST requirement was not met, the certificate being used not trusted,
    etc. The following link is my `private` (which is only visible to myself, if
    you do want to see it, please get in touch with Yuanpeng Zhang at
    [zhangy3@ornl.gov](zhangy3@ornl.gov)) notes following the link below,

    [https://www.notion.so/iris2020/SSL-SMTP-Setup-37e94c2e004a40d7b2d16cf7d29515b5?pvs=4](https://www.notion.so/iris2020/SSL-SMTP-Setup-37e94c2e004a40d7b2d16cf7d29515b5?pvs=4)

    <br />

    > The note is just a random collection of issues and the solutions, so it is
    not presented in an organized manner.

- Domain name configuration

    > If we want to configure the MyBB forum to stay under a certian directory
    rather then the root directory, there are two aspects we need to configure
    as detailed here. For example, by default we have MyBB configured to be
    hosted at 'https://my.domain.com' and we want to change it to be hosted at
    'https://my.domain.com/forum'. To do this, first we need to change the nginx
    configuration, assuming we are using nginx for the reverse proxy and web
    host. The following snippet should be good to go in the server section,

    ```
    server {
        listen 80;
        server_name powder.ornl.gov;
        return 302 https://powder.ornl.gov/forum$request_uri;
    }

    server {
        large_client_header_buffers 4 16k;
        listen 443;

        ssl on;
        ssl_certificate     /home/cloud/Dev/SSL/powder.pem;
        ssl_certificate_key         /home/cloud/Dev/SSL/powder.key;

        server_name powder.ornl.gov;

        # root /usr/share/nginx/html;
        root /var/www/html;
        index index.html

        ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
        ssl_prefer_server_ciphers on;
        ssl_session_timeout 1d;
        ssl_session_cache shared:SSL:50m;
        add_header Strict-Transport-Security max-age=31536000;

        location /forum {
            index index.php index.html index.htm index.nginx-debian.html;
        }

        location /hub/ {
            proxy_pass http://127.0.0.1:8000;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header Host $host;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

            # websocket headers
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection $connection_upgrade;
            proxy_set_header X-Scheme $scheme;

            proxy_buffering off;
        }

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        }

        location ~ /\.ht {
            deny all;
        }

        # Managing requests to verify letsencrypt host
        location ~ /.well-known {
            allow all;
        }
    }
    ```

    > Then we need to change the settings in MyBB to adapt to the directory change.
    To do this, we need to go to the location where all the MyBB files are stored
    (e.g., `/var/www/html/forum`). Then we need to change the settings according to
    our specific domain name and directory name where we want to host the forum,

    ```
    ./inc/settings.php:$settings['bburl'] = "https://powder.ornl.gov/forum";
    ./inc/settings.php:$settings['cookiedomain'] = ".powder.ornl.gov";
    ./inc/settings.php:$settings['homeurl'] = "https://powder.ornl.gov/forum";
    ```

    > The settings in MyBB should be consistent with that in nignx considering the
    domain name and the directory, as is the case in the example above.

- To add in a favicon (the little icon that will show on the tab of the web page) for MyBB, we
just need to put the `favicon.ico` file (has to be exactly the name) in the root directory of the
MyBB host, e.g., `/var/www/html`. If the whole forum contents are located within a sub-folder under
the web host folder, like this `/var/www/html/forum`, the `favicon.ico` file should then sit under
`/var/www/html` but NOT `/var/www/html/forum`.

- To add in javascript in the header for all pages of the forum -- for example, we may want to add
in the javascript for tracking the forum traffic using some services -- we need to go to `Admin CP`
-> `Templates & Style` -> `Templates` -> `Default Templates` -> `Header Templates` -> `header`. Then
we can just put the javascript to the top of the template block.

<b>References</b>

[1] [https://community.mybb.com/mods.php?action=view&pid=1043](https://community.mybb.com/mods.php?action=view&pid=1043)

[2] [https://community.mybb.com/mods.php?action=view&pid=659](https://community.mybb.com/mods.php?action=view&pid=659)
