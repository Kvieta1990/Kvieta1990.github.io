---
layout: post
title: Local Development of GSAS-II -- New Instructions
tags: [scattering, crystallography, programming, dev, technical, diffraction]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Here in this post, I will put down the instructions for setting up the local developing environment for `GSAS-II`. See the legacy instructions in my previous post [here]() for the old version of the source codes.

Conda Environment Setup
===

```bash
conda create -n gsasii_dev
conda activate gsasii_dev
conda install python numpy matplotlib wxpython pyopengl scipy git gitpython PyCifRW pillow conda requests hdf5 h5py imageio zarr xmltodict pybaselines seekpath pywin32 -c conda-forge -y
```

VSCode Debug Environment Setup
===

Click on `Run and Debug` in the VSCode side bar,

<p align='center'>
<img src="/assets/img/posts/vscode_debug.png"
   style="border:none;"
   width="500"
   alt="abs_geo"
   title="abs_geo" />
</p>

Then click on the gear setting icon on the top to bring up the JSON configuration file,

<p align='center'>
<img src="/assets/img/posts/vscode_debug_config.png"
   style="border:none;"
   width="500"
   alt="abs_geo"
   title="abs_geo" />
</p>

and populate with the following contents,

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Python Debugger: Current File",
            "type": "debugpy",
            "request": "launch",
            "python": "C:\\Users\\kviet\\.conda\\envs\\gsasii_dev\\python",
            "program": "GSASII\\G2.py",
            "console": "integratedTerminal"
        }
    ]
}
```

<br>

> Change the python path to wherever your conda python is located.

Press `F5` or click on the run button as shown below,

<p align='center'>
<img src="/assets/img/posts/vscode_debug_run.png"
   style="border:none;"
   width="500"
   alt="abs_geo"
   title="abs_geo" />
</p>

to launch the GSAS-II GUI.

GSAS-II Binaries
===

For the `main` branch development, we no longer need to grab those binaries manually like in the old days with the `master` branch development. Now with the `main` branch, When launching the GSAS-II GUI for the first time, the program will check the existence of those binaries and if they are not existing in the dedicated location (the `GSASII-bin` directory under the main source code directory, i.e., parallel to the `GSASII` directory under the main source code directory). This will download the pre-built binaries from the web. If we want to build our own version of the binaries, we can refer to the instructions [here](https://advancedphotonsource.github.io/GSAS-II-tutorials/compile.html).

Other options
===

The GSAS-II documentation page for developers also provide other ways of setting up the local development environment -- see [here](https://advancedphotonsource.github.io/GSAS-II-tutorials/install_dev.html#using-pixi-to-install-gsas-ii).