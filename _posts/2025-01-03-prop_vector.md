---
layout: post
title: Description of Magnetic Structure with Propogation Vector
subtitle:
tags: [solid state physics, magnetism]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<style>
    .faq-container {
        margin: 0 auto;
    }
    .faq-question {
        margin-bottom: 10px;
        font-weight: bold;
        cursor: pointer;
    }
    .faq-answer {
        display: none;
        margin-bottom: 20px;
    }
    .callout {
        background-color: #e8f4fd; /* Light blue background */
        border-left: 5px solid #007BFF; /* Blue accent on the left */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
        font-family: Arial, sans-serif; /* Ensuring the font is consistent */
    }
    .multiline-span {
        display: block; /* or display: inline-block; */
    }
</style>

The magnetic structure sits on top of the nucleus structure and in general, the periodicity of the magnetic structure can be different from that of the nucleus structure. However, the periodicity of the two is indeed related and usually we treat the magnetic structure as a modulation of the nucleus structure, just like whatever other types of modulation of the nucleus structure that can happen in practice, such as the occupancy, distortion, etc. Mathematically, modulations of the nucleus structure can be described by the propogation vector in such a way that is pretty much like the tight binding model – starting from a local component in the origin unit cell (which can be chosen arbitrarily), we can build up the component in remote unit cells by knowing how the component propagates through, via plane waves. Formula-wise, this can be written as,

$$
\vec{m}_{lj} = \sum_{\vec{k}}\vec{S}_{\vec{k}j}exp[-2\pi i \vec{k}\cdot \vec{R}_l]\ \ (1)
$$

where $$l$$ refers to the index of unit cell and $$j$$ labels the atom in the unit cell. $$\vec{R}_l$$ is the vector pointing from the origin of the origin unit cell to the origin of the unit cell labeled with $$l$$, and $$k$$ is just the propagation vector. $$\vec{S}_{\vec{k}j}$$ is our ‘local component’ that sits on the atom labeled with $$j$$, which is fundamentally just the Fourier component corresponding to a certain propagation vector. It is worthwhile mentioning that $$\vec{S}_{\vec{k}j}$$ is a complex vector, which can be written as like,

$$
\vec{S}_{\vec{k}j} = \vec{S}_{\vec{k}j}^r + \vec{S}_{\vec{k}j}^i\ \ (2)
$$

where both the real and imaginary components are in a vector form and therefore in total the Fourier component vector contains 6 parameters. Also, it should be kept in mind that at the end of the day, the magnetic moment as given in Eqn. (1) should be real. So, there for sure should be a way to guarantee the complex form in Eqn. (1) turns out to give something real. The magic actually goes all the way back to the basic principle of Fourier transform where the negative frequency is introduced to make sure we get back the real function out of the complex form of the Fourier transform [1]. Fundamentally, the negative terms in the Fourier series are not independent terms and it is the relation between the positive and negative terms that guarantee the outcome from the complex Fourier series is giving something real. Specifically in the current context for description of the magnetic structure, we have,

$$
\vec{S}_{-\vec{k}j} = \vec{S}_{\vec{k}j}^*\ \ (3)
$$

It is the focus of this blog then to show explicitly how, based on Eqn. (3), one can work out the real form of the magnetic moments from its complex form given in Eqn. (1).

<br>

First, let's put the $$\vec{k}$$ and $$-\vec{k}$$ relevant terms together,

$$
\begin{align}
& \vec{S}_{\vec{k}j}exp[-2\pi i \vec{k}\cdot \vec{R}_l] + \vec{S}_{-\vec{k}j}exp[-2\pi i (-\vec{k})\cdot \vec{R}_ l]\\
& \hspace{-0.8cm} = \vec{S}_{\vec{k}j}exp[-2\pi i \vec{k}\cdot \vec{R}_l] + (\vec{S}_{\vec{k}j})^*\{exp[-2\pi i \vec{k}\cdot \vec{R}_l]\}^*\\
& \hspace{-0.8cm} = \vec{S}_{\vec{k}j}exp[-2\pi i \vec{k}\cdot \vec{R}_l] + \{\vec{S}_{\vec{k}j}exp[-2\pi i \vec{k}\cdot \vec{R}_l]\}^*\ \ \ \ \ \ (4)
\end{align}
$$

from which we can already see that the end result will definitely be real as we are adding up a complex number with its own complex conjugate. But we will keep working till we get the final form and here we write down the complex Fourier vector component in its full explicit form, turning Eqn. (4) into,

$$
(S_{\vec{k}j}^x e^{i\phi_{\vec{k}}^x}\vec{\hat{x}} + S_{\vec{k}j}^y e^{i\phi_{\vec{k}}^y}\vec{\hat{y}} + S_{\vec{k}j}^z e^{i\phi_{\vec{k}}^z}\vec{\hat{z}})exp[-2\pi i \vec{k}\cdot\vec{R}_l] + c.c.\ \ (5)
$$

where $$c.c.$$ means the accompanying complex conjugate of the first term. Here, each of the vector component along $$\vec{\hat{x}}$$, $$\vec{\hat{y}}$$ or $$\vec{\hat{z}}$$ direction is specified by the amplitude and the phase, in its Euler form of the complex coefficient. Focusing on the $$\vec{\hat{x}}$$ component, we have,

$$
\begin{align}
& S_{\vec{k}j}^x e^{i\phi_{\vec{k}}^x} \vec{\hat{x}} exp[-2\pi i \vec{k}\cdot\vec{R}_l] + c.c.\\
& \hspace{-0.8cm} = S_{\vec{k}j}^x e^{i(-2\pi\vec{k}\cdot\vec{R}_l + \phi_{\vec{k}}^x)}\vec{\hat{x}} + c.c.\\
& \hspace{-0.8cm} = 2S_{\vec{k}j}^x cos(-2\pi\vec{k}\cdot\vec{R}_l + \phi_{\vec{k}}^x)\\
& \hspace{-0.8cm} = 2S_{\vec{k}j}^x cos(2\pi\vec{k}\cdot\vec{R}_l)cos(\phi_{\vec{k}}^x) + 2S_{\vec{k}j}^x sin(2\pi\vec{k}\cdot\vec{R}_l)sin(\phi_{\vec{k}}^x)\\
& \hspace{-0.8cm} = A_{\vec{k}j\{cos\}}^x cos(2\pi\vec{k}\cdot\vec{R}_l) + A_{\vec{k}j\{sin\}}^x sin(2\pi\vec{k}\cdot\vec{R}_l)\ \ \ \ \ \ \ \ \ \ \ \ \ \ \ \ (6)
\end{align}
$$

which basically resembles the usually used form when performing the calculation in practice, e.g., as given by Eqn. (3) in Ref. [2].

<br>

References
===

[1] [https://iris2020.net/2020-06-28-fourier_transform/](https://iris2020.net/2020-06-28-fourier_transform/)

[2] [S. V. Gallego, et al. J. Appl. Cryst. (2016). 49, 1941–1956.](https://doi.org/10.1107/S1600576716015491)