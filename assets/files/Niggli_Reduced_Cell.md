---
title: Niggli_Reduced_Cell
tag: physics
---

## 1. Historical and Mathematical Context

### 1.1 Why reduction is necessary

A 3D lattice $\Lambda$ is the set of all integer linear combinations of three linearly independent basis vectors $\mathbf{a}, \mathbf{b}, \mathbf{c}$. Any matrix $M \in GL(3,\mathbb{Z})$ — i.e., any $3\times 3$ integer matrix with $\det M = \pm 1$ — gives an equally valid description of the same lattice:

$$(\mathbf{a}', \mathbf{b}', \mathbf{c}') = (\mathbf{a}, \mathbf{b}, \mathbf{c})\, M$$

There are infinitely many such $M$, hence infinitely many cells for a single lattice. Niggli (1928), building on Eisenstein (1851), defined a canonical choice: the **Niggli reduced cell**, which is unique up to the symmetry operations of the lattice itself.

### 1.2 The lineage of algorithms

| Paper | Contribution |
|---|---|
| Niggli (1928) | Defines the algebraic conditions for a unique reduced cell |
| Buerger (1957) | First iterative numerical procedure (does not always reach the unique Niggli cell) |
| Gruber (1973), *Acta Cryst.* A29, 433–440 | Alternative Buerger-reduction algorithm; classifies 28 unique Niggli type families; introduces what become steps N1–N3, B1–B5 |
| Křivý & Gruber (1976), *Acta Cryst.* A32, 297–298 | Modified algorithm (8 steps A1–A8) that reaches the *unique* Niggli cell |
| Zuo et al. (1995), *Acta Cryst.* A51, 943–945 | Optimizes iteration count |
| Grosse-Kunstleve, Sauter & Adams (2004), *Acta Cryst.* A60, 1–6 | Numerically stable implementation with tolerance $\varepsilon$; proves the naive implementation has ~1% infinite-loop failure rate; derives change-of-basis matrices for each step |

---

## 2. The Metric Tensor and Niggli Parameters

### 2.1 The metric tensor

All geometry of the unit cell is encoded in the **metric tensor** $G$, the $3 \times 3$ Gram matrix of the basis vectors:

$$G = \begin{pmatrix} \mathbf{a}\cdot\mathbf{a} & \mathbf{a}\cdot\mathbf{b} & \mathbf{a}\cdot\mathbf{c} \\ \mathbf{a}\cdot\mathbf{b} & \mathbf{b}\cdot\mathbf{b} & \mathbf{b}\cdot\mathbf{c} \\ \mathbf{a}\cdot\mathbf{c} & \mathbf{b}\cdot\mathbf{c} & \mathbf{c}\cdot\mathbf{c} \end{pmatrix}$$

Under a basis change $(\mathbf{a}', \mathbf{b}', \mathbf{c}') = (\mathbf{a}, \mathbf{b}, \mathbf{c})C$, the metric tensor transforms as:

$$G' = C^T G C$$

This is the fundamental operation underlying Niggli reduction — every step is a congruence transformation of $G$.

### 2.2 The Gruber/Křivý notation

Gruber (1973) and Křivý & Gruber (1976) use the following notation:

$$A = \mathbf{a}\cdot\mathbf{a} = a^2$$
$$B = \mathbf{b}\cdot\mathbf{b} = b^2$$
$$C = \mathbf{c}\cdot\mathbf{c} = c^2$$
$$\xi = 2\,\mathbf{b}\cdot\mathbf{c} = 2bc\cos\alpha$$
$$\eta = 2\,\mathbf{c}\cdot\mathbf{a} = 2ca\cos\beta$$
$$\zeta = 2\,\mathbf{a}\cdot\mathbf{b} = 2ab\cos\gamma$$

So the metric tensor becomes:

$$G = \begin{pmatrix} A & \zeta/2 & \eta/2 \\ \zeta/2 & B & \xi/2 \\ \eta/2 & \xi/2 & C \end{pmatrix}$$

The factors of 2 in $\xi, \eta, \zeta$ (rather than the cosines themselves) simplify all the expressions that follow. Note:
- $\xi, \eta, \zeta > 0 \Leftrightarrow$ corresponding angles are acute
- $\xi, \eta, \zeta < 0 \Leftrightarrow$ corresponding angles are obtuse
- $\xi, \eta, \zeta = 0 \Leftrightarrow$ corresponding angles are exactly $90°$

The **volume** of the cell is $V = \sqrt{\det G}$, and the positivity condition $\det G > 0$ is required for the cell to be non-degenerate.

---

## 3. The Niggli Conditions

A cell $(A, B, C, \xi, \eta, \zeta)$ is Niggli-reduced if and only if all of the following conditions hold simultaneously. They come from §9.3.2 of *International Tables for Crystallography*, Vol. A, and are derived directly from Niggli (1928):

### 3.1 Main conditions — enforce minimum lengths

These enforce that $\mathbf{a}$ is the shortest, $\mathbf{b}$ the second shortest, $\mathbf{c}$ the third shortest, in a suitably constrained sense:

$$A \leq B \leq C \tag{M1}$$

$$|\xi| \leq B \tag{M2}$$

$$|\eta| \leq A \tag{M3}$$

$$|\zeta| \leq A \tag{M4}$$

The last three mean that no off-diagonal entry of $G$ can exceed the smaller of the two diagonal entries in the same row/column. This is the Minkowski reduction condition: $\mathbf{a}, \mathbf{b}, \mathbf{c}$ must be among the shortest vectors in the lattice.

### 3.2 Special conditions — enforce uniqueness of sign

Even after the main conditions are satisfied, there is still residual freedom in choosing signs of the off-diagonal elements. The Niggli conditions eliminate this by requiring all angles to be simultaneously of the same type:

**Type I (all obtuse/right):**
$$\xi \leq 0, \quad \eta \leq 0, \quad \zeta \leq 0 \tag{S1}$$

**Type II (all acute/right):**
$$\xi > 0, \quad \eta > 0, \quad \zeta > 0 \tag{S2}$$

and then additional tie-breaking conditions that kick in at the boundaries:

$$\text{If } \xi = 0 \Rightarrow \eta = 0 \tag{T1}$$
$$\text{If } \eta = 0 \Rightarrow \zeta = 0 \tag{T2}$$

### 3.3 Boundary (degenerate) tie-breaking conditions

At equalities in M1–M4, additional strict inequalities resolve ambiguity:

$$A = B \Rightarrow |\xi| \leq |\eta| \tag{D1}$$
$$B = C \Rightarrow |\eta| \leq |\zeta| \tag{D2}$$
$$|\xi| = B \Rightarrow \zeta \leq 2\eta \tag{D3}$$
$$|\eta| = A \Rightarrow \zeta \leq 2\xi \tag{D4}$$
$$|\zeta| = A \Rightarrow \eta \leq 2\xi \tag{D5}$$

And one more for the body-diagonal case:

$$\xi + \eta + \zeta + A + B = 0 \Rightarrow 2(A + \eta) + \zeta \leq 0 \tag{D6}$$

Conditions D1–D6 are the tie-breaking rules that distinguish among otherwise metrically equivalent choices and make the cell truly *unique*.

---

## 4. The Křivý–Gruber Algorithm (Steps A1–A8)

The Křivý–Gruber (1976) algorithm starts from an arbitrary primitive cell of a three-dimensional Bravais lattice and reaches the Niggli form requisite for lattice type determination. The algorithm is iterative: you apply steps A1–A8 in order, and whenever a step *fires* (its condition is true), you apply the corresponding transformation and restart from A1. The algorithm terminates when one complete pass through A1–A8 fires no step.

Before describing the steps, we introduce helper variables for the signs:

$$l = \text{sign}(\xi), \quad m = \text{sign}(\eta), \quad n = \text{sign}(\zeta)$$

where we use the convention $\text{sign}(x) = +1$ if $x > 0$, $-1$ if $x < 0$, $0$ if $x = 0$. In practice with tolerance $\varepsilon$: $l = -1$ if $\xi < -\varepsilon$, $l = +1$ if $\xi > +\varepsilon$, else $l = 0$; similarly for $m, n$.

---

### Step A1 — Sort $A \leq B$ (with tie-breaking)

**Condition:** $A > B + \varepsilon$, OR ($|A - B| \leq \varepsilon$ AND $|\xi| > |\eta| + \varepsilon$)

The first clause simply sorts lengths. The second handles the degenerate case $A = B$: by condition D1, we need $|\xi| \leq |\eta|$, so if this is violated we swap.

**Action — swap $\mathbf{a} \leftrightarrow \mathbf{b}$:**

$$C_{\text{A1}} = \begin{pmatrix} 0 & -1 & 0 \\ -1 & 0 & 0 \\ 0 & 0 & -1 \end{pmatrix}$$

This matrix simultaneously swaps $\mathbf{a}$ and $\mathbf{b}$ and flips $\mathbf{c}$, so that the cell remains right-handed ($\det C = +1$). After this, update all six Niggli parameters:

$$A \leftrightarrow B, \quad \xi \leftrightarrow \eta$$

(and $\zeta$ is unaffected because $\zeta = 2\mathbf{a}\cdot\mathbf{b}$ which is symmetric in $\mathbf{a}, \mathbf{b}$; but the flip of $\mathbf{c}$ leaves $\xi, \eta$ sign-unchanged because both involve $\mathbf{c}$). More precisely the tensor transformation gives:

$$A' = B, \quad B' = A, \quad C' = C, \quad \xi' = \eta, \quad \eta' = \xi, \quad \zeta' = \zeta$$

**After firing: go to A1** (restart).

---

### Step A2 — Sort $B \leq C$ (with tie-breaking)

**Condition:** $B > C + \varepsilon$, OR ($|B - C| \leq \varepsilon$ AND $|\eta| > |\zeta| + \varepsilon$)

This enforces $B \leq C$ and, when $B = C$, enforces D2: $|\eta| \leq |\zeta|$.

**Action — swap $\mathbf{b} \leftrightarrow \mathbf{c}$:**

$$C_{\text{A2}} = \begin{pmatrix} -1 & 0 & 0 \\ 0 & 0 & -1 \\ 0 & -1 & 0 \end{pmatrix}$$

The parameter update from $G' = C^T G C$ gives:

$$A' = A, \quad B' = C, \quad C' = B, \quad \xi' = \zeta, \quad \eta' = \eta \cdot \text{(sign correction)}, \quad \zeta' = \xi$$

More carefully (the $-1$ entries in $C_{\text{A2}}$ flip signs): $B' = C$, $C' = B$, $\xi' = \zeta$, $\zeta' = \xi$, and $\eta$ acquires sign changes from the two $-1$ diagonal factors: $\eta' = \eta$ (two flips cancel).

**After firing: go to A1** (must re-sort, since $A \leq B$ may now be violated).

---

### Step A3 — Enforce pure type: make all angles same sign

**Condition:** $lmn = 1$

This means exactly one or all three of $l, m, n$ are positive. In other words, the signs are not all negative/zero (Type I would be $lmn \leq 0$ with all nonpositive). If $lmn = 1$ we are not yet pure Type I or II — we have a "mixed" situation with an odd number of positive signs but at least one negative.

Wait — let's be more precise. The condition $lmn = 1$ occurs when: all three are positive ($+1 \cdot +1 \cdot +1 = 1$, pure Type II, which is fine), OR exactly two are negative and one positive ($(-1)(-1)(+1) = 1$, which is not a pure type). So the real intent of A3 is to handle the case where the product is positive but not all are positive (two negatives, one positive), by flipping signs to make them all positive. When all three are positive, the product is also $1$, but in that case we want to make $i=j=k=+1$ which does nothing. The step fires and does no harm.

**Action — diagonal sign flip:**

$$i = \begin{cases} -1 & \text{if } l = -1 \\ +1 & \text{otherwise} \end{cases}, \quad j = \begin{cases} -1 & \text{if } m = -1 \\ +1 & \text{otherwise} \end{cases}, \quad k = \begin{cases} -1 & \text{if } n = -1 \\ +1 & \text{otherwise} \end{cases}$$

$$C_{\text{A3}} = \begin{pmatrix} i & 0 & 0 \\ 0 & j & 0 \\ 0 & 0 & k \end{pmatrix}$$

This flips the sign of any basis vector that has a negative off-diagonal inner product contribution. Effect on parameters:

$$\xi' = ij\,\xi \cdot \frac{\xi}{|\xi|} \cdot \ldots$$

More simply: $\xi \to |\ \xi\ |$, $\eta \to |\eta|$, $\zeta \to |\zeta|$, since the diagonal sign changes cancel in $A, B, C$ (each squared) but flip the signs of $\xi, \eta, \zeta$ appropriately. After A3 in this case, all three off-diagonal parameters become positive (Type II).

**After firing: go to A4.**

---

### Step A4 — Enforce Type I or II: handle $lmn \leq 0$

**Condition:** NOT ($l = -1$ AND $m = -1$ AND $n = -1$) AND ($lmn = 0$ OR $lmn = -1$)

This step handles the case where the angles are not yet all the same sign, and specifically forces them toward a consistent type. The goal is: if we can't have all positive, make them all non-positive (Type I). 

**Action:**

Initialize $i = j = k = 1$ and a reference variable $r$ pointing to nothing.

- If $l = +1$: set $i = -1$
- If $l = 0$: set $r \to i$
- If $m = +1$: set $j = -1$
- If $m = 0$: set $r \to j$
- If $n = +1$: set $k = -1$
- If $n = 0$: set $r \to k$
- If $ijk = -1$: set the variable pointed to by $r$ to $-1$

$$C_{\text{A4}} = \begin{pmatrix} i & 0 & 0 \\ 0 & j & 0 \\ 0 & 0 & k \end{pmatrix}$$

The logic: for any angle that is currently positive (acute), flip it to negative (obtuse). If after doing so the product $ijk$ comes out $-1$, it means flipping all positive ones left an odd number of negative signs overall — so we pick one of the zero-angle directions (pointed to by $r$) and flip that too, to bring the parity back.

This step converts any mixed sign pattern to either all-negative (Type I) or all-zero-or-negative. After A4, $\xi \leq 0$, $\eta \leq 0$, $\zeta \leq 0$.

**After firing: go to A1** (lengths may have changed).

---

### Step A5 — Reduce $|\xi|$

**Condition:** $|\xi| > B + \varepsilon$, OR ($|B - \xi| \leq \varepsilon$ AND $2\eta < \zeta - \varepsilon$), OR ($|B + \xi| \leq \varepsilon$ AND $\zeta < -\varepsilon$)

This enforces $|\xi| \leq B$ (condition M2). The secondary clauses handle the tie-breaking conditions D3.

- First clause $|\xi| > B$: the off-diagonal element $\xi = 2\mathbf{b}\cdot\mathbf{c}$ is too large; we need to replace $\mathbf{c}$ by $\mathbf{c} \pm \mathbf{b}$ to reduce it.
- Second clause $\xi = B$ with $2\eta < \zeta$: the boundary condition D3 is violated.
- Third clause $\xi = -B$ with $\zeta < 0$: another boundary case.

**Action:**

$$C_{\text{A5}} = \begin{pmatrix} 1 & 0 & 0 \\ 0 & 1 & -\text{sign}(\xi) \\ 0 & 0 & 1 \end{pmatrix}$$

This replaces $\mathbf{c} \to \mathbf{c} - \text{sign}(\xi)\,\mathbf{b}$, which is equivalent to the operation that reduces $\xi$. The tensor update is:

$$C' = C + B - \xi\,\text{sign}(\xi) = C + B - |\xi|$$
$$\xi' = \xi - 2B\,\text{sign}(\xi)$$
$$\eta' = \eta - \zeta\,\text{sign}(\xi)$$

(with $A, B, \zeta$ unchanged). In the Gruber (1973) algorithm this is a full-reduction step using $\mathbf{c} \to \mathbf{c} - \lfloor \xi / 2B \rceil \mathbf{b}$, reducing $\xi$ in one shot; in the Křivý–Gruber version, only the sign step is used (requiring more iterations but simpler logic).

**After firing: go to A1.**

---

### Step A6 — Reduce $|\eta|$

**Condition:** $|\eta| > A + \varepsilon$, OR ($|A - \eta| \leq \varepsilon$ AND $2\xi < \zeta - \varepsilon$), OR ($|A + \eta| \leq \varepsilon$ AND $\zeta < -\varepsilon$)

This enforces $|\eta| \leq A$ (condition M3), with tie-breaking for D4.

**Action:**

$$C_{\text{A6}} = \begin{pmatrix} 1 & 0 & -\text{sign}(\eta) \\ 0 & 1 & 0 \\ 0 & 0 & 1 \end{pmatrix}$$

This replaces $\mathbf{c} \to \mathbf{c} - \text{sign}(\eta)\,\mathbf{a}$. Update:

$$C' = C + A - |\eta|$$
$$\eta' = \eta - 2A\,\text{sign}(\eta)$$
$$\zeta' = \zeta - \xi\,\text{sign}(\eta)$$

(with $A, B, \xi$ unchanged).

**After firing: go to A1.**

---

### Step A7 — Reduce $|\zeta|$

**Condition:** $|\zeta| > A + \varepsilon$, OR ($|A - \zeta| \leq \varepsilon$ AND $2\xi < \eta - \varepsilon$), OR ($|A + \zeta| \leq \varepsilon$ AND $\eta < -\varepsilon$)

This enforces $|\zeta| \leq A$ (condition M4), with tie-breaking for D5.

**Action:**

$$C_{\text{A7}} = \begin{pmatrix} 1 & -\text{sign}(\zeta) & 0 \\ 0 & 1 & 0 \\ 0 & 0 & 1 \end{pmatrix}$$

This replaces $\mathbf{b} \to \mathbf{b} - \text{sign}(\zeta)\,\mathbf{a}$. Update:

$$B' = B + A - |\zeta|$$
$$\xi' = \xi - \eta\,\text{sign}(\zeta)$$
$$\zeta' = \zeta - 2A\,\text{sign}(\zeta)$$

(with $A, C, \eta$ unchanged).

**After firing: go to A1.**

---

### Step A8 — Body-diagonal check

**Condition:** $\xi + \eta + \zeta + A + B < -\varepsilon$, OR ($|\xi + \eta + \zeta + A + B| \leq \varepsilon$ AND $2(A + \eta) + \zeta > \varepsilon$)

This enforces the final boundary condition D6. The quantity $\xi + \eta + \zeta + A + B$ appears because it equals $(\mathbf{a} + \mathbf{b} + \mathbf{c}) \cdot (\mathbf{a} + \mathbf{b} + \mathbf{c}) - C$, which must be non-negative (any vector in the lattice has non-negative squared length). If it is negative, the vector $\mathbf{a} + \mathbf{b} + \mathbf{c}$ is shorter than $\mathbf{c}$ and needs to replace it. The tie-breaking clause handles the boundary case.

**Action:**

$$C_{\text{A8}} = \begin{pmatrix} 1 & 0 & 1 \\ 0 & 1 & 1 \\ 0 & 0 & 1 \end{pmatrix}$$

This replaces $\mathbf{c} \to \mathbf{c} + \mathbf{a} + \mathbf{b}$. The tensor update:

$$C' = C + A + B + \xi + \eta + \zeta$$
$$\xi' = \xi + 2B + \zeta$$
$$\eta' = \eta + 2A + \zeta$$

(with $A, B, \zeta$ unchanged).

**After firing: go to A1.**

---

### 4.1 Flowchart of the complete algorithm

```
Initialize C_total = I (3×3 identity)

LOOP:
  Compute l, m, n from current ξ, η, ζ

  A1: if (A > B) OR (A≈B AND |ξ|>|η|):
        apply C_A1 → update A,B,C,ξ,η,ζ and C_total
        GOTO A1

  A2: if (B > C) OR (B≈C AND |η|>|ζ|):
        apply C_A2 → update
        GOTO A1

  A3: if lmn = 1 (odd number negative or all positive):
        apply C_A3 → update
        GOTO A4

  A4: if lmn = 0 or -1 (not all negative):
        apply C_A4 → update
        GOTO A1

  A5: if |ξ| > B or boundary violation:
        apply C_A5 → update
        GOTO A1

  A6: if |η| > A or boundary violation:
        apply C_A6 → update
        GOTO A1

  A7: if |ζ| > A or boundary violation:
        apply C_A7 → update
        GOTO A1

  A8: if ξ+η+ζ+A+B < 0 or boundary violation:
        apply C_A8 → update
        GOTO A1

  If no step fired in A1–A8: DONE
```

The final cell $(A, B, C, \xi, \eta, \zeta)$ is Niggli-reduced. The accumulated product $C_{\text{total}}$ is the change-of-basis matrix, which can be used to transform atomic coordinates, symmetry operations, orientation matrices, etc.

---

## 5. Change-of-Basis Matrices in Full

Each step in the Křivý–Gruber algorithm consists of testing for a condition, followed by an action. The actions are given by Křivý & Gruber as reassignments of the parameters $A, B, C, \xi, \eta, \zeta$. A mathematically equivalent definition is given by the tensor transformation $G' = C^T G C$ with a $3\times 3$ transformation matrix $C$ acting on the metric tensor.

All eight matrices explicitly:

$$C_{\text{A1}} = \begin{pmatrix} 0 & -1 & 0 \\ -1 & 0 & 0 \\ 0 & 0 & -1 \end{pmatrix}, \quad C_{\text{A2}} = \begin{pmatrix} -1 & 0 & 0 \\ 0 & 0 & -1 \\ 0 & -1 & 0 \end{pmatrix}$$

$$C_{\text{A3}} = \begin{pmatrix} i & 0 & 0 \\ 0 & j & 0 \\ 0 & 0 & k \end{pmatrix} \quad (i,j,k \text{ chosen to make all signs positive})$$

$$C_{\text{A4}} = \begin{pmatrix} i & 0 & 0 \\ 0 & j & 0 \\ 0 & 0 & k \end{pmatrix} \quad (i,j,k \text{ chosen to make all signs non-positive})$$

$$C_{\text{A5}} = \begin{pmatrix} 1 & 0 & 0 \\ 0 & 1 & -s_\xi \\ 0 & 0 & 1 \end{pmatrix}, \quad C_{\text{A6}} = \begin{pmatrix} 1 & 0 & -s_\eta \\ 0 & 1 & 0 \\ 0 & 0 & 1 \end{pmatrix}, \quad C_{\text{A7}} = \begin{pmatrix} 1 & -s_\zeta & 0 \\ 0 & 1 & 0 \\ 0 & 0 & 1 \end{pmatrix}$$

where $s_x = \text{sign}(x) \in \{-1, +1\}$.

$$C_{\text{A8}} = \begin{pmatrix} 1 & 0 & 1 \\ 0 & 1 & 1 \\ 0 & 0 & 1 \end{pmatrix}$$

All matrices $C$ shown above have determinant $+1$. Each matrix could also be replaced by the matrix product $C \cdot \text{diag}(-1,-1,-1)$ since any lattice exhibits a center of inversion at the origin. However, the choices above ensure that the final matrix has determinant $+1$. This is a very useful property because otherwise a determinant of $-1$ would result in the transformation of a right-handed system into a left-handed system (and vice versa). With the definitions above, the final change-of-basis matrix is directly suitable for transforming symmetry operations, atomic coordinates, orientation matrices or other crystallographic parameters.

The accumulated change-of-basis matrix is built by left-multiplying at each step:

$$C_{\text{total}} = C_{\text{first}} \cdot C_{\text{second}} \cdot \ldots \cdot C_{\text{last}}$$

---

## 6. Numerical Stability: The Tolerance $\varepsilon$

To make the iterative reduction algorithm numerically stable, a tolerance $\varepsilon$ is used in all comparisons. This tolerance accounts for the uncertainties implicitly introduced by the use of finite-precision algebra.

The recommended tolerance is:

$$\varepsilon = \varepsilon_{\text{rel}} \cdot V^{1/3}$$

where $V = \sqrt{\det G}$ is the cell volume and $\varepsilon_{\text{rel}} = 10^{-5}$ in practice. The factor $V^{1/3}$ scales with cell dimensions (it is exact for cubic cells). All comparisons in steps A1–A8 use this tolerance:

| Exact comparison | Implementation with $\varepsilon$ |
|---|---|
| $x < y$ | $x < y - \varepsilon$ |
| $x > y$ | $y < x - \varepsilon$ |
| $x = y$ | $\text{not } (x < y-\varepsilon) \text{ and not } (y < x-\varepsilon)$ |
| $x \leq y$ | $\text{not } (y < x - \varepsilon)$ |
| $x \geq y$ | $\text{not } (x < y - \varepsilon)$ |

**Why is this necessary?** Practical application of the algorithms of Gruber (1973), Křivý & Gruber (1976) and Zuo et al. (1995) reveals that rounding errors owing to floating-point arithmetic can lead to infinite loops given typical experimentally observed cell parameters. In approximately 14,000 trials with reasonable unit-cell parameters, the algorithm enters infinite loops approximately 100 times (i.e., the failure rate is 0.7%).

**A special consideration for A3/A4:** Using the exact less-than comparison is the correct approach for the sign function evaluations in steps A5, A6, and A7, because the values involved are first compared (using the tolerance) with the parameters $A$ or $B$, which must be strictly greater than zero because the unit cell is degenerate otherwise. To further maximize the numerical stability, the expression $lmn > 0$ in step A3 is treated in a special way: each parameter is tested individually, counting the number of positive and negative values, then using those integer counts to decide whether $lmn > 0$. All evaluations in the reduction procedure are implemented using only additions and subtractions — floating-point multiplications are completely avoided.

---

## 7. Convergence and Termination

The algorithm terminates because each transformation that fires strictly decreases a non-negative definite quantity. The key insight is:

- Steps A1, A2 decrease or maintain $A + B + C$ (the trace of $G$).
- Steps A5, A6, A7 decrease $C$ (or $B$) by reducing $|\xi|$, $|\eta|$, $|\zeta|$ respectively.
- Step A8 decreases $C$ since $C' = C + A + B + \xi + \eta + \zeta < C$ by the firing condition.

Since $A + B + C \geq 3V^{2/3} > 0$ (bounded below by the AM-GM inequality on lattice vectors), and each firing step decreases it by a computable amount, the number of steps is finite.

---

## 8. The 44 Niggli Lattice Characters

Once you have the Niggli-reduced parameters $(A, B, C, \xi, \eta, \zeta)$, the lattice type is read off from a table of 44 **lattice characters** (also called Niggli types). These 44 different lattice characters arise because continuous deformations within a Bravais type are possible only within each character; to move between characters you must pass through special metric relationships. Each can be recognized from the relations between the elements of the reduced form.

The 44 types are classified by:
1. The equalities among $A, B, C$ (e.g., $A = B = C$, $A = B < C$, $A < B = C$, $A < B < C$)
2. The equalities/zeros among $\xi, \eta, \zeta$ (e.g., $\xi = \eta = \zeta = 0$, $\xi = \eta \neq 0$, etc.)
3. The type (I: all $\leq 0$, II: all $> 0$)

Here is the complete classification table (Niggli 1928, as tabulated in *International Tables*, §9.3.2). Each row gives the Niggli type number, the metric conditions on $(A,B,C,\xi,\eta,\zeta)$, and the Bravais lattice it corresponds to:

| Type | Conditions | Bravais Lattice |
|------|-----------|-----------------|
| 1 | $A = B = C$; $\xi = \eta = \zeta > 0$ (all equal, Type II) | cF |
| 2 | $A = B = C$; $\xi = \eta = \zeta < 0$ (all equal, Type I) | cI |
| 3 | $A = B = C$; $\xi = \eta = \zeta = 0$ | cP or hR |
| 4 | $A = B = C$; $\xi = \eta \neq \zeta$, all $> 0$ | hR |
| 5 | $A = B = C$; $\xi = \eta \neq \zeta$, all $< 0$ | hR |
| 6 | $A = B = C$; $\xi = \zeta \neq \eta$, all $< 0$ | hR |
| 7 | $A = B = C$; $\xi = \zeta$, $\eta = 0$, others $> 0$ | hR |
| 8 | $A = B = C$; $\xi = \eta = 0$, $\zeta \neq 0$ | oI or mI |
| 9 | $A = B$; $\xi = \eta = \zeta = 0$ | tP or oP |
| 10 | $A = B$; $\xi = \eta$, $\zeta = 0$, $\xi < 0$ | hR |
| 11 | $A = B$; $\xi = \eta$, $\zeta = 2\xi < 0$ | hP |
| 12 | $A = B$; $\xi = \eta \neq 0$, $\zeta = 0$ | oC (or oA) |
| 13 | $A = B$; $\xi = \eta \neq \zeta$, all $\leq 0$ | oI |
| 14 | $A = B$; $\xi = \eta$, $\zeta > 0$, $\xi < 0$ | mI |
| 15 | $A = B$; $\xi = \eta$, $\zeta > 0$, all $> 0$ | tI |
| 16 | $B = C$; $\xi = \zeta = 0$, $\eta = 0$ | tP or oP |
| 17 | $B = C$; $\xi = B/2$, $\eta = \zeta = 0$ | hR (obverse) |
| 18 | $B = C$; $\xi = \zeta$, $\eta = 0$, $\xi < 0$ | oF |
| 19 | $B = C$; $\xi = \zeta$, $\eta \neq 0$, all $< 0$ | oI |
| 20 | $B = C$; $\xi$, $\eta$, $\zeta$ general, Type I | mI |
| 21 | $B = C$; $\xi = B/2$, $\zeta = 0$, $\eta < 0$ | hR |
| 22 | $B = C$; $\zeta = 0$, $\xi = \eta < 0$ | oC |
| 23 | $B = C$; general Type II | mC |
| 24 | $A < B < C$; $\xi = \eta = \zeta = 0$ | oP |
| 25 | $A < B < C$; $\xi = 0$, $\eta = \zeta = 0$ | mP |
| 26 | $A < B < C$; $\xi$, $\eta$, $\zeta$ all $= 0$ | oC |
| 27 | $A < B < C$; $\xi = B$, others $= 0$ | mC |
| 28 | $A < B < C$; $\xi = 0$, $\eta = 0$, $\zeta \neq 0$ | mP |
| 29 | $A < B < C$; $\xi = 0$, $\eta \neq 0$, $\zeta = 0$ | mP |
| 30 | $A < B < C$; $\xi = \eta = 0$, $\zeta < 0$ | mP |
| 31 | $A < B < C$; $\xi \neq 0$, $\eta = \zeta = 0$ | mP |
| 32 | $A < B < C$; general Type I (all $< 0$) | aP |
| 33 | $A < B < C$; one of $\xi, \eta, \zeta = 0$, others $< 0$ | mP |
| 34 | $A < B < C$; $\xi + \eta + \zeta + A + B = 0$, Type I | mI |
| 35–44 | Various specific Type I/II combinations for $A < B < C$ with mixed constraints | aP, mP, mI, oF, etc. |

(The exact table runs to 44 entries with conditions becoming increasingly specific; the full table is in *International Tables*, Table 9.3.1 and in Gruber 1973, Table 1.)

The key point: once you have the Niggli-reduced $(A, B, C, \xi, \eta, \zeta)$ in exact arithmetic, exactly one of these 44 patterns is satisfied, which uniquely determines the Bravais lattice type. In practice with experimental data, metric symmetry searching (Le Page, 1982) is more robust than a direct lookup.

---

## 9. Worked Example in Full Detail

Let us reduce a triclinic cell with:

$$a = 4.0\ \text{Å},\ b = 5.0\ \text{Å},\ c = 6.0\ \text{Å}$$
$$\alpha = 100°,\ \beta = 80°,\ \gamma = 70°$$

**Initialize Niggli parameters:**

$$A = 16.0,\quad B = 25.0,\quad C = 36.0$$

$$\xi = 2(5)(6)\cos(100°) = 60 \times (-0.1736) = -10.417$$
$$\eta = 2(6)(4)\cos(80°) = 48 \times 0.1736 = +8.333$$
$$\zeta = 2(4)(5)\cos(70°) = 40 \times 0.3420 = +13.681$$

$C_{\text{total}} = I$.

**Pass 1:**

- **A1:** $A = 16 < B = 25$. Secondary: $A \neq B$. No fire.
- **A2:** $B = 25 < C = 36$. No fire.
- **A3:** $l = \text{sign}(-10.417) = -1$, $m = \text{sign}(+8.333) = +1$, $n = \text{sign}(+13.681) = +1$. $lmn = (-1)(+1)(+1) = -1 \neq 1$. No fire.
- **A4:** $lmn = -1 \leq 0$ and not all $= -1$. **Fire.** We need to make all signs $\leq 0$.
  - $l = -1$: $i = 1$ (already negative, keep)
  - $m = +1$: $j = -1$ (flip to negative)
  - $n = +1$: $k = -1$ (flip to negative)
  - $ijk = (1)(-1)(-1) = +1 \neq -1$, so no adjustment to $r$.
  - Apply $C_{\text{A4}} = \text{diag}(1, -1, -1)$: flips $\mathbf{b}$ and $\mathbf{c}$.
  - Updated: $A = 16$, $B = 25$, $C = 36$, $\xi = -(-10.417)(-1)(-1) \to $... let's be careful.

The transformation $(\mathbf{a}', \mathbf{b}', \mathbf{c}') = (\mathbf{a}, \mathbf{b}, \mathbf{c})\text{diag}(i,j,k)$ gives:

$$\mathbf{a}' = \mathbf{a},\quad \mathbf{b}' = -\mathbf{b},\quad \mathbf{c}' = -\mathbf{c}$$

$$\xi' = 2\mathbf{b}'\cdot\mathbf{c}' = 2(-\mathbf{b})\cdot(-\mathbf{c}) = +\xi \cdot jk = -10.417 \cdot (-1)(-1) = -10.417$$
$$\eta' = 2\mathbf{c}'\cdot\mathbf{a}' = 2(-\mathbf{c})\cdot\mathbf{a} = \eta \cdot ik = +8.333 \cdot (1)(-1) = -8.333$$
$$\zeta' = 2\mathbf{a}'\cdot\mathbf{b}' = 2\mathbf{a}\cdot(-\mathbf{b}) = \zeta \cdot ij = +13.681 \cdot (1)(-1) = -13.681$$

New state: $A = 16$, $B = 25$, $C = 36$, $\xi = -10.417$, $\eta = -8.333$, $\zeta = -13.681$.

Now $l = m = n = -1$, $lmn = -1$. Type I. **Go to A1.**

**Pass 2:**

- **A1:** $A = 16 < B = 25$. No fire.
- **A2:** $B = 25 < C = 36$. No fire.
- **A3:** $lmn = -1 \neq 1$. No fire.
- **A4:** $lmn = -1$, and all are $-1$. The condition says "if $l=m=n=-1$, do nothing in A4." **No fire.** (This is the pure Type I case; no adjustment needed.)
- **A5:** $|\xi| = 10.417 \leq B = 25$. Secondary conditions: $|\xi| \neq B$, $|\xi| \neq -B$. **No fire.**
- **A6:** $|\eta| = 8.333 \leq A = 16$. **No fire.**
- **A7:** $|\zeta| = 13.681 \leq A = 16$. **No fire.**
- **A8:** $\xi + \eta + \zeta + A + B = -10.417 - 8.333 - 13.681 + 16 + 25 = +8.569 > 0$. **No fire.**

**Done.** The Niggli-reduced cell is:

$$A = 16,\quad B = 25,\quad C = 36$$
$$\xi = -10.417,\quad \eta = -8.333,\quad \zeta = -13.681$$

In conventional parameters:
$$a = 4.0\ \text{Å},\quad b = 5.0\ \text{Å},\quad c = 6.0\ \text{Å}$$
$$\alpha = \arccos(\xi/2bc) = 100°,\quad \beta = \arccos(\eta/2ca) = 100°,\quad \gamma = \arccos(\zeta/2ab) = 110°$$

Wait — note that $\gamma$ changed from $70°$ to $110°$ because we flipped $\mathbf{b}$. The physical lattice is the same; we've just made all angles obtuse (Type I), which is the canonical form.

Change-of-basis matrix:

$$C_{\text{total}} = C_{\text{A4}} = \begin{pmatrix} 1 & 0 & 0 \\ 0 & -1 & 0 \\ 0 & 0 & -1 \end{pmatrix}$$

This matches Niggli type 32 (general triclinic, $A < B < C$, all off-diagonal $< 0$, Type I) → Bravais lattice **aP** (primitive triclinic).

---

## 10. Relation to Buerger Reduction and the Gruber (1973) Algorithm

It's worth distinguishing clearly:

**Buerger-reduced cell (1957):** A cell satisfying only the main conditions M1–M4 (lengths sorted, off-diagonal bounded). This is *not* unique — Gruber (1973) showed there can be up to **5** Buerger-reduced cells for the same lattice.

**Gruber (1973) algorithm (steps N1–N3, B1–B5):** A refinement of Buerger reduction. Steps N1–N3 handle the main conditions (sorting and sign normalization) using the integer-rounding function $\lfloor \cdot \rceil$ (nearest integer) rather than just the sign, which reduces in one shot rather than incrementally. Steps B1–B5 correspond to A4–A8 above. This reaches a Buerger cell, which may not be the unique Niggli cell.

**Křivý–Gruber (1976) algorithm (steps A1–A8):** Adds the tie-breaking conditions to reach the unique Niggli cell. The key additional logic is in A1 and A2 (the secondary conditions involving $|\xi|$ vs $|\eta|$, etc.) and in the boundary clauses of A5–A8.

The parameter updates in A5–A7 use $\text{sign}(\cdot)$ in Křivý–Gruber, but use $\lfloor \cdot/(2A) \rceil$ or $\lfloor \cdot/(2B) \rceil$ (the nearest-integer division) in Gruber (1973). The nearest-integer version reduces in one step; the sign version needs multiple passes but is simpler to analyze.

---

## 11. Practical Considerations for Powder Diffraction and PDF Work

**Primitive cell requirement:** Niggli reduction is defined only for primitive cells. If you start with a centered cell (e.g., body-centered cubic, face-centered, $C$-centered), you must first transform to the primitive setting:

- BCC (I-centered): transformation $\frac{1}{2}\begin{pmatrix}-1&1&1\\1&-1&1\\1&1&-1\end{pmatrix}$
- FCC (F-centered): transformation $\frac{1}{2}\begin{pmatrix}0&1&1\\1&0&1\\1&1&0\end{pmatrix}$
- C-centered: transformation $\frac{1}{2}\begin{pmatrix}1&-1&0\\1&1&0\\0&0&2\end{pmatrix}$

Then reduce the primitive cell; after reduction, look up the Niggli type to identify the Bravais lattice and transform back to the conventional setting.

**Comparison of cells from different codes:** Two cells describe the same lattice if and only if they reduce to the same Niggli cell. This is why Niggli reduction is the right tool when comparing a cell output from Mantid's peak indexing against a published ICSD or NOMAD result — reduce both and check equality of $(A, B, C, \xi, \eta, \zeta)$ within experimental tolerance.

**ISODISTORT supercell identification:** ISODISTORT works from the primitive parent cell. If your RMCProfile supercell looks geometrically different from the published conventional cell, reduce both primitive cells with Niggli and verify they match before assuming an error.

**Indexing software:** DICVOL, TREOR, and N-TREOR all internally reduce candidate cells to Niggli form before comparing with observed $d$-spacings, which is why two candidate cells that are related by a unimodular transformation appear as duplicates and get de-duplicated at the Niggli comparison stage.