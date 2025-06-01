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

## Issue-1: `jQuery path not found`, or something very similar.

**Solution:** This issue may be due to the version of `jupyterlab_server` -- too new a version may cause this issue, especially when trying to refresh the page. As of writing, it seems the version of `2.10.0` of `jupyterlab_server` is working properly.

<br />

Here follows is a list of currently properly working versions of packages (which can be obtained via executing `/opt/jupyterhub/bin/jupyter --version`),

<br />

---
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

---

## Issue-2: `plotly` figure is not showing up.

**Solution:**  This again is something to do with the package version that is being installed. `ipywidgets` version 8.0.2 was found not to be working with `plotly` interactive plot. The version `7.7.2` should be working, which can be installed via [2],

<br />

<code>
sudo /opt/jupyterhub/bin/python3 -m pip install ipywidgets==7.7.2
<code/>

## Issue-3: Jupyter server fails to build, when executing `sudo /opt/jupyterhub/bin/jupyter lab build`

**Solution:** Sometimes it may be due to some problematic extension that was installed. In this case, we want to use either `pip` or `conda` to uninstall or remove the extension that was causing the problem (refer to the log file of the build that will be printed out on terminal). Meanwhile, we also need to uninstall the extension that was installed via the `jupyter labextension` command (if it has ever been installed in this way). Also, it seems that the un-installation of the extension should be after the un-installation of the module with `pip` or `conda`. In the case of the `ipysheet` extension, here follows may be the step to follow for the un-installation,

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
<br />
To get a sort of permanent solution to this issue (i. e., without the need to
append `--dev-build=False --minimize=False` flags to the build command), we can
edit the `jupyter_config.py` file under `/opt/jupyterhub/etc/jupyter` (create
the file if it does not exist under the directory). The directory can be
obtained via the command `/opt/jupyterhub/bin/jupyter --path`.

## Issue-4: Installation of `xeus-cling` kernel for C++

**Solution:** For installing `xeus-cling` kernel for C++, we need to use `mamba`, following the steps below,
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

## Issue-5: List of kernels is not complete

**Solution:** Sometimes when listing out all kernels using `jupyter kernelspec list`, it
could happen that not all kernels are listed out. That could be because we were using the wrong `jupyter`. For example, if there a version of `jupyter` installed at `/usr/bin/jupyter`, it will always win over other `jupyter` versions. If it happens that we follow certain instructions to install the `jupyterhub` server into other places (e.g., Ref. [1]), then we will not see all kernels available on our machine by `jupyter ernelspec list`. In such a situation, we have to explicitly give the full path to the `jupyter` installed in a certain location (where we installed the `jupyterhub` server). In my case, the full path is `/opt/jupyterhub/bin/jupyter`.

## Issue-6: Installation of Juka kernel

**Solution:** To install the `juka` kernel, for sure, initially, we need to install `Juka` - see Ref. [4]. Then execute `sudo /opt/jupyterhub/bin/pip install juka-kernel`, followed by `sudo /opt/jupyterhub/bin/python -m juka-kernel.install`. Finally, we need to go to the `juka_kernel` installation location (e.g. `/opt/jupyterhub/lib/python3.8/site-packages/juka/`) and execute, `/opt/jupyterhub/bin/python install.py --user`.

## Issue-7: Installation of SSH kernel

**Solution:** When installing the SSH kernel following the instruction in Ref. [5], by default, the kernel is installed into `/root/.local/share/jupyter/kernels/ssh`. It seems that Jupyter has difficulty in finding the installed kernel in the location above. So, we may need to manually copy the whole directory above to our local area, such as `~/.local/share/jupyter/kernels/ssh`. A restart of the
server is needed to be able to see the copied kernel.

## Issue-8: Installation of C kernel

**Solution:** When installing the C kernel following Ref. [6], the execution of the kernel installation command may require `sudo` privilege,
in which case the kernel will be installed to the root directory (i.e. `/root/.local/share/jupyter/kernels/c`). In that case, we then need to    manually copy the directory `c` to our local area, i.e., `$HOME/.local/share/jupyter/kernels/c`.

## Issue-9: Installation of Fortran kernel

**Solution:** For installing the `Coarray Fotran` kernel, we can following Ref. [7]. One of the prerequisite package for installing this kernel is `opencoarrays`. For the installation of this package, if we are on Ubuntu higher than 18.04, we may need to follow the instruction in Ref. [8] to install it manually.
<br />
<b> Solution-2:</b> If the `Coarrays Fortran` kernel fails to execute any commands (complaining about libraries issues),
try to execute the followings commands,
<br />

---

$ sudo cp /usr/lib/x86_64-linux-gnu/open-coarrays/mpich/lib/* /usr/lib/x86_64-linux-gnu/
<br />
$ sudo cp /usr/lib/x86_64-linux-gnu/open-coarrays/openmpi/lib/* /usr/lib/x86_64-linux-gnu/
<br />
$ cd /usr/lib/x86_64-linux-gnu/
<br />
$ sudo ln -s libmpi_usempif08.so.40 libmpi_usempif08.so
<br />
$ sudo ln -s libmpi_usempi_ignore_tkr.so.40 libmpi_usempi_ignore_tkr.so
<br />
$ sudo ln -s libmpi_mpifh.so.40 libmpi_mpifh.so
<br />
$ sudo ln -s libmpi_mpifh.so.40 libmpi_mpifh.so
<br />
$ sudo ln -s libmpi.so.40 libmpi.so
<br />
$ sudo ln -s libopen-rte.so.40 libopen-rte.so
<br />
$ sudo ln -s libopen-pal.so.40 libopen-pal.so
<br />
$ sudo ln -s libevent-2.1.so.7 libevent.so
<br />
$ sudo ln -s libevent_pthreads-2.1.so.7 libevent_pthreads.so
<br />
$ sudo ln -s libhwloc.so.15 libhwloc.so
<br />
$ sudo mkdir -p  /usr/include/OpenCoarrays-2.0.0-rc1_GNU-7.3.0
<br />
$ sudo mkdir -p /usr/lib/x86_64-linux-gnu/openmpi/include
<br />

---

## Issue-10: conda environment as jupyter kernel

**Solution:** When following Ref. [1] to install the jupyterhub server, a conda environment was set up to become a kernel environment. In the future if we want to install packages to that environment to make certain modules available in that kernel, we need to use the conda `--prefix` flag to specify the full path to the conda environment where we are going to install certain modules.

<br />

A typical command would be,

`sudo /home/cloud/.conda/envs/mantid-developer/bin/python -m ipykernel install --prefix=/opt/jupyterhub/ --name 'mantid-developer' --display-name "Mantid"`

<br />

Using the command above, the local conda environment located at `/home/cloud/.conda/envs/mantid-developer` will be registered as a jupyter kernel. The kernel will be installed into `/opt/jupyterhub/share/jupyter/kernels/mantid-developer`.

## Issue-11: Installation of Lua kernel

**Solution:** The `Lua` kernel can be easily installed following Ref. [9], in which the `iLua` kernel will be installed. Also, one needs to install `lua` separately to make the `lua` command system-wide available.

## Issue-12: Installation of `diffpy-cmi` & `mpdf` kernel

**Solution:** To install `diffpy`, `diffpy-CMI` and `mpdf` packages into a single conda environment and make it available in a dedicated kernel, follow the steps below,
<br />

---

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

---

## Issue-13: Installation of `sagemath` kernel

**Solution:** To install the `sagemath` kernel, we need to follow the instructions below,
<br />

---
Step-1: Ref. [10]
<br />
Step-2: Ref. [11]

---

## Issue-14: Installation of the jupyter language server

<b>Solution:</b> Follow the instruction in Ref. [12] for installation and refer to Ref. [13] for configuration of the service.

## Issue-15: Code format

<b> Solution:</b> Jupyterlab code format needs `black` to be installed. Refer to Ref. [14] for more details.

## Issue-16: Execution of commands in cell within Jupyter server takes forever

<b>Solution:</b> Sometimes a kernel may be launched successfully, but when trying to execute some commands within jupyterlab, it might fail. In this case, we can take a look at the kernel specification file (the JSON file) and see whether it is a problem with the improper command being executed when launching the kernel. Here follows is an example with the `scilab` kernel.
<br />
1. First, we can list out all the available kernels and get an idea about where the corresponding kernel file is located,
<br />
`/opt/jupyterhub/bin/jupyter kernelspec list`
<br />
<b>N. B. </b> In the case of `scilab`, the kernel file is here,
<br />
`/opt/jupyterhub/share/jupyter/kernels/scilab`
<br />
2. Then cd into the kernel directory identified above and edit the `kernel.json` file. In the case of `scilab`, the default kernel file reads,

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

3. Then we know that the actual command being executed at each time of the kernel launching is the system-wise version of `python`. In my case, this will fail, since we need the version of `python` corresponding to our Jupyterhub server, which is located at `/opt/jupyterhub/bin/python`. So, we need to change the `kernel.json` file to be like this,

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

## Issue-17: `scilab` kernel log file

<b>Solution:</b> By default, running the `scilab` kernel will create a `test.log` file under home directory. To avoid this behavior, we can edit the corresponding `kernel.py` file and comment out relevant lines -- line 97-98 in `/opt/jupyterhub/lib/python3.8/site-packages/scilab_kernel/kernel.py`, i.e., the following two lines,

```python
with open(os.path.expanduser('~/test.log'), 'w') as fid:
    fid.write('executable: ' + executable)
```

<br />

## Issue-18: `wolfram` kernel documentation file location


<b>Solution:</b> By default, the installed wolfram player kernel will use the home directory as the user documentation directory (which can be printed out within either jupyterlab cell execution or from within `wolframscript` CLI, by executing `$UserDocumentsDirectory`). This will create a `Wolfram Player` folder at each time when launching the kernel. To move this directory to another location, we need [15],
<br />
1. `xdg-user-dirs-update --set DOCUMENTS /home/cloud/.wolfram_doc` (from command line)
<br />
<b>N. B.</b> The directory specified at the end of the command can be anywhere we would like it to be.
<br />
2. Rebuild jupyterhub, by running `sudo /opt/jupyterhub/bin/jupyter lab build`.
<br />
3. Restart jupyterhub service, by running `sudo systemctl restart jupyterhub`

## Issue-19: `go` kernel creates a `go` folder under home directory

<b>Solution:</b> By default, the `go` kernel will use the `go` directory under home directory as the main working directory and therefore `$HOME/go` directory has to be there. To move the `go` directory to another location (e.g., `$HOME/.go`), we may want to put the following line in our `.bashrc` and `.bash_profile` file, `export GOPATH=$HOME/.go` and edit the `go` kernel JSON file to,

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

<b>N. B.</b> The location of `kernel.json` to be edited is, `/home/cloud/.local/share/jupyter/kernels/gophernotes/kernel.json`.

<br />

## Issue-20: `R` kernel creates an `R` folder under home directory

<b>Solution:</b> When following the instructions in Ref. [16] to install the `IRkernel` for R, the installed relevant R packages will be put under `$HOME/R` directory. Those installed packages can actually be moved to the default package installation location of `R`, e.g., `/usr/lib/R/library` so that the `R` folder under home directory can be removed. To do that, we can execute, `sudo cp -av $HOME/R/x86_64-pc-linux-gnu-library/3.6/* /usr/lib/R/library/`.

<br />

## Issue-21: Installation of `jupyterlab-latex` extension

<b>Solution:</b> To make sure the extension works properly, we need to install
`xelatex` first. Refer to Ref. [17] for information about the installation of
`xelatex`.

<br />

## Issue-22: Issue with multiline editing

<b>Solution:</b> Sometimes we may find that using `Alt` + left mouse dragging is
not able to perform the multiline editing (i.e., vertical selection). In this
case, we can go to the settings and search for `code jump` and we will be able
to see `Modifier key` in the settings. We can try to change it to `AltGraph` --
quite often it is defaulted to `Alt` key which will then make the multiline
editing fail (the cursor will change shape but not functioning). This tip can be
found in Ref. [18], where it is suggested to change this key to `Control`.
However, it seems that changing it to `Control` will disable the multi-cursor
editing (i.e., `Control` + left click on multiple locations). Finally, it seems
that changing it to `AltGraph` will enable both functionalities.

<br />

<b>N. B.</b> Changing it to `Shift` would then diable the `Shift + Left Click` for
block selection.

<br />

## Issue-23: Installation of SoS for working with multiple kernels in one notebook in a convenient manner

<b>Solution:</b> SoS notebook is actually a jupyter kernel that allows up to
select different sub-kernels inside the notebook. So that if we want to run
multiple kernels in a single notebook without the necessity to changing kernels
manually, this is the tool for us. Also, it has the capability to share
variables in between kernels so it is not simply a kernel selection tool but
instead has the interactability between different kernels. To install this tool,
we can refer to Ref. [19] which contains very detailed step-by-step instructions.
One little issue we should notice is that during the installation, there is a
step to execute `python -m sos_notebook.install`. At this stage, it may complain
about not being able to find the `jupyter-contrib-core` module. In this case, we
can use `pip install jupyter-contrib-core` [20] to install the module and rerun the
step and it should work.

<br />

<b>N. B. </b>We should prepend the two commands mentioned with the full path of
the executables, just like what we have been doing across the whole note here,
`/opt/jupyterhub/bin/`.

<br />

## Issue-24: Jupyter on Windows -- 1

<b>Solution:</b>  With my local Windows jupyter server, for some reason, it
fails to load the `jupyter_ntoebook_config.py` configuration file which sets up
the port, password, etc. Not sure why but suspect this could be due to the
package version issue  -- some of the packages might be updated or downgraded
unintentionally during installing some packages. The workaround is to directly
specify the port number when launching the jupyter lab server, by `jupyter lab
--no-browser --port 8000`. For password setup (once for all), we can run the
command `jupyter lab password` followed by inputting the password twice. A
configuration file containing the password will be saved to the config folder,
which will be discussed in next issue.

<br />

<b>N. B.</b> A bit of Googling seems to suggest that the jupyter lab
configuration file is `jupyter_lab_config.py` which can be generated by
`jupyter-lab --generate-config`. Not sure why previously using `jupyter_ntoebook_config.py` just worked out.

<br />

## Issue-25: Jupyter on Windows -- 2

The configuration file is usually located in the `.jupyter` under user's home directory. To obtain the location where the configuration file is stored, we can run `jupyter --config-dir`.

<br />

## Issue-26: Jupyter on Windows -- 3

**Solution:** To make the jupyter server an automatically loaded service when the machine starts, we can create a startup VBS script to launch the server. This may not be the optimal way, but it should work for the personal use. Here follows is attached the VBS script, for which we can make a shortcut, and we can put the shortcut into the startup folder (`Start + R` to launch the `Run` box, and type `shell:startup` to bring up the startup folder),

<br />

```
Set shell = CreateObject("WScript.Shell")
shell.CurrentDirectory = "C:\Users\yuanp\OneDrive\One_Documents\JupyterLab"
shell.Run "cmd /c jupyter lab --no-browser --port 8000 --ip 0.0.0.0", 0, false
```

<br />

By doing this, we can then access the server via `localhost:8000` (or via the internet if the port forwarding is configured properly) at any time after the machine starts, without the need to start up the server manually.

<br />

<b>N. B.</b> The second line here specifies the working directory. The '--ip 0.0.0.0' flag tells the server to accept connections from the internet but not only the local host. The `0, false` flag tells the script not to keep the terminal running after launching the server.

<br />

## Issue-27: Console Log for `ipywidgets` debug

<b>Solution:</b> When developing `ipywidgets` interface in Jupyterlab, sometime we will find that the widgets interface is not functioning as expected but there is no error message printed out. To get a knowledge about the error message in case of any for debugging purpose, we need to enable the console log view in Jupyterlab. To do that, we need to go to `View` $$\rightarrow$$ `Show Console Log` from the menu and then at the bottom of the page, we should be able to see all the console logs.

<br />

## Issue-28: `matplotlib` and `plotly` interactive plot

<b>Solution:</b> When there are some issues with either `%matplotlib widget` or
some javascript error in showing the interactive plot or widgets, it could be
something to do with the version of `ipywidgets` installed. Try the following
steps,

```bash
sudo /opt/conda/envs/python/bin/pip uninstall ipywidgets
sudo /opt/conda/envs/python/bin/pip install ipywidgets
```

It seems that installing the version of `8.0.4` for `ipywidgets` does solve the
issue. As a side note, on my local jupyter server on a Windows machine, I found
only the version of `7.6.5` for `ipywidgets` will work.

<br />

## Issue-29: Installation of `Mantid` kernel

<b>Solution:</b> First, we need to follow the instruction in Ref. [21] to install
the conda environment for `Mantid`. After setting up the conda environment, we
need to run the following command to add a certain build of `Mantid` into the
conda environment path so that the corresponding version of `python` can find
all `Mantid` relevant modules,

```bash
conda activate mantid-developer
python MANTID_REPO_DIR/build/bin/AddPythonPath.py
```


where `MANTID_REPO_DIR` refers to the full path of the `mantid` repo directory
and we are assuming that the conda environment for `Mantid` is called `mantid-developer`.
Then we can refer to the instruction presented in `Issue-10` above to register
the corresponding conda environment as a kernel of Jupyter.

<br />

## Issue-30: Use the classic interface

<b>Solution:</b> Sometimes, we may need to use the classic interface instead of the
jupyterlab interface. For example, the `RISE` extension that can turn the notebook into
slides will not work with the jupyterlab interface. To enable the classic interface, we
need to remove `lab` from the URL. Suppose the jupyterlab URL is `https://powder.ornl.gov/hub/user/cloud/lab/tree/Temp/mp_api_trying.ipynb`, the
URL for using the classic interface will be `https://powder.ornl.gov/hub/user/cloud/tree/Temp/mp_api_trying.ipynb`.

<br />

## Issue-31: Plotly plots not showing in classic interface

<b>Solution:</b> If plotly plots are not showing in the classic interface, consider shutdown the kernel and
restart.

<br />

## Issue-32: Change the log for kernel

**Solution:** To change the logo for a kernel, we need to find the kernel spec directory first. Run the following command,

```bash
/opt/jupyterhub/bin/jupyter kernelspec list
```

Here, I am assuming the instructions in Ref. [1] was followed for the Jupyterhub setup. We will see the list of kernels and their spec locations, e.g.,

```
/opt/jupyterhub/share/jupyter/kernels/python_pdfgetxn
```

Inside the directory, there is a `kernel.json` file which contains the kernel configurations, together with several `.png` files used as the logo of the kernel. We can copy a suitable `.png` file to replace the `logo-64x64.png` file to be used as the logo of the kernel. `Though, my finding is probably due to the improper image size, sometimes the new logo would not work.`

<br />

## Issue-33: jupyterlab-rise color issue

**Solution:** By default, if Jupyterlab is using a light theme, the `jupyterlab-rise` works fine when generating the presentation. However, when switching to dark mode or using some dark mode theme of Jupyterlab, the presentation generated by `jupyterlab-rise` will show a dark background with black-color texts, making the text rather difficult to see. In this case, we can go to `Settings` from the menu, then click on `Settings Editor`, followed by clicing on the `JSON Settings Editor` on the top-left corner to go to the `Advanced Settings Editor`. Here, we can find `RISE` from the left-side list, and in the `User Preferences` editor on the right, we want to put in something like this,

```json
{
    "theme": "black"
}
```

This will select the `black` theme for `RISE`, which will show white color text.

> N.B. The background color of the presentation won't change as we change the theme selection for `RISE`, once we select the dark mode theme for Jupyterlab. Available themes for `RISE` can be found in Ref. [22].

<br />

## Issue-34 Scilab kernel installation

**Solution:** Scilab does not come with an installation script - the downloaded package contains executables under the `bin` directory and they can be executed directly inside the `scilab` directory. For the executables to be found from system-wise, we need to add the full path to those executables to the environment path. Or, we can copy all those binaries into, e.g., `/usr/bin`. If we follow the latter approach, not only we need to copy over those executables, but also we need to copy all the folders parallel to the `bin` directory, including `share`, `thirdparty` and `lib` into the same destination directory where `bin` lives. When installing the jupyter kernel, the command `pip install scilab_kernel` will install the kernel spec into the relevant location to which `pip` is being executed. For example, if we follow the instruction in Ref. [1] for setting up the jupyterhub, the pip path should be `/opt/jupyterhub/bin/pip`. In this case, the scilab kernel spec will be installed into `/opt/jupyterhub/lib/python3.10/site-packages/scilab_kernel`. If the kernel does not show up after the installation, we could consider copy over all contents in the `scilab-kernel` directory into `/opt/jupyterhub/share/jupyter/kernels/scilab` in which `scilab` is an arbitrary directory name and the directory needs to be created first before the copying. If the kernel fails to connect, we can check the error message by running `sudo journalctl -u jupyterhub` to inspect what is wrong. In my case, it seems that the kernel spec is always trying to look for scilab executables in the `/bin` directory. However, my scilab binaries were installed into `/usr/bin`. Therefore, in my case, I have to go into `/opt/jupyterhub/lib/python3.10/site-packages/scilab_kernel` and edit the `kernel.py` file to change the following line,

```python
executable = '/usr/bin/scilab-adv-cli'
```

which original was `executable = '/bin/scilab-adv-cli'`.

## Issue-35 Installation of `R` kernel

For installation of the latest version of `R` on Ubuntu, the instructions in Ref. [23] can be followed. It should be noted that `Secure APT` part there should be done first by running,

```bash
wget -qO- https://cloud.r-project.org/bin/linux/ubuntu/marutter_pubkey.asc | sudo tee -a /etc/apt/trusted.gpg.d/cran_ubuntu_key.asc
```

Then we can go back to the `Installation` part for installing `R`. Once installed, we can refer to the instructions in Ref. [16] for installation of jupyterlab kernel. While installing `IRkernel`, if it says some dependency package nees to be re-installed due to the version issue, we just use the same command, i.e., `install.packages('')` to install whatever missing packages. After that, when executing `IRkernel::installspec()`, we may have the issue that `jupyter` command cannot be found. This is because when following the instructions in Ref. [1] for setting up jupyterhub, the `jupyter`, etc. executables are installed in `/opt/jupyterhub/bin` directory which is not in the system environment path. So, we need to `sudo su` to change to the `root` user and edit the `.bashrc` file under the `root` home directory after which we want to do `source .bashrc` to make it take effect. Then getting back to `R` and execute `IRkernel::installspec()` should be fine.

## Issue-36 Installation of `Scala` kernel

**Solution:** The instructions in Ref. [24] can be followed to install the almond `Scala` kernel. One thing to notice is that the `--use-bootstrap` flag in the instruction no longer works, and I found removing the flag works just fine.

## Issue-37 Installation of `dot` kernel

**Solution:** First we need to install `graphiz` -- on Ubuntu, this cna be done via,

```bash
sudo apt install graphviz
```

Then we do,

```bash
/opt/jupyterhub/bin/pip install dot_kernel
```

In Ref. [25], there is an extra step to install the `dot` kernel spec so that Jupyterlab knows how to launch the kernel. However, I am not sure what the `install-dot-kernel` command there is supposed to be executed. In my case, I was manually creating the `dot` directory under `/opt/jupyterhub/share/jupyter/kernels` and put the following contents into the `kernel.json` file under the directory,

```json
{
    "argv": ["python", "-m", "dot_kernel", "-f", "{connection_file}"],
    "display_name": "dot"
}
```

## Issue-38 Installation of Jupyterlab templates

**Solution:** The instructions in Ref. [26] can be followed to install the Jupyterlab templates extension which is a really useful utility for storing some templates notebooks for a quick start. Basically, the steps would be,

```bash
/opt/jupyterhub/bin/pip install jupyterlab_templates
/opt/jupyterhub/bin/jupyter labextension install jupyterlab_templates
/opt/jupyterhub/bin/jupyter server extension enable --py jupyterlab_templates
```

During the second step, Jupyterlab would be rebuilt and therefore after all the three steps above, we have to restart the Jupyterlab service. In my case, I was following the instructions in Ref. [1] for the Jupyterhub setup and therefore I had to do,

```bash
sudo systemctl restart jupyterhub
```

After the installation, all templates will be stored in a dedicated location and by default it will be set to a location similar to below,

```
/opt/jupyterhub/lib/python3.12/site-packages/jupyterlab_templates/templates
```

We can then put our templates in there so they can be seen and loaded from the `Template` launcher in the Jupyterlab interface.

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

[17] [https://tex.stackexchange.com/questions/179778/xelatex-under-ubuntu](https://tex.stackexchange.com/questions/179778/xelatex-under-ubuntu)

[18] [https://stackoverflow.com/questions/49988636/multi-cursor-editing-in-jupyter-lab](https://stackoverflow.com/questions/49988636/multi-cursor-editing-in-jupyter-lab)

[19] [https://vatlab.github.io/sos-docs/running.html#content](https://vatlab.github.io/sos-docs/running.html#content)

[20] [https://pypi.org/project/jupyter-contrib-core/](https://pypi.org/project/jupyter-contrib-core/)

[21] [https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#gettingstartedcondalinux](https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#gettingstartedcondalinux)

[22] [https://revealjs.com/themes/](https://revealjs.com/themes/)

[23] [https://cran.r-project.org/bin/linux/ubuntu/fullREADME.html](https://cran.r-project.org/bin/linux/ubuntu/fullREADME.html)

[24] [https://almond.sh/docs/quick-start-install](https://almond.sh/docs/quick-start-install)

[25] [https://github.com/laixintao/jupyter-dot-kernel](https://github.com/laixintao/jupyter-dot-kernel)

[26] [https://pypi.org/project/jupyterlab-templates/](https://pypi.org/project/jupyterlab-templates/)
