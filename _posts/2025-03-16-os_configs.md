---
layout: post
title: Config, Tips and Tricks for Different Operating Systems 
subtitle:
tags: [Windows, Mac, Linux]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/oses.png"
   style="border:none;"
   width="800"
   alt="abs_geo"
   title="abs_geo" />
</p>

In this blog, I will be noting down some useful configurations, tips and tricks for all the operating systems that I have been using along the way. I am a big fan of nothing, but at the same time I am also a big fan of everything. So in my work and life, I have been using all the three main types of operating systems, Windows, MacOS and those Linux distros like Ubuntu, OpenSuSE, etc. While using any of them, there are some interesting (sometimes dirty) configurations and little tricks for stuff and that is something I will be noting down in this blog, for my own record purpose and potentially for others.

{: .box-note}
<a id="p1"></a>
<a href="#p1"><b>#1: </b></a>Set up a profile for Windows CMD, like the `.bashrc` file for Linux/MacOS bash shell.

First, we want to open Windows registry, by pressing `Win+R` to bring up the run box and then type in `regidit` to further bring up the registry. Then we want to go to the following key,

```
Computer\HKEY_CURRENT_USER\Software\Microsoft\Command Processor
```

If `Autorun` is not there in the list, we can create it by right clicking on the blank space on the right and go to `New` $$\rightarrow$$ `String Value` and give it the name of `Autorun`. Then double click on the new created `Autorun` item and in the `Value data:` box, we put in the full path to the configuration file that we want to use. In my case, the `Value data:` box has the following contents,

```
E:\System\Configs\profiles.cmd & if exist "C:\ProgramData\anaconda3\condabin\conda_hook.bat" "C:\ProgramData\anaconda3\condabin\conda_hook.bat"
```

where the part that follows the first `&` symbol is something added in automatically by `conda`. Then in the configuration file, we can put in our preferred configurations such as defining some aliases. Here below is my version of the configuration file,

```cmd
@echo off

Title DOS
for %%* in (.) do set ADDRESS= %%~n*
prompt $E[1;30;40m[$E[1;35;40mZYP$E[1;34;40m-$E[1;32;40mDOS $E[1;31;40m@ $E[1;36;40m$d$E[1;30;40m]$E[1;31;40m$$$s$E[1;37;40m
:: Temporary system path at cmd startup
:: set PATH=%PATH%;"C:\Program Files\Sublime Text 2\"

:: conda activate

:: Commands
DOSKEY la=wsl ls -al $*
DOSKEY lat=wsl ls -alth $*
DOSKEY lar=wsl ls -altrh $*
DOSKEY rr=wsl rm -rf $*
DOSKEY pwd=wsl pwd
DOSKEY vi=vim $*
DOSKEY subl=sublime_text $*
DOSKEY pr=sublime_text E:\System\Configs\profiles.cmd
DOSKEY open=explorer $*
DOSKEY ca=conda activate $*
DOSKEY da=conda deactivate
DOSKEY clist=conda env list

:: Common directories
:: DOSKEY gd=cd D:\GooDrive
```

<br>

References
===

[1] [https://powder.ornl.gov/total_scattering/data_reduction/mts_abs_ms.html](https://powder.ornl.gov/total_scattering/data_reduction/mts_abs_ms.html)

[2] [https://docs.mantidproject.org/nightly/algorithms/SetSample-v1.html#algm-setsample](https://docs.mantidproject.org/nightly/algorithms/SetSample-v1.html#algm-setsample)

[3] [https://github.com/mantidproject/mantid/blob/main/instrument/sampleenvironments/SNS/InAir.xml](https://github.com/mantidproject/mantid/blob/main/instrument/sampleenvironments/SNS/InAir.xml)

[4] [https://docs.mantidproject.org/nightly/algorithms/DefineGaugeVolume-v1.html](https://docs.mantidproject.org/nightly/algorithms/DefineGaugeVolume-v1.html)