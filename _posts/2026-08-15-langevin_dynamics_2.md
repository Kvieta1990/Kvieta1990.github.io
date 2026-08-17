---
layout: post
title: Langevin Dynamics -- From Molecular Thermostats to Diffusion Models (Part-2)
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

> This post is part-2 of the 4-parts full story about the Langevin dynamics. All references will be presented in part-4.

# Part IV -- The Fluctuation–Dissipation Relation

The task of this part is to determine the relationship between the two coefficients $$\gamma$$ and $$\sigma$$ left undetermined in (2.1). Two logically distinct routes are given.

## 4.1 Route 1, step 1: exact solution of the free Langevin equation

Set $$U = 0$$ in (3.3), leaving the momentum equation alone:

$$dp = -\gamma p\,dt + \sigma\,dW_t \tag{4.1}$$

This is the **Ornstein–Uhlenbeck (OU) process** [3]. To solve $$p$$ via integration, it needs a bit of work due to the involvement of the random process (the Wiener process corresponding to the second term). First, we want to check the product rule for two variables $$X$$ and $$Y$$ both of which are a function of time. First, for the standard calculus,

$$
d(XY) = XdY + YdX
$$

But for stochastic calculus, it is not like that. Instead,

$$
d(XY) = XdY + YdX + dXdY
$$

where the cross term cannot be crossed out -- $$X$$ and $$Y$$ are noisy and they contain a Wiener process ($$dW$$). Multiplying them together means the $$dXdY$$ term will contain $$(dW)^2$$, which, according to the earlier discussion (Section 3.2), yields $$dt$$ and therefore cannot be crossed out. Derivation of the form given above requires The Taylor expansion as below,

$$
\begin{align}
d(XY) & = \frac{\partial XY}{\partial X}dX + \frac{\partial XY}{\partial Y}dY + \frac{1}{2}\frac{\partial^2 XY}{\partial X^2}(dX)^2 + \frac{1}{2}\frac{\partial^2 XY}{\partial Y^2}(dY)^2 + \frac{\partial^2 XY}{\partial X \partial Y}(dXdY) + \cdots\\
& = YdX + XdY + \frac{1}{2}(0)(dX)^2 + \frac{1}{2}(0)(dY)^2 + 1(dXdY)\\
& = XdY + YdX + dXdY
\end{align}
$$

Let $$X = e^{\gamma t}$$ and $$Y = p_t$$, we have,

$$
d(e^{\gamma t}p) = e^{\gamma t}dp + \gamma e^{\gamma t}pdt + (\gamma e^{\gamma t} dt) \times (-\gamma p dt + \sigma dW_t)
$$

For the cross term, we have $$(dt)^2$$ and $$dtdW_t$$, both procuding 0 (remember that only $$(dW_t)^2 = dt$$ term is non-zero), and therefore the cross term just vanishes. Now back to Eqn. (4.1), and we want to multiply both sides with $$e^{\gamma t}$$ to have,

$$
e^{\gamma t}dp = -\gamma p e^{\gamma t}dt + \sigma e^{\gamma t}dW_t \Rightarrow e^{\gamma t}dp + \gamma p e^{\gamma t}dt = \sigma e^{\gamma t}dW_t
$$

whwere we can see that the left side in the final expression is just $$d(e^{\gamma t}p)$$, and therefore,

$$
d\!\left(e^{\gamma t}p\right) = \sigma e^{\gamma t}dW_t
$$

Integrate from $$0$$ to $$t$$ and rearrange:

$$p_t = p_0e^{-\gamma t} + \sigma\int_0^t e^{-\gamma(t-s)}dW_s \tag{4.2}$$

The structure is transparent. The first term is the deterministic decay of the initial momentum -- friction acting alone. The second is a superposition of all past noise kicks, each weighted by $$e^{-\gamma(t-s)}$$: a kick delivered at time $$s$$ has been damped for a duration $$t-s$$ by the time it is observed. The friction rate $$\gamma$$ thus does double duty as the *memory time* $$1/\gamma$$ over which past kicks still matter.

**Mean.** Since $$\langle dW_s\rangle = 0$$,

$$\langle p_t\rangle = p_0e^{-\gamma t} \;\longrightarrow\; 0 \tag{4.3}$$

**Variance.** Apply the Itô isometry (3.11) to the stochastic integral with $$f(s) = \sigma e^{-\gamma(t-s)}$$:

$$\text{Var}(p_t) = \sigma^2\int_0^t e^{-2\gamma(t-s)}ds = \sigma^2 e^{-2\gamma t}\int_0^t e^{2\gamma s}ds = \sigma^2e^{-2\gamma t}\cdot\frac{e^{2\gamma t}-1}{2\gamma}$$

$$\text{Var}(p_t) = \frac{\sigma^2}{2\gamma}\left(1 - e^{-2\gamma t}\right) \tag{4.4}$$

This single formula tells the whole story of thermalization from a sharp initial condition:

- $$t=0$$: variance zero -- the initial momentum is definite.
- $$t\lesssim 1/\gamma$$: variance grows approximately linearly, $$\text{Var}\approx\sigma^2 t$$ -- free diffusion in momentum space, friction not yet felt.
- $$t\gg 1/\gamma$$: variance saturates. The timescale of saturation is $$\tau_p = 1/(2\gamma)$$, the **momentum relaxation time**.

Taking $$t\to\infty$$ (and noting $$\langle p\rangle\to0$$, so variance and second moment coincide -- generally for a random variable, we have $$\text{Var}(p) = \langle p^2 \rangle - \langle p \rangle^2$$):

$$\langle p^2\rangle_{\text{ss}} = \frac{\sigma^2}{2\gamma} \tag{4.5}$$

This reproduces the one-line Itô's-lemma result (3.10) and confirms it.

**Stationary distribution ($$t \rightarrow \infty$$).** Equation (4.2) expresses $$p_t$$ as a constant plus a linear functional of Gaussian increments; any such object is Gaussian. Therefore the full stationary distribution -- not merely its second moment -- is

$$P_{\text{ss}}(p) = \frac{1}{\sqrt{2\pi\sigma^2/2\gamma}}\exp\left(-\frac{p^2}{2\sigma^2/2\gamma}\right) = \mathcal N\!\left(0,\;\frac{\sigma^2}{2\gamma}\right) \tag{4.6}$$

## 4.2 Route 1, step 2: imposing equipartition

The physical input now enters. The **equipartition theorem** states that in thermal equilibrium at temperature $$T$$, each quadratic degree of freedom in the Hamiltonian carries mean energy $$\tfrac12 k_BT$$. For the kinetic term $$p^2/2m$$:

$$\left\langle\frac{p^2}{2m}\right\rangle = \frac{1}{2}k_BT \quad\Longrightarrow\quad \langle p^2\rangle = mk_BT \tag{4.7}$$

Demanding that the stationary state (4.5) *be* this thermal state,

$$\frac{\sigma^2}{2\gamma} = mk_BT$$

$$\sigma^2 = 2\gamma m k_BT \qquad\Longleftrightarrow\qquad \sigma = \sqrt{2\gamma mk_BT} = \sqrt{2\zeta k_BT} \tag{4.8}$$

This is the **fluctuation–dissipation (FD) relation** in the Markovian, memoryless case [10]. Its content in words: the strength of the fluctuating force is not free, but is fixed by the dissipation rate multiplied by the thermal energy scale.

Equivalently, in terms of the momentum-space diffusion coefficient $$D_p \equiv \sigma^2/2$$:

$$D_p = \gamma mk_BT = \zeta k_BT \tag{4.9}$$

**Physical reading.** Friction removes energy at a rate proportional to $$\gamma$$; noise injects it at a rate proportional to $$\sigma^2$$. Equation (4.8) is the unique balance point:

| If $$\sigma^2$$ chosen... | Consequence |
|---|---|
| $$< 2\gamma mk_BT$$ | dissipation dominates; system cools below $$T$$, collapses toward $$U$$'s minimum |
| $$= 2\gamma mk_BT$$ | exact balance; stationary state is Boltzmann at temperature $$T$$ |
| $$> 2\gamma mk_BT$$ | injection dominates; system runs hotter than $$T$$ |

Note also what (4.8) does *not* say: it does not constrain $$\gamma$$ itself. Different $$\gamma$$ (with $$\sigma$$ adjusted accordingly) give the *same* equilibrium distribution but different dynamics -- faster or slower relaxation. This is why $$\gamma$$ is a free tuning parameter in a molecular-dynamics thermostat: it controls how strongly the system is coupled to the bath, not what state it equilibrates to.

## 4.3 Why Route 1 is a construction, not a derivation

The argument just given assumed equipartition -- a statement about the equilibrium state -- in order to *derive* the coefficient that produces that equilibrium state. As a proof that Langevin dynamics thermalizes, this is circular.

That does not make it useless. Read correctly, Route 1 is a **construction**: given the desire to build a stochastic thermostat that reproduces the canonical ensemble, (4.8) is the required tuning. This is precisely the logic used to justify Langevin thermostats in molecular-dynamics software, and it is legitimate engineering. But it leaves two questions open:

1. Is the FD relation *forced* by anything more fundamental, or is it merely a convenient choice?
2. Does the dynamics actually *converge* to the Boltzmann distribution, or is Boltzmann merely one stationary solution among possibly many?

Route 2 answers the first. Part VI answers the second.

## 4.4 Route 2: the Caldeira–Leggett microscopic derivation

The strategy is to stop treating $$\gamma$$ and $$\sigma$$ as phenomenological knobs, and instead derive *both* from an explicit bath model. The thermal assumption is then imposed on the **bath**, not on the system -- a far weaker and physically better-motivated hypothesis, since the bath is macroscopic and prepared in contact with a reservoir long before the system is coupled to it.

**The model.** Take the system coordinate $$x$$ coupled bilinearly to $$N$$ harmonic oscillators $$\{q_j\}$$ [8,9]:

$$H = \frac{p^2}{2m} + U(x) + \sum_j\left[\frac{p_j^2}{2m_j} + \frac{m_j\omega_j^2}{2}\left(q_j - \frac{c_jx}{m_j\omega_j^2}\right)^{\!2}\right] \tag{4.10}$$

The shifted form of the bath potential -- expanding the square produces a term $$+\tfrac{c_j^2x^2}{2m_j\omega_j^2}$$ -- is a deliberate **counter-term** ensuring the coupling does not renormalize $$U(x)$$ itself; without it, the bath would silently modify the system's potential. Here below is presented the derivation details.

First, expanding the bracketed potential term in the bath coupling,

$$
\frac{m_j\omega_j^2}{2}\left(q_j - \frac{c_jx}{m_j\omega_j^2}\right)^{\!2} = \frac{m_j\omega_j^2}{2}q_j^2 - c_jq_jx + \frac{c_j^2x^2}{2m_j\omega_j^2}
$$

The second term on the right side is the coupling term and the last term is the counter term. Without the counter term, the coupling bath energy would be,

$$E = \frac{1}{2} m_j \omega_j^2 q_j^2 - c_j x q_j$$

Because this oscillator is a physical spring, it will naturally try to minimize its energy. If the particle is sitting at some position $$x$$, the spring will stretch or compress to find a new resting point. To find this new resting point, we use standard calculus: we take the derivative of the energy with respect to the spring's position ($$q_j$$) and set it to zero to find the minimum,

$$\frac{dE}{dq_j} = m_j \omega_j^2 q_j - c_j x = 0$$

Solving for $$q_j$$, we find the new equilibrium position of the spring:

$$
q_j^* = \frac{c_j x}{m_j \omega_j^2}
$$

Notice that the spring's new resting position is directly proportional to where the particle is ($$x$$). The spring is essentially "following" the particle. Now, what happens to the total energy of the system when the spring relaxes to this new position? We plug $$q_j^*$$ back into our original energy equation,

$$
\begin{align}
E_{\text{relaxed}} & = \frac{1}{2} m_j \omega_j^2 \left( \frac{c_j x}{m_j \omega_j^2} \right)^2 - c_j x \left( \frac{c_j x}{m_j \omega_j^2} \right)\\
& = \frac{c_j^2 x^2}{2 m_j \omega_j^2} - \frac{c_j^2 x^2}{m_j \omega_j^2}\\
& = -\frac{c_j^2}{2 m_j \omega_j^2} x^2
\end{align}
$$

By simply allowing the fluid (the springs) to naturally react to the particle and relax to their lowest energy state, the total energy of the universe drops by an amount proportional to $$-x^2$$. Because this energy drop depends only on the particle's position $$x$$, it acts exactly like a negative physical potential applied directly to the particle. If the we did not add the positive counter-term ($$+\frac{c_j^2 x^2}{2m_j\omega_j^2}$$ as the result of expanding the bracketed term in the bath coupling Hamiltonian) to the Hamiltonian to perfectly cancel this out, this relaxation effect would permanently warp the particle's physical landscape.

**Equations of motion.** From (4.10),

> Applying the Hamiltonian's equation $$\dot p = -\partial H/\partial q \Rightarrow m\ddot{q} = -\partial H / \partial q$$ for the bath oscillator and the system particle, respectively.

$$m_j\ddot q_j = -m_j\omega_j^2q_j + c_jx \tag{4.11}$$

$$m\ddot x = -U'(x) + \sum_j c_j\left(q_j - \frac{c_jx}{m_j\omega_j^2}\right) \tag{4.12}$$

**Solve the bath exactly.** Equation (4.11) is a driven harmonic oscillator, linear, so it admits the exact solution

$$q_j(t) = q_j(0)\cos\omega_jt + \frac{p_j(0)}{m_j\omega_j}\sin\omega_jt + \frac{c_j}{m_j\omega_j}\int_0^t\sin\!\left[\omega_j(t-s)\right]x(s)\,ds \tag{4.13}$$

(homogeneous solution from the initial conditions, plus the particular solution from the drive $$c_jx$$).

**Integrate the driven term by parts.** With $$u=x(s)$$ and $$dv = \sin[\omega_j(t-s)]ds$$, so $$v = \omega_j^{-1}\cos[\omega_j(t-s)]$$:

$$\frac{c_j}{m_j\omega_j}\int_0^t\sin[\omega_j(t-s)]x(s)ds = \frac{c_j}{m_j\omega_j^2}\left\{x(t) - x(0)\cos\omega_jt - \int_0^t\cos[\omega_j(t-s)]\,\dot x(s)\,ds\right\}$$

**Substitute back into (4.12).** The term $$\frac{c_j^2x(t)}{m_j\omega_j^2}$$ generated here cancels the counter-term exactly -- which is what the counter-term was for. What survives is the **generalized Langevin equation**:

$$m\ddot x = -U'(x) - \int_0^t \Gamma(t-s)\,\dot x(s)\,ds + \eta(t) \tag{4.14}$$

with the two derived objects

$$\Gamma(t) = \sum_j\frac{c_j^2}{m_j\omega_j^2}\cos\omega_jt \tag{4.15}$$

$$\eta(t) = \sum_j c_j\left[\tilde q_j(0)\cos\omega_jt + \frac{p_j(0)}{m_j\omega_j}\sin\omega_jt\right], \qquad \tilde q_j(0)\equiv q_j(0) - \frac{c_jx(0)}{m_j\omega_j^2} \tag{4.16}$$

Three observations, all essential:

- The friction is now a **memory kernel** $$\Gamma(t)$$, not an instantaneous coefficient. Nothing was assumed about memorylessness; it must be derived (§4.5).
- The "random" force $$\eta(t)$$ is a completely *deterministic* function of the bath's initial conditions. Randomness enters only because those initial conditions are not known and are distributed thermally. This is where stochasticity genuinely originates.
- **$$\Gamma$$ and $$\eta$$ are built from the same coupling constants $$c_j$$.** They cannot be independent.

**Impose thermal equilibrium -- on the bath only.** At $$t=0$$, take the bath oscillators to be **thermally distributed in their shifted coordinates** (see the discussion below), uncorrelated with each other:

$$\langle\tilde q_j(0)^2\rangle = \frac{k_BT}{m_j\omega_j^2}, \qquad \langle p_j(0)^2\rangle = m_jk_BT, \qquad \langle\tilde q_j(0)p_k(0)\rangle = 0 \tag{4.17}$$

These are equipartition applied to the bath's *own* Hamiltonian -- nothing is assumed about the system's stationary state. Here below is presented some details abut some of the statements here in the context.

**"Thermally distributed in their shifted coordinates"**

The bath is modeled as a bunch of springs (oscillators). At time $t=0$, we need to know where these springs are and how fast they are moving.

* **Shifted coordinates ($$\tilde{q}_j$$):** If we drop a particle into a fluid, the fluid molecules naturally cluster and bump around *where the particle actually is*, not around some arbitrary zero point in the room. The shifted coordinate ($$\tilde{q}_j = q_j - \frac{c_j x}{m_j\omega_j^2}$$) just means we are measuring the stretch of the bath's springs relative to the particle's starting position.

* **Thermally distributed:** We don't know the exact position or momentum of any single fluid molecule at $$t=0$$. But because they are at a temperature $$T$$, we know their *statistical averages* follow the **Equipartition Theorem**. This theorem states that every quadratic degree of freedom gets exactly $$\frac{1}{2}k_BT$$ of thermal energy.

**Breaking down Equation (4.17)**

Equation (4.17) takes that Equipartition Theorem and writes it out mathematically for the initial state of the bath. It has three parts,

**Part 1: Potential Energy (Position)**

$$
\langle \tilde{q}_j(0)^2 \rangle = \frac{k_B T}{m_j\omega_j^2}
$$

For a spring, potential energy is $$\frac{1}{2} k x^2$$. Here, the spring constant is $$k = m_j\omega_j^2$$. If we set the average potential energy equal to the thermal energy ($$\frac{1}{2} m_j\omega_j^2 \langle \tilde{q}^2 \rangle = \frac{1}{2} k_B T$$) and solve for the variance of the position ($$\langle \tilde{q}^2 \rangle$$), we get exactly this first term. It tells us how widely the oscillators are stretched by thermal jitter.

**Part 2: Kinetic Energy (Momentum)**

$$
\langle p_j(0)^2 \rangle = m_j k_B T
$$

For a moving mass, kinetic energy is $$\frac{p^2}{2m}$$. If we set the average kinetic energy equal to the thermal energy ($$\frac{\langle p^2 \rangle}{2m_j} = \frac{1}{2} k_B T$$) and solve for the variance of the momentum ($$\langle p^2 \rangle$$), we get this second term. It tells us how violently the oscillators are vibrating due to the temperature.

**Part 3: Uncorrelated States**

$$
\langle \tilde{q}_j(0) p_k(0) \rangle = 0
$$

This translates the phrase "uncorrelated with each other". It states two things mathematically,

- A single oscillator's initial position has no relation to its initial momentum. (Knowing a spring is stretched doesn't tell us if it's currently moving left or right).

- Oscillator $$j$$ has no idea what oscillator $$k$$ is doing. They are independent, random variables.

**Compute the noise autocorrelation.** Using (4.16) and (4.17), cross terms between different oscillators vanish, leaving

$$\langle\eta(t)\eta(t')\rangle = \sum_j c_j^2\left[\frac{k_BT}{m_j\omega_j^2}\cos\omega_jt\cos\omega_jt' + \frac{m_jk_BT}{m_j^2\omega_j^2}\sin\omega_jt\sin\omega_jt'\right]$$

Both bracketed terms carry the identical prefactor $$k_BT/(m_j\omega_j^2)$$, so they combine, and the trigonometric identity $$\cos A\cos B + \sin A\sin B = \cos(A-B)$$ collapses the bracket:

$$\langle\eta(t)\eta(t')\rangle = k_BT\sum_j\frac{c_j^2}{m_j\omega_j^2}\cos\!\left[\omega_j(t-t')\right]$$

Comparing to (4.15):

$$\langle\eta(t)\eta(t')\rangle = k_BT\,\Gamma(t-t') \tag{4.18}$$

**This is the fluctuation–dissipation theorem**, in its general (memory-carrying) form [8,10]. It was not imposed; it emerged, because friction and noise are two projections of a single underlying object -- the bath's coupling spectrum -- and the bath's thermal preparation ties them together.

## 4.5 The Ohmic / Markovian limit

To recover (2.1) from (4.14), the memory kernel must collapse to a delta function. Introduce the **bath spectral density**, which packages all the coupling information into a single function,

$$J(\omega) \equiv \frac{\pi}{2}\sum_j\frac{c_j^2}{m_j\omega_j}\delta(\omega-\omega_j) \tag{4.19}$$

so that (4.15) becomes

$$\Gamma(t) = \frac{2}{\pi}\int_0^\infty\frac{J(\omega)}{\omega}\cos\omega t\,d\omega$$

A bath is called **Ohmic** when $$J(\omega) = \zeta\omega$$ over the relevant frequency range (up to a high-frequency cutoff). Then

$$\Gamma(t) = \frac{2\zeta}{\pi}\int_0^\infty\cos\omega t\,d\omega = 2\zeta\,\delta(t)$$

and the friction integral in (4.14) becomes (the factor $$\tfrac12$$ again from the delta sitting at the endpoint of the range)

$$\int_0^t 2\zeta\,\delta(t-s)\dot x(s)\,ds = \zeta\dot x(t)$$

recovering instantaneous friction. Simultaneously, (4.18) gives

$$\langle\eta(t)\eta(t')\rangle = 2\zeta k_BT\,\delta(t-t')$$

Matching against $$\eta = \sigma\xi$$ with $$\langle\xi\xi'\rangle = \delta(t-t')$$ gives $$\sigma^2 = 2\zeta k_BT = 2\gamma mk_BT$$ -- **exactly (4.8)**, now derived rather than reverse-engineered.

<br>

> **N.B.** $$\langle \eta\eta' \rangle = \sigma^2\langle \xi\xi' \rangle = \sigma^2\delta(t - t') = 2\zeta k_BT\delta(t - t') = 2\gamma mk_BT$$

> Regarding the choice of the scale factor $$\zeta$$ or $$\gamma$$, refer to the 'Convention warning 1' in [part-1](../2026-08-13-langevin_dynamics_1).

> **How do we transition from the summation of delta functions into something like the assumption of $$J(\omega) = \zeta\omega$$?** Eqn. (4.19) gives a lot of spikes along the $$\omega$$-axis corresponding to each individual oscillator $$j$$ in the heat bath. In practice, the number of oscillators is huge and accordingly the spikes are very much crowded so to be nearly forming a continuous function. Once $$J(\omega)$$ is established as a continuous, smooth curve, we still have to figure out its actual shape. The true shape depends on the exact quantum or classical microscopic details of the specific material (water vs. argon vs. honey). However, in physics, if a function is smooth and starts at zero (since there are no oscillators with exactly zero frequency), we can approximate it at low frequencies using a Taylor expansion. The first term of a Taylor expansion is just a straight line. The Ohmic assumption is the phenomenological choice to simply model that continuous curve as the simplest possible straight line over all the low frequencies that matter to the particle: $$J(\omega) = \zeta\omega$$. Here, $$\zeta$$ (the macroscopic drag coefficient) is just the slope of that line.

The Ohmic assumption is the precise statement of "the bath is fast": a flat $$J(\omega)/\omega$$ means all bath modes respond equally, so the bath has no characteristic memory time of its own.

# Part V -- The Fokker–Planck Equation

## 5.1 What it is, and why it is needed

An SDE describes a single realization of a stochastic process -- one noisy trajectory. Running it twice with the same initial condition gives different answers. What is usually wanted instead is the *distribution*: given $$\rho(x,0)$$, what is $$\rho(x,t)$$?

The **Fokker–Planck equation** (also Kolmogorov forward equation; in the phase-space case, the **Kramers equation** [5]) is the deterministic partial differential equation governing that distribution. It converts a stochastic problem into a deterministic one, at the cost of working in the space of densities rather than trajectories. All equilibrium statements -- that the stationary distribution is Boltzmann, that the dynamics converges to it -- are naturally posed at this level.

## 5.2 Derivation from the SDE

**Setup.** Let $$x_t$$ obey the Itô SDE (3.8), $$dx = a(x,t)dt + b(x,t)dW_t$$, with density $$\rho(x,t)$$. Let $$\phi(x)$$ be an arbitrary smooth **test function** that vanishes (with its derivatives, i.e., the function, its first derivative and all higher order derivatives, all vanish) outside a bounded region.

**Step 1 -- evolve the observable.** By Itô's lemma (3.9) with $$\partial_t\phi = 0$$ (i.e., $$\phi$$ is not a function of $$t$$):

$$d\phi(x_t) = \left[a\phi'(x_t) + \frac{b^2}{2}\phi''(x_t)\right]dt + b\phi'(x_t)\,dW_t$$

**Step 2 -- average.** The stochastic integral (the second term) has zero mean (§3.3), so

$$\frac{d}{dt}\langle\phi(x_t)\rangle = \left\langle a\phi'(x_t) + \frac{b^2}{2}\phi''(x_t)\right\rangle$$

**Step 3 -- write both sides as integrals against $$\rho$$.** By definition $$\langle\phi(x_t)\rangle = \int\phi(x)\rho(x,t)dx$$, so

$$\int\phi(x)\,\frac{\partial\rho}{\partial t}\,dx = \int\rho(x,t)\left[a(x,t)\phi'(x) + \frac{b(x,t)^2}{2}\phi''(x)\right]dx$$

**Step 4 -- move derivatives off the test function.** Integrate the first term by parts once and the second twice. Boundary terms vanish because $$\phi$$ has bounded support:

$$\int\rho\,a\,\phi'\,dx = -\int\phi\,\frac{\partial}{\partial x}(a\rho)\,dx, \qquad \int\rho\,\frac{b^2}{2}\phi''\,dx = +\int\phi\,\frac{\partial^2}{\partial x^2}\!\left(\frac{b^2}{2}\rho\right)dx$$

**Step 5 -- strip the test function.** Collecting everything on one side:

$$\int\phi(x)\left[\frac{\partial\rho}{\partial t} + \frac{\partial}{\partial x}(a\rho) - \frac{\partial^2}{\partial x^2}\!\left(\frac{b^2}{2}\rho\right)\right]dx = 0$$

Since $$\phi$$ is arbitrary, the bracket must vanish pointwise:

$$\frac{\partial\rho}{\partial t} = -\frac{\partial}{\partial x}\Big[a(x,t)\rho\Big] + \frac{\partial^2}{\partial x^2}\!\left[\frac{b(x,t)^2}{2}\rho\right] \tag{5.1}$$

This is the Fokker–Planck equation.

**Reading the two terms.** The first is **drift**: a first-order transport term that translates probability along the deterministic flow $$a$$. The second is **diffusion**: a second-order term that spreads probability out. Note where the diffusion term came from -- the Itô correction $$\tfrac12 b^2\phi''$$ in Itô's lemma. Without the identity $$(dW)^2 = dt$$, the Fokker–Planck equation would have no diffusion term at all and would collapse to the Liouville equation (1.3).

**Continuity form.** Equation (5.1) can always be written as $$\partial_t\rho = -\partial_x J$$ with the **probability current**

$$J(x,t) = a\rho - \frac{\partial}{\partial x}\left(\frac{b^2}{2}\rho\right) \tag{5.2}$$

making conservation of total probability manifest: $$\frac{d}{dt}\int\rho\,dx = -\int\partial_xJ\,dx = 0$$.

> Here we are integrating both sides of the equation $$\partial_t\rho = -\partial_x J$$ and for the right-hand side, $$-\int\partial_xJ\,dx = \left. J \right\vert _{-\infty}^{\infty}$$ as the probability of particles flowing at the end of the universe is essentially $$0$$.

## 5.3 The Kramers equation for phase space

Apply (5.1) to the two-variable Langevin system (3.3). The drift vector is $$\mathbf a = (p/m,\; -U'(x)-\gamma p)$$, and noise enters only the $$p$$ component with amplitude $$\sigma$$. The multi-dimensional form of (5.1) gives,

$$\frac{\partial\rho}{\partial t} = -\frac{\partial}{\partial x}\left(\frac{p}{m}\rho\right) - \frac{\partial}{\partial p}\Big[\big(-U'(x)-\gamma p\big)\rho\Big] + \frac{\sigma^2}{2}\frac{\partial^2\rho}{\partial p^2}$$

---

Here, for better understanding the formulation, it is worth mentioning how the multi-dimensional Fokker-Planck equation really works. In multiple dimensions, our system of equations is written as vectors and matrices. Let $$\mathbf{x}$$ be the state vector (e.g., position $$x_1$$ and $$x_2$$, etc. or position $$x$$ and momentum $$p$$ in the phase space). The Langevin equation looks like this,

$$d\mathbf{x} = \mathbf{A}(\mathbf{x}, t) dt + \mathbf{B}(\mathbf{x}, t) d\mathbf{W}$$

- $$\mathbf{A}$$ (The Drift Vector): This contains the deterministic parts of each equation.

- $$\mathbf{B}$$ (The Noise Matrix): This replaces the scalar $$b$$. It dictates how much each independent random noise source ($$dW_1, dW_2$$, etc.) affects each variable.

- $$d\mathbf{W}$$ (The Noise Vector): A vector of independent, standard Wiener processes.

In 1D, the diffusion term in the Fokker-Planck equation is driven by $$b^2$$. In multiple dimensions, we cannot simply square a vector. Instead, we multiply the noise matrix $$\mathbf{B}$$ by its transpose, $$\mathbf{B}^T$$, to create the Diffusion Tensor, which we call $$\mathbf{D}$$,

$$\mathbf{D} = \mathbf{B}\mathbf{B}^T$$

The elements of this matrix, $$D_{ij}$$, tell us the variance and the cross-correlation of the noise between equation $$i$$ and equation $$j$$. Because $$\mathbf{D}$$ is a matrix, the second-derivative term in Equation 5.1 expands into a double summation over all the variables, creating both pure and mixed partial derivatives. The full multidimensional Fokker-Planck equation is,

$$\partial_t \rho = - \sum_{i} \frac{\partial}{\partial x_i} [A_i \rho] + \frac{1}{2} \sum_{i} \sum_{j} \frac{\partial^2}{\partial x_i \partial x_j} [D_{ij} \rho]$$

---

Since $$x$$ and $$p$$ are independent variables, $$\partial_x(p\rho/m) = (p/m)\partial_x\rho$$. Writing $$D_p = \sigma^2/2$$:

$$
\frac{\partial\rho}{\partial t} = -\frac{p}{m}\frac{\partial\rho}{\partial x} + U'(x)\frac{\partial\rho}{\partial p} + \gamma\frac{\partial}{\partial p}(p\rho) + D_p\frac{\partial^2\rho}{\partial p^2} \tag{5.3}
$$

This is the **Kramers equation** [5]. Its structure is worth pausing on, because Part VI exploits it:

$$\underbrace{-\frac{p}{m}\partial_x\rho + U'\partial_p\rho}_{\text{= }\{H,\rho\}\text{, reversible}} \;+\; \underbrace{\gamma\partial_p(p\rho) + D_p\partial_p^2\rho}_{\text{irreversible}}$$

The first group is *exactly* the Liouville operator (1.3) -- it is what remains when $$\gamma = \sigma = 0$$. It is time-reversible and conserves energy. The second group contains every term carrying a $$\gamma$$ or a $$D_p$$, i.e. everything due to bath coupling. It is irreversible and is the only part that can change the energy distribution.

## 5.4 Alternative derivation: the Kramers–Moyal expansion

A second route, useful for seeing why *only* first and second derivatives appear, starts from the **Chapman–Kolmogorov equation** for a Markov process,

$$\rho(x,t+\tau) = \int P(x\,|\,x',\tau)\,\rho(x',t)\,dx'$$

and Taylor-expands the transition kernel in the jump size. The result is the **Kramers–Moyal expansion** [6],

$$\frac{\partial\rho}{\partial t} = \sum_{n=1}^\infty\frac{(-1)^n}{n!}\frac{\partial^n}{\partial x^n}\left[M_n(x)\,\rho\right], \qquad M_n(x) \equiv \lim_{\tau\to0}\frac{1}{\tau}\big\langle(\Delta x)^n\big\rangle$$

where $$M_n$$ is the $$n$$-th **jump moment**. Here below is presented the detailed derivation.

### Step 1: Rewrite Chapman-Kolmogorov using Jump Size

The Chapman-Kolmogorov equation dictates that the probability of a particle being at position $$x$$ at a future time $$t+\tau$$ is the integral over all possible current positions $$x'$$ at present time $$t$$, multiplied by the transition probability of jumping from $$x'$$ to $$x$$ during the time interval $$\tau$$,

$$\rho(x, t+\tau) = \int P(x\vert{}x', \tau) \rho(x', t) dx'$$

The jump size is defined as $$y = x - x'$$. If a particle arrives at destination $$x$$ after a jump of size $$y$$, its starting position must be $$x' = x - y$$. The transition probability function $$P(x\vert{}x', \tau)$$ can be rewritten as a function of the starting position and the jump size, denoted as $$W(x-y; y, \tau)$$.

Substituting these definitions and changing the integration variable to the jump size $$y$$ yields,

$$\rho(x, t+\tau) = \int W(x-y; y, \tau) \rho(x-y, t) dy$$

### Step 2: The Taylor Expansion

Inside the integral, both the transition probability $$W$$ and the probability density $$\rho$$ are evaluated at the present starting position $$x-y$$. To express this entirely in terms of the destination position $$x$$, the combined term $$[W \cdot \rho]$$ is treated as a single function and expanded using a Taylor series around the point $$x$$.

For any smooth function $$F(x-y)$$, the expansion around $$x$$ is,

$$F(x-y) = \sum_{n=0}^{\infty} \frac{(-y)^n}{n!} \frac{\partial^n}{\partial x^n} F(x)$$

Applying this expansion to the integrand produces,

$$W(x-y; y, \tau) \rho(x-y, t) = \sum_{n=0}^{\infty} \frac{(-1)^n}{n!} y^n \frac{\partial^n}{\partial x^n} [W(x; y, \tau) \rho(x, t)]$$

### Step 3: Swap the Sum and the Integral

Substituting the infinite series back into the original integral results in,

$$\rho(x, t+\tau) = \int \left( \sum_{n=0}^{\infty} \frac{(-1)^n}{n!} y^n \frac{\partial^n}{\partial x^n} [W(x; y, \tau) \rho(x, t)] \right) dy$$

Because the integration is performed exclusively over the jump size $$y$$, and the derivatives are taken exclusively with respect to the spatial position $$x$$, the summation and the spatial derivatives can be factored outside the integral operator,

$$\rho(x, t+\tau) = \sum_{n=0}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} \left[ \left( \int y^n W(x; y, \tau) dy \right) \rho(x, t) \right]$$

### Step 4: Isolate the $$n=0$$ Term

The first term of this infinite series corresponds to $$n=0$$. For this specific term, the coefficient $$(-1)^0 / 0! = 1$$, the zeroth derivative acts as an identity operator, and $$y^0 = 1$$. The term simplifies to,

$$\left( \int W(x; y, \tau) dy \right) \rho(x, t)$$

The integral of the transition probability over all possible jump sizes $$y$$ must equal exactly $$1$$, as the particle is guaranteed to transition to some location (including a jump of size zero). Thus, the entire $$n=0$$ term reduces strictly to $$\rho(x, t)$$.

Extracting this initial term from the summation leaves,

$$\rho(x, t+\tau) = \rho(x, t) + \sum_{n=1}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} \left[ \left( \int y^n W(x; y, \tau) dy \right) \rho(x, t) \right]$$

### Step 5: Form the Time Derivative

Subtracting the present state $$\rho(x, t)$$ from both sides yields the change in probability density,

$$\rho(x, t+\tau) - \rho(x, t) = \sum_{n=1}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} \left[ \left( \int y^n W(x; y, \tau) dy \right) \rho(x, t) \right]$$

Dividing both sides by the time increment $$\tau$$ gives,

$$\frac{\rho(x, t+\tau) - \rho(x, t)}{\tau} = \sum_{n=1}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} \left[ \left( \frac{1}{\tau} \int y^n W(x; y, \tau) dy \right) \rho(x, t) \right]$$

Taking the continuous-time limit as $$\tau \to 0$$ transforms the left side into the formal definition of the partial time derivative, $$\frac{\partial \rho}{\partial t}$$.

### Step 6: Define the Jump Moments

Evaluating the term remaining inside the parenthesis on the right side under the limit $$\tau \to 0$$,

$$\lim_{\tau \to 0} \frac{1}{\tau} \int y^n W(x; y, \tau) dy$$

The integral $$\int y^n W dy$$ calculates the statistical expected value of the $$n$$-th power of the jump size, denoted as $$\langle (\Delta x)^n \rangle$$. Therefore, this limit perfectly matches the definition of the $$n$$-th jump moment, $$M_n(x)$$ as given above,

$$M_n(x) \equiv \lim_{\tau \to 0} \frac{1}{\tau} \langle (\Delta x)^n \rangle$$

Substituting $$M_n(x)$$ into the time-derivative equation yields the final Kramers-Moyal expansion,

$$\frac{\partial \rho}{\partial t} = \sum_{n=1}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} [M_n(x) \rho]$$

For a process driven by Gaussian white noise, the jump moments are computed directly from the SDE: over a short interval, $$\Delta x = a\tau + b\Delta W$$ with $$\Delta W\sim\mathcal N(0,\tau)$$, so

$$M_1 = a, \qquad M_2 = b^2, \qquad M_n = 0 \text{ for } n\ge3$$

Let's first check how each of the moment is calculated. For $$\Delta W \sim \mathcal{N}(0, \tau)$$, the following statistical moments can be calculated,

$$
\begin{align}
\langle \Delta W \rangle & = 0\\
\langle (\Delta W)^2 \rangle & = \tau\\
\langle (\Delta W)^3 \rangle & = 0\\
\langle (\Delta W)^4 \rangle & = 3\tau^2
\end{align}
$$

Given the definition of $$M_n$$, the expression for $$\Delta x$$ for a process driven by Gaussian white noise, we can calculate,

$$
\begin{align}
M_1 & = \lim_{\tau \to 0} \frac{\langle a\tau + b\Delta W \rangle}{\tau} = \lim_{\tau \to 0} \frac{a\tau + \langle b\Delta W \rangle}{\tau} = \lim_{\tau \to 0} \frac{a\tau + 0}{\tau} = a
\end{align}
$$

For $$M_2$$, we have,

$$
\begin{align}
\langle (\Delta x)^2 \rangle & = \langle (a\tau + b\Delta W)^2 \rangle = \langle a^2\tau^2 + 2ab\tau\Delta W + b^2(\Delta W)^2 \rangle\\
& = a^2\tau^2 + 2ab\tau(0) + b^2(\tau) = a^2\tau^2 + b^2\tau\\
& \hspace{4cm} \Downarrow\\
M_2 & = \lim_{\tau \to 0} \frac{a^2\tau^2 + b^2\tau}{\tau} = \lim_{\tau \to 0} (a^2\tau + b^2) = b^2
\end{align}
$$

Following the same approach, we can show that $$M_3 = 0$$, $$M_4 = 0$$ and due to the form that $$\Delta x$$ takes, it can be shown that all higher terms with $$n \geq 3$$ are $$0$$. Therefore, the full Kramers-Moyal expansion turns into,

$$
\begin{align}
\frac{\partial \rho}{\partial t} & = \sum_{n=1}^{\infty} \frac{(-1)^n}{n!} \frac{\partial^n}{\partial x^n} [M_n(x) \rho]\\
& = \frac{(-1)^1}{1!} \frac{\partial^1}{\partial x^1} [M_1 \rho] + \frac{(-1)^2}{2!} \frac{\partial^2}{\partial x^2} [M_2 \rho]\\
& = - \frac{\partial}{\partial x} [a \rho] + \frac{1}{2} \frac{\partial^2}{\partial x^2} [b^2 \rho]\\
& = - \frac{\partial}{\partial x} [a(x,t) \rho] + \frac{1}{2} \frac{\partial^2}{\partial x^2} [b(x,t)^2 \rho]
\end{align}
$$

In generation cases going beyond the Gaussian white noise process, $$\Delta x$$ is not taking the Gaussian form as given above in which case we won't have all those high order terms vanishing nicely to give the truncated version of the Kramers-Moyal expansion. Then we have to work with the full version of the Kramers-Moyal expansion, which will yield some 'jumpy' transition instead of the continuous process like Brownian motion. By 'continuous process', we literally mean the process is continuous -- take the Brownian motion as the example, we do expect all particles in the system are moving continously in space but not jumping from one place to another in a discontinuous manner. A typical discontinuous process is the Poisson process which describes events happening in a certain time window or in a certain region in space, both following the Poisson distribution. The discontinuity of a Poisson process roots in the fact that the underlying Poisson distribution that dominates the process is a dicrete distribution. Here below is presented an example to showcase the relation between the Poisson process and Poisson distribution.

**The Poisson Process (The Jumps)** Imagine we are sitting with a Geiger counter, measuring radioactive decay. The process describes the timeline of the clicks. Time is flowing completely smoothly and continuously. However, our state variable -- the total number of clicks we have measured with the Geiger counter -- does not grow smoothly. It stays at $$0$$, then instantly "jumps" to $$1$$ when a click happens. It stays at $$1$$, then instantly jumps to $$2$$. This is the discontinuity. The state of the system only ever exists as strict, whole integers. It never smoothly transitions through $$1.5$$ clicks.

**The Poisson Distribution (The Summary)** Now, instead of just watching the timeline forever, we decide to run an experiment. We set a timer for exactly one minute and count how many total jumps (clicks) happened in that fixed window of time. We run this one-minute experiment thousands of times. Sometimes we get $$3$$ jumps. Sometimes $$5$$ jumps and occasionally we get $$0$$ jumps. If we calculate the probability of getting exactly $$k$$ jumps in that fixed time window, we get the Poisson distribution. The formula for the probability of observing exactly $$k$$ events is,

$$P(k) = \frac{\lambda^k e^{-\lambda}}{k!}$$

Where $$\lambda$$ is the average rate of jumps per minute.

Regarding the Kramers-Moyal expansion, there comes the Pawula theorem stating that for a general random process, the series of moments $$M_1, M_2, M_3, \dots$$ either a) terminates at the $$3^{\text{rd}}$$ term, or b) all its even terms are positive. The proof can be found on the Wikipedia page for [Kramers-Moyal expansion](https://en.wikipedia.org/wiki/Kramers%E2%80%93Moyal_expansion). The indication is, if the $$3^{\text{rd}}$$ term is not $$0$$, the the resulted Kramers-Moyal expansion can only be used to describe 'jumpy' processes like the Poisson process. Also, we cannot safely truncate terms at a finite order of term since otherwise it is possbile the resulted probability density $$\rho$$ will become negative (since we are throwing away a lot of positive even terms).

# Part VI -- The Stationary Solution Is the Boltzmann Distribution

This part establishes the central claim of Langevin thermostatting, in two logically separate stages:

- **§6.1–6.6: Stationarity.** The Boltzmann distribution is a stationary solution of the Kramers equation, and -- more strongly -- the exponential form is *forced* rather than guessed.
- **§6.7–6.9: Convergence.** The dynamics actually approaches that stationary state from arbitrary initial conditions, monotonically.

## 6.1 Strategy: split the operator

Write the Kramers equation (5.3) as

$$\frac{\partial\rho}{\partial t} = \underbrace{\{H,\rho\}}_{\text{reversible}} \;+\; \underbrace{\frac{\partial}{\partial p}\left[\gamma p\rho + D_p\frac{\partial\rho}{\partial p}\right]}_{\text{irreversible}} \tag{6.1}$$

where $$\{H,\rho\} = -\frac{p}{m}\partial_x\rho + U'(x)\partial_p\rho$$ is the Poisson bracket (1.4) with a sign convention chosen so that (6.1) matches (5.3) term by term.

Instead of just requiring the total sum to vanish (mere stationarity), we require each part to vanish individually. This stronger condition is known as **detailed balance**, meaning there is no net probability flow at equilibrium. Because microscopic reversibility forbids sustained circulating currents, this is the physically correct requirement for a system coupled to a single heat bath. It is also mathematically simpler, decoupling the problem into two independent conditions. (Section 6.6 confirms this still satisfies the weaker condition, as expected.)

## 6.2 The reversible term annihilates *any* function of $$H$$

Try the ansatz

$$\rho_{\text{eq}}(x,p) = f\big(H(x,p)\big) \tag{6.2}$$

for some as-yet-unknown single-variable function $$f$$. **No assumption is made that $$f$$ is exponential.** Then, by the chain rule for Poisson brackets,

$$\{H,f(H)\} = f'(H)\,\{H,H\} = 0 \tag{6.3}$$

because $$\{A,A\} = 0$$ identically for any $$A$$, by the antisymmetry $$\{A,B\} = -\{B,A\}$$ noted in §1.3.

**Physical content.** Any distribution depending on phase space only through the conserved energy is automatically invariant under Hamiltonian flow -- trajectories move along level sets of $$H$$, so a density constant on those level sets is simply relabelled, not changed -- the position and momentum, i.e., the coordinates in the phase space may change, but the density distribution in the phase space does not change. The reversible half of (6.1) is therefore satisfied *for free, for any $$f$$*. It imposes no constraint whatsoever on the functional form.

**Why the ansatz (6.2) is justified.** For a statistical system, if we have some conserved quantities, then even the system states can fluctuate while the position and momentum are flying around in the phase space, at the equilibrium state, all states with the same conserved quantities are equally possible to be taken by the system. Given such a principle, the system states density distribution $$\rho$$ should only depend on those conserved quantities. Regarding the Fokker-Planck equation, let's forget about the Wiener process for the moment and assume only the isolated system where we don't have any energy exchagne with outside. In this case, we know that the system energy is conserved and beyond that, we don't have other conserved quantities. Therefore, the state density distribution should only depend on the system Hamiltonian. Going beyond, when we introduce the external friction and random kick (the Wiener process), since the random kick is considered isotropic and therefore does not bring in any extra dependence for the density distribution. Although the friction part does depend on the momentum (since it is always opposite the moving direction), its fundamental origin from the random kick guarnatees that overall, there is no associated momentum dependence introduced into the system state density distribution.

## 6.3 The irreversible term forces an exponential

All the constraint therefore lives in the second bracket of (6.1). Detailed balance requires the **irreversible probability current** in momentum to vanish:

$$J_p^{\text{irr}} \equiv -\gamma p\,\rho_{\text{eq}} - D_p\frac{\partial\rho_{\text{eq}}}{\partial p} = 0 \tag{6.4}$$

Evaluate the derivative using the chain rule and $$\partial H/\partial p = p/m$$:

$$\frac{\partial f(H)}{\partial p} = f'(H)\frac{\partial H}{\partial p} = f'(H)\frac{p}{m}$$

Substituting into (6.4):

$$-\gamma p\,f(H) - D_p f'(H)\frac{p}{m} = 0$$

**The factor $$p$$ cancels from every term.** This is an important consistency check: the condition must hold for all $$p$$ simultaneously, and it does, precisely because the ansatz (6.2) was of the right form. What remains is a first-order ordinary differential equation in the single variable $$H$$:

$$\frac{f'(H)}{f(H)} = -\frac{\gamma m}{D_p} \tag{6.5}$$

Integrating,

$$\log f(H) = -\frac{\gamma m}{D_p}H + \text{const} \Rightarrow f(H) = C\exp\left(-\frac{\gamma m}{D_p}H\right) \tag{6.6}$$

Here, we see that even though going through complicated random process, the stationary distribution of the system is still following the Boltzmann distribution. Though, we have to work out the coefficient to reproduce $$\beta = 1/(k_BT)$$.

## 6.4 Identifying the coefficient: $$\beta = 1/k_BT$$

At this point the stationary distribution is established as $$\rho_{\text{eq}}\propto e^{-\lambda H}$$ with rate constant

$$\lambda \equiv \frac{\gamma m}{D_p} \tag{6.7}$$

for *whatever* $$\gamma$$ and $$D_p$$ happen to be. Identifying $$\lambda$$ with a physical temperature is a separate step -- and this is exactly where, and only where, the fluctuation–dissipation relation enters. Substituting $$D_p = \gamma mk_BT$$ from Eqn. (4.9), which Part IV derived independently from the microscopic bath model without any assumption about the system's stationary state:

$$\lambda = \frac{\gamma m}{\gamma mk_BT} = \frac{1}{k_BT} \equiv \beta \tag{6.8}$$

Therefore

$$\rho_{\text{eq}}(x,p) = \frac{1}{Z}\exp\left[-\beta\left(\frac{p^2}{2m} + U(x)\right)\right], \qquad \beta = \frac{1}{k_BT} \tag{6.9}$$

which is precisely the canonical distribution (1.5) that Part I identified as the target. The logical chain is worth restating explicitly, since its ordering is what makes the argument non-circular:

1. **Symmetry** $$\Rightarrow$$ $$\rho_{\text{eq}}$$ depends on phase space only through $$H$$. *(§6.2)*
2. **Substitution into the stationary Kramers equation** $$\Rightarrow$$ a first-order linear ODE for $$f$$. *(§6.3)*
3. **ODE theory** $$\Rightarrow$$ $$f$$ is exponential, uniquely. *(§6.3)*
4. **Microscopically-derived FD relation** $$\Rightarrow$$ the exponent's rate constant equals $$1/k_BT$$. *(§6.4, using §4.4–4.5)*

Step 4 is the only step requiring physical input beyond the dynamics itself, and that input concerns the *bath's* preparation, not the system's answer.

## 6.5 Normalization and consistency checks

**Normalization.** The distribution factorizes into independent momentum and position parts:

$$Z = \int_{-\infty}^{\infty}\!\!\int_{-\infty}^{\infty} e^{-\beta p^2/2m}e^{-\beta U(x)}\,dp\,dx = \underbrace{\sqrt{2\pi mk_BT}}_{Z_p}\cdot\underbrace{\int e^{-\beta U(x)}dx}_{Z_x} \tag{6.10}$$

using the Gaussian integral $$\int e^{-\alpha p^2}dp = \sqrt{\pi/\alpha}$$ with $$\alpha = \beta/2m$$ (Appendix A).

**Check 1: equipartition is recovered.** The momentum marginal is $$\mathcal N(0, mk_BT)$$, so $$\langle p^2\rangle = mk_BT$$ and $$\langle p^2/2m\rangle = \tfrac12k_BT$$ -- equipartition (4.7), which Route 1 in Part IV had to *assume*, now emerges as a *consequence*. This is the check that the two routes are consistent.

**Check 2: the OU limit.** Setting $$U=0$$, the momentum marginal is $$\mathcal N(0,mk_BT)$$, matching the explicitly-solved stationary OU distribution (4.6) with $$\sigma^2/2\gamma = mk_BT$$.

**Check 3: the position marginal.** Integrating out momentum gives $$\rho_{\text{eq}}(x)\propto e^{-\beta U(x)}$$ -- the Boltzmann distribution in configuration space, which is what most sampling applications actually want.

## 6.6 Direct verification (the brute-force cross-check)

Since §6.1 imposed the *stronger* detailed-balance condition, it is worth confirming directly that (6.9) satisfies the *weaker* stationarity requirement $$\partial_t\rho_{\text{eq}} = 0$$ in (5.3), with no ansatz involved. Compute the derivatives of $$\rho_{\text{eq}} = Z^{-1}e^{-\beta H}$$:

$$\frac{\partial\rho_{\text{eq}}}{\partial x} = -\beta U'(x)\rho_{\text{eq}}, \qquad \frac{\partial\rho_{\text{eq}}}{\partial p} = -\frac{\beta p}{m}\rho_{\text{eq}}$$

Substitute term by term into (5.3), with $$D_p = \gamma mk_BT = \gamma m/\beta$$:

| Term | Value |
|---|---|
| $$-\frac{p}{m}\partial_x\rho_{\text{eq}}$$ | $$+\frac{\beta p}{m}U'\rho_{\text{eq}}$$ |
| $$U'\partial_p\rho_{\text{eq}}$$ | $$-\frac{\beta p}{m}U'\rho_{\text{eq}}$$ |
| $$\gamma\partial_p(p\rho_{\text{eq}}) = \gamma\rho_{\text{eq}} + \gamma p\,\partial_p\rho_{\text{eq}}$$ | $$\gamma\rho_{\text{eq}} - \frac{\gamma\beta p^2}{m}\rho_{\text{eq}}$$ |
| $$D_p\partial_p^2\rho_{\text{eq}} = D_p\partial_p\!\left(-\frac{\beta p}{m}\rho_{\text{eq}}\right)$$ | $$\frac{\gamma m}{\beta}\left(-\frac{\beta}{m} + \frac{\beta^2p^2}{m^2}\right)\rho_{\text{eq}} = -\gamma\rho_{\text{eq}} + \frac{\gamma\beta p^2}{m}\rho_{\text{eq}}$$ |

The first two rows cancel (this is the reversible/Poisson-bracket cancellation of §6.2, seen concretely). The last two rows cancel term by term: the $$\gamma\rho_{\text{eq}}$$ pieces cancel, and the $$\gamma\beta p^2\rho_{\text{eq}}/m$$ pieces cancel. The total is zero.

## 6.7 Convergence: the H-theorem

Stationarity is established. Convergence requires a **Lyapunov functional** -- a quantity that decreases monotonically along the dynamics and is uniquely minimized at $$\rho_{\text{eq}}$$.

**The functional.** Define the **relative entropy** (Kullback–Leibler divergence) of $$\rho$$ from $$\rho_{\text{eq}}$$:

$$\mathcal H[\rho] \equiv \int \rho\,\log\!\left(\frac{\rho}{\rho_{\text{eq}}}\right)dx\,dp \;=\; \int\rho_{\text{eq}}\,g\log g\;dx\,dp, \qquad g \equiv \frac{\rho}{\rho_{\text{eq}}} \tag{6.11}$$

**Non-negativity.** The function $$u\mapsto u\log u$$ is convex. By Jensen's inequality applied with weight $$\rho_{\text{eq}}$$ (which integrates to 1):

$$\mathcal H[\rho] = \int\rho_{\text{eq}}\,g\log g \;\ge\; \left(\int\rho_{\text{eq}}g\right)\log\left(\int\rho_{\text{eq}}g\right) = 1\cdot\log1 = 0$$

with equality **if and only if** $$g$$ is constant, i.e. $$\rho = \rho_{\text{eq}}$$. So $$\mathcal H\ge0$$, vanishing only at the target.

---

The Jensen's inequality for a convex function $$f(u)$$ says,

$$
\langle f(u) \rangle \geq f(\langle u \rangle)
$$

and in the current context, we have the context function $$g\log g$$, which can then be applied to the $$\mathcal{H}[\rho]$$ functional above.

---

**Thermodynamic meaning.** Writing out (6.11) with (6.9): $$\mathcal H[\rho] = \beta\big(F[\rho] - F_{\text{eq}}\big)$$ where $$F[\rho] = \langle H\rangle - TS[\rho]$$ is the free energy functional. Proving $$d\mathcal H/dt\le0$$ is therefore literally proving the second law of thermodynamics for this dynamics: free energy decreases until it reaches its minimum.

**The reversible part contributes nothing.** The Liouville flow advects $$\rho$$ along trajectories that preserve both phase-space volume (Liouville's theorem, §1.3) and $$H$$. Since $$\rho_{\text{eq}}$$ depends only on $$H$$, the flow maps $$\rho_{\text{eq}}$$ to itself and merely relabels the level sets of $$g$$. An integral of the form $$\int\rho_{\text{eq}}\,g\log g$$ is therefore unchanged. Hamiltonian streaming is **entropy-neutral**.

**The irreversible part dissipates.** Substituting $$\rho = \rho_{\text{eq}}g$$ into the dissipative operator, using $$\partial_p\rho_{\text{eq}} = -(\beta p/m)\rho_{\text{eq}}$$ and the FD relation $$D_p\beta/m = \gamma$$, the dissipative part of the Kramers equation takes the compact form

$$\left.\frac{\partial\rho}{\partial t}\right|_{\text{irr}} = D_p\,\frac{\partial}{\partial p}\left[\rho_{\text{eq}}\frac{\partial g}{\partial p}\right] \tag{6.12}$$

(To verify: $$\rho_{\text{eq}}\partial_pg = \partial_p\rho - g\partial_p\rho_{\text{eq}} = \partial_p\rho + \frac{\beta p}{m}\rho$$, and $$D_p\partial_p[\partial_p\rho + \frac{\beta p}{m}\rho] = D_p\partial_p^2\rho + \gamma\partial_p(p\rho)$$, matching Eqn. 6.1)

Now differentiate (6.11),

$$
\begin{align}
\frac{d\mathcal H}{dt} & = \int \rho_{\text{eq}}\frac{\partial}{\partial t}(g\log g)dxdp\\
& = \int \rho_{\text{eq}}\bigg[ \frac{\partial g}{\partial t}\log g + g\frac{\partial}{\partial t}(\log g) \bigg]dxdp\\
& = \int \rho_{\text{eq}}\bigg[ \frac{\partial g}{\partial t}\log g + g \cdot \big( \frac{1}{g}\frac{\partial g}{\partial t} \big) \bigg]dxdp\\
& = \int \rho_{\text{eq}}\bigg[ \frac{\partial g}{\partial t}(\log g + 1) \bigg]dxdp\\
& = \int \rho_{\text{eq}}\frac{\partial g}{\partial t}\log g dxdp + \int \rho_{\text{eq}}\frac{\partial g}{\partial t}\,dxdp
\end{align}
$$

Recall that by definition, $$\rho = \rho_{\text{eq}}g$$. Since $$\rho_{eq}$$ is strictly independent of time, it directly follows that $$\frac{\partial \rho}{\partial t} = \rho_{eq} \frac{\partial g}{\partial t}$$. We can use this relation to rewrite both terms, so that,

$$
\begin{align}
\frac{d\mathcal{H}}{dt} &  = \int \frac{\partial \rho}{\partial t} \log g \, dx dp + \int \frac{\partial \rho}{\partial t} dx dp\\
& = \int\frac{\partial\rho}{\partial t}\log g dxdp \;=\; D_p\int\partial_p\!\left[\rho_{\text{eq}}\partial_pg\right]\log g \, dxdp
\end{align}
$$

where the second term in the first line vanishes since we can pull the time derivative out of the integration to have $$\frac{d}{dt} \int \rho \, dx dp$$. Because $$\rho$$ is a probability density function, the total probability across all of phase space must always integrate to exactly 1. Therefore, $$\int \rho = 1$$, and the derivative of a constant is zero.

Integrate by parts in $$p$$ (boundary terms vanish, since $$\rho_{\text{eq}}\to0$$ as $$\vert p \vert \to\infty$$) and use $$\partial_p\log g = (\partial_pg)/g$$,

$$
\begin{align}
\frac{d\mathcal H}{dt} & = D_p \bigg[ \left[ (\log g)(\rho_{eq} \partial_p g) \right]_{-\infty}^{\infty} - \int_{-\infty}^{\infty} (\rho_{eq} \partial_p g) \left( \frac{1}{g} \partial_p g \right) dp \bigg]\\
& = -D_p\int\rho_{\text{eq}}\,\frac{\left(\partial_pg\right)^2}{g}\,dx\,dp \;\le\; 0 \tag{6.13}
\end{align}
$$

where the first term inside the square bracket vanishes due to that $$\rho_{\text{eq}}\to0$$ as $$\vert p \vert \to\infty$$.

The integrand is manifestly non-negative: a squared quantity times the positive weight $$\rho_{\text{eq}}/g$$. **This is the H-theorem.** The right-hand side is a **Fisher information** functional; it can equivalently be written $$-4D_p\int\rho_{\text{eq}}(\partial_p\sqrt g)^2$$ -- simply applying $$\partial_p (g^{1/2}) = \frac{1}{2} g^{-1/2} (\partial_p g)$$.

**Closing the argument.** Equality in (6.13) requires $$\partial_pg = 0$$ everywhere, i.e. $$g = g(x,t)$$ -- momentum-independent. But such a $$g$$ is not left alone by the *reversible* term: substituting $$\rho = \rho_{\text{eq}}g(x,t)$$ into $$\{H,\rho\}$$, the force-dependent pieces cancel exactly (§6.2), leaving

$$\{H,\rho\}\big|_{g=g(x)} = -\frac{p}{m}\rho_{\text{eq}}\,\partial_xg$$

which **reintroduces $$p$$-dependence** unless $$\partial_xg = 0$$ as well. Combined with $$\partial_pg=0$$, this forces $$g$$ constant, and normalization fixes the constant to $$1$$:

$$g\equiv1 \Leftrightarrow \rho = \rho_{\text{eq}}$$

Here we see that the dissipation term destroys the $$p$$-dependence but it does not contain the operation regarding $$x$$, meaning that the dissipation term does not yield constaints on what values that the particle position should take. However, the Hamiltonian term (i.e., the first term in Eqn. 6.1) does contain the operation upon $$x$$ (i.e., the $$\partial_xg$$ above) and we arrived at the conclusion that the whole process will only halt when neither dependence ($$x$$ and $$p$$) remains for the defined quantity $$g$$. This coupling of a degenerate-but-dissipative operator (e.g., the second term in Eqn. 6.1) with an entropy-neutral-but-mixing one (e.g., the first term in Eqn. 6.1) is the phenomenon Villani named **hypocoercivity** [11]; the kinetic Fokker–Planck equation is its canonical example.

## 6.8 Rate of convergence

The H-theorem establishes monotonicity but not a rate: it proves that the relative entropy $$\mathcal{H}$$, the "distance" to equilibrium, is always decreasing ($$d\mathcal{H}/dt \le 0$$), but this alone gives no sense of how quickly the system settles. A ball rolling downhill is guaranteed to move toward the bottom, but if the hill gradually flattens out to be almost horizontal, the ball could in principle roll for an arbitrarily long time without ever fully stopping. A rate follows only if the equilibrium distribution $$\rho_{eq}$$ satisfies a further condition: a **logarithmic Sobolev inequality (LSI)** with constant $$\lambda_{LS} > 0$$, which guarantees that the landscape stays steep enough to force convergence within a bounded timeframe.

> Here we may wonder why the distribution gets to dictate whether the landscape is steep enough. Because in thermal equilibrium, the distribution and the landscape are two sides of the exact same coin.For example, with the Boltzmann distribution $$\rho_{eq} \propto e^{-U(x)/k_BT}$$, the physical shape of the bowl ($$U$$) completely defines the mathematical shape of the equilibrium distribution ($$\rho_{eq}$$). If the landscape $$U(x)$$ is a nice, steep, single valley (like a harmonic oscillator), the $$\rho_{eq}$$ is a nice, tight, single Gaussian peak. This distribution satisfies the LSI. If the landscape $$U(x)$$ is a "W" shape with two valleys, the $$\rho_{eq}$$ will have two distinct peaks separated by a region of near-zero probability. This distribution fails to satisfy the LSI (with a strictly positive $$\lambda_{LS}$$).

### The logarithmic Sobolev inequality

Proving exponential decay requires linking the *distance* to equilibrium with the *driving force* pushing the system toward it:

- **Distance:** $$\mathcal{H}[\rho]$$, the relative entropy.
- **Driving force:** $$\mathcal{I}[\rho]$$, the Fisher information — the integral term $$\int \rho_{eq} (\partial g)^2 / g$$ appearing in Equation (6.13). It measures how "wiggly," or out of shape, the current distribution is relative to the equilibrium distribution.

The LSI is the theorem that supplies this link, stating that the distance to equilibrium is strictly capped by the driving force,

$$
\mathcal{H}[\rho] \le \frac{1}{2\lambda_{LS}} \mathcal{I}[\rho], \qquad \mathcal{I}[\rho] \equiv \int \rho_{eq} \frac{(\partial g)^2}{g} \tag{6.14}
$$

In plain terms: a system far from equilibrium (large $$\mathcal{H}$$) is guaranteed to experience a correspondingly large driving force ($$\mathcal{I}$$) pulling it back. The constant $$\lambda_{LS}$$ is simply the proportionality factor that determines how strong this guarantee is.

### Exponential decay

The pieces combine directly. The H-theorem (6.13) shows the rate of change is driven by $$\mathcal{I}$$,

$$
\frac{d\mathcal{H}}{dt} = -D \cdot \mathcal{I}[\rho]
$$

Rearranging the LSI (6.14) gives $$\mathcal{I}[\rho] \ge 2\lambda_{LS}\mathcal{H}[\rho]$$. Substituting this bound into the H-theorem rate yields a differential inequality,

$$
\frac{d\mathcal{H}}{dt} \le -2D\lambda_{LS}\mathcal{H}
$$

This is the standard form of an exponential-decay equation, structurally identical to radioactive decay or Newton's law of cooling -- when the rate of change is proportional to the current amount, the quantity shrinks exponentially. Integrating gives the stated result, proving that the distance to equilibrium decays exponentially fast,

$$
\mathcal{H}[\rho_t] \le \mathcal{H}[\rho_0] \, e^{-2D\lambda_{LS} t} \tag{6.15}
$$

### The Bakry–Émery criterion: the physical shape of the bowl

The derivation above holds only if $$\lambda_{LS}$$ is strictly greater than zero. The **Bakry–Émery criterion** identifies where this constant comes from physically: it is determined by the shape of the potential energy landscape, $$U(x)$$. If $$\rho_{eq} \propto e^{-V}$$ with $$V'' \ge \kappa > 0$$ (that is, $$V$$ is $$\kappa$$-strongly convex), then the LSI holds with $$\lambda_{LS} = \kappa$$. Since $$V = \beta U$$, this gives $$\lambda_{LS} = \beta U''_{min}$$.

Picturing $$U(x)$$ as a physical bowl makes the Bakry–Émery criterion highly intuitive. The constant $$\kappa$$ (which serves as the $$\lambda_{LS}$$) represents the minimum curvature, $$U''$$, found anywhere in that bowl. The crucial takeaway is this: as long as the curvature at the absolute flattest part of the potential is strictly positive ($$U'' \ge \kappa > 0$$), the entire system is guaranteed to converge exponentially fast. Because $$\kappa$$ defines the "bottleneck," if the restoring force is strong enough to push the system through this flattest region, then everywhere else on the landscape is steeper and will force the system toward equilibrium even faster.

This criterion works because of the specific mathematical relationship between the equilibrium distribution and the potential landscape. The distribution takes the form of the Boltzmann distribution, arguably the central equation of equilibrium statistical mechanics:

$$
\rho_{eq} \propto e^{-V}
$$

Here, $$\rho_{eq}$$ is the equilibrium probability density (the likelihood of finding the system fully settled at a given point), $$\propto$$ denotes proportionality (omitting the normalization constant, or partition function $$Z$$), and $$V = U/k_B T$$ is the dimensionless potential.

Physically, this means the probability of finding the system in a given state decreases exponentially as the energy of that state increases. At the bottom of the bowl where $$U$$ is low, $$e^{-U/k_BT}$$ is comparatively large, making the state highly likely. Conversely, high on the walls where $$U$$ is large, $$e^{-U/k_BT}$$ becomes vanishingly small.

Because the probability distribution is tied to the landscape via this exact exponential function, taking the derivative of the probability inherently pulls down the derivative of the landscape's shape. This is the mechanism by which the heavy, abstract machinery of LSI reduces to a simple physical rule: because the distribution is $$e^{-V}$$, the system's global convergence speed can be guaranteed entirely by the physical curvature of the bowl.

### Worked example: the harmonic well

Take $$U = \frac{1}{2}kx^2$$, overdamped (Part VII), so $$D = k_BT/\zeta$$ and $$U'' = k$$. Then $$\lambda_{LS} = k/k_BT$$, and the convergence rate is:

$$
2D\lambda_{LS} = 2 \cdot \frac{k_BT}{\zeta} \cdot \frac{k}{k_BT} = \frac{2k}{\zeta}
$$

An independent check confirms this result physically. Consider a particle in a thick, honey-like fluid attached to a spring -- the overdamped harmonic well. If the particle is displaced from center and released, its mean position relaxes back at rate $$r = k/\zeta$$, shrinking as $$e^{-rt}$$. Variance measures the spread of the distribution and is computed from the squared position; since the position shrinks as $$e^{-rt}$$, its square shrinks as $$(e^{-rt})^2 = e^{-2rt}$$. Consequently the variance relaxes exactly twice as fast as the mean, at rate $$2r = 2k/\zeta$$ -- matching the rate the Bakry–Émery calculation produced above.

This match is not a coincidence: relative entropy is quadratic in the deviation from equilibrium, so it is expected to relax at the variance rate rather than the mean rate -- and indeed it does. This quadratic behavior follows from a short Taylor expansion. Writing a small deviation from equilibrium as $$g = 1 + \delta$$ (so $$g = 1$$ at equilibrium), the core of the relative entropy calculation is the function $$g\log g$$. Using $$\log(1+\delta) \approx \delta - \delta^2/2$$:

$$
g\log g = (1+\delta)\log(1+\delta) \approx (1+\delta)\left(\delta - \frac{\delta^2}{2}\right)
$$

Expanding and dropping cubic and higher-order terms leaves:

$$
g\log g \approx \delta + \frac{\delta^2}{2}
$$

Integrating this over the full phase space causes the linear term in $$\delta$$ to vanish entirely -- recall Eqn. (6.11) and the definition of $$g = \rho/\rho_{\text{eq}}$$, and therefore we have $$\delta = g - 1 = \rho/rho_{\text{eq}} - 1$$. Putting this into the integration in Eqn. (6.11) and calculate the integration, we will have,

$$\int\rho_{\text{eq}}\bigg( \frac{\rho}{\rho_{\text{eq}}} - 1 \bigg) dx = \int\rho dx - \int\rho_{\text{eq}} dx = 1 - 1 = 0$$

What remains is only the second-order term, $$\delta^2/2$$. This is the reason relative entropy behaves quadratically in the deviation, and hence relaxes at the variance rate of $$2k/\zeta$$, exactly as the Bakry–Émery calculation predicts.

## 6.9 What stationarity does not buy: metastability

The convergence results established so far show that a system governed by Langevin dynamics will always reach equilibrium eventually. This section addresses the practical limits of that guarantee: "eventually" can exceed the lifespan of the universe.

Equations (6.13)-(6.15) guarantee fast, exponential convergence, but only when the potential $$U$$ is shaped like a single steep well (strongly convex). When $$U$$ is **multimodal** instead, that guarantee breaks down. Consider a double-well potential: two valleys separated by a hill, or energy barrier, of height $$\Delta U$$. A particle starting in one valley quickly settles at the bottom of that well and, because it sits in a local minimum, it appears locally stable. This is **metastability** -- the system looks like it has reached equilibrium, but it has not reached the true global minimum in the other valley. The convergence result technically still holds, but the rate constant $$\lambda_{LS}$$ becomes exponentially small, and the asymptotic guarantee becomes practically useless.

Escape from the local well requires a random thermal fluctuation, a "kick" from the surrounding bath, strong enough to cross the barrier entirely. This is **Kramers' escape problem** [5]. For a particle in a well of curvature $$\omega_a^2 = U''(x_a)/m$$ separated by a barrier of height $$\Delta U$$ and curvature $$\omega_b$$, the escape rate in the high-friction regime is

$$
k_{\text{esc}} \simeq \frac{\omega_a \omega_b}{2\pi\gamma}\, e^{-\Delta U / k_B T}
\tag{6.16}
$$

The exponential factor at the end, $$e^{-\Delta U / k_B T}$$, is decisive. Here $$\Delta U$$ is the barrier height and $$k_B T$$ is the available thermal energy; the ratio $$\Delta U / k_B T$$ sets the probability that a given kick clears the barrier. Because the dependence is exponential, a small increase in barrier height makes crossing exponentially less likely.

> **Concrete numbers.** For a barrier of $$\Delta U = 10\, k_B T$$, the Arrhenius factor is $$e^{-10} \approx 4.5\times10^{-5}$$, roughly one crossing per $$2\times10^{4}$$ attempt-times. At $$\Delta U = 20\, k_B T$$ it is $$e^{-20} \approx 2\times10^{-9}$$, i.e. roughly $$5\times10^{8}$$ attempt-times per crossing. A simulation resolving well dynamics with $$10^3$$ steps per attempt-time would then need on the order of $$10^{12}$$ steps to observe a single barrier crossing.

The mathematics guarantees the correct answer eventually; the physics indicates that "eventually" may exceed any feasible simulation. On a rugged, multi-valley landscape, ordinary Langevin dynamics becomes trapped in the first valley encountered and remains there indefinitely for all practical purposes. This gap between mathematical guarantee and computational feasibility is the reason enhanced sampling is required in molecular simulation, and, as Part IX shows, the direct reason **annealing is required in generative modeling**.

The standard remedy is simulated annealing: the temperature $$T$$ of the simulation is temporarily raised, which enlarges the $$e^{-\Delta U / k_B T}$$ term and allows the system to cross barriers and explore the full landscape, before being slowly cooled again to settle into the true global equilibrium. Raw Langevin dynamics, without such temperature manipulation, is therefore a poor tool for exploring rugged landscapes: it converges in principle but remains trapped in metastable states in practice.

<br>

(to be continued...)