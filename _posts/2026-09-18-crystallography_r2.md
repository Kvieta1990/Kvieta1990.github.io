---
layout: post
title: Some (Random) Notes On crystallography (Part-2)
subtitle:
tags: [maths, crystallography, physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Let's continue the discussion on some (random) topics about crystallography, and this time, I will start with looking at the diagram for the space group $$R3m$$.

<p align='center'>
<a href="/assets/img/posts/R3m.png" target="_blank">
<img src="/assets/img/posts/R3m.png"
   style="border:none;"
   width="1000"
   alt="R3m"
   title="R3m" />
</a>
</p>

Meaning carried by those symbols in the diagram are well explained in Refs. [1, 2] and I won't reproduce them here. I will just put down some notes which I think will be helpful in understanding the diagram and the underlying symmetry.

## $$R3m$$ diagram

First, it should be noted that the diagram shows the projection of the lattice onto the $$(111)$$ plane -- in the layer below the space group diagram [3], I put a dummy structure created in Vesta [4] viewed from the $$[111]$$ direction in the rhombohedral setting. As the result of the screw axis (there is something we need to talk about on this, will come back to it), we have some of those generated points leveraged above the screen plane, indicated by $$+\frac{1}{3}$$ and $$+\frac{2}{3}$$. The fraction here refers to the diagonal $$[111]$$ axis of the rhombohedral unit cell, and on the right side of the figure, I present the unit cell viewed from another angle so that the $$[111]$$ direction lies in the screen and points to the right. Also, I labeled out those fractions for better understanding what we mean by $$+\frac{1}{3}$$ and $$+\frac{2}{3}$$. An animation is included below to see how we transition from the projection view to the side view -- nothing fundamental but just for better constructing the spatial picture in our brain,

<p align='center'>
<a href="/assets/img/posts/R3m_projection_rotation.gif" target="_blank">
<img src="/assets/img/posts/R3m_projection_rotation.gif"
   style="border:none;"
   width="1000"
   alt="R3m_projection_rotation"
   title="R3m_projection_rotation" />
</a>
</p>

Second, taking the $$R3m$$ diagram as an example, I want to put down a general note about space groups. It may sound obvious but is indeed critical in understanding concepts like the Wyckoff position. We know a space group contains a lot of symmetry operations, like all those symbols in the space group diagram for $$R3m$$ presented above. Meanwhile, it is also important to be aware of that all those symmetry operations are with respect to some special elements in the lattice, like specific rotation axes, diffrently located and oriented mirror planes, etc. Trivial fact, but it is worthwhile keeping in mind -- I will come back to this when talking about the Wyckoff positions.

## Symmorphic Space Group

Third, if we look at the symmetry operations involved in the space groups (see Ref. [3]), we will realize that no fractional offsets are involved in all those operations -- only permutations. This is something special and noteworthy -- space groups like this are called symmorphic space groups. By definition, it refers to those space groups in which all the generating symmetry operations, apart from the lattice translations, leave one common point fixed [2, 5]. For example, for $$R3m$$, the common fixed point is $$(0, 0, 0)$$. Following the IUC convention (see section 8.1.6 in Ref. [2]), we can tell whether a space group is symmorphic straightforwardly -- if they are, the Hermann-Mauguin symbol does not contain any glide or screw operations. This means the generators can neither be glide nor screw if the space group were to be symmorphic. However, this does not mean the space group cannot contain screw or glide operations. A typical example is already presented here in this post -- the $$R3m$$ space group. As can be told from the diagram, we do have both glide planes and screw axes in the space group but $$R3m$$ is indeed symmorphic. This cries for some noting.

<p align='center'>
<a href="/assets/img/posts/R3m_symmorphic.png" target="_blank">
<img src="/assets/img/posts/R3m_symmorphic.png"
   style="border:none;"
   width="1000"
   alt="R3m_symmorphic"
   title="R3m_symmorphic" />
</a>
</p>

In the bottom-left panel of the figure, I presented the 3D view of the rhombohedral lattice for $$R3m$$ (shown on the right is its 2D projection onto the plane perpendicular to the $$[111]$$ direction). The screw axis corresponding to the circled symmetry element in the space group diagram (the top panel) is shown -- in 3D, it is an axis parallel to the $$[111]$$ direction of the rhombohedral unit cell, passing through the point $$\frac{2}{3}\vec{a} + \frac{1}{3}\vec{b}$$ (which is located in the $$ab$$ plane). Performing the $$3_1$$ screw operation around this axis, we will first bring bring the origin from $$\mathbf{0}$$ to $$\mathbf{0}'$$ as shown in the figure, followed by translating upwards along the $$[111]$$ direction by $$\frac{1}{3}$$ (again, with respect to the $$[111]$$ diagonal length). As the result, we will end up with the point at the tip of the lattice vector $$\vec{a}$$, as shown in the figure. This is the point -- although we do have $$3_1$$ screw axis in the space group, it is not an intrinsic to the structure. When I say 'intrinsic', I mean those symmetry elements that are necessary for describing the crystal structure symmetry. In this case, we do have the $$3_1$$ symmetry but it is not necessary -- all its operations can be replaced by simple lattice vector translations. Same thing for the glide plane, and therefore neither the glide nor the screw operations here are the space group generators, making $$R3m$$ a symmorphic space group.

Let's also put down some maths to double check what we said. In the rhombohedral lattice basis, the 3-fold rotation axis around the $$[111]$$ direction of the rhombohedral lattice can be performed with the following matrix,

$$
R =
\begin{bmatrix}
0 & 0 & 1 \\
1 & 0 & 0 \\
0 & 1 & 0
\end{bmatrix}
$$

so that the operation will be $$\mathbf{x}' = R\mathbf{x}$$, bringing $$\vec{a} \rightarrow \vec{b}$$, $$\vec{b} \rightarrow \vec{c}$$ and $$\vec{c} \rightarrow \vec{a}$$. If we shift the rotation axis to somewhere else (but still parallel to $$[111]$$), the operation then becomes,

$$
\mathbf{x}' = \mathbf{p} + R(\mathbf{x} - \mathbf{p}) = R\mathbf{x} + (\mathbb{I} - R)\mathbf{p}
$$

where $$\mathbf{p}$$ is the shifting vector lying in the $$ab$$ plane. For formula above is understandable -- we first shift the origin to $$\mathbf{p}$$ so that $$\mathbf{x}$$ in the original coordinate system becomes $$\mathbf{x} - \mathbf{p}$$ in the new coordinate system, then perform the rotation, and finally shift the origin back. Now, we can confirm how $$(0, 0, 0)$$ transforms,

$$
\mathbf{x}'(\mathbf{0}) = \begin{bmatrix}
0 & 0 & 1 \\
1 & 0 & 0 \\
0 & 1 & 0
\end{bmatrix}\begin{bmatrix}
0 \\
0 \\
0
\end{bmatrix} + \begin{bmatrix}
1 & 0 & -1 \\
-1 & 1 & 0 \\
0 & -1 & 1
\end{bmatrix}\begin{bmatrix}
\frac{2}{3} \\
\frac{1}{3} \\
0
\end{bmatrix} = \begin{bmatrix}
\frac{2}{3} \\
-\frac{1}{3} \\
-\frac{1}{3}
\end{bmatrix}
$$

Then we apply the translation part of the screw axis, corresponding to,

$$
\frac{1}{3}(\vec{a} + \vec{b} + \vec{c}) = \begin{bmatrix}
\frac{1}{3} \\
\frac{1}{3} \\
\frac{1}{3}
\end{bmatrix}
$$

giving us,

$$
\begin{bmatrix}
\frac{2}{3} \\
-\frac{1}{3} \\
-\frac{1}{3}
\end{bmatrix} + \begin{bmatrix}
\frac{1}{3} \\
\frac{1}{3} \\
\frac{1}{3}
\end{bmatrix} = \begin{bmatrix}
1 \\
0 \\
0
\end{bmatrix}
$$

That is exactly what we claimed, the point $$(0, 0, 0)$$ is shifted by the lattice vector $$\vec{a}$$ as the result of the screw operation.

In total, we have 73 symmorphic space groups and they are in one-to-one correspondence with the `arithmetic crystal classes` [5, 6]. To see what the arithmetic crystal class means, we need to know about the geometric crystal class [7], which is basically a way of classifying crystals according to their point groups. So naturally the geometric crystal class is in one-to-one correspondence with point groups. As for the arithmetic crystal class, it is also a way of classifying crystals according to symmetry groups and this time it is according to the space groups formed by combining point groups with only Bravais lattice translations, i.e., those symmorphic space groups.

### When will point group be the subgroup of space group?

In space groups, operations are given as $$\{R \vert \mathbf{t}_R\}$$, combining point operations with translations. So, generally, we don't always have the point group of a crystal structure as the subgroup of the space group. Only when the space group is symmorphic will we have the point group as the subgroup of the space group. First, for a space group $$G$$, we have all the pure translations (i.e., the Bravais lattice) forming a group $$T$$, as the subgroup of $$G$$. Then we can have all the left cosets corresponding to $$T$$ by multiplying all the point-translation combined operations on the left of $$T$$. To have the point group as the subgroup of $$G$$, we require $$\{R\vert \mathbf{0}\}$$ existing in all the cosets (corresponding to all $$R$$). Why? Because in that case, we can collect all the $$\{R \vert \mathbf{0}\}$$ for all the point operations $$R$$, which then natuarally gives the point group, as the subgroup of the space group. In those cases where the condition is not met, not all the point operations are existing independently (i.e., without combining with translations) and therefore those independent point operations do not even form a group, not to say being the subgroup of the space group. To meet the condition, either the translation $$\mathbf{t}_R$$ in $$\{R \vert \mathbf{t}_R\}$$ is not corresponding to screw or glide operations (so that $$\mathbf{t}_R$$ can always be made $$\mathbf{0}$$ by origin choice for all $$R$$, meaning that all the point operations $$R$$ can be made `pure`) or the screw or glide $$\mathbf{t}_R$$ is not intrinsic at all (so that $$\{R \vert \mathbf{t}_R\}$$ can always replaced by $$\{E \vert \mathbf{0}\}$$ -- see the $$R3m$$ example discussed above. $$E$$ means identity point operation, i.e., doing nothing).

Earlier when we put down the definition for the symmorphic space group, we say,

> it refers to those space groups in which all the generating symmetry operations, apart from the lattice translations, leave one common point fixed

The question is how this is relevant to the requirement above,

> $$\{R\vert \mathbf{0}\}$$ existing in all the cosets (corresponding to all $$R$$)

The top requirement says, there exists a point $$\mathbf{x}_0 \in \mathbb{R}^3$$ such that every generator $$\{R \vert \mathbf{t}_R\}$$ (other than pure translations) satisfies,

$$
\{R \vert \mathbf{t}_R\}\mathbf{x}_0 = \mathbf{x}_0
$$

Acting out the left side,

$$
R\mathbf{x}_0 + \mathbf{t}_R = \mathbf{x}_0 \Rightarrow \mathbf{t}_R = (E - R) \mathbf{x}_0
$$

That means for all $$\{R \vert \mathbf{t}_R\}$$, if we shift the origin to $$\mathbf{x}_0$$ (the fixed point, if existing), we have,

$$
\mathbf{t}_R \mapsto \mathbf{t}_R + (R - E)\mathbf{x}_0 = \mathbf{t}_R - (E - R)\mathbf{x}_0
$$

Since we have already worked out the top requirement yielding $$\{R \vert \mathbf{t}_R\}\mathbf{x}_0 = \mathbf{x}_0$$, shifting the origin to $$\mathbf{x}_0$$ yields,

$$
\mathbf{t}_R \mapsto \mathbf{0}
$$

proving that the two requirements are equivalent.

## Wyckoff Position

There is no need to emphasize the importance of Wyckoff position in describing crystal structures, and we know they are special positions, 'special' in the sense that some of the symmetry elements (including the identity operation) involved in the space group leave atoms on those positions fixed. When we see a Wyckoff position symbol like $$4f$$ for the space group $$P4mm$$, we know the multiplicity of the site is 4, meaning that atoms on any of positions in the set will generate 4 equivalent positions. Stepping back a bit, we can always ask, what is the actual definition of Wyckoff position, rigorously? Let's dive a bit deeper into it, where we will see some relevance to the discussion earlier in this post about the reference elements when talking about space group operations.

First, let's put down the rigorous definition for Wyckoff position. It is a set of positions, and the site symmetry group for positions in the set are all conjugate sugroup to each other. Oh no, a simple thing we keep referring to in our daily life dealing with crystal structures suddenly becomes a bit involved. Well, first, it is rigorous, definitely better than just vague descriptions. Second, learning about its rigorous definition is helpful in understanding what we fundamentally mean when we say points belonging to the same Wyckoff position are equivalent to each other. Let's start with the notion of `site symmetry group`. Literally, it means the symmetry group for a specific site, a group of symmetry elements that leave the site invariant. It is a subgroup of the space group of the structure.

Knowing what the site symmetry group means, we can be clear about what we mean by equivalence among all those points invovled in a Wyckoff position (remember the definition -- a Wyckoff position is a set of positions). Mathematically, this just means the site symmetry groups for all those points in the Wyckoff position are conjugate to each other. This may still be a bit abstract, so let's put down a specific example,

<p align='center'>
<a href="/assets/img/posts/P4mm_demo.png" target="_blank">
<img src="/assets/img/posts/P4mm_demo.png"
   style="border:none;"
   width="500"
   alt="P4mm_demo"
   title="P4mm_demo" />
</a>
</p>

Here is presented the space group diagram for $$P4mm$$ and the two points circled out with light blue color belong to the Wyckoff position $$2c$$ with the coordinate in the form of $$(1/2, 0, z)$$ and $$(0, 1/2, z)$$. This Wyckoff position therefore contains all points with the $$a$$ coordinate fixed at $$x = 1/2$$ (or $$0$$), $$b$$ coordinate fixed at $$y = 0$$ (or $$1/2$$), and $$c$$ coordinate $$z$$ being anything. Looking at the diagram, we can see that both of the two series of positions have the following set of symmetries, identity, a 2-fold rotation, two mirror reflections. Therefore, if we focus on the point operation part for each site symmetry group, the two sites basically have identical local point symmetry. Mathematically, we say both of the site symmetry groups are isomorphic to the same point group, namely $$mm2$$ in this case. Regarding the site symmetry group, we have,

$$
\mathrm{Stab}(0,1/2,z) = g\,\mathrm{Stab}(1/2,0,z)\,g^{-1}.
$$

{: .info}
> The site symmetry group contains space group operations and therefore do carry the translation part, given as $$\{R \vert \mathbf{t}\}$$. Therefore, they can only be isomorphic to subgroups of the point group that the space group belongs to, but the site symmetry groups themselves cannot be the subgroup of the point group.
>
> <br>
>
> Also, the site symmetry group does care about the location of the symmetry elements, as we will see below, which is fundamentally why the conjugacy (but not necessarily being identical) is required for the site symmetry group for defining the Wyckoff position.

where $$\mathrm{Stab}(0,1/2,z)$$ and $$\mathrm{Stab}(1/2,0,z)$$ means the site symmetry group for the site $$(0,1/2,z)$$ and $$(1/2, 0, z)$$, respectively. $$g$$ refers to an operation in the space group but not in either of the site symmetry groups -- in the diagram presented above, $$g$$ is the 4-fold rotation with respect to the $$c$$-axis (with $$\mathbf{0}$$ translation, i.e., $$\mathbf{t} = 0$$). Specifically, for the site $$(0,1/2,z)$$, the site symmetry group is,

$$
\{\,1,\;\; 2\!\left[0,\tfrac12\right],\;\; m_{x=0},\;\; m_{y=1/2}\,\}
$$

and for the site $$(1/2,0,z)$$, it is,

$$
\{\,1,\;\; 2\!\left[\tfrac12,0\right],\;\; m_{x=1/2},\;\; m_{y=0}\,\}
$$

These are two groups as the symmetry elements involved in each are not identical. But they are conjugate, as given in the formula earlier (which I will prove below). Visually, what this means can be told from the diagram above. With the $$g$$ operation, the site at $$(1/2,0,z)$$ is brought to $$(0,1/2,z)$$, the local 2-fold rotation axis is brought to the corresponding new location. Same thing happens for the two mirror planes as well with their orientations also changed (refer to the two differently colored mirror planes). However, even though the symmetry elements are either relocated or reoriented, the type of symmetry still does not change. Therefore, the site symmetry for the two sites should be considered as equivalent, and that is what the `conjugacy` guarantees.

Finally, let's mathematically walk through the equivalence among sites in a Wyckoff position and the conjugacy of the site symmetry groups. Let $$G$$ be the space group acting on points by $$g\cdot \mathbf{x}$$, with $$g \in G$$ and $$\mathbf{x} \in \mathbb{R}^3$$. For a point $$\mathbf{p}$$, we have its stabalizer given as,

$$
\text{Stab}(\mathbf{p}) = \{ h \in G : h\cdot \mathbf{p} = \mathbf{p}\}
$$

For $$g \in G$$ and $$g \notin \text{Stab}(\mathbf{p})$$, we have,

$$
\mathbf{q} = g\cdot \mathbf{p}
$$

i.e., the operation $$g$$ brings the point $$\mathbf{p}$$ to $$\mathbf{q}$$. Take any $$h \in \text{Stab}(\mathbf{p})$$ and conjugate it with $$g$$, we have,

$$
(ghg^{-1})\cdot\mathbf{q} = (ghg^{-1}) \cdot (g\cdot \mathbf{p}) = g\cdot h\cdot (g^{-1}g)\cdot \mathbf{p} = g\cdot h\cdot \mathbf{p} = g\cdot \mathbf{p} = \mathbf{q}
$$

So $$ghg^{-1}$$ fixes the new point $$\mathbf{q}$$, i.e., $$ghg^{-1} \subseteq \text{Stab}(\mathbf{q})$$ and therefore $$g\,\text{Stab}(\mathbf{p})\,g^{-1}\subseteq \mathrm{Stab}(\mathbf{q})$$. Reversely, we can take any $$k \in \text{Stab}(\mathbf{q})$$, and accordingly, we have,

$$
k\cdot \mathbf{q} = \mathbf{q} \Rightarrow d \cdot (g\cdot \mathbf{p}) = g\cdot\mathbf{p}
$$

Applying $$g^{-1}$$ to both sides gives,

$$
(g^{-1}kg)\cdot \mathbf{p} = \mathbf{p}
$$

This means,

$$
g^{-1}kg \in \text{Stab}(\mathbf{p}) \Rightarrow k \in g\text{Stab}(\mathbf{p})g^{-1} \Rightarrow \text{Stab}(\mathbf{q}) \subseteq g\text{Stab}(\mathbf{p})g^{-1}
$$

Combined with relation we just derived above $$g\,\text{Stab}(\mathbf{p})\,g^{-1}\subseteq \mathrm{Stab}(\mathbf{q})$$, we have,

$$
\text{Stab}(\mathbf{q}) = g\text{Stab}(\mathbf{p})g^{-1}
$$

This means, for the site $$\mathbf{p}$$, we have its site symmetry group as $$\text{Stab}(\mathbf{p})$$. If a space group operation $$g$$ not in the stabalizer brings $$\mathbf{p}$$ to $$\mathbf{q}$$, the site symmetry group of the new site $$\mathbf{q}$$ is given as $$g\text{Stab}(\mathbf{p})g^{-1}$$ which is conjugate to $$\text{Stab}(\mathbf{p})$$ (this is the definition of conjugate subgroups). Let's walk through the conjugacy formulation and we can see why the derived conjugacy here means equivalence among sites involved in a Wyckoff position. For the position $$\mathbf{q}$$ obtained after the operation of $$g$$, not only the position of the object is changed but also the symmetry elements (rotation axis, mirror planes, etc.) are reoriented. To link to the situation before the operation of $$g$$, let's do this. We first apply $$g^{-1}$$ on $$\mathbf{q}$$ to bring it back to $$\mathbf{p}$$. Then whatever symmetry operations in $$\text{Stab}(\mathbf{p})$$ will leave $$\text{p}$$ invariant. Then we apply $$g$$ again to bring it back to $$\mathbf{q}$$. Doing this, we find that no matter how the symmetry elements are changed by the operation of $$g$$, we can always follow the chain of actions as given here to stabalize $$\mathbf{q}$$ the same way as how $$\text{Stab}(\mathbf{p})$$ would stabalize $$\mathbf{p}$$ before the operation of $$g$$. Therefore, the operation of $$g$$ does not change anything fundamentally regarding the site symmetry -- that's exactly what we mean by equivalence among sites involved in a Wyckoff position. Since the chain of action we performed here is just $$g\text{Stab}(\mathbf{p})g^{-1}$$, exactly the conjugate of $$\text{Stab}(\mathbf{p})$$, we then understand why the Wyckoff position is defined by the conjugacy of site symmetry groups.

## Acknowledgement

Figures involved in the post use the diagram figures for space groups from Ref. [9]. Thank you Jeremy Karl Cockcroft and the team at Birkbeck College, University of London for this great resource!

## References

[1] [Space Group Diagram Symbols](http://img.chem.ucl.ac.uk/sgp/misc/symbols.htm).

[2] International Tables for Crystallography, Volume A, 5th edition.

[3] [R3m space group diagram](http://img.chem.ucl.ac.uk/sgp/large/160az1.htm).

[4] K. Momma and F. Izumi (2008): J. Appl. Crystallogr., 41, 653-658.

[5] [Symmorphic space groups](https://dictionary.iucr.org/Symmorphic_space_groups).

[6] [Arithmetic crystal class](https://dictionary.iucr.org/Arithmetic_crystal_class)

[7] [Geometric crystal class](https://dictionary.iucr.org/Geometric_crystal_class)

[8] [P4mm space group diagram](http://img.chem.ucl.ac.uk/sgp/large/099az1.htm)

[9] [Space Group Diagrams and Tables](http://img.chem.ucl.ac.uk/sgp/large/sgp.htm)