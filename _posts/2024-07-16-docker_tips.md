---
layout: post
title: Notes and Tips for using Docker
subtitle:
tags: [docker, dev]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/Docker_logo.png"
   style="border:none;"
   width="600"
   alt="docker"
   title="docker" />
</p>

<br />

# 🏷️ Use `git diff` in a Docker container

Within a Docker container, if we are located in a `git` repository and try to execute `git diff`, there will be no problems. However, if the current working directory is not a `git` repository and we try to execute `git diff /full/path/to/git/repo`, the command will fail, complaining about `not a working repository`. This is something relevant to the environment and configuration within the Docker container. To solve this issue, we can set up the `GIT_DIR` environment variable first, change to the `git` repository directory, and then execute `git diff` [1],

```bash
export GIT_DIR=/full/path/to/git/repo/.git
cd /full/path/to/git/repo
git diff
```

<br />

References
===

[1] <a href="/assets/img/posts/lobehub_git_diff_docker.png" target="_blank">Solution provided by `GPT3.5 Turbo` model.</a>