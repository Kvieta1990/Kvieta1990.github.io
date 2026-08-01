---
layout: post
title: Markov Chain Monte Carlo -- An Introduction
subtitle:
tags: [maths, physics, statistics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

This post is written by both AI and myself in a 'collaborative' way. I was learning through the sampling technique with Claude AI and was asking quite a lot questions back and forth, trying to understand each of the pieces clearly. After that, I asked Claude AI to summarize the conversation in an introductory material format and I gave it detailed insturctions and logic about how to compose the story flow. Then finally I was reading through the material, making corrections that I feel necessary and noting down my understanding of certain aspects as side notes.

## 1. The Basic Markov Chain Monte Carlo Algorithm

Markov Chain Monte Carlo (MCMC) is a method for drawing samples from a target distribution $$p(x)$$ that may be difficult to sample from directly. The method constructs a sequence of samples $$x_0, x_1, x_2, \dots$$ using a transition rule $$x_{t+1} \sim T(x_{t+1} \mid x_t)$$, designed so that the distribution of $$x_t$$ converges to $$p(x)$$ regardless of the starting point. Once convergence occurs, each subsequent $$x_t$$ constitutes a (correlated) sample from $$p(x)$$.

The transition rule followed by the chain algorithm is only comparing whether the proposed sample is more probable as compared to the current state of the system, and therefore the absolute value of the state possibility does not matter that much. For example, for a canonical ensemble that follows the Boltzmann distribution,

$$
f(x) = \frac{1}{\mathcal{Z}}e^{-E(x)/(k_BT)}
$$

it is, in practice, impossible to obtain the partition function since that will require going over all the possibilities of the system state -- the partition function is nothing but a summation of probabilities of all possible system states. The MCMC sampling algorithm solves such a problem by checking and comparing the relative ratio between the possibility of the proposed state and the current state, in which case the normalization factor (i.e., the partition function which we cannot obtain in practice) will be cancelled out. Let's see how the algorithm works out in practice.

### 1.1 The Metropolis-Hastings Procedure

1. Start at some state $$x_t$$.
2. Propose a new state $$x^* \sim q(x^* \mid x_t)$$ -- commonly a small random perturbation, e.g., $$x^* = x_t + \epsilon$$, $$\epsilon \sim \mathcal{N}(0, \sigma^2)$$.

    > Here, $$x^* \sim q(x^* \mid x_t)$$ reads like "starting from the current state $$x_t$$, we want to propose a new state $$x^*$$ and the way of proposing the new state $$x^*$$ follows the distribution $$q$$".

3. Compute the acceptance ratio:
$$\alpha = \min\left(1, \frac{p(x^*)\, q(x_t \mid x^*)}{p(x_t)\, q(x^* \mid x_t)}\right)$$
   For a symmetric proposal (where $$q(x^*|x_t) = q(x_t|x^*)$$), this reduces to the simpler Metropolis ratio:
$$\alpha = \min\left(1, \frac{p(x^*)}{p(x_t)}\right)$$

    > Remember that $$p$$ here refers to the probability of states, namely $$x_t$$, $$x^*$$, etc.
    
    > Here we mentioned the assumption of the symmetric proposition of new states. In section-3, we will be discussing what we mean by an `asymmetryic` version.
    
    > In section-2.2, we will be discussing why the acceptance ratio is like that and in section-2.3, we will be explaining why having the proposition probability in the formulation is necessary in terms of asymmetric proposition of new states.

4. Draw $$u \sim \text{Uniform}(0,1)$$. If $$u \le \alpha$$, accept: $$x_{t+1} = x^*$$. Otherwise, reject: $$x_{t+1} = x_t$$ (the chain remains at the current state, which counts as a repeated sample rather than a discarded draw).
5. Repeat.

### 1.2 A Concrete Example: A Discrete Three-State Chain

Consider a target distribution over three states A, B, C with probabilities $$p = (0.2, 0.5, 0.3)$$. Suppose the proposal rule moves to one of the other two states with equal probability (a symmetric proposal). Starting at A:

- Propose B. Ratio: $$\min(1, p(B)/p(A)) = \min(1, 0.5/0.2) = 1$$. Moves toward more probable states are always accepted.
- From B, propose C. Ratio: $$\min(1, 0.3/0.5) = 0.6$$. A uniform draw determines acceptance; the move succeeds 60% of the time.
- If rejected, the chain remains at B (counted again as the next sample).

Running this process for many steps and tallying the frequency of visits to each state shows convergence to (0.2, 0.5, 0.3) in the long run -- the target probabilities emerge from the accept/reject ratios rather than being chosen directly.

### 1.3 Another Concrete Example: Sampling a Boltzmann Distribution

This example corresponds directly to Metropolis Monte Carlo sampling of particle configurations in statistical mechanics.

Target: the canonical distribution $$p(\mathbf{r}) \propto e^{-U(\mathbf{r})/k_BT}$$ over particle configurations $$\mathbf{r}$$, where $$U$$ is potential energy.

1. Current configuration $$\mathbf{r}_t$$, energy $$U_t$$.
2. Propose a small random displacement of one particle: $$\mathbf{r}^* = \mathbf{r}_t + \delta$$, with new energy $$U^*$$.
3. Since this proposal is symmetric, the acceptance ratio simplifies to:
$$\alpha = \min\left(1, \frac{e^{-U^*/k_BT}}{e^{-U_t/k_BT}}\right) = \min\left(1, e^{-(U^*-U_t)/k_BT}\right)$$
   - A move that *lowers* energy ($$\Delta U < 0$$) is always accepted ($$\alpha=1$$).
   - A move that *raises* energy ($$\Delta U > 0$$) is accepted with probability $$e^{-\Delta U/k_BT}$$ -- the Boltzmann factor itself. The chain naturally spends more time in low-energy states without ever requiring the partition function $$Z$$, which cancels in the ratio.
4. Accept or reject via a uniform draw; repeat.

---

## 2. Detailed Balance and Global Balance

### 2.1 The Goal: An Invariant Distribution

The construction of an MCMC algorithm aims to leave $$p(x)$$ invariant under the chain's transitions. A convenient sufficient condition for this is **detailed balance**:

$$p(x_t)\, T(x^* \mid x_t) = p(x^*)\, T(x_t \mid x^*)$$

This states that the probability "flow" from state $$x_t$$ to $$x^*$$ equals the flow in the reverse direction, from $$x^*$$ to $$x_t$$. When this holds for every pair of states, $$p(x)$$ is guaranteed to be a stationary distribution of the chain.

### 2.2 Splitting the Transition into Proposal and Acceptance

The transition $$T(x^* \mid x_t)$$ is constructed from two components:

$$T(x^* \mid x_t) = q(x^* \mid x_t)\, \alpha(x^*, x_t)$$

- $$q(x^*\mid x_t)$$ -- the proposal distribution: the mechanism for generating a candidate move.
- $$\alpha(x^*, x_t)$$ -- the acceptance probability: whether the move is taken.

The proposal $$q$$ can be freely chosen; the acceptance probability $$\alpha$$ must be selected so that the resulting transition $$T$$ satisfies detailed balance, correcting for any imbalance introduced by the proposal mechanism.

### 2.3 Deriving the Acceptance Ratio

Substituting the split into the detailed balance condition:

$$p(x_t)\, q(x^*\mid x_t)\, \alpha(x^*, x_t) = p(x^*)\, q(x_t \mid x^*)\, \alpha(x_t, x^*)$$

Rearranging:

$$\frac{\alpha(x^*, x_t)}{\alpha(x_t, x^*)} = \frac{p(x^*)\, q(x_t \mid x^*)}{p(x_t)\, q(x^* \mid x_t)}$$

Choosing the largest possible values of $$\alpha$$ consistent with this ratio, capped at 1, gives the Metropolis-Hastings acceptance rule:

$$\alpha(x^*, x_t) = \min\left(1,\ \frac{p(x^*)\, q(x_t \mid x^*)}{p(x_t)\, q(x^* \mid x_t)}\right)$$

The two factors have distinct roles:
- $$\dfrac{p(x^*)}{p(x_t)}$$ -- the target ratio: whether the proposed state is more or less probable than the current one.
- $$\dfrac{q(x_t \mid x^*)}{q(x^* \mid x_t)}$$ -- the proposal correction: accounts for any asymmetry in how easily the forward move is proposed compared to the reverse move.

For a symmetric proposal, the correction term equals 1, recovering the simpler Metropolis ratio. A concrete case where this correction term becomes necessary arises when sampling a positive-only quantity, such as a temperature or a bond length, using a log-normal proposal: $$x^* = x_t \cdot e^{\epsilon}$$, $$\epsilon \sim \mathcal{N}(0,\sigma^2)$$. This proposal is not symmetric -- jumping from a small $$x_t$$ to a large $$x^*$$ has a different density than jumping back. In this case the Hastings correction term $$q(x_t\mid x^*)/q(x^*\mid x_t)$$ is not equal to 1 and must be included. This example is developed in full detail in Section 3.

### 2.4 How Detailed Balance Guarantees Global Balance

Back in section-2.1, we mentioned that the detailed balance is the sufficient condition to guarantee that the Markov chain transition yields stable/invariant distribution $$px(x)$$ of the system. To see why, we need to look at the **global balance equation**:

$$
p_{t+1}(x') = \sum_x p_t(x)\, T(x' \mid x)
$$

which basically says the state $$x'$$ at the step $$t + 1$$ could possibly come from any of the system states $$x$$ at the step $$t$$. Each of the state $$x$$ has a certain probability transitioning to the state $$x'$$ and naturally the distribution $$p_{t+1}(x')$$ is given by the summation presented above. Further, stationarity requires that if $$p_t = p$$, then $$p_{t+1} = p$$ as well:

$$p(x') = \sum_x p(x)\, T(x' \mid x) \quad \text{for all } x'$$

meaning that at step $$t$$, the system distribution follows $$p(x)$$ and after the transition, the system ends up with an identical distribution as before. Then we can show that the detailed balance is a stronger, easier-to-verify condition that implies this. Starting from detailed balance holding for every pair,

$$p(x)\, T(x' \mid x) = p(x')\, T(x \mid x') \quad \text{for every pair } x, x'$$

summing both sides over all $$x$$ gives:

$$\sum_x p(x)\, T(x' \mid x) = \sum_x p(x')\, T(x \mid x') = p(x') \sum_x T(x \mid x') = p(x') \cdot 1 = p(x')$$

The last step uses the fact that $$T(x\mid x')$$, summed over all possible destinations, equals 1. The left-hand side is exactly $$p_{t+1}(x')$$ when $$p_t = p$$, so $$p_{t+1}(x') = p(x')$$ follows directly. Detailed balance, summed over one variable, collapses into global balance -- proving detailed balance for every pair of states is a stronger requirement than proving global balance alone, but automatically satisfies it.

### 2.5 What "Stationary" Actually Means

The distribution $$p(x)$$ should be understood not as the outcome being generated, but as a **distribution over where the chain could be at a given time**. For sure, when running the chain once, at step $$t$$, we will end up with the system being at a certain state $$x$$ but that is not what we mean by $$p(x)$$, which is actually a distribution. Then how to understand it? Well, we can imagnine running the chain multiple times and every time running the chain, at step $$t$$, the system will end up with different states. Across all the chain runs, we will have a distribution over the system states, and such a distribution is represented by the $$p(x)$$ function.

$$p(x)$$ is a stationary distribution of the chain if, whenever $$p_t(x) = p(x)$$ for all $$x$$, it remains true that $$p_{t+1}(x) = p(x)$$ for all $$x$$. Applying one further step of the chain does not change the distribution once it matches $$p(x)$$. This is different from saying "the chain is stuck." The chain itself keeps moving around -- the state at time $$t$$ might be A, and the state at time $$t+1$$ might be B. What is fixed is the probability distribution describing the ensemble of all possible chains: if millions of independent copies of the chain were run simultaneously, each bouncing around on its own, the fraction of copies sitting at each state would stay constant at $$p(x)$$, even though any individual copy keeps hopping between states.

### 2.6 Illustration with The Crowd of Walkers

Let's now make it more concrete by talking more about the multiple runs of the chain mentioned in the previous section. Imagine we have a crowd of 1000 independent copies of the chain running at the same time, each following the same rules (the same proposal, the same accept/reject criterion) but making its own independent random choices. At any step $$t$$, each of the 1000 walkers occupies some state. The quantity $$p_t(x)$$ is then simply a headcount, divided by 1000: the fraction of walkers currently sitting at state $$x$$. Advancing every walker by one step (each proposing a move and accepting or rejecting according to the rule) and recounting gives $$p_{t+1}(x)$$. In general, this headcount changes between steps. Stationarity describes the special case where the headcount, once matching the target proportions, remains at those proportions after every walker takes one more step. Individual walkers continue to move -- someone leaving state A is replaced by someone else arriving at A from another state, in equal amount. This resembles a busy establishment with a stable total headcount: people are constantly entering and leaving, but if arrivals at each position always match departures, the overall occupancy pattern appears frozen from a distance even though every individual keeps moving.

Detailed balance is what enforces this replacement exactly: it states that the number of walkers flowing from state A to state B per step equals the number flowing from B back to A per step, and likewise for every other pair. If every pairwise flow is exactly balanced in this way, no state's total headcount can change, since every walker leaving a state is replaced by one arriving.

Although the crowd-of-walkers description involves many parallel chains, in practice a single chain is run for a long time. If the chain runs long enough, the fraction of time a single chain spends at each state converges to the target proportions given by $$p(x)$$, for a closely related reason: once in equilibrium, the chain leaves each state as often as it returns to it, so long-run time averages settle at the target proportions. The crowd-of-walkers picture and the single-long-chain picture are two views of the same underlying fact, an equivalence referred to as ergodicity.

### 2.7 Some more Notes

Detailed balance proves that **if** the chain is already distributed according to $$p(x)$$ at time $$t$$, it remains distributed according to $$p(x)$$ at time $$t+1$$. It's a statement about $$p(x)$$ being a *fixed point* — not a statement that every step, starting from wherever the chain happens to be, already follows $$p(x)$$. In practice, the chain typically starts somewhere arbitrary (e.g., an initial guess, an arbitrary configuration), and the early steps do **not** follow $$p(x)$$ at all — they follow whatever distribution results from the initial condition, gradually reshaping step by step. This is exactly the "burn-in" period mentioned earlier: the first N steps are discarded because they haven't converged yet. So it isn't that detailed balance forces every step to already match $$p(x)$$ — it forces $$p(x)$$ to be a *stable resting point* that the chain approaches and then stays at, once reached.

Detailed balance only proves that $$p(x)$$ *is a* stationary distribution — it says nothing about whether the chain actually gets there from an arbitrary start, or whether it visits every state at all.

**Ergodicity** is a separate, additional requirement — informally, it means the chain is:
- *irreducible*: any state can eventually be reached from any other state (no isolated pockets the chain can get trapped in), and
- *aperiodic*: the chain doesn't cycle through states in a rigid repeating pattern.

Only when the chain satisfies *both* detailed balance (guaranteeing $$p(x)$$ is a fixed point) *and* ergodicity (guaranteeing the chain actually explores the whole space and converges to that fixed point from anywhere) does the long-run behavior work out. A poorly designed proposal (e.g., one that can never propose moves between two disconnected regions of state space) can satisfy detailed balance perfectly on the reachable states while still failing to converge to the true $$p(x)$$, because whole regions are never visited.

### 2.8 Worked Numerical Example

Here we use the three-state chain from Section 1.2 to illustrate all of the above concretely.

**Setup.** Proposal: from any state, propose one of the other two states with equal probability, $$q = 0.5$$ each way. Target: $$p = (0.2, 0.5, 0.3)$$ for states A, B, C.

**Step 1: Acceptance probabilities.** For each ordered pair, $$\alpha(x \to x') = \min(1, p(x')/p(x))$$:

| Move | Ratio $$p(x')/p(x)$$ | $$\alpha$$ |
|---|---|---|
| A→B | 0.5/0.2 = 2.5 | 1 |
| B→A | 0.2/0.5 = 0.4 | 0.4 |
| A→C | 0.3/0.2 = 1.5 | 1 |
| C→A | 0.2/0.3 = 0.667 | 0.667 |
| B→C | 0.3/0.5 = 0.6 | 0.6 |
| C→B | 0.5/0.3 = 1.667 → capped | 1 |

**Step 2: Full transition probabilities**, $$T(x'\mid x) = q \cdot \alpha$$:

$$
T(B|A)=0.5{\times}1=0.5 \qquad T(A|B)=0.5{\times}0.4=0.2
$$

$$T(C|A)=0.5{\times}1=0.5 \qquad T(A|C)=0.5{\times}0.667=0.333$$

$$T(C|B)=0.5{\times}0.6=0.3 \qquad T(B|C)=0.5{\times}1=0.5$$

And the "stay put" probabilities, since each row must sum to 1:

$$T(A|A) = 1 - 0.5 - 0.5 = 0$$

$$T(B|B) = 1 - 0.2 - 0.3 = 0.5$$

$$T(C|C) = 1 - 0.333 - 0.5 = 0.167$$

**Step 3: Detailed balance check for each pair.**

A ↔ B:

$$
p(A)T(B|A) = 0.2 \times 0.5 = 0.1
$$

and

$$
p(B)T(A|B) = 0.5 \times 0.2 = 0.1
$$

matches.

A ↔ C:

$$
p(A)T(C|A) = 0.2 \times 0.5 = 0.1
$$

and

$$
p(C)T(A|C) = 0.3 \times 0.333 = 0.1
$$

matches.

B ↔ C:

$$
p(B)T(C|B) = 0.5 \times 0.3 = 0.15
$$

and

$$
p(C)T(B|C) = 0.3 \times 0.5 = 0.15
$$

matches.

Every pairwise flow balances exactly.

**Step 4: Global balance check for state B.**

$$p(B) \stackrel{?}{=} p(A)T(B|A) + p(B)T(B|B) + p(C)T(B|C)$$

$$= (0.2 \times 0.5) + (0.5 \times 0.5) + (0.3 \times 0.5) = 0.1 + 0.25 + 0.15 = 0.5$$

This equals $$p(B) = 0.5$$, confirming global balance.

Decomposing the total: the terms $$0.1$$ (inflow from A) and $$0.15$$ (inflow from C) are exactly the pairwise flow values verified in Step 3, and $$0.25 = p(B)T(B\mid B)$$ is the mass that remained at B. Since flow(A→B) was already shown to equal flow(B→A), and flow(C→B) equal to flow(B→C), the inflow to B from its neighbors automatically equals the outflow from B to its neighbors, so B's total headcount cannot change. Global balance is not a separate fact requiring independent verification -- it follows automatically once every pairwise flow is individually balanced. Proving the three pairwise equalities (A↔B, A↔C, B↔C) simultaneously proves all three global balance equations, for A, B, and C.

---

## 3. Asymmetric Proposals: The Log-Normal Case

The Metropolis-Hastings acceptance ratio includes a proposal correction term precisely to accommodate proposals that are not symmetric. A concrete example of such a proposal is the log-normal proposal, commonly used when sampling a strictly positive quantity such as a temperature or a bond length:

$$x^* = x_t \cdot e^{\epsilon}, \qquad \epsilon \sim \mathcal{N}(0, \sigma^2)$$

### 3.1 Deriving the Proposal Density

Since $$\epsilon = \ln(x^*/x_t)$$, converting the Gaussian density on $$\epsilon$$ into a density on $$x^*$$ requires a change-of-variables (Jacobian) factor:

$$q(x^* \mid x_t) = \frac{1}{x^*}\cdot\frac{1}{\sqrt{2\pi\sigma^2}}\exp\!\left(-\frac{(\ln(x^*/x_t))^2}{2\sigma^2}\right)$$

The leading $$\frac{1}{x^*}$$ term arises directly from this transformation.

Applying the same reasoning in reverse, treating $$x^*$$ as the starting point and $$x_t$$ as the proposed state, gives:

$$q(x_t \mid x^*) = \frac{1}{x_t}\cdot\frac{1}{\sqrt{2\pi\sigma^2}}\exp\!\left(-\frac{(\ln(x_t/x^*))^2}{2\sigma^2}\right)$$

Because the Gaussian density is symmetric, $$f(-\epsilon) = f(\epsilon)$$, the exponential portions of both expressions are identical. The two densities differ only in their prefactors, $$1/x^*$$ versus $$1/x_t$$.

### 3.2 The Resulting Asymmetry

Taking the ratio of the two proposal densities:

$$\frac{q(x_t \mid x^*)}{q(x^* \mid x_t)} = \frac{1/x_t}{1/x^*} = \frac{x^*}{x_t}$$

Unless $$x^* = x_t$$, this ratio does not equal 1 -- the proposal is asymmetric, and the discrepancy arises entirely from the $$1/x$$ Jacobian factor rather than from the underlying Gaussian noise itself.

### 3.3 Intuitive Explanation

Considering the proposal in log-space clarifies the source of the asymmetry. Equal-sized jumps in $$\ln x$$ are symmetric, as guaranteed by the Gaussian distribution on $$\epsilon$$. However, a fixed jump in $$\ln x$$ corresponds to a different absolute jump in $$x$$ depending on the starting point:

- Starting at $$x_t = 1$$: a jump of $$\epsilon = \ln 2 \approx 0.69$$ leads to $$x^* = 2$$, an absolute change of +1.
- Starting at $$x_t = 10$$: the same $$\epsilon = \ln 2$$ leads to $$x^* = 20$$, an absolute change of +10.

A proposal that is symmetric in log-space therefore becomes asymmetric in linear space -- a large absolute jump is easy to propose from a large starting value, since multiplicative steps scale with the current value, but achieving the same absolute-sized jump back from a small starting value would require a much larger relative (log) jump, which is far less probable under the same $$\epsilon \sim \mathcal{N}(0,\sigma^2)$$. The forward and backward ease of proposing genuinely differ, and this is exactly what the $$x^*/x_t$$ correction factor compensates for in the Hastings ratio.

### 3.4 Consequence for Detailed Balance

Omitting the Hastings correction term and using the plain Metropolis ratio $$\min(1, p(x^*)/p(x_t))$$ with this proposal would violate detailed balance: the forward and backward flows would no longer match pairwise, and the chain would converge to a distribution subtly different from the intended $$p(x)$$ -- systematically over- or under-visiting large-$$x$$ states, since jumps toward large $$x$$ are proposed more readily than jumps back. Including the Hastings correction term repairs this discrepancy, restoring detailed balance and, with it, the correct stationary distribution.