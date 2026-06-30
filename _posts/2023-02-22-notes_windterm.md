---
layout: post
title: Notes on setting up WindTerm
subtitle: A platform integrating both terminal and file explorer
tags: [tool, technical]
author: Yuanpeng Zhang
comments: true
use_math: true
---

This post is about the configuration of the tool [WindTerm](https://github.com/kingToolbox/WindTerm), which is a quicker and better SSH/Telnet/Serial/Shell/Sftp client for DevOps.

<p align='center'>
<img src="/assets/img/posts/windterm.png"
   style="border:none;"
   width="900"
   alt="windterm"
   title="windterm" />
</p>

The configuration on Windows is pretty smooth while for MacOS, it does need a
bit attention.

- In my case, I was having a little tool called `fig` which would send some
  escape sequence to the terminal by default. Such escape sequence could not be
  handled properly by `WindTerm` and therefore will output a lot of weird
  characters like `ESC]...` anytime when launching a local shell or executing
  any commands. For the moment, we have to remove the `fig` tool if that
  applies.

- To install a new theme, we can download the theme package and put it under `/Applications/WindTerm.app/Contents/MacOS/global/themes` directory.
  For example, we can refer to the installation instruction for [Dracula](https://draculatheme.com/windterm) theme.

- To make `X11` working on MacOS to enable remote graphics, we may need to run
  the command `xhost +local:` on a local shell (which should be a once-for-all
  solution). Then we can set `External X display` in the session settings -- we
  can leave the input box as blank to use the default server address.

  > In my case, I have various remote servers and some of them are not
    supporting the X11 connection. However, when trying to configure them
    separately to use X11 connection for some of them and not to use for others,
    it seems for the moment we cannot do such a configuration separately -- the
    default settings will always win, which may be a bug. In this case,
    interestingly, I can select not to use the X11 display in the default
    settings and the remote graphics is still working.
