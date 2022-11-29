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

<p style='text-align: justify'>
The instructions given in Ref. [1] has been followed to set up the single-node slurm service on Ubuntu. Several things are noteworthy, as below,
</p>

0. Before following the instructions for the setup, we need both `slurm` and `munge` user available on the server. To set up those users, if they are not already existing, we need to follow the instruction in Ref. [2].

1. We can ignore step 5-8 in Ref. [1], and instead we can just use the quoted slurm configuration file below as a minimal working version,

   ```
   ControlMachine=orc-iris
   AuthType=auth/munge
   CryptoType=crypto/munge
   MpiDefault=none
   ProctrackType=proctrack/linuxproc
   ReturnToService=2
   SlurmctldPidFile=/run/slurm-llnl/slurmctld.pid
   SlurmctldPort=6817
   SlurmdPidFile=/run/slurm-llnl/slurmd.pid
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
   AccountingStoreJobComment=YES
   ClusterName=cluster
   JobCompType=jobcomp/none
   JobAcctGatherFrequency=30
   JobAcctGatherType=jobacct_gather/none
   SlurmctldDebug=3
   SlurmdDebug=3
   NodeName=orc-iris CPUs=64 Boards=1 SocketsPerBoard=64 CoresPerSocket=1 ThreadsPerCore=1 RealMemory=54306
   PartitionName=batch Nodes=orc-iris Default=YES MaxTime=INFINITE State=UP
   ```

2. The configuration file above should be saved as `/etc/slurm-llnl/slurm.conf`, but not the one mentioned in step-9 in Ref. [1].

3. The host name `orc-iris` (occurring in multiple locations) in the above quoted configuration should be replaced by whatever the host name of the server is, which can be obtained by running `hostname` in the terminal.

4. The second line from the bottom should also be replaced by relevant info to the server. The info can be obtained by running `slurmd -C` in the terminal. The `slurmd` command will become available after running step 1-4 in Ref. [1]. Also, attention to the note in Ref. [2] about the reduction of `RealMemory` value.

<p style='text-align: justify'>
After the configurations above, when running `sudo service slurmd start` and `sudo service slurmctld start` to start the service, sometimes they may fail to start. The reason could be multifold, but specifically in my case, it was due to that the port `6818` and/or `6817` is being used. In that case, we want to run the following command to kill anything running on the relevant port, e.g., 
</p>

```
sudo kill -9 `sudo lsof -t -i:6818`
```

<br />

<p style='text-align: justify'>
Sometimes, when trying to submit jobs after the server is up running, it may show that the computing node is `drained`. To undrain it, we may need to follow the instruction in Ref. [3]. Also, sometimes we may see the log of the server is complaining about not being able to fine the PID file. This should not matter that much in terms of server running and jobs management.
</p>

<br />

<b>References</b>

[1] [https://signac.io/development/2020/06/26/local-SLURM-environment.html](https://signac.io/development/2020/06/26/local-SLURM-environment.html)

[2] [https://docs.siliconcompiler.com/en/latest/tutorials/slurmsetup.html](https://docs.siliconcompiler.com/en/latest/tutorials/slurmsetup.html)

[3] [https://stackoverflow.com/questions/29535118/how-to-undrain-slurm-nodes-in-drain-state](https://stackoverflow.com/questions/29535118/how-to-undrain-slurm-nodes-in-drain-state)