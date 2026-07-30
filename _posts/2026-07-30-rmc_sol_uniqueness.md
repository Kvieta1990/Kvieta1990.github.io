---
layout: post
title: Configuration Uniqueness in Reverse Monte Carlo Modeling and Potential Solution with Kullback-Liebler Divergence
subtitle:
tags: [scattering, RMC]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In Reverse Monte Carlo modeling, when the data are not providing strong enough constraint towards the fitting target, potentially we would be having the overfitting problem. The fitting may converge to a state simply because the state entropy is high, i.e., the landscape of the configuration space is very flat regarding the agreement with the experimental data. Though, it should always be kept in mind that what our target is for the fitting — it is the actual degree of freedom that we are interested in (or based on which we draw some critical conclusions) that is our fitting target. It is NOT the whole configuration at all, unless we are interested in the position of every single atom in the supercell, which is never the case.

There are multiple ways to help solve the uniqueness problem, e.g., by adding in the energy constraint so that the configuration space degeneracy can be lowered, helping the configuration to converge to more real/stable minima. Introducing some prior to lead the fitting is another alternative solution, based on the reverse Kullback-Leibler (KL) divergence,

$$
D_{KL}(\rho||\rho^0) = \sum\rho\,log\bigg(\frac{\rho}{\rho^0}\bigg)
$$

where $$\rho$$ and $$\rho^0$$ refers to the density distribution of the fitted configuration and a prior distribution, respectively. Here, the distribution may refer to the atoms distribution around the average positions. When adding such a term to the overall $$\chi^2$$ to guide the model, we are actually penalizing configurations that steps far off from the prior distribution, thus regulating the atoms to only move when they have to (to fit the data).

Such an approach is relevant to the KL divergence but they are not actually the same thing. The normal KL divergence is defined as follows,

$$
D_{KL}(P||Q)=\sum P\,log\bigg(\frac{P}{Q}\bigg)
$$

where $$P$$ is the true probability distribution and $$Q$$ is the model distribution. The KL divergence here can be interpreted as `information waste` and here follows come some explanations. First, the summation goes over possibilities that the distribution is describing. For example, we have 26 letters from ‘a’ to ‘z’ and each of them has a certain show-up probability in a certain context — the true distribution $$P$$ may say ‘a’ shows up with the probability of 0.5 while our model $$Q$$ may say ‘a’ will show up with the probability of 0.1. When we have an event with a low probability of happening, we can imagine that we would need more information bits to describe the event — for example, a two-side coin needs 0 and 1 to describe all the possible events with each happening with the probability of 50%; a 6-side dice would then need 3 bits (000, 001, etc.) to cover all the possibilities where each of the possibilities has a probability of 16.666…% to happen. In general, we may have,

$$
\text{Bits needed} = -log_2P(x)
$$

Here we are taking $$2$$ as the base, which does not have to be case at all since anyhow the absolute magnitude of the formulation does not matter that much. So, our model then says the bits needed is,

$$
\text{Bits needed said by model} = -log_2Q(x)
$$

Therefore, we see by having the model to estimate the true distribution, we are wasting some bits of information by,

$$
[-log_2Q(x)] - [-log_2P(x)] = log_2\bigg[\frac{P(x)}{Q(x)}\bigg]
$$

from which we can give the defined KL divergence,

$$
D_{KL}(P||Q)=\sum P(x)\,log\bigg[\frac{P(x)}{Q(x)}\bigg]
$$

where the base of $$2$$ is replaced by the base of $$e$$. Also, the summation term in the KL divergence definition has a multiplicative term $$P(x)$$, inferring that the KL divergence is calculating the `expected information loss`.

<br>

References
===

[1] [https://en.wikipedia.org/wiki/Kullback%E2%80%93Leibler_divergence](https://en.wikipedia.org/wiki/Kullback%E2%80%93Leibler_divergence)