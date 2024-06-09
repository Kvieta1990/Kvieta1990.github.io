---
layout: post
title: Notes on slurm server setup
subtitle: Tips & Tricks
tags: [slurm, server]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
This post is a collection of notes of tips and tricks when trying to set up slurm server on Ubuntu 20.04 focal.
</p>

<p align='center'>
<img src="/assets/img/posts/slurm.png"
   style="border:none;"
   width="500"
   alt="slurm"
   title="slurm" />
</p>

1. First, we set up `munge`, which can be installed on Ubuntu via,

    ```bash
    sudo apt install munge
    ```
2. Install `slurmd` and `slurmctld`,

    ```bash
    sudo apt install slurm-wlm
    ```

    > If the installation fails complaining about `error "adduser: The UID 64030 is already in use"`, the following commands may be helpful,

    ```bash
    sudo apt install libuser
    sudo luseradd -r --shell=/bin/false -M --uid=64030 slurm
    ```

3. Write the following contents into `/etc/slurm/slurm.conf`,

    ```
    ControlMachine=iris2
    AuthType=auth/munge
    CryptoType=crypto/munge
    MpiDefault=none
    ProctrackType=proctrack/linuxproc
    ReturnToService=2
    SlurmctldPidFile=/run/slurm/slurmctld.pid
    SlurmctldPort=6817
    SlurmdPidFile=/run/slurm/slurmd.pid
    SlurmdPort=6818
    SlurmdSpoolDir=/var/lib/slurm-llnl/slurmd
    SlurmUser=slurm
    StateSaveLocation=/var/lib/slurm-llnl/slurmctld
    SwitchType=switch/none
    TaskPlugin=task/none
    InactiveLimit=0
    KillWait=30
    MinJobAge=300
    SlurmctldTimeout=120
    SlurmdTimeout=300
    Waittime=0
    SchedulerType=sched/backfill
    SelectType=select/cons_res
    SelectTypeParameters=CR_Core
    AccountingStorageType=accounting_storage/none
    AccountingStoreFlags=job_comment
    ClusterName=cluster
    JobCompType=jobcomp/none
    JobAcctGatherFrequency=30
    JobAcctGatherType=jobacct_gather/none
    SlurmctldDebug=3
    SlurmdDebug=3
    NodeName=iris2 CPUs=32 Boards=1 SocketsPerBoard=32 CoresPerSocket=1 ThreadsPerCore=1 RealMemory=64294
    PartitionName=batch Nodes=iris2 Default=YES MaxTime=INFINITE State=UP
    ```

    > The host name `iris2` (occurring in multiple locations) in the above quoted configuration should be replaced by whatever the host name of the server is, which can be obtained by running `hostname` on the terminal.

    > The second line from the bottom should also be replaced by relevant info to the server. The info can be obtained by running `slurmd -C` in the terminal. The `slurmd` command will become available after step-2.

4. Start `slurmd` and `slurmctld` services by running,

    ```bash
    sudo service slurmd start
    sudo service slurmctld start
    ```

    respectively, and check their status, via,

    ```bash
    sudo service slurmd status
    sudo service slurmctld status
    ```

    Quite often, we may have some issues with the PID files for either `slurmd`, or `slurmctld`, or both. Using the configuration file above, we may need to create the file `/etc/tmpfiles.d/slurmd.conf` and put the following line into the file,

    ```
    d /run/slurm 0770 root slurm -
    ```

    which can guarantee that the `slurm` can be created with the right permission after each rebooting. Also, we need to create the directories `/var/lib/slurm-llnl/slurmd` and `/var/lib/slurm-llnl/slurmctld` and give them the right owner (i.e., `slurm`), via,

    ```bash
    sudo mkdir -p /var/lib/slurm-llnl/slurmd
    sudo mkdir -p /var/lib/slurm-llnl/slurmctld
    sudo chown slurm:slurm /var/lib/slurm-llnl/slurmd
    sudo chown slurm:slurm /var/lib/slurm-llnl/slurmctld
    ```

    > If running `sudo service slurmd start` and `sudo service slurmctld start` fails due to that the port `6818` and/or `6817` is being used, we can run the following command to kill anything running on the relevant port, e.g.,

    ```
    sudo kill -9 `sudo lsof -t -i:6818`
    ```

<br />

<p style='text-align: justify'>
Sometimes, when trying to submit jobs after the server is up running, it may show that the computing node is `drained`. To undrain it, we may need to follow the instruction in Ref. [1]. Also, sometimes we may see the log of the server is complaining about not being able to fine the PID file. This should not matter that much in terms of server running and jobs management.
</p>

<br />

<b>References</b>

[1] [https://stackoverflow.com/questions/29535118/how-to-undrain-slurm-nodes-in-drain-state](https://stackoverflow.com/questions/29535118/how-to-undrain-slurm-nodes-in-drain-state)