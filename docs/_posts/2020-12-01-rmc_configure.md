---
layout: post
title: Configure RMCProfile
subtitle: Some tips for configuring RMCProfile on Unix/Linux system
tags: [total scattering, tool, data analysis]
author: Yuanpeng Zhang
comments: true
---

- **Configuration on Linux system**

    - `rmcprofile` on cluster

        <p style='text-align: justify'>
        To run `rmcprofile` on cluster, the following bash script may be helpful,
        </p>

        ```
        #!/bin/bash
        ulimit -s unlimited
        export OMP_NUM_THREADS=12
        RMCProfile_PATH=WHEREVER_RMCProfile_FOLDER_IS_LOCATED
        export PGPLOT_DIR=$RMCProfile_PATH/exe/libs
        export LD_LIBRARY_PATH=$RMCProfile_PATH/exe/libs
        export LIBRARY_PATH=$RMCProfile_PATH/exe/libs
        export PATH=$PATH:$RMCProfile_PATH/exe
        $RMCProfile_PATH/exe/rmcprofile stem_name
        ```

        where `WHEREVER_RMCProfile_FOLDER_IS_LOCATED` refers to the main 
        RMCProfile directory within which the `exe` and `tutorial` 
        sub-directories are usually located. stem_name refers to whatever
        the stem name is for our RMC fitting. Put this script within RMC 
        fitting directory and run it from there should not be a bad idea.

    - Speeding up `rmcprofile`

        No matter running on clusters or a PC, running the following two 
        commands just before kicking off RMC fitting should help speeding
        up the fitting.

        ```
        ulimit -s unlimited
        export OMP_NUM_THREADS=12
        ```

- **Configuration on MacOS**

    - Speeding up `rmcprofile`

        Running the following two commands just before kicking off RMC
        fitting should help speeding up the fitting.

        ```
        ulimit -s unlimited
        export OMP_NUM_THREADS=12
        ```
