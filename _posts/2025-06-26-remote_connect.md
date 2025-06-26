---
layout: post
title: Technical Notes on Remote Connection with Thinlinc, Windows RDP and SSH
subtitle:
tags: [network, remote, utils]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/remote_connect.png"
   style="border:none;"
   width="800"
   alt="remote"
   title="remote" />
</p>

<br />

In this post, I will put together the technical notes about several tools for remote connection. This is just a record of my strugglings with each of them while setting them up on different operating systems. Some of the notes may be very specific to my case but some of them may also be general.

Remote Desktop (RDP)
===

Here I am specifically referring to the Windows remote desktop (RDP) service with which we can remote connect to our Windows machine without a third-party software like TeamViewer, AnyDesk, Rustdesk, RemotePC, etc. It turns out the built-in RDP service on Windows is already very useful in its usability and speed. The limitation as compared to those third party options is probably that the Windows RDP can only be used for local network connections. It is possible to port forwarding for external connections but 1) security may be a concern, and 2) most organization will block traffic for most of the network port so it is just not possible to connect to the RDP on those internal machines. However, as demonstrated in my another post [here](https://iris2020.net/2025-06-23-netbird_notes), we can use the Network Address Translation (NAT) traversal (内网穿透) technique provided by something like `netbird` to realize the external connection, bypassing the need for the direct connection through the out-facing port and port forwarding. Anyways, it is a very useful tool and here I am noting down a few tricks about its usage.

## Windows

- The Windows client for RDP connection is with a very simple interface. It does not have the functionality to save multiple sessions. We have to put in the destination to connect to, for each connection. For sure, if we always have one destination, it will remember that. Though, as will be detailed below, there is a way to hand craft some scripts and shortcuts for connecting to different machines.

<br />

References
===

[1] [https://app.netbird.io/install](https://app.netbird.io/install)

[2] [https://docs.netbird.io/how-to/installation](https://docs.netbird.io/how-to/installation)

[3] [https://docs.netbird.io/how-to/installation#mac-os](https://docs.netbird.io/how-to/installation#mac-os)