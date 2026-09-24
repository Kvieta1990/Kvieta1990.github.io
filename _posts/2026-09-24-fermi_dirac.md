---
layout: post
title: Fermi Level and Chemical Potential
subtitle:
tags: [physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

In this post, I am going to put down the derivation of the Fermi-Dirac distribution and talk about the chemical potential and the Fermi level.

## Lagrange multiplier

To prepare for the derivation, we need to first take a look at the mathematical method for finding optima given constraints, using the Lagrange multiplier method. The problem can be put down as below,

- $$f(\mathbf{x})$$ is the function we want to find the optima (maxima or minima) for.

- The constraint condition is $$g(\mathbf{x})$$

This means, along the trace of $$\mathbf{x}$$ on $$g(\mathbf{x})$$, we want to find out the optima for $$f(\mathbf{x})$$. The Lagrange multiplier [1] method says, we can define a function,

$$
\mathcal{L}(\mathbf{x}, \lambda) \equiv  f(\mathbf{x}) + \lambda g(\mathbf{x})
$$

with $$\lambda$$ as the *multiplier* (thus named). For the optimization condition, we have,

$$
\begin{align}
& \Delta f(\mathbf{x}) + \lambda \Delta g(\mathbf{x}) = 0\\
& g(\mathbf{x}) = 0
\end{align}
$$

The condition $$g(\mathbf{x}) = 0$$ is the given constraint so nothing to say about it. But why is the first condition? To see why, I asked Claude to prepare an animation for better understanding and here we go (had to do a few round of back and forth prompting to polish the generation),

{::nomarkdown}
{% include lagrange-widget.html %}
{:/nomarkdown}

The black circle is $$g(\mathbf{x})$$ where the black point has to be constrained on. The <span style="color:#7f77dd"> blue-magenta arrow (&#10230;)</span> indicates the moving direction of the black particle. The <span style="color:#378add"> blue arrow (&#10230;)</span> indicates the direction $$\Delta f(\mathbf{x})$$ -- any movement along this direction will change the value of $$f(\mathbf{x})$$. So, as along as the <span style="color:#7f77dd"> blue-magenta arrow (&#10230;)</span> has a component (indicated by the <span style="color:#ba7517"> brown arrow &#10230;</span>) along the <span style="color:#378add"> blue arrow (&#10230;)</span>, we would always have $$f(\mathbf{x})$$ `not staying stationary`, as the black point moves `infinitesimally`. In another word, the stationary condition is when the <span style="color:#7f77dd"> blue-magenta arrow (&#10230;)</span> is perpendicular to the <span style="color:#378add"> blue arrow (&#10230;)</span>. That is the several points where the animation is paused for a few seconds inferring the stationary condition (thus optima).

Further, since we know that the tangent direction of $$g(\mathbf{x})$$ is always perpendicular to the gradient of $$g(\mathbf{x})$$ (indicated by the <span style="color:#d85a30"> red-orange arrow &#10230;</span>), the stationary condition just becomes that the <span style="color:#d85a30"> red-orange arrow (&#10230;)</span> is parallel or antiparallel to the <span style="color:#378add"> blue arrow (&#10230;)</span>. This is exactly what the first condition equation says, i.e., $$\Delta f(\mathbf{x})$$ is only different from $$\Delta g(\mathbf{x})$$ by a multiplicative factor.

{::nomarkdown}
{% include xy-saddle-3d.html %}
{:/nomarkdown}

Presented in the figure above is another plot I asked Claude to generate to demonstrate the situation. We can see the 3D shape of the function $$f = xy$$ we want to optimize. The <span style="color:#d85a30"> red-orange curve</span> on the surface indicates the constraint that we have to satisfy ($$x^2 + y^2 = 2$$ in this case), with its projection onto the $$XY$$ plane shown with the dashed circle. The several optima points are also labeled out, corresponding to the exactly the condition we identified earlier.

## Fermi-Dirac distribution

This is the problem we want to address here -- we want to distribute $$N$$ fermions among energy levels, subject to two constraints, and it will turn into a Lagrange optimization problem.

Say we have a series of energy levels indexed by $$i$$. Each level comes with the degeneracy of $$g_i$$, and we have $$n_i$$ fermions that we want to distribute among those $$g_i$$ states on the level $$i$$. The Pauli exclusion principle allows at most one fermion per state, so the number of ways to arrange $n_i$ fermions among $g_i$ states is $$\binom{g_i}{n_i}$$ (or, $$C_{g_i}^{n_i}$$, i.e., the number of ways of picking $$n_i$$ levels from the total $$g_i$$ levels). Given all the different $$i$$ levels, we have,

$$
W = \prod_i \frac{g_i!}{n_i!\,(g_i - n_i)!}
$$

:::info
Here are dealing with the grand canonical ensemble so both $$N$$ and $$E$$ are allowed to change.
:::

We want to maximize $\ln W$ (equivalently the entropy $S = k_B \ln W$), with the following two constraints,

$$
\sum_i n_i = N, \qquad \sum_i n_i \varepsilon_i = E
$$

This now becomes a standard Lagrange multiplier problem,

$$
\mathcal{L} = \ln W - \alpha\left(\sum_i n_i - N\right) - \beta\left(\sum_i n_i\varepsilon_i - E\right).
$$

Using Stirling's approximation $\ln x! \approx x\ln x - x$, we have,

$$
\begin{align}
\ln\,W & = \ln\prod_i \frac{g_i!}{n_i!\,(g_i - n_i)!}\\
& = \sum_i \ln \frac{g_i!}{n_i!\,(g_i - n_i)!}\\
& = \sum_i \Big[ \ln\,g_i! - \ln\,n_i! - \ln\,(g_i - n_i)! \Big]\\
& = \sum_i \Big[ g_i \ln g_i - n_i \ln n_i - (g_i - n_i)\ln(g_i - n_i) \Big]
\end{align}
$$

Setting $\partial \mathcal{L}/\partial n_i = 0$,

$$
\ln\frac{g_i - n_i}{n_i} - \alpha - \beta\varepsilon_i = 0
\Rightarrow
\frac{n_i}{g_i} = \frac{1}{e^{\alpha + \beta\varepsilon_i} + 1}.
$$

Now we need to work out $$\alpha$$ and $$\beta$$. To do that, let's say we have the total number of fermions $$N$$ and the total energy $$E$$ are changed a bit. Accordingly we should have the optimal occupation $$n_i$$ changed by a bit ($$d\,n_i$$). Given the expression for $$\ln\,W$$ above, we have,

$$
\begin{align}
d\ln W & = \sum_i \frac{\partial \ln\,W}{\partial n_i} d\,n_i\\
& = \sum_i \Bigg\{ \bigg\{ -\ln\,n_i - n_i\frac{1}{n_i} - \big[(g_i - n_i) \frac{-1}{g_i - n_i} - \ln(g_i - n_i)\big]\bigg\} d\,n_i \Bigg\}\\
& = \sum_i \ln\bigg( \frac{g_i - n_i}{n_i} \bigg)d\,n_i
\end{align}
$$

From the optimal condition earlier, we already know,

$$
\ln\bigg( \frac{g_i - n_i}{n_i} \bigg) = \alpha + \beta \varepsilon_i
$$

so,

$$
d\ln W = \alpha \sum_i dn_i + \beta \sum_i \varepsilon_i\, dn_i = \alpha\, dN + \beta\, dE
$$

Therefore, we have,

$$
\begin{align}
\left(\frac{\partial \ln W}{\partial N}\right)_E & = \alpha\\
\left(\frac{\partial \ln W}{\partial E}\right)_N & = \beta
\end{align}
$$

Compare this with the thermodynamic identity for the grand canonical ensemble,

$$
\begin{align}
S & = k_B\ln W\\
dS & = \frac{1}{T}\,dE - \frac{\mu}{T}\,dN
\end{align}
$$

we have,

$$
\begin{align}
\bigg(\frac{\partial S}{\partial E}\bigg)_N & = k_B\bigg(\frac{\partial \ln W}{\partial E}\bigg)_N = \frac{1}{T}\\
\bigg(\frac{\partial S}{\partial N}\bigg)_W & = k_B\bigg(\frac{\partial \ln W}{\partial N}\bigg)_E = -\frac{\mu}{T}
\end{align}
$$

Pulling in the results we just derived above, we can obtain,

$$
\begin{align}
\beta & = \frac{1}{k_BT}\\
\alpha &= -\frac{\mu}{k_BT}
\end{align}
$$

Now, we have the final form for the Fermi-Dirac distribution,

$$
f(\varepsilon) = \frac{1}{e^{(\varepsilon - \mu)/k_BT} + 1}
$$

## Chemical potential & Fermi level

According to the grand canonical ensemble, the chemical potential is,

$$
\mu = \left(\frac{\partial F}{\partial N}\right)_{T,V}
$$

meaning that it is the free energy cost of adding one more particle. Specifically for the Fermi-Dirac distribution, we have,

$$
N = \int_{-\infty}^{\infty} g(\varepsilon)\, f(\varepsilon;\mu,T)\, d\varepsilon
$$

If we want to keep the number of fermions fixed (it is allowed to change but nothing prevents us from focusing on the situation where it does not change), as the temperature $$T$$ varies, the value of $$\mu$$ has to be changed accordingly to keep $$N$$ fixed. For example, as $$T$$ increases, the Fermi-Dirac distribution becomes more flattened, meaning that the probability of occupying high energy level is increased. Since $$f$$ is antisymmetry about $$\mu$$, the increased probability above $$\mu$$ will be equal to the decreased probability below $$\mu$$. However, the density of states $$g(\varepsilon)$$ is not symmetric around $$\mu$$ and is larger above $$\mu$$ than below. As a result, if $$\mu$$ is fixed, the increasing of temperature $$T$$ will cause the creation of fermions out of nowhere (this is contradictory to our prerequisite assumption that we don't have particle exchange with surroundings). To compromise, $$\mu$$ has to decrease. Then if we take the same reference point as before, the area below that reference will be larger than that above the reference, balancing $$g(\varepsilon)$$.

<br>

{::nomarkdown}
{% include fd_delta_f_animation.html %}
{:/nomarkdown}

Again, an animation will tell more than words. Originally at $$T = 0$$, we have all fermions occupying the level below $$E_F$$, and this is exactly the definition of the Fermi level. As $$T$$ increases, fermions will tend to be excited to higher energy beyond the Fermi level $$E_F$$. As presented above, the blue region is for the gaining of fermions, thus corresponding to $$f(E)$$, beyond $$E_F$$ and the orange region represents the loss (thus corresponding to $$1 - f(E)$$, and it is mirrored to below the x-axis to emphsize that it represents the loss) of fermions (where those gained electrons beyond $$E_F$$ are draining from). As can be seen, the region where $$\mu < E < E_F$$ now belongs to the loss region, and accordingly, the area of the loss region is now larger than that of gaining region. This will then balance out the asymmetric $$g(E)$$ so the overal gaining and losing can be balanced.

Last but not least, when $$T = 0$$, as shown in the figure, the chemical potential $$\mu$$ equals the Fermi level $$E_F$$. More rigorously, we should say, as $$T \rightarrow 0$$, $$\mu \rightarrow E_F$$. Looking at the Fermi-Dirac distribution, as $$T \rightarrow 0$$, if $$\varepsilon - \mu > 0$$, the exponent tends to $$\infty$$, making $$f(\varepsilon) \rightarrow 0$$. This means no energy levels beyond $$\mu$$ will be likely to be occupied -- this is exactly the definition of the Fermi level $$E_F$$. As discussed above, with the increasing of $$T$$, levels above $$\mu$$ are likely to be occupied and accordingly we have $$\mu$$ shifting away from $$E_F$$. The Fermi level, though, stays the same, since it is *defined* as such that if we fill the energy levels, totally regardless of the probability of the level being occupied, from low to high, the highest level we can get to, is the Fermi level.

## References

[1] [Lagrange multiplier](https://en.wikipedia.org/wiki/Lagrange_multiplier)