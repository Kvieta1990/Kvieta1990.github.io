---
layout: post
title: Enantiomorphic Space Groups
subtitle:
tags: [physics, crystallography]
author: Yuanpeng Zhang
comments: true
use_math: true
---

{::nomarkdown}
{% include mirror-demo.html %}
{:/nomarkdown}

## Enantiomorph

An enantiomorph is a mirrored structure of an original structure -- it can be obtained via reflection as shown in the animation above. After the reflection, all the basic metrics (distance and angle) are kept -- the two structures are basically the same thing except that they carry different chirality. Just like our two hands, we can let them facing each other and they will match. But if we let them both face up and try to lay them on top of each other, we will find that we could never do that if we are not allowed to flip either of them. In this case, one of the hands is the enantiomorph of the other -- it is a reflection of the original image but after the reflection, we can never superimpose them identically with only rotation and translation.

For a structure to have an enantiomorph, the structure itself should not carry any improper symmetry element. To build this up mathematically, let's start by laying out the group recipes. First, the crystal structure $$S$$ is an object containing a collection of 3D points, i.e., $$S \subset \mathbb{R}^3$$. The space group of a crystal structure can be given as,

$$
G = \{ g \in E(3) : g(S) = S \}
$$

It says $$G$$ is a collection of operations $$g$$ such that $$g(S) = S$$. Mathematically, $$g \in E(3)$$ means $$g$$ belongs to the $$E(3)$$ group -- the group of Euclidean isometries of $$\mathbb{R}^3$$.

{: .info}
> An isometry is, fundamentally, a *mapping* and it maps from one metric space to another while preserving the distance. A metric space is basically a set of points with distance among points defined. Following such terminologies, we can see that $$g$$ mentioned above is a symmetry operation and it indeed maps one metric space to another -- specifically, from a $$\mathbb{R}^3$$ Euclidean space to itself. Here, by 'itself', I mean the Euclidean space itself -- 'it' refers to the space. More specifically, let's say the operation $$g$$ maps an object (say, a crystal structure) in the $$\mathbb{R}^3$$ Euclidean space to another object. The resulted object is still an object in the $$\mathbb{R}^3$$ Euclidean space. The resulted object does not have to superimpose the original object but the the space they belong to is the same, i.e., the $$\mathbb{R}^3$$ Euclidean space. So, indeed $$g$$ is an isometry and all such isometries put together form the $$E(3)$$ group (i.e., the isometries are not simply put together but rather they follow the rules of a group where the group multiplication is just applying one mapping after another).

$$g$$ in general can be written as $$g(\mathbf{x}) = A\mathbf{x} + \mathbf{t}$$, where $$\mathbf{x}, \mathbf{t} \in \mathbb{R}$$, and $$A \in O(3)$$.

{: .info}
> **$$O(3)$$** is a subgroup of $$E(3)$$ -- it contains all operations that keep the distance in the $$\mathbb{R}^3$$ Euclidean space. Meanwhile, all operations in the $$O(3)$$ group also keeps the orthogonality of vectors in the $$\mathbb{R}^3$$ Euclidean space. In fact, regarding the preservation of distance and orthogonality, it also applies for the $$E(3)$$ group. The difference is on the translation bit -- the $$O(3)$$ group does not care about the translation.
>
> For the discussion about operations being proper or improper, the translation part does not matter at all. Therefore in the following discussion, we will focus on the $$O(3)$$ group.

Since $$A \in O(3)$$, we have $$\vert \text{det}\ A \vert = 1 \Rightarrow \text{det}\ A = \pm 1$$. Picking the '$$+$$' sign, we have the *proper* operation and for the '$$-$$' sign, we have *improper* operations. Here, by 'proper' and 'improper', we mean whethr or not the charility is kept. For translation or normal rotations (which does not involve any reflections, in contrast to a improper rotation which does), the charility of the transformed object is kept. For example, no matter how we rotate or translate our right hand, right hand is always right hand -- see the animation presented below (generated with Claude).

{::nomarkdown}
{% include chirality-embed.html %}
{:/nomarkdown}

Mathematically, we have the following group homomorphism $$\delta$$,

$$
\delta: O(3) \rightarrow \{\pm 1\}, \quad \delta(g) = \text{det}\ A_g
$$

{: .info}
> A group homomorphism is a mapping, from one group to another. Say we have group $$G$$ and $$H$$, a group homomorphism $$h$$ from $$G$$ to $$H$$ should satisfy that for all $$u$$ and $$v$$ in $$G$$, it holds that $$h(u * v) = h(u) * h(v)$$. Back to the context above -- we have a lot of operations in the $$O(3)$$ group and we have a group homomorphism $$h$$ that maps those operations to operations in the group $$\{+1, -1\}$$. With such a group homomorphism, we can straightforwardly know whether a given operation $$\sigma_1 \cdot \sigma_2$$ is a proper or improper operation given $$\delta(\sigma_1)$$ and $$\delta(\sigma_2)$$, since,
>
> $$\delta(\sigma_1 \cdot \sigma_2) = \delta(\sigma_1) \cdot \delta(\sigma_2)$$

Now, it is time to come back to building up the enantiomorph, and first we want to pick a mirror operation. The question is, does it matter what mirror operation (i.e., where we put the mirror plane) we pick to define 'the' enantiomorph? The answer is No, it does not matter.

{: .info}
> The mirror operation here is an improper isometry -- in the notes above, we have seen 'improper' and also 'isometry' so the jargon here should no longer sound unfamiliar.

For improper isometries $$\sigma_1, \sigma_2 \in O(3) \backslash SO(3)$$ (here let's forget about the translation part temporarily since it does not impact the 'properness'), we should have $$\sigma_2\sigma_1^{-1}$$ being a proper isometry. Why?

{: .info}
> $$SO(3)$$ refers to the subgroup of $$O(3)$$ with $$\text{det} A = 1,\ \forall A \in SO(3)$$. $$O(3) \backslash SO(3)$$ means the collection of all operations with $$\text{det}\ A = -1$$. It should be noted that $$O(3) \backslash SO(3)$$ is called a *collection* but *NOT* a group since indeed it is not a group -- obviously, the identity operation which maps to $$\text{det}\ \mathbb{I} = 1$$ is not a member of the collection.

To answer why, we can write down,

$$
\delta(\sigma_1\sigma_1^{-1}) = \delta(\mathbb{I}) = 1
$$

where $$\sigma_1^{-1}$$ refers to the inverted operation with respect to $$\sigma_1$$, i.e., if $$\sigma_1$$ is the mirror reflection, $$\sigma_1^{-1}$$ just reflects backwards. Accordingly,

$$
\delta(\sigma_1\sigma_1^{-1}) = \delta(\sigma_1)\delta(\sigma^{-1}) = 1 \Rightarrow \delta(\sigma_1^{-1}) = -1
$$

which is kind of a straightforward conclusion -- the mirror reflection is an improper isometry and therefore the inverted reflection is for sure an improper isometry. Further, we have,

$$
(\sigma_2\sigma_1^{-1})[\sigma_1(S)] = \sigma_2[\sigma_1^{-1}\sigma_1(S)] = \sigma_2[\mathbb{I}(S)] = \sigma_2(S)
$$

This trivial formulation tells us something non-trivial -- there is not a special enantiomorph. For any enantiomorph obtained via the operation $$\sigma_1$$, we can always obtain another enantiomorph obtained from another operation $$\sigma_2$$, via a proper isometry ($$\sigma_2\sigma_1^{-1}$$, which, as shown above, is a proper isometry), i.e., only rotation and translation.

Finally, we can come back to the statement 'For a structure to have an enantiomorph, the structure itself should not carry any improper symmetry element'. In another word, an enantiomorph of a structure exists only if the structure is chiral. This further means that the space group of the structure should not contain any improper isometries/operations. To prove, let's first operate the structure $$S$$ with a mirror reflection $$\sigma$$ (which we know is an improper isometry$$^\dagger$$),

$$
S^* = \sigma(S)
$$

{: .info}
> $$^{\dagger}$$Taking a mirror reflection in 2D plane as the example, we can put the mirror plane (in 2D, it is a line) at the $$y$$ axis, and accordingly the transformation goes like,
> 
> $$
> \begin{pmatrix}
> -x \\
> y
> \end{pmatrix}
> =
> \begin{pmatrix}
> -1 & 0 \\
> 0 & 1
> \end{pmatrix}
> \begin{pmatrix}
> x \\
> y
> \end{pmatrix}
> $$
>
> Apparently the determinant of the transformation matrix is $$-1$$, inferring this is indeed an improper operation.

If the space group of the crystal structure $$S$$ contains an improper operation $$\sigma_0$$, then we can always build up a proper operation $$h = \sigma_0\sigma^{-1}$$ (see the discussion above for why this composite operation is a proper one) such that,

$$
h(S^*) = \sigma_0\sigma^{-1}(S^*) = \sigma_0\sigma^{-1}\sigma(S) = \sigma_0\mathbb{I}(S) = \sigma_0(S) = S
$$

This means that for a mirror image of the structure $$S$$ in the context, we can always transform it back to the original structure $$S$$ via only rotation and translation, i.e., proper operations. In another word, the enantiomorph of the structure $$S$$ does not exist.

Two examples to end this bit. First,

<p align='center'>
<img src="/assets/img/posts/enantiomorph_existing.png"
   style="border:none;"
   width="1000"
   alt="enantiomorph_existing"
   title="enantiomorph_existing" />
</p>

In $$\mathbb{R}^3$$, the inversion operation is an improper operation, since,

$$
\text{det}\ \begin{bmatrix}
-1 & 0 & 0\\
0 & -1 & 0\\
0 & 0 & -1
\end{bmatrix} = -1
$$

Therefore, in $$\mathbb{R}^3$$, if the space group of a crystal structure contains the inversion symmetry operation, the structure does not have its enantiomorph. However, in $$\mathbb{R}^2$$ as presented above, even though the structure is inversion symmetric, the enantiomorph still exists -- for the structure presented above, we can mirror it and will find that no matter how we rotate and translate the mirrored image, we can never superimpose the mirrored structure to the original structure. Why? Because in $$\mathbb{R}^2$$, the inversion operation is equivalent to a simple rotation -- here a 2-fold rotation around the axis punching through the inversion center will do the same transformation as with the inversion operation. Mathematically,

$$
\text{det} \begin{bmatrix}
-1 & 0 \\
0 & -1
\end{bmatrix} = 1
$$

and therefore the inversion operation in $$\mathbb{R}^2$$ is a proper operation.

The second example is one that does not have its enantiomorph since its corresponding point group does contain an improper mirror operation (we already showed above that the mirror operation in $$\mathbb{R}^2$$ is improper),

<p align='center'>
<img src="/assets/img/posts/enantiomorph_not_existing.png"
   style="border:none;"
   width="1000"
   alt="enantiomorph_not_existing"
   title="enantiomorph_not_existing" />
</p>

where we see that after the mirror reflection, we can rotate the reflected structure around its center, followed by translation, to superimpose onto the original structure, identically. That means, the enantiomorph does not exist in this case.

## Enantiomorphic space groups

Knowing what enantiomorph is, it is straightforward to understand what enantiomorphic space groups mean. In total, we have 230 types of space groups. Crystal structures follow either of the 230 types of space groups. Say we pick a crystal structure which follows the space group of type $$G$$ ($$G$$ is just a random symbol I pick, e.g., $$G = P4_1$$), and we then mirror the crystal structure to have its enantiomorph, if existing. We know that the two structures are basically the same except their chirality being different, again, just like our left and right hands. Since they are indeed different structures and accordingly their associated space group will be different (e.g., with the example $$G = P4_1$$ for the original structure, the type of the space group followed by the enantiomorph is $$P4_3$$). If we don't care about the chirality, the two types of space groups and the crystal structures on which they apply are equivalent, and such types of space groups (e.g., $$P4_1$$ and $$P4_3$$) are the enantiomorphic pairs. Regarding such equivalence, in total we have 219 distinctive types of space groups [1], which are referred to as the affine space groups. I will talk about this in future posts.

## References

[1] [Enantiomorphic space groups](https://aflow.org/p/enantiomorph_info.html)