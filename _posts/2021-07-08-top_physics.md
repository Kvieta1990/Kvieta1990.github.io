---
layout: post
title: Notes on topological physics
subtitle:
tags: [solid state physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/top.png"
   style="border:none;"
   alt="top"
   title="top" />
<br />
Image reproduced from Ref. [5].
</p>

<p style='text-align: justify'>
In this blog, I will put down my learning notes as reading through the article on topological electrons by A. P. Ramirez and B. Skinner [1]. Basically, I will follow the story flow in Ref. [1] and therefore, first, I will note down the main flow that one can follow to arrive at the notion of topological electrons. Then I will cover the implications of topology and its connection to quantum Hall effect and quantum spin Hall effect. Finally, Weyl semimetal will be covered. It should be pointed that I will not reproduce all the nice discussions presented in Ref. [1]. Rather instead, I will just try to note down those key points or questions that I came across during reading Ref. [1].

First, the topological structure of an geometric object is defined by the Gauss-Bonnet integral,
</p>

$$
\frac{1}{2\pi}\int_S K dA = n
$$

<p style='text-align: justify'>
where $$K$$ refers to the curvature at points on surface $$A$$. The integral will give us a constant $$n$$ which corresponds to the genus (number of holes) of a certain geometric object in the following manner,
</p>

$$
n = 2(1 - g)
$$

<p style='text-align: justify'>
When defining topological electrons, we are trying to look for similar definition as for the Gauss-Bonnet integral presented above. We can summarize the discussion in Ref. [1] as the following flow, Berry connection $$\rightarrow$$ Berry curvature $$\rightarrow$$ Berry phase. Considering then the integral of Berry curvature on the boundary of a 2D Brillouin zone, we then arrive at the topological invariant - the Berry phase,
</p>

$$
\frac{1}{2\pi\hbar^2}\int_{BZ}\Omega d^2p = C
$$

<p style='text-align: justify'>
where the topological invariant $$C$$ in this case is called the Chern number. $$\Omega = \nabla \times \mathbf{X}$$ is defined as Berry curvature, where $$\mathbf{X}$$ is the Berry connection. The general definition for Berry connection and Berry phase can be found on Wikipedia [2] and won't be reproduced here. Though, it should be pointed out that the general variable $$R$$ in the formulation presented in Ref. [2] becomes $$p$$ (i.e. momentum of electron) in the specific context of discussing topological electrons. Accordingly, the Berry connection in the context of topological electrons has its physical meaning - the average position of electron as the function of momentum. To understand this, we can refer to Eqn. (5.34)-(5.39) in Ref. [3].
</p>

> In Ref. [1], it was briefly discussed that the `space of allowable momenta is called the Brillouin zone`. Here is some side notes for the discussion with this regard. According to de Broglie, the momentum is related to the wavelength of the matter wave. In the context of eletrons, the momentum of electrons `describes the process of hopping between beighboring crystal lattice sites` (quoted from Ref. [1]), or put in the language of wave, the `wave` here specifically refers to the wave of electrons which are surrounding the atoms and thus possessing the same underlying crystal lattice symmetry. In wave theory, we know that the wavelength actually refers to the periodicity in space and therefore the electron wave periodicty in space (i.e., the wavelength) cannot be smaller than the distance between neighboring unit cells of the crystal. Considering the de Broglie relation $$\lambda = 2\pi\hbar/p$$, there is then an upper limit of the values that the momentum can take. From the perspective of Fourier transform, we know that the discreteness in one space yields periodicity in the coupled space. Therefore, we know that in the momentum space, beyond the first Brillouin zone, we will just have duplicates of the first BZ, for stuff of interest, e.g., the energy band.

> In Ref. [1], it was mentioned that the definition of tbe Berry connection $$\mathbf{X}$$ is `not gauge-invariant`, and it is fundamentally due to that Bloch function involved in the Berry connection formulation is only defined up to a multiplicative phase factor $$e^{i\theta_{\vec{p}}}$$. According to the Bloch theorem, the Bloch function in Ref. [1] refers to the real part of the electron wavefunction and since it is real, we can always write it as the modulus of a complex number which can have any arbitrary phase.

<p style='text-align: justify'>
According to the Chern number formulation discussed above, it is fundamentally related to the phase factor associated with the Berry connection integration along a path $$P$$ in momentum space. When the integration happens to be along the BZ boundary, we know that integrating along the clockwise and counterclockwise direction should yield physically equivalent states. However, meanwhile, the integration along the clockwise and counterclockwise direction should be giving negative phaes factor. Therefore, we know that the phase factor absolute value should be either 0 or an integer multiple of $$2\pi$$ (physically equivalent to the state with phase factor of 0). Further, we know that to guarantee we have non-zero Chern number (i.e., non-zero path integration of the Berry connection), we should have $$+\vec{p}$$ and $$-\vec{p}$$ being inequivalent to each other, since otherwise, for any $$\vec{p}$$ state, we would have a corresponding $$-\vec{p}$$ state with identical Berry connection, which would make the integration always equal to 0. As a result, a non-zero Chern number would mean the Berry connection forms a "winding" or "self-rotation" pattern, as illustrated in Ref. [1]. As such, it infers that a system with non-zero Chern number implies intrinsic magnetic field. Fundamentally, the Berry connection here in the context means the average position of electrons as the function of momentum. The winding pattern of the Berry connection then implies the relation between the electrons position and momentum which implies an angular momentum. To understand this, refer to Fig. 3 in Ref. [1] where we see the position vector as the function of momentum and the winding of the position vector in momentum space reproduces the behavior of particles rotation in space which then yields angular momentum. Therefore, we can see the analogous relation between Berry curvature and magnetic field - they both break symmetry (Berry curvature breaks the inversion symmetry when we have non-zero Chern number); they both bring in orbital angular momentum. Another implication for magnetic field from Berry curvature is that non-zero curl of $$\mathbf{X}$$ will introduce movement in the transverse direction as, e.g., electrons are accelerated in electric field - refer to Wikipage for curl [4] (see the two examples given there).
</p>

<p style='text-align: justify'>
The implication for magnetic field as originated from topological invariant brings us to the connection with quantum Hall effect. When certain 2D materials are immersed in strong magnetic field and low temperature environment, they show quantized Hall conductance behavior. The physics picture for such a behavior is, electrons are circulating interior the bulk and rolling along the surface. Such circulating and rolling orbitals are quantized according to Landau levels which gives the quantization of cyclotron orbitals of charged particles in a uniform magnetic field. The quantization of Hall conductance can be explained purely by Landau's quantization theory for magnetic field [6], and also it can be deduced based on topological consideration [7, 8]. The topological paradigm also explains the robustness of quantum Hall effect, i.e. the quantized Hall conductance is protected by topological invariant [7, 8]. According to Ref. [7, 8], the topological invariant is defined in parameter space, which is different from the version as presented in current summary based on Ref. [1] where the topological invariant is given in momentum space. Going back to the discussion about topological invariant as defined in momentum space, the implication for magnetic field gives rise to edge states much like those in the quantum Hall effect. There are two things to note down at this point, 1) as pointed out just above, the topological protection mechanism of quantum Hall effect systems and the topological system defined in momentum space is different. 2) though the topological invariant (again, defined in momentum space) implies magnetic field (which seems to suggest that the edge states can stably exists without external magnetic field), the breaking of time reversal symmetry in such topological systems makes it impossible to hold stable edge currents as what we can have in quantum Hall effect systems. However, in certain systems with strong spin-orbital coupling [1, 9], electrons with spin up and spin down can break down the time reversal symmetry separately to give opposite non-zero Chern number. In another word, spin up electrons flow, e.g., to the right on surface whereas spin down electrons to the left. In such systems. the overall time-reversal symmetry is kept and on surface, we won't have net current of electrons but we do have net flux of spins - either up or down. Such an effect is called quantum spin Hall effect [1, 10, 11].

<br />

The topological notion discussed here can apply to both 2D and 3D systems -- in 3D systems, it can be used to understand Weyl semimetal. What is semimetal? It shows continuous band structure as what we have for metal but the connection part (somewhat like the neck) is quite narrow -- that is typical band structure of semimetals. Specifically for Weyl semimetals, two bands touch each other in momentum space at Weyl points where we have the abrupt change in the orbital character of the wavefunction -- the momentum of electrons does not change smoothly crossing Weyl points. The picture shown at the top of current blog gives a typical picture of the corresponding band structure in 3D space. Usually, electronic band cannot coincide in energy since anytime two bands meet each other, there will be hybridization happening which will splits the degeneracy and therefore the two bands are bounced away from each other. In early age, the touching of two bands was explained to be accidental in which case hybridization is avoided. However, such an accidental touching could be easily destroyed by perturbation and therefore does not stand. In the context of topological paradigm, we see that such a type of band structure for Weyl semimetal systems is protected by the topological invariant. The value of topological invariant is relevant to the inclusion of Weyl points in the integration area when evaluating the Chern number integral. Weyl points are topological analogues to electric charges -- they are monopoles of Berry flux [1]. Also the Weyl points always come in pairs with opposite topological charges and the two Weyl points in the pair are with different chirality. Therefore, in Weyl semimetal systems, half of electrons are Weyl fermions with one chirality (see Ref. [12] for definition of electron chirality -- basically, it means whether electron spin is in the same or opposite direction flowing direction), the other half being Weyl fermions (electrons without explicit chirality are Dirac [fermions) with opposite chirality.
</p>

---

- See also the highlights [here](https://www.notion.so/iris2020/How-Can-Electrons-Be-Topological-c8601f76d275408ba7a1a05f38be7821?pvs=4) in reading the blog post by A. P. Ramirez and B. Skinner -- it is an excerpt from a draft of Ref. [1].

- Also refer to the blog post [here](https://www.notion.so/iris2020/What-s-a-Topological-Insulator-ScienceBlogs-a53ca9f8517e492381eb4edf3e2414b9?pvs=4) for a basic introduction to topological insulator.

---

<br />

<b>References</b>

[1] A. P. Ramirez and B. Skinner.  Physics Today 73, 9, 30 (2020) -- see also the saved page [here](https://www.notion.so/iris2020/How-can-electrons-be-topological-Gravity-and-Levity-45c7f3e1064142ec9cbf75a975bf5dc0?pvs=4).

[2] [https://en.wikipedia.org/wiki/Berry_connection_and_curvature](https://en.wikipedia.org/wiki/Berry_connection_and_curvature)

[3] [https://1drv.ms/b/s!AlZpbyasn9jtoqUZCYW069akHDG2rQ?e=gCynHc](https://1drv.ms/b/s!AlZpbyasn9jtoqUZCYW069akHDG2rQ?e=gCynHc)

[4] [https://en.wikipedia.org/wiki/Curl_(mathematics)](https://en.wikipedia.org/wiki/Curl_(mathematics))

[5] [https://www.bnl.gov/newsroom/news.php?a=117401](https://www.bnl.gov/newsroom/news.php?a=117401)

[6] [https://1drv.ms/b/s!AlZpbyasn9jtoqUlsQ_9KB7CG2g-og?e=kSOfZu](https://1drv.ms/b/s!AlZpbyasn9jtoqUlsQ_9KB7CG2g-og?e=kSOfZu)

[7] [https://1drv.ms/b/s!AlZpbyasn9jtoqUkYCO6KG6h8JZFFA?e=gdOCXB](https://1drv.ms/b/s!AlZpbyasn9jtoqUkYCO6KG6h8JZFFA?e=gdOCXB)

[8] [https://1drv.ms/b/s!AlZpbyasn9jtoqUj2N8fMFbK-0_cNQ?e=oe58FK](https://1drv.ms/b/s!AlZpbyasn9jtoqUj2N8fMFbK-0_cNQ?e=oe58FK)

[9] [https://jqi.umd.edu/glossary/quantum-hall-effect-and-topological-insulators](https://jqi.umd.edu/glossary/quantum-hall-effect-and-topological-insulators)

[10] [https://scienceblogs.com/principles/2010/07/20/whats-a-topological-insulator](https://scienceblogs.com/principles/2010/07/20/whats-a-topological-insulator)

[11] [https://www.evernote.com/shard/s266/sh/2a935515-dfaa-4312-bb66-1d5dd5086339/dcdb9fe33e21b49f7c10168fd8980861](https://www.evernote.com/shard/s266/sh/2a935515-dfaa-4312-bb66-1d5dd5086339/dcdb9fe33e21b49f7c10168fd8980861)

[12] [https://en.wikipedia.org/wiki/Chirality_(physics)](https://en.wikipedia.org/wiki/Chirality_(physics))