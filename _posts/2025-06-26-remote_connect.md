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
   width="400"
   alt="rdp_interface"
   title="rdp_interface" />
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

<p align='center'>
<img src="/assets/img/posts/macos_win_app.png"
   style="border:none;"
   width="800"
   alt="macos_win_app"
   title="macos_win_app" />
</p>

- To add in a remote, we go to `Devices` and then click on the `+` dropdown in the top-right corner and select `Add PC...`. For the `PC name`, we put in the domain name or the IP address to the remote to connec to. We can give it a friendly name for the remote for us to recognize easily. Compared to the Windows simple interface of the RDP client, the capability of saving sessions is very useful.

- The client can also remember credentials, in a more oragnized way. We can pre-enter credentials and give them some meaningful name and for a certain saved session, we can choose from those pre-entered credentials to be used for the saved session. To do this, in the `Add PC...` interface, we go to the dropdown for `Credentials` and at the bottom of the dropdown, we have the option to `Add Credentials...`. Clicking on it, we can then add the credendial and give it a friendly name for us to remember. The saved session will then be attached to the added credential and it saves the effort of manually inputting the credential for the connection. Also, the added credential can be used for creating another new connection -- when setting up a new connection, just select the previously added credential from the `Credentials` dropdown.

<p align='center'>
<img src="/assets/img/posts/macos_win_app_1.png"
   style="border:none;"
   width="800"
   alt="macos_win_app"
   title="macos_win_app" />
</p>

- It is also possible to use shared folder with the app. When setting up a new connection (or editing an existing one, by right clicking on the saved session and select `Edit...`), we go to the `Folders` tab and check the `Redirect folders` box and then click on `+` in the bottom-left corner to select a local folder to share with the remote. Again, the remote machine will mount the folder in the `Redirected drives and folders` group in `Explorer`.

- The new `Windows App` app does work very smoothly and it looks nice. Just as a side note, I was having some troubles with the previous version of the app, i.e., `Microsoft Remote Desktop` -- when using spotlight or `Alfred` for launching the app, it will launch but it will always hide behind other windows and I needed to click on the icon on the dock to bring it up to the front. As a workaround, I created an `Alfred` workflow。

... to continue ...

<br />

References
===

[1] [https://app.netbird.io/install](https://app.netbird.io/install)

[2] [https://docs.netbird.io/how-to/installation](https://docs.netbird.io/how-to/installation)

[3] [https://docs.netbird.io/how-to/installation#mac-os](https://docs.netbird.io/how-to/installation#mac-os)