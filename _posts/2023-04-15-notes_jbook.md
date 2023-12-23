---
layout: post
title: Notes on using Jupyter book
subtitle:
tags: [jupyter, programming]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/jbook.png"
   style="border:none;"
   width="400"
   alt="jbook"
   title="jbook" />
</p>

Following the instruction for building our book with Jupyter book, we will get the built book in HTML
format, which we can then grab and host it somewhere. The GitHub page was used as the demo in the official
Jupyter book website. However, if we do have our own web server, we can put the HTML files on our server
and host it through some configurations. In my case, I was using `nginx` for serving the web service , the
following snippet a typical configuration of the nginx server concerning hosting our built HTML book on
our server,

```
location /pd {
	autoindex on;
	autoindex_exact_size on;
	index index.html;
	root /mnt/Iris_ExtL;
}
```

where `/mnt/Iris_ExtL` is the location where the `pd` directory is located. Such an organization of
folder location and the corresponding input in the nginx configuration file is indeed necessary. For
example, if we think that as all the HTML files are located under `/mnt/Iris_ExtL/pd` (indeed, in my
case, it is) and put `/mnt/Iris_ExtL/pd` as the root in the above snippet, it won't work.

The next thing we need to pay attention to is the ownership and permission of the specified root
directory (as the one shown above, for example, specified to be `/mnt/Iris_ExtL/` -- combined with
the `location` entry, this means all the HTML files are located under `/mnt/Iris_ExtL/pd`). The owner
of the `/mnt/Iris_ExtL/pd` directory, for example, should be the nginx user. We can know about the
nginx in our nginx configuration file and in my case, it is `/etc/nginx/nginx.conf`. The entry
`user nginx;` can be found in the configuration file from which we can tell that the nginx user in
this case is `nginx`. So, the owner of the `/mnt/Iris_ExtL/pd` directory and all the sub-directories
should be set to `nginx:nginx`. Meanwhile, all the files under `/mnt/Iris_ExtL/pd/pd` should be `755`.

## Miscellaneous Notes

- The indent of the YAML file should be consistent, and otherwise the configuration won't be read in properly.

- To use the bibliography, when following the instruction in the Jupyter book official documentation, we should
be aware of that the file hosting the bibliography should be created. Then we should put the following contents
in the file (followed by including such a bibliography file as part of the TOC),

<p style="margin-left: 1.1cm">
   <a>\`</a><a>\`</a><a>\`</a><a>{bibliography}</a>
   <br />
   :style: unsrt
   <br />
   <a>\`</a><a>\`</a><a>\`</a>
</p>

   > Here, a side note (which is irrelavant to current topic) is that to put the code block included within
   the three backticks marks as plain text, we can refer to the source code of current page located at Ref. [1].

- Sometimes, the change in the deployed jupyter book may not be directly reflected at the client side due to
caching. For example, on the server side, we may already change the root page from `intro` to `index`, the client
side may be still seeing `intro` as the root page, in which case we then need to clear the browser cache and reload the page
to see the effect.

- When using `nginx` to server the website, as usual we can put the HTML files under a certain directory and configure
the nginx server as specified above. However, I noticed that we CANNOT put the HTML files under our home directory, e.g.,
`/home/cades/some_dir/pd` -- the server would then complain about permission denied even the owner and the permission of
all the HTML files were configured properly. The directory used above just worked out!

- To insert javascript to the header of each page in the built book, in the `_config.yml` file, we need to have the
`sphinx` section like this,

```
sphinx:
  config:
    bibtex_reference_style: author_year
    nb_execution_show_tb: True
    nb_execution_timeout: 120
    html_js_files: [ ['https://umm.iris-home.net/script.js', {'data-website-id': '1ec8f93f-95ed-478f-9f23-a038fe03adf3'}] ]
```

    > The corresponding javascript insertion would be,

    ```
    <script async src="https://umm.iris-home.net/script.js" data-website-id="1ec8f93f-95ed-478f-9f23-a038fe03adf3"></script>
    ```

<b>References</b>

[1] [https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/_posts/2023-04-15-notes_jbook.md](https://github.com/Kvieta1990/Kvieta1990.github.io/blob/master/_posts/2023-04-15-notes_jbook.md)
