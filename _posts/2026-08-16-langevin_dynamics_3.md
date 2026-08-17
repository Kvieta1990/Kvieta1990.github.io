---
layout: post
title: Langevin Dynamics -- From Molecular Thermostats to Diffusion Models (Part-3)
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

> This post is part-3 of the 4-parts full story about the Langevin dynamics. All references will be presented in part-4.

# Part VII -- The Overdamped (Smoluchowski) Limit

## 7.1 The physical regime

Two timescales are present in the Langevin system:

| Timescale | Symbol | Meaning |
|---|---|---|
| Momentum relaxation | $$\tau_p = 1/\gamma = m/\zeta$$ | time for velocity to forget its initial value |
| Configurational | $$\tau_x$$ | time for $$x$$ to move over the scale of interest |

<br>

> **About the relaxation time scale**
>
> In the Langevin equation (Eqn. 2.1 [here](../2026-08-13-langevin_dynamics_1)), let's for the moment forget about the noise term in the motion equation and assume $$0$$ potential field,
>
> $$\frac{dp}{dt} = -\gamma p$$
>
> (where $$\gamma = \zeta/m$$ is the friction coefficient per unit mass)
>
> Solution of the equation yields,
>
> $$p(t) = p(0)e^{-\gamma t}$$
>
> from which we can see the typical relaxation time for momentum is,
>
> $$\tau_p = \frac{1}{\gamma}$$
{: .info}

The **overdamped limit** is $$\tau_p \ll \tau_x$$: friction is so strong that momentum equilibrates essentially instantaneously on the timescale over which position changes. At any "slow" time, momentum can be treated as already Maxwell-distributed at the local position. This is an **adiabatic elimination** of a fast variable.

## 7.2 The heuristic

If $$\tau_p \ll \tau_x$$, the inertial term in $$m\ddot x = -\zeta\dot x - U'(x) + \sigma\xi$$ has already relaxed on the timescale of interest (i.e., the momentum already equilibrates so $$dp/dt \approx 0$$), and dropping it gives,

$$0 \approx -\zeta\dot x - U'(x) + \sigma\xi(t) \Rightarrow \dot x = -\frac{U'(x)}{\zeta} + \frac{\sigma}{\zeta}\xi(t)$$

With $$\sigma = \sqrt{2\zeta k_BT}$$ (recall the discussion in part-IV about the fluctuation-dissipation theorem), the noise prefactor is $$\sigma/\zeta = \sqrt{2k_BT/\zeta}$$, and if we ignore the potential term for the moment, it can be shown to yield the Einstein diffusion relation for the 1D diffusion problem.

> **Heuristic diffusion relation derivation**
>
> First, the 1D diffusion equation (see [here](https://math.libretexts.org/Bookshelves/Differential_Equations/Differential_Equations_(Chasnov)/09%3A_Partial_Differential_Equations/9.01%3A_Derivation_of_the_Diffusion_Equation) for derivation) for the probability density is,
>
> $$ \frac{\partial P(x, t)}{\partial t} = D\frac{\partial^2 P(x, t)}{\partial x^2}$$
>
> Starting from $$P(x, 0) = \delta(x)$$, i.e., at the beginning ($$t = 0$$), the distribution is at exactly $$x = 0$$ as a delta function, the solution is given as,
>
> $$ P(x, t) = \frac{1}{\sqrt{4\pi Dt}}e^{-x^2/(4Dt)}$$
>
> Solving the partial differential equation over the whole $$x$$-space without boundary involves the utilization of a Fourier transform trick and details can be found [here](https://share.gemini.google/210PERVN35AH). For the bounded situation, refer to the discussion [here](https://math.libretexts.org/Bookshelves/Differential_Equations/Differential_Equations_(Chasnov)/09%3A_Partial_Differential_Equations/9.05%3A_Solution_of_the_Diffusion_Equation).
>
> Given the Gaussian solution and by comparing to the standard Gaussian distribution $$\frac{1}{\sqrt{2\pi\sigma^2}}e^{-x^2/(2\sigma_0^2)}$$, we have the variance of the position,
>
> $$\langle x^2 \rangle = \sigma_0^2 = 2Dt$$
>
> Then we turn to the stochastic equation while ignoring the potential term,
>
> $$\frac{dx}{dt} = \sigma\xi(t)$$
>
> Integrating over $$t$$ on both sides and squaring, we have,
>
> $$
> \begin{align}
> \langle x(t)^2 \rangle & = \left\langle \left( \int_0^t \sigma \xi(t') dt' \right) \left( \int_0^t \sigma \xi(t'') dt'' \right) \right\rangle\\
> & = \sigma^2\int_0^t\int^t\langle \xi(t')\xi(\xi'') \rangle dt'dt''\\
> & = \sigma^2 \int_0^t \int_0^t \delta(t' - t'') dt' dt''\\
> & = \int_0^t dt'\sigma^2\int_0^t \delta(t' - t'') dt'' = \sigma^2 t
> \end{align}
> $$
>
> So, we have $$2D = \sigma^2 \Rightarrow D = \frac{k_BT}{\zeta}$$.
{: .info}

<br>

> Here, for the standard Gaussian distribution function, I was using $$\sigma_0$$ to represent the variance to avoid confusion with the $$\sigma$$ parameter used in the stochastic differential equation.

The derivation here is rigorous for the stochastic process but the starting point is a bit loose -- it is assumed the momentum already equilibrates during the 'slow' time duration. This can be a good assumption for normal processes but not the case for the stochastic process would yield an infinite force exerted on particles.

## 7.3 Systematic derivation: Chapman–Enskog elimination

Start from the Kramers equation (5.3) with $$D_p = \gamma mk_BT$$:

$$\frac{\partial\rho}{\partial t} = \mathcal L_1\rho + \gamma\,\mathcal L_0\rho \tag{7.1}$$

where the two operators are defined as,

$$\mathcal L_1 \equiv -\frac{p}{m}\partial_x + U'(x)\partial_p, \qquad \mathcal L_0 \equiv \partial_p\big(p\;\cdot\;\big) + mk_BT\,\partial_p^2 \tag{7.2}$$

**Step 1 -- identify the null space of $$\mathcal L_0$$.** Solving $$\mathcal L_0\phi = 0$$ gives the **local Maxwellian**,

$$\phi_{\text{MB}}(p) = \frac{1}{\sqrt{2\pi mk_BT}}\exp\left(-\frac{p^2}{2mk_BT}\right), \qquad \mathcal L_0\phi_{\text{MB}} = 0 \tag{7.3}$$

The null space is $$\{\phi_{\text{MB}}(p)\,n(x)\}$$ for arbitrary $$n$$ -- momentum is pinned to the Maxwellian, position is unconstrained.

> **Some notes**
>
> *Null space* refers to functions that nullifies (i.e., zero out) an operator -- here, the solution $$\phi$$ for $$\mathcal{L}_0\rho = 0$$ forms the null space for the operator $$\mathcal{L}_0$$.
>
> <br>
>
> To solve $$\mathcal{L}_0\phi = 0$$,
>
> $$
> \begin{align}
> \mathcal{L}_0\phi & = \frac{\partial(p\phi)}{\partial p} + mk_BT\frac{\partial^2\phi}{\partial p^2}\\
> & = \frac{d}{dp}[p\phi + mk_BT\phi'] = 0
> \end{align}
> $$
>
> where we replace the partial derivative with the full derivative since the derivative involved in the $$\mathcal{L}$$ operator only operates on the momentum but not the position. We know that the term inside the square bracket should be a constant and given the boundary condition that $$\phi \rightarrow 0$$ and $$\phi' \rightarrow 0$$ as $$p \rightarrow \pm \infty$$, we have,
>
> $$p\phi + mk_BT\phi' = 0$$
>
> Therefore,
>
> $$mk_BT\phi' = -p\phi \Rightarrow \frac{\phi'}{\phi} = -\frac{p}{mk_BT}$$
>
> Integrating both sides can solve $$\phi$$ up to a multiplicative constant and applying the normalization of the probability to give an overall value of 1, we can obtain the solution for $$\phi$$ as presented above.
{: .info}

**Step 2 -- set up the expansion.** Let $$\epsilon \equiv 1/\gamma$$ be the small parameter and rescale to the slow time $$\tau = t/\gamma = \epsilon t$$, so $$\partial_t = \epsilon\,\partial_\tau$$. Multiplying (7.1) through by $$\epsilon$$:

$$\epsilon^2\,\partial_\tau\rho = \epsilon\,\mathcal L_1\rho + \mathcal L_0\rho \tag{7.4}$$

> Here, we are doing nothing but changing the variable from $$t$$ to the rescaled time variable $$\tau = \epsilon t$$.

Expand $$\rho = \rho^{(0)} + \epsilon\rho^{(1)} + \epsilon^2\rho^{(2)} + \cdots$$ and collect orders -- we are trying to solve $$\rho$$ and first we write $$\rho$$ in this specific expansion form so terms containing different orders of $$\epsilon$$ can be treated term by term. Also, it turns out that the lower order terms solution will be used in solving the higher order terms.

**Order $$\epsilon^0$$.**

Putting the expansion into Eqn. (7.4), the left-side does not have $$\epsilon^0$$ term due to the $$\epsilon^2$$ multiplicative factor in the front. Therefore, the $$\epsilon^0$$ term on the right side should give 0. Therefore,

$$\mathcal L_0\rho^{(0)} = 0 \Rightarrow \rho^{(0)}(x,p,\tau) = \phi_{\text{MB}}(p)\,n(x,\tau) \tag{7.5}$$

**Order $$\epsilon^1$$.**

Same logic applies here to balance terms between the left and right side, we have,

$$\mathcal L_0\rho^{(1)} = -\mathcal L_1\rho^{(0)} \tag{7.6}$$

Compute the right-hand side using $$\partial_p\phi_{\text{MB}} = -\frac{p}{mk_BT}\phi_{\text{MB}}$$,

$$\mathcal L_1\rho^{(0)} = -\frac{p}{m}\phi_{\text{MB}}\partial_xn + U'n\,\partial_p\phi_{\text{MB}} = -\frac{p}{m}\phi_{\text{MB}}\underbrace{\left[\partial_xn + \frac{U'n}{k_BT}\right]}_{\equiv\,A(x,\tau)}$$

<ins>*Solvability.*</ins>  Equation (7.6) is solvable only if its right-hand side is orthogonal to the null space of $$\mathcal L_0^\dagger$$. This is a lemma from the linear algebra, stating that for solving $$Ax = y$$, we will have a solution if and only if the vector $$y$$ has zero overlap (a dot product of zero) with the null space of the transpose matrix $$A^T$$. Here we are applying the lemma to the differential equation -- fundamentally, the differential operator is just like a matrix operation with infinite dimension and the item it operates on in a vector with inifinite dimension as well (a function). Therefore, the lemma for linear algebra can be applied for the differential equation here. Further, the adjoint operator $$\mathcal{L}_0^\dagger$$ of $$\mathcal{L}$$ is defined such that,

$$\int (L_0 f) \, g \, dp = \int f \, (L_0^\dagger g) \, dp$$

The null space for $$\mathcal{L}_0^\dagger$$ therefore should be spanned by function $$g$$ that satisfies,

$$
\mathcal{L}_0^\dagger g = 0
$$

Accordingly, we have,

$$
\int (L_0 f) \, g \, dp = \int f \, (L_0^\dagger g) \, dp = \int f \cdot 0 \, dp = 0
$$

for all functions $$f(p)$$. Since the operator $$\mathcal{L}_0$$ is a total derivative with respect to $$p$$, we have $$\mathcal{L}_0f = \frac{dh}{dp}$$ ($$h$$ here just refers to whatever function of $$p$$ corresponding to the $$\mathcal{L}_0$$). Accordingly,

$$\int (L_0 f) \, g \, dp = \int\frac{dh}{dp}g dp = hg\Big\vert{}_{-\infty}^{\infty} - \int h\frac{dg}{dp}dp = 0$$

for all functions $$h(p)$$. Given the boundary condition that both $$h$$ and $$g$$ should vanish at $$\pm\infty$$, we have,

$$\int h\frac{dg}{dp}dp = 0$$

for all functions $$h(p)$$ and this requires $$dg/dp = 0$$, inferring that $$g(p)$$ is a constant. So, the null space for $$\mathcal{L}_0^\dagger$$ is constant function and here we can take $$g = 1$$. Back to the condition for the solvability of Eqn. (7.5), we should satisfy the orthogonality equation $$\int 1 \cdot \mathcal{L}_1\rho^{(0)} dp = 0$$. We have already worked out $$\rho^{(0)}$$ in Eqn. (7.5), and further taking the involved $$\phi_{\text{MB}}$$ there given by Eqn. (7.3) while considering the form of operator $$\mathcal{L}_1$$ in Eqn. (7.2), we can obtain,

$$
L_1 \rho^{(0)} \propto p \phi_{\text{MB}}
$$

and therefore the integration boils down to,

$$
\int p \phi_{\text{MB}} dp
$$

Since $$p$$ is an odd function and $$\phi_{\text{MB}}$$ is the Maxwell distribution function which is a symmetric bell curve centered exactly at $$p = 0$$, the integration will be 0. So, the solvability condition is natually satisfied.

<ins>*Solve.*</ins> The useful fact is that $$p\,\phi_{\text{MB}}$$ is an eigenfunction of $$\mathcal L_0$$ with eigenvalue $$-1$$. Verify directly,

$$
\begin{align}
\partial_p\!\left(p\cdot p\phi_{\text{MB}}\right) & = 2p\phi_{\text{MB}} - \frac{p^3}{mk_BT}\phi_{\text{MB}}\\
mk_BT\,\partial_p^2\!\left(p\phi_{\text{MB}}\right) & = mk_BT\,\partial_p\!\left[\phi_{\text{MB}} - \frac{p^2}{mk_BT}\phi_{\text{MB}}\right] = -3p\phi_{\text{MB}} + \frac{p^3}{mk_BT}\phi_{\text{MB}}
\end{align}
$$

Adding up, we have,

$$\mathcal L_0\!\left(p\,\phi_{\text{MB}}\right) = -p\,\phi_{\text{MB}} \tag{7.7}$$

Putting the following results together,

$$
\begin{align}
\mathcal{L}_0(p\phi_{\text{MB}}) & = -p\phi_{\text{MB}}\\
\mathcal{L}_1\rho^{(0)} & = -\frac{pA(x, \tau)}{m}\phi_{\text{MB}}\\
\mathcal{L}_0\rho^{(1)} & = -\mathcal{L}_1\rho^{(0)}
\end{align}
$$

We have the solution of (7.6) given as,

$$\rho^{(1)} = -\frac{A(x,\tau)}{m}\,p\,\phi_{\text{MB}}(p) \tag{7.8}$$

**Order $$\epsilon^2$$.**

$$\partial_\tau\rho^{(0)} = \mathcal L_1\rho^{(1)} + \mathcal L_0\rho^{(2)} \tag{7.9}$$

Integrate over $$p$$. The last term drops ($$\int\mathcal L_0\rho^{(2)}dp = 0$$, since $$\mathcal{L}_0$$ is a total derivative with respect to $$p$$ and therefore $$\int\mathcal{L}_0(\cdot)dp = 0$$), and the left side gives $$\partial_\tau n$$ since $$\int\phi_{\text{MB}}dp=1$$. On the right,

$$\int\mathcal L_1\rho^{(1)}dp = -\frac{1}{m}\partial_x\!\int p\,\rho^{(1)}dp + U'\!\underbrace{\int\partial_p\rho^{(1)}dp}_{=\,0}$$

and, using $$\int p^2\phi_{\text{MB}}dp = mk_BT$$,

$$\int p\,\rho^{(1)}\,dp = -\frac{A}{m}\int p^2\phi_{\text{MB}}\,dp = -A\,k_BT$$

Therefore,

$$\partial_\tau n = \frac{k_BT}{m}\,\partial_x A = \frac{k_BT}{m}\partial_x\!\left[\partial_xn + \frac{U'n}{k_BT}\right]$$

**Step 3 -- restore real time.** With $$\tau = t/\gamma$$, i.e. $$\partial_\tau = \gamma\partial_t$$, and $$\zeta = m\gamma$$,

$$
\frac{\partial n}{\partial t} = \frac{\partial}{\partial x}\left[\frac{U'(x)}{\zeta}n\right] + \frac{k_BT}{\zeta}\frac{\partial^2n}{\partial x^2} \tag{7.10}
$$

This is the **Smoluchowski equation** -- a Fokker–Planck equation in position alone.

Note precisely where the diffusion coefficient (forget about the potential term in Eqn. 7.10, we can see the 1D diffusion equation is reproduced) came from -- it is $$\frac{1}{m^2}\int p^2\phi_{\text{MB}}dp \times \tau_p$$ (refer to the derivation process presented above), i.e. the *equilibrium velocity variance* multiplied by the momentum correlation time. Multiplying an equilibrium variance by a relaxation time is the classic signature of a **Green-Kubo** relation, which proves that macroscopic transport (diffusion) is fundamentally built out of the integral of microscopic equilibrium fluctuations over time.

## 7.4 The overdamped SDE and the Einstein relation

Matching (7.10) against the general Fokker–Planck form (5.1), $$\partial_t\rho = -\partial_x(a\rho) + \partial_x^2(\tfrac{b^2}{2}\rho)$$ (reminded that Eqn. 7.10 is the position part of the Fokker-Planck equation), identifies $$a = -U'/\zeta$$ and $$b^2/2 = k_BT/\zeta$$. The corresponding Itô SDE is

$$
dx = -\frac{U'(x)}{\zeta}\,dt + \sqrt{\frac{2k_BT}{\zeta}}\;dW_t \tag{7.11}
$$

or in the notation of Part II, with $$\zeta = m\gamma$$,

$$\dot x = -\frac{1}{m\gamma}U'(x) + \sqrt{\frac{2k_BT}{m\gamma}}\;\xi(t) \tag{7.11}$$

Defining the **mobility** $$\mu \equiv 1/\zeta$$ (velocity per unit applied force) and the position-space diffusion coefficient

$$D \equiv \frac{k_BT}{\zeta} = \mu k_BT \tag{7.12}$$

recovers the **Einstein relation** [2]. Also refer to the derivation in the highlighted part in §7.2.

## 7.5 Consistency: stationary solution of the Smoluchowski equation

Write (7.10) in continuity form, $$\partial_tn = -\partial_xJ$$ with $$J = -\frac{U'}{\zeta}n - D\,\partial_xn$$. Detailed balance requires $$J = 0$$:

$$D\,n' = -\frac{U'}{\zeta}n \Rightarrow \frac{n'}{n} = -\frac{U'}{\zeta D} = -\frac{U'}{k_BT}$$

$$
n_{\text{eq}}(x) = \frac{1}{Z_x}e^{-\beta U(x)} \tag{7.13}
$$

exactly the position marginal of (6.9). The overdamped reduction preserves the equilibrium distribution, as it must.

## 7.6 Validity criterion

A practical criterion for guaranteeing the validity of the overdamped limit,

$$\tau_p = \frac{m}{\zeta} \;\ll\; \tau_x = \frac{\ell^2}{D} = \frac{\ell^2\zeta}{k_BT}$$

for the smallest length scale $$\ell$$ over which $$U$$ varies appreciably. Equivalently,

$$\frac{m\,k_BT}{\zeta^2\ell^2} \ll 1$$

For the colloidal bead with $$\ell = a = 1\,\mu$$m: $$\tau_p = 0.22\,\mu$$s, $$D = k_BT/\zeta = 2.2\times10^{-13}\,\text{m}^2/\text{s}$$, $$\tau_x = a^2/D \approx 4.6$$ s. The ratio is $$\sim5\times10^{-8}$$ -- overwhelmingly overdamped. For atomistic molecular dynamics, the ratio is order unity or larger, which is why MD codes integrate the full underdamped equations rather than (7.11).

# Part VIII -- Euler–Maruyama Discretization

## 8.1 The scheme

Analytic solutions exist only for linear SDEs. For a general potential, (7.11) must be integrated numerically. The **Euler–Maruyama (EM) scheme** [12] is the stochastic analogue of the [forward Euler method](https://math.libretexts.org/Bookshelves/Differential_Equations/Numerically_Solving_Ordinary_Differential_Equations_(Brorson)/02%3A_Forward_Euler_method) -- for the general SDE $$dx = a(x,t)dt + b(x,t)dW_t$$, fix a step $$\Delta t$$ and iterate

$$x_{k+1} = x_k + a(x_k,t_k)\,\Delta t + b(x_k,t_k)\sqrt{\Delta t}\;z_k, \qquad z_k\sim\mathcal N(0,1)\;\text{i.i.d.} \tag{8.1}$$

> `i.i.d.` $$\Rightarrow$$ independent and identically distributed. In probability theory and statistics, a collection of random variables is i.i.d. if each random variable has the same probability distribution as the others (identically distributed) and all are mutually independent of one another.

Its derivation is immediate from the integral form: over $$[t_k, t_k+\Delta t]$$,

$$x_{k+1} - x_k = \int_{t_k}^{t_k+\Delta t}a\,ds + \int_{t_k}^{t_k+\Delta t}b\,dW_s$$

Freezing the integrands at their **left-endpoint** values (which is exactly the Itô convention, §3.3 -- so EM is intrinsically an Itô scheme) gives $$a(x_k,t_k)\Delta t$$ for the first integral and $$b(x_k,t_k)\,\Delta W_k$$ for the second, with $$\Delta W_k = W_{t_{k+1}}-W_{t_k}\sim\mathcal N(0,\Delta t)$$ sampled as $$\sqrt{\Delta t}z_k$$ per (3.4) in §3.1.

## 8.2 Why the noise scales as $$\sqrt{\Delta t}$$

The single most important structural feature of (8.1) is that the deterministic term carries $$\Delta t$$ while the noise term carries $$\sqrt{\Delta t}$$. This is not a convention -- it is forced by (W3), and it has a decisive consequence.

Consider what happens as $$\Delta t\to0$$ with the noise incorrectly scaled as $$\Delta t$$ instead:

| Scaling used | Behaviour as $$\Delta t\to0$$ | Result |
|---|---|---|
| $$b\,\Delta t\,z$$ | noise vanishes faster than drift | deterministic gradient descent -- converges to a single minimum |
| $$b\sqrt{\Delta t}\,z$$ | noise and drift balance in the FD sense | correct sampler -- explores the full Boltzmann distribution |
| $$b\,\Delta t^{1/4}z$$ | noise dominates | pure random walk; potential is irrelevant |

Only the middle row reproduces the stationary variance derived in Parts IV–VI. **The $$\sqrt{\Delta t}$$ scaling is what makes the algorithm a sampler rather than an optimizer**. According to the table, if the noise term is scaled linearly just as the deterministic term, as $$\Delta t \rightarrow 0$$, the noise term will vanish more quickly since the standard deviation of the random noise scales as $$(\Delta t)^2$$. On the other hand, if the noise term scales as something like $$(\Delta t)^{1/4}$$, it will dominate the process and the whole process therefore just becomes a random walk.

## 8.3 Orders of convergence: strong versus weak

For stochastic processes, we need to consider its accuracy. Two inequivalent notions of accuracy apply to stochastic schemes.

**Strong convergence** measures pathwise accuracy -- how close the numerical path is to the exact path driven by the *same* noise realization:

$$\big\langle\,|x_N - x(T)|\,\big\rangle \le C\,(\Delta t)^{\alpha}$$

**Weak convergence** measures distributional accuracy -- how close the numerical *statistics* are to the exact ones:

$$\Big|\big\langle\phi(x_N)\big\rangle - \big\langle\phi(x(T))\big\rangle\Big| \le C_\phi\,(\Delta t)^{\beta_w}$$

for smooth test functions $$\phi$$. For Euler–Maruyama, the standard results are

$$\alpha = \tfrac12 \quad\text{(strong)}, \qquad \beta_w = 1 \quad\text{(weak)}$$

> Here, $$N$$ refers to the stem $$N$$ in the discrete-time numerical scheme, so that $$T_N = N\Delta t$$.

**Which one matters here?** For sampling applications -- computing thermodynamic averages, or drawing samples from a target distribution -- only the *distribution* matters, never the individual path. Weak order 1 is therefore the relevant figure of merit, and EM's poor strong order is irrelevant.

**A useful special case.** The next-order scheme, **Milstein's method**, adds the correction term $$\tfrac12 b\,\partial_xb\left[(\Delta W)^2 - \Delta t\right]$$ and achieves strong order 1. But for the overdamped Langevin equation (7.11), $$b = \sqrt{2D}$$ is *constant* in $$x$$, so $$\partial_xb = 0$$ and the Milstein term vanishes identically. **For additive noise, Euler–Maruyama is already strong order 1** and Milstein offers nothing. This is a genuine simplification specific to Langevin-type equations.

> See the notes in §3.3 about `additive` and `multiplicative` noise.

## 8.4 The discrete overdamped Langevin update

Applying (8.1) to the overdamped SDE (7.11), with $$a = -U'(x)/\zeta$$ and $$b = \sqrt{2D}$$, $$D = k_BT/\zeta$$:

$$x_{k+1} = x_k - \frac{\Delta t}{\zeta}\,U'(x_k) + \sqrt{\frac{2k_BT\,\Delta t}{\zeta}}\;z_k \tag{8.2}$$

In the statistics literature this is the **Unadjusted Langevin Algorithm (ULA)** [13]; in lattice field theory it appeared earlier as **stochastic quantization** [15]; in machine learning, with a stochastic gradient substituted for $$U'$$, it is **Stochastic Gradient Langevin Dynamics (SGLD)** [14]. All three are (8.2).

Pseudocode:

```
input: potential gradient dU(x), drag ζ, temperature kT, step Δt, n_steps
x ← x_0
for k = 1 … n_steps:
    z ← randn()  # standard normal
    x ← x − (Δt/ζ)·dU(x) + sqrt(2·kT·Δt/ζ)·z
    record x
```

Each iteration costs exactly one gradient evaluation and one Gaussian draw.

## 8.5 Discretization bias: an exactly solvable example

An essential caveat: **the chain (8.2) does not have $$e^{-\beta U}$$ as its exact stationary distribution.** It has a nearby one, differing at $$O(\Delta t)$$. This is quantifiable in closed form for the harmonic well.

Take $$U = \tfrac12kx^2$$, so $$U' = kx$$ and (8.2) becomes a linear recursion:

$$x_{k+1} = a\,x_k + \sqrt{2D\Delta t}\;z_k, \qquad a \equiv 1 - \frac{k\Delta t}{\zeta} \tag{8.3}$$

This is an [first order autoregressive AR(1) process](https://www.econometrics-with-r.org/14.3-autoregressions.html). We can find the stationary variance $$V = \langle x^2 \rangle$$ by taking the variance of both sides at equilibrium,

$$
\begin{align}
V & = a^2 V + (\text{variance of the noise term})
& = a^2V + 2D\Delta t
\end{align}
$$

Solving for $$V$$,

$$
V(1 - a^2) = 2D\Delta t \Rightarrow V_{\text{EM}} = \frac{2D\Delta t}{1 - a^2}
$$

> EM: Euler–Maruyama

Now, substitute $$a = 1 - \frac{k \Delta t}{\zeta}$$ and use $$D = \frac{k_B T}{\zeta}$$,

$$
V_{\text{EM}} = \frac{k_B T}{k} \cdot \frac{1}{1 - \frac{k \Delta t}{2\zeta}}
$$

Using a Taylor expansion for small $$\Delta t$$ ($$\frac{1}{1 - x} \approx 1 + x$$), this becomes,

$$
V_{\text{EM}} = \frac{k_B T}{k} \left( 1 + \frac{k \Delta t}{2\zeta} + \mathcal{O}(\Delta t^2) \right)
$$

For a one-dimensional harmonic oscillator, the potential energy is $$\frac{1}{2}kx^2$$ and from the equipartition theorem, we know that the average potential energy associated with the spatial coordinate $x$ is equal to $$\frac{1}{2}k_B T$$, so,

$$\left\langle \frac{1}{2}kx^2 \right\rangle = \frac{1}{2}k_B T$$

Therefore, $$V_{\text{EM}}$$ can be written as,

$$V_{\text{EM}} = V_{\text{exact}} \left( 1 + \frac{k \Delta t}{2\zeta} + \mathcal{O}(\Delta t^2) \right)$$

where,

$$V_{\text{exact}} = \frac{k_B T}{k}$$

Some takeaways,

- **First-Order Bias:** The error scales as $$\mathcal{O}(\Delta t)$$, which matches the theoretical weak order 1 of the Euler-Maruyama method. Halving $$\Delta t$$ cuts the error in half.

- **Systematically Too Hot:** The bias term is positive ($$+ \frac{k \Delta t}{2\zeta}$$). This means the discrete simulation consistently overestimates the variance -- the system behaves as though it is at a higher temperature than the target $$T$$. Physically, this happens because the deterministic restoring force $-U'(x)$ is evaluated at the beginning of the time step and held constant, missing the continuous pull toward the center during the interval.

- **Not a Transient Effect (i.e., the effect does not depend on a single specific moment):** Because this is a property of the stationary state, running the simulation longer will not make the bias go away. The only ways to fix it are to use a smaller time step $\Delta t$ or employ a higher-order numerical integrator.

## 8.6 Stability limit

The recursion (8.3) is stable only if $$\vert a \vert <1$$:

$$\left|1 - \frac{k\Delta t}{\zeta}\right| < 1 \Leftrightarrow \Delta t < \frac{2\zeta}{k} = \frac{2}{k}\,\zeta \tag{8.5}$$

Beyond this, the iteration oscillates with growing amplitude and diverges. For a general potential, the binding constraint is set by the **stiffest** direction, $$k\to U''_{\max}$$ (remember that here we are dealing with the harmonic oscillator), so the usable step size is governed by the largest curvature anywhere in the landscape, regardless of the presence of noise.

## 8.7 Removing the bias: the Metropolis correction

The $$O(\Delta t)$$ bias can be eliminated exactly by treating (8.2) as a *proposal* in a Metropolis–Hastings scheme rather than as the sampler itself. This is the **Metropolis-Adjusted Langevin Algorithm (MALA)** [13].

Given current state $$x$$, propose

$$x' = x - \frac{\Delta t}{\zeta}U'(x) + \sqrt{2D\Delta t}\,z$$

so the proposal density is the Gaussian

$$q(x'|x) = \mathcal N\!\left(x';\; x - \tfrac{\Delta t}{\zeta}U'(x),\;\; 2D\Delta t\right)$$

Accept $$x'$$ with probability

$$P_{\text{acc}} = \min\left(1,\;\frac{e^{-\beta U(x')}\,q(x\,|\,x')}{e^{-\beta U(x)}\,q(x'\,|\,x)}\right) \tag{8.6}$$

otherwise remain at $$x$$. Because the acceptance test enforces detailed balance with respect to $$e^{-\beta U}$$ exactly, MALA has the *exact* Boltzmann distribution as its stationary state for any $$\Delta t$$ -- the bias of (8.4) is removed entirely.

**The trade-off:** each step now requires an evaluation of $$U$$ itself (not just its gradient), and the acceptance rate falls as $$\Delta t$$ grows. Therefore, the time step must be adjusted to account for different dimension. The [optimal-scaling theory for MALA](
https://doi.org/10.48550/arXiv.1702.01777) in high dimension $$d$$ recommends tuning $$\Delta t$$ to an acceptance rate near $$0.574$$, with $$\Delta t\propto d^{-1/3}$$; unadjusted ULA, by contrast, requires no accept/reject step but tolerates a bias.

<br>

> In practice, targeting a certain acceptance of $$0.574$$ means we need to tune the step size $\Delta t$ for a high-dimensional target distribution until the empirical acceptance rate hovers around the $$0.574$$ threshold.

**Which to use?** In molecular dynamics, the unadjusted scheme is standard, because $$\Delta t$$ is already constrained to be far below the stability limit for accuracy reasons and the residual bias is negligible. In Bayesian sampling with large steps, MALA's exactness is usually worth its cost. In generative modelling (Part IX–X), the unadjusted scheme is used -- the score is only approximate anyway, so an exact accept/reject test against an inexact target buys little.

<br>

(to be continued...)