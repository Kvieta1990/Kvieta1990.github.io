---
layout: post
title: RMCProfile + LAMMPS Implementation
subtitle:
tags: [programming, RMCProfile, LAMMPS]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/rmc_lammps.png"
   style="border:none;"
   width="600"
   alt="rmc_lammps"
   title="rmc_lammps" />
</p>

<br />

This blog is a collection of notes about implementing the `lammps` engine in our RMCProfile package [1] for energy
calculation, using the provided wrapper routines provided with the `lammps` distribution. The note will be covering
the installation of MPI compilers, building the `lammps` wrapper libraries, the implementation in the caller
program, shipping of the main caller program with the shared dynamic libraries, proper execution of the main caller
program, and the hybrid OMP & MPI implementation.

Compiler Setup
===

To build and run MPI program, we have to install a specific MPI
implementation, and `Open MPI` is the one that is often used. We can download the
latest version of Open MPI from here,

[https://www.open-mpi.org/](https://www.open-mpi.org/)

Compiling Open MPI is straightforward and instruction can be found here,

[https://www.open-mpi.org/faq/?category=building](https://www.open-mpi.org/faq/?category=building)

> N.B. The compiler used for compiling Open MPI should be consistent with that
used for compiling our program that will use the MPI libraries. For example, in our
Fortran program, we are going to import the MPI library by using `USE MPI`.
Therefore, we have to specify the compiler we want to use for compiling Open MPI.
Here follows is the example case where we were using
`openmpi-4.1.4`. After unzipping the package to obtain the `openmpi-4.1.4`
directory, we can cd into it and execute `./configure --help` to list out all
the available flags among which we can find how to set a specific compiler for
certain languages, e.g., `FC` for fortran.

> Concerning the specification of fortran compiler, initially `FC=ifort` was
used, which however turned to be not working for some reason. Then it seems that
we have to give the full path to `ifort` compiler to make it work.

> At the end of the day, the following commands were executed in sequence to
compile `openmpi-4.1.4`,

```bash
tar xzvf openmpi-4.1.4.tar.gz
cd openmpi-4.1.4
mkdir build
cd build
../configure FC=/opt/intel/bin/ifort --prefix=/opt/bin/
sudo make all install
```

<br />

> `--prefix=/opt/bin/` is telling the compiler where to install the compiled
executable.

> Alternatively, we can also follow the instruction here below to compile
Open MPI,

[https://www.ibm.com/support/pages/how-do-i-recompile-and-install-openmpi-12-intel-compiler](https://www.ibm.com/support/pages/how-do-i-recompile-and-install-openmpi-12-intel-compiler)

Demo program for LAMMPS interface with Fortran
===

1. Untar the tarball downloaded containing the source codes of LAMMPS and then
go into the obtained directory, e.g., `lammps-stable_29Sep2021_update2`.

   > Here is the source of lammps for this release, [https://github.com/lammps/lammps/releases/tag/stable_29Sep2021_update2](https://github.com/lammps/lammps/releases/tag/stable_29Sep2021_update2)

2. Build LAMMPS shared library,

    > Current note is a clean summary for steps we need to go through for
    building lammps. For detailed instruction, refer to the link below,

    > [https://docs.lammps.org/Build_cmake.html#getting-started](https://docs.lammps.org/Build_cmake.html#getting-started)

    > Location of the built shared library needs to be specified when building
    the final caller program.

    > Also, the shared library needs to be shipped together with the main caller
    program and meanwhile, the location of the shipped shared library needs to
    be exported before running the main caller program (using command
    like `export LD_LIBRARY_PATH=[wherever_the_shared_lib_is_located]`)

    2.1. `mkdir build && cd build`

    2.2. `cmake ../cmake -D BUILD_SHARED_LIBS=yes -DCMAKE_CXX_COMPILER=/opt/bin/mpicxx`

    > Refer to [the link](https://docs.lammps.org/Build_basics.html#build-the-lammps-executable-and-library)
    for more information about building lammps library

    > The `-DCMAKE_CXX_COMPILER` flag is for specifying the C++ compiler to use for
    compiling lammps. If not specifying the compiler explicitly, `cmake` will be able
    to find a usable C++ compiler on the building machine. However, in our case for
    building the lammps wrapper (see section-3.1 below), we specified the C++ compiler
    as `/opt/bin/mpicxx`. Therefore, it should be better to keep consistence in terms
    of the C++ compiler being used in multiple spots. Though, I am not sure whether
    using different version of the C++ compilers will work. But at least, using the same
    version of the C++ compiler turned out to be working without problems.

    2.3. `make -j 24`

    > `24` here means we want to use 24 cores for building lammps in parallel to
    speed up the compilation.

    2.4. Taking the main directory `lammps-stable_29Sep2021_update2` as the
    example, we should be able to find `liblammps.so.0` (together with a soft
    symbol `liblammps.so` linked to `liblammps.so.0`, in the same directory with
    `liblammps.so.0`) in the following directory,

    `/[whatever_location]/lammps-stable_29Sep2021_update2/build`

3. Build the main caller program

    > Here, we will take the `fortran2` example included in the shipped
    source codes of lammps. Again, taking the [stable_29Sep2021_update2](https://github.com/lammps/lammps/releases/tag/stable_29Sep2021_update2)
    as the example, the `fortran2` directory can be found here,

    > `/[whatever_location]/lammps-stable_29Sep2021_update2/examples/COUPLE/fortran2`

    > N.B. for the following instructions, we will assume that we are located in
    this directory,

    `/home/y8z/BBird_Ext/Packages/lammps-stable_29Sep2021_update2/examples/COUPLE/fortran2`

    > Codes in this directory has been modified to build a minimal example to be
    used as the template for further development.

    3.1. `make`

    > The `Makefile` is provided with lammps distribution, which basically
    packages up those steps as specified in the `README` file.

    > In our case for compiling the lammps wrapper for RMCProfile package, the `Makefile` being
    used can be found here,

    > [https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_lammps_wrapper](https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_lammps_wrapper)

    > In the Makefile, the C++ compiler was specified as `/opt/bin/mpicxx`, as can be found here,

    > [https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_lammps_wrapper#L11](https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_lammps_wrapper#L11)

    3.2. `/opt/bin/mpifort -c simple.f90`

    3.3. `/opt/bin/mpifort -c serial.f90`

    3.4. `/opt/bin/mpifort -c main.f90`

    3.3. `/opt/bin/mpifort main.o serial.o LAMMPS.o LAMMPS-wrapper.o simple.o -L /home/y8z/BBird_Ext/Temp_Ext/lammps-stable_29Sep2021_update2/build -L . -llammps -llammps_fortran -lmpi_cxx -lstdc++ -lm -o main`

    > `main` is the finally compiled executable.

    > The prefix `/opt/bin/` may be removed if `mpifort` is in our system path.
    However, in my case, for the reason given down below (see step 4), adding
    the path to our environment path variable won't work. The workaround may be
    to create alias to `/opt/bin/mpifort` and all the other commands.

    > In [this link](https://docs.lammps.org/Build_basics.html#build-the-lammps-executable-and-library),
    we can see that the `LAMMPS_MACHINE` flag can be specified. If we do that
    and assume we give it the name of `myname`, the file name of the compiled
    shared library will also be changed accordingly, from the default
    `liblammps.so.0` to `liblammps_myname.so.0` (same thing will happen to the
    soft link as well). In this case, the library link flag in the final
    compile command should be changed from `-llammps` to `-llammps_myname`.

    > Same comments as above applies to the `-llammps_fortran` as well - in the
    included `Makefile`, the compiled shared library is by default given the
    name of `liblammps_fortran.so`. If we change its name in `Makefile`, we need
    to change the flag accordingly.

    > For the flag `-llammps`, there is a pitfall to notice - the actual shared
    library name is `liblammps.so.0` (taking its default name as the example)
    and we have `liblammps.so` as a soft link to `liblammps.so.0`. When
    compiling the final executable (here, `simpleF`), the compiling command is
    actually expecting `liblammps.so`. However, when executing the final
    executable (see step below for an example of execution), it is instead
    expecting `liblammps.so.0`, simple because the `liblammps.so.0` is the file
    containing the actual contents of the library and `liblammps.so` is just a
    soft link to the library file.

4. Execute the main executable as,

    `/opt/bin/mpirun -np 4 ./main`

    > According to the notes in the following link,

    > [https://stackoverflow.com/questions/37110538/open-mpi-scalasca-can-not-run-mpirun-command-with-option-prefix](https://stackoverflow.com/questions/37110538/open-mpi-scalasca-can-not-run-mpirun-command-with-option-prefix)

    > prepending the full path to the `mpirun` executable is equivalent to add
    in the `--prefix` flag to specify the prefix option. It seems that this flag
    is to specify the library location and in principle we can export the path
    of the Open MPI libraries to the `LD_LIBRARY_PATH` variable -- following the example
    above, the command would be,

    ```bash
    export LD_LIBRARY_PATH="/opt/lib:$LD_LIBRARY_PATH"
    ```

    <br />

    > See the `Compiler Setup` section of the current note for the path specified
    to contain the compiled Open MPI libraries.

    > However, given the current configuration on BigBird machine, it seems that
    the currently existing multiple versions of OpenMPI builds in the systems
    messed up the libraries, etc. So, for some reason, exporting the library
    path like above won't work. In such a situation, we have to prepend the
    executable with the full path. A workaround may be to create alias to the
    full path of the command.

    > Unfortunately, the approach of creating alias for the full path won't work
    when submitting jobs via, e.g., slurm, i.e., it seems that for some reason the
    job submission routine won't be able to find the right libraries to use even
    we have already created the alias for the full path of the `mpirun` command.
    In such cases, we have to use the full path of the `mpirun` executable in the
    job submission script.

5. Here following is a list of those libraries that need to be shipped with
RMCProfile package to guarantee they can be found and called at run time.

    - libifcoremt.so.5

    - libifport.so.5

    - libimf.so

    - libintlc.so.5

    - liblammps.so.0

    - liblammps.so -> liblammps.so.0

    - liblammps_fortran.so

    - libmpi.so.40.30.4

    - libmpi.so -> libmpi.so.40.30.4

    - libsvml.so

    > As a personal note, those libraries can be found in the following directory on
    our building machine BigBird,

    ```
    /home/y8z/BBird_Ext/Packages/lammps-stable_29Sep2021_update2/build
    ```

    <br />

    > On the public domain, those libraries can be found here,

    > [https://code.ornl.gov/mth/RMCProfile/-/tree/lammps_rmc_ready/package/linux/MPI](https://code.ornl.gov/mth/RMCProfile/-/tree/lammps_rmc_ready/package/linux/MPI)

6. With the MPI program compiled following the way described in this doc, it
should be only executed via `/opt/bin/mpirun`, i.e., directly executing the
compiled executable just like a serial program should *NOT* work.

    > In our case of compiling the RMCProfile package with lammps, we are linking the
    dynamic library `libmpi_cxx.so` (see the `-lmpi_cxx` flag in the link below),

    > [https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_mpi#L21-24](https://code.ornl.gov/mth/RMCProfile/-/blob/lammps_rmc_ready/ubuntu1804-cuda-intel/Makefile_mpi#L21-24)

    > So, we include the `libmpi_cxx.so.40.20.1` library file together with its
    soft link in our shipped RMCProfile package as well, under the shared library
    directory which will be exported as the `LD_LIBRARY_PATH` environment variable
    while running RMCProfile. I am not sure whether this is necessary but it should
    be safe to include it anyways.

7. About the hybrid MPI and OMP parallel implementation.

    > It seems that the OMP session can be safely included in the MPI code without
    that much special attention to be paid, as long as we are not playing around
    with MPI communications within the OMP session.

    > For compiling, when using intel compiler, the flag for OMP is `-qopenmp`
    whereas for GNU compiler, the flag is `-fopenmp`.

<b>References</b>

[1] [https://rmcprofile.ornl.gov/](https://rmcprofile.ornl.gov/)