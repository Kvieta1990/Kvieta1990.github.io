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

Currently, the source code of `RMCProfile` are hosted on `code.ornl.gov` and with the up-to-date authentication mechanism, not all domains can access the repo, using either SSH or HTTPS for connection. So, if the building machine is outside the ORNL firewall, we may need to grab the source codes inside the firewall first and transfer it to the building machine via, e.g., `rsync`. For example, the machine for building the package for ARM architecture is my own VPS hosted on the Oracle cloud and it is outside the firewall and I have to use `rsync` to tranfer the source codes indirectly. Talking about the building on the ARM machine, once the source codes are transferred, we could use `docker` for building RMCProfile. Here follows is the command we could run,

```bash
sudo docker run -v .:/root/Temp/ -it apw247/rmc_arm64 bash
```

which assumes that the source codes are sitting in the location where this command is to be executed. The command will run the docker image in the interactive mode and inside the running container, we can `cd` into the building directory and run the building script `build_rmcprofile_arm64.sh` to build the package. Before running the command, we may need to create the `distributions` directory in the main directory of the source codes. Then inside the container, we can copy over the compiled package to `/root/Temp/` and it will be available outside the docker container (in the directory where the above command was running).

For building the package compatible with legacy Linux operating systems (some HPC may be still running some of the legacy OS's, surprisingly), the docker route can be used, just like above. Here is the command to run on local host,

```bash
docker container run -v .:/root/Temp/ -it apw247/rmc_dev:zyp_ubuntu1204_rmc bash
```

Depending on the docker setup, we may or may not need `sudo` prepended to the command above. Then everything else concerning the docker procedures pretty much applies here.