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
When defining topological electrons, we are trying to look for similar definition as for the Gauss-Bonnet integral presented above. We can summarize the discussion in Ref. [1] as the following flow, Berry connection $$\rightarrow$$ Berry curvature $$\rightarrow$$ Berry phase. Considering then the integral of Berry curvature on the boundary of a 2D Brillouin zone, we then arrive the topological invariant - the Berry phase,
</p>

$$
\frac{1}{2\pi\hbar^2}\int_{BZ}\Omega d^2p = C
$$

<p style='text-align: justify'>
where the topological invariant $$C$$ in this case is called the Chern number. $$\Omega = \nabla \times \mathbf{X}$$ is defined as Berry curvature, where $$\mathbf{X}$$ is the Berry connection. The general definition for Berry connection and Berry phase can be found on Wikipedia [2] and won't be reproduced here. Though, it should be pointed out that the general variable $$R$$ in the formulation presented in Ref. [2] becomes $$p$$ (i.e. momentum of electron) in the specific context of discussing topological electrons. Accordingly, the Berry connection in the context of topological electrons has its physical meaning - the average position of electron as the function of momentum. To understand this, we can refer to Eqn. (5.34)-(5.39) in Ref. [3].

Next, I will talk about the implication of topological invariant for magnetic field. First, to have non-zero Chern number, we will have a self-rotation in momentum space, as presented in Fig. 3 in Ref. [1]. Moreover, from the discussion above, we know that the mean value of atomic center position in unit cell is a function of momentum $$\mathbf{p}$$. This indicates that the changing in momenta (i.e., rotation - attention to the difference between rotation in momentum and real space) accompanies the changing of position and therefore indicates that the angular momentum is associated with the local Berry curvature (which infers the rotation of momenta). Therefore, we can see the analogous relation between Berry curvature and magnetic field - they both break symmetry (Berry curvature breaks the inversion symmetry when we have non-zero Chern number); they both bring in orbital angular momentum. Another implication for magnetic field from Berry curvature is that non-zero curl of $$\mathbf{X}$$ will introduce movement in the transverse direction as, e.g., electrons are accelerated in electric field - refer to Wikipage for curl [4] (see the two examples given there).
</p>

<blockquote cite="">
If inversion symmetry is not broken, the path integral along the boundary of
Brillouin zone will always Chern number of 0 -- for any $$\mathbf{p}$$,
we have a corresponding $$-\mathbf{p}$$ with identical Berry connection,
which will make the integration always 0.
</blockquote>

<p style='text-align: justify'>
The implication for magnetic field as originated from topological invariant brings us to the connection with quantum Hall effect. When certain 2D materials are immersed in strong magnetic field and low temperature environment, they show quantized Hall conductance behavior. The physics picture for such a behavior is, electrons are circulating interior the bulk and rolling along the surface. Such circulating and rolling orbitals are quantized according to Landau levels which gives the quantization of cyclotron orbitals of charged particles in a uniform magnetic field. The quantization of Hall conductance can be explained purely by Landau's quantization theory for magnetic field [6], and also it can be deduced based on topological consideration [7, 8]. The topological paradigm also explains the robustness of quantum Hall effect, i.e. the quantized Hall conductance is protected by topological invariant [7, 8]. According to Ref. [7, 8], the topological invariant is defined in parameter space, which is different from the version as presented in current summary based on Ref. [1] where the topological invariant is given in momentum space. Going back to the discussion about topological invariant as defined in momentum space, the implication for magnetic field gives rise to edge states much like those in the quantum Hall effect. There are two things to note down at this point, 1) as pointed out just above, the topological protection mechanism of quantum Hall effect systems and the topological system defined in momentum space is different. 2) though the topological invariant (again, defined in momentum space) implies magnetic field (which seems to suggest that the edge states can stably exists without external magnetic field), the breaking of time reversal symmetry in such topological systems makes it impossible to hold stable edge currents as what we can have in quantum Hall effect systems. However, in certain systems with strong spin-orbital coupling [1, 9], electrons with spin up and spin down can break down the time reversal symmetry separately to give opposite non-zero Chern number. In another word, spin up electrons flow, e.g., to the right on surface whereas spin down electrons to the left. In such systems. the overall time-reversal symmetry is kept and on surface, we won't have net current of electrons but we do have net flux of spins - either up or down. Such an effect is called quantum spin Hall effect [1, 10, 11].

<br />

The topological notion discussed here can apply to both 2D and 3D systems -- in 3D systems, it can be used to understand Weyl semimetal. What is semimetal? It shows continuous band structure as what we have for metal but the connection part (somewhat like the neck) is quite narrow -- that is typical band structure of semimetals. Specifically for Weyl semimetals, two bands touch each other in momentum space at Weyl points where we have the abrupt change in the orbital character of the wavefunction -- the momentum of electrons does not change smoothly crossing Weyl points. The picture shown at the top of current blog gives a typical picture of the corresponding band structure in 3D space. Usually, electronic band cannot coincide in energy since anytime two bands meet each other, there will be hybridization happening which will splits the degeneracy and therefore the two bands are bounced away from each other. In early age, the touching of two bands was explained to be accidental in which case hybridization is avoided. However, such an accidental touching could be easily destroyed by perturbation and therefore does not stand. In the context of topological paradigm, we see that such a type of band structure for Weyl semimetal systems is protected by the topological invariant. The value of topological invariant is relevant to the inclusion of Weyl points in the integration area when evaluating the Chern number integral. Weyl points are topological analogues to electric charges -- they are monopoles of Berry flux [1]. Also the Weyl points always come in pairs with opposite topological charges and the two Weyl points in the pair are with different chirality. Therefore, in Weyl semimetal systems, half of electrons are Weyl fermions with one chirality (see Ref. [12] for definition of electron chirality -- basically, it means whether electron spin is in the same or opposite direction flowing direction), the other half being Weyl fermions (electrons without explicit chirality are Dirac [fermions) with opposite chirality.
</p>

<br />

<b>References</b>

[1] A. P. Ramirez and B. Skinner.  Physics Today 73, 9, 30 (2020).

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