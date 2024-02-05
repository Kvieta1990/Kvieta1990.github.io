---
layout: post
title: Set up our own search engine with Yacy
subtitle:
tags: [web, search]
author: Yuanpeng Zhang
comments: true
use_math: true
---

The fundamental idea of web searching is through web crawling, critical information about the website and the embedded links in the crawled website can be obtained. Based on the crawling results, a ranking algorithm could be applied, based on which one can search the website using keywords. Nowadays, we get so much into Google, which also takes the same way of performing the ranking of millions or probably billions of website for searching. Google, as a centralized searching service provider, they need to deal with so many websites in the market and as an end user, if we want our website to be seen by Google, we may need to submit the crawling request to Google -- if not, Google still is able to find our website and perform crawling for it but that will probably take forever, since the reality is, as said above, Google needs to deal with those tons of websites on the market and our own website has little chance to be seen if we are not submitting the crawling request to Google. However, even though we submit the crawling request, still it may take some time and sometimes, for whatever reason, the crawling may not give back a full crawling for our site, etc. As an end user, if we want to have a dedicated search engine just for our website, we can go to the market and search for alternative search engines. `Yacy` is the one that I found useful and here in this blog, I will note down the setup of `Yacy` on my own VPS and its implementation in my own blog website (actually, the current blog website that you are visiting now).

`Yacy` provides docker image for the service, and to run the docker container to fire up the server, we can simply run the following command,

   ```bash
   sudo docker run -d --name yacy_search_server -p 8092:8090 -p 8443:8443 -v yacy_search_server_data:/opt/yacy_search_server/DATA --restart unless-stopped --log-opt max-size=200m --log-opt max-file=2 yacy/yacy_search_server:aarch64-latest
   ```

where `8092` is my selected port number.

> Here, concerning the proxy setup to make the `Yacy` server secure, we can refer to my another blog <a href="https://iris2020.net/2023-09-08-docker_nginx/" target="_blank">here</a>.

The next step is to implement the `Yacy` engine into our own website. In my case, I am using `jekyll` to host the current blog on `GitHub`. We can refer to the YAML configuration file <a href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/_config.yml" target="_blank">here</a> about the overall configuration of the whole site to be generated with `jekyll`. Specifically, the search page link in the top menu is configured <a href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/ed61a590f1cbb2f61146345e590bc1804c9d02ac/_config.yml#L17" target="_blank">here</a>. This further points to the `search.html` file <a href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/search.html" target="_blank">here</a>, which further contains the inclusion of the `search-google.html` file. The `search-google.html` file then contains the HTML codes for hosting the search box which will lead up to the `Yacy` engine. The file can be found <a href="https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/_includes/search-google.html" target="_blank">here</a>. The file was created by combining multiple pieces. The first piece is from the `Yacy` server. Once we have our `Yacy` server set up, we can visit the page, e.g., `ya.iris-home.net` in my case. Further, we go to `Administration` (needs login) => `Search Portal Integration` (left-side menu) => `Portal Configuration` => `Search Box Anywhere` (top selection menu in the right-side panel). There, we can see the code snippet that we need to grab for the implementation of `Yacy`. Here, I did not take them all but instead only the form part. Next, it seems that `Yacy` does not provide the API interface with which we can directly provide our own website as the parameters to tell `Yacy` to search our own website instead of an overall searching. As a workaround, I have a piece of javascript to append our site to the input search contents and pass the concatenated search string to `Yacy` to guide it to search our own website for the provided keywords.

> Before `Yacy` is able to search our website, we need to crawl it first. To do this, we go to `First Steps` => `Load Web Pages, Crawler`, Then, we only need to fill in our website URL and hit the `Start New Crawl` and `Yacy` will start the crawling. That's all. Once the crawling is done, our website information will be available to `Yacy` and the search over our website should be doable with `Yacy`.