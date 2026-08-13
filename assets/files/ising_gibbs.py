"""
Gibbs sampling for the 2D Ising model.

Joint distribution over L*L spins s_i in {-1, +1}:

    p(s) = (1/Z) * exp(-beta * H(s))
    H(s) = -J * sum_{<i,j>} s_i s_j  -  h * sum_i s_i

Z sums over 2^(L*L) configurations -> intractable for anything but tiny L.
But the full conditional for one spin, given all others, depends only on
its 4 lattice neighbors:

    p(s_i = +1 | s_{-i}) = sigmoid(2 * beta * local_field_i)
    local_field_i = J * (sum of 4 neighbor spins) + h

That conditional is cheap to sample from, so we can Gibbs-sample the whole
lattice by sweeping over sites and re-drawing each spin from its conditional
given the CURRENT values of its neighbors (periodic boundary conditions).
This is the classic "heat-bath" Monte Carlo algorithm.
"""

import numpy as np
import matplotlib.pyplot as plt


def gibbs_ising(L=20, beta=0.45, J=1.0, h=0.0, n_sweeps=500, seed=0):
    """Run Gibbs (heat-bath) sampling on an L x L Ising lattice.

    Returns the final spin configuration and the magnetization trace
    (mean spin value) after each full sweep of the lattice.
    """
    rng = np.random.default_rng(seed)
    spins = rng.choice([-1, 1], size=(L, L))
    mags = np.empty(n_sweeps)

    for sweep in range(n_sweeps):
        for i in range(L):
            for j in range(L):
                # sum of the 4 nearest neighbors, periodic boundaries
                neighbor_sum = (
                    spins[(i + 1) % L, j] + spins[(i - 1) % L, j]
                    + spins[i, (j + 1) % L] + spins[i, (j - 1) % L]
                )
                local_field = J * neighbor_sum + h
                p_up = 1.0 / (1.0 + np.exp(-2 * beta * local_field))
                spins[i, j] = 1 if rng.random() < p_up else -1
        mags[sweep] = spins.mean()

    return spins, mags


if __name__ == "__main__":
    # J=1 critical inverse temperature for the 2D Ising model is
    # beta_c = ln(1+sqrt(2))/2 ~= 0.4407. beta=0.45 puts us just below
    # criticality, in the ordered (magnetized) phase, so you can watch
    # the sampler "commit" to a dominant magnetization direction.
    L = 20
    spins, mags = gibbs_ising(L=L, beta=0.45, n_sweeps=500, seed=0)

    fig, axes = plt.subplots(1, 2, figsize=(10, 4))

    axes[0].imshow(spins, cmap="coolwarm", vmin=-1, vmax=1)
    axes[0].set_title(f"Final spin configuration ({L}x{L})")
    axes[0].set_xticks([])
    axes[0].set_yticks([])

    axes[1].plot(mags)
    axes[1].axhline(0, color="gray", lw=0.5)
    axes[1].set_xlabel("Sweep")
    axes[1].set_ylabel("Magnetization (mean spin)")
    axes[1].set_title("Convergence via Gibbs sampling")

    plt.tight_layout()
    plt.savefig("ising_gibbs.png", dpi=150)
    print("Final magnetization:", mags[-1])
    print("Saved plot to ising_gibbs.png")
