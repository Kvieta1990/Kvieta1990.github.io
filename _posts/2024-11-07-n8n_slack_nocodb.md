---
layout: post
title: Set up workflows for bidirectional connection between NocoDB and Slack
subtitle:
tags: [web, server, tutorial, Slack, automation, database]
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

`NocoDB` is a web-based platform that allows building no-code database, with a spreadsheet-like interface, allowing convenient interaction with the database. Here in this post, I am going to walk through the setup of workflows that connects the NocoDB database with Slack, in a bidirectional manner. Namely, we can build a workflow that grabs the NocoDB database entries and post messages to Slack. Or, we can trigger a workflow via Slack event (e.g., app mentioning) and post some entries to the NocoDB database.

To set up the forward connection, i.e., from NocoDB to Slack, we first need to create an API token in `NocoDB` via `Team & Settings` $$\rightarrow$$ `Tokens` $$\rightarrow$$ `Add new token`, and we can give the token a meaningful name. Then we go to `n8n` and further go to `Credentials` $$\rightarrow$$ `Add credential` and search for `NocoDB Token account` to paste in the token we created in `NocoDB`. The JSON file in Ref. [1] can be imported into `n8n` (see the instructions in Ref. [2, 3] for more details). Again, when exclamation marks is shown alongside a node, that indicates the credential for the node needs to be selected. We just double click on the node and select the credential in the relevant dropdown list. In my example workflow, I have a `NocoDB` database containing my collection of English words and its corresponding Chinese translation. My workflow is responsible for randomly grabbing 5 entries in the database every day at noon and 5:00 pm and send them to my Slack channel so I can check for better memory.

For the backward connection, we want to trigger the workflow via an event in Slack. Here, by `event`, we basically mean something happening in Slack. We can refer to Ref. [4-6] for available events in Slack. On the Slack side, we want to use `Event Subscriptions` [4] -- go to the list of apps in our Slack workspace at Ref. [7] (if needed app does not exist, we can create it in the page) and select our intended app from the list, and then go to `Event Subscriptions` to enable it. Once enabled, we can put in the webhook URL that we would get from `n8n` when creating the node of a slack trigger, e.g., `On bot app mention`. Double clicking the Slack trigger node, we can see the `Webhook URLs` on the top and we can expand it to see the URL. We can copy the URL and paste it into the Slack `Event Subscriptions` settings. Then under `Subsribe to bot events` in `Event Subscriptions`, we can add in the trigger event to Subsribe, e.g., `app_mention`. Then we can save the changes. Back to the `n8n` settings, we need to choose the channel to watch and once the channel is selected, we again need to go to Slack and add the Slack app (which we used above for setting up the `Event Subscriptions`) to the channel (see Ref. [3]).

<br>

> Here, it is worth mentioning that for the `Webhoook URLs`, when we are doing tests of the workflow, we need to select the `Test URL` to use in `n8n`. Once we finish the testing and make sure everything is working fine, we should then copy the `Production URL` to Slack `Event Subscriptions` settings.

The JSON file for the backward connection workflow can be found in Ref. [8].

<br>

References
===

[1] <a href="/assets/files/Dict2Slack.json" target="_blank">Dict2Slack.json</a>

[2] [https://iris2020.net/2024-11-04-n8n_rss_gmail/](https://iris2020.net/2024-11-04-n8n_rss_gmail/)

[3] [https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/](https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/)

[4] [https://api.slack.com/apis/events-api#verification](https://api.slack.com/apis/events-api#verification)

[5] [https://api.slack.com/apis/http](https://api.slack.com/apis/http)

[6] [https://api.slack.com/events](https://api.slack.com/events)

[7] [https://api.slack.com/apps/](https://api.slack.com/apps/)

[8] <a href="/assets/files/Msg2NocoDB.json" target="_blank">Msg2NocoDB.json</a>