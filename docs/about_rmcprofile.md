---
layout: page
title: About RMCProfile
subtitle: Brief introduction to RMC method and RMCProfile package
---

<p style='text-align: justify;'>The Reverse Monte Carlo (RMC) method will give you a unique view of the atomic
structure of matter that is derived directly from experimental data.
The mainstream usage of RMC is to analyse neutron and x-ray total scattering data
from disordered materials, which are materials for which other probes can only 
give limited data. Examples include liquids and amorphous materials
(which provided the original motivation for the development of the RMC method),
magnetically disordered materials, and crystals with significant thermal disorder,
rotational disorder of molecular groups, and site occupancy disorder.</p>

<br />

<p style='text-align: justify;'>The original code for RMC was RMCA, and was designed for the study an amorphous and
fluid matter. We started to develop RMCProfile as a significant extension of the
original RMCA code in order to add support for new functionality, particularly
to model crystals. In principle we could simply have used RMCA for this, but we
wanted to exploit the information that is specifically contained in the Bragg peaks
separately from its contribution to the total scattering. Since then we have added
a lot of new functionality, and we have also converted the code from Fortran77 to
Fortran95 which has led to significant changes deep in the heart of the program.
RMCA is no longer being developed and is not supported. Thus RMCProfile should be
seen as the evolutionary successor to RMCA and used in preference to RMCA;
an alternative code developed by one of the core developers of RMCA is RMC++,
and if you are not interested in crystalline you might like to take a look at this code.</p>
