---
layout: post
title: Dify Server Maintenance
subtitle:
tags: [gpt, LLM, web, docker]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/dify.png"
   style="border:none;"
   width="800"
   alt="abs_geo"
   title="abs_geo" />
</p>

In a previous post ([here](https://iris2020.net/2024-01-27-docker_dify/)), I was going through the process of setting up the Dify server with docker. To remind ourselves, Dify is a web-based service with which we can set up own AI agent and workflows, through the APIs of various LLM models. Here in this post, I will put down some maitenance and configuration notes for Dify.

{: .box-note}
<a id="p1"></a>
<a href="#p1"><b>#1</b></a>Dify upgrade

With `docker`, the upgrade for Dify server is straightforward, by following the instructions in Ref. [1]. The critical step here is to make backups for the docker compose file, the `.env` file and the volume directory (which contains all the setups and agents we had in the Dify platform). After we make sure the new version of the server is working fine, we can remove the big volume backup files or save it somewhere safe for future check purposes. Sometimes, we may have some specific configurations that we put into either the docker compose file or the `.env` file. In that case, we want to copy over those information into the new version of the relevant files. At this stage, doing a `diff` comparison between the old and new version of the docker compose and `.env` files will be very helpful.

{: .box-note}
<a id="p2"></a>
<a href="#p2"><b>#1</b></a>Dify configuration to use Oracle storage object

In Dify, we can use the Oracle bucket for storage purpose and here is noted down the setup. First, we need to create an Oracle storage bucket (free if under 20 GB). Detailed instructions can be found in Oracle official documentation in Ref. [2]. Here below are several screenshots for the process,

<p align='center'>
<img src="/assets/img/posts/oci_bucket_1.png"
   style="border:none;"
   width="1000"
   alt="abs_geo"
   title="abs_geo" />
</p>

<p align='center'>
<img src="/assets/img/posts/oci_bucket_2.png"
   style="border:none;"
   width="1000"
   alt="abs_geo"
   title="abs_geo" />
</p>

<p align='center'>
<img src="/assets/img/posts/oci_bucket_3.png"
   style="border:none;"
   width="1000"
   alt="abs_geo"
   title="abs_geo" />
</p>

<p align='center'>
<img src="/assets/img/posts/oci_bucket_4.png"
   style="border:none;"
   width="1000"
   alt="abs_geo"
   title="abs_geo" />
</p>

Once we create the bucket, we can then copy the bucket name as shown in the screenshot above. In Dify configuration, the `OCI_ENDPOINT` option should have been filled with a proper value so we don't need to worry about it. We then fill in the bucket name like this,

```
OCI_BUCKET_NAME: ${OCI_BUCKET_NAME:-bucket-20250311-1049}
```

where `bucket-20250311-1049` is our bucket name. We then need to create the access key and the corresponding secret key. To do this, we follow the procedures down below,

<p align='center'>
   <video width="800" controls>
      <source src="/assets/img/posts/OCI_access_secret_keys.mp4" type="video/mp4">
   </video>
</p>

where the first key we copied in the video is the secret key which we want to fill into the following part of Dify docker compose file,

```
OCI_SECRET_KEY: ${OCI_SECRET_KEY:-<OUR_SECRET_KEY>}
```

The second key we copied in the video is the access key which will be populated into the Dify docker compose file like this,

```
OCI_ACCESS_KEY: ${OCI_ACCESS_KEY:-<OUR_ACCESS_KEY>}
```

***N.B.*** The `-` symbol in both cases needs to be there but it does not belong to the keys, and `<>` should not be there.

<br>

References
===

[1] [https://github.com/langgenius/dify/releases/](https://github.com/langgenius/dify/releases/)

[2] [Creating Oracle object storage bucket](https://docs.oracle.com/en-us/iaas/Content/Object/Tasks/managingbuckets_topic-To_create_a_bucket.htm#top)