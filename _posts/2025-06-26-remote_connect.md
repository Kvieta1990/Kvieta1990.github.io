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

- The Windows client for RDP connection is with a very simple interface. It does not have the functionality to save multiple sessions. We have to put in the destination to connect to, for each connection. For sure, if we always have one destination, it will remember that. Though, as will be detailed below, there is a way to hand craft some scripts and shortcuts for connecting to different machines. One thing to note here is, the Windows RDP client does have the functionality to remember the credential. As shown in the screenshot below, we can click on the `Edit` link to edit the credential for the domain name that we want to connect to and then the client will remember the credential until we click on the `Delete` link.

<p align='center'>
<img src="/assets/img/posts/rdp_credential.png"
   style="border:none;"
   width="800"
   alt="remote"
   title="remote" />
</p>

- As pointed out above, the Windows RDP client cannot remember sessions. To save the effort of manually inputting different domain names for each different connection, we can use the command line interface (CLI) of the Windows RDP client, `mstsc`. We can create a batch script to embed the command for connecting to a certain remote. Here down below is a typical example,

   ```
   @echo off
   mstsc /v:<remote_domain_or_IP>
   ```

   We save the script as `.bat` file and we can launch it by double clicking. If we do not want to see a terminal popping up while executing the script, we can then created VB script to call the script in a terminal-less way, and here down below is a typical example to call the batch script above,

   ```
   Set WshShell = CreateObject("WScript.Shell")
   WshShell.Run "c:\<HERE_GOES_THE_PATH>\vwindows.bat", 0
   Set WshShell = Nothing
   ```

   The script should be saved as `.vbs` file and we can double click to launch it. Further, we can create a shortcut for the VB script by right clicking on the file and choose `More options` $$\rightarrow$$ `Send to` $$\rightarrow$$ `Desktop (create shortcut)`. This will create a shortcut on the desktop for the script. Even further, we can go to the Windows `Start` and right click on an application there and click on `Open file location` to bring up the location in `Explorer` where Windows saves shortcuts appearing in `Start`. Usually it is,

   ```
   C:\ProgramData\Microsoft\Windows\Start Menu\Programs
   ```

   Then we copy/cut and paste the desktop shortcut into the folder and the shortcut will be available in `Start`.

   > The `<>` here is only for quotation purpose and should not be included in the command.

- We can add a shared drive for the RDP connection so that the connected remote will mount the specified folder remotely and can therefore get access to files on the local machine. To do it, on the RDP client interface, we click on `Show Options` in the bottom-left corner, then go to the `Local Resouces` tab, then `Local devices and resources`, and then click on the `More...` button down at the bottom. In the pop-up windows, we can choose the folder we want to mount onto the remote machine. Then we can find the local folder mounted in the remote machine -- see the `Redirected drives and folders` group in `Explorer`.

## MacOS

On MacOS, we have the `Windows App` app which previously was the `Microsoft Remote Desktop` app (the previously installed `Microsoft Remote Desktop` app still works).

<br />

References
===

[1] [https://app.netbird.io/install](https://app.netbird.io/install)

[2] [https://docs.netbird.io/how-to/installation](https://docs.netbird.io/how-to/installation)

[3] [https://docs.netbird.io/how-to/installation#mac-os](https://docs.netbird.io/how-to/installation#mac-os)