---
layout: post
title: Resize Oracle VPS volume
subtitle:
tags: [web, VPS, oracle]
author: Yuanpeng Zhang
comments: true
use_math: true
---

When setting up Oracle cloud instance, we would give the instance an initial allocation of volume, and later on we
might find that the initial volume size is not enough as we install more and more services into our VPS. In such a
situation, we may want to expand the volume size. Here in this blog, I will note down the main processes we need to go
through for such a purpose.

1. First, we want to go to Ref. [1] for resizing the volume through the web interface, i.e., the Oracle Cloud web
console.

   > Here, we want to first make it clear what type of volume we want to expand. In my case, I was only dealing with
   the `boot` volume as I wanted to expand the volume where the operation system is installed.

   > Here follows is a recorded video demonstrating the initial process,

   <p align='center'>
      <video width="600" controls>
         <source src="/assets/img/posts/oracle_extend_boot_vol.mp4" type="video/mp4">
      </video>
   </p>

2. Next, we want to rescan the disk as stated in the step-6 in Ref. [1]. For this, we can refer to Ref. [2] for the
command to execute in our VPS terminal. It should be noticed that specifically on Ubuntu system, the command provided
there in the reference will not work. Instead, it should be like this,

   ```bash
   sudo dd iflag=direct if=/dev/<device_name> of=/dev/null count=1
   echo "1" | sudo tee /sys/block/<device_name>/device/rescan
   ```

   where `<device_name>` refers to the name of the device to rescan. To obtain the full list of available devices on
   our VPS, we can run the following command, `lsblk`. In my case, my device name is `sda`.

3. Then we should use the Oracle CLI tool `oci-growfs` to expand the volume. For some VPS instance on Oracle, such tools
are by default installed. For some others, like in my case, the Ubuntu system, we have to install the tools by ourselves.
For this, we can refer to the instructions in Ref. [3]. After the tools are installed, we can use the following command
to expand the volume [4],

   ```bash
   sudo /usr/libexec/oci-growfs -y
   ```

3-1. On Ubuntu, the `oci-growfs` tool may not be available, in which case we can follow the following steps for the volume extension,

   ```
   sudo fdisk -l /dev/sda
   sudo growpart /dev/sda 1  # Replace 3 with our won partition number
   sudo fdisk /dev/sda
   sudo resize2fs /dev/sda1  # or the appropriate partition
   ```

<br>

References
===

[1] [https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/resizingavolume.htm#OnlineResize](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/resizingavolume.htm#OnlineResize)

[2] [https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/rescanningdisk.htm](https://docs.oracle.com/en-us/iaas/Content/Block/Tasks/rescanningdisk.htm)

[3] [https://docs.oracle.com/en-us/iaas/Content/API/SDKDocs/cliinstall.htm#InstallingCLI__linux_and_unix](https://docs.oracle.com/en-us/iaas/Content/API/SDKDocs/cliinstall.htm#InstallingCLI__linux_and_unix)

[4] [https://docs.oracle.com/en-us/iaas/oracle-linux/oci-utils/index.htm#oci-growfs](https://docs.oracle.com/en-us/iaas/oracle-linux/oci-utils/index.htm#oci-growfs)