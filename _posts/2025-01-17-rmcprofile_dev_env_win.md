---
layout: post
title: Development environment setup for RMCProfile on Windows
subtitle:
tags: [RMCProfile, tool, technical, dev, Windows, rmc, scattering]
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

This blog is for noting down the details about setting up the development environment for RMCProfile on Windows machine. The notes here are still based on the conventional way of compiling using `make` files. There is some work happening in the background now to transfer the compiling to using `cmake`. Before the whole pipeline of `cmake` is ready, we will still rely on the route as in the current blog. In fact, even the current route is depracated, some of the notes will still apply when setting up the development environment.

- Nowadays, we are pretty much using the intel Fortran compiler which now is free, through the `oneAPI` toolkit. To install the compiler, we need to first install `Microsoft Visual Studio` (the community version is fine, and it is free). We may need to remove whatever existing old versions of `Microsoft Visual Studio` (e.g., the 2019 version may need to be removed, if the we intend to use the 2022 version). I am not sure whether this is the cause for my issue, but in my case, I have been struggling a lot with the `oneAPI` setup in the down stream. Anyhow, having a clean version of 2022 turns out to be working fine for me.

- Then we want to install the `OneAPI` toolkit.

   > Here is the official website for download and instructions (as of writing), [oneAPI Base Toolkit download and install](https://www.intel.com/content/www/us/en/developer/tools/oneapi/base-toolkit-download.html?operatingsystem=windows)

   > See the `Run Sample Code to Verify Installation` section on the website for the command to run for terminal (e.g., `powershell`) initialization.

- To use `make` and also for the purpose of back-compatibility for some plotting routines, we need to install `Cygwin` and set up the `pgplot` library inside the `Cygwin` environment (see the section at the very bottom for details about setting up `pgplot` in `Cygwin`). When installing `Cygwin`, the following selections are necessary: `make`, `x11` and `xorg`. Later on when installing the `pgplot` library, if some of the `X11` stuff are missing, we can Google what we need to include for `X11` installation according to the error message we got.

- We will need to install `nvcc` for compiling CUDA codes involved in the `RMCProfile` project. As of writing, the offial website for download the installation file is [this](https://developer.nvidia.com/cuda-downloads). In case it is changed, we can just google 'CUDA nvcc install' or something relevant to search for information.

- In preparation for the compiling, we may need to delete unnecessary entries in the `PATH` environment variable. Otherwise, the `nvcc` command will fail when it is trying to set up the environment on-the-fly. It is something relevant to the running of `vcvars64.bat` script which will be running on-the-fly when running `nvcc`, and the fundamental reason for the potential failure is the value of the environment variable `PATH` is too long. On Windows, we can use `Start` to search for 'environment variable' to bring up the GUI window for changing the `PATH` variable.

- On `powershell`, we need to run

   ```powershell
   cmd.exe "/K" '"C:\Program Files (x86)\Intel\oneAPI\setvars.bat" && powershell'
   ```

    to initialize the terminal environment.

   > This command needs to be run each time when launching a new `powershell` terminal.

   > When we see error like this,

   ```
   LINK : fatal error LNK1104: cannot open file 'ifconsol.lib'
   ```

   it infers the `oneAPI` environment initializer was not run, in which case we just need to run the command above to initialize the environment and try again the compiling.

- As a side note, with the latest intel `oneAPI` compiler (`ifort`), the argument that goes into `c_loc` (`the function used in the source codes for fetching the address of a pointer`) should be defined as a pointer. The original definition of some of the variables (used as the argument for `c_loc`) as allocatable arrays would not work.

- When encountering the issue with unresolved CUDA symbols, we may need to change the cuda library files to the version that is compatible with the version of `nvcc` being used for the compiling. Specifically, two CUDA library files `cublas.lib` and `cudart.lib` need to be linked while making the `rmcprofile.exe` executable. For specific command for linking, etc., refer to those `Makefile` files in the `RMCProfile` repository -- for the moment, the source code is not open source yet. To get access, get in touch with me <a href="https://iris2020.net/contact/" target="_blank">here</a>. We need to copy the proper version of these two files to somewhere (and the path to the two library files will be specified while linking to make `rmcprofile.exe`).

   > In my case, I was using the `12.6` version of `nvcc` and my library files could be found here, `C:\Program Files\NVIDIA GPU Computing Toolkit\CUDA\v12.6\lib\x64`.

<p style="text-align: center;">=================I AM A SEPARATOR=================</p>

<br />

Here follows are the detaild instructions about setting up `pgplot` in `Cygwin`.

- Grab the `pgplot5.2.tar.gz` file which has already been included in the main directory of the `RMCProfile` repository.

- Decompress and extract the contents of the distribution file in a source directory. In this sense, I always place `pgplot5.2.tar.gz` under /usr/local/src. Then we can do,

    ```
    cd /usr/local/src
    mv [SOMEWHERE]/pgplot5.2.tar.gz .
    tar zxvf pgplot5.2.tar.gz
    ```

    This will create `/usr/local/src/pgplot` and subdirectories.

- Create the directory where `PGPLOT` will be actually installed,

    ```
    mkdir /usr/local/pgplot
    cd /usr/local/pgplot
    ```

- Copy the file `drivers.list` from the source directory to the installation directory,

    ```
    cp /usr/local/src/pgplot/drivers.list .
    ```

- Edit that file and remove the exclamation mark (first column of each row) in front of the following graphic devices,

    ```
    /PS, /VPS, /CPS, /VCPS,  /Xserve and /XWindow
    ```

- Create the makefile. From the installation directory `/usr/local/pgplot`, execute,

    ```
    /usr/local/src/pgplot/makemake /usr/local/src/pgplot linux g77_gcc_aout
    ```

- Edit the file makefile and change the line,

    ```
    FCOMPL=g77
    ```

    to

    ```
    FCOMPL=gfortran
    ```

- Compile the source files,

    ```
    make
    make cpg
    make clean
    ```

<p style="text-align: center;">=================I AM A SEPARATOR=================</p>

Here are some random notes on compiling with `cmake`.

- In the main `CMakeLists.txt` file, there is one line `project(RMCProfile LANGUAGES CXX C Fortran)` specifying what languages are included in the project. `cmake` will perform the necessary configuration checking and testing at the configuration stage for all the included languages. With `CUDA`, I found that on my Windows machine, including `CUDA` in the language list would never pass the initial configuration stage. However, leaving out `CUDA` from the list, thus skipping the `CUDA` checking and testing, is still OK -- the compiling could still be finished without problems with `CUDA` codes compiled successfully. Not sure why, but suspecting whether it is the following line that puts down `CUDA` specification explicitly actually did the magic,

    ```cmake
    find_package(CUDA 11.4 REQUIRED)

    set(CMAKE_CUDA_STANDARD 11)
    set(CMAKE_CUDA_STANDARD_REQUIRED ON)

    find_package(CUDAToolkit REQUIRED)

    if(CUDAToolkit_VERSION_MAJOR GREATER_EQUAL 12)
        set(CMAKE_CUDA_ARCHITECTURES "61;70;75;80;86;89")
    else()
        set(CMAKE_CUDA_ARCHITECTURES "61;70;75;80;86")
    endif()

    message(STATUS "Using Nvidia GPU architectures ${CMAKE_CUDA_ARCHITECTURES}")

    if(CMAKE_BUILD_TYPE STREQUAL "Debug")
        set(CMAKE_CUDA_FLAGS ${CMAKE_CUDA_FLAGS} "-g -G")  # enable cuda-gdb
    endif()
    ```