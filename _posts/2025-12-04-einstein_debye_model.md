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
E = 3N_A\langle E \rangle \frac{h\nu}{e^{-\frac{h\nu}{k_BT}}}
$$

The specific heat $$C_V$$ then can be calculated as,

$$
C_V = \frac{\partial E}{\partial T} = 3N_Ak_B(\frac{h\nu}{k_BT})^2\frac{e^{\frac{h\nu}{k_BT}}}{(e^{\frac{h\nu}{k_BT}} - 1)^2}
$$

At low temperature ($$h\nu/(k_BT) \gg 1$$), the term $$e^{\frac{h\nu}{k_BT}}$$ will become very large and will dominate the denominator (over $$1$$ there in the equation above). So, $C_V$ at low temperature in Einstein's model becomes,

$$
C_V \approx 3N_Ak_B(\frac{h\nu}{k_BT})^2e^{-\frac{h\nu}{k_BT}}
$$

The middle term $$(\frac{h\nu}{k_BT})^2$$ will approach infinity when $$T$$ approaches 0. However, the exponential term will approach 0 more quickly, and therefore, altogether, $$C_V$$ in this case will approach 0 as $$T$$ approaches 0. Such an asymptotic value is indeed consistent with the experimental result, but the asymptotic behavior (i.e., how $$C_V$$ approaches 0 at low temperature) is inconsistent with the experimental observation. Experimentally, at low temperature, we have $$C_V \prop T^3$$ [1]. This is the limitation of the Einstein model and fundamentally, it is due to the over-simplified assumption that all particles in the system vibrate independently. Also, the Einstein model does not say anything about the frequency -- it is just something there playing the role of a constant scaling factor.

The Debye model takes a step further, by saying that, instead of particles vibrating independently, they are actually vibrating in a collective manner. Although, this is in fact a quantum effect but we can classically imagine such a collective vibration of particles as a wave propogating in the system. So we may have different waves propogating in the solid simultaneously and each of them may be with different frequency. In quantum mechanics, such waves are described as phonons (i.e., particles corresponding to the wave picture, in the wave-particle duality context). In the phonon context, several relevant quantities/properties are presented below,

$$
\begin{equation}\begin{aligned}E & = \hbar \omega\\\hbar & = \frac{h}{2\pi}\\ \oemga & = 2\pi \nu\\ q & = \frac{2\pi}{\lambda} v_s & = \frac{\lambda}{T} = \lambda \nu = \lambda \omega / 2\pi = \omega / q \\ n_E & = \frac{1}{e^{\frac{E}{k_BT}} - 1} = \frac{1}{e^{\frac{\hbar\omega}{k_BT}} - 1}\end{aligned}\end{equation}
$$

<br />

References
===

[1] [https://eng.libretexts.org/Bookshelves/Materials_Science/Supplemental_Modules_(Materials_Science)/Electronic_Properties/Debye_Model_For_Specific_Heat](https://eng.libretexts.org/Bookshelves/Materials_Science/Supplemental_Modules_(Materials_Science)/Electronic_Properties/Debye_Model_For_Specific_Heat)