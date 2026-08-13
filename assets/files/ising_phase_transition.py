"""
Sweep beta across the 2D Ising critical point and watch the magnetization
phase transition emerge purely from Gibbs sampling.

Uses a checkerboard (red-black) Gibbs update: on a bipartite lattice,
all sites of one color are conditionally independent given the other
color (their neighbors are always the opposite color), so we can draw
an entire sublattice's spins in one vectorized step. This is still
exactly single-site Gibbs sampling -- just batched for speed.
"""

import numpy as np
import matplotlib.pyplot as plt


def make_checkerboard_mask(L):
    idx = np.arange(L)
    ii, jj = np.meshgrid(idx, idx, indexing="ij")
    return (ii + jj) % 2 == 0  # True = "black" sublattice


def gibbs_ising_checkerboard(L, beta, J=1.0, h=0.0, n_sweeps=300, seed=0):
    rng = np.random.default_rng(seed)
    spins = rng.choice([-1, 1], size=(L, L)).astype(np.int8)
    black = make_checkerboard_mask(L)
    white = ~black
    mags = np.empty(n_sweeps)

    for sweep in range(n_sweeps):
        for mask in (black, white):
            neighbor_sum = (
                np.roll(spins, 1, axis=0) + np.roll(spins, -1, axis=0)
                + np.roll(spins, 1, axis=1) + np.roll(spins, -1, axis=1)
            )
            local_field = J * neighbor_sum + h
            p_up = 1.0 / (1.0 + np.exp(-2 * beta * local_field))
            draws = rng.random((L, L)) < p_up
            new_spins = np.where(draws, 1, -1).astype(np.int8)
            spins = np.where(mask, new_spins, spins)
        mags[sweep] = spins.mean()

    return spins, mags


if __name__ == "__main__":
    L = 24
    n_sweeps = 300
    burn = 150
    beta_c = np.log(1 + np.sqrt(2)) / 2  # ~0.4407 for J=1, h=0

    betas = np.linspace(0.2, 0.7, 26)
    mean_abs_mag = np.empty_like(betas)
    std_mag = np.empty_like(betas)

    for k, beta in enumerate(betas):
        _, mags = gibbs_ising_checkerboard(L, beta, n_sweeps=n_sweeps, seed=k)
        post_burn = np.abs(mags[burn:])
        mean_abs_mag[k] = post_burn.mean()
        std_mag[k] = post_burn.std()
        print(f"beta={beta:.3f}  T={1/beta:.3f}  <|m|>={mean_abs_mag[k]:.3f}")

    fig, ax = plt.subplots(figsize=(6.5, 4.5))
    ax.errorbar(betas, mean_abs_mag, yerr=std_mag, fmt="o-", ms=4, capsize=2)
    ax.axvline(beta_c, color="gray", ls="--", lw=1, label=r"$\beta_c \approx 0.4407$")
    ax.set_xlabel(r"$\beta = 1/T$")
    ax.set_ylabel(r"$\langle |m| \rangle$ (mean $|$magnetization$|$ over post-burn-in sweeps)")
    ax.set_title(f"Ising phase transition from Gibbs sampling ({L}x{L} lattice)")
    ax.legend()
    plt.tight_layout()
    plt.savefig("ising_phase_transition.png", dpi=150)
    print("Saved plot to ising_phase_transition.png")
