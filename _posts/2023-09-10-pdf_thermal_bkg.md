---
layout: post
title: Thermal background in pair distrubtion function (PDF)
subtitle:
tags: [total scattering, theory, solid state physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

This post discusses the thermal background of a pair distribution function (PDF), taking the paper by
K. Page, et al. [1] as the reference and noting down several aspects that are critical for understanding
the topic. The paper was originally reporting an alternative way for coping with the incoherent inelastic
background issue for the total scattering measurement for systems containing hydrogen. Here, the overall
formulation of the one-dimensional (after isotropic averaging, see Appendix-J in the book by M. Dove for
reference [2]) total scattering structure factor from Ref. [1] (Eqn. 11) is reproduced without extra
details -- one can refer to the paper for detailed derivation.

$$
\begin{equation}
\begin{aligned}
S_R(Q) & = exp(-\langle\langle u_i^2\rangle\rangle Q^2)S(Q) + S_{I, coherent} + S_{I, incoherent}\nonumber\\
& = exp(-\langle\langle u_i^2\rangle\rangle Q^2)\frac{1}{N\langle b\rangle^2}\sum_i^N\sum_j^N\frac{sin(Q\,r_{ij})}{Q\,r_{ij}}\nonumber\\
& \hspace{0.5cm} + exp(-\langle\langle u_i^2\rangle\rangle Q^2)L + 1 - exp(-\langle\langle u_i^2\rangle\rangle Q^2)\nonumber\\
& \hspace{0.5cm} + S_{I, incoherent}\nonumber
\end{aligned}
\end{equation}
$$

**📌Note-1**

<br/>

The multiplicative term $$exp(-\langle\langle u_i^2\rangle\rangle Q^2)$$ originates from the atoms being
away from ideal position (from which the structure factor is calculated), either statically or dynamically.
But still, the resulted scattering is coherent and elastic, based on which Debye and Waller derived such a
Gaussian dampening term of the scattering intensity as the function of $$Q$$.

<br/>

**📌Note-2**

<br/>

In practice, the scattering event has the contribution from the inelastic scattering resulted from the energy
exchange with phonons. If considering the coherent contribution only, the energy-integrated intensity (the
$$S_{I, coherent}$$ term in the formulation above) can be approximated by
$$1 - exp(-\langle\langle u_i^2\rangle\rangle Q^2)$$. However, in practice, such an approximation may not
exactly stand. For example, for time-of-flight instrument with detectors located at different scattering angle,
one can always find that the thermal background of the total scattering function $$S(Q)$$ is not sitting on the
same level of background, which then obviously disagree with the indication by the approximation. So, in practice,
a more generic *ad hoc* form of the thermal background corresponding to the coherent inelastic scattering contribution
may be given as,

$$
a - b\,exp(-c\,Q^2)
$$

where $$a$$, $$b$$ and $$c$$ are parameters that can be fitted given the extracted background curve -- how to extract
the background level is a separate issue. For example, one can use the background estimation tool available in OriginLab
or some tools with some Python modules.

<br/>

**📌Note-3**

<br/>

For sure, if the total scattering data is reduced in such a way that the high $$Q$$ level is always to approach $$1$$,
e.g., by adjusting the packing fraction as the tweaking factor, one would have the parameter $$a$$ above always being $$1$$.

<br/>

**📌Note-4**

<br/>

In practice, however, the actual background corresponding to the coherent inelastic scattering does not matter that
much since the Fourier transform of such a background will always give signal limited to the very low-$$r$$ region in
real-space. As such, the 'thermal background' can be reconstructed automatically by the Fourier filter which is usually
applied at the post-procerssing stage of the total scattering data reduction, by forcing the low-$$r$$ region of the
real-space function to follow the baseline. This is equivalent to multiplying a window function onto the real-space function,
the inverse Fourier transform of which will then create the 'thermal background'.

<br/>

<b>References</b>

[1] K. Page, et al. J.Appl.Cryst.(2011).44, 532–539.

[2] Martin T. Dove. Structure and Dynamics: An Atomic View of Materials. 2002, Oxford University Press Inc., New York.