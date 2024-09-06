---
layout: post
title: Description of Magnetic Structure with Propogation Vector
subtitle:
tags: [crystallography]
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

The magnetic structure sits on top of the nucleus structure and in general, the periodicity of the magnetic structure can be different from that of the nucleus structure. However, the periodicity of the two is indeed related and usually we treat the magnetic structure as a modulation of the nucleus structure, just like whatever other types of modulation of the nucleus structure that can happen in practice, such as the occupancy, distortion, etc. Mathematically, modulations of the nucleus structure can be described by the propogation vector in such a way that is pretty much like the tight binding model -- starting from a local component in the `origin unit cell` (which can be chosen arbitrarily), we can build up the component in remote unit cells by knowing how the component `propogates` through, via plane waves. Formula-wise, this can be written as,

$$
m_{lj} = \sum_{\{\vec{k}\}}\vec{S}_{\vec{k}j}exp\{-2\pi i\vec{k}\cdot\vec{R}_l}
$$

where $$l$$ refers to the index of unit cell and $$j$$ labels the atom in the unit cell. $$\vec{R}_l$$ is the vector pointing from the origin of the `origin unit cell` to the origin of the unit cell labeled with $$l$$, and $$\vec{k}$$ is just the `propogation vector`. $$\vec{S}_{\vec{k}j}$$ is our 'local component' that sits on the atom labeled with $$j$$, which is fundamentally just the Fourier component corresopnding to a certain `propogation vector`. It is worthwhile mentioning that 