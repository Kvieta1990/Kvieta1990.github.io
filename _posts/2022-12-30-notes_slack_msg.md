---
layout: post
title: Notes on Slack App Setup
subtitle: Send message to Slack with Python
tags: [tool, technical, Slack, Python]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
To set up for sending message to Slack using Python, we can refer to Ref. [1]. Most of the instructions in the reference
would work out, with several glitches and thus necessary adjustments.
</p>

<p align='center'>
<img src="/assets/img/posts/slack.png"
   style="border:none;"
   width="100"
   alt="slack"
   title="slack" />
</p>

> First, it should be stressed the importance of setting the proper `scope` when setting up the `OAuth & Permissions`. If the
scope is not set up properly, the message would not be sent successfully even the login token is configured without any problems.
The instructions in Ref. [1] provides all the list of scope items that would guarantee a successful message sending.

> Another tip is that the Python script containing the token cannot be exposed to public domains, e.g., be pushed to Github repo.
Once we do that, Slack API can detect the exposure and disable the API, which does make sense since exposing the token to the
public domain is definitely not a good idea. However, this only applies to the situation when we put the token directly in the
script. If we define a system variable for the token and access the token within the script via the system variable, it should be fine.

> A little glitch in the instruction in Ref. [1] is about the channel name. It seems that the channel name cannot contain the `#` symbol.

Here follows is a working example of sending message to Slack using Python,

```python
#!/usr/bin/env python

from slack_sdk import WebClient
from slack_sdk.errors import SlackApiError
import random

client = WebClient(token="MY_TOKEN_FROM_SLACK_API")

num = random.random()
if num >= 0.3:
    file_in = "/home/y8z/Dropbox/Documents_ORNL/Memorize/words.md"
else:
    file_in = "/home/y8z/Dropbox/Documents_ORNL/Memorize/others.md"

file_read = open(file_in, "r")
lines = file_read.readlines()

if len(lines) > 0:
    to_send = []
    num_to_send = min(5, len(lines))
    for i in range(num_to_send):
        index_tmp = random.randint(0, len(lines) - 1)
        to_send.append(index_tmp)
    msg_list = [lines[i] for i in to_send]
    msg_body = "\n".join(msg_list)
    client.chat_postMessage(channel="memorize", text=msg_body)
```

The script will randomly grab 5 words or other memorize items from the files in which I manually populated entries from time to time. Then it will
send the randomly grabbed memorize items to my Slack channel. I set up a cron job that executes this script on a daily base so that I can deepen my
memory on those items on a daily basis.

<br />

<b>References</b>

[1] [https://plazagonzalo.medium.com/send-messages-to-slack-using-python-4b986586cb6e](https://plazagonzalo.medium.com/send-messages-to-slack-using-python-4b986586cb6e)