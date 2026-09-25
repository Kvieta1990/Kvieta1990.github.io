---
layout: post
title: Systematic Absences (Crystallography)
subtitle:
tags: [physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In reciprocal space, some certain combinations of $$hkl$$ indices are not observable for certain types of structures. When the missing pattern of $$hkl$$ is systematic, we call it systematic absence. For example, for body centered Bravais lattices (oI, tI and cI), only those $$(hkl)$$ reflections that satisfy the condition $$h + k + l = 2n$$ ($$n = 0, 1, 2, \dots$$) can exist. All other reflections are *systematically absent*.

Fundamentally, it is either because we have non-primitive unit cell or there exist some glide and/or screw symmetry elements. In both cases, a given position in the unit cell will generate other positions in the unit cell via either the centering shift or the translation induced by screw and/or glide. The combination of terms from those positions will create conditions for which the structure factor is calculated to zero. Let's first take a centering case in 2D so that we can quickly get the idea.

<p align='center'>
<a href="/assets/img/posts/2d_sa.png" target="_blank">
<img src="/assets/img/posts/2d_sa.png"
style="border:none;"
width="800"
alt="2d_sa"
title="2d_sa" />
</a>
</p>

Shown in the figurue is the 2D centered rectangle lattice (oc) and we choose the conventional cell with a center -- the green arros indicate the conventional unit cell vectors. Accordingly, the reciprocal space cell vectors are shown with the blue color on the right. The structure factor in this case can be calculated as,

$$
S = \sum_i b\,e^{2\pi i(hx_i + ky_i)} = b\,e^{2\pi i(h\cdot 0 + k \cdot 0)} + b\,e^{2\pi i (\frac{1}{2}h + \frac{1}{2}k)} = b\bigg[1 + e^{\pi i(h + k)}\bigg]
$$

where we have two atoms in the unit cell, located at $$(0, 0)$$ and $$(\frac{1}{2}, \frac{1}{2})$$, respectively. Given the result, when $$h + k = 2n + 1$$, where $$n = \cdots, -2, -1, 0, 1, 2, \cdots$$, $$S$$ is calculated to be $$0$$, giving rise to the systematic absence. This means that for all those reciprocal space lattice point with the sum of $$h$$ and $$k$$ being odd, the corresponding reflection should not exist. Those reciprocal space lattice points are represented withe open red circles on the right in the figure.

<p align='center'>
<a href="/assets/img/posts/2d_pc.png" target="_blank">
<img src="/assets/img/posts/2d_pc.png"
style="border:none;"
width="800"
alt="2d_pc"
title="2d_pc" />
</a>
</p>

Now, if we choose to use a primitive unit cell for describing the lattice, as shown in the figure above, we see that the reciprocal space cell vectors catch all those solid circles. In this case, those open circles do not need to be there at all. Accordingly, we don't have any systematic absence -- since we don't need it at all. Several things to note here,

- From the two unit cell choices above, we can see that with the conventional unit cell choice, the unit cell in real space is larger and accordingly, the reciprocal space unit cell is smaller.This means we have more lattice points per unit area in reciprocal space, as compared to the case of primitive unit cell choice. But we know that the number of reflections points should not depend on whatever unit cell choice in real space at all -- the reflection points are all physical so indeed they should be independent of unit cell choice which can be totally arbitrary. So, logically, we are expecting some of the reciprocal space lattice points with the conventional unit cell choice are not real/physical.

- In reciprocal space, we can notice that the lattice system type is also centered rectangle.

- In an earlier post (see [here](../2026-09-17-crystallography_r1/#bravais-lattice)), it was pointed out that the centered rectangle lattice cannot be made primitive (mp). But here we are saying that we can choose different cells for describing the lattice. Are they contradictory? The answer is No. In the earlier post, what I was discussing is the lattice system which cares about the lattice point symmetry. For a centered rectangle, the first thing we want to know is, it is a rectangle, with 2-fold rotation symmetry, and therefore it belongs to the orthorhombic lattice system. Pinning down the big scope, we then say, OK, it is a rectangle lattice, but it is not a primitive one. Then we have the centered rectangle. Here, what we are saying is totally different -- either the conventional centered cell or the primitive cell, they are both the cell we choose to describe the lattice. In fact, we have infinite number of choices for the unit cell to describe a lattice -- see [here](../2026-09-18-crystallography_r2/#random-collection-of-topics).

Following exactly the way of calculation as shown above for the 2D lattice example, we can establish the systematic absences condition comprehensively. Detailed table can be found in Refs. [1, 2]. There are multiple types of systematic absences,

- General condition

    - Integral condition -- originates from centering, and $$hkl$$ sets meeting the condition exist in 3D.

    - Zonal condition -- originates from glide planes, and $$hkl$$ sets meeting the condition exist in 2D, something like $$hk0$$, $$hhl$$, etc.

    - Serial condition -- originates from screw axes, and and $$hkl$$ sets meeting the condition exist in 1D, namely $$h00$$, $$0k0$$ and $$00l$$.

- Special condition

    The special condition only cancels the contribution from atoms on a particular site.

    $$F(\mathbf{h}) = \sum_{\text{sites } s} F_s(\mathbf{h}), \qquad F_s(\mathbf{h}) = \sum_{j\in s} f_j\, e^{2\pi i\,\mathbf{h}\cdot\mathbf{r}_j}$$

    A special condition for site $$s$$ means $$F_s(\mathbf{h}) = 0$$ for certain $$\mathbf{h}$$. It says nothing about the other sites. So,

    - If every occupied site has $F_s = 0$ at $\mathbf{h}$, then $F = 0$ and the reflection is systematically absent.

    - If some site doesn't obey that condition, then the reflection is present. However, since only a portion of atoms contribute to the reflection, it is systematically weaker.

Before ending this post, I will put down another piece of note on systematic absence, which we will quite often encounter when working with symmetry lowering distortions or magnetic structures. In either case, we would have some new periodicity emerging on top of the original lattice. Accordingly, the periodicity change as compared to the original cell can be described with the $$k$$ vector. For example, when working with magnetic structure solution, usually the first step is to find out the $$k$$ vector. In reciprocal space, we know that the lattice is periodic and therefore $$k$$ vectors different by reciprocal space lattice vectors are supposed to be equivalent. However, regarding the $$k$$ vectors associated with the physically observed reflections, we need to be careful about such equivalence -- it only applies to the situation rigorously when primitive cell is used.

<p align='center'>
<a href="/assets/img/posts/k_vec.png" target="_blank">
<img src="/assets/img/posts/k_vec.png"
style="border:none;"
width="800"
alt="k_vec"
title="k_vec" />
</a>
</p>

A 1D example is presented above. If we choose the conventional setting, there are some systematically absent lattice points as indicated by those open circles. In this case, the two blue $$k$$ values are different by a reciprocal space lattice vector, but they are not equivalent. The reason is, the lattice vector they are different by, is systemtically absent. In another word, the actual periodicity in reciprocal space is $$a^*$$ corresponding to the primitive unit cell choice but not $$a^*/2$$ corresponding to the conventional unit cell setting.

## References

[1] [Reflection conditions](https://dictionary.iucr.org/Reflection_conditions) -- here it should be noticed that the table involved in this reference has an error -- the middle row in the '000l' row for the screw axes table should not contain the $$4_1$$ entry.

[2] International Tables for Crystallography, Volume A, 5th edition -- see section 2.2.13 in this edition for the detailed information about the reflection conditions (the opposite of the systematic absences).