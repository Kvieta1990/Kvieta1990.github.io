---
layout: post
title: Langevin Dynamics -- From Molecular Thermostats to Diffusion Models (Part-1)
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

> This post is part-1 of the 4-parts full story about the Langevin dynamics. All references will be presented in part-4.

## 0. Scope, prerequisites, and how to read this

This document develops Langevin dynamics from first principles and follows a single thread all the way from classical statistical mechanics to the stochastic differential equations underlying modern diffusion models. The claim being defended throughout is that these are not two subjects connected by a loose analogy: the sampler at the heart of score-based generative modelling *is* the overdamped Langevin equation, with the physical potential replaced by an engineered one, and every convergence guarantee for the former is inherited from the equilibration theory of the latter.

**Prerequisites.** Multivariable calculus, ordinary differential equations, basic probability (Gaussian distributions, expectation, variance), and elementary classical mechanics. No prior exposure to stochastic calculus is assumed -- Part III builds the required machinery from scratch. Familiarity with the canonical ensemble is helpful but not required.

**Structure.**

| Part | Content |
|---|---|
| I | Deterministic dynamics, and why it cannot sample a temperature |
| II | The Langevin equation with undetermined coefficients |
| III | Stochastic calculus: Wiener processes, Itô integrals, Itô's lemma |
| IV | The fluctuation–dissipation relation, derived two ways |
| V | The Fokker–Planck equation: what it is and where it comes from |
| VI | Proof that the stationary solution is the Boltzmann distribution |
| VII | The overdamped (Smoluchowski) limit |
| VIII | Euler–Maruyama discretization and the discrete sampler |
| IX | Static targets: the bridge to generative modelling |
| X | Time-dependent targets: diffusion models |

Parts I–VIII are physics and mathematics; Parts IX–X are the machine-learning payoff. A reader interested only in the sampling algorithm can read Parts III, VII, VIII, and IX, but the justification for *why* the algorithm works lives in Parts IV–VI.

## Notation and conventions

Consistent notation matters here more than usual, because two widespread conventions for the friction coefficient differ by a factor of the mass, and because the symbol $$\beta$$ is standard for two unrelated quantities in the two halves of this document.

| Symbol | Meaning | Units (SI) |
|---|---|---|
| $$x, p$$ | position, momentum | m, kg·m/s |
| $$m$$ | mass | kg |
| $$U(x)$$ | potential energy | J |
| $$H(x,p)$$ | Hamiltonian, $$H = p^2/2m + U(x)$$ | J |
| $$T$$ | bath temperature | K |
| $$k_B$$ | Boltzmann constant, $$1.381\times10^{-23}$$ | J/K |
| $$\beta$$ | inverse temperature, $$\beta \equiv 1/k_BT$$ | 1/J |
| $$\gamma$$ | **friction rate** | 1/s |
| $$\zeta$$ | **drag coefficient**, $$\zeta \equiv m\gamma$$ | kg/s |
| $$\sigma$$ | noise amplitude in the momentum equation | kg·m·s$$^{-3/2}$$ |
| $$D_p$$ | momentum-space diffusion coefficient, $$D_p \equiv \sigma^2/2$$ | -- |
| $$D$$ | position-space (Einstein) diffusion coefficient | m²/s |
| $$W_t$$ | Wiener process (standard Brownian motion) | s$$^{1/2}$$ |
| $$\xi(t)$$ | Gaussian white noise, formally $$dW_t/dt$$ | s$$^{-1/2}$$ |
| $$\rho(x,p,t)$$ | phase-space probability density | -- |

<br>

> **⚠ Convention warning 1 -- friction.** Two forms of the Langevin equation appear in the literature:

> $$\dot p = -\gamma p + \cdots \qquad\text{versus}\qquad m\ddot x = -\zeta\dot x + \cdots$$

> Since $$p = m\dot x$$, these are the same equation with $$\zeta = m\gamma$$. The first makes $$\gamma$$ a *rate* (units 1/s), the second makes $$\zeta$$ a *drag coefficient* (units kg/s, matching [Stokes' law](https://en.wikipedia.org/wiki/Stokes%27s_law) $$\zeta = 6\pi\eta a$$ for a sphere of radius $$a$$ in a fluid of viscosity $$\eta$$). The corresponding fluctuation–dissipation relations therefore differ by a factor of $$m$$:

> $$\sigma^2 = 2\gamma m k_BT = 2\zeta k_BT$$

> This document uses $$\gamma$$ (rate) in the momentum equation and $$\zeta = m\gamma$$ wherever the drag coefficient is more natural, and states which is meant at each use.

> **⚠ Convention warning 2 -- the symbol $$\beta$$.** In Parts I–IX, $$\beta = 1/k_BT$$ is the thermodynamic inverse temperature. In Part X, $$\beta(t)$$ denotes the *noise schedule* of a diffusion model.

Throughout, one spatial dimension is used for clarity. Every result generalizes to $$d$$ dimensions by replacing $$\partial_x \to \nabla$$, $$\partial_x^2 \to \nabla^2$$ (or $$\nabla\cdot\nabla$$), and treating each Cartesian component as carrying its own independent noise.

# Part I -- Deterministic Dynamics, and Why It Is Not Enough

## 1.1 Hamiltonian equations of motion

A classical particle of mass $$m$$ moving in a potential $$U(x)$$ is described by the Hamiltonian

$$H(x,p) = \frac{p^2}{2m} + U(x)$$

and Hamilton's equations,

$$\dot x = \frac{\partial H}{\partial p} = \frac{p}{m}, \qquad \dot p = -\frac{\partial H}{\partial x} = -U'(x) \tag{1.1}$$

The second equation is Newton's second law, $$\dot p = F$$, with $$F = -U'(x)$$. This system is **closed**: no reference is made to anything outside the particle, and nothing in the equations mentions temperature.

## 1.2 Energy is conserved

Differentiate $$H$$ along a trajectory generated by (1.1):

$$\frac{dH}{dt} = \frac{\partial H}{\partial x}\dot x + \frac{\partial H}{\partial p}\dot p = \frac{\partial H}{\partial x}\frac{\partial H}{\partial p} + \frac{\partial H}{\partial p}\left(-\frac{\partial H}{\partial x}\right) = 0 \tag{1.2}$$

The two terms cancel identically. Every trajectory of (1.1) is confined for all time to the level set $$\{(x,p): H(x,p) = E_0\}$$ determined by its initial condition. **Energy is a constant of the motion, exactly, forever.**

## 1.3 Liouville's theorem

Consider now not a single trajectory but an ensemble of them, described by a phase-space density $$\rho(x,p,t)$$. Probability is conserved (trajectories are neither created nor destroyed), so $$\rho$$ obeys a continuity equation in phase space,

$$\frac{\partial\rho}{\partial t} + \frac{\partial}{\partial x}(\dot x\,\rho) + \frac{\partial}{\partial p}(\dot p\,\rho) = 0$$

Substituting (1.1) and using the fact that $$\dot x$$ depends only on $$p$$ and $$\dot p$$ only on $$x$$,

$$\frac{\partial}{\partial x}\left(\frac{p}{m}\rho\right) + \frac{\partial}{\partial p}\left(-U'(x)\rho\right) = \frac{p}{m}\frac{\partial\rho}{\partial x} - U'(x)\frac{\partial\rho}{\partial p}$$

so,

$$
\frac{\partial\rho}{\partial t} = -\frac{p}{m}\frac{\partial\rho}{\partial x} + U'(x)\frac{\partial\rho}{\partial p} \equiv \{H,\rho\} \tag{1.3}
$$

where the **Poisson bracket** of two phase-space functions $$A$$ and $$B$$ is defined as,

$$
\{A,B\} \equiv \frac{\partial A}{\partial p}\frac{\partial B}{\partial x} - \frac{\partial A}{\partial x}\frac{\partial B}{\partial p} \tag{1.4}
$$

Equation (1.3) is the **Liouville equation**. It says the density is simply advected along the Hamiltonian flow without compression: phase-space volume is preserved. This structure -- and specifically the antisymmetry $$\{A,B\} = -\{B,A\}$$, hence $$\{H,H\}=0$$ -- reappears as the key algebraic step in Part VI.

## 1.4 Why deterministic dynamics cannot sample the canonical ensemble

The goal of equilibrium statistical mechanics is often to draw samples from the **canonical (NVT) distribution**

$$\rho_{\text{eq}}(x,p) = \frac{1}{Z}e^{-\beta H(x,p)}, \qquad \beta = \frac{1}{k_BT} \tag{1.5}$$

which describes a system in thermal contact with a reservoir at temperature $$T$$. Equation (1.5) assigns nonzero probability to *every* energy, weighted exponentially. But a trajectory of (1.1) never changes its energy. A single deterministic trajectory therefore explores, at most, one energy level set -- a set of measure zero under (1.5).

<br>

> In mathematics (specifically measure theory), a set of measure zero is a subset of a space that is so infinitely small compared to the whole space that its "volume" (or area, or length, depending on the dimensions) is essentially zero.

> Example: Imagine a 3D cube. The entire volume of the cube is the "space." A single 2D sheet of paper inside that cube has a volume of exactly zero. In that 3D space, the 2D plane is a "set of measure zero."

Three consequences follow, and they motivate everything that comes after:

1. **A thermostat is necessary.** Some mechanism must be added that allows energy to flow in and out, so that the trajectory visits different energy shells with the correct relative frequencies.
2. **The thermostat must be balanced.** A mechanism that only removes energy drives the system to the potential minimum ($$T \to 0$$); one that only adds energy heats it without bound. The two effects must be tuned against each other.
3. **The balance point must encode $$T$$.** The correct balance is not a free parameter: it must reproduce (1.5) at the specified temperature.

Part II adds the mechanism; Part IV determines the balance.

# Part II -- Coupling to a Heat Bath: The Langevin Equation

## 2.1 The physical picture

Consider a heavy particle immersed in a fluid of many light particles -- a pollen grain in water, a solute molecule in solvent, an atom coupled to the phonon bath of a surrounding lattice. Tracking every bath particle explicitly is both infeasible and uninformative. Instead, the bath effect is effectively represented by two terms:

- **A systematic drag.** A particle moving through the fluid collides more often, and more energetically, with bath particles ahead of it than behind. The net effect is a force opposing the motion, proportional to the velocity for small velocities: $$-\zeta\dot x = -\gamma p$$.

- **A random force.** Even at rest, the particle is bombarded by bath particles from all directions. These collisions do not cancel exactly at any instant; the residual is a rapidly fluctuating random force.

These are *not* two independent physical effects. They are the mean and the fluctuation of *the same* microscopic bombardment. That observation, made precise, is the fluctuation–dissipation theorem (Part IV).

## 2.2 The equation, with undetermined coefficients

Adding both terms to (1.1),

$$
\dot x = \frac{p}{m}, \qquad \dot p = -U'(x) - \gamma p + \sigma\,\xi(t) \tag{2.1}
$$

This is the **Langevin equation** [1]. The three forces on the right are, in order: the conservative force from the potential, the friction, and the random force. The random force is written as an amplitude $$\sigma$$ times a normalized noise process $$\xi(t)$$ specified by,

$$\langle \xi(t)\rangle = 0, \qquad \langle \xi(t)\xi(t')\rangle = \delta(t-t') \tag{2.2}$$

and taken to be Gaussian. Such a process is called **Gaussian white noise**: "white" because its power spectrum, the Fourier transform of the delta-correlated autocovariance, is flat (all frequencies equally represented); "Gaussian" because any finite linear functional of it is normally distributed.

<br>

> Delta-correlated autocovariance refers to the equation (2.2).

Two modelling assumptions are embedded in (2.2) and deserve to be named,

- **Zero memory.** The delta correlation asserts that the random force at time $$t$$ is statistically independent of the force at any other time. Physically this requires the bath's own correlation time to be much shorter than any timescale of interest for the system -- a good approximation when the bath particles are light and fast compared to the system particle. The general case, with a memory kernel, appears in §4.4.
- **Gaussianity.** The random force is a sum of a very large number of approximately independent collision impulses; the central limit theorem then makes the sum Gaussian regardless of the distribution of individual impulses.

**At this stage $$\gamma$$ and $$\sigma$$ are two independent, undetermined constants.** Nothing so far forces any relationship between them, and nowhere does the temperature $$T$$ appear. Establishing that relationship -- and thereby introducing $$T$$ -- is the content of Part IV.

## 2.3 A warning about $$\xi(t)$$

Equation (2.1) is written in the notation of ordinary differential equations, but it is not one. The trouble is that a process satisfying (2.2) cannot be an ordinary function: setting $$t=t'$$ gives $$\langle\xi(t)^2\rangle = \delta(0) = \infty$$. White noise has infinite variance at every instant.

The equation is nonetheless meaningful, because $$\xi$$ only ever appears *integrated over time*, and its integral is perfectly well behaved. Making this precise is the business of stochastic calculus, developed next -- manipulation of (2.1) formally refers to Part IV, with the three results from Part III -- Itô's lemma, the Itô isometry, and the scaling $$dW \sim \sqrt{dt}$$ as the building block to understand the topic.

# Part III -- The Stochastic Calculus Toolkit

## 3.1 The Wiener process

Rather than defining $$\xi(t)$$ directly, define its integral. The **Wiener process** (standard Brownian motion) $$W_t$$ is the stochastic process characterised by:

- **(W1)** $$W_0 = 0$$.
- **(W2)** *Independent increments*: for $$t_1 < t_2 \le t_3 < t_4$$, the random variables $$W_{t_2}-W_{t_1}$$ and $$W_{t_4}-W_{t_3}$$ are statistically independent.
- **(W3)** *Gaussian increments*: for $$t > s$$,
$$W_t - W_s \sim \mathcal N(0,\,t-s)$$
i.e. normally distributed with mean zero and variance equal to the elapsed time.
- **(W4)** *Continuous paths*: $$t\mapsto W_t$$ is continuous with probability one.

Property (W3) carries essentially all of the unusual behaviour. The standard deviation of an increment over a time interval $$\Delta t$$ is $$\sqrt{\Delta t}$$, not $$\Delta t$$:

$$\Delta W \sim \sqrt{\Delta t} \tag{3.1}$$

For a smooth function, a small time step produces a displacement proportional to $$\Delta t$$; here it is proportional to $$\sqrt{\Delta t}$$, which is *much larger* for small $$\Delta t$$. Every peculiarity of stochastic calculus traces back to this mismatch.

**Non-differentiability.** The difference quotient is $$\Delta W/\Delta t \sim \sqrt{\Delta t}/\Delta t = 1/\sqrt{\Delta t} \to \infty$$. The Wiener process is continuous but nowhere differentiable -- its paths are infinitely jagged at every magnification. This is the precise sense in which $$\xi(t) = dW_t/dt$$ fails to exist as a function, and why (2.1) must be interpreted as shorthand for the integral equation

$$p(t) = p(0) - \int_0^t\left[U'(x(s)) + \gamma p(s)\right]ds + \sigma\int_0^t dW_s \tag{3.2}$$

The differential notation used from here on,

$$dx = \frac{p}{m}dt, \qquad dp = \left[-U'(x)-\gamma p\right]dt + \sigma\,dW_t \tag{3.3}$$

is understood as an abbreviation for (3.2). This is a **stochastic differential equation** (SDE).

**Simulating an increment.** In practice, $$dW_t$$ over a step $$\Delta t$$ is sampled as

$$\Delta W = \sqrt{\Delta t}\;z, \qquad z\sim\mathcal N(0,1) \tag{3.4}$$

which reproduces (W2) and (W3) exactly for the discrete times visited.

## 3.2 The central identity: $$(dW_t)^2 = dt$$

For a smooth function $$f$$, if we want to calculate $$\sum (\Delta f)^2$$, we would have $$\sum (\Delta f)^2 \approx \sum (f'\Delta t)^2 = O(\Delta t)\to 0$$. Fundamentally, this is because $$dt$$ is considered as an infinitesimally small quantity and therefore $$(dt)^2$$ can be ingnored. However, for stochastic integration we are dealing with here, this is not the case. Consider the sum of squared increments of $$W$$ over $$[0,T]$$, partitioned into $$n$$ equal steps of size $$\Delta t = T/n$$. We have,

$$S_n = \sum_{i=1}^n (\Delta W_i)^2$$

Its mean is, using (W3),

$$\langle S_n\rangle = \sum_{i=1}^n \langle(\Delta W_i)^2\rangle = \sum_{i=1}^n \Delta t = n\Delta t = T$$

Its variance is, using independence (W2) and the Gaussian fourth moment $$\langle Z^4\rangle = 3\langle Z^2\rangle^2$$,

$$\text{Var}(S_n) = \sum_{i=1}^n\text{Var}\!\left[(\Delta W_i)^2\right] = \sum_{i=1}^n\left[\langle(\Delta W_i)^4\rangle - \langle(\Delta W_i)^2\rangle^2\right] = \sum_{i=1}^n\left[3(\Delta t)^2 - (\Delta t)^2\right] = 2n(\Delta t)^2 = \frac{2T^2}{n}$$

<br> 

> For a variable $$X$$ following Gaussian distribution with the mean of $$0$$, the $$n$$-th moment refers to $$\langle X^n \rangle = \mathbb{E}[X^n]$$.

As $$n\to\infty$$, $$\text{Var}(S_n)\to 0$$ while $$\langle S_n\rangle = T$$ exactly. The sum of squared increments therefore converges **to a deterministic limit**:

$$\sum_i (\Delta W_i)^2 \;\longrightarrow\; T \quad\text{in mean square} \tag{3.5}$$

This is summarised by the formal rule

$$(dW_t)^2 = dt \tag{3.6}$$

together with the companion rules $$dW_t\,dt = 0$$ and $$(dt)^2 = 0$$, both of which vanish faster than $$dt$$ (respectively as $$\Delta t^{3/2}$$ and $$\Delta t^2$$) and can be dropped.

Rule (3.6) is the origin of everything distinctive about stochastic calculus. In ordinary calculus, second-order differentials are negligible; here one of them is not -- $$(dt)^2$$ and $$dW_tdt$$ are, $$(dW_t)^2 = dt$$ is not.

## 3.3 The Itô integral, and the Itô–Stratonovich ambiguity

To make sense of $$\int_0^T f(s)\,dW_s$$, we need to mimic the [Riemann construction](https://en.wikipedia.org/wiki/Riemann_integral) (basically, how we turn the integration into the summation of areas for a lot of small pieces): partition $$[0,T]$$, evaluate $$f$$ somewhere in each subinterval, multiply by the increment of $$W$$, and sum. For an ordinary [Riemann–Stieltjes integral](https://en.wikipedia.org/wiki/Riemann%E2%80%93Stieltjes_integral), the choice of evaluation point within each subinterval does not matter in the limit. **Here it does**, because $$f$$ (which typically depends on the process being driven) and $$\Delta W$$ are correlated within a subinterval, and the correlation does not vanish relative to the $$\sqrt{\Delta t}$$-sized increments.

<br>

> The expression $$\int_0^T f(s) dW_s$$ is a stochastic integral. In standard mechanics, if we want to find out how much a force changes a particle's momentum over time, we integrate that force from time $$0$$ to time $$T$$. Here, we are doing the exact same thing, but the "force" is the infinitely jagged, random microscopic bombardment from the heat bath, represented by the Wiener process increment $$dW_s$$. This integral is the mathematical act of adding up an infinite number of infinitesimally tiny, completely random kicks over a specific time window.

> **Why do we need  to evaluate it?** As pointed out in Section 2.3, the raw Langevin equation ($$dp = \dots + \sigma \xi(t)dt$$) is actually mathematically broken if you look at a single frozen instant. Pure white noise ($$\xi(t)$$) has infinite variance at any exact microsecond. We cannot ask, "What is the exact random force at exactly $$t = 1.000$$ seconds?" The math blows up. However, if we ask, "What is the total accumulated effect of the random forces between $$t = 1$$ and $$t = 2$$ seconds?", the infinities cancel out and we get a perfectly well-behaved, finite answer (a Gaussian distribution). To get from the broken instant-in-time equation to a physically measurable change over time, we are strictly required to integrate it.

> $$f(s)$$ is the amplitude (or strength) of the noise at time $$s$$. It acts as a scaling factor for the raw, mathematical random kick $$dW_s$$.

Two conventions are in standard use:

**Itô convention** [4] -- evaluate at the **left** endpoint:

$$\int_0^T f\,dW_s \equiv \lim_{n\to\infty}\sum_{i=0}^{n-1} f(s_i)\left(W_{s_{i+1}} - W_{s_i}\right) \tag{3.7}$$

**Stratonovich convention** -- evaluate at the **midpoint**:

$$\int_0^T f\circ dW_s \equiv \lim_{n\to\infty}\sum_{i=0}^{n-1} f\!\left(\tfrac{s_i + s_{i+1}}{2}\right)\left(W_{s_{i+1}} - W_{s_i}\right)$$

These give genuinely different answers. The trade-off:

| | Itô | Stratonovich |
|---|---|---|
| Evaluation point | left endpoint | midpoint |
| Anticipates future noise? | no (*non-anticipating*) | yes (slightly) |
| $$\langle\int f\,dW\rangle = 0$$? | yes | no |
| Ordinary chain rule? | no (correction term) | yes |
| Natural for | forward simulation, numerics | limits of smooth (coloured) noise |

The **Itô convention is used exclusively in this document**, for two reasons. First, it matches the physical causality of a simulation: the noise kick applied over $$[s_i, s_{i+1}]$$ should not influence the state used to compute the force at $$s_i$$. Second, it makes stochastic integrals **martingales**, so $$\langle\int f\,dW\rangle = 0$$, which simplifies every expectation computed below.

<br>

> *'it matches the physical causality of a simulation'*: At time $$s_i$$, the particle is at a specific position. We look at that position, calculate the forces acting on it, and then we let the random thermal noise kick it toward its new position at $$s_{i+1}$$. The calculation relies only on the past and present, never the future. This matches exactly how real physics works: cause precedes effect.

> A martingale is a mathematical concept that essentially models a "fair game." In a martingale, a system has no predictable drift; the expected future value, given everything you know right now, is exactly equal to its current value.

For **additive noise** -- noise amplitude independent of the state, which is the case for (3.3) since $$\sigma$$ is constant -- the two conventions coincide, so the choice is immaterial for the physics of Parts IV–VIII. It matters for multiplicative noise, and it matters for interpreting formal manipulations, as the next example shows.

<br>

> **Additive noise** *The Math*: $$b(x_t, t)$$ is just a constant (like $$\sigma$$) or a function that only changes with time $$b(t)$$. It does not contain the state variable $$x_t$$. *Physical Example*: A particle floating in a glass of water that is sitting in a perfectly temperature-controlled room. No matter where the particle floats—top, bottom, left, or right—the surrounding water molecules hit it with the exact same average thermal intensity.

> **Multiplicative noise** *The Math*: $$b(x_t, t)$$ contains the state variable $$x_t$$. *Physical Example*: Imagine that same glass of water, but now there is a blowtorch heating the left side of the glass and an ice pack cooling the right side. If the particle drifts to the left side, the water is hotter, meaning the molecules are moving faster, and the random kicks the particle receives suddenly become much stronger. The noise amplitude depends entirely on where the particle is.

Let's end this section with a working example to showcase the pecularity of stochastic integration. Back to Eqn. (2.1), the second equation is basically about the force ($$\dot p = dp/dt = f$$), and therefore each term on the right-hand side corresponds to a certain form of force. If we take the force term for the random noise $$\sigma\,\xi(t)$$ and try to calculate the average rate that the random noise pumps energy into the system $$\langle \sigma p(t)\xi(t) \rangle$$ ($$\text{Power} = \text{Force} \times \text{Velocity}$$), we may have two instinct arguments,

- Argument A: "White noise has zero memory and is completely random. Therefore, the random noise $$\xi(t)$$ shouldn't be correlated with the momentum $$p(t)$$ at all. The answer should be $$0$$."

- Argument B: "Wait, the noise is the physical force pushing the particle! Therefore, at the exact instant the noise pushes, the momentum perfectly reacts to it. They must be perfectly correlated. The answer should be $$\sigma$$."

It turns out that both are wrong. Using the explicit solution derived in §4.1, $$p(t) = \sigma\int_0^t e^{-\gamma(t-s)}dW_s$$ (again, back to Eqn. 2.1 -- integrating the noise term yields momentum gain), and $$\langle\xi(s)\xi(t)\rangle = \delta(t-s)$$, we have,

$$
\begin{align}
\langle p(t)\xi(t)\rangle & = \left\langle \left( \sigma \int_0^t e^{-\gamma(t-s)} \xi(s) ds \right) \xi(t) \right\rangle\\
& = \sigma \int_0^t e^{-\gamma(t-s)} \langle \xi(s)\xi(t) \rangle ds\\
& = \sigma\int_0^t e^{-\gamma(t-s)}\delta(t-s)\,ds = \frac{\sigma}{2}
\end{align}
$$

The factor of $$\tfrac12$$ arises because the delta function sits exactly at the endpoint of the integration range, so only half its weight is captured. This is precisely the Itô convention's non-anticipating structure showing up in a formal calculation. The clean way to avoid such reasoning entirely is Itô's lemma, next.

## 3.4 Itô's lemma (the stochastic chain rule)

**Statement.** Let $$x_t$$ satisfy the Itô SDE

$$dx_t = a(x_t,t)\,dt + b(x_t,t)\,dW_t \tag{3.8}$$

and let $$\phi(x,t)$$ be twice continuously differentiable. Then

$$
d\phi = \left[\frac{\partial\phi}{\partial t} + a\frac{\partial\phi}{\partial x} + \frac{b^2}{2}\frac{\partial^2\phi}{\partial x^2}\right]dt + b\frac{\partial\phi}{\partial x}\,dW_t \tag{3.9}
$$

**Derivation.** Taylor-expand $$\phi$$ to second order in the increment -- and, crucially, keep the second-order term, which ordinary calculus discards:

$$d\phi = \frac{\partial\phi}{\partial t}dt + \frac{\partial\phi}{\partial x}dx + \frac12\frac{\partial^2\phi}{\partial x^2}(dx)^2 + \cdots$$

Now evaluate $$(dx)^2$$ using (3.8) and the multiplication rules of §3.2:

$$(dx)^2 = \left(a\,dt + b\,dW\right)^2 = a^2\underbrace{(dt)^2}_{=0} + 2ab\underbrace{dt\,dW}_{=0} + b^2\underbrace{(dW)^2}_{=\,dt} = b^2\,dt$$

So $$(dx)^2$$ is of order $$dt$$, not $$dt^2$$ -- it must be retained. Substituting and grouping gives (3.9). Third and higher order terms involve $$(dW)^3 \sim \Delta t^{3/2}$$ or smaller, and hence vanish.

The extra term $$\tfrac12 b^2\partial_x^2\phi$$ is the **Itô correction**. It is the entire difference between stochastic and ordinary calculus, and it is responsible for the second-derivative term in the Fokker–Planck equation (Part V).

<br>

> **Worked example: $$\phi = p^2$$.** For the free Langevin process $$dp = -\gamma p\,dt + \sigma\,dW$$ (no potential), Itô's lemma with $$\phi(p) = p^2$$, $$a = -\gamma p$$, $$b = \sigma$$ gives
> $$d(p^2) = \left[-2\gamma p^2 + \sigma^2\right]dt + 2\sigma p\,dW$$
> Taking expectations and using $$\langle\int f\,dW\rangle = 0$$:
> $$\frac{d\langle p^2\rangle}{dt} = -2\gamma\langle p^2\rangle + \sigma^2 \tag{3.10}$$
> The friction term drains $$\langle p^2\rangle$$ at rate $$2\gamma$$; the $$\sigma^2$$ source term -- which is *purely* the Itô correction, and which ordinary calculus would have missed entirely -- pumps it back up. Setting the left side to zero gives the stationary variance $$\langle p^2\rangle_\infty = \sigma^2/2\gamma$$ in one line. This is the fluctuation–dissipation balance in its rawest form, and §4.1 rederives it by explicit solution as a cross-check.

## 3.5 The Itô isometry

**Statement.** For a non-anticipating integrand $$f$$,

$$
\left\langle\left(\int_0^T f(s)\,dW_s\right)^{\!2}\right\rangle = \int_0^T \langle f(s)^2\rangle\,ds \tag{3.11}
$$

**Derivation.** Discretize and square:

$$\left(\sum_i f_i\,\Delta W_i\right)^{\!2} = \underbrace{\sum_i f_i^2(\Delta W_i)^2}_{\text{diagonal}} + \underbrace{2\sum_{i<j} f_if_j\,\Delta W_i\,\Delta W_j}_{\text{cross}}$$

For the cross terms with $$i<j$$: the increment $$\Delta W_j$$ lies strictly in the future of everything determining $$f_i$$, $$f_j$$, and $$\Delta W_i$$ (this is exactly where the *left-endpoint*, non-anticipating property of the Itô convention is used), so by independence and $$\langle\Delta W_j\rangle = 0$$,

$$\langle f_if_j\Delta W_i\Delta W_j\rangle = \langle f_if_j\Delta W_i\rangle\langle\Delta W_j\rangle = 0$$

Every cross term vanishes. For the diagonal terms, $$f_i$$ is independent of $$\Delta W_i$$ by the same argument, so $$\langle f_i^2(\Delta W_i)^2\rangle = \langle f_i^2\rangle\Delta t$$. Summing and taking $$n\to\infty$$ gives (3.11).

The isometry is the workhorse for computing variances of stochastic integrals and is used in §4.1, §8.6, and §10.4.

> **N.B.* Let's just repeat what $$\Delta W$$ here means, just to remind ourselves. So, $$\Delta W_j$$ is the total accumulated random "kick" (the Wiener increment) that is applied to the system over that specific, tiny time interval from $$t_j$$ to $$t_j + \Delta t$$. Mathematically, it is the change in the Wiener process over that window: $$\Delta W_j = W(t_j + \Delta t) - W(t_j)$$.

## 3.6 Summary of Part III

| Object | Definition / rule |
|---|---|
| Wiener increment | $$\Delta W\sim\mathcal N(0,\Delta t)$$, sampled as $$\sqrt{\Delta t}\,z$$ |
| Scaling | $$dW\sim\sqrt{dt}$$ -- not $$dt$$ |
| Quadratic variation | $$(dW)^2 = dt$$; $$dW\,dt = 0$$; $$(dt)^2 = 0$$ |
| Itô integral | left-endpoint sum; non-anticipating; $$\langle\int f\,dW\rangle = 0$$ |
| Itô's lemma | $$d\phi = [\partial_t\phi + a\partial_x\phi + \tfrac12 b^2\partial_x^2\phi]dt + b\partial_x\phi\,dW$$ |
| Itô isometry | $$\langle(\int f\,dW)^2\rangle = \int\langle f^2\rangle ds$$ |

<br>

(to be continued...)