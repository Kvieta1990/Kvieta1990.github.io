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
   width="100"
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

	```
	 ```{bibliography}
	 :style: unsrt
	 ``` 
	```

References
===

[1] [http://pd.chem.ucl.ac.uk/pdnn/refine2/fourier.htm](http://pd.chem.ucl.ac.uk/pdnn/refine2/fourier.htm)

[2] [Personal clip of Ref. [1]](https://www.notion.so/iris2020/Repo-of-Clips-5a8f345bf1d04f4fbc956bc44fa4bcc4?pvs=4#cad9f4d1f7674957940b7567972ee05d)