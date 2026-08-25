import warnings
import numpy as np
import matplotlib.pyplot as plt
from sklearn.gaussian_process import GaussianProcessRegressor
from sklearn.gaussian_process.kernels import Matern, WhiteKernel, ConstantKernel
from sklearn.exceptions import ConvergenceWarning

warnings.filterwarnings("ignore", category=ConvergenceWarning)

rng = np.random.default_rng(5)

TRUE_TC = 143.7
TRUE_WIDTH = 2.5
NOISE_SD = 0.03


def simulate_measurement(T, noise_sd=NOISE_SD):
    phi_true = 0.5 * (1 + np.tanh((TRUE_TC - T) / TRUE_WIDTH))
    return phi_true + rng.normal(0, noise_sd)


class HybridScoutLeapLocator:
    def __init__(self, T_start, T_end, step_min=5.0, step_max=30.0,
                 sigma_n=NOISE_SD, k_significance=4.0, leap_scale_factor=0.8,
                 length_scale_bounds=(1.0, 150.0)):
        assert T_start > T_end
        self.T_start, self.T_end = T_start, T_end
        self.step_min, self.step_max = step_min, step_max
        self.sigma_n = sigma_n
        self.k_significance = k_significance
        self.leap_scale_factor = leap_scale_factor
        self.noise_floor_slope = np.sqrt(2) * sigma_n / step_min

        kernel = (ConstantKernel(1.0, (1e-2, 1e2))
                  * Matern(length_scale=5.0, length_scale_bounds=length_scale_bounds, nu=2.5)
                  + WhiteKernel(noise_level=sigma_n**2, noise_level_bounds=(1e-6, 1e-1)))
        self.gp = GaussianProcessRegressor(kernel=kernel, n_restarts_optimizer=2)

        self.T_obs, self.phi_obs = [], []
        self.leap_log = []  # (from_T, to_T, sigma_capped_leap_used)

    def observe(self, T, phi):
        self.T_obs.append(T)
        self.phi_obs.append(phi)
        if len(self.T_obs) >= 2:  # GP needs >=2 points to be meaningful
            X = np.array(self.T_obs).reshape(-1, 1)
            y = np.array(self.phi_obs)
            self.gp.fit(X, y)

    def _observed_slope(self, T1, phi1, T2, phi2):
        return abs(phi2 - phi1) / abs(T1 - T2)

    def _gp_informed_leap_distance(self, from_T, direction_sign, max_leap):
        if len(self.T_obs) < 3:
            return max_leap
        length_scale = float(self.gp.kernel_.k1.k2.length_scale)
        leap_cap = self.leap_scale_factor * length_scale
        return min(max_leap, leap_cap)

    def run(self):
        current_T = self.T_start
        current_phi = simulate_measurement(current_T)
        self.observe(current_T, current_phi)
        mode = "CRUISE"
        print(f"T={current_T:6.2f} K  phi={current_phi:.3f}  [start]")

        while current_T > self.T_end:
            if mode == "CRUISE":
                scout_T = max(current_T - self.step_min, self.T_end)
                scout_phi = simulate_measurement(scout_T)
                self.observe(scout_T, scout_phi)

                slope = self._observed_slope(current_T, current_phi, scout_T, scout_phi)
                informative = slope > self.k_significance * self.noise_floor_slope
                print(f"T={scout_T:6.2f} K  phi={scout_phi:.3f}  step={self.step_min:4.1f}  "
                      f"[scout, slope={slope:.4f} vs floor={self.noise_floor_slope:.4f}] "
                      f"{'-> INFORMATIVE, entering FINE' if informative else '-> boring'}")

                if informative:
                    current_T, current_phi = scout_T, scout_phi
                    mode = "FINE"
                else:
                    max_leap = self.step_max - self.step_min
                    leap_d = self._gp_informed_leap_distance(scout_T, -1, max_leap)
                    leap_T = max(scout_T - leap_d, self.T_end)
                    leap_phi = simulate_measurement(leap_T)
                    self.observe(leap_T, leap_phi)
                    self.leap_log.append((scout_T, leap_T, leap_d, max_leap))
                    print(f"T={leap_T:6.2f} K  phi={leap_phi:.3f}  "
                          f"step={leap_d:5.1f} (of max {max_leap:.1f})  "
                          f"[GP-informed leap{' -- SHORTENED' if leap_d < max_leap - 1e-6 else ' -- full'}]")
                    current_T, current_phi = leap_T, leap_phi

            else:  # FINE mode
                next_T = max(current_T - self.step_min, self.T_end)
                next_phi = simulate_measurement(next_T)
                self.observe(next_T, next_phi)

                slope = self._observed_slope(current_T, current_phi, next_T, next_phi)
                informative = slope > self.k_significance * self.noise_floor_slope
                print(f"T={next_T:6.2f} K  phi={next_phi:.3f}  step={self.step_min:4.1f}  "
                      f"[fine, slope={slope:.4f} vs floor={self.noise_floor_slope:.4f}] "
                      f"{'stays FINE' if informative else '-> back to CRUISE'}")

                current_T, current_phi = next_T, next_phi
                if not informative:
                    mode = "CRUISE"

        print(f"\ntotal measurements: {len(self.T_obs)}")

    def estimate_TN(self, n_samples=2000, window_halfwidth=8.0):
        span = self.T_start - self.T_end
        pad = 0.1 * span
        T_grid = np.linspace(self.T_end - pad, self.T_start + pad, 500).reshape(-1, 1)
        dT = T_grid[1, 0] - T_grid[0, 0]

        y_samples = self.gp.sample_y(T_grid, n_samples=n_samples, random_state=1)
        abs_d = np.abs(np.gradient(y_samples, dT, axis=0))
        mu_d = abs_d.mean(axis=1)
        T_flat = T_grid.ravel()

        in_domain = (T_flat >= self.T_end) & (T_flat <= self.T_start)
        mu_d_domain = np.where(in_domain, mu_d, -np.inf)
        center_T = T_flat[np.argmax(mu_d_domain)]
        valid = in_domain & (np.abs(T_flat - center_T) <= window_halfwidth)

        masked = np.where(valid[:, None], abs_d, -np.inf)
        peak_indices = np.argmax(masked, axis=0)
        return T_flat[peak_indices], T_grid


def plot_results(loc, TN_samples, T_grid):
    mu, sigma = loc.gp.predict(T_grid, return_std=True)
    T_flat = T_grid.ravel()

    fig, axes = plt.subplots(3, 1, figsize=(8, 10),
                              gridspec_kw={"height_ratios": [3, 1.3, 1]})
    ax = axes[0]
    ax.plot(T_flat, mu, color="#1f77b4", label="GP posterior mean")
    ax.fill_between(T_flat, mu - 1.96*sigma, mu + 1.96*sigma, color="#1f77b4", alpha=0.2)
    T_obs = np.array(loc.T_obs)
    ax.scatter(T_obs, loc.phi_obs, c=np.arange(len(T_obs)), cmap="viridis", zorder=5, s=20)
    ax.axvline(TRUE_TC, color="red", ls=":", lw=1.5, label="true $T_c$")
    ax.invert_xaxis()
    ax.set_ylabel("$\\phi(T)$")
    ax.legend(fontsize=8)
    ax.set_title(f"Hybrid: scout safety-check + GP-informed leap distance "
                 f"(step_min={loc.step_min}, step_max={loc.step_max}K)")

    ax2 = axes[1]
    for from_T, to_T, d, max_d in loc.leap_log:
        color = "#d62728" if d < max_d - 1e-6 else "#2ca02c"
        ax2.plot([from_T, to_T], [d, d], color=color, marker="o", ms=3)
    ax2.invert_xaxis()
    ax2.set_ylabel("leap distance\nused (K)")
    ax2.set_xlabel("leap start T (K)  [green=full step_max, red=GP-shortened]")

    ax3 = axes[2]
    ax3.hist(TN_samples, bins=40, color="#2ca02c", alpha=0.7)
    ax3.axvline(TRUE_TC, color="red", ls=":", lw=1.5)
    ax3.set_xlabel("Temperature (K)")
    ax3.set_ylabel("post. samples\nof $T_N$")

    plt.tight_layout()
    plt.savefig("./hybrid_scout_leap_locator.png", dpi=300)
    print("saved plot -> hybrid_scout_leap_locator.png")


if __name__ == "__main__":
    loc = HybridScoutLeapLocator(T_start=270.0, T_end=10.0,
                                  step_min=5.0, step_max=30.0,
                                  leap_scale_factor=0.8)
    loc.run()
    TN_samples, T_grid = loc.estimate_TN()
    TN_mean = np.mean(TN_samples)
    ci_lo, ci_hi = np.percentile(TN_samples, [2.5, 97.5])
    print(f"\nEstimated T_N = {TN_mean:.2f} K, 95% CI [{ci_lo:.2f}, {ci_hi:.2f}] K "
          f"(true T_c = {TRUE_TC} K)")
    plot_results(loc, TN_samples, T_grid)
