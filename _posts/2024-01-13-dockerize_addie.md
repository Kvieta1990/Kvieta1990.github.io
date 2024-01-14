---
layout: post
title: Dockerize the ADDIE service
subtitle:
tags: [web, docker, scattering]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/addie_logo_ornl.png"
   style="border:none;"
   width="600"
   alt="addie"
   title="addie" />
</p>

<br />

As conventionally, the ADDIE (ADvanced DIffraction Environment, available at [addie.ornl.gov](addie.ornl.gov)) service is deployed on a
VPS (specifically, an instance on the ORNL hosted research cloud). The service itself and all
the necessary configurations are therefore locked to the VPS machine, making it extremely painful
for transfer and maintenance. Especially for the maintenance, anytime we need to install a new
functionality into the ADDIE service, unavoidably we would need some new dependencies. However, the
existing modules and libraries will potentially be conflicting with the module we are trying to
install and quite often we need to do the homework for testing and find out the compatible versions
of all the modules (existing ones and those to be installed). Doing such on a VPS locally is rather
risky as once the installed new module breaks some other modules, it is very difficult, or sometimes
even impossible, to roll back. So, from the long run, we need to make the service containerized, using,
e.g., docker so the whole service is self-contained and more importantly, the testing will become
way easier. We just need to pull the docker image, fire up a container and perform the installation
and test interactively. Once finished, we can then push the image to a new version and deploy it on
the VPS via docker. This blog will keep a record of the preparation of the docker image for ADDIE.
The procedure may not be the optimal one, as the finally prepared docker image is with the size of
~10 Gb. But at least, it will give us a working version of the docker image, with which we can then
easily fire up a ADDIE service instance on any supported VPS.

> **N.B.** The reason why the docker image is large is that we include all the required modules and
configurations in the image. We could probably do the installation and configuration via docker
entrypoint or startup script. But in this case, I will not go through that route, and in my case,
the image is huge, but the startup script is very simple.

1. Pull a startup docker image, e.g.,

    ```bash
    docker pull ubuntu
    ```

2. Run the pulled docker image interactively,

    ```bash
    docker run -i -t ubuntu bash
    ```

where the `ubuntu` refers to the pulled docker image name.

3. Within the interactive docker container, install all the necessary packages,

    ```bash
    apt install git
    apt install gfortran
    apt install build-essential
    apt install vim
    apt install wget
    apt install curl
    wget https://repo.anaconda.com/miniconda/Miniconda3-latest-Linux-x86_64.sh -P /tmp & bash /tmp/Miniconda3-latest-Linux-x86_64.sh
    apt install python-wxtools
    curl https://subversion.xray.aps.anl.gov/admin_pyGSAS/downloads/gsas2full-Latest-Linux-x86_64.sh > /tmp/gsas2full-Latest-Linux-x86_64.sh
    bash /tmp/gsas2full-Latest-Linux-x86_64.sh -b -p ~/g2full
    apt install redis-server
    ```

    > `curl` and `python-wxtools` are needed for running GSASII. Without `python-wxtools`, running GSASII will complain about
    `wx` not found.

    > GSASII also needs `libgfortran.so.4` to run and usually the library file is not available in the startup image. We can
    download this file [here](/assets/files/libgfortran.so.4) and put it somewhere in the docker container (e.g., `/usr/lib64`)
    which we will use later on.

4. Clone the source code of ADDIE (assuming we are located at `/` in the docker container on the command line),

    ```bash
    git clone https://code.ornl.gov/general/tsitc.git
    ```

    > We need the token for login. Go to `Settings` -> `Access Tokens` -> `Add new token`, and select proper
    permission, select the expiration date and then copy the generated token.

5. Create a conda environment,

    ```bash
    conda create -n py37 python=3.7
    conda activate py37
    ```

    > The `Diffpy-CMI` module is only compatible with Python=3.7, so we have to stay with it for
    the moment, even the Python=3.7 has been dropped for support.

6. Go into the `tsitc` directory and install all the required python modules,

    ```bash
    pip install -r requirements.txt
    ```

7. Install `Diffpy-CMI`,

    ```bash
    conda config --add channels diffpy
    conda install diffpy-cmi
    ```

8. Install `strumining`,

    ```bash
    git clone https://code.ornl.gov/ly0/strumining.git
    cd strumining
    pip install pymatgen
    pip install pybtex
    pip install PyYAML
    python setup.py install
    ```

    > After the installation, we need to manually copy over two folders (`data` and `utils`) in the `strumining` source code
    tree to the corresponding location in the conda environment. In my case, the destination is,

    ```
    /root/miniconda3/envs/py37/lib/python3.7/site-packages/strumining-1.0.0-py3.7.egg/strumining
    ```

    > The specific location will depend on the conda installation location and the conda environment we created previously.

9. Finally, we need to copy over the `instance` folder (download the zip file [here](https://kd.iris-home.net/#s/-CMXrVqA),
get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a> for access passcode) to the `/tsitc` directory.

10. Going through all the procedures as detailed above, the container should be ready to be committed to a new image
with which we can then fire up an ADDIE service.

11. Exit the docker container -- just execute `exit` from the command line.

12. Commit the container to a new image. First, we can check the running container(s) using the command,

    ```bash
    docker ps -a
    ```

    Identifying the container we were working on by its name and ID, then,

    ```bash
    docker commit [CONTAINER_ID] flask_addie
    ```

    Use the command `docker images` to check the new committed image in the docker image list.

12. Prepare a `Dockerfile` file, as below,

    ```
    FROM flask_addie

    WORKDIR /tsitc

    COPY startup.sh /

    CMD ["/bin/bash", "-c", "/startup.sh"]
    ```

    where `flask_addie` is just the name of the new committed docker image in previous step. The name
    can be whatever we prefer to use. To avoid confusion, we can choose a new name anytime we commit the
    new image from a container -- I am not sure whether committing a container to an already existing image
    will cause any issues.

13. In the same folder (now, we already existed the docker container and we are on the host machine) as the `Dockerfile`
file, we need to create a `startup.sh` file as below,

    ```
    #!/bin/bash

    source /root/miniconda3/etc/profile.d/conda.sh
    conda activate py37
    export LD_LIBRARY_PATH='/usr/lib64'
    gunicorn -w 3 -b :5000 run:app &
    redis-server --port 6379 &
    celery -A pdfitc.app.celery worker --loglevel=info
    ```

    > **N.B.** The startup script will be run when launching the docker image and it will be running in the linux environment.
    So, if we were preparing the `startup.sh` file on Windows, the file ending will cause some issues, in which case, we may
    need to edit the file using special solutions, e.g., in WSL linux environment on Windows.

    > The `export` command is for exporting the library system path so that the GSASII program can find the
    `libgfortran.so.4` that we previously put in the `/usr/lib64` directory.

    > In the last line of the startup script, we don't need the `&` sign so that the process will be running as the
    for-ground process without releasing the process. If we put an `&` sign to the end of the command, docker will
    exit immediately after running the last command since he thinks that he has gone over all the processes and will
    exit without worrying about those jobs running in the background.

14. Build the docker image,

    ```powershell
    docker image build -t flask_addie .
    ```

15. Fire up the container,

    ```powershell
    docker run -p 5000:5000 -d flask_addie
    ```

16. The Flask server should be now accessible from the host machine, at `localhost:5000`.

17. Push the local docker image to the Docker Hub,

    ```
    docker login --username=apw247
    docker tag bb38976d03cf apw247/flask_addie:latest
    docker push apw247/flask_addie
    ```

    where `bb38976d03cf` is the docker image ID which we can obtain via the command,

    ```
    docker images
    ```

    to see all the existing images on our host machine and we can identify the associated
    image ID with the `flask_addie` image.

References
===

[1] [https://blog.logrocket.com/build-deploy-flask-app-using-docker/](https://blog.logrocket.com/build-deploy-flask-app-using-docker/)

[2] [https://www.baeldung.com/linux/docker-cmd-multiple-commands](https://www.baeldung.com/linux/docker-cmd-multiple-commands)

[3] [https://stackoverflow.com/questions/34549859/run-a-script-in-dockerfile](https://stackoverflow.com/questions/34549859/run-a-script-in-dockerfile)

[4] [https://subversion.xray.aps.anl.gov/trac/pyGSAS/wiki/InstallLinux](https://subversion.xray.aps.anl.gov/trac/pyGSAS/wiki/InstallLinux)

[5] [https://www.letscloud.io/community/how-to-launch-a-docker-container-with-an-interactive-shell](https://www.letscloud.io/community/how-to-launch-a-docker-container-with-an-interactive-shell)

[6] [https://phoenixnap.com/kb/install-gcc-ubuntu](https://phoenixnap.com/kb/install-gcc-ubuntu)

[7] [https://stackoverflow.com/questions/8609666/python-importerror-no-module-named-wx](https://stackoverflow.com/questions/8609666/python-importerror-no-module-named-wx)

[8] [https://www.diffpy.org/products/diffpycmi/index.html](https://www.diffpy.org/products/diffpycmi/index.html)

[9] [https://jsta.github.io/r-docker-tutorial/04-Dockerhub.html](https://jsta.github.io/r-docker-tutorial/04-Dockerhub.html)

[10] [https://askubuntu.com/questions/1218048/activating-conda-environment-in-within-a-shell-script](https://askubuntu.com/questions/1218048/activating-conda-environment-in-within-a-shell-script)

[11] [https://phoenixnap.com/kb/install-gcc-ubuntu](https://phoenixnap.com/kb/install-gcc-ubuntu)