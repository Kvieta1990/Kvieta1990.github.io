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

    > With the recent update to include the LDAP authentication, there is another file `ldap_blueprint.py` under the
    `pdfitc` directory that needs to be copied over manually, as this file contains sensitive authentication information
    which should not be included in the git history.

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

10. Going through all the procedures as detailed above, the container should be ready to be committed to a new image
with which we can then fire up an ADDIE service.

11. Exit the docker container -- just execute `exit` from the command line.

    > After exiting the container, if we want to run the container again in the interactive mode, use the following command,

    ```bash
    docker exec -it [CCONTAINER_NAME] bash
    ```

12. Commit the container to a new image. First, we can check the running container(s) using the command,

    ```bash
    docker ps -a
    ```

    Identifying the container we were working on by its name and ID, then,

    ```bash
    docker commit [CONTAINER_ID] flask_addie
    ```

    Use the command `docker images` to check the new committed image in the docker image list.

    > The `flask_addie` here refers to the name of the image to be created by the commitment. It could
    be possible that an image with the same name already exists on the local host. In such a situation,
    the existing image with the same name as, e.g., `flask_addie`, will be renamed to `<none>` and the
    new `flask_addie` stays the latest.

    > <span style="color:red">***N.B.*** It is always a good practice not to do the commit so often since otherwise
    the docker image will have a lot of layers until hitting the limit whereby we cannot do the commit anymore.
    Instead, once we have done the initial commit, we can use the `git pull` command in the startup script
    (see step below) to update the source codes so that the web service inside the docker container can be
    updated.</span>

13. Prepare a `Dockerfile` file, as below,

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

14. In the same folder (now, we already existed the docker container and we are on the host machine) as the `Dockerfile`
file, we need to create a `startup.sh` file as below,

    ```
    #!/bin/bash

    source /root/miniconda3/etc/profile.d/conda.sh
    conda activate py37
    export LD_LIBRARY_PATH='/usr/lib64'

    git pull
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

15. Build the docker image,

    ```powershell
    docker image build -t flask_addie .
    ```

    > Again, the `flask_addie` here refers to the image to be created via the image building. Same as the comments
    above, if a local image of the same name already exists, the existing image will be renamed to `<none>`, with the
    new `flask_addie` staying the latest.

16. Fire up the container,

    ```powershell
    docker run -p 5000:5000 -d flask_addie
    ```

17. The Flask server should be now accessible from the host machine, at `localhost:5000`.

18. Push the local docker image to the Docker Hub,

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

    > For security purpose, the remote docker repository is made private. To contribute to the repo,
    please get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a> to request access.

# Development

Following the procedures above, we now have the docker image to start with. For further development,
we need a) new codes, b) local testing, c) making new docker image, and d) deployment. In this section,
we will focus the local testing, assuming that we have put in our new codes. The source codes for ADDIE
is hosted on ORNL gitlab server, [https://code.ornl.gov/general/tsitc](https://code.ornl.gov/general/tsitc).
The `master` branch corresponds to the current deployed production version. The `docker` branch is specifically
for the the docker version of the service, as the GSASII installation location is different in the docker image
and the server where the current service is deployed. The `docker_dev` branch is for the development and testing
of the docker version. Here follows are given the detailed steps to do the local testing,

1. Commit and push the new codes to be tested to the `docker_dev` branch.

    > The source code is also protected, so, to contribute, get in touch with <a href="mailto:zyroc1990@gmail.com">Yuanpeng<a>.

2. Pull the docker image, check local images and run the image as a container interactively,

    ```bash
    docker pull apw247/flask_addie:latest
    docker images
    docker run -i -t apw247/flask_addie bash
    ```

3. Within the docker container, change directory to the source code repo of ADDIE, pull the codes and exit the container,

    ```bash
    cd /tsitc
    git checkout docker_dev
    git pull
    exit
    ```

    > There should be a better way to build the docker image to include such steps in the startup running script. But
    I will stay with the slightly tedious steps here, which is actually not too tedious.

    > To prevent the popup request for username and the token any time when running the git push or pull command, we could
    run the following command (before running `git push` or `git pull`) to remember the passcode for git,

    ```bash
    git config --global credential.helper store
    ```

4. Check the running container, identify the one we were just working with, and commit the container to a new image,

    ```bash
    docker commit [CONTAINER_ID] flask_addie
    ```
5. On the local machine, change directory to the `tsitc` repo and further into the `docker` directory inside the repo --
clone the source repo if not yet cloned.

6. Follow the steps 13-15 in previous section to build the new image and run the service locally for testing. 

# Deployment

1. For deployment, the initial steps are quite similar to the development procedures as detailed in the `Development` section above.
The only difference is that we need to check out the `master` branch instead of the `docker_dev` one. After all the initial steps,
we need to push the new docker image to Docker Hub, following the instruction as presented in step-18 in the first section.

2. Finally, on the remote VPS, we need to run,

    ```bash
    docker run -p 5000:5000 -d flask_addie
    ```

    to start up the server and then we can configure `nginx` to redirect the traffic on the port 443 to the local 5000 port.

3. The ADDIE service is hosted on an ORNL research cloud instance -- this is the ORNL internal cloud computation resource. For security concerns, ORNL needs to perform systematic scanning over the server and the ADDIE service. On the system side, those vulnerabilities can be easily patched according to the instructions provided by the cyber security team. On the `nginx` side, we need to add specific headers for mitigating the potential vulnerabilities and it turns out that not only we need to patch for the `nginx` configuration, but also we need to add in some security header into the Flask app. Here is the saved `nginx` configuration, [Click Me](https://pf.iris-home.net/yuanpeng/4b777e0fd8df413babd544f05fac427f/raw/HEAD/nginx.conf), and here is the chunk of codes in the Flask app,

    ```python
    # Define a function to set X-Frame-Options header
    def add_security_headers(response):
        response.headers['X-Frame-Options'] = 'DENY'
        response.headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload;'
        return response


    # Register the function as an after_request handler
    app.after_request(add_security_headers)
    ```

4. There are some other security patches that will be needed in the Flask app, to mitigate risks such as Cross-Site Request Forgery (CSRF), rigorous input field checking, etc. We can refer to the source codes for ADDIE (ORNL internal access only, not open source yet) here, [Click Me](https://code.ornl.gov/general/tsitc).

5. Another domain `addie-dev.ornl.gov` has been made available to point to the local `6000` port. So, to fire up a test service of ADDIE, we can execute the following command first,

    ```bash
    sudo docker run --privileged -v /home/cloud/.ssh:/root/.ssh/keys -it -p 6000:5000 apw247/flask_addie bash
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

6. The production version of the server can be fired up using the following command,

    ```bash
    sudo docker run --privileged -v /home/cloud/.ssh:/root/.ssh/keys -p 5000:5000 -d apw247/flask_addie:latest
    ```

7. While the production docker container is running, we can lanuch an interactive shell in the running container like this,

    ```bash
    sudo docker exec -it CONTAINER_ID /bin/bash
    ```

    where `CONTAINER_ID` is the ID of the running container, which can be obtained via running `sudo docker ps -a`.

8. Sometimes during the docker image commit and building process, we could have multiple repositories attached a single image ID. In this case, if we want to remove certain tags, we can do,

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