---
layout: post
title: About Gradient Update for the Cross Entropy Loss
subtitle:
tags: [machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/robot_loss_function_intuition.png"
   style="border:none;"
   width="700"
   alt="robot"
   title="robot" /><br>
<em>Image generated with Gemini with the prompt 'robot with intuition for loss function' (here I am using exactly the same prompt as presented in a reference post about cross entropy loss. [1]).</em></p>

<br>

Cross entropy loss is a commonly used loss function for classification problems with neural network. A nice and detailed introduction can be found in Ref. [1] and I won't reproduce all the details here. Instead, I will try to focus on how the cross entropy loss function helps with the gradient vanishing problem for weights update.

First, let's see how the loss function is defined for the multi-class claffication problem and how the weights update works. Say we have $$C$$ classes (e.g., dog, cat, bear, etc.) as the potential output of the network, and we can assign the ground truth probability to a specific training data (e.g., an image) -- for the class of the true label, we have the probability of $$1$$ and $$0$$ for all the other classes. For example, if we have an image of dog, the ground truth probability vector would be $$[y_{dog}, y_{cat}, y_{bear}] = [1, 0, 0]$$ for this specific image. Then if our network predicts the probability of each of the classes to be, $$[p_{dog}, p_{cat}, p_{bear}] = [0.8, 0.1, 0.1]$$, we want to have a way to characterize how the predicted probability is far off from the ground truth. Cross entropy loss is one of the alternative loss functions but here let's start with a more straightforward loss function, the Mean Square Error (MSE) loss, which is defined as,

$$
\mathcal{L}_{MSE} = \frac{1}{2}\sum_{k=1}^C(p_k - y_k)^2
$$

where we use the symbol $$k$$ to represent those different classes, like dog, cat, bear, etc. This is just the definition of a loss function and the meaning is very straightforward to interpret -- a good model should be the one that minimizes the difference between the prediction and the ground truth. Therefore, when translated into the language of the loss function, that is saying we want to minimize our *loss*.

Knowing our target, namely to reduce the loss, we want to see how we are going to do it. Fundamentally, this is through the gradient calculation. For example, if we have a function $$y = f(x)$$, the gradient $$\frac{dy}{dx} = f^{\prime}(x)$$ tells us how the change in $$x$$ will have impact on the change of $$y$$. So, to reduce our loss, we just need to calculate the gradient of our loss function to get the derivative of the loss function over our parameters (i.e., the weights) -- changing the weights to aim at lower loss is basically what we mean by 'training a network'. The question now is how to calculate the derivative of $$\mathcal{L}$$ over the weights, i.e., $$\frac{\partial \mathcal{L}}{\partial w_i}$$. Tracing the loss function all the way back to our weights to be updated,

$$
\begin{align}
z & = w \cdot x + b\\
p_k & = \frac{1}{1 + e^{-z_k}}\\
\mathcal{L}_{MSE} & = \frac{1}{2}\sum_{k=1}^C(p_k - y_k)^2
\end{align}
$$

where $$z = [z_1, z_2, \dots, z_k, \dots]$$ is a vector containing all the raw output (called `logits`) of the network. $$w$$ is a matrix. $$x$$ and $$b$$ are both vectors -- $$x$$ refers to the input of the current layer and $$b$$ refers to the bias vector. Then we know how to calculate the derivative -- just to follow the chain rule of the derivative calculation, as,

$$
\frac{\partial \mathcal{L}}{\partial w_i^k} = \frac{\partial \mathcal{L}}{\partial p_k}\frac{\partial p_k}{\partial z_k}\frac{\partial z_k}{\partial w_i^k}
$$

Here, we write down a specific weight component $$w_i$$ explicitly -- for a specific $$z_k$$, we have,

$$
z_k = w_1^k * x_1 + w_2^k * x_2 + \cdots + w_i^k * x_i + \cdots + b^k
$$

where the $$k$$ in $$w_i^k$$ refers to the $$k$$th class but NOT the exponent. Given the MSE loss function presented above, the derivative can be calculated to be,

$$
\frac{\partial\mathcal{L}}{\partial w_i^k} = (p_k - y_k)\cdot p_k(1 - p_k) \cdot x_i
$$

Having the derivative, we know how to update the weights so that we can reduce our loss,

$$
(w_i^k)_{new} = (w_i^k)_{old} - \alpha \frac{\partial\mathcal{L}}{\partial w_i^k}
$$

<br>

> The '-' in the formulation means that we want to follow the opposite direction as indicated by the gradient to change the weights. The reason is obvious -- if we follow the same direction as what the gradient infers, the updated weight will lead to the increase of the loss function, since that is what exactly the gradient is supposed to do. Following the opposite direction against the gradient, we get what we need -- by updating the weights according to the way described in the formulation, we will be reducing the loss.

> The gradient tells us the direction of the weight updating, i.e., increase or decrease, in order to reduce the loss. However, it does not tell us by how much should we change the weight. In fact, the magnitude of the weight update can be an arbitrary parameter and that is why we have the hyperparameter $$\alpha$$ in the formulation which in practice is called the `learning rate`.

Having laied the foundation for the discussion by giving the loss function and how we are supposed to update weights, we now get back to the cross entropy loss topic. To see why we may need it, we first see what problems the MSE loss function could potentially give us. Suppose at the early stage of the training, we have an image in the training data labeled as a cat, in which case we have $$y = [0, 1, 0]$$ (assuming that we only have 3 classes, dog, cat and bear), and the prediction our networks gives is $$p = [0.99999, 0.00001, 0]$$. This is definitely a terrible prediction since our network thinks the image is a dog with the confidence of 99.999% which is terribly wrong. So, obviously, what we want here is to have our model updated so that the predicted probability for the image being a cat increases dramatically, by updating our weights according to the gradient. This is something we were discussing above. However, here we have a problem when we try to put in the values into the gradient formulation presented above,

$$
\frac{\partial\mathcal{L}}{\partial w_i^2} = (p_2 - y_2)\cdot p_2(1 - p_2) \cdot x_i = (0.00001 - 1)\cdot 0.00001(1 - 0.00001)\cdot x_i \approx -0.00001x_i
$$

We can see that the pre-factor $$0.00001$$ we have in the gradient is very small, meaning that potentialyl our weight is probably not going to be updated by a significant amount, and this is definitely not something we were expecting to happen -- our network would be stuck due to the predicted probability of a certain class being tiny which can indeed happen in practice.

To solve the issue, we now introduce the cross entropy loss function given as,

$$
\mathcal{L}_{CE} = -\sum_{k = 1}^C y_klog(p_k)
$$

Following the chain rule to calculate the derivative, we have,

$$
\begin{align}
\frac{\partial \mathcal{L}}{\partial w_i^k} & = \frac{\partial \mathcal{L}}{\partial p_k}\frac{\partial p_k}{\partial z_k}\frac{\partial z_k}{\partial w_i^k}\\
& = \frac{p_k - y_k}{p_k(1 - p_k)} \cdot p_k(1 - p_k) \cdot x_i\\
& = (p_k - y_k)\cdot x_i
\end{align}
$$

where we the previosuly problematic term $$p_k(1 - p_k)$$ causing the vanishing gradient due to $$p_k$$ being notoriously small, is now cancelled out. Therefore, with the cross entropy loss, we solve the vanishing gradient problem. In fact, there are more benefits of using the cross entropy loss as presented in Ref. [1] and I won't reproduce them here.

<br>

References
===

[1] [A Brief Overview of Cross Entropy Loss](https://medium.com/@chris.p.hughes10/a-brief-overview-of-cross-entropy-loss-523aa56b75d5#id_token=eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwZmUwZTIzYzRkNmUzNmM1MjU3N2IxZTJmZWZkMWFiYzM4ODk1ZGUiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIyMTYyOTYwMzU4MzQtazFrNnFlMDYwczJ0cDJhMmphbTRsamRjbXMwMHN0dGcuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIyMTYyOTYwMzU4MzQtazFrNnFlMDYwczJ0cDJhMmphbTRsamRjbXMwMHN0dGcuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDYxMzU5MTE0MTM5NDk2Mzg0ODAiLCJlbWFpbCI6Inp5cm9jMTk5MEBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibm9uY2UiOiJub3RfcHJvdmlkZWQiLCJuYmYiOjE3ODUzMzMwODcsIm5hbWUiOiJZdWFucGVuZyBaaGFuZyIsInBpY3R1cmUiOiJodHRwczovL2xoMy5nb29nbGV1c2VyY29udGVudC5jb20vYS9BQ2c4b2NJY2dkVXgzdlgtZlRGOFlXcldiSDFyN1dHUFJMQ2tNMTIzdVVFRUZiMjRtd1k1M2RlcVh3PXM5Ni1jIiwiZ2l2ZW5fbmFtZSI6Ill1YW5wZW5nIiwiZmFtaWx5X25hbWUiOiJaaGFuZyIsImlhdCI6MTc4NTMzMzM4NywiZXhwIjoxNzg1MzM2OTg3LCJqdGkiOiJhMmI1YzY3NDg5OTEyYTEzN2I3ZWE3YzU1NTIwZmI1ODAxZjRkZmU1In0.pQhteAL4zEh9VMPUkfiZACUYCvoe0T-pm6mDTfohscwskMrc9D-_eaurhwKgLEx2ggsNx7wjYm3UZkDH6C73fSBjg-cWxPH3Zc76ubCL3mbYQJhMXzUchTO64FWGOamydrMjFJ1pL-VQNvR04XGOdZ2vBtxQem6BOgj4VeCMlJeN4G1Rm4HvpJU0SyFSw0pT_ks15C2rgTKuwjgNB2LZQlLmkCnLlnpnRer1XMrIzrl2jkEJKFhpmhLeBTISax-4ErFrDdAtXekCot_5whpOjy9qJojWVqaliiRcP1fvED2t4JP4Hwr4VU2JnqRllQiH9-PqbhTqEIc2HY9YHTI_Qg)