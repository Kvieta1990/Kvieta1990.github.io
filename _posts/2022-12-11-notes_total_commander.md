---
layout: post
title: Notes on Total Commander Setups
subtitle: Tips & Tricks
tags: [tool, technical, software, Windows]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
This post is a collection of notes of tips and tricks for Total Commander setups. This is a software as an alternative
to the file explorer on Windows. It allows double column management and operations, making life much easier in terms of
file and directory operations.
</p>

<p align='center'>
<img src="/assets/img/posts/TC.png"
   style="border:none;"
   width="800"
   alt="tc"
   title="tc" />
</p>

> Import/Export configuration file

By default, the configuration files for TC are stored in `C:\Users\USER_NAME\AppData\Roaming\GHISLER`, so when reinstalling
the program, normally we don't need to worry about the importing of previous configuration files. However, in case we need to
worry about that, we can back up the configuration files under that directory and copy them to wherever suitable for the new
installation. To get an idea about where the program is looking for those configuration files, we can go to `Help` -> `About Total Commander` in TC.
There, we will see the location of the setting files. Click [here](/assets/zip/GHISLER.zip) for the backed up zip files for the configuration.

> Button definition

We can define buttons under the menu items in the TC interface to get quick access to programs, files, etc. This can be done manually. Also, we can
go to the installation location of TC and look for files ending with `bar` or `BAR` and we can change or add items from there. Here follows is a typical
definition of a button,

```
button40=shell32.dll,19
cmd40=openbar %Commander_path%\Sysapp.bar
iconic40=1
menu40=SystemTools
```

The first entry specifies the icon. The second one specifies the command to execute when the button is pressed. Here in this specific example, we can
see that the command is pointing to another `bar` file, which will then make the current button a parent group containing a whole bunch of children buttons
defined in the `Sysapp.bar` file.

> Editing multiple files

Alternative to the solution with F4Menu (which is discussed elsewhere as posted below), I am providing a native solution. With this configuration, we can select multiple files and press a defined key combo to edit them using the defined editor.

Go to `Configuration` -> `Options...` -> `Misc.` -> Specify a hotkey combo (like in my case, I chose `Ctrl+E`) under the `Redefine hotkeys (Keyboard remapping)` section in the right panel. Then click on the magnify glass next to the `Command:` input box down below. A `Choose command` window will pop up where we can specify the command we want to associate with the hotkey we just defined.

At the bottom of the pop-up window (the `Choose command` window), we can click on the `New...` button, which will bring up another pop-up window where we can give the command a name (e.g., `em_notepadpp`). After giving it a name, we will be lead to another window where we can put down the specific commands we want to execute. In the first box, we put in the full path to the editor executable that we want to use. In my case, I am using Notepad++ and this is my full path, `C:\Program Files (x86)\Notepad++\notepad++.exe`. To make sure it is working properly, we may want to include double quotation mark before and after the full path. In the second input box, we want to put `%P%S` as the parameter of the command - this time, we don`t need any quotation mark and also we don`t have space between the two parameters. Then if we want to, we can specify the icon down below but I guess this is not necessary.

Then we can just select the command we just defined (e.g., `em_notepadpp`) in the `Choose command` window and click on `OK`. Then we will go back to the `Configuration` window where we can see the defined command is already populated in the `Command:` input box. Clicking on the `√` on the right hand side will confirm the command. Then we are done by clicking on the `OK` button.

The same trick was posted on TC user forum -- see Ref. [1].

> Configure the Image Viewer

By default, Total Commander will use the old Windows image viewer which looks ugly and also sometimes does not work at all for opening some images. To use the new version of the image viewer on Windows, we need to select an image file, then go to `Files` => `Associate With...` and make sure the `Replace with: PhotoViewer.dll` is `unchecked` (by default, this box would be checked and TC would use the old Windows image viewer for opening an image), as shown below,

<p align='center'>
<img src="/assets/img/posts/tc_image_viewer_config.png"
   style="border:none;"
   width="500"
   alt="tc_imc"
   title="tc_imc" />
</p>

Instructions originate from Ref. [2].

<br />

<b>References</b>

[1] [https://www.ghisler.ch/board/viewtopic.php?t=28817](https://www.ghisler.ch/board/viewtopic.php?t=28817)

[2] [https://ghisler.ch/board/viewtopic.php?t=72249](https://ghisler.ch/board/viewtopic.php?t=72249)