---
layout: post
title: Jupyterlab kernel setup
subtitle: Conda environment to be used as Jupyterlab kernel across users
tags: [programming, technical, web development]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/jupyter.png"
   style="border:none;"
   alt="jupyter"
   title="jupyter" />
</p>

<br />

<p style='text-align: justify'>
When trying to set up our own Jupyterlab server where general users can visit and use, the link in Ref. [1] could be followed. Once we follow literally the step-1 specified in the link, the server should be running without problem and we can just start using our own online Jupyterlab service. However, since the Jupyterlab service we just set up is a system-wise available service and therefore when we want to add kernels to the Jupyterlab service, we cannot use those kernels belonging to a specific user. Instead, we should set up system-wise available environment which then can be used as system-wise available kernels. The step-2 in Ref. [1] provides us with a generic way to set up the system-wise available environment using <i>conda</i>. Definitely we can follow the instructions there to set up <i>conda</i> in such way that conda itself is also system-wise available. As an alternative, we can be a bit flexible - we can use general users' local <i>conda</i> environment to do this as well, as long as the general user has the <i>sudo</i> privilege. For example, the following commands could be executed to set up the system-wise available Python environment,
</p>

<blockquote cite="">
sudo ~/miniconda3/condabin/conda  create --prefix /opt/conda/envs/py37 python=3.7
sudo /opt/conda/envs/py37/bin/python -m pip install ipykernel
sudo /opt/conda/envs/py37/bin/python -m ipykernel install --name py37 --display-name "Python (py37)" --prefix=/usr/local/
</blockquote>

<p style='text-align: justify'>
In this way, the py37 environment could also be accessed across the system by all logged in users.
</p>

<br />

<b>References</b>

[1] [https://jupyterhub.readthedocs.io/en/1.2.0/installation-guide-hard.html](https://jupyterhub.readthedocs.io/en/1.2.0/installation-guide-hard.html)