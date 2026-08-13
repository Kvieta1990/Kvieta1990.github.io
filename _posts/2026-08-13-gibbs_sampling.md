---
layout: post
title: Gibbs Sampling
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In one of my earlier posts [1], several typical sampling algorithms were briefly discussed, such as the direct inverse sampling, rejection sampling and Markov Chain Monte Carlo (MCMC) sampling. The direct inverse sampling requires analytical form of the distribution function and reversability. For suitable 1D distribution function, it can be straightforwardly implemented. For high-dimensional distributions, we need to apply the algorithm on conditional distributions. The rejection method can be applied to general distribution functions that are not reversable. For example, Ref. [2] presents a complicated probability distribution function,

$$
f(x) = e^{-x^2/2} \cdot [sin^2(6 + x) + 3 \cdot cos^2(x) \cdot sin^2(4x) + 1]
$$

in which case it is practically impossible to work out the reverse function of the cumulative distribution function and therefore the direct inverse sampling is not applicable. The rejection method can be applied to high dimension as well but the efficiency is a big limitation. MCMC is a typical option to sample complicated high-dimensional space and a comprehensive introduction is already covered in my earlier post [3]. In Ref. [3], I was mainly focusing on the Metropolis-Hasting MCMC -- in fact, when I was learning through the MCMC algorithm, I did not realize we actually have other algorithms than the Metropolis-Hasting method that fall into the scope of MCMC. Here in this post, I am putting down some notes about the Gibbs sampling algorithm which also belongs to the MCMC family.

Ref. [4] provides a very nice introduction to the Gibbs sampling algorithm and a Python implementation for a bivariate Gaussian distribution is also provided. I really enjoyed reading it and I don't want/need to repeat it all here. Instead, I am putting down here,

- some side notes about my understanding of the algorithm

- a comparison to the conditional sampling with the direct inverse method (I know it is probably naive but initially when I thought about the multi-variate version of the inverse method, I was wondering whether that is just the Gibbs sampling since both of them are working with conditional distribution 😂)

- a slightly more complicated and real-life example for the application of Gibbs sampling

Fundamentally, Gibbs sampling is based on the following compact formulation,

$$
x_m^k \sim p(x_m \vert x_1^k, x_2^k, \cdots, x_{m - 1}^k, x_{m + 1}^{k - 1}, \cdots, x_n^{k - 1})
$$

Here, $$n$$ refers to the dimension of the space, i.e., the number of independent variables we want to sample. $$m$$ refers to a specific variable that we are currently sampling. The sampling works in an iterative manner and $$k$$ refers to the index of the iteration. To unwrap a bit, the formulation says, to sample variable $$x_m$$, in the $$k^{\text{th}}$$ round of sampling, we are going to sample from the conditional distribution,

$$
x_m \sim p(x_m \vert x_{-m})
$$

where $$x_{-m}$$ means all the variables except $$x_m$$. The conditional distribution means fixing all $$x_{-m}$$'s so that the multi-variate distribution becomes a single-variate distribution of $$x_m$$. Regarding the fixed values for $$x_{-m}$$, here is the thing -- suppose we sample the variables following the order of $$x_1 \rightarrow x_2 \rightarrow \cdots$$, when sampling $$x_m$$ in the $$k^{\text{th}}$$ round of the iteration, values for $$x_1 \rightarrow x_{m - 1}$$ are already sampled in the $$k^{\text{th}}$$ round so can be used. For variables $$x_{m + 1} \rightarrow x_{n}$$, the $$k^{\text{th}}$$ round of values are not yet available and therefore we have to use their values from the previous round, i.e., the $$(k - 1)^{\text{th}}$$ round of sampled values. Following the compact formulation here, we can start from a random value for each of the variables and then implement an iterative sampling routine to perform the multi-variate Gibbs sampling.

Taking the bivariate Gaussian distribution as an example, Ref. [4] gives its Python realization of the Gibbs sampling, and here I am attaching the Jupyter notebook reproduced from Ref. [4] -- [Click Me](../assets/files/Gibbs_sampling_demo.ipynb). Here, an interesting question to think about is -- the inverse sampling method also works with conditional distribution for multi-variate distributions, what is the difference from the Gibbs sampling? Taking the bivariate case as an example, we have,

$$
p(x, y) = p(x)p(y \vert x)
$$

and we can sample $$x$$ first,

$$
\begin{align}
U_1 & \sim U(0, 1)\\
X & = F_X^{-1}(U_1)
\end{align}
$$

where $$F_X$$ refers to the cumulative distribution function corresponding to the marginal distribution function $$p(x)$$, i.e., all other variables (for bivariate distribution here, we only have $$y$$) integrated out. $$U(0, 1)$$ refers to the uniform distribution over the range $$(0, 1)$$. Having the sampled value for $$x = X$$, we can sample the conditional distribution $$p(y \vert x)$$,

$$
\begin{align}
U_2 & \sim U(0, 1)\\
Y & = F_{Y\vert X}^{-1}(U_2 \vert X)
\end{align}
$$

to give a sample $$(X, Y)$$ from the joint distribution $$p(x, y)$$. From the process here, we can see the difference from the Gibbs sampling. For the inverse method, the sampling follows a sequential approach -- it starts from the marginal distribution for the first variable, then move to sample the conditional distribution for the next variable, and so on. For example, if we have three variables, we want to follow the chain below,

$$
p(x, y, z) = p(x)p(y \vert x)p(z \vert x, y)
$$

For $$p(y \vert x)$$, the value for $$x$$ is known (sampled in the first step), and $$z$$ is integrated out. For $$p(z \vert x, y)$$, both $$x$$ and $$y$$ are known (sampled in the first and second step, respectively). For Gibbs sampling, we start from random values for all variables and the following sampling for all variables follow their own conditional distributions,

$$
(x^0, y^0)
$$

and,

$$
\begin{align}
x^1 & \sim p(x \vert y^0)\\
y^1 & \sim p(y \vert x^1)
\end{align}
$$

then,

$$
\begin{align}
x^2 & \sim p(x \vert y^1)\\
y^2 & \sim p(y \vert x^2)
\end{align}
$$

and so forth, to form a Markov chain,

$$
(x^0, y^0) \rightarrow (x^1, y^1) \rightarrow (x^2, y^2) \rightarrow \cdots
$$

Next, I put down an example of using the Gibbs Sampling for a real-life physics problem -- simulation of the 2D Ising Model.

## The problem: an intractable joint distribution

Consider a two-dimensional lattice of $$L \times L$$ spins, $$s_i \in \{-1, +1\}$$, interacting according to the Ising Hamiltonian,

$$H(\mathbf{s}) = -J\sum_{\langle j,k\rangle} s_j s_k - h\sum_j s_j$$

where $$\langle j,k \rangle$$ denotes neighboring lattice sites, $$J$$ is the coupling strength, and $$h$$ is an external field. The system's equilibrium distribution is the Boltzmann distribution,

$$p(\mathbf{s}) = \frac{1}{\mathcal{Z}}\exp\big[-\beta H(\mathbf{s})\big], \qquad \mathcal{Z} = \sum_{\mathbf{s}} \exp\big[-\beta H(\mathbf{s})\big]$$

The sum defining $$\mathcal{Z}$$ runs over all $$2^{L^2}$$ spin configurations. For a modest $$20 \times 20$$ lattice this is $$2^{400}$$ terms -- far beyond exact enumeration. Without $$\mathcal{Z}$$, the joint density cannot be evaluated or normalized, and direct sampling from $$p(\mathbf{s})$$ is not possible. This is the high-dimensional, intractable-partition-function setting in which Gibbs sampling becomes useful rather than merely illustrative.

## Why Gibbs sampling applies: the conditional is tractable

Although the joint distribution cannot be normalized, the **full conditional** distribution of a single spin given every other spin can be. Each spin interacts only with its four nearest neighbors, so the conditional density depends on a small, fixed number of neighboring values rather than on the full configuration. This locality is what makes single-site conditionals cheap to derive and sample from, even though the joint distribution is not.

## Deriving the full conditional

Split the Hamiltonian into the terms involving $$s_i$$ and the remainder,

$$H(\mathbf{s}) = -s_i\underbrace{\left(J\sum_{j \in \text{nbr}(i)} s_j + h\right)}_{h_i} + H_{-i}(\mathbf{s}_{-i})$$

where $$h_i$$, the **local field** at site $$i$$, depends only on the four neighboring spins and is independent of $$s_i$$ itself. Substituting into the Boltzmann factor,

$$p(\mathbf{s}) = \frac{1}{\mathcal{Z}}\exp(\beta s_i h_i)\exp[-\beta H_{-i}(\mathbf{s}_{-i})]$$

Conditioning on $$\mathbf{s}_{-i}$$ (all the other spins other than the one we are currently trying to sample) fixes the second factor, which cancels between numerator and denominator when normalizing over the two possible values of $$s_i$$,

$$
\begin{align}
p(s_i \mid \mathbf{s}_{-i}) & = \frac{p(\mathbf{s}_{-i} \mid s_i)p(s_i)}{\sum_i p(\mathbf{s}_{-i} \mid s_i)p(s_i)}\\
& = \frac{\frac{1}{\mathcal{Z}_{\text{local}}\mathcal{Z}_{\text{non-local}}}\exp[-\beta H_{-i}(\mathbf{s}_{-i})]\exp(\beta s_i h_i)}{\frac{1}{\mathcal{Z}_{\text{local}}\mathcal{Z}_{\text{non-local}}}\exp[-\beta H_{-i}(\mathbf{s}_{-i})]\exp(+\beta h_i) + \frac{1}{\mathcal{Z}_{\text{local}}\mathcal{Z}_{\text{non-local}}}\exp[-\beta H_{-i}(\mathbf{s}_{-i})]\exp(-\beta h_i)}\\
& = \frac{\exp(\beta s_i h_i)}{\exp(\beta h_i) + \exp(-\beta h_i)}
\end{align}
$$

which gives, for $$s_i = +1$$,

$$
p(s_i = +1 \mid \mathbf{s}_{-i}) = \frac{1}{1 + \exp(-2\beta h_i)} = \sigma(2\beta h_i)
$$

with $$\sigma$$ the logistic sigmoid, and $$p(s_i = -1 \mid \mathbf{s}_{-i}) = 1 - \sigma(2\beta h_i)$$ by symmetry. This conditional depends only on the scalar $$h_i$$, making it trivial to sample from despite the intractability of the full joint distribution. A Gibbs sampler for the Ising model therefore proceeds by repeatedly visiting spins and redrawing each one from this conditional, given the current values of its neighbors -- a procedure known in the statistical mechanics literature as the heat-bath algorithm.

## Python implementation: single-site sweeps

A direct implementation sweeps through the lattice site by site, at a fixed inverse temperature $$\beta$$, redrawing each spin from the conditional derived above using its current neighbors.

<br>

Here is the attached the script, [ising_gibbs.py](../assets/files/ising_gibbs.py).

## Python implementation: sweeping β across the critical point

The single-spin procedure above can be repeated over a range of $$\beta$$ values to trace out the magnetization as a function of temperature, recovering the ferromagnetic phase transition purely from Gibbs samples -- with no closed-form expression for $$Z$$ ever computed.

<br>

Here is the attached the script, [ising_phase_transition.py](../assets/files/ising_phase_transition.py).

### The checkerboard update

Directly looping over all $$L^2$$ sites for many $$\beta$$ values is slow. A vectorized alternative updates the lattice in two batches using a checkerboard (bipartite) decomposition.

**Why it works.** Color the lattice like a checkerboard: site $$(i,j)$$ is "black" if $$i+j$$ is even and "white" otherwise. On a square lattice with nearest-neighbor coupling, every neighbor of a black site is white and vice versa -- the two color classes form a proper 2-coloring of the lattice graph. Since each spin's conditional distribution depends only on its four neighbors, and no two black sites share a neighbor relationship, the black spins are mutually independent conditional on the white sublattice,

$$p(\mathbf{s}_{\text{black}} \mid \mathbf{s}_{\text{white}}) = \prod_{i \,\in\, \text{black}} p(s_i \mid \text{its 4 white neighbors})$$

This is an instance of blocked Gibbs sampling: instead of updating one spin at a time, an entire block of variables is resampled jointly from its conditional distribution given everything outside the block. Ordinarily this requires knowing the block's joint conditional, but here the conditional independence within a color class means that joint conditional is simply the product of the individual single-site conditionals -- so drawing each black spin independently from its own conditional is exactly equivalent to sampling the block jointly.

**How it works.** Each sweep proceeds in two passes:

1. Compute the local field at every lattice site simultaneously, using the current spin configuration.

    <br>

    > When I say 'current spin configuration', I mean the current spin configuration as of the sampling iteration. With the checkerboard type of spin updating, all black-color spins are updated simultaneously and all white-color spins are updated simultaneously. In the script attached above, the black-color spins go first in the loop and therefore when updating black-color spins in the $$k^{\text{th}}$$ round of iteration, all white-color spins are still with the values from the $$(k - 1)^{\text{th}}$$ round. When it is the turn of white-color spins to update in the $$k^{\text{th}}$$ round, all black-color spins are already updated and therefore the new values (in the $$k^{\text{th}}$$ round) of black-color spins are used for the white-color spins update.

2. Draw new values for all black sites at once from their sigmoid conditionals, leaving white sites unchanged.

3. Recompute local fields using the updated black spins, then draw new values for all white sites at once.

Because same-colored sites never condition on one another, resampling an entire color class in one vectorized array operation is mathematically identical to resampling those spins one at a time in sequence -- the checkerboard scheme is exact Gibbs sampling, not an approximation, and yields substantial speed gains by replacing $$L^2$$ sequential single-site draws with two whole-array operations per sweep.

<br>

References
===

[1] [https://iris2020.net/2026-08-02-sampling_algorithms](https://iris2020.net/2026-08-02-sampling_algorithms)

[2] [https://medium.com/data-science/what-is-rejection-sampling-1f6aff92330d](https://medium.com/data-science/what-is-rejection-sampling-1f6aff92330d)

[3] [https://iris2020.net/2026-07-31-markov_chain_monte_carlo](https://iris2020.net/2026-07-31-markov_chain_monte_carlo)

[4] [https://jaketae.github.io/study/gibbs-sampling](https://jaketae.github.io/study/gibbs-sampling/)