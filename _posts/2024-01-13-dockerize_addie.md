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

# Initial Docker Image Build

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
    apt install sshfs
    wget https://repo.anaconda.com/miniconda/Miniconda3-latest-Linux-x86_64.sh -P /tmp
    bash /tmp/Miniconda3-latest-Linux-x86_64.sh
    apt install python-wxtools
    wget https://github.com/AdvancedPhotonSource/GSAS-II-buildtools/releases/download/v1.0.1/gsas2full-Latest-Linux-x86_64.sh -P /tmp
    bash /tmp/gsas2full-Latest-Linux-x86_64.sh -b -p ~/g2full
    apt install redis-server
    ```

    <br>

    > `GSAS-II` requires quite a few compiled modules to run and those modules were pre-compiled against a certain Python version. However, for the reason to be mentioned down below, we have to stay with `Python=3.7` which is no longer working with the latest compiled `GSAS-II` modules. So, after we install the full `GSAS-II` package, we need to remove all those pre-compiled modules within the `/root/g2full/GSAS-II/GSASII-bin` directory and place the unzipped files from [this link](https://flv.iris-home.net/pkgs/bindist.zip) in there -- files in the link here were compiled some old version of Python which works just fine with `Python=3.7`.

    > `curl` and `python-wxtools` are needed for running GSASII. Without `python-wxtools`, running GSASII will complain about
    `wx` not found.

    > GSASII also needs `libgfortran.so.4` to run and usually the library file is not available in the startup image. We can
    download this file [here](/assets/files/libgfortran.so.4) and put it somewhere in the docker container (e.g., `/usr/lib64`)
    which we will use later on.

4. Clone the source code of ADDIE (assuming we are located at `/` in the docker container on the command line),

    ```bash
    git clone https://code.ornl.gov/general/tsitc.git
    git checkout -b docker_new remotes/origin/docker_new
    ```

    > We need the token for login. Go to `Settings` -> `Access Tokens` -> `Add new token`, and select proper
    permission, select the expiration date and then copy the generated token.

    > Also, we need to let the system memorize the git credentials so we don't to type in the access token every time we try to pull the updates. We can run `remotes/origin/docker_new`.

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
    cd
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

9. Finally, we need to copy over the `instance` folder (download the zip file [here](https://pv.iris-home.net/s/Q4NjAxM),
get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a> for access passcode) to the `/tsitc` directory.

    <!---
    One of the commonly used passwords was used here for `instance.zip` file.
    -->

    > With the recent update to include the LDAP authentication, there is another file `ldap_blueprint.py` (download it [here](https://pv.iris-home.net/s/I1MDY5O) -- again, get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a> for access passcode) under the
    `pdfitc` directory that needs to be copied over manually, as this file contains sensitive authentication information
    which should not be included in the git history.

    <!---
    One of the commonly used passwords was used here for `dap_blueprint.py` file.
    -->

    > Also with the LDAP implementation, we need to install the `flask-session`, `flask-wtf`, `pyldap`, and `pyoncat`
    modules,

    ```bash
    conda install conda-forge::flask-session
    conda install anaconda::flask-wtf
    pip install pyldap
    pip install https://oncat.ornl.gov/packages/pyoncat-1.5.1-py3-none-any.whl
    ```

    > When installing `pyldap` with `pip`, we might come across with errors relevant to the `gcc` failure, in which case
    the following command might be helpful [12],

    ```bash
    sudo apt install libsasl2-dev libldap2-dev libssl-dev
    ```

10. Install the `VESTA` and `data2config` software, by running,

    ```
    mkdir /Applications
    cd /Applications
    wget https://jp-minerals.org/vesta/archives/testing/VESTA-gtk3-x86_64.tar.bz2
    tar xvf VESTA-gtk3-x86_64.tar.bz2
    mv VESTA-gtk3-x86_64 VESTA-gtk3
    mkdir -p /Applications/RMCProfile_package_V6.7.9/exe
    cd /Applications/RMCProfile_package_V6.7.9/exe
    wget https://flv.iris-home.net/pkgs/data2config
    ```

11. Exit the docker container -- just execute `exit` from the command line.

12. Commit the container to a new image. First, we can check the running container(s) using the command,

    ```bash
    docker ps -a
    ```

    Identifying the container we were working on by its name and ID, then,

    ```bash
    docker commit [CONTAINER_ID] flask_addie_n
    ```

    Use the command `docker images` to check the new committed image in the docker image list.

    > The `flask_addie_n` here refers to the name of the image to be created by the commitment. It could
    be possible that an image with the same name already exists on the local host. In such a situation,
    the existing image with the same name as, e.g., `flask_addie_n`, will be renamed to `<none>` and the
    new `flask_addie_n` stays the latest.

    > <span style="color:red">***N.B.*** It is always a good practice not to do the commit so often since otherwise
    the docker image will have a lot of layers until hitting the limit whereby we cannot do the commit anymore.
    Instead, once we have done the initial commit, we can use the `git pull` command in the startup script
    (see step below) to update the source codes so that the web service inside the docker container can be
    updated.</span>

13. Prepare a `Dockerfile` file, as below,

    ```
    FROM flask_addie_n

    WORKDIR /tsitc

    COPY startup.sh /

    CMD ["/bin/bash", "-c", "/startup.sh"]
    ```

    where `flask_addie_n` is just the name of the new committed docker image in previous step. The name
    can be whatever we prefer to use and we can always use `flask_addie_n` as it will overwrite the existing
    image with the same name and the original one will be backed up to a `<none>` image.

14. In the same folder (now, we already existed the docker container and we are on the host machine) as the `Dockerfile`
file, we need to create a `startup.sh` file as below,

    ```
    #!/bin/bash

    source /root/miniconda3/etc/profile.d/conda.sh
    conda activate py37
    export LD_LIBRARY_PATH='/usr/lib64'

    sns_dir="/SNS"
    if findmnt | grep -q "${sns_dir}"; then
        echo "The directory ${sns_dir} is mounted."
    else
        echo "The directory ${sns_dir} is not mounted."
        sshfs sns:/SNS /SNS
    fi

    hfir_dir="/HFIR"
    if findmnt | grep -q "${hfir_dir}"; then
        echo "The directory ${hfir_dir} is mounted."
    else
        echo "The directory ${hfir_dir} is not mounted."
        sshfs sns:/HFIR /HFIR
    fi

    git pull
    gunicorn -c gunicorn_config.py run:app &
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

15. Build the docker image,

    ```bash
    docker image build -t flask_addie_n .
    ```

    > Again, the `flask_addie_n` here refers to the image to be created via the image building. Same as the comments
    above, if a local image of the same name already exists, the existing image will be renamed to `<none>`, with the
    new `flask_addie_n` staying the latest.

16. Fire up the container,

    ```bash
    docker run --privileged -v /home/cloud/.ssh:/root/.ssh/keys -p 5000:5000 -d flask_addie_n
    ```

    > In the demo here, we were mapping the local directory `/home/cloud/.ssh` to the `/root/.ssh/keys` directory inside the docker container. In general situation, we can for sure adjust the location on both sides, but we will take this as is in current documentation.

17. The Flask server should be now accessible from the host machine, at `localhost:5000`.

18. Now, we want to go into the running container in the interactive mode and set up the passwordless connection to the `Analysis` cluster,

    ```
    docker exec -it [CONTAINER_ID] /bin/bash
    cd ~/.ssh
    cp keys/* .
    chown root:root config
    ```

19. Push the local docker image to the Docker Hub,

    ```
    docker login --username=apw247
    docker tag bb38976d03cf apw247/flask_addie_n:latest
    docker push apw247/flask_addie_n
    ```

    where `bb38976d03cf` is the docker image ID which we can obtain via the command,

    ```
    docker images
    ```

    to see all the existing images on our host machine and we can identify the associated
    image ID with the `flask_addie_n` image.

    > For security purpose, the remote docker repository is made private. To contribute to the repo,
    please get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a> to request access.

# Deployment

1. First, log in the remote VPS and run,

    ```bash
    docker run --privileged -v /home/cloud/.ssh:/root/.ssh/keys -p 5000:5000 -d apw247/flask_addie_n
    ```

    to start up the server and then we can configure `nginx` to redirect the traffic on the port 443 to the local 5000 port.

2. The ADDIE service is hosted on an ORNL research cloud instance -- this is the ORNL internal cloud computation resource. For security concerns, ORNL needs to perform systematic scanning over the server and the ADDIE service. On the system side, those vulnerabilities can be easily patched according to the instructions provided by the cyber security team. On the `nginx` side, we need to add specific headers for mitigating the potential vulnerabilities and it turns out that not only we need to patch for the `nginx` configuration, but also we need to add in some security header into the Flask app. Here is the saved `nginx` configuration, [Click Me](https://pf.iris-home.net/yuanpeng/4b777e0fd8df413babd544f05fac427f/raw/HEAD/nginx.conf), and here is the chunk of codes in the Flask app,

    ```python
    # Define a function to set X-Frame-Options header
    def add_security_headers(response):
        response.headers['X-Frame-Options'] = 'DENY'
        response.headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload;'
        return response


    # Register the function as an after_request handler
    app.after_request(add_security_headers)
    ```

3. There are some other security patches that will be needed in the Flask app, to mitigate risks such as Cross-Site Request Forgery (CSRF), rigorous input field checking, etc. We can refer to the source codes for ADDIE (ORNL internal access only, not open source yet) here, [Click Me](https://code.ornl.gov/general/tsitc).

4. Another domain `addie-dev.ornl.gov` has been made available to point to the local `6000` port. So, to fire up a test service of ADDIE, we can execute the following command first,

    ```bash
    sudo docker run --privileged -v /home/cloud/.ssh:/root/.ssh/keys -it -p 6000:5000 apw247/flask_addie_n bash
    ```

    <br>

    > The `--privileged` flag is to ensure that remote drives can be mounted using `sshfs` within the docker container.

    Then, within the docker container, run the following commands to start up the server,

    ```bash
    sshfs sns:/SNS /SNS
    sshfs sns:/HFIR /HFIR
    conda activate py37
    gunicorn -c gunicorn_config.py run:app &
    redis-server --port 6379 &
    celery -A pdfitc.app.celery worker --loglevel=info &
    ```

    As the dev server is up running, we can open another terminal to execute,

    ```
    sudo docker exec -it [CONTAINER_ID] /bin/bash
    ```

    where `[CONTAINER_ID]` refers to the ID of the running docker container started in previous step (use `sudo docker ps -a` to see all the running containers). Within this interactive shell, we can change the code and it will be directly reflected onto the `addie-dev.ornl.gov` server -- we may have to kill the `gunicorn` job from another shell and restart it if it is the Python source codes that were changed. The changes in template HTML files will be directly reflected without restarting the server, though.

5. While the docker container is running, we can lanuch an interactive shell in the running container like this,

    ```bash
    sudo docker exec -it CONTAINER_ID /bin/bash
    ```

    where `CONTAINER_ID` is the ID of the running container, which can be obtained via running `sudo docker ps -a`.

6. Sometimes during the docker image commit and building process, we could have multiple repositories attached a single image ID. In this case, if we want to remove certain tags, we can do,

    ```bash
    sudo docker rmi REPO_NAME
    ```

    where `REPO_NAME` refers to the repository name associated with a certain image. It can be obtained via running `sudo docker images` and usually the first column would be the repository name.

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

[12] [https://stackoverflow.com/questions/4768446/i-cant-install-python-ldap](https://stackoverflow.com/questions/4768446/i-cant-install-python-ldap)