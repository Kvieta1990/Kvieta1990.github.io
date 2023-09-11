---
layout: post
title: Rustdesk server setup
subtitle:
tags: [server, web, software]
author: Yuanpeng Zhang
comments: true
use_math: true
---

There are several available software in the market for remote control, such as Teamviewer (probably the most well-known
one), AnyDesk, etc., including the built-in Windows remote control utility (which is actually already very good
especially for the use of local network connection). For cross-platform usage, for sure, Teamviewer is probably the best
among all. However, these days it seems that Teamviewer is trying to push users to but license and I have been suffering
from the 5-minute connection time limit as it is always complaining about commercial user being detected. It is
probably due to that I have teamviewer installed on the company machine and such traffic would be detected and
recognized as commercial use. As such, I have to look for some new tools for my daily remote control usage and I found
[`Rustdesk`](https://rustdesk.com/) is perfectly fitting my need, considering its ease-to-use and also that we can set up
our own relay server to make the connection faster (since the connection traffic is through our own dedicated server
which is not shared with others). This post will cover the key aspects of setting up our own `rustdesk` server.

First, we need to have our own VPS, and in this case, I am use the Oracle always-free VPS based on the Arm64 archicture.
One can refer to my another post about the VPS setup, following the link
[here](https://iris2020.net/2023-09-08-docker_nginx/). Then logging into our VPS, we could follow the instructions
provided by the `rustdesk` official documentation [1] to install the `rustdesk` service on our VPS. Several things to
note down here,

1. In the instruction, it says `port 8000 only needed if you want to use the auto generated install files`, meaning that
if we don't need the automatically generated install files, we don't need to worry about setting up the port 8000.

2. Sometimes, the default port number may have been used by some other services on our VPS, which will then make the
`rustdesk` service not functioning. If that is the situation, we need to change the port number to some other unused
ones. In the initial step, the instruction asks us to enable several port number on our VPS, so, if we are going to use
other port number than the default ones (those specified in the instruction), we should change those port numbers to
whatever we will be actually using.

3. Most VPS service provides, if not all, have control over the opening of port number through the web interface.
Therefore, to open a certain port number for network traffic, not only do we need to enable the port number on our VPS
on the command line, but also we need to enable them through the web interface. Refer to the following demo for detailed
steps,

    ![oracle_vps_port](/assets/img/posts/oracle_vps_port_open.gif)

4. The installation script `install.sh` is a all-in-one script as it will install the `rustdesk` service on our VPS
server as well as setting up the useful `hbbs` and `hbbr` system service (see the note #5 below). The system service
for `hbbs` and `hbbr` is named as `rustdesksignal` and `rustdeskrelay`, respectively. One can then use the command like
`sudo systemctl restart rustdesksignal` or similar to manage the corresponding system service.

    > To the end of the `install.sh` script running, it will print out a server address together with the admin user
    name and password. This is related to the note #1 above in terms of the optional port `8000`. In my case, I am
    having trouble in getting this server up running. However, since anyhow this is optional, we don't need to worry too
    much about it.

5. The `hbbs` abd `hbbr` server instances both need to be installed manually since otherwise the system service set up
in previous step will not be able to find the command to execute.

6. By default, the system service `rustdesksignal` and `rustdeskrelay` will use the default port number for `hbbs` and
`hbbr`, which is `21116` and `21117`, respectively, together with several other port numbers that will be used in the
setup. In my case, both of the two ports are already being used by other service on my server so I had to change the
port number here to something else. To do this, we can edit the following two files for the `rustdesksignal` and
`rustdeskrelay` system services,

    ```
    /etc/systemd/system/rustdeskrelay.service
    /etc/systemd/system/rustdesksignal.service.service
    ```

    We need to change the corresponding line in the two files above to something like below,

    ```
    ExecStart=/opt/rustdesk/hbbr -p 21127 -k _
    ```

    and

    ```
    ExecStart=/opt/rustdesk/hbbs -p 21126 -r rust.iris-home.net:8030 -k _
    ```

    respectively. Here `rust.iris-home.net` is my domain name and `8030` is the port number that I used to replace the
    `8000` port. However, as mentioned earlier, the configuration download server using `8030` port does not work, for some
    reason. Since I don't need the automatically generated configuration file, I did not dedicate efforts in getting it
    working.

7. Once the system service file is changed, we need to run the command below to reload the daemons,

    ```
    sudo systemctl daemon-reload
    ```

8. Getting the `hbbs` and `hbbr` service running, we can then turn to the client side to fill in the relay server
information to use our own `rustdesk` server. The key can be found in the file `/opt/rustdesk/id_ed25519.pub` on our
server. The actual key file name should be different from case to case. Also, if we want to regenerate the key, we can
delete the two key files (both the private and the public keys) and restart the `hbbs` and `hbbr` service.

    > In my case, I was trying to follow the official instructions in Ref. [1] literally and I was trying to run `hbbs`
    and `hbbr` locally. The corresponding key files were generated in the local directory where I ran them locally and
    initially I was using the key therein in the client side configuration on one of my machines. Later on, when I
    realized that we do have the system service available, I started to use the key stored in the
    `/opt/rustdesk/id_ed25519.pub` file on my another machine. This, when trying to establish connection between the two
    machines, cause the `key mismatch` issue. Simply, if we propagate the key in `/opt/rustdesk/id_ed25519.pub` to all
    the client side configuration, we should then be fine.

9. In Ref. [1], it was specifically mentioned that for Windows, we can change the `rustdesk` executable file to include
the `rustdesk` server hostname and the key. However, I did not have success in doing so and I found just adding in the
configuration in the settings would work perfectly so I did not spend efforts in getting it working.

<br/>

<b>References</b>

[1] [https://rustdesk.com/docs/en/self-host/rustdesk-server-oss/install/](https://rustdesk.com/docs/en/self-host/rustdesk-server-oss/install/)