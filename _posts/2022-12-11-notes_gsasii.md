---
layout: post
title: Notes on GSAS-II
subtitle: Tips & Tricks
tags: [scattering, crystallography]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
This post is a collection of notes of tips and tricks for using GSAS-II.
</p>

> Multi-phase refinement

When we have more than one phase in GSAS-II refinement, we should have phase fraction for each of the phases involved. With this regard,
we have, suppose we have two phases, three parameters -- the overall scale factor and two phase fraction factors. In this case, we cannot
let all of these three parameters to be refined since otherwise the program will by a high chance crash due to the strong correlation among
these parameters. Instead, we should refine the scale factor together with one of the two phase fraction factors, while setting a constraint
to specify that overall phase fraction should be 1. To add the constraint, we should follow the instruction in Ref. [1] (see #3 in Step-I),
or [here](/assets/doc/GSASII_Multiphase_setup.pdf). Here it should be noticed that the phase fraction is referred to as `Scale` for each
phase and this `Scale` should not be confused with that of the overall histogram.

> Development environment setup for GSAS-II

> Refer to the documentation of GSAS-II for detailed explanation -- see Ref. [2].

1. Set up the conda environment, using the command below,

    `conda create -n g2python python=3.10 wxpython numpy scipy matplotlib pyopengl  pillow h5py imageio conda requests -c conda-forge`

2. Check out the GSAS-II source code, using SVN,

    `svn checkout https://subversion.xray.aps.anl.gov/pyGSAS/`

3. Here follows, the configuration is specific to the PyCharm IDE. First, launch the PyCharm IDE and open the GSAS-II folder (i.e., the one we just checked out, `pyGSAS`).

4. Go to the top right corner of the interface and click on the drop-down menu as indicated with the green arrow below,

    ![pycharm gsasii](/assets/img/posts/pycharm_gsasii_1.png)

5. Select `Edit Configurations...` to open up the configuration window where we need to fill in information for the python interpreter, the main python script, etc.

6. Refer to the screenshot below for detailed configuration as a typical example,

    ![pycharm gsasii](/assets/img/posts/pycharm_gsasii_2.png)

<br />

<b>References</b>

[1] [https://subversion.xray.aps.anl.gov/pyGSAS/Tutorials/SeqRefine/SequentialTutorial.htm](https://subversion.xray.aps.anl.gov/pyGSAS/Tutorials/SeqRefine/SequentialTutorial.htm)

[2] [https://gsas-ii.readthedocs.io/en/latest/packages.html#gui-requirements](https://gsas-ii.readthedocs.io/en/latest/packages.html#gui-requirements)