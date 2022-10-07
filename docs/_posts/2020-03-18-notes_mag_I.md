---
layout: post
title: Notes on magnetism - I
subtitle: Magnetism of free atoms and ions
tags: [solid state physics, magnetism]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
To understand the magnetism of free atoms and ions, we may go through several stages, as follows,
</p>
<ul>
<li>Hartree approximation - electrons are treated in a non-interactive manner.</li>
<li>Beyond Hartree - interaction between electrons is taken into account.</li>
<li>Spin-orbital coupling.</li>
<li>Hyperfine interaction - the interaction between electrons and nucleus.</li>
</ul>

<p style='text-align: justify'>
<u><b>Stage-1</b></u>

<br />

At this stage, the single electron Schrodinger equation can be solved in the spherical potential field and one can get quantized energy levels indexed by quantum number $$n$$, $$l$$ and $$m$$. Hydrogen atom is the simplest and the most special case - the Coulomb potential is rigorously inverse proportional to $$r$$ and therefore energy levels with the same $$n$$ but different $$l$$ are degenerate. For general cases, e. g. hydrogen-like atoms, the effective Coulomb potential is not rigorously inverse proportional to $$r$$, as the result of screening effect from the atom core charge (nucleus + electrons). This results in the splitting of $$r$$ levels. However, no matter for which situation, the degeneracy over $$m$$ is always there. Based on the recipes given here, one can then follow the Aufbau principle to fill electrons into various shells. Suppose we have already filled all levels between 1s and 4s and we are left with 5 more electrons to worry about, how many possibilities we have to finish filling the 5 electrons? The general formula is given as $$C_{2(2l+1)}^{n_{nl}}$$

<br />

where $$l$$ refers to the angular momentum quantum number and $$n_{nl}$$ refers to the number of left-over electrons. The logic here is that we have in total $$2(2l+1)$$ degenerate states (considering spin up and down) and we can just pick up any $$n_{nl}$$ ones from them to fill electrons, simply because they are all degenerate.
</p>

<br />

<p style='text-align: justify'>
<u><b>Stage-2</b></u>

<br />

Going beyond the Hartree approximation, we start to consider the interaction between electrons and here it comes that Hund's rule plays an important role for determining the ground state (N.B. Hund's rule is not working for determining excited state. Also, it is even not working perfectly, though for most of cases it is working fine, for determining the ground state). Behind the Hund's rule, we have some fundamental physics, which we will mention later. But here we take exactly the same example mentioned above - to fill 5 electrons into the 3d levels. The 1st Hund's rule says we should have the maximum coupled S. Here it immediately brings in a critical question: what coupled S really is. We already said that by going beyond the Hartree approximation, interaction between electrons is taken into account. Therefore, now electrons are not independent any more and we say they are coupled, including the coupling between orbital and spin momentum. In another word, previously we have been using $$l$$, $$s$$, $$m_l$$ and $$m_s$$ to label quantum states, but now we have to change our label for quantum states to the coupled representation, using $$L$$, $$S$$, $$m_L$$ and $$m_S$$ instead. How to get to coupled representation from independent representation is a typical/fundamental quantum mechanics problem, for which we can refer to Shankar's book Principles of quantum mechanics (2nd edition, chapter-15). Back to the 1st Hund's rule mentioned above (focusing on the coupling between electron spins for the moment), it can give us a non-degenerate (unique) ground state, with spins of all the five electrons aligned in a parallel manner, occupying each of the $$m_l = -2, -1, 0, 1, 2$$ level. Why? Because Hund's rules says we should have the coupled $$S$$ as large as possible, and we know that the largest $$S$$ should be equal to the largest possible value of $$S_z$$. Furthermore, we know for sure that the largest possible value of $$S_z$$ should be $$5/2$$ with all spins lined up in the same direction. Here it may be a bit confusing that we said we go from independent representation to coupled representation, but it seems that the ground coupled state is just one of the original state in the independent representation. Actually, they are just seemingly identical and actually they are different stuff - in the coupled representation, the ground state we arrived at above is labeled with quantum number $$S = 5/2$$ and $$m_S = 5/2$$. However, for the independent representation, we have 5 independent labels for this single state (it's just that all the five labels takes $$m_s = 1/2$$)! Furthermore, if going beyond the ground state, we will immediately see the difference between these two representations - in most of the cases, the coupled state is a superposition of those states in the independent representation, where we have the so-called Clebsch-Gordan (CG) coefficients as the linear combination coefficients. For those excited states in the coupled representation, again we can refer to Shankar's book mentioned above for details (chapter-15).
</p>

<blockquote cite="">
Here, though not directly relevant to current discussion, it's still worth mentioning the other two general important issues in fundamental quantum mechanics (which most people just take for granted yet without knowing why): 1) given the principal quantum number $$n$$, we all know the orbital angular momentum can take $$0, 1, \dots, n-1$$. But why? We can find the answer to this question in Shankar's book mentioned above (page 422-423, 2nd edition), where the quantum version of Runge-Lenz vector is introduced to act as a ladder operator with respect to orbital angular momentum. The values of orbital angular momentum is then obtained by observing that all $$l>n$$ terms will vanish. 2) Given the orbital angular momentum quantum number $$l$$, we all know $$m_l = -l, -(l-1), \dots, (l-1), l$$. But why? The answer to this question can be found in page 321-324 in Shankar's book and again the ladder operator is acting an important role here.
</blockquote>

<p style='text-align: justify'>
When the coupled spin $$S$$ does not recognize a unique ground state, here comes the 2nd Hund's rule, concerning coupling of orbital angular momentum. It says the ground state should be the one with largest coupled $$L$$. Following the same recipe above, we should immediately know that we are actually looking for a state with largest possible $$L_z$$. For the five electrons situation mentioned above, coupled $$S$$ has already specified a unique ground state. To demonstrate the 2nd Hund's rule, we go for six electrons situation. Following the 1st Hund's rule, the first five electrons should line up in a parallel manner into the five available levels and the 6th electron can choose whichever of the first five electrons to pair with, and obviously after pairing we should have the 6th electron aligned in the opposite direction with the spins of the first five electrons. To determine the true ground state, we need the 2nd Hund's rule here - we are trying to look for the largest possible $$L_z$$, which obviously point to that the 6th electron should be put on the level indexed by $$m_L = \pm 2$$ (here the sign does not really make any difference since it is just a matter of choosing the positive direction of $$z$$-axis).
</p>

<blockquote cite="">
The golden rule to keep in mind concerning the angular momentum coupling is that the value of the coupled angular momentum is equal to the largest possible sum of the corresponding magnetic quantum number of all the independent angular moment.
</blockquote>

<p style='text-align: justify'>
Considering the interaction between electrons, the total number of non-degenerate states become $$(2L+1)(2S+1)$$.
</p>

<blockquote cite="">
Understanding the physics behind the first two Hund's rules. 1) Considering the first Hund's rule, according to Pauli exclusion principle, electrons are not allowed to stay at exactly the same state. Therefore, by aligning in a parallel manner, electrons have to 'stay away from each other' (e. g. by taking up states indexed by different magnetic quantum number), which then help reducing the system energy (we can understand this as an effective Coulomb repulsion). 2) As for the second Hund's rule, electrons tend to have the same orbital angular momentum, which means they rotate in the same direction and therefore they become further apart effectively because of the Coulomb repulsion - this also helps lowering the system energy.
</blockquote>

<br />

<p style='text-align: justify'>
<u><b>Stage-3</b></u>
</p>

<p align='center'>
<img src="/assets/img/posts/pauli_dirac.jpg"
   style="border:none;"
   alt="pauli_dirac"
   title="pauli_dirac" />
<br />
</p>

<p style='text-align: justify'>
Now we need to bring in the spin-orbital coupling, which is crucial for understanding both the magnetic property of free atoms or ions discussed here and that of solid materials where atoms are bonded together to form crystal field. Attached picture shows two key figures in shaping quantum mechanics - Paul Dirac and Wolfgang Pauli. Specially, the relativistic version of $${\rm{Shr}}\ddot o{\rm{dinger}}$$ equation given initially by Dirac not only embed spins in a natural way, but also it brings in the spin-orbital coupling term explicitly.

<br />

Here we will just briefly mention the spin-orbital coupling in terms of $$LS$$ mode coupling (another alternative coupling mode is the $$jj$$ mode, for which the coupling between $$l$$ and $$s$$ of a single electron is considered first and the coupling between electrons are considered as perturbation. In contrast, for the $$LS$$ mode coupling, since the coupling between electrons is stronger, all $$l$$'s and $$s$$'s are coupled first and $$LS$$ coupling is considered afterwards). Within the $$LS$$ coupling mode, $$L$$ and $$S$$ are coupled following the basic addition principle of angular momentum (see chapter-15 in Shankar's book mentioned above), which gives $$J = |L-S|, \dots, |L+S|$$. In this case, the degenerate states in stage-2 discussed above will be further split, with respect to the coupled quantum number $$J$$ and here we have the 3rd Hund's rule comes into play - the ground state takes minimum $$J$$ if we have less than half filled levels (e. g. suppose the situation where we have 4 electrons filling the $$3d$$ level); or the ground state takes maximum $$J$$ if we have more than half filled levels (e. g. suppose the situation where we have 6 electrons filling the $$3d$$ level); when we have half filled level, the 3rd Hund's rule becomes trivial since we have $$L = 0$$ (e. g. suppose the situation where we have 5 electrons filling the $$3d$$ level) and thus we have $$|L-S| = |L+S|$$.
</p>

<br />

<p style='text-align: justify'>
<u><b>Stage-4</b></u>

<br />

The hyperfine interaction which further splits energy levels is due to the interaction between nucleus spins and electron spins. Detailed formulation is a bit complicated and we won't proceed with it here. We can refer to section-2.6 in the lecture note <a href="https://draft.blogger.com/blog/post/edit/713170236114697752/2147277757047669261#" target="_blank">Theory of Magnetism</a> by Dr. C. Timm.
</p>