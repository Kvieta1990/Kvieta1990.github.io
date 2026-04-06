---
layout: post
title: Strands Agent Episode-1 — Setup
subtitle:
tags: [agent, AI]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/strands_agent.svg"
   style="border:none;"
   width="500"
   alt="strands_agnet"
   title="strands_agent" />
</p>

This is the first post in the series of using Strands agent SDK for agent development. The walkthrough can be found in the official quick start documentation with Python [1]. This post serves as the side note for the documentation, noting down some technical details that we may need regarding the setup.

By default, the Strands agent SDK uses the AWS Bedrock for accessing LLMs, where AWS Bedrock is a bridge platform that provides secure access to those foundational models provided by AI companies. So, to use the default model provided by AWS Bedrock in Strands agent, we need to first have an AWS account. Ref. [2] provides some quickstart instructions for the account setup and quickly starting to play around with the AWS Bedrock models. The quickstart instructions there are using the long-term API key for granting access to the AWS models. Though, the Strands agent development to be discussed below is not using this way, still here I am putting down some notes about using the long-term API key for accessing the AWS models. Following the instructions in Ref. [2], we should set up the long-term API key first. Then we go to our local machine to set up the Python environment and install the necessary packages -- see Ref. [2]. Next, create a folder on our local machine, e.g., `aws_bedrock_test`, inside which we want to create a file `.env` (should be with exactly the name) to put in the following two lines,

```
OPENAI_API_KEY=<aws_bedrock_long_term_api_key>
OPENAI_BASE_URL=https://bedrock-mantle.us-east-1.api.aws/v1
```

where `<aws_bedrock_long_term_api_key>` refers to the long-term API key we obtained from AWS Bedrock above. Here `us-east-1` is the region for my AWS account which may need to be changed according to the specific region of our AWS account. To see the region name, we can go to [https://aws.amazon.com/cli/](https://aws.amazon.com/cli/) and click on `Sign in to console` (top-right of the page). In the main page of the console, we should be able to find the current region in the `Application` session. Then we can create a Python script as below to test out accessing LLMs through AWS Bedrock,

```python
from dotenv import load_dotenv
load_dotenv()

from openai import OpenAI

client = OpenAI()

response = client.responses.create(
    model="openai.gpt-oss-120b",
    input="Can you explain the features of Amazon Bedrock?"
    )
print(response)
```

where the open model `gpt-oss-120b` provided OpenAI is used.

Regarding the AWS Bedrock access with Strands agent, Ref. [1] provides four options and here I am going with the option-2, namely, using the `aws` CLI tool. First, we need to install the `aws` CLI tool on our local machine, following the instructions in Ref. [3]. Next, we want to run `aws configure` from the command line (e.g., on `powershell`) and we will be asked for the `AWS Access Key ID`, `AWS Secret Access Key`, `Default region name` and `Default output format`. The first two entries need to be created from the AWS console. Signing in the console from [https://aws.amazon.com/cli/](https://aws.amazon.com/cli/), we can search `IAM` in the search bar on the top of the console. Inside the `IAM` console, we further go to `Users` under `Access Management` where we can see the Bedrock users listed there. If we were following the instructions above to start with AWS Bedrock, the users should have already been created. Otherwise, we can create a user here. Then click on the user name, followed by `Security credentials` to see the `Access keys` session. Create the access key by clicking on `Create access key` where we can obtain the `Access Key ID` and `Secret Access Key`. The `Default region name` is just the region name associated with our AWS account as mentioned above and `Default output format` can be set to `json`. After running `aws configure` and filling in those entries properly, the connection to the AWS Bedrock can be set up properly and Strands agent should be able to use those models available through AWS Bedrock.

Following the quickstart instructions in Ref. [1], by default the model to be used by Strands agent is `claude-sonnet-4-20250514-v1:0` by Anthropic, where `claude-sonnet-4-20250514-v1:0` is the model ID. To see the available models and their IDs, we go to the AWS console, search for `Amazon Bedrock` and go to its console. Then go to `Model catalog` under `Discover` to see the list of available models. Clicking on any of the models in the list, we can see details of the model, including the model ID, e.g., `anthropic.claude-sonnet-4-6` for the Claude Sonnect 4.6 model. First time setting this up, running the Strands agent script provided in the quickstart instruction would fail, complaining something like,

```
botocore.errorfactory.ResourceNotFoundException: An error occurred (ResourceNotFoundException) when calling the ConverseStream operation: Model use case details have not been submitted for this account. Fill out the Anthropic use case details form before using the model. If you have already filled out the form, try again in 15 minutes.
```

This is due to that the first time AWS Bedrock models use requires approval by us manually in the AWS console. Again, in the model catalog console, we expect to see some warning message at the top part of the page guiding us to approve the AWS Bedrock models usage. We just need to follow the instructions there to approve the access and after a short while we expect to receive an email notifying that we have got the access. After that, the Strands agent script should be able to run.

<br />

References
===

[1] [https://strandsagents.com/docs/user-guide/quickstart/python/](https://strandsagents.com/docs/user-guide/quickstart/python/)

[2] [https://docs.aws.amazon.com/bedrock/latest/userguide/getting-started.html](https://docs.aws.amazon.com/bedrock/latest/userguide/getting-started.html)

[3] [https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html#getting-started-install-instructions](https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html#getting-started-install-instructions)