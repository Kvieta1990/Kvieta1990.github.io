---
layout: post
title: Learning Notes on Denoising Diffusion Probablistic Model
subtitle:
tags: [AI, machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/diffusion_model.png"
   style="border:none;"
   width="700"
   alt="diffusion_model"
   title="diffusion_model" /><br>
<em>Image generated with Gemini with the prompt 'generate an image with a person thinking hardly on how diffusion model really works. follow the ghibli style'.</em></p>

<br>

My learning for the diffusion model started with the cource by fast.ai, which is a great learning resource. It tries to stay on the practical level with a bit of technical or mathematical explanations but may not be that deep. When first learning through the course, I thought everything is making perfect sense but after a few weeks not touching the subject and when trying to get back to it, I found myself totally lost, forgetting all the logic and understanding. The problem is I vaguely remember how things are working together and I might have read something relevant here and there, and therefore something beyond the scope of the course did come into my mind randomly. When having all those pieces of knowledge in mind without a systematic framework for understanding the subject, I found myself lost, I mean totally lost. That feel is something like, did I ever learn about this before? Yes. But how does the training process really go? The course seems to explain the denoising process in an intuitive way but how does it really work in practice? I mean, I know what it is trying to say -- given a noisy image, if we have some 'API' (named used in the course) that predict the noise, we can try to make the image less noisy, moving closer towards the clean image target. But what is the 'API' and how did we ever obtain it? When initially learning through the course, I was following the story flow of the course so not even having that much time to really think about why and how about this -- at that time of initial learning, the course did make perfect sense since my brain was staying together with the course on the intuitive level. Later on, when coming from a fresh mode, my brain could not figure out at all what is really happening.

So I decided to learn with those multiple GPT models, namely Gemini, ChatGPT and Claude. I was starting with the very naive prompt as below -- as I mentioned, the whole subject was totally a mess in my brain at that time and therefore the question I came up with may not make any sense at all. Fortunately, Gemini initially was kind of understanding my pain and maybe it is just trying to come up with something relevant to what I asked. Anyways, its response gave me something to start with. Since I was totally not sure whether the response makes any real sense or not, I was trying to give the response to both ChatGPT and Claude to let them judge and give me further information. The Claude version did not go so well since I found an obvious mistake in its response so I stayed with the ChatGPT most of the time for all the following studies. At the end of the day, I think now at least I have a much more clear pipeline of understanding in my brain by quite a lot of back-and-forth interactions with ChatGPT, based on which I put down the current post to record the understanding of the topic.

Here below is my very initial prompt to Gemini,

---

starting from the clean image, we add in noise step by step until we get a complete noise image. then we revserse the process during training to try to tune the parameter of network target at minimizing the difference from the noise we added in. If my understanding is correct, we have different noise added at different time step while noising the data, are all the noise at all time step considered into the calculation of the loss function? Also, there is no guarantee that the denoised image in the reverse process is the same as the image at the same stage in the forward noising stage so are the noise in the forward and reverse process really comparable?

maybe I am still confused by the algorithm, so please give me some demo with some very easy and straightforward examples to help my understanding

---

Now, let's dive into the topic.

## 1. Training Walkthrough, at a high level

Diffusion models learn by doing the opposite of what they'll eventually be used for: instead of learning to *create* images, they learn to *undo noise*.

**The setup.** Start with a real image, $$x_0$$. A fixed, precomputed schedule controls how much Gaussian noise gets mixed in as a function of a timestep $$t$$, running from $$t=1$$ (barely touched) to $$t=T$$ (pure noise, typically $$T=1000$$). Because each step of that schedule is Gaussian, the noise doesn't need to be added incrementally -- there's a closed-form shortcut that jumps straight from $$x_0$$ to any $$x_t$$:

$$x_t = \sqrt{\bar\alpha_t}\,x_0 + \sqrt{1-\bar\alpha_t}\,\epsilon, \qquad \epsilon \sim \mathcal{N}(0,I)$$

$$\bar\alpha_t$$ is a number between 0 and 1, read off a precomputed table (where that table comes from is covered in Section 4.2). As $$t$$ grows, $$\bar\alpha_t$$ shrinks toward 0 -- less of the original signal survives, more noise dominates.

**One training example.** For a given image:

1. Pick a random timestep $$t \sim \text{Uniform}(1,\dots,T)$$.
2. Sample fresh noise $$\epsilon \sim \mathcal N(0,I)$$.
3. Build $$x_t$$ with the formula above.
4. Feed $$x_t$$ (and $$t$$) into the network and ask it to guess which $$\epsilon$$ was used.
5. Compare the guess $$\hat\epsilon$$ against the real $$\epsilon$$: $$\mathcal L = \|\epsilon - \hat\epsilon\|^2$$.

That is it, at the high level -- the training *data* is a noisy image at some random noise level, and the training *target* is the exact noise that produced it. What the network does internally to produce $$\hat\epsilon$$ is deferred to Section 4.

Because $$t$$ is re-sampled randomly on every example, no single training step covers every noise level -- but across millions of steps, every $$t$$ gets visited proportionally. The objective is really an expectation over the whole schedule:

$$\mathcal L = \mathbb E_{x_0,\,t,\,\epsilon}\big[\|\epsilon - \epsilon_\theta(x_t,t)\|^2\big]$$

**Where does this actually happen -- pixels, or something else?** Most modern text-to-image systems (Stable Diffusion [1] and similar) are *latent* diffusion models, and this matters for what "$$x_0$$" really is:

- A variational autoencoder (VAE) [2] is trained once, beforehand, and then frozen. It compresses images into a much smaller latent grid -- e.g. a 512×512×3 image down to something like 64×64×4.
- From that point on, every $$x_0$$ and $$x_t$$ in the formulas above lives in that compressed latent space, not pixel space: $$x_0 = \text{VAE}_{\text{encoder}}(\text{image})$$, and the noise $$\epsilon$$ is sampled with the same shape as that latent, then added directly to it.
- The network never sees a raw pixel during training. Only after the full reverse process (Section 2) produces a clean latent $$\hat x_0$$ does a VAE decoder turn it back into pixels: $$\text{image} = \text{VAE}_{\text{decoder}}(\hat x_0)$$.

This is a deliberate design choice, not a mathematical necessity -- the original Denoising Diffusion Probabilistic Model (DDPM) [3], GLIDE (Guided Language to Image Diffusion for Generation and Editing) [4], and Imagen [5] instead apply the exact same equations directly to pixels, no VAE involved. The latent version is popular mainly because diffusing a 64×64×4 grid is far cheaper than diffusing a 512×512×3 image, while a well-trained VAE loses little perceptually-relevant detail in the compression.

## 2. Generating images: the reverse process

Generation reverses the story: start from pure noise $$x_T \sim \mathcal N(0,I)$$ and walk backward, $$x_T \to x_{T-1} \to \cdots \to x_1 \to x_0$$, using the same trained network at every step.

**Why not jump straight from $$x_t$$ to $$x_0$$?** The forward formula can actually be algebraically rearranged into a one-shot estimate of the clean image, given a noise prediction:

$$\hat x_0 = \frac{x_t - \sqrt{1-\bar\alpha_t}\,\epsilon_\theta(x_t,t)}{\sqrt{\bar\alpha_t}}$$

But at large $$t$$, $$x_t$$ is mostly noise -- there isn't much signal left for the network to work with, so this single-shot $$\hat x_0$$ is a coarse, low-confidence guess. Committing to it in one leap tends to produce blurry or inconsistent results. Instead, diffusion takes many small, conservative steps: predict noise, move only *slightly* toward the estimated clean image, then re-predict using the updated, less-noisy input. Each step gets a better-conditioned problem than the last, which is what makes the final output sharp and coherent.

**The actual step**, from $$x_t$$ to $$x_{t-1}$$:

$$x_{t-1} = \frac{1}{\sqrt{\alpha_t}}\left(x_t - \frac{1-\alpha_t}{\sqrt{1-\bar\alpha_t}}\,\epsilon_\theta(x_t,t)\right) + \sigma_t z$$

$$z \sim \mathcal N(0,I)$$

Every symbol here:

| Symbol | Meaning |
|---|---|
| $$\beta_t$$ | per-step noise variance, fixed ahead of time by the schedule (Section 4.2) |
| $$\alpha_t = 1-\beta_t$$ | fraction of signal retained in a *single* step $$t$$ |
| $$\bar\alpha_t = \prod_{s=1}^t \alpha_s$$ | cumulative retained signal from step 1 through $$t$$ |
| $$\epsilon_\theta(x_t,t)$$ | the network's noise prediction, given the current noisy input and timestep |
| $$\sigma_t$$ | standard deviation of a small amount of fresh randomness re-injected at this step (commonly set to $$\sqrt{\beta_t}$$, following the schedule directly) |
| $$z$$ | a freshly sampled standard Gaussian, independent at every step (skipped, i.e. $$z=0$$, on the very last step $$t=1$$) |

Intuitively: subtract off the network's best guess at the noise (scaled appropriately), rescale by how much signal this step is supposed to retain, then stir back in a small amount of fresh randomness so the sampling process doesn't collapse onto one deterministic path. Repeating this $$T$$ times, with the *same* network weights but a different $$t$$ fed in each time, is the entire generation procedure.

## 3. The score function bridge

There's a second, equivalent way to describe what the network has learned, borrowed from *score-based* generative modeling [6].

The **score** of a distribution is $$s_t(x) = \nabla_{x}\log p_t(x)$$ -- the direction in which log-probability increases fastest. Intuitively, at any noisy point $$x_t$$, the score points toward the nearest region of more plausible (less noisy, more image-like) data. That is exactly the information the reverse process needs at every step.

The network was trained to predict $$\epsilon$$, not the score -- but the two turn out to be the same quantity, up to a known rescaling. The bridge rests on two results, derived in full below.

### 3.1 The conditional score

Conditioned on a specific $$x_0$$, the forward process makes $$x_t$$ Gaussian:

$$q(x_t\mid x_0) = \mathcal N\big(\sqrt{\bar\alpha_t}\,x_0,\ (1-\bar\alpha_t)I\big)$$

**Start from the general multivariate Gaussian density.** For any Gaussian $$\mathcal N(\mu,\Sigma)$$ in $$d$$ dimensions, the probability density at a point $$x$$ is:

$$q(x) = \frac{1}{(2\pi)^{d/2}|\Sigma|^{1/2}}\exp\left(-\frac{1}{2}(x-\mu)^\top\Sigma^{-1}(x-\mu)\right)$$

The normalizing prefactor makes the total probability integrate to 1; the part that depends on where $$x$$ actually sits is entirely inside the exponent.

**Plug in this specific Gaussian.** Here $$\mu = \sqrt{\bar\alpha_t}\,x_0$$ and $$\Sigma = (1-\bar\alpha_t)I$$ -- a single scalar, $$(1-\bar\alpha_t)$$, times the identity matrix. This is what "isotropic" noise means: every dimension gets the same variance, with no correlation between dimensions, so the whole covariance matrix collapses to one number times $$I$$.

**Simplify the quadratic form.** Because $$\Sigma$$ is a scalar times $$I$$, its inverse is trivial: $$\Sigma^{-1}=\frac{1}{1-\bar\alpha_t}I$$. So the exponent's quadratic term becomes:

$$(x_t-\mu)^\top\Sigma^{-1}(x_t-\mu) = \frac{1}{1-\bar\alpha_t}(x_t-\mu)^\top(x_t-\mu) = \frac{\|x_t-\mu\|^2}{1-\bar\alpha_t}$$

(A vector dotted with itself, $$(x_t-\mu)^\top(x_t-\mu)$$, is by definition $$\|x_t-\mu\|^2$$.) The full density is therefore:

$$q(x_t\mid x_0) = \frac{1}{(2\pi)^{d/2}(1-\bar\alpha_t)^{d/2}}\exp\left(-\frac{\|x_t-\sqrt{\bar\alpha_t}\,x_0\|^2}{2(1-\bar\alpha_t)}\right)$$

**Take the log.** Logging a product turns it into a sum, and $$\log\exp(\cdot)$$ cancels:

$$\log q(x_t\mid x_0) = -\frac{d}{2}\log(2\pi) \;-\; \frac{d}{2}\log(1-\bar\alpha_t) \;-\; \frac{\|x_t-\sqrt{\bar\alpha_t}\,x_0\|^2}{2(1-\bar\alpha_t)}$$

**Drop the terms that do not depend on $$x_t$$.** The next step is differentiating this with respect to $$x_t$$, and the gradient of anything that does not contain $$x_t$$ is zero. $$-\frac{d}{2}\log(2\pi)$$ depends only on the fixed dimensionality $$d$$. $$-\frac{d}{2}\log(1-\bar\alpha_t)$$ depends on $$\bar\alpha_t$$, which is fixed once the timestep $$t$$ is fixed. Neither touches $$x_t$$, so both collapse into a single constant carried along without being written out term by term:

$$\log q(x_t\mid x_0) = -\frac{\|x_t-\sqrt{\bar\alpha_t}\,x_0\|^2}{2(1-\bar\alpha_t)} + \text{const}$$

**Differentiate.** Using the standard vector-calculus identity $$\nabla_x\|x-a\|^2 = 2(x-a)$$:

$$\nabla_{x_t}\log q(x_t\mid x_0) = -\frac{1}{2(1-\bar\alpha_t)}\cdot 2\big(x_t-\sqrt{\bar\alpha_t}\,x_0\big) = -\frac{x_t-\sqrt{\bar\alpha_t}\,x_0}{1-\bar\alpha_t}$$

**Substitute the forward equation.** $$x_t-\sqrt{\bar\alpha_t}\,x_0 = \sqrt{1-\bar\alpha_t}\,\epsilon$$, so:

$$\nabla_{x_t}\log q(x_t\mid x_0) = -\frac{\sqrt{1-\bar\alpha_t}\,\epsilon}{1-\bar\alpha_t} = -\frac{\epsilon}{\sqrt{1-\bar\alpha_t}}$$

So *if* the specific $$x_0$$ (and the exact $$\epsilon$$ that produced this $$x_t$$) were known, the conditional score would just be that noise, rescaled. In practice neither is available at generation time -- what is actually needed is the *marginal* score $$\nabla_{x_t}\log p_t(x_t)$$, averaged over every $$x_0$$ that could plausibly have produced this $$x_t$$. That average is the second result.

### 3.2 From conditional to marginal score (denoising score matching [7])

Let $$p(x_0)$$ denote the distribution of clean images -- the training data distribution. The marginal density of $$x_t$$ is obtained by integrating the conditional over every possible clean image that could have produced it:

$$p_t(x_t) = \int q(x_t\mid x_0)\,p(x_0)\,dx_0$$

The marginal score is $$\nabla_{x_t}\log p_t(x_t) = \nabla_{x_t}p_t(x_t)\,/\,p_t(x_t)$$. Differentiating the integral with respect to $$x_t$$ (moving the gradient inside the integral, valid under the mild regularity conditions that hold here):

$$\nabla_{x_t}p_t(x_t) = \int \nabla_{x_t}q(x_t\mid x_0)\,p(x_0)\,dx_0$$

The identity $$\nabla_x f(x) = f(x)\,\nabla_x\log f(x)$$ -- which follows directly from the chain rule applied to $$\log f(x)$$ -- turns $$\nabla_{x_t}q(x_t\mid x_0)$$ into $$q(x_t\mid x_0)\,\nabla_{x_t}\log q(x_t\mid x_0)$$:

$$\nabla_{x_t}p_t(x_t) = \int q(x_t\mid x_0)\,\nabla_{x_t}\log q(x_t\mid x_0)\,p(x_0)\,dx_0$$

Dividing both sides by $$p_t(x_t)$$:

$$\nabla_{x_t}\log p_t(x_t) = \int \frac{q(x_t\mid x_0)\,p(x_0)}{p_t(x_t)}\,\nabla_{x_t}\log q(x_t\mid x_0)\,dx_0$$

The fraction inside the integral is exactly Bayes' rule for the posterior over $$x_0$$ given $$x_t$$ -- since $$p_t(x_t)=\int q(x_t\mid x_0)p(x_0)\,dx_0$$ is precisely the normalizing constant that turns the joint $$q(x_t\mid x_0)p(x_0)$$ into a proper posterior, $$q(x_0\mid x_t) = q(x_t\mid x_0)\,p(x_0)\,/\,p_t(x_t)$$. Substituting this in:

$$\nabla_{x_t}\log p_t(x_t) = \int q(x_0\mid x_t)\,\nabla_{x_t}\log q(x_t\mid x_0)\,dx_0 = \mathbb E_{x_0\sim q(x_0\mid x_t)}\big[\nabla_{x_t}\log q(x_t\mid x_0)\big]$$

the marginal score is the posterior-weighted average of the conditional score.

### 3.3 Combining the two results

Substituting the conditional score from Section 3.1 into the averaging identity from Section 3.2:

$$\nabla_{x_t}\log p_t(x_t) = \mathbb E_{x_0\sim q(x_0\mid x_t)}\left[-\frac{\epsilon}{\sqrt{1-\bar\alpha_t}}\right] = -\frac{1}{\sqrt{1-\bar\alpha_t}}\,\mathbb E[\epsilon\mid x_t]$$

(The scalar $$1/\sqrt{1-\bar\alpha_t}$$ does not depend on $$x_0$$, so it pulls straight out of the expectation.)

### 3.4 What the network's loss actually estimates

Separately, minimizing an $$\|\epsilon-\hat\epsilon\|^2$$ loss is ordinary least-squares regression, whose optimal solution is the conditional expectation of the target: $$\epsilon_\theta(x_t,t) \approx \mathbb E[\epsilon\mid x_t]$$. Substituting the result here into Section 3.3 gives the bridge:

$$s_\theta(x_t,t) = \nabla_{x_t}\log p_t(x_t) \approx -\frac{\epsilon_\theta(x_t,t)}{\sqrt{1-\bar\alpha_t}}$$

equivalently

$$\epsilon_\theta(x_t,t) \approx -\sqrt{1-\bar\alpha_t}\,\nabla_{x_t}\log p_t(x_t)$$

Practically, this is why DDPM-style noise prediction and score-based/stochastic differential equation (SDE) diffusion are the same underlying model wearing different notation -- samplers and theory built for one transfer directly to the other via this rescaling.

## 4. Under the hood: how the network is actually trained

This section unpacks what Section 1 deferred -- what actually happens between "noisy input" and "noise prediction", including how a text prompt steers the result. The denoising network itself is almost always a U-Net [8]: a convolutional architecture, originally designed for biomedical image segmentation, whose encoder progressively downsamples the input and whose decoder upsamples it back, with skip connections linking matching resolutions (the original block input gets added back onto the result) -- giving it its characteristic U-shaped diagram.

### 4.1 What goes in

For text-to-image training, each dataset example is an (image, caption) pair, $$(x_0, c)$$. The caption is run once through a frozen, pretrained text encoder -- for example Contrastive Language-Image Pre-training (CLIP) [9] or the Text-to-Text Transfer Transformer (T5) [10] -- to produce a sequence of embeddings $$C$$, *before* diffusion training even starts. The full pipeline:

$$\text{image}+\text{caption} \;\to\; x_0 + C \;\to\; \text{add noise} \;\to\; x_t + C$$

$$x_t + C \;\to\; \text{U-Net + cross-attention} \;\to\; \hat\epsilon \;\to\; \|\epsilon-\hat\epsilon\|^2$$

> In the first pipeline, between $$x_0 + C$$ and `add noise` steps, there is an implicit layer of operation to encode the input images into the latent space.

### 4.2 The forward process is closed-form math, not a network

$$x_t = \sqrt{\bar\alpha_t}x_0 + \sqrt{1-\bar\alpha_t}\epsilon$$ involves no learned weights -- $$\bar\alpha_t$$ is a lookup into a table built *before training starts*. Where that table comes from:

**The per-step definition.** The forward process is originally specified one small step at a time:

$$q(x_t \mid x_{t-1}) = \mathcal N\big(\sqrt{1-\beta_t}\,x_{t-1},\ \beta_t I\big)$$

equivalently

$$x_t = \sqrt{\alpha_t}\,x_{t-1}+\sqrt{1-\alpha_t}\,\epsilon_t, \qquad \alpha_t=1-\beta_t$$

$$\beta_t$$ is the *noise budget for that single step*. Its values are not learned; they come from a hand-chosen schedule, fixed and cached before training starts. A small illustrative schedule, with $$T=5$$:

$$\beta = [0.0001,\ 0.005,\ 0.01,\ 0.015,\ 0.02]$$

increasing across steps, so each step injects a little more noise than the last. Real schedules run to $$T=1000$$ or more and are typically generated from a formula rather than listed by hand -- linear interpolation between a $$\beta_{\min}$$ and $$\beta_{\max}$$ is a common baseline,

$$
\beta_t = \beta_{\text{min}} + \frac{t - 1}{T - 1}(\beta_{\text{max}} - \beta_{\text{min}})
$$

The cosine schedule is another popular choice, and the formulation is a bit more complex,

$$\bar{\alpha}_t = \frac{f(t)}{f(0)},\ \ \ \ \ \ \ f(t) = cos^2\bigg(\frac{t/T + s}{1 + s}\cdot\frac{\pi}{2}\bigg)$$

where $$s$$ is an arbitrarily chosen small positive offset [11]. In this case, the schedule $$\beta_t$$ is actually not given explicitly, but we can derive it. Since,

$$\bar\alpha_t=\prod_{s=1}^{t}\alpha_s$$

it follows that,

$$
\frac{\bar\alpha_t}{\bar\alpha_{t-1}} = \alpha_t = 1 - \beta_t \Rightarrow \beta_t = 1 - \frac{\bar\alpha_t}{\bar\alpha_{t-1}} = 1 - \frac{f(t)}{f(t-1)}
$$

Anyways, a small five-step version is enough to see how the pieces fit together. Taking $$\alpha_t=1-\beta_t$$ term by term:

$$\alpha = [0.9999,\ 0.995,\ 0.99,\ 0.985,\ 0.98]$$

**From per-step to cumulative.** Chaining two such steps shows how the closed-form shortcut in Section 1 arises. Substituting the $$x_1$$ equation into the $$x_2$$ equation:

$$
\begin{align}
x_2 & = \sqrt{\alpha_2}\big(\sqrt{\alpha_1}x_0+\sqrt{1-\alpha_1}\epsilon_1\big)+\sqrt{1-\alpha_2}\,\epsilon_2\\
& = \sqrt{\alpha_1\alpha_2}\,x_0 + \underbrace{\bigg(\sqrt{\alpha_2(1-\alpha_1)}\,\epsilon_1+\sqrt{1-\alpha_2}\,\epsilon_2\bigg)}_{\text{sum of two independent Gaussians}}
\end{align}
$$

A sum of independent Gaussians is itself Gaussian, with variance equal to the sum of the individual variances: $$\alpha_2(1-\alpha_1)+(1-\alpha_2)=1-\alpha_1\alpha_2$$. The bracketed sum can therefore be replaced by a single equivalent noise term, $$\sqrt{1-\alpha_1\alpha_2}\,\epsilon$$:

$$x_2=\sqrt{\alpha_1\alpha_2}\,x_0+\sqrt{1-\alpha_1\alpha_2}\,\epsilon$$

With the schedule above, $$\alpha_1\alpha_2 = 0.9999\times0.995\approx0.9949$$, so after two steps roughly 99.5% of the original signal variance remains. Repeating this merge at every subsequent step projects the product of retention fractions out to step $$t$$, producing the cumulative quantity used throughout this document:

$$\bar\alpha_t=\prod_{s=1}^{t}\alpha_s$$

Carrying the same schedule through all five steps gives $$\bar\alpha_5\approx0.9508$$ -- after the full five-step schedule, about 95% of the original signal variance is still present, with the rest replaced by noise. This is exactly what turns the step-by-step process into the one-shot formula from Section 1:

$$x_t=\sqrt{\bar\alpha_t}\,x_0+\sqrt{1-\bar\alpha_t}\,\epsilon$$

So the three quantities relate as: $$\beta_t$$ is the raw per-step schedule input, $$\alpha_t=1-\beta_t$$ is the signal-retention fraction it implies for a single step, and $$\bar\alpha_t$$ is the cumulative retention fraction after chaining steps 1 through $$t$$ together. Sampling $$x_t$$ directly is therefore mathematically exact, not an approximation -- it is the same distribution as simulating $$x_1,x_2,\dots,x_t$$ one at a time, yielding one closed-form step instead of $$t$$ successive ones.

Once $$\alpha_t$$ and $$\bar\alpha_t$$ are computed and cached this way, $$t$$ is nothing more than a row index into that table.

### 4.3 Two separate conditioning pathways, inside the U-Net

The time step $$t$$ gets used a *second*, completely different way once inside the network -- easy to be confused with its role above, but the two are unrelated.
 
**Timestep pathway.** The U-Net's weights are reused at every one of the $$T$$ diffusion steps, from nearly pure noise down to nearly clean. But the task looks different at each end: near $$t=1$$ the network is doing fine cleanup on an input that is mostly signal already; near $$t=T$$ it has almost no real signal to anchor on and has to lean much more on learned priors (and on the caption). Without being told $$t$$, the network would have to infer which regime it is in purely from the statistics of $$x_t$$ -- a harder, noisier problem to learn. The timestep pathway hands it that information directly, in three steps.
 
**Step 1: turn the scalar $$t$$ into a vector.** Neural networks work better with vectors than raw scalars -- a bare integer gives the network no useful structure to learn from. $$t$$ is mapped into a vector using the same sinusoidal construction as Transformer positional encodings [12]:
 
$$PE(t)_{2i}=\sin\!\left(\frac{t}{10000^{2i/d}}\right)$$
 
$$PE(t)_{2i+1}=\cos\!\left(\frac{t}{10000^{2i/d}}\right)$$
 
Several reasons for this specific construction rather than, say, feeding in the raw integer or a one-hot vector: 1) nearby timesteps produce nearby vectors (smoothness), so the network generalizes across steps instead of treating each step independently; 2) mixing several frequencies lets the network distinguish both coarse position (which era of the schedule) and fine position (the exact step); 3) avoid values exploding for large $$t$$ the way a raw integer would -- to keep consistent with the general number scaling in the neural network.

> $$d$$ here is an arbitrarily chosen dimension of the resulted position encoding. For instance, the example presented below is using $$d = 4$$.

Concrete example, $$d=4$$ (so $$i=0,1$$), $$t=500$$:
 
- $$i=0$$: divisor $$10000^{0/4}=1$$, angle $$=500$$ rad $$\to \sin(500)\approx-0.468$$, $$\cos(500)\approx-0.884$$
- $$i=1$$: divisor $$10000^{2/4}=100$$, angle $$=5$$ rad $$\to \sin(5)\approx-0.959$$, $$\cos(5)\approx0.284$$
$$PE(500) = [-0.468,\ -0.884,\ -0.959,\ 0.284]$$

The rationale carried by #1 and #3 reasons presented above are straightforward to follow. For #2, we can see its logic by grouping the encoding list by the value of $$i$$ -- in the example above, we have $$i = 0, 1$$ and accordingly the first two numbers form the first group with $$i = 0$$ and the last two numbers form the second group with $$i = 1$$. With $$i = 0$$, the encoding equation just becomes $$sin(t)$$ and $$cos(t)$$, respectively, and the variation of the encoding is fast, with the frequency of $$\frac{1}{2\pi}$$ in the time step space. Therefore, this bit of the encoding 'cares' more about the fast variation of the noise across time steps. With $$i = 1$$, the encoding equations become $$sin(t/100)$$ and $$cos(t/100)$$, respectively, and the variation of the time step encoding in this case is very slow with the frequency of $$\frac{1}{200\pi}$$. Therefore, the $$i = 1$$ group of the encoding 'cares' more about the slow variation bit of the time step encoding. Putting them together, we have the neural network covering the different frequency components regarding the noise variation across the time steps.

**Step 2: project into the network's working dimension.** The sinusoidal vector is fixed, not learned. It is passed through a small learned MLP (Multi-Layer Perceptron), typically two linear layers with a nonlinearity:
 
$$\text{temb} = W_2\cdot\text{SiLU}(W_1\cdot PE(t)+b_1)+b_2$$
 
This lets the network learn how to reinterpret the raw sinusoidal signal, and projects it from the sinusoidal dimension to whatever channel width the U-Net's ResBlocks expect. Continuing the example with toy weights $$W_1=\begin{bmatrix}0.1&-0.2&0.3&0.1\\0.2&0.1&-0.1&0.4\end{bmatrix}$$, $$b_1=[0,0]$$:
 
$$W_1\cdot PE(500) = \begin{bmatrix}0.1(-0.468)+(-0.2)(-0.884)+0.3(-0.959)+0.1(0.284)\\0.2(-0.468)+0.1(-0.884)+(-0.1)(-0.959)+0.4(0.284)\end{bmatrix} = \begin{bmatrix}-0.129\\0.028\end{bmatrix}$$
 
Applying SiLU (see Section 4.4) elementwise: $$\text{SiLU}(-0.129)\approx-0.060$$, $$\text{SiLU}(0.028)\approx0.014$$. With a second layer $$W_2=I$$, $$b_2=0$$ for simplicity:
 
$$\text{temb} \approx [-0.060,\ 0.014]$$
 
> This is a self-contained illustration at $$d=4$$; the working example in Section 4.6 uses its own, independently hand-picked temb for a smaller 2-dimensional setup, so the two are not meant to represent literally the same diffusion step.
 
**Step 3: modulate the feature maps.** `temb` -- the same vector, reused at every ResBlock in the U-Net -- modulates *feature maps*: the intermediate activations inside the U-Net's conv layers, not $$x_t$$ itself. The mechanism is **FiLM** (Feature-wise Linear Modulation) [13]: rather than concatenating the timestep information onto the feature maps (clunky for convolutional data), a small linear layer projects `temb` into a per-channel scale $$\gamma$$ and shift $$b$$, and the feature map is rescaled channel by channel:
 
$$\gamma, b = \text{Linear}(\text{temb}), \qquad h' = \gamma \odot h + b$$
 
Stable Diffusion's U-Net implements this as "adaptive group norm" applied immediately after a GroupNorm layer. $$\odot$$ is elementwise multiplication per channel, so each channel of the feature map gets its own scale and shift, both conditioned on how noisy the current timestep is. Section 4.6 carries this exact mechanism through numerically applying a FiLM layer to the ResBlock output from that section's running example.

### 4.4 Inside a ResBlock

The U-Net is a stack of residual blocks (ResBlocks) [14]. Each computes $$h_{\text{out}} = x + f(x)$$ -- the input skips around a small convolutional stack and gets added back at the end, and accordingly such a process is called `skip connection`. This matters for trainability: during backpropagation, $$\partial h_{\text{out}}/\partial x = 1 + \partial f/\partial x$$, so the `+1` term guarantees a clean gradient path no matter how many blocks are stacked, which is what makes training of deep networks practical.

> `skip connection` basically refers to that the input of the ResBlock is skipping whatever transformation in the ResBlock and gets added directly to the output of the ResBlock.

A typical block chains normalization, activation, and convolution: `GroupNorm → SiLU → Conv → [FiLM by temb] → GroupNorm → SiLU → Conv` (the whole chain here corresponds to $$f(x)$$ in $$h_{out}$$), then adds the skip connection. GroupNorm is Group Normalization [15], and Conv is a convolutional layer. SiLU is the activation used throughout [16]:

$$\text{SiLU}(x) = x\cdot\sigma(x)$$

$$\sigma(x)=\frac{1}{1+e^{-x}} \qquad \text{(sigmoid)}$$

Unlike the Rectified Linear Unit (ReLU) [17]'s hard cutoff at 0, SiLU smoothly damps negative inputs toward -- but not exactly to -- zero: e.g. $$\text{SiLU}(-2) \approx -0.24$$, $$\text{SiLU}(2)\approx 1.76$$. That smoothness keeps gradients flowing more cleanly through very deep stacks.

### 4.5 The complete training step

1. Sample an (image, caption) pair; encode the caption once: $$C = \text{TextEncoder}(c)$$.
2. Sample $$t \sim \text{Uniform}(1,\dots,T)$$ and $$\epsilon \sim \mathcal N(0,I)$$.
3. Look up $$\bar\alpha_t$$, compute $$x_t = \sqrt{\bar\alpha_t}\,x_0 + \sqrt{1-\bar\alpha_t}\,\epsilon$$ (Section 4.2).
4. Compute $$\text{temb} = \text{MLP}(\text{sinusoidal}(t))$$, then $$\hat\epsilon = \text{UNet}(x_t,\ \text{temb},\ C)$$ -- `temb` modulates ResBlocks via FiLM, $$C$$ conditions via cross-attention (Section 4.3).
5. $$\mathcal L = \|\epsilon - \hat\epsilon\|^2$$; backpropagate and update the U-Net's weights, including the small `temb` MLP and the FiLM/attention projection layers. (The text encoder itself is typically kept frozen.)

The training *target* is always the actually-sampled $$\epsilon$$, at every step, with or without a caption. Text conditioning never changes what's being predicted -- it only gives the network extra information to make that same prediction more accurately, by narrowing down what the clean image underneath the noise is likely to contain.

### 4.6 A concrete working example

Tiny dimensions so every step is checkable by hand. Embedding dimension $$d=2$$, latent $$x_0 = [1,-1]$$, caption embedding $$C = \begin{bmatrix}1&0\\0&1\end{bmatrix}$$ (two token vectors), $$\bar\alpha_t = 0.8$$, sampled noise $$\epsilon = [0.5,-0.2]$$. This version follows the full "ResBlock, then cross-attention" structure end to end, including both residual connections -- the ResBlock's own skip connection (Section 4.4) and cross-attention's separate residual -- rather than compressing past either.

**Forward step:**

$$x_t = \sqrt{0.8}\,[1,-1] + \sqrt{0.2}\,[0.5,-0.2]$$

$$= [0.894,-0.894]+[0.224,-0.089] = [1.118,-0.983]$$

**The ResBlock.** Following Section 4.4's structure exactly: `GroupNorm → SiLU → Conv → [FiLM by temb] → GroupNorm → SiLU → Conv`, then a skip connection closes the block.

*GroupNorm₁* (toy: mean/variance standardization only, with no extra learned affine beyond what FiLM already provides):

$$\text{mean}=\frac{1.118+(-0.983)}{2}=0.068,\qquad \text{std}=\left|\frac{1.118-(-0.983)}{2}\right|=1.051$$

$$\text{GroupNorm}_1(x_t) = \left[\frac{1.118-0.068}{1.051},\ \frac{-0.983-0.068}{1.051}\right] = [1.000,\,-1.000]$$

*SiLU₁:*

$$\text{SiLU}(1.000)\approx0.731,\qquad \text{SiLU}(-1.000)\approx-0.269$$

*Conv₁* (toy: identity, so this step leaves the numbers unchanged): $$[0.731,-0.269]$$

*FiLM* (Section 4.3): with $$\text{temb}=[-0.046,-0.003]$$, $$W_\gamma=[1,\,0.5]$$, $$b_\gamma=1$$, $$W_b=[0.2,\,-0.3]$$, $$b_b=0$$:

$$\gamma = W_\gamma\cdot\text{temb}+b_\gamma = 0.952, \qquad b = W_b\cdot\text{temb}+b_b = -0.008$$

$$h' = \gamma\odot[0.731,-0.269]+b = [0.696,-0.256]+(-0.008) = [0.688,\,-0.264]$$

*GroupNorm₂* applied to $$[0.688,-0.264]$$:

$$\text{mean}=0.212,\qquad\text{std}=0.476$$

$$\text{GroupNorm}_2 = \left[\frac{0.688-0.212}{0.476},\ \frac{-0.264-0.212}{0.476}\right] = [1.000,\,-1.000]$$

This lands on exactly the same $$[1.000,-1.000]$$ as GroupNorm₁ -- not a copy-paste artifact. With only two elements, mean/variance standardization always collapses to $$[\pm1,\mp1]$$ regardless of the input's actual scale, since it discards everything except the sign of the difference between the two entries. That means FiLM's specific scale and shift, applied between the two GroupNorms here, ends up invisible to everything downstream as long as $$\gamma$$ stays positive: it changes the numbers momentarily, but the second normalization erases that change. This is a limitation of the toy two-dimensional setup, not of the real architecture -- with the hundreds of channels a real ResBlock actually has, GroupNorm normalizes per-group statistics without discarding the relative structure FiLM introduces across channels, so FiLM's effect on the final output does survive there.

*SiLU₂:* identical input to SiLU₁, so identical output: $$[0.731,-0.269]$$

*Conv₂* (toy: identity): $$[0.731,-0.269]$$ -- this is $$f(x_t)$$, the ResBlock's full internal transformation.

*Skip connection* (the ResBlock's own residual from Section 4.4, $$h_{\text{out}}=x+f(x)$$):

$$r = x_t + f(x_t) = [1.118,-0.983]+[0.731,-0.269] = [1.849,\,-1.252]$$

**Cross-attention**, consuming the ResBlock's output $$r$$ as its query (the "ResBlock → Cross-attention" ordering from Section 4.3), toy: $$W_Q=W_K=W_V=I$$, so $$Q=r$$, $$K=V=C$$:

$$QK^\top = [1.849,-1.252], \qquad \text{scaled by }1/\sqrt2 = [1.308,-0.885]$$

$$\text{softmax} = [0.900,\ 0.100]$$

$$\text{Attn} = 0.900[1,0]+0.100[0,1] = [0.900,\,0.100]$$

**Attention's own residual** (a separate skip connection from the ResBlock's -- the standard $$x+\text{Attention}(x)$$ pattern wrapping the attention sub-layer):

$$h = r + \text{Attn} = [1.849+0.900,\ -1.252+0.100] = [2.749,\,-1.152]$$

**Output head** (toy: $$W_{out}=0.5I$$):

$$\hat\epsilon = W_{out}\,h = 0.5\,[2.749,-1.152] = [1.375,\,-0.576]$$

**Loss:**

$$\|\epsilon-\hat\epsilon\|^2 = (0.5-1.375)^2+(-0.2+0.576)^2$$

$$= 0.766+0.141 = 0.906$$

This scalar drives backpropagation through every weight touched along the way -- both Convs, both GroupNorms' implicit statistics, the FiLM layer's $$W_\gamma,W_b$$, $$W_Q,W_K,W_V$$, and $$W_{out}$$. The loss is larger here than in a version that skipped the ResBlock's internal structure, which makes sense: this pipeline now genuinely passes through two residual connections and two rounds of normalization rather than one shortcut projection, so an untrained set of toy weights has more opportunity to push the prediction away from the true noise. Everything here is still deliberately trivialized (identity convolutions, one attention head, dimension 2, hand-picked temb), but the sequence of operations -- GroupNorm, SiLU, convolution, FiLM modulation, a second GroupNorm/SiLU/convolution, the ResBlock's own skip connection, cross-attention, attention's own residual, noise prediction, mean-squared error -- now mirrors Section 4.4's actual ResBlock structure end to end, rather than compressing past it.

### 4.7 Steering the result harder: classifier-free guidance

During training, the caption is occasionally dropped (replaced with an empty condition) so the network learns both $$\epsilon_\theta(x_t,t)$$ and $$\epsilon_\theta(x_t,t,C)$$ [18]. At generation time, both predictions are computed and combined:

$$\epsilon_{\text{guided}} = \epsilon_{\text{uncond}} + w\,(\epsilon_{\text{cond}} - \epsilon_{\text{uncond}})$$

$$\epsilon_{\text{cond}}-\epsilon_{\text{uncond}}$$ approximates "what the prompt wants to change"; the guidance scale $$w>1$$ amplifies that direction, which is why turning guidance up produces images that hew more strongly -- sometimes too strongly -- to the prompt.

<div class="info-box" markdown="1">
**About the weight factor $$w$$**

- It is a **hyperparameter** that can be tuned manually.
- **What tuning it trades off.** Larger $$w$$ pushes samples to hew more closely to the prompt, since it's extrapolating further in the direction $$\epsilon_{\text{cond}}-\epsilon_{\text{uncond}}$$ ("what the prompt wants to change", as stated above). But push it too far and that extrapolation overshoots -- samples tend to get oversaturated, lose diversity, or pick up artifacts, since the model is being driven outside the range of predictions it ever actually saw during training. So in practice $$w$$ is tuned empirically per model, and often per prompt, balancing prompt-fidelity against sample quality rather than having one universally correct value.
- **$$w<1$$ and $$w=0$$ are meaningful too**, not just large values: $$w=0$$ recovers pure unconditional generation (ignores the caption entirely), and $$0<w<1$$ produces something weaker than the plain conditional model's prediction.
</div>

## Notation reference

| Symbol | Meaning |
|---|---|
| $$x_0$$ | clean data (image, or its VAE latent for latent diffusion) |
| $$x_t$$ | data after $$t$$ steps of noise |
| $$\epsilon$$ | the actual Gaussian noise sampled during training (the target) |
| $$\epsilon_\theta(x_t,t)$$, $$\hat\epsilon$$ | the network's noise prediction |
| $$\beta_t$$ | per-step noise variance, from the fixed schedule |
| $$\alpha_t = 1-\beta_t$$ | single-step signal retention fraction |
| $$\bar\alpha_t=\prod_{s\le t}\alpha_s$$ | cumulative signal retention through step $$t$$ |
| $$C$$ | caption/text embeddings (output of a frozen text encoder) |
| temb | learned embedding of $$t$$, used for FiLM modulation |
| $$\gamma, b$$ (FiLM) | per-channel scale/shift derived from temb |
| $$s_t(x)=\nabla_x\log p_t(x)$$ | the score function |
| $$\sigma_t$$, $$z$$ | reverse-step noise scale, and the fresh Gaussian sample it multiplies |
| $$w$$ | classifier-free guidance scale |

<br>

References
===

[1]. Rombach, R., Blattmann, A., Lorenz, D., Esser, P., & Ommer, B. (2022). *High-Resolution Image Synthesis with Latent Diffusion Models*. (The architecture behind Stable Diffusion.)

[2]. Kingma, D. P., & Welling, M. (2013). *Auto-Encoding Variational Bayes*. (Introduces the variational autoencoder.)

[3]. Ho, J., Jain, A., & Abbeel, P. (2020). *Denoising Diffusion Probabilistic Models*.

[4]. Nichol, A., Dhariwal, P., Ramesh, A., et al. (2021). *GLIDE: Towards Photorealistic Image Generation and Editing with Text-Guided Diffusion Models*.

[5]. Saharia, C., Chan, W., Saxena, S., et al. (2022). *Photorealistic Text-to-Image Diffusion Models with Deep Language Understanding*. (Imagen.)

[6]. Song, Y., Sohl-Dickstein, J., Kingma, D. P., Kumar, A., Ermon, S., & Poole, B. (2021). *Score-Based Generative Modeling through Stochastic Differential Equations*.

[7]. Vincent, P. (2011). *A Connection Between Score Matching and Denoising Autoencoders*. (The denoising score matching identity used in Section 3.)

[8]. Ronneberger, O., Fischer, P., & Brox, T. (2015). *U-Net: Convolutional Networks for Biomedical Image Segmentation*.

[9]. Radford, A., Kim, J. W., Hallacy, C., et al. (2021). *Learning Transferable Visual Models From Natural Language Supervision*. (CLIP.)

[10]. Raffel, C., Shazeer, N., Roberts, A., et al. (2020). *Exploring the Limits of Transfer Learning with a Unified Text-to-Text Transformer*. (T5.)

[11]. Nichol, A., & Dhariwal, P. (2021). *Improved Denoising Diffusion Probabilistic Models*. (Introduces the cosine noise schedule used in Section 4.2.)

[12]. Vaswani, A., Shazeer, N., Parmar, N., et al. (2017). *Attention Is All You Need*. (Introduces the query/key/value attention mechanism and the sinusoidal positional encoding used in Section 4.3.)

[13]. Perez, E., Strub, F., de Vries, H., Dumoulin, V., & Courville, A. (2017). *FiLM: Visual Reasoning with a General Conditioning Layer*.

[14]. He, K., Zhang, X., Ren, S., & Sun, J. (2015). *Deep Residual Learning for Image Recognition*. (Introduces the residual/skip connection used in ResBlocks.)

[15]. Wu, Y., & He, K. (2018). *Group Normalization*.

[16]. Elfwing, S., Uchibe, E., & Doya, K. (2017). *Sigmoid-Weighted Linear Units for Neural Network Function Approximation in Reinforcement Learning*. (SiLU; independently proposed as "Swish" by Ramachandran, P., Zoph, B., & Le, Q. V. (2017), *Searching for Activation Functions*.)

[17]. Nair, V., & Hinton, G. E. (2010). *Rectified Linear Units Improve Restricted Boltzmann Machines*. (ReLU.)

[18]. Ho, J., & Salimans, T. (2022). *Classifier-Free Diffusion Guidance*.

[19] [https://course.fast.ai/Lessons/lesson9.html](https://course.fast.ai/Lessons/lesson9.html)