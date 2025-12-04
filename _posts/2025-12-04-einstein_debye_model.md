---
layout: post
title: Einstein and Debye Model for Specific Heat
subtitle:
tags: [physics, solid state physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In the low temperature region, the Dulong-Petit describing the specific heat $$C_V$$ as $$3R$$ fails -- $$C_V$$ will approach 0 while $$3R$$ stays a constant across the whole temperature range. Fundamenally, this is due to the failure of the equipartition theorem in the low temperature region (see Ref. [1] for a nice summary). Einstein gave the problem the first shot, according to Planck's quantization of system energy. With Planck's derivation, the system energy average takes a discrete form and should be calculated as follows,

$$
\langle E \rangle = \frac{\sum_{n=0}^{\infty} E_n e^{-E_n / k_B T}}{\sum_{n=0}^{\infty} e^{-E_n / k_B T}}
$$

Substitute $$E_n = n h \nu$$,

$$
\langle E \rangle = \frac{\sum_{n=0}^{\infty} (n h \nu) e^{-n h \nu / k_B T}}{\sum_{n=0}^{\infty} e^{-n h \nu / k_B T}}
$$

Let $$x = e^{-h \nu / k_B T}$$. The denominator is a geometric series $$\sum x^n = \frac{1}{1-x}$$. The numerator involves the derivative of that series. Evaluating the sum yields,

$$
\langle E \rangle_{quantum} = \frac{h \nu}{e^{h \nu / k_B T} - 1}
$$

At high temperature ($$h\nu/(k_BT) \ll 1$$), the denominator term can be Taylor expanded and simplied by picking up the first two terms,

$$
e^{h \nu / k_B T} - 1 = (1 + \frac{h\nu}{k_BT} + \cdots) - 1 \approx \frac{h\nu}{k_BT}
$$

When substituting the result into the expression for the average energy coming from the Planck's result, we obtain,

$$
\langle E \rangle_{quantum} = k_BT
$$

which reproduces the Dulong-Petit result.

> From the result above, we can see that with Einstein's model, it is assumed that particles in the system are all vibrating with the same frequency -- since, obviously, in the formulation, we only have a single frequency $$\nu$$ which is allowed to change as a parameter but will be fixed once a value is picked.

The average presented above is for a single degree-of-freedom of a single partilce and in a system, if we have 1 $$mole$$ particles (i.e., $$N_A$$ of them), the total energy of the system will be,

$$
E = 3N_A\langle E \rangle \frac{h\nu}{e^{-\frac{h\nu}{k_BT}} - 1}
$$

The specific heat $$C_V$$ then can be calculated as,

$$
C_V = \frac{\partial E}{\partial T} = 3N_Ak_B(\frac{h\nu}{k_BT})^2\frac{e^{\frac{h\nu}{k_BT}}}{(e^{\frac{h\nu}{k_BT}} - 1)^2}
$$

At low temperature ($$h\nu/(k_BT) \gg 1$$), the term $$e^{\frac{h\nu}{k_BT}}$$ will become very large and will dominate the denominator (over $$1$$ there in the equation above). So, $C_V$ at low temperature in Einstein's model becomes,

$$
C_V \approx 3N_Ak_B(\frac{h\nu}{k_BT})^2e^{-\frac{h\nu}{k_BT}}
$$

The middle term $$(\frac{h\nu}{k_BT})^2$$ will approach infinity when $$T$$ approaches 0. However, the exponential term will approach 0 more quickly, and therefore, altogether, $$C_V$$ in this case will approach 0 as $$T$$ approaches 0. Such an asymptotic value is indeed consistent with the experimental result, but the asymptotic behavior (i.e., how $$C_V$$ approaches 0 at low temperature) is inconsistent with the experimental observation. Experimentally, at low temperature, we have $$C_V \propto T^3$$ [1]. This is the limitation of the Einstein model and fundamentally, it is due to the over-simplified assumption that all particles in the system vibrate independently. Also, the Einstein model does not say anything about the frequency -- it is just something there playing the role of a constant scaling factor.

The Debye model takes a step further, by saying that, instead of particles vibrating independently, they are actually vibrating in a collective manner. Although, this is in fact a quantum effect but we can classically imagine such a collective vibration of particles as a wave propogating in the system. So we may have different waves propogating in the solid simultaneously and each of them may be with different frequency. In quantum mechanics, such waves are described as phonons (i.e., particles corresponding to the wave picture, in the wave-particle duality context). In the phonon context, several relevant quantities/properties are presented below,

$$
\begin{equation}\begin{aligned}E & = \hbar \omega\\\hbar & = \frac{h}{2\pi}\\ \omega & = 2\pi \nu\\ q & = \frac{2\pi}{\lambda}\\ v_s & = \frac{\lambda}{T} = \lambda \nu = \lambda \frac{\omega}{2\pi} = \frac{\omega}{q} \\ n_E & = \frac{1}{e^{\frac{E}{k_BT}} - 1} = \frac{1}{e^{\frac{\hbar\omega}{k_BT}} - 1}\end{aligned}\end{equation}
$$

Here, $$q$$ refers to the wave vector, hich can be interpreted as the 'angular velocity' in real space, just like the angular velocity in time space can be written as $$\omega = \frac{2\pi}{T}$$. The $$v_s$$ here refers to the velocity of the sound wave (just the wave mentioned above). The expression in the last line gives the expected number of Bosons (phonon follows the Bose-Einstein statistics) for a certain state.

Given the relations presented here, we now can move ahead deriving the Debye version of the specific heat. First, we start with what values that the wave vector $$q$$ are supposed to take. Say we have the wave here as plain waves, formulated simply as,

$$
e^{iqx}
$$

For simplicity, we deal with the 1D situation but the idea can be generalized to 3D. If the size of the system is $$L$$, the periodic boundary condition assumes that beyond the size $$L$$, the system just repeats itself. This means,

$$
e^{iq(x + L)} = e^{iqx}
$$

from which we know,

$$
e^{iql} = 1 \Rightarrow q = \frac{2\pi n}{L}
$$

where $$n = 0, 1, 2, 3, \dots$$. The space that each single $$q$$ takes, is then given as,

$$
\frac{2\pi (n + 1)}{L} - \frac{2\pi n}{L} = \frac{2\pi}{L}
$$

In 3D-space, we simply have the volume that each allowed $$q$$ value takes, is given as,

$$
(\frac{2\pi}{L})^3 = \frac{8\pi^3}{V}
$$

Each $$q$$ value corresponds to a certain mode, i.e., a certain way of collective motion of particles in the system, with a certain wavelength of the wave (remember that $$q = 2\pi/\lambda$$). Taking the 1D case as an example (we can always extend it to 3D by a factor of 3), if we have $$N$$ primitive unit cell (the repeating unit in real space), we know that in total we have $$N$$ degree-of-freedom. We also know that the degree-of-freedom should not depend on how we describe the system, either in real space (which is intuitively more straightforward, by number counting) or in the wave vector space (i.e., the reciprocal space). In the $$q$$-space, each $$q$$ value carries one degree-of-freedom, and therefore, we know that in total, we should have $$N$$ modes in $$q$$-space. For practical systems, the system size is always finite and therefore $$N$$ (again, initially given as the number of primitive cells) should be finite. This further means that in $$q$$-space, the number of modes is also finite. From the derivation above, we already know the volume taken by each $$q$$ value, and accordingly, we should have a certain upper limit for the $$q$$ values to take. Mathematically, we have,

$$
N = \frac{\frac{4}{3}\pi q_D^3}{\frac{8\pi^3}{V}}
$$

Accordingly, we have,

$$
\begin{equation}\begin{aligned}q_D & = (6\pi^2\frac{N}{V})^{\frac{1}{3}}\\\omega_D & = v_sq_D = v_s(6\pi^2\frac{N}{V})^{\frac{1}{3}}\end{aligned}\end{equation}
$$

from which the Debye temperature can be defined,

$$
T_D = \frac{\hbar\omega_D}{k_B} = \frac{\hbar v_s}{k_B}(6\pi^2\frac{N}{V})^{\frac{1}{3}}
$$

To calculate the specific heat, we need to calculate the system energy and from the relation presented above, we know that each mode $$\omega$$ carries the energy of $$\hbar\omega$$. So to obtain the overall energy, we need to integrate over $$\omega$$. Further, there are three recipes for the integration,

- The phonon energy corresponding to a certain mode, namely, $$\hbar\omega$$.

- The number of modes of which the energy falls into the range $$\hbar\omega \rightarrow \hbar(\omega + d\omega)$$ -- this is just the density of states (DOS).

- The number of phonons that each mode can host -- that is the $$n_E$$ as presented above, given by the Bose–Einstein statistics for Bosons.

First, the DOS can be evaluated as,

$$
D(\omega) = \frac{dN}{d\omega} = \frac{V\omega^2}{2\pi^2v_s^3}
$$

> In Debye model, the sound velocity $$v_s$$ of a certain system is assumed to be a constant.

Then the system energy can be calculated as,

$$
E = \int d\omega D(\omega)n_E(\omega)\hbar\omega = \int_0^{\omega_D} d\omega\frac{V\omega^2}{2\pi^2v_s^3}\frac{\hbar\omega}{e^{\frac{\hbar\omega}{k_BT}} - 1}
$$

By replacing variables $$x = \hbar\omega/k_BT$$ and $$x_D  T_D/T$$, the integration above can be evaluated and the specific heat can be calculated as,

$$
C_V = \frac{\partial E}{\partial T} = 9Nk_B(\frac{T}{T_D})^3\int_0^{x_D} dx\frac{x^4e^x}{(e^x - 1)^2}
$$

which reproduces the experimental $$T^3$$ behavior in the low temperature region. Further discussions about the low and high temperature limits can be found in Ref. [1]. Before ending, it should be mentioned that on top of the energy result presented above, the actual actual has a multiplicative factor of 3, which compensates the missing factor of 3 while deriving the total number of modes as discussed above.

<br />

References
===

[1] [https://eng.libretexts.org/Bookshelves/Materials_Science/Supplemental_Modules_(Materials_Science)/Electronic_Properties/Debye_Model_For_Specific_Heat](https://eng.libretexts.org/Bookshelves/Materials_Science/Supplemental_Modules_(Materials_Science)/Electronic_Properties/Debye_Model_For_Specific_Heat)