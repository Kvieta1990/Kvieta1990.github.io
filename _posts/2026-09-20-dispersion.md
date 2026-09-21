---
layout: post
title: A Piece of Note on Refraction and Dispersion
subtitle:
tags: [physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<a href="https://en.wikipedia.org/wiki/Refractive_index#/media/File:Mplwp_dispersion_curves.svg" target="_blank">
<img src="/assets/img/posts/Mplwp_dispersion_curves.svg"
   style="border:none;"
   width="800"
   alt="Mplwp_dispersion_curves"
   title="Mplwp_dispersion_curves" />
</a>
</p>

We all know different colors in the sun light will be separated by a prism due to the different refraction index of light with different wavelength. Saying that, we basically mean that the refraction index $$n$$ is a function of the wavelength $$\lambda$$. The specific functional relation depends on the medium -- shown above is a figure from Wikipedia for the $$n-\lambda$$ relation for various types of medium. We also have,

$$
\begin{align}
n & = \frac{c}{v}\\
\lambda\nu & = v
\end{align}
$$

where $$c$$ is for the speed of light in vacuum, $$\nu$$ is for the frequency of light and $$v$$ is for the speed of light in the medium. From the second equation, we can see that the speed of light (specifically, we mean the phase velocity here) in the medium will change with $$\lambda$$. But wait, how about the frequency? What if $$\nu$$ is also changing? What if the frequency is just inversely proportional to $$\lambda$$, i.e., $$\nu \propto 1/\lambda$$ (in which case the speed of light would be a constant as $$\lambda$$ changes -- this is definitely not rare since it is indeed the case in the vacuum)?

So, the relation between the refration index and the wavelength is fundamentally determined by the relation between the frequency and wavelength, namely, the `dispesion relation`. In the example figure presented above, we can pick the `Dense flint SF10` case and fit the `Sellmeier function` (an empirical relationship between refractive index and wavelength [1]) to the data to get,

$$
n(\lambda) = \sqrt{1 + \frac{B_1\lambda^2}{\lambda^2 - C_1} + \frac{B_2\lambda^2}{\lambda^2 - C_2} + \frac{B_3\lambda^2}{\lambda^2 - C_3}}
$$

The fitting result and all the parameters invovled in the function can be found in the following figure,

<p align='center'>
<a href="/assets/img/posts/sf10_sellmeier_fit.png" target="_blank">
<img src="/assets/img/posts/sf10_sellmeier_fit.png"
   style="border:none;"
   width="800"
   alt="sf10_sellmeier_fit"
   title="sf10_sellmeier_fit" />
</a>
</p>

Then, we can work out the `dispersion relation` via reverse engineer, combining all the three equations above, yielding,

$$
\nu(\lambda) = \frac{c}{\lambda n(\lambda)}
$$

With the fitted $$n(\lambda)$$ above, we can plot the `dispersion curve` for this demo case,

<p align='center'>
<a href="/assets/img/posts/sf10_frequency.png" target="_blank">
<img src="/assets/img/posts/sf10_frequency.png"
   style="border:none;"
   width="800"
   alt="sf10_frequency"
   title="sf10_frequency" />
</a>
</p>

## References

[1] [Sellmeier equation](https://en.wikipedia.org/wiki/Sellmeier_equation)