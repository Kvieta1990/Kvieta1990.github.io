import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

qa_dot = np.arange(0.001, 4. * 2. * np.pi, 0.001)

fig, ax = plt.subplots(figsize=(10, 8))

ax.plot(qa_dot,
        np.sin(2. * qa_dot / 2.)**2. / np.sin(qa_dot / 2.)**2.,
        label="N = 2",
        color="green")
ax.plot(qa_dot,
        np.sin(3. * qa_dot / 2.)**2. / np.sin(qa_dot / 2.)**2.,
        label="N = 3",
        color="orange")
ax.plot(qa_dot,
        np.sin(4. * qa_dot / 2.)**2. / np.sin(qa_dot / 2.)**2.,
        label="N = 4",
        color="blue")

ax.set_xlim([-0.8, 4. * 2. * np.pi])
ax.set_ylim([-0.8, 21])
ax.set_xticks([], [])
ax.set_yticks([], [])
ax.spines[['left', 'bottom', 'right', 'top']].set_visible(False)

ax.text(4. * 2. * np.pi + 1.2, 16.0, "N = 4", fontsize=16, ha='center', va='center', color='blue')
ax.text(4. * 2. * np.pi + 1.2, 9.0, "N = 3", fontsize=16, ha='center', va='center', color='orange')
ax.text(4. * 2. * np.pi + 1.2, 4.0, "N = 2", fontsize=16, ha='center', va='center', color='green')
ax.tick_params(axis='y', which='major', length=8, width=1.5)
ax.spines['left'].set_linewidth(1.5)
ax.spines['bottom'].set_linewidth(1.5)

arrow_x = patches.FancyArrowPatch((-0.01, 0.), (4. * 2. * np.pi, 0.),
                                  arrowstyle='-|>',
                                  mutation_scale=15,
                                  linewidth=1.5,
                                  color='black')
arrow_y = patches.FancyArrowPatch((0., -0.05), (0., 20.),
                                  arrowstyle='-|>',
                                  mutation_scale=15,
                                  linewidth=1.5,
                                  color='black')

tick_y1 = patches.FancyArrowPatch((0.01, 4.), (-0.5, 4.),
                                  arrowstyle='-',
                                  mutation_scale=15,
                                  linewidth=1.5,
                                  color='green')
tick_y2 = patches.FancyArrowPatch((0.01, 9.), (-0.5, 9.),
                                  arrowstyle='-',
                                  mutation_scale=15,
                                  linewidth=1.5,
                                  color='orange')
tick_y3 = patches.FancyArrowPatch((0.01, 16.), (-0.5, 16.),
                                  arrowstyle='-',
                                  mutation_scale=15,
                                  linewidth=1.5,
                                  color='blue')
ax.text(-.9, 16.0, "16", fontsize=16, ha='center', va='center', color='blue')
ax.text(-.9, 9.0, "9", fontsize=16, ha='center', va='center', color='orange')
ax.text(-.9, 4.0, "4", fontsize=16, ha='center', va='center', color='green')

ax.text(4. * 2. * np.pi + 0.8, 0., r"$\vec{Q}\cdot\vec{a}$",
        fontsize=16, ha='center', va='center', color='black')
ax.text(-1.7, 20., r"$[\frac{sin(\frac{N\vec{Q}\cdot\vec{a}}{2})}{sin(\frac{\vec{Q}\cdot\vec{a}}{2})}]^2$",
        fontsize=16, ha='center', va='center', color='black')

ax.add_patch(arrow_x)
ax.add_patch(arrow_y)
ax.add_patch(tick_y1)
ax.add_patch(tick_y2)
ax.add_patch(tick_y3)
fig.tight_layout()

fig.savefig("diff_size_effect.png", dpi=300)
plt.show()