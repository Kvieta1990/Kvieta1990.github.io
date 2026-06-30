---
layout: post
title: Set up an automated workflow for collecting RSS feeds and send to Gmail
subtitle:
tags: [web, server, tutorial, gmail, automation]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<style>
    .faq-container {
        margin: 0 auto;
    }
    .faq-question {
        margin-bottom: 10px;
        font-weight: bold;
        cursor: pointer;
    }
    .faq-answer {
        display: none;
        margin-bottom: 20px;
    }
    .callout {
        background-color: #e8f4fd; /* Light blue background */
        border-left: 5px solid #007BFF; /* Blue accent on the left */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
        font-family: Arial, sans-serif; /* Ensuring the font is consistent */
    }
    .multiline-span {
        display: block; /* or display: inline-block; */
    }
</style>

<p align='center'>
<img src="/assets/img/posts/n8n.png"
   style="border:none;"
   width="200"
   alt="n8n"
   title="n8n" />
</p>

Following previous post at Ref. [1], we get a feel about the procedures for setting up an automated workflow with `n8n`. Here in this post, we try to build another simple workflow and the assumption here is that we have already gone through the post in Ref. [1] for all the initial setups, including the installation of `n8n`, the configuration for secure connection, etc.

Once again, I put the workflow JSON file in Ref. [2]. Once importing the JSON file into `n8n`, there are several configurations we need to go through to get it working. First, we need to put in our intended RSS address in the `RSS feed` node -- in my example, I was using the RSS for `Slashdot`. If we have never set up the Gmail API credential in `n8n`, again, the `Gmail` node will show an exclamation mark, indicating that we should set up the credential first. To do this, we need to go to the `n8n` credentials setup page (see Ref. [1]) and select the `Gmail OAuth2 API`. Here, we need to follow the instructions in Ref. [3] to set up the Gmail API (N.B. we may need to enable the `Gmail API` as well, apart from those APIs mentioned in the post). After this, we will obtain the API credential ID and secret that we need to put into the `n8n` credentials setup. When setting up the Google API, we will be asked for the redirect URL and we need to copy the URL from the `OAuth Redirect URL` box in the `Gmail OAuth2 API` setup page (in `n8n`). One thing to notice is, when installing `n8n` from docker, we need to manually put in two environment variables to tell the docker engine the URL associated with the `n8n` service. Otherwise, the default redirect URL (something like `http://localhost:5678`) will be shown in the `Gmail OAuth2 API` setup page, which for sure will not work. To add in the environment variables, in the `docker-compose.yml` file, we need to put in `N8N_EDITOR_BASE_URL: "https://nn.iris-home.net"` and `WEBHOOK_URL: "https://nn.iris-home.net"` under the `environment:` entry [4]. After changing the `docker-compose.yml` file, we need to rebuild the docker container -- in `1Panel`, this is just simply a button clicking. Then the workflow should just work.

<br>

References
===

[1] [https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/](https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/)

[2] <a href="/assets/files/Slashdot2Gmail.json" target="_blank">Slashdot2Gmail.json</a>

[3] [https://docs.n8n.io/integrations/builtin/credentials/google/oauth-generic/?utm_source=n8n_app&utm_medium=credential_settings&utm_campaign=create_new_credentials_modal#create-a-google-cloud-console-project](https://docs.n8n.io/integrations/builtin/credentials/google/oauth-generic/?utm_source=n8n_app&utm_medium=credential_settings&utm_campaign=create_new_credentials_modal#create-a-google-cloud-console-project)

[4] [https://community.n8n.io/t/oauth-redirect-url/45108/2](https://community.n8n.io/t/oauth-redirect-url/45108/2)