---
layout: post
title: Installation of GSAS-II on Linux
subtitle:
tags: [scattering, crystallography, programming, dev, technical, diffraction]
author: Yuanpeng Zhang
comments: true
use_math: true
---

GSAS-II is a powerful software for analyzing the crystallographic structure from diffraction data. It is written in Python, with some underlying routines in Fortran and C++. Here I am sharing my working pipeline to get `GSAS-II` working on my Linux machine of `Ubuntu` distro. FYI, here below is listed my OS information,

```
Distributor ID: Ubuntu
Description:    Ubuntu 22.04.5 LTS
Release:    22.04
Codename:   jammy
```

Originally I was trying to follow the instruction [here](https://advancedphotonsource.github.io/GSAS-II-tutorials/install-g2f-linux.html) for the installation. The installation was getting through. In my case, it was installed into `$HOME/g2main` directory, and I was trying to launch the program with the `$HOME/g2main/RunGSASII.sh` command. When doing that, expectedly I was missing some binary files needed for `GSAS-II` to run. These days, `GSAS-II` is able to automatically figure out the missing binaries files and download them. Indeed it was doing that in the background, and here below is shared the whole output when initially launching the program,

```
Adding GSAS-II location to Python system path
2 values read from /home/y8z/.GSASII/config.ini
======================================================================
Module pyspg in '/home/y8z/g2main/GSAS-II/GSASII-bin/linux_64_p3.13_n2.2' could not be loaded
error msg: /lib/x86_64-linux-gnu/libm.so.6: version `GLIBC_2.38' not found (required by /home/y8z/g2main/GSAS-II/GSASII-bin/linux_64_p3.13_n2.2/pyspg.cpython-313-x86_64-linux-gnu.so)
======================================================================
*** ERROR: Unable to find GSAS-II binaries. Much of GSAS-II cannot function
binary load error: pyspg not found
pypowder is not available - profile calcs. not allowed
pydiffax is not available for this platform
pypowder is not available - profile calcs. not allowed
binary load error: pytexture not found
binary load error: kvec_general not found
Python/module versions loaded:
  Python:     3.13.5  from /home/y8z/g2main/bin/python3.13. 
  wx:         4.2.3  
  matplotlib: 3.10.3  
  numpy:      2.2.6  
  scipy:      1.16.0  
  OpenGL:     3.1.9  
  Image:      11.3.0 (PIL or Pillow)
  Platform:   linux 64bit x86_64
The requested numpy version, 2.2.6, matches the binary dist, version 2.2
Downloading https://github.com/AdvancedPhotonSource/GSAS-II-buildtools/releases/download/v1.0.1/linux_64_p3.13_n2.2.tgz
Created GSAS-II binary file Build.notes.txt
Created GSAS-II binary file GSASIIversion.txt
Created GSAS-II binary file LATTIC
Created GSAS-II binary file convcell
Created GSAS-II binary file fmask.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file histogram2d.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file kvec_general.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file libgcc_s.so.1
Created GSAS-II binary file libgfortran.so.5
Created GSAS-II binary file pack_f.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file pydiffax.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file pypowder.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file pyspg.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file pytexture.cpython-313-x86_64-linux-gnu.so
Created GSAS-II binary file unpack_cbf.cpython-313-x86_64-linux-gnu.so
Binary files created in /home/y8z/g2main/GSAS-II/GSASII-bin/linux_64_p3.13_n2.2
Binaries installed from https://github.com/AdvancedPhotonSource/GSAS-II-buildtools/releases/download/v1.0.1/linux_64_p3.13_n2.2.tgz to /home/y8z/g2main/GSAS-II/GSASII-bin

2 values read from /home/y8z/.GSASII/config.ini
======================================================================
Module pyspg in '/home/y8z/g2main/GSAS-II/GSASII-bin/linux_64_p3.13_n2.2' could not be loaded
error msg: /lib/x86_64-linux-gnu/libm.so.6: version `GLIBC_2.38' not found (required by /home/y8z/g2main/GSAS-II/GSASII-bin/linux_64_p3.13_n2.2/pyspg.cpython-313-x86_64-linux-gnu.so)
======================================================================
*** ERROR: Unable to find GSAS-II binaries. Much of GSAS-II cannot function
  GSAS-II:    5818/v5.5.0 posted 31-Jul-25 16:38 (no new updates) [07d17120]
Configuration settings saved as /home/y8z/.GSASII/config.ini
```

To the bottom part, we can see some errors occurring, meaning that although the `pyspg` (which I think is some module regarding the space group operations) is pulled successfully, it cannot be used due to the incompatible version of `GLIBC` library on my machine. Since this library is a system-level library and I believe I cannot do anything about it. As such, this way of installation fails for me, so have to switch to alternative ways, which we do have, and one of them is detailed below.

---

First, we can set up a `conda` environment and install some dependencies,

```
conda create -n gsasii_dev
conda activate gsasii_dev
conda install python numpy matplotlib wxpython pyopengl scipy git gitpython PyCifRW pillow conda requests hdf5 h5py imageio zarr xmltodict pybaselines seekpath pywin32 -c conda-forge -y
```

I am not sure whether this is the best way to do it and also I am not sure whether all the dependencies installed here will be needed. The commands here are those I used for doing the local GSAS-II development and testing, for which detailed instructions can be found [here](https://iris2020.net/2025-04-21-gsasii_dev_new/). But anyhow, it turns out to be working for me, though, we still have something else to do as will be shown later.

Next, we want to download the `gitcompile.py` script from the `GSAS-II-buildtools` repository,

```bash
mkdir ~/Dev/gsasii
cd ~/Dev/gsasii
wget https://raw.githubusercontent.com/AdvancedPhotonSource/GSAS-II-buildtools/refs/heads/main/install/gitcompile.py
```

where `~/Dev/gsasii` is just my choice of location to host everything and we can change it per needed. Then, with the `conda` environment created above being activated (if not, `conda activate gsasii_dev` will do the job), we can try to run,

```bash
python gitcompile.py
```

to compile all those necessary binaries and install `GSAS-II` to our machine. If things are running smoothly through, we are expecting a `GSAS-II.desktop` shortcut file created on our `Desktop`. By default, Ubuntu may not allow the launching of the shortcut by double clicking, in which case we should right click on the shortcut and click on `Allow Launching`.

In my case, when trying `python gitcompile.py`, I was seeing some errors, due to the missing of some necessary modules or tools. Specifically, I was missing `Cython` and `meson`. I was then using the following commands to install them,

```bash
pip install Cython
pip3 install meson
```

Then `python gitcompile.py` would run through successfully -- shortcut was created and I could launch `GSAS-II` without problems.

Using this way, the `GSAS-II` source codes would be pulled directly from the `git` repository into the directory we created above (i.e., `~/Dev/gsasii` in my case) and it is happening automatically when executing `python gitcompile.py`. If we have `git` installed on our machine, we can go into `~/Dev/gsasii/GSAS-II` and execute `git pull` to pull the latest version of the codes afterwards. Then the program can still be launched by double clicking on the created shortcut to reflect the most recent changes to the program.

> As a side note, if `git pull` execution is not successful due to some intended or unintended local changes to the repository (i.e., files inside, e.g., `~/Dev/gsasii/GSAS-II`), there are some extra steps to go through before running `git pull`. First, we can run `git status` to check whether we have some local changes as compared to the last `git pull`. If we do see changes and if those changes are unintended, we can run `git checkout -- .` when located in `~/Dev/gsasii/GSAS-II` on the command line. This will revoke all the local changes and roll the codes back to the status of last `git pull`. If changes are intended, meaning that we do want to keep those changes and potentially contribute to the `GSAS-II` central code base on GitHub, we can do `git checkout -b my_branch` (replace `my_branch` with whatever meaningful name regarding the changes), followed by,

```bash
git add . --all
git commit -m "a short meaningful commit message"
git push --set-upstream origin my_branch
```

> This requires one has access to the `GSAS-II` GitHub repository to be able to create remote branches. We may need to talk to [Dr. Brian Toby](https://www.anl.gov/profile/brian-h-toby) regarding this. Alternatively, one can choose to `fork` the `GSAS-II` repository to their own `GitHub` account, do the development there and make contribution to the central `GitHub` repository by submitting a `pull request`. As for how to `fork`, how to use `git` in general and how to submit a `pull request`, there are tons of materials online and these days with `GPT` models and tools like `ChatGPT`, `Claude`, `Gemini` and so on, it should not be a big problem to do them at all.