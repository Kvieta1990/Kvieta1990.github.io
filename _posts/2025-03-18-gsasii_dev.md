---
layout: post
title: Local Development of GSAS-II
tags: [scattering, crystallography, programming, dev, technical, diffraction]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
GSAS-II is a powerful software for analyzing the crystallographic structure from diffraction data. It is written in
Python, with some underlying routines in Fortran and C++. It is possible to grab the source codes of GSAS-II, develop
and test it locally. This post covers the steps to set up the local development environment for GSAS-II.
</p>

1. According to the instructions presented in Ref. [1], the following command could be executed to set up the conda environment for the local development of GSAS-IIe,

    ```bash
    conda create -n g2python311 python=3.11 numpy=1.26 matplotlib scipy wxpython  pyopengl imageio h5py hdf5 pillow requests ipython conda spyder-kernels scons sphinx sphinx-rtd-theme jupyter git gitpython -c conda-forge
    ```
2. Here, I am using `VSCode` for codes editing and local testing. First, we want to clone the GSAS-II repository, by running,

    ```bash
    git clone https://github.com/AdvancedPhotonSource/GSAS-II.git
    ```

    > If we don't have the permission to create branch in the remote, we can fork the repository to our own GitHub repo, do the implementation in our own forked repo and submit pull request to make contribution to the central repo.

3. Then inside `VSCode`, we want to open the cloned repo by going to `File` $$\rightarrow$$ `Open Folder...` and select the cloned GSAS-II repo.

4. In `VSCode`, we click on the `Run and Debug` item in the left bar and if we never set up the local development environment for the folder that we are currently opening in `VSCode`, we can see a link `create a launch.json file`. Clicking on the link, we should be opening the JSON file to edit, and we can paste in the following contents,

    ```json
    {
        "version": "0.2.0",
        "configurations": [
            {
                "name": "Python Debugger: Current File",
                "type": "debugpy",
                "request": "launch",
                "python": "C:\\Users\\kviet\\.conda\\envs\\g2python311\\python",
                "program": "GSASII\\GSASII.py",
                "console": "integratedTerminal"
            }
        ]
    }
    ```

    where `C:\\Users\\kviet\\.conda\\envs\\g2python311\\python` is the full path to the Python specific to the `g2python311` conda environment that we created in `step-1` above. The `\\` symbol there in the path is necessary on Windows.

5. In early versions of GSAS-II codes, we had to manually download those necessary binaries files into the `GSAS-II` source codes directory for the local version of the program to work. However, with the up-to-date version of GSAS-II, we don't have to do that anymore. The program will check the existence of those necessary binary files and if they are found to be missing, GSAS-II will download them from the web and put them into the proper location. This really saves a lot of efforts!

6. In `VSCode`, now we can launch our local dev version of GSAS-II by simply pressing the `F5` key.

<br />

<b>References</b>

[1] [https://gsas-ii.readthedocs.io/en/latest/packages.html#gui-requirements](https://gsas-ii.readthedocs.io/en/latest/packages.html#gui-requirements)