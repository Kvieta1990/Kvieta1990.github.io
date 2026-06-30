---
layout: post
title: Set up Dify server on VPS with docker
subtitle:
tags: [web, docker, gpt]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/dify.png"
   style="border:none;"
   width="600"
   alt="dify"
   title="dify" />
</p>

Dify is an LLM application platform which can provide access to various LLM models, tools and applications. With it,
we can build our own ChatBot server acting just like ChatGPT. At the backend, it utilizes the LLM services from various
providers, such as openAI, Gemini, etc., through API call. In my case, I am using both the `openAI` and `Gemini` API
calls. Within the `Dify` platform, we can create apps serving different purposes and they are independent from each
other. Also, we can set up our own knowledge and use that together with the API to train the chatbot with our own
knowledge base. Here in this blog, I am going to walk through the process of setting up our own `Dify` server, create
apps in it, add in our own knowledge base, and add in extra tools (such as `DALL-E`, `Mathematica API`, etc.) to access
more functionalities.

1. To self host a `Dify` server, we need our own VPS. In my case, I am using the Oracle VPS with `ARM64` architecture.
Oracle provides a `always-free` version of the `ARM64` VPS which comes with a very powerful configuration (4 CPU cores and
24 Gb RAM). One could obtain such a powerful and always-free service via [Oracle Cloud](https://www.oracle.com/cloud/).

   > The `always-free` service with such a powerful configuration is `ONLY` available for the `ARM64` architecture and it
   is not always available. It depends on the availability of Oracle resources in different regions. So, when registering
   for the service, choosing the region of the VPS server is important. Also, the registration process is bundled with
   credit card and personal information verification and once registered, the region cannot be changed. However, it may
   not be straightforward to know beforehand in which region will the powerful and always-free `ARM64` architecture be
   available. So, in practice, whether or not we can get the powerful machine for free is a bit luck-based.

2. Once we have a VPS, log into it and grab the docker compose file [here](https://github.com/langgenius/dify/blob/main/docker/docker-compose.yaml)
for firing up the `Dify` service. Usually, the docker compose file will be up-to-date to catch up with the backend codes
and the docker images. So, for installation and update, we can, most of the time, rely on this docker compose file.

   > Using the docker compose way for setting up the server, when we want to update the server, we may need to stop and remove
   the old docker containers for `Dify` and all the other associated docker containers (such as the `nginx` and the `dify-web`)
   and then run the compose again with the up-to-date compose file.

3. Once the `Dify` server is up, we can go to its admin page via the main domain name. The docker compose file should
contain the port on host that the `Dify` service will be running on. With the port number, we can for sure access the
service with the IP address together with the port number, via the HTTP traffic. In practice, we may need a domain name
and also to make our website secure, we also need to configure the reverse proxy (using, e.g., `nginx`) and the SSL
certificate, etc. Details on these processes will not be covered in this blog and we can refer to my another blog
[here](https://iris2020.net/2023-09-08-docker_nginx/).

4. To add in tools in the up-to-date version of `Dify`, in the admin page, we can refer to the following image,

   <p align='center'>
   <img src="/assets/img/posts/dify_tools.png"
      style="border:none;"
      width="600"
      alt="dify_tools"
      title="dify_tools" />
   </p>

5. For some of the tools there, they need authorization which usually requires us to obtain API from the backend
service provider. For example, to use the `DALL-E` tool, we need to obtain the API from openAI and it is not free. Once
authorized, we can go to the `Studio` menu (see the top part of the image above) and either create a new app of click on
and existing app to go into the app configuration page. First , we want to make sure that we should choose the type of
the app as `Agent Assistant` but not the `Basic Assistant` since the tools can only be added in `Agent Assistant`.

   <p align='center'>
   <img src="/assets/img/posts/dify_tools1.png"
      style="border:none;"
      width="600"
      alt="dify_tools1"
      title="dify_tools1" />
   </p>

   > In my case, when trying to use the `DALL-E` tool, there was a bug in the `Dify` codes and the tool implementation
   was not working. Refer to the link [here](https://github.com/langgenius/dify/issues/2246#issuecomment-1912535817) for
   the solution.

   > For using the `Mathematica` tool, we can obtain our Mathematica API [here](https://developer.wolframalpha.com/access).
   My company has the `Mathematica` subscription and therefore I can get the API access through my company account. Also,
   it is worth mentioning that after the `Mathematica` app ID is created, we need to give it a while before it starts to
   take effect. Within that period before its taking effect, adding in the App ID in `Dify` will suggest that the App ID
   is invalid.

6. Google recently release the Gemini Pro API and in `Dify`, we can also configure it to use the Gemini API. We can go
to the settings interface (click on the top right user dropdown and go to the settings panel to further select
`Model Provider`). Here follows is shown the way to obtain the Gemini API,

   <p align='center'>
      <video width="600" controls>
         <source src="/assets/img/posts/gemini_api.mp4" type="video/mp4">
      </video>
   </p>

7. To add in our own knowledge base to train our own chatbot, we can go to the `Knowledge` option on the top of the `Dify`
admin page. There, we can import files, or in the future, we can choose to `Sync from website` (currently not available
as of Jan-28-2024). Then in our app, we can go to the `Context` section and select the knowledge we just created in the
`Knowledge` section.

8. Since some of the GPT API's may have limitation of tokens, sometimes in our chat app in `Dify`, when the context is
becoming long, the app may not be able to further respond to our inquiry, in which case we then have to lose the context
and create a new chat.

9. If we go to the `Overview` section in our app configuration page -- see the image below,

   <p align='center'>
   <img src="/assets/img/posts/dify_app_overview.png"
      style="border:none;"
      width="600"
      alt="dify_app_overview"
      title="dify_app_overview" />
   </p>

    we can further click on the `Embedded` option just below the public URL of our app and see a few options that `Dify`
provides to embed our app in other apps such as our website, or Google Chrome extension. In my case, I was implementing
my `Dify` app in my personal website constructed using wordpress. In my wordpress, I need to go to `Settings` and further
go to `Head and Footer` to populate the `Dify` app codes into the `Head and footer` injection part.

   <p align='center'>
   <img src="/assets/img/posts/dify_wp.png"
      style="border:none;"
      width="600"
      alt="dify_wp"
      title="dify_wp" />
   </p>

10. To upgrade `Dify` to the latest version, we could copy over the `docker-compose.yaml` file from the [GitHub repo](https://github.com/langgenius/dify/blob/main/docker/docker-compose.yaml). We may need to copy over the settings in our old compose file to the downloaded new version. We may want to stop the currently running `Dify` service by running `sudo docker ps -a | grep 'dify'` first to grab the container IDs relevant to the `Dify` service, followed by running `sudo docker container stop DIFY_CONTAINER_ID_1 DIFY_CONTAINER_ID_2 DIFY_CONTAINER_ID_3` and `sudo docker container rm DIFY_CONTAINER_ID_1 DIFY_CONTAINER_ID_2 DIFY_CONTAINER_ID_3` commands. After that, we then go to the directory where the `docker-compose.yaml` file is located and run the `sudo docker compose up -d` command to start the `Dify` service. Be default, there will be another docker containing running to support the `Dify` service, namely the `nginx` docker container for web hosting. We also need to restart the `nginx` container. In my case, I only have one `nginx` container running and it is easy to identify the ID and restart it by running `sudo docker restart NGINX_CONTAINER_ID`. If we have multiple `nginx` containers running, for sure, we need to first identify which one is relevant to the `Dify` service.
