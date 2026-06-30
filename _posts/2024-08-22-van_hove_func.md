---
layout: post
title: Van Hove Function
subtitle:
tags: [crystallography, total scattering, dynamic PDF]
author: Yuanpeng Zhang
comments: true
use_math: true
---

The Van Hove function describes the time dependent pair correlation between atoms,

$$
G(r, t) = \frac{1}{4\pi \rho_0Nr^2}\sum_{i,j}\delta(r - |\vec{r}_i(0) - \vec{r}_j(t)|)
$$

$$i$$ and $$j$$ runs over all the $$N$$ atoms in the system. $$\vec{r}_i$$ refers to the position of the atom $$i$$ at time $$t$$. The following formula describes the fundamental construction of the Van Hove function from the dynamic structure factor $$S(Q, E)$$,

$$
G(r, t) = \frac{1}{2\pi\rho_0r}\int S(Q, E)sin(Qr)e^{i\omega t}Q\,dQ\,dE
$$

For more details and references about the formulation, refer to Ref. [1, 2]. From the above formulation, there are two main immediate derivations to reiterate,


1. If we integrate out the energy (as mathematically shown in Eqn. 6 in Ref. [1]), as for a normal pair distribution function (PDF) measurement without discriminating the incoming and outgoing energy, that is just to set $$t = 0$$, in the formula above -- the exponential term in the Fourier transform of the energy part accordingly becomes $$1$$, and we can pull out the energy relevant terms to have Eqn. 6. in Ref. [1]. Such a mathematical result indicates that in a normal PDF measurement, we are actually measuring the equal-time correlation (i.e., simultaneously catch atoms with respect to the pair distance -- since we can only measure pair distances without knowing sample position directly due to the phase problem). In contrast, the non-equal-time correlation (i.e., $$t \neq 0$$) refers to something like catching atom A at $$t = 0$$ but catching atom B at $$t=t_1$$, again, with respect to the pair distance, in which case we then get the evolution of the real-space correlation with time.

2. If we can somehow measure only the elastic scattering, that means the $$S(Q, E)$$ function is a Dirac $$\delta$$ function in the energy space and we know that the Fourier transform of a Dirac $$\delta$$ function is a constant in the coupled space. Therefore, such a measurement will yield a static picture of the atomic configuration, i.e., correlation is constant in time space, or, atoms 'not moving' in the measured picture.

<br>

References
===

[1] [T. Egami & Y. Shinohara, Dynamics of water in real space and time, *Mol. Phys.*, 2019, 117 (22), 3227-3231.](https://doi.org/10.1080/00268976.2019.1649488)