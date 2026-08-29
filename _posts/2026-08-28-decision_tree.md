---
layout: post
title: Decision Tree
subtitle:
tags: [machine learning]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/decision_tree_abs.png"
   style="border:none;"
   width="700"
   alt="decision_tree"
   title="decision_tree" /><br>
<em>Image generated with Gemini with the prompt 'generate an abstract image for demo of decision tree pictorially. use the Ghibli style'.</em></p>

<br>

## Introduction

A decision tree is composed of various nodes branching from the top to the bottom, forming a tree-like structure. Each node is a preset criterion based on which objects belonging to the current node can be splitted into two sub-groups. Each of the two sub-groups form a new node and is considered as branching out from the parent node. Such splitting will continue until reaching the terminal node, called the *leaf*. The data going into the decision tree will be checked against those splitting criteria for nodes in the tree, and they may come with multiple dimensions. The value that the tree is trying to predict may also come with multiple dimensions as well, but for brevity, we can stay with a single dimension for the prediction here in the post. To make it specific, we can stay with the example used in Ref. [1]. In fact, explanation and codes provided in Ref. [1] are super nice and clear and there is no need to reproduce any of the details discussed in it. Here in my post, I am going to,

- note down my understanding for the overall architecture of the decision tree training, trying to present it in an abstract diagram and giving us a bird view of the overall picture about what is happening

- put down some side notes about the cost complexity pruning

- include some further notes about the random forest and XGBoost

Back to the specific example provided in Ref. [1], we have a dataset to be used to predict the number of golfers visiting, depending on a series of conditions including the weather, temperature, humidity and wind. Here in the example, `weather, temperature, humidity and wind` is just the multi-dimensional input data that will go into the decision tree, and a specific dimension of a given data point (which carries all those multi-dimensions of information) will be checked against the preset (trained, will talk about how the training will be performed soon) criteria for those nodes in the tree, from the top all the way down until landing on a specific leaf node. Once such a multi-dimensional data lands on a leaf node, the outcome value that we try to predict (e.g., the number of golfers visiting in the example) associated with the same data point is considered to belong to the leaf node. During training, all the outcome values belonging to a leaf node will be collected and be used for computing the prediction corresponding to the leaf node. We will come back to this. Now, we should check out how we do the training.

<br>

> Right click on the image and select `Open image in a new tab` (or something similar) to view a larger version of the image.

<p align='center'>
<img src="/assets/img/posts/Decision_Tree.png"
   style="border:none;"
   width="1000"
   alt="Decision_Tree"
   title="Decision_Tree" />
</p>

For the training, we are always asking such a question -- 'If I split all the data points on my hand according to a certain criterion, how well will the selected criterion split my data points?' The criterion is straightforward to understand -- taking the example above, it will be something like 'temperature > 100 °F?'. Once we have the criterion, data points will be splitted into two groups depending purely on the temperature dimension. Then the question is how to justify how well we are performing in the data splitting. As one of the alternative ways, we could compute the mean square error (MSE). To see why, we can imagine a perfect splitting. For example, let's just assume the criterion being 'temperature > 100 °F?', and imagine that all data points are perfectly splitted into two groups. The first group with temperature > 100 °F has all data points having 0 golfers visiting, and the second group with temperature <= 100 °F has all data points having 100 golfers visiting (in practice, for sure we would have some variation but let's assume no variation here for the demo purpose). In this case, we know that the criterion 'temperature > 100 °F?' is a perfect one since it successfully splits data points into two pure groups -- that is exactly what we need a perfect criterion to do. So, we know that a good splitting criterion should be the one that yields better purity of the data splitting. MSE is one of the good characteristic number that can tell us the purity of a group of data points -- smaller MSE infers better purity (since more values tend to be similar and 0 for all values being the same). It is just based on such a principle that we can construct the decision tree. Details of the training process can be found in Ref. [1] and here I am summarizing the flow into the diagram shown above.

In the figure,

- $$d$$ represents the dimension of the input data -- in the example above, `weather, temperature, humidity and wind` gives us $$d = 4$$

- '$$(1)$$' in the superscript of $$X_1^{(1)}$$, etc. refers to the index of data points.

- The green dashed line represents the candidate splitting points. What we do is to put all the values of a certain dimension (e.g., temperature) from all data points together, sort them and make a list of only unique values. Then we have a list of all mid-point values for every pair of adjacent values in the unique and sorted list -- these will be our candidate splitting points regarding the specific dimension. For each candidate splitting point, we can split the data and compute the MSE -- in the figure, we have in total $$k$$ candidate splitting points for the single dimension of data. Let's call the two splitted groups of data points `left` and `right`, and,

   $$
   \begin{align}
   \text{MSE}_{\text{left}} & = \frac{\sum_{n = 1}^{M_{\text{left}}}(Y_{\text{left}}^{(n)} - \bar{Y}_{\text{left}})^2}{M_{\text{left}}}\\
   \text{MSE}_{\text{right}} & = \frac{\sum_{n = 1}^{M_{\text{right}}}(Y_{\text{right}}^{(n)} - \bar{Y}_{\text{right}})^2}{M_{\text{right}}}\\
   \text{MSE}_{\text{Ave}} & = \frac{M_{\text{left}} \times \text{MSE}_{\text{left}} + M_{\text{right}} \times \text{MSE}_{\text{right}}}{M_{\text{left}} + M_{\text{right}}}
   \end{align}
   $$

- As indicated in the figure, the process in the boxed diagram will be applied to all the $$d$$ dimensions. For each of the dimensions, we have a certain number of candidate splitting points and the correspondingly obtained MSE values. Putting all the MSE values from all dimensions together, we can pick up the one that gives the smallest MSE, and that will be our splitting criterion for the current node. Once we have the node criterion, we can split the data points into two groups, and for each of the two groups, we can repeat the process of splitting so that we can have further branching nodes, and so on, until reaching the leaf node.

- $$M_{\mathcal{N}}$$ in the figure refers to the total number of data points that belong the a certain node $$\mathcal{N}$$ in the decision tree.

From the training process, we can see that not like other supervised training, the 'loss function' here is actually not obtained by comparing to the ground truth value. This is fine -- as we already discussed, using MSE (or other quantities like the Gini value [2]) does make sense to train the tree. However, the question is how we are going to make predictions with the trained decision tree if it is not directly giving a predicted value -- the only thing we know is values of the quantities (that we want to predict) landing on those leaf nodes. Depending on what we are trying to do with the decision tree, the value corresponding to the leaf nodes can be computed accordingly. For example, if we want classification (e.g., raining or not), we can just count the number of 'raining' values (e.g., $$1$$) and 'not raining' values (e.g., $$0$$) on a certain leaf node and the countings just give us the idea of probability of predicted 'raining' or 'not raining'. For non-classification problems, like the example presented above, we could just add all the values up for a certain leaf node and that value will be our prediction for the leaf node.

## Cost complex pruning

To avoid overfitting, we may need to prune some leaf nodes. The pruning can be done at two stages -- pre-pruning which happens during the training by controlling the tree depth, etc. (see Ref. [1] for de6tails) and post-pruning which 'closes out' sub-nodes depending on the so-called cost complex pruning principle. Again, here I am not going to reproduce the details in Ref. [1] but instead just note down some of my understandings.

- For all the nodes in the trained tree, we can calculate its corresponding impurity, indicated by, e.g., the MSE. For the leaf node, it is straightforward -- we just calculate the MSE for all the data points landing on the leaf node. For non-leaf nodes, we need to put together all data points that are branching out (downwards) from the node and compute the MSE.

- For those nodes with already small MSE values, we may consider prune them since the data landing on the node are already pure even without further splitting. Otherwise, we are potentially overfitting.

- If we prune too many nodes, the decision tree may not perform well in prediction since the model is too 'coarse'. Therefore, in practice, there will be some parameter controlling the balance between overfitting and coarse modeling -- the $$\alpha$$ parameter mentioned in Ref. [1].

## Random forest

For better stability and accuracy of the decision tree model, one of the popular ways is to have a random forest containing many decision trees. The predictions from those decision trees included in the random forest will be combined to yield the final prediction. Data and feature randomization strategies can be used fot the creation of the random forest. For data randomization, we drop off a portion of the training data randomly to train multiple decision trees. For feature randomization, we drop off some of the dimensions randomly to train multiple decision trees. The algorithm is relatively easy to understand and therefore details will not be covered in the post here.

## XGBoost

The XGBoost (Extreme Gradient Boosting) provides another way of having multiple decision trees to make the model more robust. The official documentation page for XGBoost [4] provides a nice explanation about the theory. I enjoyed reading the documentation to build up the understanding from head to tail without problems. Here what I want to do is to reverse the story flow -- starting with the final formulation to follow for the training, I then trace back to put down what recipes we need to build up the formulation. The purpose is only for deepening my understanding of the algorithm -- for initial  learning, for sure we should follow the documentation in Ref. [4] and once we do that, we can go from tail to head, for better understanding.

For XGBoost, the decision tree training is performed step by step -- at each step, we worry about the construction of a single decision tree. At the $$t$$-th step, the full version of the objective function we want to optimize is,

$$
\text{obj}^{(t)} = \sum_{j = 1}^T[G_j\omega_j + \frac{1}{2}(H_j + \lambda)\omega_j^2] + \gamma T
$$

- Let's just accept the formula here and we will peel the onion layer by layer.

- The index $$j$$ refers to the index of `leaf` nodes in the decision tree and $$T$$ is the overall number of `leaf` nodes in the tree. At this point, we see what we are trying to do -- with the overall objective function presented above, we are trying to find the optimal decision tree at step $$t$$, to construct the $$t$$-th tree.

- $$\omega_j$$ is the weight value correponding to the leaf node $$j$$. With XGBoost, the predicted value for a leaf node is a weight, e.g., $$\omega_j$$, and the final outcome of prediction will be the sum of all weights across all the decision tree. For example, a certain data point lands on leaf nodes with weights $$\omega_0, \omega_1, \cdots, \omega_t$$, respectively for each of the decision tree at different steps, and the overall prediction outcome will be $$\omega_0 + \omega_1 + \cdots + \omega_t$$.

- $$G_j = \sum_{i\in I_j}g_i$$, where $$I_j = \{i \vert q(x_i) = j\}$$ is the set of indices of data points assigned to the $$j$$-th leaf node. $$q$$ here means `mapping`, or in the language of current post, `landing`. Further,

   $$
   g_i = \partial_{\hat{y}_i^{(t - 1)}}l\bigg[ y_i, \hat{y}_i^{(t - 1)} \bigg]
   $$

   which means the partial derivative of the loss function over the observation variable, evaluated at the value of the observation variable $$\hat{y}_i^{(t - 1)}$$. $$y_i$$ represents the value of $$i$$-th data points. Here, same as the process of decision tree training discussed above, the value of $$y_i$$ corresponds to one of the dimensions -- the one that we are currently testing. In fact, the diagram shown in the decision tree section applies here as well. More on this later.

   Here, we notice that the evaluation of $$g_i$$ requires knowing $$\hat{y}_i^{(t - 1)}$$ from leaf node in the decision tree of previous step. Such a requirement then happens recursively, until reaching $$\hat{y}_i^{(0)}$$. Going forward, if we start from a known value of $$\hat{y}_i^{(0)}$$, indeed we can go all the way to $$\hat{y}_i^{(t - 1)}$$ to give $$g_i$$ at $$t$$-th step without problems.

- The same logic above for $$G_j$$ applies to $$H_j$$ as well, and,

   $$
   \begin{align}
   H_j & = \sum_{i\in I_j}h_i\\
   h_i & = \partial_{\hat{y}_i^{(t - 1)}}^2l\bigg[ y_i, \hat{y}_i^{(t - 1)} \bigg]
   \end{align}
   $$

- $$\gamma$$ is the minimum loss reduction hyperparameter -- later we will see why.

- $$\lambda$$ is the L2 regularization hyperparameter, trying to prevent a single leaf from having an outsized impact baesd on a small number of noisy data points.

Since the objective function presented above is actually a quadratic function of $$\omega_j$$, the optimal objective we can get is,

$$
\begin{align}
\omega_j^* & = -\frac{G_j}{H_j + \lambda}\\
\text{obj}^* & = -\frac{1}{2}\sum_{j = 1}^T\frac{G_j^2}{H_j + \lambda} + \gamma T
\end{align}
$$

As mentioned above, the objective function here is for the overall tree in the $$t$$-th step. So, does this mean that we need to exhaustively go over all the possible tree structures, evaluate the objective function for each of them and select the one with the minimal objective function value? The answer is no as such a process is intractable. Instead, we can just follow the same way as we would for a standard decision tree -- we construct the node step by step, from the top to the bottom. In the standard decision tree training, the diagram presented previously tells us that we need to come up with various splitting scenarios for each and every dimension of data points, evaluate the MSE value for each individual trial, and finally we decide which trial to pick depending on the MSE value. Here we should follow exactly the same route -- it is just we will replace the MSE to evaluate with something else. Again, for each node splitting, we call the two sub-groups `left` (L) and `right` (R). The thing we want to evaluate is,

$$
\text{Gain} = \frac{1}{2}\bigg[ \frac{G_L^2}{H_L + \lambda} + \frac{G_R^2}{H_R + \lambda} - \frac{(G_L + G_R)^2}{H_L + H_R + \lambda}\bigg]
$$

Why is it like this? The idea is, we should be checking the change of the objective function before and after the proposed splitting, and we want to see how signicantly the proposed splitting reduces the objective function. Before the splitting,

$$
\text{obj}_{\text{before}}^* = -\frac{1}{2}\sum_{j \neq j_T}^Y \frac{G_j^2}{H_j + \lambda} + \gamma(T - 1) + \bigg[-\frac{1}{2}\frac{G_{j_T}^2}{H_{j_T} + \lambda} + \gamma\bigg]
$$

where $$j_T$$ is the node we want to split and here we simply split the summation into a term that does not involve the node to be splitted (left) and the term for the node to be splitted (right). After the splitting,

$$
\begin{align}
\text{obj}_{\text{after}}^* & = -\frac{1}{2}\sum_{j \neq j_T}^T \frac{G_j^2}{H_j + \lambda} + \gamma(T - 1)\\
& \hspace{1cm} + \bigg[-\frac{1}{2}\frac{G_L^2}{H_L + \lambda} + \gamma\bigg]\\
& \hspace{1cm} + \bigg[-\frac{1}{2}\frac{G_R^2}{H_R + \lambda} + \gamma\bigg]
\end{align}
$$

Therefore, the reduction of the objective function (i.e., the `Gain`) is,

$$
\begin{align}
\text{Gain} & = \text{obj}_{\text{before}}^* - \text{obj}_{\text{after}}^*\\
& = \frac{1}{2}\bigg[ \frac{G_L^2}{H_L + \lambda} + \frac{G_R^2}{H_R + \lambda} - \frac{G_{j_T}^2}{H_{j_T} + \lambda} \bigg] - \gamma\\
& = \frac{1}{2}\bigg[ \frac{G_L^2}{H_L + \lambda} + \frac{G_R^2}{H_R + \lambda} - \frac{(G_L + G_R)^2}{H_L + H_R + \lambda} \bigg] - \gamma
\end{align}
$$

As given above, $$G_j = \sum_{i\in I_j}g_i, H_j = \sum_{i\in I_j}h_i$$, so,

$$
\begin{align}
G_{j_T} & = \sum_{i\in I_{j_T}}g_i = \sum_{i\in I_L}g_i + \sum_{i\in I_R}g_i = G_L + G_R\\
H_{j_T} & = \sum_{i\in I_{j_T}}h_i = \sum_{i\in I_L}h_i + \sum_{i\in I_R}h_i = H_L + H_R
\end{align}
$$

where $$I_{j_T} = I_L \cup I_R$$ and $$I_L \cap I_R = \emptyset$$ (i.e., there is no overlap between the left and right sub-groups after splitting).

## Useful resources

- A Jupyter notebook contaiing two examples of decision tree training and prediction can be found [here](../assets/files/decision_tree.ipynb) [1, 3].

<br>

References
===

[1] [Decision Tree Regressor, Explained: A Visual Guide with Code Examples](https://towardsdatascience.com/decision-tree-regressor-explained-a-visual-guide-with-code-examples-fbd2836c3bef/).

[2] [Understanding the Gini Index and Information Gain in Decision Trees](https://medium.com/analytics-steps/understanding-the-gini-index-and-information-gain-in-decision-trees-ab4720518ba8).

[3] [Implementing Decision Tree Regression using Scikit-Learn](https://www.geeksforgeeks.org/machine-learning/python-decision-tree-regression-using-sklearn/).

[4] [Introduction to Boosted Trees](https://xgboost.readthedocs.io/en/stable/tutorials/model.html).