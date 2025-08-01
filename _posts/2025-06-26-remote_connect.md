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

- The new `Windows App` app does work very smoothly and it looks nice. Just as a side note, I was having some troubles with the previous version of the app, i.e., `Microsoft Remote Desktop` -- when using spotlight or `Alfred` for launching the app, it will launch but it will always hide behind other windows and I needed to click on the icon on the dock to bring it up to the front. As a workaround, I created an `Alfred` workflow [4], following the step below,

   - Bring up the `Alfred ` main window by either clicking on the icon in the top task bar and selecting `Preferences` or using the shortcut (in my case, `Super+Space`) to bring up the `Alfred` search box and then right clicking on the `Alfred` icon to the right of the search box to select `Preferences`.

   - Click on `Workflows`, followed by clicking on `+` at the bottom to create a new `Blank Workflow`.

   - Right click on the canvas to select `Inputs` $$\rightarrow$$ `Keyword` and the put in the keyword to use. This will be the characters we will type in the search box to call the workflow. In the dropdown selection to the right, select `No Argument`. Give the node a friendly title in the `Title` box. Also, we can drag an icon to the icon box.

   - Right click on the canvas to select `Actions` $$\rightarrow$$ `Launch Apps/Files` and then browse and select the `Microsoft Remote Desktop` app from the `/Applications` directory.

   - Drag to connect the input node to the app launch node.

   - Right click on the canvas to select `Actions` $$\rightarrow$$ `Run Script`. In the language dropdown selection, select `/usr/bin/osascript (AppleScript)` and put in the following contents,

      ```
      delay 0.5

      tell application "Microsoft Remote Desktop"
         activate
      end tell
      ```

      and then click on `Save`.

   - Then drag to connect the input node to the `Run Script` node.

   - With this setup, typing in the specified keyword will start the workflow and it will do two things in parallel -- launch the `Microsoft Remote Desktop` and run the `AppleScript`. In the `AppleScript` above, the first line `delay 0.5` sleeps for 0.5 second to wait for the app launching. Then after the 0.5 second waiting, the app should already be launched and the `AppleScript` will then continue to activate the app, i.e., bring it up to the front.

## Using Microsoft Account

Nowadays, Windows uses the Microsoft account on the computer -- the Microsoft account is the cloud account with which we log in the Office 365 service. With this capability, we can then set up the `Windows Hello` login and use PIN instead of the password to log in the computer. This is indeed very convenient, but for the remote desktop connection purpose, the `Windows Hello` + `PIN` login mechanism would not work. We have to disable the `Windows Hello` and `PIN` login to use the Microsoft account for RDP connection. For example, in my case, my Microsoft account user name is the email address associated with my Microsoft account and that is just my user name which we will use as the user name for the RDP connection. The password is just the cloud account login password, i.e., the one used for logging into the Office 365 service.

To disable `Microsoft Hello` and `PIN` login, go to `Start` $$\rightarrow$$ `Settings` $$\rightarrow$$ `Accounts` $$\rightarrow$$ `Sign-in options`. In the `Additional settings` section, disable the option `For improved security, only allow Windows Hello sign-in for Microsoft accounts on this device (Recommended)`. Then in the top `Ways to sign in` section, click on `PIN (Windows Hello)` to expand and then click on `Remove` (or something similar -- on my computer, I already disable it so cannot see originally what the button says before being disabled) to disable the `PIN` login. Then we may need to log out and log back in to let the configuration take effect, after which we should be able to use the Microsoft account and the corresponding password for the RDP connection.

---

Thinlinc
===

Thinlinc is a very useful tool for remote connection to Linux servers with the GUI support. For personal use, we can set up our own Thinlinc server on our VPS and use the Thinlinc client for remote connecting to our Linux server. Instructions given in Ref. [5, 6] could be referred to and it should be straightforward to set up, given some special attention to some of the following bullet points.

- If we connect to the remote Linux server in a headless way (e.g., using `SSH`), we may not be able to install the thinlinc server successfully, even with the X11 graphics support.

- When setting up the Thinlinc server, we are actually expected to set up two things -- the `master` node and the `agent` node. The `master` node is responsible for taking in the request for connection and it will transfer the request to the `agent` node which is the actual node that we will be finally connected to (i.e., to see the remote desktop). If we are only having one VPS and we are trying to set up the Thinlinc server on it, the single server will be used as the `master` and the `agent` node simultaneously. Regarding this, there is one thing we need to specifically pay attention for the setup. If we are trying to connec to the Thinlinc server from internal network using the internal IP or domain name, we might be able to just connect fine without any special settings on the server. However, if we are trying to connect from externally -- for example, if we are using `netbird` to form a private network (see my another post [here](https://iris2020.net/2025-06-23-netbird_notes/)) and using the `netbird` IP address or domain name for the Thinlinc connection, we would fail to connect and there is a key to set up here. On the Linux server, we want to edit the file `/opt/thinlinc/etc/conf.d/vsmagent.hconf` and look for the line containing `agent_hostname=` and change it to `agent_hostname=<NETBIRD_DOMAIN>` (without the bracket), where `<NETBIRD_DOMAIN>` is just the domain name we will use for the Thinlinc connection. What this does is to specify the domain name for the agent explicitly so the `master` node knows how to find the `agent` node specifically in the case of such external connections.

   > After changing the configuration, we should run the following command to restart the `thinlinc` services to make the changes take effect [8],

   ```bash
   sudo systemctl restart vsmserver vsmagent tlwebadm tlwebaccess
   ```

- Thinlinc provides the CLI way to start the connection, with `tlclient`. On MacOS, The CLI is located at `/Applications/ThinLinc\ Client.app/Contents/MacOS/tlclient`. On Wndows, the CLI can be found by going to `Start`, searching for `Thinlinc` and right clicking on it to select `Open file location`. This will bring us to the folder where the `Start` item for `Thinlinc` is located. Then right click on `Thinlinc client` to select `Open file location` which will bring us to the location where the `tlclient.exe` executable file is located.

- The `tlclient` documentation can be found in Ref. [7]. With the client, however, it seems that we cannot provide the private key for the passwordless connection. The only way I found to use the private key for the `tlclient` connection is to specify the configuration file with the `-C` flag (see Ref. [7] for the instruction) and we can specify the private key file to use in the configuration file. The question is then how to prepare the configuration file. The way I go with is this. First, I started the `Thinlinc` GUI and put in all the configuration as usual, including the selection of using the private key file for the connection, the port to use (usually, it is the `22` prot but sometimes it can be a different port), etc. Then Thinlinc will create a configuration file in the background for the connection just initialized with the GUI. Then we can find the configuration file created by Thinlinc, make a copy of it and make whatever needed changes to the file, e.g., change the private key file to use. On both MacOS and Linux, the configuration file created by Thinlinc is located at `~/.thinlinc` where `~` represents the user home directory.

   > It should be noticed that even we specify the configuration file to use, we may still need to specify the `-P` flag (for the specification of the password). Though, we don't have to give it the real password since we are going to use the private key for the connection. It is just that if we don't include the `-P` flag, it seems `tlclient` will not initialize the connection automatically and we need to manually click on the `Connect` button once the client interface is brought up. So, as a little trick, we can provide the `-P` flag with a dummy password string like 'ddd' or whatever.

   > On Windows, it seems that Thinlinc created configuration file will be written into the registry directly and I am not sure whether it is possible to output the configuration to a local file on Windows to be used for later connection. However, if we have a Linux or MacOS machine, we can copy the configuration file created on those machines to Windows and the Windows client can read in those configuration files created from other operating systems without any problems. Here I am backing up the configuration which we can grab and adapt to our needs. The entries included inside the `<>` bracket (including the bracket itself) need to be replaced with their real values -- [click me](/assets/files/tlclient.conf).

- Thinlinc does not support session saving. But given the `tlclient` CLI tool, we can create our own scripts for connecting to different servers. On Windows, we can create batch files and use the same trick as presented above to use the VB scripts for launching the `tlclient` without an explicit terminal showing up. Here I am sharing the batch script and VB script for connecting to the ORNL Analysis cluster with `tlclient` -- [click me](/assets/files/tlclient_win_scripts.zip). On MacOS, we can use `Alfred` to create a workflow for initializing the `tlclient` connection. The procedure would be very similar to the one presented above for the `Microsoft Remote Desktop` launching. The idea is the same -- we use keyword to initialize the workflow to start the `tlclient` connection and we use another node to activate the GUI to bring it up to the front. Also, we need to give the program a little bit delay so that the window activation part of the script can find the window to activate. Here below I am putting down the scripts that I am using for the `Alfred` workflow for connecting to the ORNL Analysis Thinlinc server.

   ```bash
   cd /Users/y8z/Packages/thinlinc
   /Applications/ThinLinc\ Client.app/Contents/MacOS/tlclient analysis.sns.gov:2222 -C ~/.thinlinc/tlclient_analysis.conf -u y8z -p PLACE_HOLDER --minimize
   cd -
   ```

   and

   ```
   delay 2.0

   tell application "ThinLinc Client"
      activate
   end tell
   ```

   > The workflow organization will be exactly the same as the one detailed above for `Microsoft Remote Desktop` -- the only major difference is that the `Launch Apps/Files` action in the workflow above needs to be replaced with the action of `Run Script` for which we need to select the script language as `/bin/bash` and paste in the bash script shared above (the first one).

   > In the bash script shared above, we have two `cd` commands executed before and after the `tlclient` call. This is to take care of the running log created by `tlclient` while running -- the program will create a running log file at the location where the command is being executed. If we are using the `Alfred` way to start `tlclient`, it will put the running log file to our home directory, which can be a bit annoying if we want to keep things clean and organized. That is what the `cd` command is for -- change to a dedicated location before executing `tlclient` so the running log file can be saved into that dedicated place to keep things clean and organized.

   > In my case, for the delay, I found using `2.0` is probably necessary. In fact, even with the long delay as 2 seconds, still randomly, it takes longer for the `tlclient` initialization so to cause that the Thinlinc window is not activated automatically.

   > The reason why we need to do such a trick of delay and window activation is because if we don't do this, the Thinlinc window will hide itself behind other windows and the connection will never be initialized until we manually bring the Thinlinc window up to the front.

- Even specifying the configuration file to use and the file actually contains the specification of the key file to use for the connection, still some of the connection is not picking up the specified key file in the configuration file. It seems that `tlclient` tends to always pick up the same key file for all the connections to different servers. In this case, I guess we have to add the same key to all the servers that we want to connect to.

---

File Transfer via SSH, SFTP, etc.
===

If we have SSH access to a server, we can use tools based on the `SCP` (Secure Copy Protocol, e.g., `scp` -- it is the tool with the same name as the protocol) or `SFTP` (SSH File Transfer Protocol, e.g., `rsync`, `WinSCP` and `CyberDuck`), for file transfer. Normally, using those tools is pretty much routine and works the same way as for the SSH connection, using either the password or private key file for the connection. However, here I am putting down a little problem with my file transfer connection. So, initially, I could SSH into my MacOS server without any problems but I just could not use whichever tools mentioned here for file transfer. After struggling a bit with this, I found the problem is specific to my Mac shell configuration. These days, MacOS uses `zsh` shell as the default shell (cannot remember when they started to do this) instead of the `bash` shell. Initially, this caused some trouble to me since some tools are still initializign the `bash` shell (but I do want to use the `zsh` shell) and my original solution for such problems was to include the `zsh` command in the `.bashrc` and `.bash_profile` files to automatically switch to the `zsh` shell when the `bash` shell is initializing. This was working fine but I never realized this was causing the failure with establishing the file transfer channel through `SCP` or `SFTP` tools. The phenomemon was that any time when I used, e.g., the `rsync` tool for file transfer, the commmand would just hang there for forever without any error message or logs (which is the reason why I could not figure out the reason for the hanging, for quite a while). Same phenomemon happens for the `WinSCP` and `MobaXTerm` as well. Later on, I realized that the `zsh` command included in the `bash` initialization configuration files caused the hanging. Not sure exactly why, but anyhow the shell switching is confusing those file transfer tools and after removing the `zsh` command in those `bash` initialization configuration files, the issue is gone. In fact, since by default we will be using the `zsh` shell anyhow, it is probably beneficial to remove all configurations in those `bash` initialization configuration files just in case any of those configuration are messing things up.

<br />

References
===

[1] [https://app.netbird.io/install](https://app.netbird.io/install)

[2] [https://docs.netbird.io/how-to/installation](https://docs.netbird.io/how-to/installation)

[3] [https://docs.netbird.io/how-to/installation#mac-os](https://docs.netbird.io/how-to/installation#mac-os)

[4] [https://www.alfredapp.com/](https://www.alfredapp.com/)

[5] [https://www.cendio.com/thinlinc/docs/install/](https://www.cendio.com/thinlinc/docs/install/)

[6] [https://www.cendio.com/thinlinc/docs/install/simple-nat/](https://www.cendio.com/thinlinc/docs/install/simple-nat/)

[7] [https://www.cendio.com/resources/docs/tag/client_cmdline.html](https://www.cendio.com/resources/docs/tag/client_cmdline.html)

[8] [https://community.thinlinc.com/t/start-stop-thinlinc-server/1163](https://community.thinlinc.com/t/start-stop-thinlinc-server/1163)