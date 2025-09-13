---
layout: post
title: Configure a Local Mantid Live Reduction Service
tags: [scattering, dev, technical, data_proc]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Here in this post, I am writing down notes regarding the configuration of a Mantid live reduction service locally. By 'locally', I mean the service can be managed by a local user, instead a system-wise one for which the management would require a `sudo` privilege. Relevant scripts are stored in the following repo, [https://code.ornl.gov/y8z/nom_livered_local](https://code.ornl.gov/y8z/nom_livered_local). Since some of the scripts involved here contain sensitive information, the repo is kept as a private script. To get access to the repo, please reach out to me at [https://iris2020.net/contact](https://iris2020.net/contact/).

First, we need to create a service file, e.g., `livereduce_zyp.service` and put it under `${HOME}/.config/systemd/user/`. This way, the created service can be executed and managed by the local user without the need for  a `sudo` privilege. Here below is the contents to be populated into the service file,

```
[Unit]
Description=Live processing service
StartLimitInterval=8640
StartLimitBurst=1000

[Service]
WorkingDirectory=/SNS/users/y8z/Utilities/nom_live
ExecStart=/SNS/users/y8z/Utilities/nom_live/livereduce_zyp.sh
Restart=always
RestartSec=10

[Install]
WantedBy=default.target
```

Once the service file created in place (it has to be exactly under the directory mentioned above), run the following command to reload the daemon,

```bash
systemctl --user daemon-reload
```

where `--user` flag is telling `systemctl` to run as the local user. After this, the local service can be executed and managed by the local user, such as,

```bash
systemctl --user start livereduce_zyp.service
systemctl --user stop livereduce_zyp.service
systemctl --user restart livereduce_zyp.service
systemctl --user status livereduce_zyp.service
```

In the service file template above, the working directory is set to `/SNS/users/y8z/Utilities/nom_live` and we do want to put all the relevant scripts regarding the service running under the working directory. The main script is given in the service file, i.e., `livereduce_zyp.sh`, which further calls a Python script `livereduce_zyp.py` which should be placed under the working directory. The service running will also require a configuration file `livereduce_zyp.conf` (the name of which can be changed in the Python script `livereduce_zyp.py`). The configuration file defines the instrument, the Mantid environment to use, the system memory limit, and live reduction stream interval. Depending on the specific codes in the Python script `livereduce_zyp.py`, the actual Python scripts for the data processing and post-processing may be different. For my version here, the processing and post-processing scripts are `/SNS/NOM/shared/livereduce/reduce_NOM_live_proc_zyp.py` and `/SNS/NOM/shared/livereduce/reduce_NOM_live_post_proc_zyp.py`, respectively.

> For the live reduction, the setup is to use the processing script for processing the live stream data and the post-processing script is responsible for accumulating the processed data so that we can keep accumulating the processing history.

At the end of the `post-processing`, the script (again, `/SNS/NOM/shared/livereduce/reduce_NOM_live_post_proc_zyp.py` in my case), will post the resulted workspace to the data monitor ([monitor.sns.gov](https://monitor.sns.gov)). This will require the authentication information stored locally. In my case, the `post-processing` script imports a local Python module (see `scripts/utilities/finddata/publish_plot.py` in  the repo) which imports the authentication information from a local configuration file (see `post-processing.conf` in the repo). The live reduction log file, with my configuration, will be saved to the working directory as `livereduce.log` (the working directory is defined in the service file above).

The following files mentioned in current post can all be found in the repo,

```
livereduce_zyp.sh
livereduce_zyp.py
livereduce_zyp.conf
post-processing.conf
scripts/reduce_NOM_live_proc_zyp.py
scripts/reduce_NOM_live_post_proc_zyp.py
scripts/utilities/finddata/publish_plot.py
```

The live reduction scrip is using the Mantid `StartLiveData` [1] framework. With my current configuration, the `StartLiveData` service is configured with `FromNow=False` and `FromStartOfRun=True`. This means, the live reduction will `record live data, but go back to the start of the run and process all data since then`. For example, during the data collection, if the live reduction service fails for some reason and the service gets restarted, the service will go back to the start of the run and process all data since then so that we don't have the worry of losing data in case of live reduction service failure. If no failure, the live service will keep processing each streamed chunk (by default, every 30 s forms a chunk, which can be changed in the `livereduce_zyp.conf` configuration file) and accumulating the chunks. Also, it is worth mentioning that the actual length of the chunk is the larger one between the defined value (e.g., 30 s) and the actual processing time of the previous chunk. For example, if the processing of the previous chunk only takes 20 s, the next chunk to be processed will be the data streamed in 30 s right after the previous chunk. However, if the processing of the previous chunk takes 40 s, the data streamed in 40 s right after the previous chunk will be taken as the next chunk to process.

---

<span style="display:block; text-align:center;">New Notes on 09/13/2025</span>

---

In some cases, we may have multiple machines mounting the same drive to the same path and therefore the user level services configuration file will be seen on all those machines. In such a situation, the services will be running on all the machines which may cause some issues in many potentially different ways (file writing conflicts, etc.). We do want to set a flag in the configuration file to let the service only run on a certain machine and to do that, we want to put in a conditional check in the configuration file like follows,

```
[Unit]
Description=My Unique Service
ConditionHost=my-specific-hostname
...
...
```

Then the `My unique Service` will be only running on the machine with the hostname of `my-specific-hostname`. To obtain the hostname of a machine, just run `hostname` from the terminal.

References
===

[1] [https://docs.mantidproject.org/nightly/algorithms/StartLiveData-v1.html](https://docs.mantidproject.org/nightly/algorithms/StartLiveData-v1.html)