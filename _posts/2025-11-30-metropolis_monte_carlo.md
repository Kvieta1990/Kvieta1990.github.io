---
layout: post
title: About the Metropolis Monte Carlo Method
subtitle:
tags: [maths, statistics, general, physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/dice.jpg"
   style="border:none;"
   width="300"
   alt="dice"
   title="dice" />
</p>

When we have a high-dimension configuration space, e.g., a system containing hundreds of thousands of atoms with each atom carrying 3 degrees of freedom (DOF), it becomes difficult to sample the system. For example, if we want to sample the configuration with low energy (thus considered as energetically stable) or in best agreement with experimental data, we will find such a fact -- the portion that the collection of systems that we want to sample takes among the overall number of possbile configurations, is vanishingly small. Therefore, accordingly, the probability of sampling such configurations in a normal way, e.g., sampling in a uniformly random manner, is vanishingly small. In practice, this just becomes a forbidded task.

In such situations, Metropolis Monte Carlo algorithm comes to help, at least providing a practical way to sample the system. Here in the blog, I am taking systems that can be described by the canonical ensemble as an examples to demonstrate the mathematical idea for the algorithm. In fact, the same logic to be presented here applies to general types of distributions as well. For the canonical ensemble, systems follow the Boltzmann distribution -- namely, the distribution of the number of microstates (one microstate is just a possible configuration) follows the Boltzmann statistics. In another word, the number of configurations with a certain property (e.g., energy, or, agreement with experimental data) follows the Boltzmann statistics,

$$
e^{-\beta E_i}
$$

where $$\beta = \frac{1}{k_B T}$$, $$k_B$$ is the Boltzmann constant, and $$T$$ refers to the system temperature. $$i$$ here labels a certain state of the system. Given the distribution, the idea of Metropolis Monte Carlo algorithm is straightforward. Starting from a random configuration of the system, for a certain configuration and the proposed next-step in the chain of configuration evolution, though the absolute probability for each configuration (current one and the one proposed next) cannot be calculated (since the partition function is intractable, i.e., impossible to calculate), the relative probability can be computed since the partition function will simply be canceled out. As such, during the Metropolis Monte Carlo running, the relative probability of the proposed configuration will be calculated against the current configuration. If the relative probability is larger than 1, this means the proposed configuration is for sure possible and therefore it will be considered as being accepted. If the relative probability is smaller than 1, then we don’t directly reject it. Instead, a random number will be generated and compared to the relative probability. The acceptance/rejection will be determined depending on the relation between the two. Doing such is to avoid the system being trapped in local minima. Such a process applies to any type of distribution. Mathematically, we have,

$$
P_i = \frac{e^{-\beta E_i}}{Z}
$$

where the top part is just the Boltzmann distribution and the bottom part is the ***partition function*** (which sums up similar top terms for all microstates, serving as the overall normalization factor). Say $$i=c$$ for the current configuration and $$i=n$$ for the next configuration (i.e., the proposed one), then the relative probability is calculated as,

$$
\frac{P_n}{P_c} = e^{-\beta(E_n - E_c)}
$$

This is the quantity in the heart of the commonly implemented Metropolis Monte Carlo approach for sampling systems (e.g., the one used in the RMCProfile package [1-3]). In the expression above, I am using energy in the exponential part of the Boltzmann distribution. In practice, depending on our specific problems, it can be replaced by other quantities, e.g., the agreement with the experimental data while using the algorithm for driving the structure model towards the best agreement with the experimentally measured data, e.g., the total scattering data.

<br />

References
===

[1] [https://rmcprofile.ornl.gov/](https://rmcprofile.ornl.gov/)

[2] [Matthew G Tucker, et al. 2007, J. Phys.: Condens. Matter, 19, 335218.](https://iopscience.iop.org/article/10.1088/0953-8984/19/33/335218)

[3] [Yuanpeng Zhang, et al. 2020, J. Appl. Cryst., 53, 1509-1518.](https://doi.org/10.1107/S1600576720013254)