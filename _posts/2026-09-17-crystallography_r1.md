---
layout: post
title: Some (Random) Notes On crystallography
subtitle:
tags: [maths, crystallography, physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

This post is not meant to be a systematic note about a specific topic. It's a collection of learning notes on a few basic topics on crystallography and may come in a sort of random order, which may seem a bit messy at a first (second, or maybe even third) look. However, topics covered in the post are indeed relevant to each other since they were all emerging as I was trying to clarify my understanding for those relevant topics. I don't remember which one of them did I start from -- anyhow, as I dived into a specific topic, relevant new topics keep emerging and I was trying my best to scribble down whatever in my mind at that moment. So, the post here roughly follows my scribbles on the draft paper.

## Crystal System

In the context of crystallography, the *crystal system* is an often mentioned notion. We all know that there are 7 of them, namely, triclinic, monoclinic, orthorhombic, tetragonal, trigonal, hexagonal and cubic. However, think about it -- what do they really mean? I mean, what is the reference that we are referring to when dividing crystals into those 7 *crystal systems*? Is it based on the lattice? No, since we have 7 lattice systems -- some of the names overlap with those of the crystal systems and some not. So, apparently, the crystal systems classification is not based on the lattice. Then, what is the reference? BTW, I mentioned the *lattice system*, but what is a lattice system and based on what do we classify lattices into those systems? Also, what...is a *lattice*? This is what I wrote at the very top -- as we think through these stuff, all sorts of questions just pop up and quite often we would find that for a lot of topics that we think we are already pretty familiar with, it may still be a bit difficult to answer the easy question 'what they are and why'.

So, `what is a crystal system`? Mathematically, `point groups` are classified into different crystal systems according to the shared (and distinctive to the class) symmetry element(s). For example, all the point groups belonging to the trigonal crystal class have a single 3-fold rotation axis. Saying this, it should become clear that the crystal system classification refers to the crystal structure (well, for sure). The reason to emphasize this is, by comparison, the classfication of lattice systems is refers to the lattice. Sounds like a crap, right? Well, no, because there comes a very important point about the `lattice` and the `crystal structure`. I will come back to this.

A detailed table for the member of point groups belonging to each crystal system can be found in Ref. [1].

## Crystal Family

The classification according to the `crystal family` follows a very similar rationale like the `crystal system` and in fact they almost overlap, except that the crystal systems `trigonal` and `hexagonal` are put together to give the `hexagonal` family. From the perspective of shared symmetry elements, such a merging is understandable -- point groups in the hexagonal crystal system share a 6-fold rotation axis and we know that two 6-fold rotation applied one after another yields a 3-fold rotation. So, no wonder why `trigonal` and `hexagonal` are merged into the same family. But why is the family NOT called `trigonal`? According to the Wikipedia page [2],

<br>

> Crystal systems that have space groups assigned to a common lattice system are combined into a crystal family.

The common lattice system here is `hexagonal` and thus named for the crystal family.

**N.B.** It should be noted that 'have' as in the quoted statement above does *NOT* mean 'all' -- as we can see in the table on Wikipedia [3], there are (meaning, 'have', as in the quoted statement) space groups that belong to the trigonal crystal system having the hexagonal lattice system but not all of them, since some of the space groups in the trigonal crystal system have the rhrombohedral lattice system.

## Lattice System

It has been mentioned a few times so it is time to stop by it. First, we need to clarify what a `lattice` is. So, lattice is a collection of *abstract* points where `the environment surrounding each of the abstract points is identical`. Uhm...the definition itself is a bit...abstract. Let's be specific and I will take the very classical and representative example (which is probably every crystallography textbook would take) -- a 2D honeycomb.

<p align='center'>
<img src="/assets/img/posts/honeycomb_lattice_1.png"
   style="border:none;"
   width="1000"
   alt="honeycomb_lattice_1"
   title="honeycomb_lattice_1" />
</p>

Here we show the honeycomb structure in 2D where I labeled the two distinctive local environment -- standing on the red solid circle and the open cyan circle, looking to the left of the screen, we will see different environment. So, the intersection corners of the honeycomb structure themselves cannot be abstracted directly as the lattice. Instead, the figure on the right gives the actual lattice -- the red solid circles can be abstracted as lattice points to give the lattice as indicated by the dashed framework. Now if we stand on those red circles and look around, the environment looks identical. So the lattice points are just those red solid circles? Uh...Yes and No. As mentioned already, lattice points are really abstract and not attached to any specific objects. It is more like a way of describing the periodicity of the underlying structure. In abstract algebra, it is actually an object in the `affine space` (see the previous note [here](../2026-09-15-abstract_algebra)). This is stepping too far off the topic here. Let's put down an illustrative diagram to show what it means,

<p align='center'>
<img src="/assets/img/posts/honeycomb_lattice_2.png"
   style="border:none;"
   width="1000"
   alt="honeycomb_lattice_2"
   title="honeycomb_lattice_2" />
</p>

On the left, the lattice points stay on top of the red solid circles whereas on the right hand side, we shift the whole lattice. The lattice is still the lattice, totally independent of where we put it -- the only thing that changes is the position of those circles (the `decoration` of the lattice) but that does not matter at all regarding the description of the periodicity, with the lattice.

Clarifying what we mean by `lattice`, we can talk about the `lattice system`,

- It is a set of lattices

- Lattices belonging to the same set (the lattice system) shares something

- The thing they share is the lattice point group, called the holohedry.

Two things, I guess, that need some immediate explanation -- `lattice point group` and `holohedry`. Earlier when we were talking about the crystal system, I said that the crystal system classify crystals with respect to the crystal structure according to their point groups. Given the clarification of what a lattice is -- abstract points without concrete objects decoration -- we can say that the crystal point group refers to the system of the crystal structure that has the underlying lattice decorated with concrete objects. Then it should become apparent what the lattice point group means -- it refers to the point group of the lattice points without any decorations. Again, let's look at an example,

<p align='center'>
<img src="/assets/img/posts/honeycomb_lattice_3.png"
   style="border:none;"
   width="1000"
   alt="honeycomb_lattice_3"
   title="honeycomb_lattice_3" />
</p>

In the left figure, we have the lattice decorated with concrete objects, making the original honeycomb. In this case, a typical symmetry element is a 3-fold rotation axis as labeled out in the figure. On the right, though, I removed all the decorations, leaving only the lattice, where we can see the symmetry is changed -- the same rotation axis now becomes 6-fold (the red dashed lines are added to guide our eyes only). So, the left decorated lattice has its point group which is the one used for crystal system classification while the right undecorated lattice also has its point group which is the one used for lattice system classification.

Based on the notes above, if the point group of a crystal is identical to that of the lattice (removing all the decorations), the point group is called holohedral, and the corresponding geometric crystal class [4] (one-to-one mapping to the point group) is called a holohedry [5].

## The Hexagonal Family

...to continue

## References

[1] [Crystal_system#Crystal_classes](https://en.wikipedia.org/wiki/Crystal_system#Crystal_classes)

[2] [Crystal system](https://en.wikipedia.org/wiki/Crystal_system#Crystal_classes)

[3] [Crystal systems table](https://en.wikipedia.org/wiki/Crystal_system#In_3_dimensions)

[4] [Holohedry](https://dictionary.iucr.org/Holohedry)

[5] [Geometric crystal class](https://dictionary.iucr.org/Geometric_crystal_class)