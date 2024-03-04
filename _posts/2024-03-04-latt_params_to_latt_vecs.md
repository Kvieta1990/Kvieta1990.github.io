---
layout: post
title: Construct lattice vectors from lattice parameters
subtitle:
tags: [crystallography]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In crystallography, quite often we would use the 6 lattice parameters to describe the unit cell, namely, a, b, c, $$\alpha$$,
$$\beta$$ and $$\gamma$$. In some cases, we would also need to construct those lattice vectors ($$\vec{a}$$, $$\vec{b}$$ and $$\vec{c}$$) in Cartesian coordinate system. For example, when we want to put atoms into the unit cell, we would have to have those lattice vectors given in the Cartesian coordinate so that atom coordinates can be easily obtained. Here in this blog, I will present a generic derivation for how to construct the lattice vectors in the Cartesian coordinate system, from the 6 lattice parameters.

   > It should be noticed that in practice, we would have different choices of convention to put the lattice vectors in the Cartesian coordinate system. Here, we will first put the c-axis along the z-axis. Then, we put b-axis in the y-z plane, with the y-axis project always positive. Finally, we put down the a-axis, with the x-axis project always positive.

<p align='center'>
<img src="/assets/img/posts/a_b_projection.png"
   style="border:none;"
   width="1000"
   alt="abproj"
   title="abproj" />
Lattice vectors projection onto the Cartesian coordinate system -- (left) b-axis projection & (right) a-axis projection.
</p>

First, it is straightforward to put down the lattice vector $$\vec{c}$$ as,

$$
\vec{c} = c\vec{\hat{z}}
$$