---
layout: post
title: Notes on Jupyterhub server setup
subtitle: Tips & Tricks
tags: [jupyter, web development]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<style>
code {
  font-family: Consolas,"courier new";
  color: crimson;
  background-color: #f1f1f1;
  padding: 2px;
  font-size: 105%;
}
</style>

<p style='text-align: justify'>
This post is a collection of notes of tips and tricks when trying to set up Jupyterhub server on Ubuntu 20.04 focal, together with the installation of various kernels and extensions.
</p>

<p align='center'>
<img src="/assets/img/posts/jupyterhub.png"
   style="border:none;"
   alt="jupyterhub"
   title="jupyterhub" />
</p>

<p style='text-align: justify'>
The instructions in Ref. [1] had been followed to set up the Jupyterhub server. The server was set up in a virtual environment located at `/opt/jupyterhub` and therefore all the `python`, `jupyter`, etc. relevant executables belongs to this virtual environment. This means that every time we need to execute one of those executables to install, e.g., a kernel, or a jupyter server relevant executables (e.g., `ipykernel`, `jupyter_server`, etc.), we need to explicitly refer to this virtual environment where the server resides.

<br />

Here follows is a random collection of issues that I came across when trying to set up the server and running various kernels and capabilities. Sometimes, improper combination of server executables may prevent certain services/capabilities from functioning properly.
</p>

> N.B. The issues listed here follows are not following a logic order.

> Issue-1: `jQuery path not found`, or something very similar.

<p style='text-align: justify; margin-left: 50px'>
<b>Solution: </b>This issue may be due to the version of `jupyterlab_server` -- too new a version may cause this issue, especially when trying to refresh the page. As of writing, it seems the version of `2.10.0` of `jupyterlab_server` is working properly.

<br />

Here follows is a list of currently properly working versions of packages (which can be obtained via executing `/opt/jupyterhub/bin/jupyter --version`),
<br />
---

<br />
IPython          : 8.6.0
<br />
ipykernel        : 6.17.1
<br />
ipywidgets       : 8.0.2
<br />
jupyter_client   : 7.4.4
<br />
jupyter_core     : 4.11.2
<br />
jupyter_server   : 1.23.1
<br />
jupyterlab       : 3.5.0
<br />
nbclient         : 0.7.0
<br />
nbconvert        : 7.2.4
<br />
nbformat         : 5.7.0
<br />
notebook         : 6.5.2
<br />
qtconsole        : not installed
<br />
traitlets        : 5.5.0
<br />

---

</p>

> Issue-2: `plotly` figure is not showing up.

<p style='text-align: justify; margin-left: 50px'>
<b>Solution: </b> This again is something to do with the package version that is being installed. `ipywidgets` version 8.0.2 was found not to be working with `plotly` interactive plot. The version `7.7.2` should be working, which can be installed via [2],

<br />

<code>
sudo /opt/jupyterhub/bin/python3 -m pip install ipywidgets==7.7.2
<code/>

</p>

> Issue-2: `plotly` figure is not showing up.

<p style='text-align: justify; margin-left: 50px'>
<b>Solution: </b> This again is something to do with the package version that is being installed. `ipywidgets` version 8.0.2 was found not to be working with `plotly` interactive plot. The version `7.7.2` should be working, which can be installed via [2],

<br />

<code>
sudo /opt/jupyterhub/bin/python3 -m pip install ipywidgets==7.7.2
<code/>

</p>

> Issue-2: Jupyter server fails to build, when executing `sudo /opt/jupyterhub/bin/jupyter lab build`

<p style='text-align: justify; margin-left: 50px'>
<b>Solution-1: </b> Sometimes it may be due to some problematic extension that was installed. In this case, we want to use either `pip` or `conda` to uninstall or remove the extension that was causing the problem (refer to the log file of the build that will be printed out on terminal). Meanwhile, we also need to uninstall the extension that was installed via the `jupyter labextension` command (if it has ever been installed in this way). Also, it seems that the un-installation of the extension should be after the un-installation of the module with `pip` or `conda`. In the case of the `ipysheet` extension, here follows may be the step to follow for the un-installation,

<br />
<br />

1. `sudo /opt/jupyterhub/bin/python3 -m pip uninstall ipysheet`
<br />
or
<br />
`sudo /opt/conda/bin/conda remove -p /opt/conda/envs/python ipysheet`
<br />
<br />
2. `sudo /opt/jupyterhub/bin/jupyter labextension uninstall ipysheet`
<br />
<br />
<b>Solution-2: </b> Sometimes it could be due to low memory issue, i.e., the system resource is not enough for building the jupyterlab server. In such a situation, following the prompt on the command line usually does not help. Specifically for Google cloud service, the advantage is that we can freely change the size of our instance allocation. So we can increase the size temporarily and build the jupyterlab server. After that, we can then change the size back to the smaller value as before just to save money -- we will be billed based on the actual use of resource, which is very nice! Alternatively, the following command could also be used to build jupyter server to get around with the memory issue, sometimes,
<br />
`sudo /opt/jupyterhub/bin/jupyter lab build --dev-build=False --minimize=False`
</p>

> Issue-3: Installation of `xeus-cling` kernel for C++

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> For installing `xeus-cling` kernel for C++, we need to use `mamba`, following the steps below,
<br />
1. Follow the instruction in Ref. [3] to install `mamba` - we need to use the manual way of installation, i.e. using the `curl` command. Then we want to copy the `micromamba` binary to a central location, e.g., `/opt/jupyterhub/bin`.
<br />
<b>N. B. </b> This is the directory that has been used for hosting the central jupyterhub server and I just put `mamba` executable there as well just to stay self-consistent. `This may not be necessary.`
<br />
2. `mamba shell init --shell=bash --prefix=~/.micromamba`
<br />
<b>N. B. </b> This guarantees that all envs and `mamba` stuff will be installed to the specified prefix directory.
<br />
3. `source .bashrc`
<br />
<b>N. B. </b> This is to make sure that the above added mamba initialization in our bash configuration file is taking effect.
<br />
4. Create the `cling` env, via `mamba create -n cling`.
<b>N. B. </b> To remove an env, we need `mamba env remove -n cling`.
<br />
5. `mamba install xeus-cling -c conda-forge --prefix=~/.micromamba`
<br />
6. The command above will create jupyter kernel under `$PREFIX/share/jupyter/kernels`, or on some machines, the kernels may be found in some other places, like `$PREFIX//cling/share/jupyter/kernels`. To let our jupyterhub server see our kernels, we need to manually copy the available kernels to the proper directory (where jupyterhub can find). In my case, the target directory is `/opt/jupyterhub/share/jupyter/kernels/`.
</p>

> Issue-4: List of kernels is not complete

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> Sometimes when listing out all kernels using `jupyter kernelspec list`, it
could happen that not all kernels are listed out. That could be because we were using the wrong `jupyter`. For example, if there a version of `jupyter` installed at `/usr/bin/jupyter`, it will always win over other `jupyter` versions. If it happens that we follow certain instructions to install the `jupyterhub` server into other places (e.g., Ref. [1]), then we will not see all kernels available on our machine by `jupyter ernelspec list`. In such a situation, we have to explicitly give the full path to the `jupyter` installed in a certain location (where we installed the `jupyterhub` server). In my case, the full path is `/opt/jupyterhub/bin/jupyter`.
</p>

> Issue-5: Installation of Juka kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> To install the `juka` kernel, for sure, initially, we need to install `Juka` - see Ref. [4]. Then execute `sudo /opt/jupyterhub/bin/pip install juka-kernel`, followed by `sudo /opt/jupyterhub/bin/python -m juka-kernel.install`. Finally, we need to go to the `juka_kernel` installation location (e.g. `/opt/jupyterhub/lib/python3.8/site-packages/juka/`) and execute, `/opt/jupyterhub/bin/python install.py --user`.
</p>

> Issue-6: Installation of SSH kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> When installing the SSH kernel following the instruction in Ref. [5], by default, the kernel is installed into `/root/.local/share/jupyter/kernels/ssh`. It seems that Jupyter has difficulty in finding the installed kernel in the location above. So, we may need to manually copy the whole directory above to our local area, such as `~/.local/share/jupyter/kernels/ssh`. A restart of the
server is needed to be able to see the copied kernel.
</p>

> Issue-7: Installation of C kernel

<p style='text-align: justify; margin-left: 50px'>
<b> Solution:</b> When installing the C kernel following Ref. [6], the execution of the kernel installation command may require `sudo` privilege,
in which case the kernel will be installed to the root directory (i.e. `/root/.local/share/jupyter/kernels/c`). In that case, we then need to    manually copy the directory `c` to our local area, i.e., `$HOME/.local/share/jupyter/kernels/c`.
</p>

> Issue-8: Installation of Fortran kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution-1:</b> For installing the `Coarray Fotran` kernel, we can following Ref. [7]. One of the prerequisite package for installing this kernel is `opencoarrays`. For the installation of this package, if we are on Ubuntu higher than 18.04, we may need to follow the instruction in Ref. [8] to install it manually.
<br />
<b> Solution-2:</b> If the `Coarrays Fortran` kernel fails to execute any commands (complaining about libraries issues),
try to execute the followings commands,
<br />
---
<br />
sudo cp /usr/lib/x86_64-linux-gnu/op
<br />en-coarrays/mpich/lib/* /usr/lib/x86_64-linux-gnu/
<br />
sudo cp /usr/lib/x86_64-linux-gnu/open-coarrays/openmpi/lib/* /usr/lib/x86_64-linux-gnu/
<br />
cd /usr/lib/x86_64-linux-gnu/
<br />
sudo ln -s libmpi_usempif08.so.40 libmpi_usempif08.so
<br />
sudo ln -s libmpi_usempi_ignore_tkr.so.40 libmpi_usempi_ignore_tkr.so
<br />
sudo ln -s libmpi_mpifh.so.40 libmpi_mpifh.so
<br />
sudo ln -s libmpi_mpifh.so.40 libmpi_mpifh.so
<br />
sudo ln -s libmpi.so.40 libmpi.so
<br />
sudo ln -s libopen-rte.so.40 libopen-rte.so
<br />
sudo ln -s libopen-pal.so.40 libopen-pal.so
<br />
sudo ln -s libevent-2.1.so.7 libevent.so
<br />
sudo ln -s libevent_pthreads-2.1.so.7 libevent_pthreads.so
<br />
sudo ln -s libhwloc.so.15 libhwloc.so
<br />
sudo mkdir -p  /usr/include/OpenCoarrays-2.0.0-rc1_GNU-7.3.0
<br />
sudo mkdir -p /usr/lib/x86_64-linux-gnu/openmpi/includ
<br />
---
</p>

> Issue-10: conda environment as jupyter kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> When following Ref. [1] to install the jupyterhub server, a conda environment was set up to become a kernel environment. In the future if we want to install packages to that environment to make certain modules available in that kernel, we need to use the conda `--prefix` flag to specify the full path to the conda environment where we are going to install certain modules.
</p>

> Issue-11: Installation of Lua kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> The `Lua` kernel can be easily installed following Ref. [9], in which the `iLua` kernel will be installed. Also, one needs to install `lua` separately to make the `lua` command system-wide available.
</p>

> Issue-12: Installation of `diffpy-cmi` & `mpdf` kernel

<p style='text-align: justify; margin-left: 50px'>
<b> Solution:</b> To install `diffpy`, `diffpy-CMI` and `mpdf` packages into a single conda environment and make it available in a dedicated kernel, follow the steps below,
<br />
---
<br />
$ sudo /opt/conda/bin/conda create --prefix /opt/conda/envs/diffpy python=3.7 ipykernel
<br />
$ sudo /opt/conda/envs/diffpy/bin/python -m ipykernel install --prefix=/opt/jupyterhub/ --name 'diffpy' --display-name "Python (diffpy)"
<br />
$ sudo /opt/conda/bin/conda install -p /opt/conda/envs/diffpy -c diffpy diffpy-cmi
<br />
$ git clone https://github.com/FrandsenGroup/diffpy.mpdf.git
<br />
$ mv diffpy.mpdf ~/.diffpy.mpdf && cd ~/.diffpy.mpdf
<br />
$ sudo /opt/conda/envs/diffpy/bin/python setup.py install
<br />
---
</p>

> Issue-13: Installation of `sagemath` kernel

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> To install the `sagemath` kernel, we need to follow the instructions below,
<br />
---
<br />
Step-1: Ref. [10]
<br />
Step-2: Ref. [11]
<br />
---
</p>

> Issue-14: Installation of the jupyter language server

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> Follow the instruction in Ref. [12] for installation and refer to Ref. [13] for configuration of the service.
</p>

> Issue-15: Code formt

<p style='text-align: justify; margin-left: 50px'>
<b> Solution:</b> Jupyterlab code format needs `black` to be installed. Refer to Ref. [14] for more details.
</p>

> Issue-16: Execution of commands in cell within Jupyter server take forever

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> Sometimes a kernel may be launched successfully, but when trying to execute some commands within jupyterlab, it might fail. In this case, we can take a look at the kernel specification file (the JSON file) and see whether it is a problem with the improper command being executed when launching the kernel. Here follows is an example with the `scilab` kernel.
<br />
1. First, we can list out all the available kernels and get an idea about where the corresponding kernel file is located,
<br />
$ `/opt/jupyterhub/bin/jupyter kernelspec list`
<br />
<b>N. B. </b> In the case of `scilab`, the kernel file is here,
<br />
`/opt/jupyterhub/share/jupyter/kernels/scilab`
<br />
2. Then cd into the kernel directory identified above and edit the `kernel.json` file. In the case of `scilab`, the default kernel file reads,
</p>

   ```json
   {
       "argv": ["python",
                "-m", "scilab_kernel",
                "-f", "{connection_file}"],
       "display_name": "Scilab",
       "language": "scilab",
       "mimetype": "text/x-octave",
       "name": "scilab"
   }
   ```
<p style='text-align: justify; margin-left: 50px'>
3. Then we know that the actual command being executed at each time of the kernel launching is the system-wise version of `python`. In my case, this will fail, since we need the version of `python` corresponding to our Jupyterhub server, which is located at `/opt/jupyterhub/bin/python`. So, we need to change the `kernel.json` file to be like this,
</p>

   ```json
   {
       "argv": ["/opt/jupyterhub/bin/python",
                "-m", "scilab_kernel",
                "-f", "{connection_file}"],
       "display_name": "Scilab",
       "language": "scilab",
       "mimetype": "text/x-octave",
       "name": "scilab"
   }
   ```

<br />

> Issue-17: `scilab` kernel log file

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> By default, running the `scilab` kernel will create a `test.log` file under home directory. To avoid this behavior, we can edit the corresponding `kernel.py` file and comment out relevant lines -- line 97-98 in `/opt/jupyterhub/lib/python3.8/site-packages/scilab_kernel/kernel.py`, i.e., the following two lines,
</p>

   ```python
   with open(os.path.expanduser('~/test.log'), 'w') as fid:
      fid.write('executable: ' + executable)
   ```

<br />

> Issue-18: `wolfram` kernel documentation file location

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> By default, the installed wolfram player kernel will use the home directory as the user documentation directory (which can be printed out within either jupyterlab cell execution or from within `wolframscript` CLI, by executing `$UserDocumentsDirectory`). This will create a `Wolfram Player` folder at each time when launching the kernel. To move this directory to another location, we need [15],
<br />
1. `xdg-user-dirs-update --set DOCUMENTS /home/cloud/.wolfram_doc` (from command line)
<br />
<b>N. B.</b> The directory specified at the end of the command can be anywhere we would like it to be.
<br />
2. Rebuild jupyterhub, by running `sudo /opt/jupyterhub/bin/jupyter lab build`.
<br />
3. Restart jupyterhub service, by running `sudo systemctl restart jupyterhub`
</p>

> Issue-19: `go` kernel creates a `go` folder under home directory

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> By default, the `go` kernel will use the `go` directory under home directory as the main working directory and therefore `$HOME/go` directory has to be there. To move the `go` directory to another location (e.g., `$HOME/.go`), we may want to put the following line in our `.bashrc` and `.bash_profile` file, `export GOPATH=$HOME/.go` and edit the `go` kernel JSON file to,
</p>

   ```json
   {
       "argv": [
           "/home/cloud/.go/bin/gophernotes",
           "{connection_file}"
       ],
       "display_name": "Go",
       "language": "go",
       "name": "go"
   }
   ```

<p style='text-align: left; margin-left: 50px'>
<b>N. B.</b> The location of `kernel.json` to be edited is, `/home/cloud/.local/share/jupyter/kernels/gophernotes/kernel.json`.
</p>

> Issue-20: `R` kernel creates an `R` folder under home directory

<p style='text-align: justify; margin-left: 50px'>
<b>Solution:</b> When following the instructions in Ref. [16] to install the `IRkernel` for R, the installed relevant R packages will be put under `$HOME/R` directory. Those installed packages can actually be moved to the default package installation location of `R`, e.g., `/usr/lib/R/library` so that the `R` folder under home directory can be removed. To do that, we can execute, `sudo cp -av $HOME/R/x86_64-pc-linux-gnu-library/3.6/* /usr/lib/R/library/`.
</p>

<br />

<b>References</b>

[1] [https://jupyterhub.readthedocs.io/en/1.2.1/installation-guide-hard.html](https://jupyterhub.readthedocs.io/en/1.2.1/installation-guide-hard.html)

[2] [https://github.com/jupyterlab/jupyterlab/issues/12977](https://github.com/jupyterlab/jupyterlab/issues/12977)

[3] [https://mamba.readthedocs.io/en/latest/installation.html#linux-and-macos](https://mamba.readthedocs.io/en/latest/installation.html#linux-and-macos)

[4] [https://jukalang.com/download](https://jukalang.com/download)

[5] [https://github.com/NII-cloud-operation/sshkernel](https://github.com/NII-cloud-operation/sshkernel)

[6] [https://github.com/brendan-rius/jupyter-c-kernel](https://github.com/brendan-rius/jupyter-c-kernel)

[7] [https://github.com/sourceryinstitute/jupyter-CAF-kernel](https://github.com/sourceryinstitute/jupyter-CAF-kernel)

[8] [https://askubuntu.com/questions/1277932/cannot-install-open-coarrays-bin-for-gfortran-on-ubuntu-20-04](https://askubuntu.com/questions/1277932/cannot-install-open-coarrays-bin-for-gfortran-on-ubuntu-20-04)

[9] [https://github.com/guysv/ilua](https://github.com/guysv/ilua)

[10] [https://sagemanifolds.obspm.fr/install_ubuntu.html](https://sagemanifolds.obspm.fr/install_ubuntu.html)

[11] [https://doc.sagemath.org/html/en/installation/launching.html#setting-up-sagemath-as-a-jupyter-kernel-in-an-existing-jupyter-notebook-or-jupyterlab-installation](https://doc.sagemath.org/html/en/installation/launching.html#setting-up-sagemath-as-a-jupyter-kernel-in-an-existing-jupyter-notebook-or-jupyterlab-installation)

[12] [https://jupyterlab-lsp.readthedocs.io/en/latest/Installation.html#please-read-this-first](https://jupyterlab-lsp.readthedocs.io/en/latest/Installation.html#please-read-this-first)

[13] [https://github.com/jupyter-lsp/jupyterlab-lsp#other-configuration-methods](https://github.com/jupyter-lsp/jupyterlab-lsp#other-configuration-methods)

[14] [https://black.readthedocs.io/en/stable/getting_started.html](https://black.readthedocs.io/en/stable/getting_started.html)

[15] [https://mathematica.stackexchange.com/questions/89369/prevent-10-2-from-creating-wolfram-mathematica-directory-on-linux](https://mathematica.stackexchange.com/questions/89369/prevent-10-2-from-creating-wolfram-mathematica-directory-on-linux)

[16] [https://irkernel.github.io/installation/](https://irkernel.github.io/installation/)