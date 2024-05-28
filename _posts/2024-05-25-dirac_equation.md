---
layout: post
title: Dirac Equation
subtitle:
tags: [Quantum Mechanics, Relativity]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Introduction
===

Here in this post, I will be summarizing the derivation of Dirac Equation, following the YouTube video series in Ref. [1-3]. Dirac equation, at the same time beautifully combining quantum mechanics (QM) and special relativity (SR), also infers the existence of electron spin, proposes the existence of antimatter, and predicts the hydrogen fine structure. The video series give a concise summary about the derivation and indication of Dirac equation, which I think is worthwhile being summarized in the form of text. By checking through this post, one is expected to have a basic understanding of the origination of Dirac equation, its important indication (e. g., how the electron spin magically emerges purely from combining QM with SR), and the problem that comes with it (and its further indication).

Pauli Matrix
===

First, let's take a look at electron spin in the QM framework -- starting from that the operators in QM can be given in the form of matrix while states can be presented in the form of vector, the two states of electron spin can be written as follows,

> Before Dirac equation, the existence of electron spin has already been pinned down -- starting from the Stern-Gerlach experiment demonstrating the two level splits of silver atoms in magnetic field [4], to the proposition of Pauli exclusion principle [5], to the identification of electron spin by Goudsmit and Uhlenbeck [5, 6]. All of these happened before Dirac's work. However, no theoretical framework had ever been presented to explain the emergence of electron spin, theoretically.

$$
|\uparrow\rangle = \begin{bmatrix}1\\ 0\end{bmatrix},\ \ |\downarrow\rangle = \begin{bmatrix}0\\1\end{bmatrix}\ \ \ \ (1)
$$

and the stationary Shrödinger equation for electron spin is,

$$
\hat{S}_z|\uparrow\rangle = \frac{\hbar}{2}|\uparrow\rangle,\ \ \hat{S}_z|\downarrow\rangle = \frac{\hbar}{2}|\downarrow\rangle\ \ \ \ (2)
$$

The matrix form should be,

$$
\begin{bmatrix}A & B\\C & D\end{bmatrix}\begin{bmatrix}1\\0\end{bmatrix} = \begin{bmatrix}A\\C\end{bmatrix} = \frac{\hbar}{2}\begin{bmatrix}1\\0\end{bmatrix}\ \ \ \ (3)
$$

so that we have $$A = 1, C = 0$$. Similarly, we can derive that $$B = 0, D = -1$$, for spin down relevant matrix equation. Therefore, the $$\hat{S}_z$$ operator can be given as,

$$
\hat{S}_z = \frac{\hbar}{2}\begin{bmatrix}1 & 0\\0 & -1\end{bmatrix}\ \ \ \ (4)
$$

To obtain $$\hat{S}_x$$ and $$\hat{S}_y$$, we need to go back to the general angular momentum operators, as presented below,

$$
\begin{align}
\hat{L}_x & = -i\hbar(y\partial_z - z\partial_y)\ \ \ \ (5)\\
\hat{L}_y & = -i\hbar(y\partial_x - z\partial_z)\ \ \ \ (6)\\
\hat{L}_z & = -i\hbar(y\partial_y - z\partial_x)\ \ \ \ (7)
\end{align}
$$

and their commute relation,

$$
\begin{align}
[\hat{L}_x, \hat{L}_y] & = i\hbar\hat{L}_z\ \ \ \ (8)\\
[\hat{L}_y, \hat{L}_z] & = i\hbar\hat{L}_x\ \ \ \ (8)\\
[\hat{L}_z, \hat{L}_x] & = i\hbar\hat{L}_y\ \ \ \ (8)
\end{align}
$$

Writing the $$\hat{S}_x$$ and $$\hat{S}_y$$ operators in the matrix form with 4 parameters for each, taking the already derived $$\hat{S}_z$$ operator above, applying the commute relation to the spin angular momentum, we can solve the two unknown matrices -- see the Mathematica notebook in Ref. [7]. However, the approach can only solve the two matrices to the following stage,

$$
\hat{S}_x = \frac{\hbar}{2}\begin{bmatrix}0 & 1/c\\ c & 0\end{bmatrix}\ \ \ \ (9)
$$

and

$$
\hat{S}_y = \frac{\hbar}{2}\begin{bmatrix}0 & -i/c\\ ic & 0\end{bmatrix}\ \ \ \ (10)
$$

with the parameter $$c$$ yet to be determined. To find out $$c$$, either we assume knowing the eigen state for $$\hat{S}_x$$ (or $$\hat{S}_y$$), or we have to go through the comprehensive QM approach as detailed in Ref. [8]. Let's assumse we do know the eigen state for $$\hat{S}_x$$, as given by,

$$
|\hat{S}_x;+\rangle = \frac{\sqrt{2}}{2}\begin{bmatrix}1\\0\end{bmatrix} + \frac{\sqrt{2}}{2}\begin{bmatrix}0\\1\end{bmatrix} = \frac{\sqrt{2}}{2}\begin{bmatrix}1\\1\end{bmatrix}\ \ \ \ (11)
$$

Then solving the eigen equation for $$\hat{S}_x$$ should give us $$c = 1$$, so that we can arrive at the final solution for all the three spin operators,

$$
\hat{S}_x = \frac{\hbar}{2}\begin{bmatrix}0 & 1\\ 1 & 0\end{bmatrix},\ \hat{S}_y = \frac{\hbar}{2}\begin{bmatrix}0 & -i\\ i & 0\end{bmatrix},\ \hat{S}_z = \frac{\hbar}{2}\begin{bmatrix}1 & 1\\ 0 & -1\end{bmatrix}\ \ \ \ (12)
$$

and the Pauli matrix is taking another form, removing the $$\hbar/2$$ constant,

$$
\hat{\sigma}_x = \begin{bmatrix}0 & 1\\ 1 & 0\end{bmatrix},\ \hat{\sigma}_y = \begin{bmatrix}0 & -i\\ i & 0\end{bmatrix},\ \hat{\sigma}_z = \begin{bmatrix}1 & 1\\ 0 & -1\end{bmatrix}\ \ \ \ (13)
$$

Finally, the overall electron wavefunction with spin can be expressed in the form of `spinor`,

$$
|\psi\rangle = \begin{bmatrix}\psi_1(x, y, z, y)\\\psi_2(x, y, z, t)\end{bmatrix}\ \ \ \ (14)
$$

where $$｜\psi_1｜^2dV$$ gives the probability of finding the electron at some point at some time with spin up and $$｜\psi_2｜^2dV$$ gives the probability of finding the electron at some point at some time with spin down, with the following constraint for the overall probability,

$$
\langle \psi | \psi \rangle = \int (|\psi_1|^2 + |\psi_2|^2)dV = 1\ \ \ \ (15)
$$

Relativistic Wave Equation
===

Starting with the relativistic expression for energy,

$$
E = \sqrt{(pc)^2 + (mc^2)^2}\ \ \ \ (16)
$$

we can substitute in the quantum operators for corresponding quantities,

$$
i\hbar\partial_t\psi = \bigg[\sqrt{-(\hbar c)^2\nabla^2 + (mc^2)^2}\bigg]\psi\ \ \ \ (17)
$$

Square both sides of the relativistic energy equation and then replace again those quantities with corresponding operators to make sense out of the square root of operators,

$$
-\hbar^2\partial_t^2\psi = -\hbar^2c^2\nabla^2\psi + m^2c^4\psi\ \ \ \ (18)
$$

This is just the `Klein-Gordon` (KG) equation, which does not come without problems. Referring to Ref. [9], two of the main problems are,

- it predicts incorrect energy levels for hydrogen atom.

- the wavefunction solution of the equation cannot be interpreted as probability of finding electron at a point, and also the value of $$\int｜\psi｜^2dV$$ can change, i.e., the electron density is not conservative (if we interpret $$｜\psi｜^2$$ as the probability).

Dirac Equation
===

Dirac's idea of getting around with the problems in KG equation is to turn the square root expression in the original relativistic equation into a parameterized form,

$$
E = \sqrt{(pc)^2 + (mc^2)^2} = \sqrt{p^2 + m^2} = \alpha_xp_x + \alpha_yp_y + \alpha_zp_z + \beta m\ \ \ \ (19)
$$

where the `natural unit` is being used, namely $$c = \hbar = 1$$. Then, squaring both sides of the trial equation,

$$
\begin{align}
E^2 & = p^2 + m^2 = (\alpha_xp_x + \alpha_yp_y + \alpha_zp_z + \beta m)(\alpha_xp_x + \alpha_yp_y + \alpha_zp_z + \beta m)\\
& = \alpha_x^2p_x^2 + \alpha_y^2p_y^2 + \alpha_z^2p_z^2 + \beta^2m^2\\
& \hspace{0.5cm} + \alpha_x\alpha_yp_xp_y + \alpha_y\alpha_xp_yp_x + \alpha_x\alpha_zp_xp_z + \alpha_z\alpha_xp_zp_x + \alpha_x\beta p_xm + \beta\alpha_xmp_x\\
& \hspace{0.5cm} + \alpha_y\alpha_zp_yp_z + \alpha_z\alpha_yp_zp_y + \alpha_y\beta p_ym + \beta\alpha_ymp_y + \alpha_z\beta p_zm + \beta\alpha_zmp_z\\
& = p_x^2 + p_y^2 + p_z^2 + m^2\ \ \ \ (20)
\end{align}
$$

Comparing across terms, it is easy to find out that all the cross terms should vanish, leaving,

$$
\alpha_x^2 = \alpha_y^2 = \alpha_z^2 = 1\ \ \ \ (21)
$$

However, considering the cross terms, e.g., we should have,

$$
\alpha_x\alpha_yp_xp_y + \alpha_y\alpha_xp_yp_x = (\alpha_x\alpha_y + \alpha_y\alpha_x)p_xp_y\ \ \ \ (22)
$$

since $$[\hat{p}_x, \hat{p}_y] = 0$$. Therefore,

$$
\alpha_x\alpha_y = -\alpha_y\alpha_x = -\alpha_x\alpha_y\ \ \ \ (23)
$$

which, if $$\alpha_x$$ and $$\alpha_y$$ are numbers, indicate that one should have $$\alpha_x = \alpha_y = 0$$. This actually contradicts with the result obtained above telling that $$\alpha_x^2 = \alpha_y^2 = 1$$. However, if $$\alpha_x$$, $$\alpha_y$$ and $$\alpha_x$$ are matrices, such a contradiction could be resolved, and in fact, the Pauli matrix just satisfies what we need, as,

$$
\begin{align}
\hat{\sigma}_x\hat{\sigma}_y & = -\hat{\sigma}_y\hat{\sigma}_x\\
\hat{\sigma}_x\hat{\sigma}_z & = -\hat{\sigma}_z\hat{\sigma}_x\\
\hat{\sigma}_y\hat{\sigma}_z & = -\hat{\sigma}_z\hat{\sigma}_y\ \ \ \ (24)
\end{align}
$$

and

$$
\hat{\sigma}_x^2 = \hat{\sigma}_x^2 = \hat{\sigma}_x^2 = \begin{bmatrix}0 & 1\\ 1 & 0\end{bmatrix} \ \ \ \ (25)
$$

where the identity matrix is serving the role of `1`. We are, though, still missing the matix for $$\beta$$, and it turns out that we cannot find four $$2\times 2$$ matrices that meet our needs. So, in Dirac's solution, four $$4\times 4$$ matrices were constructed as follows,

$$
\alpha_x = \begin{bmatrix}0 & 0 & 0 & 1\\ 0 & 0 & 1 & 0\\ 0 & 1 & 0 & 0\\ 1 & 0 & 0 & 0\end{bmatrix}\ \ \ \ (26)
$$

$$
\alpha_y = \begin{bmatrix}0 & 0 & 0 & -i\\ 0 & 0 & i & 0\\ 0 & -i & 0 & 0\\ i & 0 & 0 & 0\end{bmatrix}\ \ \ \ (27)
$$

$$
\alpha_z = \begin{bmatrix}0 & 0 & 1 & 0\\ 0 & 0 & 0 & -1\\ 1 & 1 & 0 & 0\\ 0 & -1 & 0 & 0\end{bmatrix}\ \ \ \ (28)
$$

$$
\beta = \begin{bmatrix}1 & 0 & 0 & 0\\ 0 & 1 & 0 & 0\\ 0 & 0 & -1 & 0\\ 0 & 0 & 0 & -1\end{bmatrix}\ \ \ \ (29)
$$

The Dirac equation for free electron can then be given as,

$$
i\partial_t\psi = -i\alpha_x\partial_x\psi - -i\alpha_y\partial_y\psi - -i\alpha_z\partial_z\psi + m\beta\psi\ \ \ \ (30)
$$

As we turn those coefficients in the equation into matrices, the wavefunction solution should also be given in the form that is similar to the `spinor` and in this case, we will have to have a `4-component` vector form of the wavefunction,

$$
\psi = \begin{bmatrix}\psi_1 \\ \psi_2 \\ \psi_3 \\ \psi_4\end{bmatrix}\ \ \ \ (31)
$$

and the Dirac equation can be written as,

$$
i\partial_t\begin{bmatrix}\psi_1 \\ \psi_2 \\ \psi_3 \\ \psi_4\end{bmatrix} = -i\partial_x\begin{bmatrix}\psi_4 \\ \psi_3 \\ \psi_2 \\ \psi_1\end{bmatrix} + \partial_y\begin{bmatrix}-\psi_4 \\ \psi_3 \\ -\psi_2 \\ \psi_1\end{bmatrix} - i\partial_z\begin{bmatrix}\psi_3 \\ -\psi_4 \\ \psi_1 \\ -\psi_2\end{bmatrix} + m\begin{bmatrix}\psi_1 \\ \psi_2 \\ -\psi_3 \\ -\psi_4\end{bmatrix}\ \ \ \ (32)
$$

which then turns into four pieces of euations,

$$
\begin{align}
i\partial_t\psi_1 & = -i\partial_x\psi_4 - \partial_y\psi_4 - i\partial_z\psi_3 + m\psi_1\\
i\partial_t\psi_2 & = -i\partial_x\psi_3 + \partial_y\psi_3 + i\partial_z\psi_4 + m\psi_2\\
i\partial_t\psi_3 & = -i\partial_x\psi_2 - \partial_y\psi_2 - i\partial_z\psi_1 - m\psi_3\\
i\partial_t\psi_4 & = -i\partial_x\psi_1 + \partial_y\psi_1 + i\partial_z\psi_2 - m\psi_4\ \ \ \ (33)
\end{align}
$$

First, we take a look at the electron at rest, i.e. $$p = 0$$, in which case all the spatial terms in the Eqn. (33) will vanish, leaving,

$$
\begin{align}
i\partial_t\psi_1 & = m\psi_1\\
i\partial_t\psi_2 & = m\psi_2\\
i\partial_t\psi_3 & = - m\psi_3\\
i\partial_t\psi_4 & = - m\psi_4\ \ \ \ (34)
\end{align}
$$

For each of the components, the equation resembles the stationary Shrödinger equation, where the wavefunction solution can be separated into the time-independent part and the time dependent part ($$e^{-iEt}$$), so that the general form of the solution for the equation would be,

$$
i\partial_t e^{-iEt} = Ee^{-iEt}\ \ \ \ (35)
$$

Putting all 4 components together, the solution to Dirac equation for electron at rest should be,

$$
\psi = \begin{bmatrix}1 \\ 0 \\ 0 \\ 0\end{bmatrix}e^{-iEt}\ \ \ \ (36)
$$

or

$$
\psi = \begin{bmatrix}0 \\ 1 \\ 0 \\ 0\end{bmatrix}e^{-iEt}\ \ \ \ (36)
$$

for $$E = m$$ (remember that we are using tne natural unit here so the energy value should actually be $$E = mc^2$$ -- refer to Eqn. 19). The solution corresponding to a negative energy value $$E = -m$$ is,

$$
\psi = \begin{bmatrix}0 \\ 0 \\ 1 \\ 0\end{bmatrix}e^{-iEt}\ \ \ \ (37)
$$

or

$$
\psi = \begin{bmatrix}0 \\ 0 \\ 0 \\ 1\end{bmatrix}e^{-iEt}\ \ \ \ (38)
$$

So, the energy value corresponding to the solution of Dirac equation for electron at rest does reproduce what special relativity is supposed to give us, i.e., $$E = mc^2$$. Therefore, zero mass should correspond to zero energy and vice versa, namely, zero energy corresponds to `nothing`. So, what does negative energy really mean?

With a two-component spinor space, the spin operator (the $$z$$-component) is given by the Pauli matrix and for the four-component wavefunction, the z-component of the spin operator can be constructed by putting two Pauli matrices along the diagonal, as,

$$
\hat{\Sigma}_z = \begin{bmatrix}1 & 0 & 0 & 0 \\ 0 & -1 & 0 & 0 \\ 0 & 0 & 1 & 0 \\ 0 & 0 & 0 & -1 \end{bmatrix}\ \ \ \ (39)
$$

When operating on the four-component wavefunction, we have,

$$
\hat{\Sigma}_z\psi = \begin{bmatrix}\psi_1 \\ -\psi_2 \\ \psi_3 \\ -\psi_4\end{bmatrix}\ \ \ \ (40)
$$

> Follwing the result above for the electron at rest, the first two components correspond to the positive energy solution and the last two for the negative energy. In each group of components, the first component corresponds to spin up and the second for spin down.

and the actual spin operator is given as $$\hat{S}_z = \frac{1}{2}\hat{\Sigma}_z$$ by bringing back the coefficient -- remember that we are still taking the natural unit here and therefore the $$\hbar$$ coefficient is still omitted. Further consider the Hamiltonian operator,

$$
\hat{H} = -i\alpha_x\partial_x - i\alpha_y\partial_y - i\alpha_z\partial_z + m\beta\ \ \ \ (41)
$$

If we put in all the derived coefficients in their matrix form into the Hamiltonian, considering the angular momentum operator, e.g., $$\hat{L}_z = -i(x\partial_y - y\partial_x)$$, and calculate the following commute relation,

$$
\hat{H}\hat{L}_z\psi - \hat{L}_z\hat{H}\psi\ \ \ \ (42)
$$

we could find out that the commute relation does not return 0, i.e., the Hamiltonian operator and the angular momentum operator does not commute with each other. This means the angular momentum in the case of Dirac equation can no longer be determined simultanesouly with the energy. In another word, given a state with a definite value of angular momentum, the energy is no longer conserved. However, if we include the spin operator to make,

$$
\hat{J}_z = \hat{L}_z + \hat{S}_z\ \ \ \ (43)
$$

the coupled (or, total) angular momentum $$\mathbf{J}$$ is conserved, i.e.,

$$
\hat{H}\hat{J}_z\psi - \hat{J}_z\hat{H}\psi = 0\ \ \ \ (44)
$$

Therefore, we can see that in the QM theory framework, the spin-orbital coupling is a result of taking into account of the special relativity.

Next, we take a look at the solution for moving electron, taking the example of electron moving along the $$x$$-direction, i.e., $$\mathbf{p} = (p_x, 0, 0)$$. The Dirac equation can be given as,

$$
\begin{align}
i\partial_t\psi_1 & = -i\partial_x\psi_4 + m\psi_1\\
i\partial_t\psi_2 & = -i\partial_x\psi_3 + m\psi_2\\
i\partial_t\psi_3 & = -i\partial_x\psi_2 - m\psi_3\\
i\partial_t\psi_4 & = -i\partial_x\psi_1 - m\psi_4\ \ \ \ (45)
\end{align}
$$

where the terms relevant to the $$y$$ and $$z$$ components of the momentum is omitted. From the equation, it can be seen that the first and fourth components are coupled whereas the second and third components are coupled. The trial solution concerning the first and fourth components would be,

$$
\psi = \begin{bmatrix}1 \\ 0 \\ 0 \\ a\end{bmatrix}e^{-(p_xx - Et)}\ \ \ \ (46)
$$

where the parameter $$a$$ is yet to be determined. Putting the trial solution into Eqn. (45), we obtain,

$$
\begin{align}
E & = p_xa + m\\
0 & = 0\\
0 & = 0\\
Ea & = p_x - ma\ \ \ \ (47)
\end{align}
$$

The solution for $$a$$ from the first and fourth equation should be equal, yielding $$E^2 = p^2 + m^2$$, which we know is true, so we do have the valid solution for $$a$$ for the set of equations in Eqn. (47),

$$
a = \frac{p_x}{E + m}\ \ \ \ (48)
$$

for $$E = \sqrt{p_x^2 + m^2} > 0$$, and,

$$
a = \frac{｜E｜ + m}{-p_x}\ \ \ \ (49)
$$

for $$E = -\sqrt{p_x^2 + m^2} < 0$$. The corresponding set of solutions for the positive and negative energy for the wavefunction can then be given as,

$$
\psi = \begin{bmatrix}1 \\ 0 \\ 0 \\ \frac{p_x}{E + m}\end{bmatrix}e^{i(p_xx - Et)},\ \psi = \begin{bmatrix}0 \\ 1 \\ \frac{p_x}{E + m} \\ 0 \end{bmatrix}e^{i(p_xx - Et)}\ \ \ \ (50)
$$

and,

$$
\psi = \begin{bmatrix}0 \\ \frac{-p_x}{｜E｜ + m} \\ 1 \\ 0 \end{bmatrix}e^{i(p_xx - Et)},\ \psi = \begin{bmatrix} \frac{-p_x}{｜E｜ + m} \\ 0 \\ 0 \\ 1 \end{bmatrix}e^{i(p_xx - Et)}\ \ \ \ (51)
$$

respectively. There are several 'weird' aspects for the solution of Dirac equation for moving electron,

1. The negative energy solution suggests that as the momentum of electron becomes larger, the energy actually become lower. This means that the particles indicated by the negative energy solution tends to move faster and faster by giving up energy to a lower energy (thus more stable) state, until the particle approaches the speed of light. This does sound counterintuitive.

2. Unlike the Shrödinger equation, the wavefunction solution does not come with pure spin up or spin down. Checking the wavefunction solution above, we can see that the solution contains the positive energy component with spin up and a negative energy component with spin down, or vice versa.

3. The solution above corresponds to the an observer at rest, observing the electron moving in the rest frame. However, if an observer moves along with the same electron, they would observe the electron being at rest and therefore the solution would go back to the solution for rest electron presented above, which would yield different momentum (0 in that case) and energy ($$mc^2$$ in that case) as compared to the moving electron solution. Not only this, the different observer would observe different spin for the electron, as the rest observer would observe different spin state whereas the moving observer would observe only one spin state -- refer to Eqn. (36-38).

Forgetting about the several critical problems of Dirac equation yet to be addressed, one of its greatest success is to predict the hydrogen fine structure. So, let's summarize a bit about what we can say about hydrogen fine structure with the Dirac equation. First, the Dirac equation for hydrogen atom can be written as,

$$
i\partial_t\psi = -i\alpha_x\partial_x\psi - i\alpha_y\partial_y\psi - i\alpha_z\partial_z\psi + m\beta\psi - \frac{\alpha}{r}\psi\ \ \ \ (52)
$$

where the last term originates from the eletric potential field by the proton and the coefficient $$\alpha \approx \frac{1}{137}$$ is the fine structure constant. Unlike the Shrödinger equation, the Dirac equation in Eqn. (52) yields common eigen states indicated by quantum numbers $$n$$, $$j$$ and $$m_j$$, where $$n$$ refers to the energy, $$j$$ for the coupled ($$\mathbf{J} = \mathbf{L} + \mathbf{S}$$) angular momentum, and $$m_j$$ for the $$z$$ component of the overall angular momentum. The eigen value solution for Dirac equation even for the simple hydrogen problem is complicated,

$$
E_{nj} = \frac{m}{\sqrt{1 + \frac{\alpha^2}{[n - j - \frac{1}{2} + \sqrt{(j + \frac{1}{2})^2 - \alpha^2}]^2}}} = m - \frac{m\alpha^2}{2n^2} - \frac{m\alpha^4}{2n^4}(\frac{n}{j + 1/2} - \frac{3}{4}) + \cdots\ \ \ \ (53)
$$

where the first term corresponds to the energy of electron at rest (the $$mc^2$$ term), the second term corresponds to the Shrödinger equation solution and the following term is just the fine structure term which indicates the energy level split as the result of the spin-orbital coupling. The important fact is that the energy level split predicted by the Dirac equation agrees well with what was observed by the experiment. A detailed description of Dirac equation's solution can be found in Ref. [10] (see chapter 8).

About the Negative Energy Solution
===

For the postive energy solution, the lowest possible energy level corresponds to the electron at rest, giving the energy of $$E = mc^2$$ and as the electron moves faster, the momentum will increase and the energy will go up in level. When electron jumps from the lower energy level to a higher one, it will acquire energy from external resources like photons. However, the existence of negative energy levels means that electrons can jump from the postive energy states to those negative states by emitting photons and such a process can keep going without end -- as it goes on, electron would gain momentum and thus would move faster and faster until approaching the speed of light. This brings in the difficulty for the Dirac equation to stand and its indication at this point seems impossible and indeed it is impossible. The workaround proposed by Dirac himself is to assume that all the negative energy states are fully occupied and not observable until an electron is excited to jump from the negative energy states to the positive states, leaving a hole in the so-called `Dirac sea` formed by all electrons occupying all the negative energy states. Such a hole was later on identified as anti-electron, the particle with the same mass as electron but with positive charge of the same magnitude. So, the Dirac equation actually predicts the existence of anti-matter, which did come as a surprise initially. But not so long after Dirac's proposition and prediction, Carl Anderson experimentally observed the existence of anti-electron, called `positron`, which won himself the Nobel Prize in Physics in 1936 [11].

<br />

Finally, it should be pointed out that though Dirac equation predicts the existence of positron, but it achieves this by introducing the Diract sea that is full of un-observable infinite number of electrons occupying the negative energy states. However, at the end of the day, the picture with electron and positron is simple and elegant enough to explain the experiments, so comes the question why we actually need the invention of Dirac sea in the first place. This is a pain point of Dirac equation and it actually goes back to the problem with quantum mechanics itself, which, is a theory dealing with a single electron and `positron` never appears in the theory and therefore no wonder it cannot directly work with the idea of positron without any workaround. Second, in quantum mechanics, the wavefunction in conserved in space, meaning that the full space integration will always yield 1 (assuming normalized wavefunction). Therefore, it is not compatible with the idea of particle being created or destroyed. Instead, it only allows the idea of particles transitioning between energy levels, and therefore, the creation of particles would somehow correspond to the picture of transitioning from an invisible state in the quantum mechanics framework and vice versa for the disappearance of particles. That is to say, the quantum mechanics framework fundamentally is not compatible with the relativistic wave mechanics. We need quantum electrodynamics (QED) to come into help, with this regard.

<!-- Template for picture and equation -->

<!-- <p align='center'>
<img src="/assets/img/posts/a_b_projection.png"
   style="border:none;"
   width="1000"
   alt="abproj"
   title="abproj" />
Lattice vectors projection in the Cartesian coordinate system. The left panel shows the $$b$$-axis projection and the right panel shows the $$a$$-axis projection.
</p>

$$
\begin{equation}
\begin{aligned}
\vec{a} & = \sqrt{a^2 - \Big[\frac{a(cos\gamma - cos\alpha cos\beta)}{sin\alpha}\Big]^2 - a^2cos^2\beta}\,\vec{\hat{x}} + \frac{a(cos\gamma - cos\alpha cos\beta)}{sin\alpha}\,\vec{\hat{y}} + a\,cos\beta\,\vec{\hat{z}}\\
\vec{b} & = b\,sin\alpha\,\vec{\hat{y}} + b\,cos\alpha\,\vec{\hat{z}}\\
\vec{c} & = c\,\vec{\hat{z}}
\end{aligned}
\end{equation}
$$ -->

References
===

[1] [https://www.youtube.com/watch?v=OCuaBmAzqek](https://www.youtube.com/watch?v=OCuaBmAzqek)

[2] [https://www.youtube.com/watch?v=tR6UebCvFqE&t=2s](https://www.youtube.com/watch?v=tR6UebCvFqE&t=2s)

[3] [https://www.youtube.com/watch?v=0DL-Xa1f3QI](https://www.youtube.com/watch?v=0DL-Xa1f3QI)

[4] [https://en.wikipedia.org/wiki/Stern%E2%80%93Gerlach_experiment](https://en.wikipedia.org/wiki/Stern%E2%80%93Gerlach_experiment)

[5] [https://en.wikipedia.org/wiki/Pauli_exclusion_principle](https://en.wikipedia.org/wiki/Pauli_exclusion_principle)

[6] [The discovery of the electron spin](/assets/files/spin_discovery.pdf)

[7] [Pauli matrix derivation](/assets/files/pauli_matrix_semi_solution.nb)

[8] [Spin Algebra, Spin Eigenvalues, Pauli Matrices](/assets/files/spin_lecture.pdf)

[9] [Klein-Gordon equation lecture notes](/assets/files/gft_handout2_06.pdf)

[10] J. J. Sakurai and J. Napolinano, Modern Quantum Mechanics.

[11] [https://www.nobelprize.org/prizes/physics/1936/anderson/facts/](https://www.nobelprize.org/prizes/physics/1936/anderson/facts/)
