---
layout: post
title: Bond Valence Energy Landscape Calculation with the BondStr Program in Fullprof Suite 
subtitle:
tags: [technical, diffraction, ion diffusion]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/fullprof.svg"
   style="border:none;"
   width="300"
   alt="fullprof"
   title="fullprof" />
</p>

Understanding ion diffusion is essential in battery materials research and characterizing diffusion pathways is critical for rational materials design and optimization. While using the diffraction technique for the characterization of battery materials, we could obtain the refined structure configuration via fitting the diffraction data, with the `small-box` or `big-box` approach, focusing on `local structure` or `average structure`. Accordingly, the ion diffusion behavior analysis could be conducted to yield the critical information about the ion diffusion pathway, based on experimental data, in contract to the similar information obtained via, e.g., molecular dynamics simulation.

Regarding the construction of the ion diffusion pathway from a structure configuration, a typical approach is based on the bond valence (BV) method. Details about the theory and formulation can be found in Refs. [1, 2]. In short, the backbone of the methodology can be summarized as follows,

- The actual contribution to the valence of a central atom from a surrounding atom depends on the distance between them.

- The overall valence of a central atom is the sum of contributions from all surrounding atoms, and in practice, a cutoff needs to be set to define the `surrounding` atoms.

- Positions of ions are allow to vary in reality and the diffusion pathway can be considered as the connection of those points yielding the same bond valence for ions.

- Such an idea can be expanded to define an energy term that considers both the BV-determined energy and a Coulomb interaction term.

- The ion diffusion pathway is then just the isosurface corresponding to a pre-defined energy value.

Such a theoretical framework based on the BV method for ion diffusion pathway construction is implemented in the `BondStr` program in the `Fullprof Suite` [3, 4]. Using the program is straightforward, but still there are some technical details to pay attention to get it working. First, we need to install the `Fullprof Suite`, which can be downloaded from their website [5]. The program is available on all Windows, Linux and MacOS -- I am only using its Windows version and am not sure how robust it is working on Linux or MacOS. Once installed and launched, we should see the following interface of the program,

<p align='center'>
<img src="/assets/img/posts/fullprof_bondstr.png"
   style="border:none;"
   width="1200"
   alt="fullprof_bondstr"
   title="fullprof_bondstr" />
</p>

Regarding the purpose here, we want to click on the button as indicated by the red arrow shown in the picture to launch the `Bond_Str` GUI interface. This will bring up the following interface,

<p align='center'>
<img src="/assets/img/posts/fullprof_bondstr_gui.png"
   style="border:none;"
   width="800"
   alt="fullprof_bondstr_gui"
   title="fullprof_bondstr_gui" />
</p>

To use the program, there may be multiple workflow and here I am only putting down the one that I believe should be most common one. To start, we need a CIF file for the structure we want to analyze, and here I am using the example used in Ref. [4] and the CIF file can be downloaded [here](../assets/files/LiFePO4.cif). In the GUI, we go to `File` $$\Rightarrow$$ `Import CIF`, and select the CIF we want to analyze. With the example CIF file here, the interface will now look like,

<p align='center'>
<img src="/assets/img/posts/fullprof_bondstr_gui_loaded.png"
   style="border:none;"
   width="800"
   alt="fullprof_bondstr_gui_loaded"
   title="fullprof_bondstr_gui_loaded" />
</p>

Looking at the atom lines in the red rectangle, we can see that the loaded atom lines are not following the expected format as given by the example atom string. Here we can manually edit those atom lines to make it look like the following,

<p align='center'>
<img src="/assets/img/posts/fullprof_bondstr_gui_corrected.png"
   style="border:none;"
   width="800"
   alt="fullprof_bondstr_gui_corrected"
   title="fullprof_bondstr_gui_corrected" />
</p>

where we change the `Spec.` part in each atom line to something like `Li+1`, `FE+2`, `P+5` and `O-2` to specify the ideal charge for each element type. Without any further changes, we can click on `Run` in the menu to start the bond valence calculation, in which case we will not calculate the isosurface regarding the ion diffusion pathway. Instead, the bond valence sum (BVS) for each atomic site (Wyckoff position) will be calculated and results can be found in the output `.bvs` file under the `Working Directory` (see the GUI for the specification of the `Working Directory`).

> Here, it should be noticed that in the GUI, we have the `Code of files:` entry which determines the stem name of output files. For example, if we follow the procedures above to calculate the BVS for the example CIF file provided, the `Code of files:` entry would be `LiFePO4_cfl` and accordingly the BVS output file would be `LiFePO4_cfl_sum.bvs` under the `Working Directory`.

Next, to calculate the bond valence energy landscape (BVEL) map (see the #5 bullet point above), we want to put in some parameters in the `Number of Division along x,y,z,...` input box and click on the `BV-Energy-Landscape Map` option. The input box could be given a string like below,

```
103 60 47 Li+1 8.0 3
```

where the first three numbers specify the number of grid points along the x, y and z direction -- in fact, I think it actually means the crystallographic a, b and c direction. The next part `Li+1` specifies the atomic species to calculate and for Li-ion batteries, for sure the Li ions diffusion is what we care about. The next part `8.0` specifies the distance cutoff for considering surrounding atoms as `neighbors`. The last part `3` specifies the delta value for the energy such that the grid volume yielding an energy value smaller than `Emin + delta` will be considered as the `mobile volume`, i.e., the fractional volume that can be potentially considered as being able to contribute to the ion diffusion. Given a certain delta value, for sure, the bigger the fractional volume is, the better the expected ion diffusion will be.

> The calculated fractional volume will be presented in the output window to be presented once the calculation is finished.

> When the calculation is started, a terminal console will be brought up and once the calculation is finished, we can press the `[Enter]` key to kill the terminal and the output window will be brought up automatically. To continue the program running, we need to close the output window.

> The fraction volume mentioned here can be found in the output window following `Volume  fraction for ion mobility in the unit cell:`

Last, we may want to check `Percolation` box and input a value in the `Max. Energy` input box. The value here is also a delta value of energy. In this case, grid points yielding the energy lower than `Emin + delta` will be considered for establishing the percolation network of diffusion ions. For sure, a larger `Max. Energy` value would require longer computation time.

> Here, `Emin` mentioned in the context refers to the ground state energy.

A series of output will be generated along with the calculation and the most interesting output that we may want to check out is probably the `.vesta` file with which we can visualize the energy isosurface using `VESTA`. Opening the generated `.vesta` file in `VESTA`, we should be able to see the energy isosurface like shown below,

<p align='center'>
<img src="/assets/img/posts/bvel_isosurface.jpg"
   style="border:none;"
   width="800"
   alt="bvel_isosurface"
   title="bvel_isosurface" />
</p>

In fact, while loading the `.vesta` file into `VESTA`, by default we won't see the isosurface as presented above. We need to make some changes to the isosurface parameters in `VESTA`. In the menu, we go to `Objects` $$\Rightarrow$$ `Properties` $$\Rightarrow$$ `isosurfaces...` to bring up the window as shown below,

<p align='center'>
<img src="/assets/img/posts/vesta_isosurface_setup.png"
   style="border:none;"
   width="800"
   alt="vesta_isosurface_setup"
   title="vesta_isosurface_setup" />
</p>

Highlighting the part shown in the red rectangle, we can change the parameters for both the dropdown selection (`Positive` as shown in the picture) and the `Isosurface level` (`-1`, and the unit should be `eV`). The displayed isosurface is then the one corresponding to the percolation energy of `-1 eV`.

A demonstration video covering the basic procedures discussed in current post can be found below (click on the image to go to the YouTube video),

<div align="center">
  <a href="https://www.youtube.com/watch?v=SfQpLev9at0" target="_blank">
    <img src="https://img.youtube.com/vi/SfQpLev9at0/maxresdefault.jpg" alt="Demo video for using Bond_Str" width="800">
  </a>
</div>

References
===

[1] [M. Sale and M. Avdeev, *J. Appl. Cryst.* (2012). **45**, 1054–1056.](https://doi.org/10.1107/S0021889812032906)

[2] [S. Adams and R. P. Rao, *Phys. Status Solidi A* (2011) **208** (8), 1746–1753.](https://doi.org/10.1002/pssa.201001116)

[3] [BondStr documentation](../assets/files/po5136sup2.pdf)

[4] [BondStr presentation](../assets/files/6-BVEL-BVS-BondStr.pdf)

[5] [https://www.ill.eu/sites/fullprof/](https://www.ill.eu/sites/fullprof/)