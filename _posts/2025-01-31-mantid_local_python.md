---
layout: post
title: Use local build of Mantid with Python script
subtitle:
tags: [Mantid, technical, dev, scattering]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<style>
    .faq-container {
        margin: 0 auto;
    }
    .faq-question {
        margin-bottom: 10px;
        font-weight: bold;
        cursor: pointer;
    }
    .faq-answer {
        display: none;
        margin-bottom: 20px;
    }
    .callout {
        background-color: #e8f4fd; /* Light blue background */
        border-left: 5px solid #007BFF; /* Blue accent on the left */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
        font-family: Arial, sans-serif; /* Ensuring the font is consistent */
    }
    .multiline-span {
        display: block; /* or display: inline-block; */
    }
</style>

Here in the blog, I will covering how to build a local version of `Mantid` and use it with `python`. For example, we may have a `Python` script which uses some `Mantid` algorithm. On ORNL Analysis cluster, a certain version of `Mantid` framework has been deployed and we can execute a `Python` script using the deployed version of `Mantid` via, `mantidpython script_name.py`. But the local build of `Mantid` does not come with something like `mantidpython` wrapper so some extra work needs to be done to get it working.

With the up-to-date version of `Mantid`, the way of setting up the development `conda` environment has been changed to using `mamba` and instructions can be found [here](https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#setup-the-mantid-conda-environment). This new way of setup does not seem to work with the legacy version of `Mantid`, e.g., `6.8.0`. For those legacy versions, we have to set up the `conda` environment in the `legacy` way. To do that, we `cd` into the main directory of the `Mantid` repository from the terminal and run,

```bash
conda env create --file mantid-developer-linux.yml
```

We may need to change the `conda` environment name in the first line of the YAML file above, if needed, e.g., when a `conda` environment with the default name already exists. Suppose the `conda` environment name is `mantid-developer-v6p8p0`, to build `Mantid`, we do,

```bash
conda activate mantid-developer-v6p8p0
cmake --preset=linux
cd build
ninja
```

Next, we want to create a virtual python environment. First, `cd` into a location where we want to create the virtual environment and then execute,

```bash
virtualenv -p <LOCATION>/mantid-developer-v6p8p0/bin/python --system-site-packages .venv
```

where `<LOCATION>` refers to the location where the `conda` environment lives, and in my case, it is `/SNS/users/y8z/miniconda/envs/`. After all the steps, we can execute the following two commands to make `python` in our virtual environment being able to access the local build of `Mantid`,

```bash
source <LOCATION>/.venv/bin/activate > /dev/null 2>&1
python <MANTID_REPO>/build/bin/AddPythonPath.py > /dev/null 2>&1
```

where `<LOCATION>` refers to the location where `.venv` sits and `<MANTID_REPO>` refers to the full path to the `Mantid` repo. The `> /dev/null 2>&1` part of the commands suppress all the terminal printout to keep the output clean.