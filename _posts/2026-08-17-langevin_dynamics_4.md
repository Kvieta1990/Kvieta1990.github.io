---
layout: post
title: Langevin Dynamics -- From Molecular Thermostats to Diffusion Models (Part-4)
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

> This post is part-4 of the 4-parts full story about the Langevin dynamics. All references are presented in the current part.

# Part IX -- Static Targets: The Bridge to Generative Modelling

Everything to this point has been physics: given a potential $$U$$, sample its Boltzmann distribution. Generative modelling inverts the question: **given a distribution one wishes to sample, construct dynamics that samples it.** The bridge is a single substitution.

## 9.1 Inverting the Boltzmann relation

In physics, $$U$$ is given and $$p\propto e^{-\beta U}$$ follows. Now reverse the logic: let $$p(x)$$ be a *target* density -- the distribution of natural images, of stable crystal structures, of anything one wishes to generate -- and **define** an effective potential whose Boltzmann distribution is that target:

$$p(x) \equiv \frac{1}{Z}e^{-\beta U(x)} \Rightarrow U(x) \equiv -k_BT\log p(x) + \text{const} \tag{9.1}$$

$$U$$ is no longer a physical energy. It is an *engineered* potential, constructed so that the equilibrium machinery of Parts VI–VII delivers the desired distribution. Differentiating (9.1),

$$U'(x) = -k_BT\,\frac{p'(x)}{p(x)} = -k_BT\,\nabla_x\log p(x) \tag{9.2}$$

## 9.2 The score function

The quantity

$$
s(x) \equiv \nabla_x\log p(x) \tag{9.3}
$$

is called the **score function** of the distribution. Equation (9.2) reads,

$$-U' = k_BT\,s$$

Therefore, **the score is the force, up to the thermal energy scale**.

Two properties make the score the right object:

- **It is normalization-free.** $$\nabla\log\left(\tilde p/Z\right) = \nabla\log\tilde p$$, so the score of an unnormalized density equals that of the normalized one. The intractable partition function $$Z$$ -- the central obstacle in energy-based modelling -- simply never appears. This is exactly why score-based methods are practical where explicit-density methods are not.
- **It points uphill in probability.** At any point $$x$$, $$s(x)$$ is the direction of steepest increase of $$\log p$$. Following it moves toward more probable configurations.

## 9.3 The sampler

Substitute (9.2) into the overdamped Langevin equation (7.11),

$$dx = \frac{k_BT}{\zeta}\nabla_x\log p(x)\,dt + \sqrt{\frac{2k_BT}{\zeta}}\,dW_t$$

and recognize $$D = k_BT/\zeta$$ from (7.12),

$$
dx = D\,\nabla_x\log p(x)\,dt + \sqrt{2D}\;dW_t \tag{9.4}
$$

Since the target $$p$$ was engineered rather than physical, $$D$$ carries no physical meaning -- it merely sets the unit of time, and can be fixed at will. Choosing $$D = 1$$,

$$dx = \nabla_x\log p(x)\,dt + \sqrt2\;dW_t \tag{9.5}$$

Discretizing with Euler–Maruyama (8.1), step $$h$$,

$$x_{k+1} = x_k + h\,\nabla_x\log p(x_k) + \sqrt{2h}\;z_k \tag{9.6}$$

## 9.4 Reconciling the standard convention

The machine-learning literature normally writes the sampler as

$$
x_{k+1} = x_k + \frac{\varepsilon}{2}\nabla_x\log p(x_k) + \sqrt{\varepsilon}\;z_k \tag{9.7}
$$

which differs from (9.6). The two are related by a **relabelling of the step size**: setting $$\varepsilon \equiv 2h$$ in (9.6) gives exactly (9.7). Equivalently, (9.7) corresponds to choosing $$D = \tfrac12$$ rather than $$D=1$$ in (9.4).

There is no physics in this difference. The $$\varepsilon/2$$ convention, originating with Welling and Teh [14], is preferred in machine learning because it makes the drift term literally a **gradient-ascent step on $$\log p$$ with learning rate $$\varepsilon/2$$**, so the algorithm reads as "noisy gradient ascent on log-likelihood, with noise variance $$\varepsilon$$."

What is *not* a convention is the relative scaling: drift $$\propto\varepsilon$$, noise $$\propto\sqrt\varepsilon$$. Per §8.2, altering that ratio destroys the sampler.

## 9.5 Worked example: a Gaussian target

Let $$p = \mathcal N(0,s^2)$$, so $$\log p = -x^2/2s^2 + \text{const}$$ and the score is $$\nabla\log p = -x/s^2$$. The sampler (9.7) becomes the AR(1) recursion

$$x_{k+1} = \left(1 - \frac{\varepsilon}{2s^2}\right)x_k + \sqrt\varepsilon\,z_k$$

By the same algebra as §8.5, its stationary variance is

$$V_{\text{ULA}} = \frac{\varepsilon}{1-\left(1-\frac{\varepsilon}{2s^2}\right)^2} = \frac{s^2}{1 - \dfrac{\varepsilon}{4s^2}} = s^2\left(1 + \frac{\varepsilon}{4s^2} + O(\varepsilon^2)\right)$$

The sampler converges to a slightly **over-dispersed** version of the target, with relative bias $$\varepsilon/4s^2$$, and is unstable for $$\varepsilon > 4s^2$$. Both are the generic behaviours established in Part VIII, now visible in the generative-modelling notation.

## 9.6 Where the score comes from: score matching

The sampler (9.7) requires $$\nabla\log p$$ at every point -- but $$p$$ is unknown; only samples from it are available. The resolution is to *learn* the score with a neural network $$s_\theta(x)$$.

**Explicit score matching** [16] would minimize the obvious objective

$$J(\theta) = \frac12\,\mathbb E_{p(x)}\Big\|s_\theta(x) - \nabla_x\log p(x)\Big\|^2$$

which appears to require the unknown target score (the $$\nabla_x\log p(x)$$ bit). Expanding the square,

$$J(\theta) = \frac{1}{2} \int p(x) \left[ \Vert{}s_\theta(x)\Vert{}^2 - 2 s_\theta(x) \cdot \nabla_x \ln p(x) + \Vert{}\nabla_x \ln p(x)\Vert{}^2 \right] dx$$

Notice that the final term, $$\frac{1}{2} \int p(x) \Vert{}\nabla_x \ln p(x)\Vert{}^2 dx$$, is completely independent of the network parameters $$\theta$$. Therefore, it can be absorbed into a $$\text{const}$$, leaving us with,

$$J(\theta) = \frac{1}{2} \mathbb{E}_{p(x)} [\Vert{}s_\theta(x)\Vert{}^2] - \mathbb{E}_{p(x)} [s_\theta(x) \cdot \nabla_x \ln p(x)] + \text{const}$$

The core difficulty here is the middle term, which explicitly contains the unknown true score $$\nabla_x \ln p(x)$$. We can rewrite the expectation of the cross term as an integral,

$$
\begin{align}
\mathbb{E}_{p(x)} [s_\theta(x) \cdot \nabla_x \ln p(x)] & = \int p(x) \, s_\theta(x) \cdot \nabla_x \ln p(x) \, dx\\
& = \int p(x) \, s_\theta(x) \cdot \left( \frac{\nabla_x p(x)}{p(x)} \right) dx\\
& = \int s_\theta(x) \cdot \nabla_x p(x) \, dx\\
& = \Big[ s_\theta(x) p(x) \Big]_{-\infty}^{\infty} - \int \left( \nabla_x \cdot s_\theta(x) \right) p(x) \, dx\\
& = 0 -\int p(x) \left( \nabla_x \cdot s_\theta(x) \right) dx\\
& = -\mathbb{E}_{p(x)} \left[ \nabla_x \cdot s_\theta(x) \right]
\end{align}
$$

Substituting this result back into the expanded objective,

$$
\begin{align}
J(\theta) & = \frac{1}{2} \mathbb{E}_{p(x)} \left[ \Vert{}s_\theta(x)\Vert{}^2 \right] - \left( -\mathbb{E}_{p(x)} [\nabla_x \cdot s_\theta(x)] \right) + \text{const}\\
& = \mathbb{E}_{p(x)} \left[ \frac{1}{2} \Vert{}s_\theta(x)\Vert{}^2 + \nabla_x \cdot s_\theta(x) \right] + \text{const} \tag{9.8}
\end{align}
$$

> Here, we need to keep in mind that $$x_\theta(x)$$ is a vector with each component corresponding to a certain dimension. That is why we have the dot product form in the derivation above.

This is computable from samples alone. Its drawback is the divergence term, $$\nabla_x \cdot s_\theta(x)$$, which costs $$O(d)$$ backward passes in dimension $$d$$. Another problem is if the data lie on (or near) a low-dimensional manifold in a high-dimensional ambient space, the score is undefined off the manifold, and a sampler initialized from a broad prior may start from where the score does not even exist.

<br>

{: .info}
> **Some notes**
>
> Real-world data -- images, crystal structures, anything with strong internal correlations -- does not fill its ambient space uniformly. A $$1024 \times 1024$$ image is technically a point in $$\mathbb{R}^{1048576}$$, but the set of *valid* images occupies only a thin, low-dimensional manifold embedded within that vast space. Everywhere off the manifold, the true density is exactly zero: $$p(x) = 0$$, so $$\ln p(x) = -\infty$$. The score $$s(x) = \nabla_x \ln p(x)$$ is therefore either zero or ill-defined across the sharp boundary separating the manifold from empty space. Since a Langevin sampler is typically initialized from an uninformative prior like $$\mathcal{N}(0, I)$$, and high-dimensional space is overwhelmingly empty, the sampler will almost always start off-manifold -- exactly where the score provides no usable gradient signal, or blows up numerically. This is the structural reason pure score matching on a fixed target fails for real data, independent of the Jacobian cost above.

Denoising score matching (DSM), introduced by [Pascal Vincent (2011)](https://doi.org/10.1162/NECO_a_00142), then comes to help solve the computational bottleneck. The Core Idea is to perturbed data and follow the conditional score for the training. Rather than matching the score of the clean distribution $$p(x)$$ directly, DSM corrupts each data point $$x$$ with Gaussian noise, producing a noisy sample $$\tilde{x}$$ drawn from a perturbation kernel,

$$q_\sigma(\tilde{x} \mid x) = \mathcal{N}(\tilde{x}; x, \sigma^2 I) = \frac{1}{(2\pi\sigma^2)^{d/2}} \exp\left( -\frac{\Vert\tilde{x} - x\Vert^2}{2\sigma^2} \right)$$

The marginal distribution of these noisy samples is obtained by integrating out the clean data,

$$q_\sigma(\tilde{x}) = \int q_\sigma(\tilde{x} \mid x) p(x) \, dx$$

Because $$q_\sigma(\tilde{x}) $$ is a smooth convolution of $$p(x)$$ with a Gaussian, it is strictly positive everywhere -- the manifold problem noted above is resolved by construction, since noise has smeared probability mass into every corner of the ambient space.

The real payoff, however, is that the *conditional* score $$\nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x)$$ can be written down in closed form, since the kernel is just a Gaussian centered at $$x$$. Taking the log,

$$\ln q_\sigma(\tilde{x} \mid x) = -\frac{d}{2} \ln(2\pi\sigma^2) - \frac{\Vert\tilde{x} - x\Vert^2}{2\sigma^2}$$

Differentiating with respect to $$\tilde{x}$$ gives the conditional score,

$$\nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) = -\frac{\tilde{x} - x}{\sigma^2} = -\frac{\varepsilon}{\sigma}$$

where $$\varepsilon = \frac{\tilde{x} - x}{\sigma} \sim \mathcal{N}(0, I)$$ is the standardized noise vector. Unlike the marginal score $$\nabla_{\tilde{x}} \ln q_\sigma(\tilde{x})$$, this conditional quantity requires no knowledge of $$p(x)$$ at all -- it is fully determined by the noise that was just added.

### Vincent's Identity: Connecting the Marginal to the Conditional

The score model $$s_\theta(\tilde{x})$$ should ideally match the true score of the noisy marginal:

$$J_{\text{ideal}}(\theta) = \frac{1}{2} \mathbb{E}_{q_\sigma(\tilde{x})} \left[ \Vert s_\theta(\tilde{x}) - \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x}) \Vert^2 \right]$$

but $$\nabla_{\tilde{x}} \ln q_\sigma(\tilde{x})$$ is still intractable -- it requires integrating over all of $$p(x)$$. Vincent's identity shows that this objective is, up to an additive constant, identical to one built entirely from the tractable conditional score. First, the expected squared error over the joint distribution $$p(x) q_\sigma(\tilde{x} \mid x)$$ is given as,

$$\mathcal{I} = \frac{1}{2} \iint p(x) q_\sigma(\tilde{x} \mid x) \Vert s_\theta(\tilde{x}) - \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) \Vert^2 \, dx \, d\tilde{x}$$

and expanding the squared norm,

$$\mathcal{I} = \frac{1}{2} \int q_\sigma(\tilde{x}) \Vert s_\theta(\tilde{x})\Vert^2 d\tilde{x} - \int s_\theta(\tilde{x}) \cdot \left[ \int p(x) q_\sigma(\tilde{x} \mid x) \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) dx \right] d\tilde{x} + \text{const}$$

For the integral of the cross-term, we can use the following expression,

$$q_\sigma(\tilde{x} \mid x) \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) = \nabla_{\tilde{x}} q_\sigma(\tilde{x} \mid x)$$

so we have,

$$\int p(x) q_\sigma(\tilde{x} \mid x) \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) dx = \int p(x) \nabla_{\tilde{x}} q_\sigma(\tilde{x} \mid x) dx = \nabla_{\tilde{x}} \left[ \int p(x) q_\sigma(\tilde{x} \mid x) dx \right] = \nabla_{\tilde{x}} q_\sigma(\tilde{x})$$

Substituting back into the cross-term, we have,

$$\int s_\theta(\tilde{x}) \cdot \nabla_{\tilde{x}} q_\sigma(\tilde{x}) \, d\tilde{x}$$

and applying integration by parts -- the same maneuver used for explicit score matching as before -- gives the cross term as,

$$-\int q_\sigma(\tilde{x}) (\nabla_{\tilde{x}} \cdot s_\theta(\tilde{x})) \, d\tilde{x}$$

Reassembling the pieces, we will find that the result of the expansion for the conditional score loss function is identical to the expansion of the marginal score function (which I did not do here but it is basically the same as what we did with the derivation in §9.6), up to a constant, namely,

$$\frac{1}{2} \mathbb{E}_{q_\sigma(\tilde{x})} \left[ \Vert s_\theta(\tilde{x}) - \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x}) \Vert^2 \right] = \frac{1}{2} \mathbb{E}_{p(x) q_\sigma(\tilde{x} \mid x)} \left[ \Vert s_\theta(\tilde{x}) - \nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) \Vert^2 \right] + \text{const}$$

The right-hand side involves only the closed-form conditional score derived above -- the intractable marginal has been eliminated entirely, and with it, the divergence term that made explicit score matching so costly. This is Vincent's identity.

### The Practical Objective

Substituting the analytical conditional score $$\nabla_{\tilde{x}} \ln q_\sigma(\tilde{x} \mid x) = -\frac{\tilde{x} - x}{\sigma^2} = -\frac{\varepsilon}{\sigma}$$ into Vincent's identity gives:

$$J_{\text{DSM}}(\theta) = \frac{1}{2} \mathbb{E}_{p(x)} \mathbb{E}_{q_\sigma(\tilde{x} \mid x)} \left[ \left\Vert s_\theta(\tilde{x}) - \left( -\frac{\tilde{x} - x}{\sigma^2} \right) \right\Vert^2 \right]$$

Since $$\tilde{x} = x + \sigma\varepsilon$$ with $$\varepsilon \sim \mathcal{N}(0, I)$$, the expectation can be re-expressed over the clean data $$x$$ and the noise $$\varepsilon$$ directly:

$$J_{\text{DSM}}(\theta) = \frac{1}{2} \mathbb{E}_{x, \varepsilon} \left[ \left\Vert s_\theta(x + \sigma\varepsilon) + \frac{\varepsilon}{\sigma} \right\Vert^2 \right]$$

Multiplying inside the norm by $$\sigma$$ produces Equation (9.10):

$$J_{\text{DSM}}(\theta) = \frac{1}{2\sigma^2} \mathbb{E}_{x, \varepsilon} \left[ \left\Vert \sigma s_\theta(x + \sigma\varepsilon) + \varepsilon \right\Vert^2 \right]$$

### From Score to Noise Prediction

Defining a noise-prediction network $$\epsilon_\theta(\tilde{x}) \equiv -\sigma s_\theta(\tilde{x})$$ collapses the objective into a standard mean-squared-error regression:

$$
J_{\text{DSM}}(\theta) = \frac{1}{2\sigma^2} \mathbb{E}_{x, \varepsilon} \left[ \left\Vert \epsilon_\theta(x + \sigma\varepsilon) - \varepsilon \right\Vert^2 \right] \tag{9.9}
$$

Framed this way, training no longer resembles density estimation at all -- the network simply looks at a noisy sample $$x + \sigma\varepsilon$$ and regresses toward the noise vector $$\varepsilon$$ that was added to produce it.

This reformulation is what makes the method practical at scale. Explicit score matching (Equation 9.8) required computing the divergence $$\nabla_x \cdot s_\theta(x)$$, an operation whose cost grows with dimension $$d$$ and demands gradients-of-gradients on every training step. Denoising score matching (Equation 9.9) eliminates that term entirely, replacing it with a simple regression task -- and, as the earlier note on manifolds makes clear, it does so while also filling in the zero-density voids that would otherwise cripple a sampler started from random noise. The noise level $$\sigma$$ is no longer just a nuisance parameter introduced to make the math work; it is the mechanism by which the model gains a smooth, well-defined gradient signal everywhere in space, on-manifold or off.

## 9.7 The failure mode, and why annealing is necessary

The theory of Part VI guarantees convergence -- asymptotically. Section 6.9 already warned why this can be practically useless, and for real data distributions it is. Three compounding problems:

1. **Multimodality and metastability.** Real data distributions have many well-separated modes. Kramers' formula (6.16) makes inter-mode transitions exponentially rare, so a single Langevin chain, correctly implemented, will explore one mode and effectively never reach the others.

2. **Low-density regions carry no signal.** The score $$\nabla\log p$$ is estimated from training data. In regions where $$p$$ is tiny -- precisely the regions separating modes -- there are no training samples and $$s_\theta$$ is arbitrary. A walker attempting to cross between modes is guided by an essentially random vector field.

3. **The manifold problem.** If the data lie on (or near) a low-dimensional manifold in a high-dimensional ambient space, $$\log p$$ is $$-\infty$$ off the manifold and the score is undefined there. A sampler initialized from a broad prior begins where the score does not exist.

**The fix -- anneal.** Note that all three problems are *cured by adding noise*. Convolving $$p$$ with a broad Gaussian smooths modes together, fills in the low-density gaps with meaningful gradient signal, and spreads mass off the manifold into the whole ambient space. But it also destroys the fine structure one wishes to generate.

The resolution is to do both, sequentially. Choose a decreasing sequence of noise levels $$\sigma_1 > \sigma_2 > \cdots > \sigma_L$$, define the smoothed targets $$p_{\sigma_i} = p * \mathcal N(0,\sigma_i^2)$$, train a single **noise-conditional** score network $$s_\theta(x,\sigma_i)$$ on all of them via (9.10), and run the sampler (9.7) at each level in turn, warm-starting each stage from the previous stage's output:

```
x ← sample from broad prior N(0, σ₁²)
for i = 1 … L:                        # decreasing noise levels
    ε_i ← ε · σ_i² / σ_L²             # step size scaled to the level
    for k = 1 … K:                    # Langevin steps at this level
        z ← randn()
        x ← x + (ε_i/2)·s_θ(x, σ_i) + sqrt(ε_i)·z
return x
```

This is **annealed Langevin dynamics** [18]. Each level is one instance of the fixed-target problem solved in this Part: at high $$\sigma$$ the target is nearly Gaussian and mixes rapidly; at each subsequent level the chain begins already inside a high-probability region of the next, slightly sharper target, so no barrier crossing is ever required. The algorithm is a direct descendant of simulated annealing and of replica-exchange molecular dynamics, and it exchanges one impossible sampling problem for a chain of easy ones.

**This ladder of noise levels is the object that Part X takes to its continuum limit.**

# Part X -- Time-Dependent Targets: Diffusion Models

> **⚠ Notation.** From here, $$\beta(t)$$ denotes the **noise schedule** of a diffusion model, *not* the inverse temperature $$1/k_BT$$ of Parts I–IX.

## 10.1 From a ladder to a continuum

Part IX explored how to sample from a fixed target distribution using score-based Langevin dynamics. However, as noted in the manifold problem, real-world data lives on a low-dimensional manifold where the true density is zero off-manifold, causing standard score-based samplers initialized from a broad prior to fail.

To bridge this gap, practice dictates moving from a single static target to a **discrete ladder of noise levels** $$\sigma_1 > \cdots > \sigma_L$$, where each progressively smaller noise level $$\sigma_i$$ defines a smoothed target distribution $$p_{\sigma_i}$$ that is easier for the Langevin sampler to navigate.

Part X takes the limit $$L \to \infty$$.

(The idea of learning to reverse a gradual noising process predates the score-based formulation; it was introduced by [Sohl-Dickstein et al. [22]](http://localhost:8000/) under the name *nonequilibrium thermodynamics*, itself borrowing directly from statistical physics.) In the limit, the ladder becomes a continuous family of distributions $$\{p_t(x)\}_{t \in [0, 1]}$$, interpolating from the data distribution at $$t = 0$$ to a simple analytically-known distribution at $$t = 1$$. The family is not postulated; it is generated by running a stochastic process forward in time. Two questions then arise, and Anderson’s theorem and the Fokker–Planck equation answer them respectively:

1. Given a forward process that destroys data into noise, what process runs it backward?
2. What must be known in order to run it backward? (Answer: the score of $$p_t$$, at every $$t$$.)

The essential conceptual shift from Part IX is that the target is now time-dependent. In Part IX, one fixed $$p(x)$$ was approached asymptotically. Here, $$p_t(x)$$ moves, and the reverse process tracks it exactly at every instant.

## 10.2 The forward SDE

Postulate a forward noising process of the general form

$$dx = f(x,t)\,dt + g(t)\,dW_t, \qquad t: 0\to1 \tag{10.1}$$

with $$x(0)\sim p_{\text{data}}$$. Term by term, in the language of Parts III–V:

- $$f(x,t)$$ -- the **drift**, a deterministic vector field, typically a contraction toward the origin;
- $$g(t)$$ -- the **diffusion coefficient**, the amplitude of noise injection per unit time. This is the direct generalization of the constant $$\sigma$$ in the Langevin equation (2.1); the only new feature is that it may depend on $$t$$, because the "effective bath temperature" changes along the schedule.

By construction, the family $$p_t(x)$$ is the set of marginal densities of (10.1), and it obeys the Fokker–Planck equation (5.1),

$$\frac{\partial p_t}{\partial t} = -\nabla\cdot\!\big[f(x,t)\,p_t\big] + \frac{g(t)^2}{2}\nabla^2p_t \tag{10.2}$$

## 10.3 The VP-SDE: derivation from the DDPM chain

The most widely used forward process is obtained as the continuum limit of the DDPM (Denoising Diffusion Probabilistic Model, the framework derived in Part-IX) discrete Markov chain [19]. That chain is

$$x_k = \sqrt{1-\beta_k}\;x_{k-1} + \sqrt{\beta_k}\;\varepsilon_{k-1}, \qquad \varepsilon_{k-1}\sim\mathcal N(0,I) \tag{10.3}$$

with $$k = 1,\dots,N$$ and small per-step noise variances $$\beta_k$$.

**Take the continuum limit.** Set $$\Delta t = 1/N$$ and treat the per-step variance as a *rate* times the step: $$\beta_k \to \beta(t)\,\Delta t$$, with $$t = k/N$$. Expand the square root for small argument, $$\sqrt{1-\beta(t)\Delta t} \approx 1 - \tfrac12\beta(t)\Delta t$$:

$$x_{t+\Delta t} \approx x_t - \frac12\beta(t)\,\Delta t\;x_t + \sqrt{\beta(t)\Delta t}\;z_t$$

**Match against Euler–Maruyama.** The EM discretization (8.1) of (10.1) reads $$x_{t+\Delta t} = x_t + f\,\Delta t + g\sqrt{\Delta t}\,z_t$$. Comparing coefficients term by term,

$$f(x,t) = -\frac{1}{2}\beta(t)\,x, \qquad g(t) = \sqrt{\beta(t)} \tag{10.4}$$

giving the **variance-preserving (VP) SDE** [20],

$$dx = -\frac12\beta(t)\,x\,dt + \sqrt{\beta(t)}\;dW_t \tag{10.5}$$

This is precisely the structure of the Ornstein–Uhlenbeck process (4.1), with a time-dependent rate for both the linear restoring drift and the additive noise.

## 10.4 Solving the VP-SDE exactly

Equation (10.5) is linear, so the integrating-factor method of §4.1 applies verbatim. Define the cumulative schedule

$$B(t) \equiv \int_0^t\beta(s)\,ds$$

Multiply (10.5) by $$e^{B(t)/2}$$; as in §4.1 the left side assembles into $$d\!\left(e^{B(t)/2}x_t\right)$$, giving

$$x_t = e^{-B(t)/2}x_0 + \int_0^t e^{-\left[B(t)-B(s)\right]/2}\sqrt{\beta(s)}\;dW_s \tag{10.6}$$

**Mean:** $$\langle x_t\rangle = e^{-B(t)/2}x_0$$, since $$\langle dW_s\rangle = 0$$.

**Variance:** by the Itô isometry (3.11),

$$\text{Var} = \int_0^t e^{-\left[B(t)-B(s)\right]}\beta(s)\,ds = e^{-B(t)}\int_0^t e^{B(s)}\beta(s)\,ds = e^{-B(t)}\Big[e^{B(s)}\Big]_0^t = 1 - e^{-B(t)}$$

using $$\frac{d}{ds}e^{B(s)} = \beta(s)e^{B(s)}$$. Therefore the transition kernel (i.e., the distribution that $$x_t$$ follows, as derived above) is exactly Gaussian,

$$p_{t|0}(x_t\,|\,x_0) = \mathcal N\!\left(x_t;\;\sqrt{\bar\alpha_t}\,x_0,\;\;(1-\bar\alpha_t)\,I\right), \qquad \bar\alpha_t \equiv e^{-B(t)} = e^{-\int_0^t\beta(s)ds} \tag{10.7}$$

which is **identical** to the DDPM closed-form forward process $$x_t = \sqrt{\bar\alpha_t}x_0 + \sqrt{1-\bar\alpha_t}\,\varepsilon$$. The discrete and continuous formulations agree exactly, and the continuous route derives $$\bar\alpha_t$$ rather than defining it as a product $$\prod_k(1-\beta_k)$$ (for details about DDPM, see my earlier post [here](../2026-08-09-diffusion_algorithm)).

**Why "variance-preserving."** If the data are normalized to unit variance, $$\text{Var}(x_0)=1$$, then,

$$\text{Var}(x_t) = \bar\alpha_t\cdot1 + (1-\bar\alpha_t) = 1 \quad\text{for all }t$$

The signal contracts by $$\sqrt{\bar\alpha_t}$$ while the injected noise grows by exactly the complementary amount. This is the same balance that pinned the OU stationary variance to $$mk_BT$$ in Part IV -- drift and diffusion are tuned against each other so the total does not run away.

## 10.5 The VE-SDE

The alternative standard choice takes **no drift at all** -- set $$f = 0$$ and let $$\sigma(t)$$ be a prescribed increasing noise scale (i.e., as $$t$$ increases, $$\sigma(t)$$ monotonically increases). With $$g(t) = \sqrt{d[\sigma(t)^2]/dt}$$ (chosen as such that the integration below yields the variance of $$\sigma(t)^2$$ for the noise term),

$$dx = \sqrt{\frac{d[\sigma(t)^2]}{dt}}\;dW_t \tag{10.8}$$

Integrating directly (no isometry needed beyond additivity of variances):

$$\text{Var}(x_t) = \text{Var}(x_0) + \sigma(t)^2 - \sigma(0)^2$$

Variance accumulates without bound: this is the **variance-exploding (VE) SDE**. The contrast with VP is exactly the contrast between an OU process and a free Brownian particle -- with $$\gamma = 0$$ there is no restoring force to establish a stationary variance, so noise simply piles up.

## 10.6 Concrete schedules -- and the fact that they are design choices

We see two ways of perturbing the system via introducing the time-dependent noise level. Such a forward process is called noise *scheduling*. The schedule is a **modelling choice**, not a physical constant, and this is the one structural respect in which diffusion models genuinely differ from the physics they inherit: in Part IV, $$k_BT$$ was set by an actual bath; here the analogous quantity is chosen by the practitioner. The standard options,

| Schedule | Form | Typical parameters | Source |
|---|---|---|---|
| **VP, linear** | $$\beta(t) = \beta_{\min} + t(\beta_{\max}-\beta_{\min})$$ | $$\beta_{\min}=0.1$$, $$\beta_{\max}=20$$, $$t\in[0,1]$$ | [20] |
| **DDPM discrete** | $$\beta_k$$ linear in $$k$$ | $$\beta_1 = 10^{-4}$$ → $$\beta_N = 0.02$$, $$N=1000$$ | [19] |
| **VP, cosine** | $$\bar\alpha_t = \dfrac{h(t)}{h(0)},\;\; h(t) = \cos^2\!\left(\dfrac{t+s}{1+s}\cdot\dfrac{\pi}{2}\right)$$ | $$s = 0.008$$ | [23] |
| **VE, geometric** | $$\sigma(t) = \sigma_{\min}\left(\sigma_{\max}/\sigma_{\min}\right)^t$$ | $$\sigma_{\min}=0.01$$; $$\sigma_{\max}\sim$$ max pairwise data distance | [20] |

## 10.7 The reverse-time SDE

The process (10.1), run backward in time, is itself a diffusion, with drift corrected by the score:

$$
dx = \Big[f(x,t) - g(t)^2\,\nabla_x\log p_t(x)\Big]dt + g(t)\,d\bar W_t \tag{10.10}
$$

where $$d\bar W$$ denotes a Wiener process in reverse time and $$dt$$ is a negative increment.

**Derivation via the Fokker–Planck equation.** Define reversed time $$\bar t = T-t$$ and the reversed density $$\bar p_{\bar t}(x) \equiv p_{T-\bar t}(x)$$. Then $$\partial_{\bar t}\bar p = -\partial_tp_t$$, so from (10.2),

$$\frac{\partial\bar p}{\partial\bar t} = +\nabla\cdot(f\,\bar p) - \frac{g^2}{2}\nabla^2\bar p \tag{10.11}$$

> Replace $$t$$ with $$\bar{t}$$ in Eqn. (10.2) and apply $$\frac{\partial p_t}{\partial t} = \frac{\partial \bar{p}_{\bar{t}}}{\partial t} = \frac{\partial \bar{p}_{\bar{t}}}{\partial \bar{t}} \cdot \frac{d\bar{t}}{dt} = -\frac{\partial \bar{p}_{\bar{t}}}{\partial \bar{t}}$$.

The task is to find a drift $$\bar f$$ such that (10.11) *is* a standard forward Fokker–Planck equation in $$\bar t$$ with the same diffusion coefficient $$g$$:

$$\frac{\partial\bar p}{\partial\bar t} = -\nabla\cdot(\bar f\,\bar p) + \frac{g^2}{2}\nabla^2\bar p$$

Equating the two right-hand sides:

$$-\nabla\cdot(\bar f\bar p) + \frac{g^2}{2}\nabla^2\bar p = \nabla\cdot(f\bar p) - \frac{g^2}{2}\nabla^2\bar p$$

$$\nabla\cdot(\bar f\bar p) = -\nabla\cdot(f\bar p) + g^2\nabla^2\bar p = \nabla\cdot\Big[-f\bar p + g^2\nabla\bar p\Big]$$

The divergences agree, and so do their arguments,

$$\bar f = -f + g^2\frac{\nabla\bar p}{\bar p} = -f + g^2\,\nabla\log\bar p \tag{10.12}$$

Translating back to forward time (which flips the sign of $$\bar f$$ and of $$dt$$) gives (10.10).

**Why this is the whole algorithm.** The reverse drift requires exactly one unknown: $$\nabla\log p_t(x)$$, the score of the noised marginal at each time. Neither $$p_t$$ itself nor its normalization is needed -- the same normalization-free property noted in §9.2. This means we can use the Denoising Score Matching (DSM) algorithm to train a single neural network $$s_\theta(x, t)$$ across various noise levels to predict the noise added to data samples. The network implicitly learns this exact score function at every point in time. Once the neural network has learned $$\nabla_x \log p_t(x)$$, we have everything required to run the reverse SDE. We simply drop the network's score prediction into the reverse drift equation, start from a sample of pure Gaussian noise, and integrate backward to generate brand-new, pristine data.

**Relation to the Part IX sampler.** Comparing (10.10) with the fixed-target sampler (9.4), $$dx = D\nabla\log p\,dt + \sqrt{2D}dW$$:

| | Fixed-target Langevin (Part IX) | Reverse SDE (Part X) |
|---|---|---|
| Score drift | $$D\,\nabla\log p$$ | $$-g(t)^2\,\nabla\log p_t$$ (sign from reverse time) |
| Extra drift | none | $$f(x,t)$$ -- undoes the forward contraction |
| Coefficient | constant $$D$$ | time-varying $$g(t)^2$$ |
| Target | static $$p$$ | moving $$p_t$$ |
| Guarantee | $$\rho_t\to p$$ asymptotically (H-theorem, §6.7) | $$\rho_t = p_t$$ **exactly, at every $$t$$** (by construction) |

The extra term $$f$$ is present only because the forward process was non-stationary; the fixed-target sampler needed no such term because nothing was moving. The final row is the sharpest difference: the reverse SDE does not *equilibrate toward* its target, it *tracks* it exactly. Convergence in the H-theorem sense is replaced by an identity.

## 10.8 The probability flow ODE

A remarkable consequence of (10.2): the noise can be removed entirely without changing any marginal.

**Derivation.** Rewrite the diffusion term of the Fokker–Planck equation using $$\nabla p_t = p_t\nabla\log p_t$$:

$$\frac{g^2}{2}\nabla^2p_t = \frac{g^2}{2}\nabla\cdot\big(\nabla p_t\big) = \nabla\cdot\left(\frac{g^2}{2}p_t\,\nabla\log p_t\right)$$

Substituting into (10.2) and combining both terms under one divergence:

$$\frac{\partial p_t}{\partial t} = -\nabla\cdot\left[\underbrace{\left(f - \frac{g^2}{2}\nabla\log p_t\right)}_{\equiv\;v(x,t)}p_t\right] \tag{10.13}$$

This is a pure **continuity equation** with velocity field $$v(x,t)$$ -- no second-derivative term remains. A continuity equation is exactly the density evolution generated by the deterministic ODE $$\dot x = v(x,t)$$. Therefore the **probability flow ODE**

$$\frac{dx}{dt} = f(x,t) - \frac{1}{2}g(t)^2\,\nabla_x\log p_t(x) \tag{10.14}$$

produces the identical marginals $$p_t$$ at every $$t$$ as the stochastic process (10.1)/(10.10), despite containing no noise at all. Note the factor $$\tfrac12$$ relative to (10.10) -- half the score correction suffices when the diffusion term is absorbed rather than reversed.

> **Note on Stochastic vs. Deterministic Generation** 
>
> While the stochastic SDE is essential during training, providing the noisy forward process required to make learning tractable via [Denoising Score Matching](https://doi.org/10.1162/NECO_a_00142) and adding helpful error-correction "jitter" during sampling. The deterministic Probability Flow ODE can generate the exact same data distributions without any noise at all. This noise-free path turns generation into a smooth, one-to-one mapping, allowing us to use advanced numerical solvers for much faster generation, precisely encode data into latent noise, and compute exact likelihoods.

## 10.9 The score/noise-prediction identity

The network is trained to predict noise, but the samplers require the score. The two are the same object, and the relation is exact for the VP process.

From the closed-form kernel (10.7), the *conditional* score is available analytically by differentiating the Gaussian log-density (this is (9.9) again, with $$\sigma^2\to1-\bar\alpha_t$$):

$$\nabla_{x_t}\log p_{t|0}(x_t|x_0) = -\frac{x_t - \sqrt{\bar\alpha_t}x_0}{1-\bar\alpha_t} = -\frac{\varepsilon}{\sqrt{1-\bar\alpha_t}} \tag{10.15}$$

using $$x_t = \sqrt{\bar\alpha_t}x_0 + \sqrt{1-\bar\alpha_t}\,\varepsilon$$ to eliminate $$x_0$$.

By the denoising-score-matching identity of §9.6, the minimizer of the DDPM training objective $$\mathbb E\big\|\varepsilon - \epsilon_\theta(x_t,t)\big\|^2$$ is the conditional expectation $$\epsilon_\theta^*(x_t,t) = \mathbb E[\varepsilon\, \vert \,x_t]$$, and the *marginal* score is the corresponding average of (10.15):

$$\nabla_x\log p_t(x) = -\frac{\epsilon_\theta^*(x,t)}{\sqrt{1-\bar\alpha_t}} \tag{10.16}$$

**The noise-prediction objective is score matching in disguise.** A network trained to answer "what noise was added?" has, without being told, learned $$\nabla\log p_t$$ -- the one quantity the reverse SDE (10.10) and the probability flow ODE (10.14) require. Substituting (10.16) into either equation converts it into a runnable algorithm.

## 10.10 Samplers are numerical integrators

With the score supplied by the network, generating a sample means integrating a differential equation backward from $$t=1$$ to $$t=0$$, starting from $$x(1)\sim\mathcal N(0,I)$$. Every named "scheduler" is a choice of integrator:

| Sampler | Equation integrated | Scheme |
|---|---|---|
| DDPM ancestral | reverse SDE (10.10) | Euler–Maruyama (§8.1) -- fresh noise each step |
| DDIM [24] | probability flow ODE (10.14) | Euler -- deterministic, permits step skipping |
| PNDM / LMS / DPM-Solver | probability flow ODE (10.14) | linear multistep / exponential integrators -- reuse past score evaluations for higher order |
| Predictor–corrector [20] | both | alternate a reverse-SDE step with fixed-$$t$$ Langevin steps |

The ODE-based samplers need far fewer steps than the SDE-based ones for the reason familiar from ordinary numerical analysis -- deterministic trajectories are smooth, so multistep and higher-order methods achieve high accuracy with few function evaluations, whereas the $$\sqrt{\Delta t}$$ noise scaling (§8.3) caps the achievable order for stochastic schemes.

## 10.11 The complete dictionary

| Statistical mechanics | Score-based generative modelling |
|---|---|
| Potential energy $$U(x)$$ (given by physics) | $$U = -k_BT\log p$$ (engineered from the target) |
| Force $$-\nabla U$$ | Score $$\nabla\log p$$, up to $$k_BT$$ |
| Boltzmann distribution $$e^{-\beta U}/Z$$ | Data distribution $$p_{\text{data}}$$ |
| Partition function $$Z$$ | Never appears -- score is normalization-free |
| Temperature $$k_BT$$ (set by the bath) | Absorbed into step size; conventionally 1 |
| Friction rate $$\gamma$$ | Drift rate $$\tfrac12\beta(t)$$ (VP) |
| Noise amplitude $$\sigma = \sqrt{2\gamma mk_BT}$$ | Diffusion coefficient $$g(t) = \sqrt{\beta(t)}$$ (VP) |
| FD relation fixes $$\sigma$$ given $$\gamma$$ and $$T$$ | VP construction fixes $$g$$ given $$f$$ so variance is preserved |
| Stationary variance $$mk_BT$$ | $$\text{Var}(x_t) = 1$$ for all $$t$$ |
| Simulated annealing / replica exchange | Noise schedule $$\beta(t)$$ or $$\sigma(t)$$ |
| Kramers escape barrier (6.16) | Mode-mixing failure that annealing circumvents |
| Euler–Maruyama integrator | "Scheduler" (DDPM ancestral sampling) |
| H-theorem convergence $$\mathcal H\to0$$ | Anderson time-reversal: $$\rho_t = p_t$$ exactly |
| Eliminating momentum (Smoluchowski) | Eliminating noise (probability flow ODE) -- *different operations* |

## 10.12 Where the analogy has genuine limits

Three honest caveats, to avoid over-reading the correspondence:

1. **The score is learned, not exact.** All guarantees above assume $$\nabla\log p_t$$ is known. In practice $$s_\theta$$ carries approximation error, which enters the relative-entropy argument of §6.7 as a source term breaking exact monotonicity. Quantifying this is the subject of the modern convergence-rate literature for diffusion models [26], which bounds sampling error in terms of score-matching error, discretization error, and the initialization mismatch at $$t=1$$.

2. **The physical Langevin equation has momentum; the standard diffusion model does not.** The VP/VE processes are first-order, corresponding to the *overdamped* regime. Reintroducing an explicit momentum variable -- i.e. working with the full Kramers dynamics of Part V rather than its Smoluchowski reduction -- gives **critically-damped Langevin diffusion** [25], which achieves faster mixing for the same reason that underdamped MD explores configuration space faster than Brownian dynamics.

3. **The direction of the modelling arrow is reversed.** Physics starts from a known $$U$$ and asks what distribution results. Generative modelling starts from a distribution known only through samples and constructs a $$U$$ (equivalently, a score) to match. The mathematics is shared; the epistemic situation is not.

## Appendix A -- Gaussian integrals used in the text

$$\int_{-\infty}^{\infty}e^{-\alpha u^2}du = \sqrt{\frac{\pi}{\alpha}}, \qquad \int_{-\infty}^{\infty}u^2e^{-\alpha u^2}du = \frac{1}{2\alpha}\sqrt{\frac{\pi}{\alpha}}, \qquad \int_{-\infty}^{\infty}u^{2n+1}e^{-\alpha u^2}du = 0$$

Hence for the Maxwell–Boltzmann momentum distribution $$\phi_{\text{MB}}(p)\propto e^{-p^2/2mk_BT}$$ (so $$\alpha = 1/2mk_BT$$):

$$\int\phi_{\text{MB}}\,dp = 1, \qquad \int p\,\phi_{\text{MB}}\,dp = 0, \qquad \int p^2\phi_{\text{MB}}\,dp = mk_BT$$

Gaussian moments used in §3.2: for $$Z\sim\mathcal N(0,s^2)$$, $$\langle Z^2\rangle = s^2$$ and $$\langle Z^4\rangle = 3s^4$$.

## Appendix B -- Summary of key equations

| # | Equation | Meaning |
|---|---|---|
| (2.1) | $$\dot p = -U'(x) - \gamma p + \sigma\xi$$ | Langevin equation |
| (3.6) | $$(dW)^2 = dt$$ | quadratic variation of Brownian motion |
| (3.9) | $$d\phi = [a\phi' + \tfrac12b^2\phi'']dt + b\phi'dW$$ | Itô's lemma |
| (3.11) | $$\langle(\int f dW)^2\rangle = \int\langle f^2\rangle ds$$ | Itô isometry |
| (4.4) | $$\text{Var}(p_t) = \frac{\sigma^2}{2\gamma}(1-e^{-2\gamma t})$$ | OU relaxation |
| (4.8) | $$\sigma^2 = 2\gamma mk_BT = 2\zeta k_BT$$ | fluctuation–dissipation relation |
| (4.18) | $$\langle\eta(t)\eta(t')\rangle = k_BT\,\Gamma(t-t')$$ | FD theorem, general form |
| (5.1) | $$\partial_t\rho = -\partial_x(a\rho) + \partial_x^2(\tfrac{b^2}{2}\rho)$$ | Fokker–Planck equation |
| (6.9) | $$\rho_{\text{eq}} = Z^{-1}e^{-\beta H}$$ | stationary solution = Boltzmann |
| (6.13) | $$d\mathcal H/dt \le 0$$ | H-theorem |
| (7.10) | $$\partial_tn = \partial_x[\tfrac{U'}{\zeta}n] + D\,\partial_x^2n$$ | Smoluchowski equation |
| (7.11) | $$dx = -\tfrac{U'}{\zeta}dt + \sqrt{2D}\,dW$$ | overdamped Langevin SDE |
| (7.12) | $$D = k_BT/\zeta$$ | Einstein relation |
| (8.1) | $$x_{k+1} = x_k + a\Delta t + b\sqrt{\Delta t}z$$ | Euler–Maruyama |
| (9.7) | $$x_{k+1} = x_k + \tfrac{\varepsilon}{2}\nabla\log p + \sqrt\varepsilon z$$ | score-based Langevin sampler |
| (10.5) | $$dx = -\tfrac12\beta(t)x\,dt + \sqrt{\beta(t)}dW$$ | VP forward SDE |
| (10.7) | $$p_{t \vert 0} = \mathcal N(\sqrt{\bar\alpha_t}x_0, (1-\bar\alpha_t)I)$$ | VP closed-form kernel |
| (10.10) | $$dx = [f - g^2\nabla\log p_t]dt + g\,d\bar W$$ | reverse-time SDE |
| (10.14) | $$\dot x = f - \tfrac12g^2\nabla\log p_t$$ | probability flow ODE |
| (10.16) | $$\nabla\log p_t = -\epsilon_\theta/\sqrt{1-\bar\alpha_t}$$ | score $$\Leftrightarrow$$ noise prediction |

References
===

[1] P. Langevin, "Sur la théorie du mouvement brownien," *Comptes Rendus de l'Académie des Sciences (Paris)* **146**, 530–533 (1908).

[2] A. Einstein, "Über die von der molekularkinetischen Theorie der Wärme geforderte Bewegung von in ruhenden Flüssigkeiten suspendierten Teilchen," *Annalen der Physik* **322**, 549–560 (1905).

[3] G. E. Uhlenbeck and L. S. Ornstein, "On the theory of the Brownian motion," *Physical Review* **36**, 823–841 (1930).

[4] K. Itô, "Stochastic integral," *Proceedings of the Imperial Academy (Tokyo)* **20**, 519–524 (1944).

[5] H. A. Kramers, "Brownian motion in a field of force and the diffusion model of chemical reactions," *Physica* **7**, 284–304 (1940).

[6] H. Risken, *The Fokker–Planck Equation: Methods of Solution and Applications*, 2nd ed., Springer, Berlin (1989).

[7] C. W. Gardiner, *Stochastic Methods: A Handbook for the Natural and Social Sciences*, 4th ed., Springer, Berlin (2009).

[8] R. Zwanzig, *Nonequilibrium Statistical Mechanics*, Oxford University Press, Oxford (2001).

[9] A. O. Caldeira and A. J. Leggett, "Quantum tunnelling in a dissipative system," *Annals of Physics* **149**, 374–456 (1983).

[10] R. Kubo, "The fluctuation-dissipation theorem," *Reports on Progress in Physics* **29**, 255–284 (1966).

[11] C. Villani, "Hypocoercivity," *Memoirs of the American Mathematical Society* **202** (950), American Mathematical Society, Providence (2009).

[12] P. E. Kloeden and E. Platen, *Numerical Solution of Stochastic Differential Equations*, Springer, Berlin (1992).

[13] G. O. Roberts and R. L. Tweedie, "Exponential convergence of Langevin distributions and their discrete approximations," *Bernoulli* **2**, 341–363 (1996).

[14] M. Welling and Y. W. Teh, "Bayesian learning via stochastic gradient Langevin dynamics," *Proceedings of the 28th International Conference on Machine Learning (ICML)*, 681–688 (2011).

[15] G. Parisi and Y.-S. Wu, "Perturbation theory without gauge fixing," *Scientia Sinica* **24**, 483–496 (1981).

[16] A. Hyvärinen, "Estimation of non-normalized statistical models by score matching," *Journal of Machine Learning Research* **6**, 695–709 (2005).

[17] P. Vincent, "A connection between score matching and denoising autoencoders," *Neural Computation* **23**, 1661–1674 (2011).

[18] Y. Song and S. Ermon, "Generative modeling by estimating gradients of the data distribution," *Advances in Neural Information Processing Systems (NeurIPS)* **32** (2019).

[19] J. Ho, A. Jain, and P. Abbeel, "Denoising diffusion probabilistic models," *Advances in Neural Information Processing Systems (NeurIPS)* **33**, 6840–6851 (2020).

[20] Y. Song, J. Sohl-Dickstein, D. P. Kingma, A. Kumar, S. Ermon, and B. Poole, "Score-based generative modeling through stochastic differential equations," *International Conference on Learning Representations (ICLR)* (2021).

[21] B. D. O. Anderson, "Reverse-time diffusion equation models," *Stochastic Processes and their Applications* **12** (3), 313–326 (1982).

[22] J. Sohl-Dickstein, E. A. Weiss, N. Maheswaranathan, and S. Ganguli, "Deep unsupervised learning using nonequilibrium thermodynamics," *Proceedings of the 32nd International Conference on Machine Learning (ICML)*, 2256–2265 (2015).

[23] A. Q. Nichol and P. Dhariwal, "Improved denoising diffusion probabilistic models," *Proceedings of the 38th International Conference on Machine Learning (ICML)*, 8162–8171 (2021).

[24] J. Song, C. Meng, and S. Ermon, "Denoising diffusion implicit models," *International Conference on Learning Representations (ICLR)* (2021).

[25] T. Dockhorn, A. Vahdat, and K. Kreis, "Score-based generative modeling with critically-damped Langevin diffusion," *International Conference on Learning Representations (ICLR)* (2022).

[26] S. Chen, S. Chewi, J. Li, Y. Li, A. Salim, and A. R. Zhang, "Sampling is as easy as learning the score: theory for diffusion models with minimal data assumptions," *International Conference on Learning Representations (ICLR)* (2023).

[27] B. Leimkuhler and C. Matthews, *Molecular Dynamics: With Deterministic and Stochastic Numerical Methods*, Springer, Cham (2015).

[28] G. Bussi and M. Parrinello, "Accurate sampling using Langevin dynamics," *Physical Review E* **75**, 056707 (2007).

[29] D. Bakry and M. Émery, "Diffusions hypercontractives," in *Séminaire de Probabilités XIX*, Lecture Notes in Mathematics **1123**, Springer, 177–206 (1985).
