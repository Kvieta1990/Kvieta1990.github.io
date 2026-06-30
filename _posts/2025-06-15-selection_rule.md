---
layout: post
title: Notes on Selection Rule for Transition between Energy Levels
subtitle:
tags: [Quantum Mechanics, quantum physics]
author: Yuanpeng Zhang
comments: true
use_math: true
---

When a physical system is absorbing or emitting electromagnetic radiation, there will be a transition happening between the quantum energy levels of the system. The selection rule determines whether or not such a transition can happen. It is a typical topic in quantum mechanics textbook and it is not the intention of this blog to note down nitty-gritties of the whole topic. Instead, I will just put down my basic understanding of the selection rule from the high level, which can help us establish an overall picture about the selection rule.

First, there is the so-called **gross selection rule** [1], which specifies the general requirements for atoms or molecules to exhibit a particular type of spectrum, such as infrared (IR) or microwave spectroscopy. This rule determines whether a transition is fundamentally allowed based on properties like the presence of a permanent dipole moment (for microwave radiation) or a change in dipole moment during vibration (for IR). Here below is a table summarizing the **gross several selection rules** for different types of electromagnetic radiation,

|           | Interaction | Selection Rule                                  | Rep Indication | References |
|-----------|-------------|-------------------------------------------------|----------------|------------|
| Microwave | Rotation    | Permanent dipole moment                         | -              | [2]           |
| IR        | Vibration   | Change in dipole moment as change in vibration  | Rep transforms similar to any one of the Cartesian coordinates x,y, or z.               | [3]           |
| Raman     | Vibration   | Change in polarizibility as change in vibration | Rep transforms similarly to the direct products of any one of the x, y, or z coordinates. That includes any of the functions: xy, xz, yz, x2, y2, z2, or any combination.               | [3]           |

Microwave Rotational Spectroscopy
===

The microwave rotation spectroscopy refers to the interaction between the microwave radiation and the rotation degree of freedom of molecules. It is basically a process of momentum transfer between photons and molecules. To allow such a process, the electromagnetic radiation should be able to exert a torque on molecules through the electromagnetic interaction and therefore it is not difficult to imagine that we require the molecule to carry a permanent dipole moment for such a type of interaction. Also, due to the momentum conservation law, the **specific selection rule** (here, we come across the other type of `selection rule` which specifies the specifically allowed transition for a certain type of interaction) is $$\Delta J = \pm 1$$, where $$J$$ refers to the rotational quantum number of the underlying physical system. This is because photons carry the angular moment of `1` and therefore it can only take in or give away the angular moment of unit `1`.

IR & Raman
===

In Ref. [3], details about these two types of spectroscopy are presented very nicely and here I don't want to reproduce. Instead, I will just present a typical example for each, and it should be pointed out that the examples here are `provided by Google AI search`,

***IR***: Consider the asymmetric stretch of a CO$$_2$$ molecule. In this vibration, one C=O bond stretches while the other compresses. This creates a temporary dipole moment along the axis of the molecule, making the vibration infrared active.

***Raman***: Again, with the CO$$_2$$ example, one of the ways of the molecule vibration is the symmetric stretching. In this mode, the oxygen atoms move away from and then back towards the central carbon atom in a symmetrical way. The molecule remains linear throughout this vibration. The polarizability changes during this vibration. As the oxygen atoms move away from the carbon, the electron cloud is less concentrated, and the polarizability decreases. When the oxygen atoms move closer, the electron cloud becomes more concentrated, and the polarizability increases. This change in polarizability occurs along the molecular axis (the $$z$$-axis). This is represented by the $$z^{2}$$ component of the polarizability tensor.

Transition among Atomic Levels or Orbitals
===

The selecton rule applied here refers to the transitions between the different energy levels corresponding to either the atomic levels or moculelar orbitals. In this context, we normally see the notion of `dipole selection rule`. Regarding this, we have quite a lot useful resources to learn about, and here I am just giving several of them in Ref. [4-7]. Again, I am not going to note down the detailed derivation of the selection rule here -- derivation and discussions can refer to those references. Simply, the dipole selection rule is,

$$
\begin{equation}
\begin{aligned}
l' & = l \pm 1\\
m' & = m,\ m \pm 1
\end{aligned}
\end{equation}
$$

where $$l$$ and $$m$$ are the angular moment quantum number and magnetic quantum number, respectively.

A naive question that I want to talk a bit about here is, what does the `dipole selection rule` really mean? We know what a dipole is -- simple a positive and negative charge forming a dipole and we have the electric field established from such a dipole arrangement of charges. But how is `dipole` associated with the quantum level transition problem that we are talking about here? In fact, a very direct physics picture for answering such a question is, when we have the transition between different energy levels (say, from low to high level), electrons get promoted from a lower level to a higher energy level, leaving a `hole` in the original level. The core `hole` then can be effectively treated as a positive charge and therefore the interaction between the core `hole` and the excited electron naturally form a `dipole`. As for the fundamental association between the quantum transition and the electronic dipole (or quadrupole, etc.), it goes all the way back to the electromagnetic transition probability constructed from the Fermi's golden rule. From Ref. [5, 6], we can clearly see the emergence of the electronic dipole moment $$\hat{\epsilon}\cdot\vec{r}$$ in the calculation of the transition probability. In fact, the dipole transition is only an approximation for the actual transition, which, in fact, could involve higher order transitions such as the quadrupole transition -- see Ref. [8, 9] for what a quadrupole means and what the selection rule is for a quadrupole transition.

<br>

References
===

[1] [Physical_and_Theoretical_Chemistry_Textbook - Selection_Rules](https://chem.libretexts.org/Bookshelves/Physical_and_Theoretical_Chemistry_Textbook_Maps/Supplemental_Modules_(Physical_and_Theoretical_Chemistry)/Spectroscopy/Fundamentals_of_Spectroscopy/Selection_Rules)

[2] [Physical_and_Theoretical_Chemistry_Textbook - Microwave_Rotational_Spectroscopy](https://chem.libretexts.org/Bookshelves/Physical_and_Theoretical_Chemistry_Textbook_Maps/Supplemental_Modules_(Physical_and_Theoretical_Chemistry)/Spectroscopy/Rotational_Spectroscopy/Microwave_Rotational_Spectroscopy)

[3] [Physical_and_Theoretical_Chemistry_Textbook - Selection_Rules_for_IR_and_Raman_Spectroscopy](https://chem.libretexts.org/Bookshelves/Inorganic_Chemistry/Supplemental_Modules_and_Websites_(Inorganic_Chemistry)/Advanced_Inorganic_Chemistry_(Wikibook)/01%3A_Chapters/1.13%3A_Selection_Rules_for_IR_and_Raman_Spectroscopy)

[4] [Selection_rules_for_Electric_Dipole_Transitions.pdf](/assets/files/Selection_rules_for_Electric_Dipole_Transitions.pdf)

[5] [https://quantummechanics.ucsd.edu/ph130a/130_notes/node421.html](https://quantummechanics.ucsd.edu/ph130a/130_notes/node421.html)

[6] [https://quantummechanics.ucsd.edu/ph130a/130_notes/node422.html](https://quantummechanics.ucsd.edu/ph130a/130_notes/node422.html)

[7] [Introductory_Quantum_Mechanics - Selection_Rules_(Hydrogen_Atoms)](https://phys.libretexts.org/Bookshelves/Quantum_Mechanics/Introductory_Quantum_Mechanics_(Fitzpatrick)/12%3A_Time-Dependent_Perturbation_Theory/12.10%3A_Selection_Rules_(Hydrogen_Atoms))

[8] [https://www.youtube.com/watch?v=uPkoK3vIRhc&t=21s](https://www.youtube.com/watch?v=uPkoK3vIRhc&t=21s)

[9] [https://farside.ph.utexas.edu/teaching/qm/Quantum/node89.html](https://farside.ph.utexas.edu/teaching/qm/Quantum/node89.html)