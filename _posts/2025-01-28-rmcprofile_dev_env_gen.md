---
layout: post
title: Notes on the development environment setup for RMCProfile
subtitle:
tags: [RMCProfile, tool, technical, dev, rmc, scattering]
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

This blog is for noting down the technical details about the `RMCProfile` package building. This is `not` intended to be a tutorial so it won't cover all the nitty-gritties. Instead, only some key aspects will be noted down for future reference.

- The ARM64 package is built on my personal VPS with ARM architecture hosted on Oracle Cloud. The `build_rmcprofile_arm64.sh` script under the `linux-gfortran` can be used for the package building.

- For building the package compatible with legacy Linux operating systems (some HPC may be still running some of the legacy OS's, surprisingly), the docker route can be used. Here is the command to run the docker image in an interactive container,

    ```bash
    docker container run -v .:/root/Temp/ -it apw247/rmc_dev:zyp_ubuntu1204_rmc bash
    ```

    Depending on the docker setup, we may or may not need `sudo` prepended to the command above. The `build_rmcprofile_lower.sh` script can be used for the package building.

- On Windows, with the Intel OneAPI, execute the following command to activate the OneAPI environment before compiling the package,

    ```cmd
    cmd.exe "/K" '"C:\Program Files (x86)\Intel\oneAPI\setvars.bat" && powershell'
    ```

- On MacOS, the `conf_viewer` and `rmc_mon` script is different between the Intel and ARM version. In the App package folder, `NEVER` replace the two scripts included in there.