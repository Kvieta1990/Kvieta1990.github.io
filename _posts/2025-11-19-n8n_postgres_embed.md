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

In this post, I will put down the notes for configuring the `postgres` connection in `n8n`. At the end of the day, the purpose is to create a workflow for the Retrieval Augmented Generation (RAG). In Ref. [1], we can find a workflow template for scraping the `n8n` documentation, embedding the scraped contents and saving it to a vector database (so that RAG can be realized with LLM models). The embedding engine used there is Gemini and the vector database for storing the embedded contents is a temporary one. This means, any time the `n8n` instance is restarted, the saved vector databases will be gone and we need to run the scraping and embedding workflow again. This is not an option for the deployment as a product, and it is the purpose of this blog to set up a database server running locally so that `n8n` can store the embedded contents in the local server.

I was following the instructions in Ref. [2] for setting up the local `postgres` server with the `pgvector` extension (for vector database). Here below is the `docker-compose.yml` file being used,

```yaml
networks:
  n8n_local:
    external: true
services:
  db:
    image: pgvector/pgvector:pg17 # PostgreSQL with pgvector support
    container_name: pgvector-db
    environment:
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: <my_password>
      POSTGRES_DB: vec_emb
    networks:
      - n8n_local
    ports:
      - "5432:5432"
    volumes:
      - pgdata:/var/lib/postgresql/data
      - ./postgres/schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  pgdata: # Stores data outside the container to ensure persistence
```

Parallel to the `docker-compose.yml` file, we should create a directory called `postgres` inside which we want to put in a file `schema.sql` with the following contents (for setting up the `pgvector` extension and a sample database),

```
-- Enable pgvector extension
CREATE EXTENSION IF NOT EXISTS vector;

-- Create sample table
CREATE TABLE items (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    item_data JSONB,
    embedding vector(1536) -- vector data
```

Here, we want to make sure setting a secure password to replace `<my_password>` in the `YAML` file above. To start the container, run,

```bash
sudo docker compose up -d
```

Later on, we can worry about getting rid of `sudo` from the command, which may not be necessary at this stage.

Next, we want to set up `n8n`. Here below is the `docker-compose.yml` file being used,

```YAML
networks:
    n8n_local:
        external: true
services:
    n8n:
        container_name: ${CONTAINER_NAME}
        deploy:
            resources:
                limits:
                    cpus: ${CPUS}
                    memory: ${MEMORY_LIMIT}
        environment:
            N8N_SECURE_COOKIE: false
            N8N_EDITOR_BASE_URL: "https://nen.ornl.gov"
            WEBHOOK_URL: "https://nen.ornl.gov"
        networks:
            - n8n_local
        image: n8nio/n8n:1.120.3
        labels:
            createdBy: Apps
        ports:
            - ${HOST_IP}:${PANEL_APP_PORT_HTTP}:5678
        restart: always
        volumes:
            - ./data:/home/node/.n8n
            - ./local-files:/files
```

Parallel to the `docker-compose.yml` file, we need to create the `.env` file and populate it with the following contents,

```
CONTAINER_NAME="addie-n8n"
CPUS=0
HOST_IP=""
MEMORY_LIMIT=0
PANEL_APP_PORT_HTTP=5678
```

where the `CONTAINER_NAME` can be an arbitrary name meaningful to ourselves. All the other entries can be left untouched, unless we have to -- e.g., if the port `5678` is already in use, we may need to change it to something else. We also need to create a folder `local-files` sitting parallel to the `docker-compose.yml` file. To start the container, run,

```bash
docker compose up -d
```

Here, I am omitting the `sudo` in the docker command which by default would be required. But in my case, initially for some reason, the docker container failed to start up complaining about the permission issue for the `data` directory created automatically while running the docker compose command above. To solve the issue, I was referring to Ref. [3], but meanwhile, I was also following the instructions in Ref. [4] to remove the need for the `sudo` in docker compose command. So, I am not sure which one actually solved the issue so I just put down the notes for both here in the post for future reference. Once the `n8n` container is up running, we need to worry about connecting to the service from externally in a secure way. For such a purpose, we need to set up the web service on the server where `n8n` is running. Here I am using `nginx` for the reverse proxy. Some detailed notes regarding the `nginx` setup together with the SSL certificate for secure connections can be found in Ref. [5]. Here I am setting up the service on the ORNL research cloud virtual machine and therefore the domain name setup is very specific to ORNL. Basically, we can go to [https://devices.ornl.gov](https://devices.ornl.gov) and assign aliases of the domain name to the machine. `Aliase` here just means another sub-level domain name for the machine. For example, one of the domain names for ORNL is `ornl.gov` and we can have `addie.ornl.gov` and `nen.ornl.gov` as the sub-level domain names pointing to the same machine. Once we have the domain name, we can follow the instructions in Ref. [5] for setting up `nginx` + `SSL` for a secure web connection from the internet. For sure, the port `80` (for `HTTP` traffic, which actually will be redirected to `443` through the `nginx` configuration) and `443` (for `HTTPS` traffic) for the machine hosting the services should be opened -- for ORNL research cloud instances, we need to submit exception request form and the cyber security team will perform security check for the machine. The ports can be opened only if the security requirements are met. Here I am backing up the `nginx` configuration for future reference,

```
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;
error_log /var/log/nginx/error.log;

include /usr/share/nginx/modules/*.conf;

events {
    worker_connections 1024;
    # multi_accept on;
}

http {
    log_format main  '$remote_addr - $remote_user [$time_local] "$request" '
        '$status $body_bytes_sent "$http_referer" '
        '"$http_user_agent" "$http_x_forwarded_for"';

    client_max_body_size 200M;
    sendfile            on;
    tcp_nopush          on;
    tcp_nodelay         on;
    keepalive_timeout   65;
    types_hash_max_size 2048;

    add_header X-Frame-Options DENY;

    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    ssl_protocols TLSv1.3 TLSv1.2;
    ssl_prefer_server_ciphers on;

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    include /etc/nginx/conf.d/*.conf;

    map $http_upgrade $connection_upgrade {
        default upgrade;
        '' close;
    }

    server {
        listen 80;
        server_name nen.ornl.gov;
        add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
        add_header X-Frame-Options DENY;
        add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
        add_header X-Frame-Options "SAMEORIGIN";
        return 301 https://nen.ornl.gov$request_uri;
    }

    map $http_upgrade $connection_upgrade {
        default upgrade;
        '' close;
    }

    server {
        listen       443 ssl;
        server_name  nen.ornl.gov;
        root         /usr/share/nginx/html;

        add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
        add_header X-Frame-Options "DENY";

        ssl_certificate "/home/cloud/packages/n8n/nen.pem";
        ssl_certificate_key "/home/cloud/packages/n8n/nen.key";

        location / {
            proxy_pass http://localhost:5678;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;

            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";

            proxy_pass_request_headers on;
            add_header Strict-Transport-Security 'max-age=31536000; includeSubDomains; preload;' always;
            add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate" always;
            add_header Pragma "no-cache" always;
            add_header Expires "0" always;
            add_header X-Frame-Options "SAMEORIGIN";
            add_header X-Frame-Options "DENY" always;
            proxy_set_header X-Frame-Options "SAMEORIGIN";
            client_max_body_size 50M;
            proxy_set_header Connection "";
            proxy_set_header Host $http_host;
            proxy_buffers 256 16k;
            proxy_buffer_size 16k;
            proxy_read_timeout 600s;
            proxy_cache_revalidate on;
            proxy_cache_min_uses 2;
            proxy_cache_use_stale timeout;
            proxy_cache_lock on;
            proxy_http_version 1.1;
        }

        error_page 404 /404.html;
            location = /404.html {
        }

        error_page 500 502 503 504 /50x.html;
            location = /50x.html {
        }
    }
}
```

The following two lines in the `nginx` configuration above is important regarding the websocket connection [6],

```
proxy_set_header Upgrade $http_upgrade;
proxy_set_header Connection "upgrade";
```

without which, in my case, the `n8n` will complain about `connection lost` in the workflow editing interface.

Now, we have both docker container up running and next we want to worry about the connection between the two -- our `n8n` instance need to talk to the `postgres` database for vector database insertion and query. If it is possible to open the port for `postgres` (by default, it is `5432`) on our server, we can configure the connection in `n8n` by using the out-facing address (domain name or IP address) of our server and the port (e.g., `5432`), in the `Credentials` configuration interface as shown below (the `Host` box should be replaced by the domain name or IP address of our server hosting the `postgres` database),

<p align='center'>
<img src="/assets/img/posts/n8n_postgres.png"
   style="border:none;"
   width="200"
   alt="n8n_postgres"
   title="n8n_postgres" />
</p>

However, if we cannot open the port for `postgres` on our server like in my case, we can go with the local connection -- basically, we have both the `n8n` and `postgres` docker containers running on the same server and then we can let them talk to each other locally. To do this, we need to first set up a docker network,

```bash
docker network create n8n_local
```

and the `n8n_local` here is a name for the network to our choice. After that, we need to bring down the running docker container (if network was not configured while firing up the two containers) first, put in the network configuration like what we have in the `docker-compose.yml` file shared above for the two containers, and re-compose the containers. Basically, we do,

```bash
cd /path/to/postgres_or_n8n/folder/where/the/docker-compose.yml/file/is/located
docker compose down
docker compose up -d
```

In the `YAML` file shared above, we have,

```
networks:
    n8n_local:
        external: true
```

at the very top of the file, telling the composing engine about the `n8n_local` local network to use for the composing, and then down below in the `services` section, we use the network,

```
networks:
    - n8n_local
```

To check whether the two containers are using the defined network `n8n_local`, we can do,

```
docker network inspect n8n_local
```

and in the output, we are expected to see the two containers are included in the list of containers that are using the inspected `n8n_local` network. If the network is configured properly for the docker containers, in our `n8n` interface for configuring the `Credentials` (see the interface screenshot above), we can put in those parameters according to the confiugration for our `postgres` database, including the `Host`, `Database`, etc. We can refer to the `docker-compose.yml` file for `postgres` about those information. Specifically for the `Host` input, we want to use the container name for `postgres`, which is also given in the `docker-compose.yml` file shared above (see the `container_name` entry). All the other configurations shown in the screenshot above should apply without the need to change. For the `SSL` connection, I disable it in this case since the connection is going to be local only.

> Some useful information about using `postgres` database with OpenAI and Ollama can be found in Refs. [7, 8].

<br>

References
===

[1] [https://n8n.io/workflows/5993-create-a-documentation-expert-bot-with-rag-gemini-and-supabase/](https://n8n.io/workflows/5993-create-a-documentation-expert-bot-with-rag-gemini-and-supabase/)

[2] [https://dev.to/yukaty/setting-up-postgresql-with-pgvector-using-docker-hcl](https://dev.to/yukaty/setting-up-postgresql-with-pgvector-using-docker-hcl)

[3] [https://community.n8n.io/t/error-in-certificate-creation-due-to-directory-permissions/188027/2](https://community.n8n.io/t/error-in-certificate-creation-due-to-directory-permissions/188027/2)

[4] [https://docs.n8n.io/hosting/installation/server-setups/docker-compose/#next-steps](https://docs.n8n.io/hosting/installation/server-setups/docker-compose/#next-steps)

[5] [https://iris2020.net/2023-02-15-notes_mybb/](https://iris2020.net/2023-02-15-notes_mybb/)

[6] [https://community.n8n.io/t/solved-connection-lost-in-workflow-editor/113110](https://community.n8n.io/t/solved-connection-lost-in-workflow-editor/113110)

[7] [https://dev.to/yukaty/getting-started-with-vector-search-part-2-3amh](https://dev.to/yukaty/getting-started-with-vector-search-part-2-3amh)

[8] [https://dev.to/yukaty/part-3-implementing-vector-search-with-ollama-1dop](https://dev.to/yukaty/part-3-implementing-vector-search-with-ollama-1dop)