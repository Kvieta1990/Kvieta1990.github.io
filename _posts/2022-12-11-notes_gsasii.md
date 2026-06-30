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
This post is a collection of notes, tips and tricks for using GSAS-II.
</p>

- Multi-phase refinement

    When we have more than one phase in GSAS-II refinement, we should have phase fraction for each of the phases involved. With this regard,
    we have, suppose we have two phases, three parameters -- the overall scale factor and two phase fraction factors. In this case, we cannot
    let all of these three parameters to be refined since otherwise the program will by a high chance crash due to the strong correlation among
    these parameters. Instead, we should refine the scale factor together with one of the two phase fraction factors, while setting a constraint
    to specify that overall phase fraction should be 1. To add the constraint, we should follow the instruction in Ref. [1] (see #3 in Step-I),
    or [here](/assets/doc/GSASII_Multiphase_setup.pdf). Here it should be noticed that the phase fraction is referred to as `Scale` for each
    phase and this `Scale` should not be confused with that of the overall histogram.

<br />

<b>References</b>

[1] [https://subversion.xray.aps.anl.gov/pyGSAS/Tutorials/SeqRefine/SequentialTutorial.htm](https://subversion.xray.aps.anl.gov/pyGSAS/Tutorials/SeqRefine/SequentialTutorial.htm)