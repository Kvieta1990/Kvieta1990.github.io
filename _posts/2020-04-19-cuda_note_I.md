---
layout: post
title: Notes on CUDA programming I
subtitle: Preparation
tags: [programming, GPU]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p style='text-align: justify'>
To understand and implement CUDA codes in practice, the very first step is to understand the allocation of `threads` on GPU. Fundamentally, `threads` are basic units on GPU where computation can happen in a parallel way. To the first level, threads are grouped into `blocks`, either in a 1D, 2D or 3D manner, where each `thread` within a certain `block` has its own "coordinate" - a unique index for pinning down the location of a certain `thread` in the `block`. At the second level, `blocks` are further grouped into `grid`, in a similar way as how `threads` are grouped into `block`. Here we use three diagrams showing the basic idea of such a division manner for GPU computation units and how we really locate a `thread` in a `block` and a `block` in a `grid`.
</p>

<p align='center'>
<img src="/assets/img/posts/cuda_demo_1.png"
   style="border:none;"
   alt="cuda_demo_1"
   title="cuda_demo_1" />
<br />
</p>

<p align='center'>
<img src="/assets/img/posts/cuda_demo_2.png"
   style="border:none;"
   alt="cuda_demo_2"
   title="cuda_demo_2" />
<br />
</p>

<p align='center'>
<img src="/assets/img/posts/cuda_demo_3.png"
   style="border:none;"
   alt="cuda_demo_3"
   title="cuda_demo_3" />
<br />
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp1')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: rgb(255, 255, 255); border: solid gray; height: 500px; overflow: auto; padding: 0.0em 0.0em; width: auto;">
<table><tbody>
<tr><td><pre style="color: grey; line-height: 125%; margin-bottom: 0px; margin-right: 5px; margin-top: 0px;"> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42</pre>
</td><td id="csp1"><pre style="line-height: 125%; margin: 0px;"><span style="color: #557799;">#include &lt;stdio.h&gt;</span>

<span style="color: #008800; font-weight: bold;">__global__</span>
<span style="color: #333399; font-weight: bold;">void</span> <span style="color: #0066bb; font-weight: bold;">saxpy</span>(<span style="color: #333399; font-weight: bold;">int</span> n, <span style="color: #333399; font-weight: bold;">float</span> a, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>y)
{
  <span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> <span style="color: #007020;">blockIdx</span>.x<span style="color: #333333;">*</span><span style="color: #007020;">blockDim</span>.x <span style="color: #333333;">+</span> <span style="color: #007020;">threadIdx</span>.x;
  <span style="color: #008800; font-weight: bold;">if</span> (i <span style="color: #333333;">&lt;</span> n) y[i] <span style="color: #333333;">=</span> a<span style="color: #333333;">*</span>x[i] <span style="color: #333333;">+</span> y[i];
}

<span style="color: #333399; font-weight: bold;">int</span> <span style="color: #0066bb; font-weight: bold;">main</span>(<span style="color: #333399; font-weight: bold;">void</span>)
{
  <span style="color: #333399; font-weight: bold;">int</span> N <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">1</span><span style="color: #333333;">&lt;&lt;</span><span style="color: #0000dd; font-weight: bold;">20</span>;
  <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333333;">*</span>y, <span style="color: #333333;">*</span>d_x, <span style="color: #333333;">*</span>d_y;
  x <span style="color: #333333;">=</span> (<span style="color: #333399; font-weight: bold;">float</span><span style="color: #333333;">*</span>)malloc(N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));
  y <span style="color: #333333;">=</span> (<span style="color: #333399; font-weight: bold;">float</span><span style="color: #333333;">*</span>)malloc(N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));

  cudaMalloc(<span style="color: #333333;">&amp;</span>d_x, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>)); 
  cudaMalloc(<span style="color: #333333;">&amp;</span>d_y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));

  <span style="color: #008800; font-weight: bold;">for</span> (<span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">0</span>; i <span style="color: #333333;">&lt;</span> N; i<span style="color: #333333;">++</span>) {
    x[i] <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">1.0f</span>;
    y[i] <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">2.0f</span>;
  }

  cudaMemcpy(d_x, x, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyHostToDevice);
  cudaMemcpy(d_y, y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyHostToDevice);

  <span style="color: #888888;">// Perform SAXPY on 1M elements</span>
  saxpy<span style="color: #333333;">&lt;&lt;&lt;</span>(N<span style="color: #333333;">+</span><span style="color: #0000dd; font-weight: bold;">255</span>)<span style="color: #333333;">/</span><span style="color: #0000dd; font-weight: bold;">256</span>, <span style="color: #0000dd; font-weight: bold;">256</span><span style="color: #333333;">&gt;&gt;&gt;</span>(N, <span style="color: #6600ee; font-weight: bold;">2.0f</span>, d_x, d_y);

  cudaMemcpy(y, d_y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyDeviceToHost);

  <span style="color: #333399; font-weight: bold;">float</span> maxError <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">0.0f</span>;
  <span style="color: #008800; font-weight: bold;">for</span> (<span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">0</span>; i <span style="color: #333333;">&lt;</span> N; i<span style="color: #333333;">++</span>)
    maxError <span style="color: #333333;">=</span> max(maxError, abs(y[i]<span style="color: #333333;">-</span><span style="color: #6600ee; font-weight: bold;">4.0f</span>));
  printf(<span style="background-color: #fff0f0;">"Max error: %f</span><span style="background-color: #fff0f0; color: #666666; font-weight: bold;">\n</span><span style="background-color: #fff0f0;">"</span>, maxError);

  cudaFree(d_x);
  cudaFree(d_y);
  free(x);
  free(y);
}
</pre>
</td></tr>
</tbody></table>
</div>

<p style='text-align: justify'>
One can save the codes as a file with extension '.cu' and compile the codes using 'nvcc' compiler provided by NVIDIA (<a target="_blank" href="https://draft.blogger.com/blog/post/edit/713170236114697752/8111989247194765686#">Click Me!</a>), just like usually what we do for compiling normal C codes, e. g. by executing 'nvcc -o first_cuda_demo first_cuda_demo.cu'. Here, we won't go into further details about the code (refer to Ref. [1] for more introduction), and instead we will give here an abstract skeleton of the codes to get a bird view.
</p>

<p align='center'>
<img src="/assets/img/posts/cuda_flow.png"
   style="border:none;"
   alt="cuda_flow"
   title="cuda_flow" />
<br />
</p>

<p style='text-align: justify'>
Also, one can define all the CUDA relevant executions as a dedicated and callable routine. Then we can call the CUDA routine from normal C, C++, Fortran or whatever relevant codes. For example, we can write the codes above in a manner as shown below.

<br />

First, the caller C codes,
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp2')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: rgb(255, 255, 255); border: solid gray; overflow: auto; padding: 0.0em 0.0em; width: auto;">
<table><tbody>
<tr><td><pre style="color: grey; line-height: 125%; margin-bottom: 0px; margin-right: 5px; margin-top: 0px;"> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29</pre>
</td><td id="csp2"><pre style="line-height: 125%; margin: 0px;"><span style="color: #557799;">#include &lt;stdio.h&gt;</span>
<span style="color: #557799;">#include &lt;stdlib.h&gt;</span>
<span style="color: #557799;">#include &lt;string.h&gt;</span>
<span style="color: #557799;">#include &lt;cuda.h&gt;</span>

<span style="color: #008800; font-weight: bold;">extern</span> <span style="color: #333399; font-weight: bold;">void</span> <span style="color: #0066bb; font-weight: bold;">cuda_kernel</span>(<span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>y, <span style="color: #333399; font-weight: bold;">int</span> <span style="color: #333333;">N</span>);

<span style="color: #333399; font-weight: bold;">int</span> <span style="color: #0066bb; font-weight: bold;">main</span>(<span style="color: #333399; font-weight: bold;">int</span> argc, <span style="color: #333399; font-weight: bold;">char</span> <span style="color: #333333;">*</span>argv[])
{
  <span style="color: #333399; font-weight: bold;">int</span> N <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">1</span><span style="color: #333333;">&lt;&lt;</span><span style="color: #0000dd; font-weight: bold;">20</span>;
  <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333333;">*</span>y;
  x <span style="color: #333333;">=</span> (<span style="color: #333399; font-weight: bold;">float</span><span style="color: #333333;">*</span>)malloc(N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));
  y <span style="color: #333333;">=</span> (<span style="color: #333399; font-weight: bold;">float</span><span style="color: #333333;">*</span>)malloc(N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));

  <span style="color: #008800; font-weight: bold;">for</span> (<span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">0</span>; i <span style="color: #333333;">&lt;</span> N; i<span style="color: #333333;">++</span>) {
  x[i] <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">1.0f</span>;
  y[i] <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">2.0f</span>;
  }

  cuda_kernel(<span style="color: #333333;">&amp;</span>x, <span style="color: #333333;">&amp;</span>y, N);

  <span style="color: #333399; font-weight: bold;">float</span> maxError <span style="color: #333333;">=</span> <span style="color: #6600ee; font-weight: bold;">0.0f</span>;
  <span style="color: #008800; font-weight: bold;">for</span> (<span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> <span style="color: #0000dd; font-weight: bold;">0</span>; i <span style="color: #333333;">&lt;</span> N; i<span style="color: #333333;">++</span>) {
    maxError <span style="color: #333333;">=</span> max(maxError, abs(y[i]<span style="color: #333333;">-</span><span style="color: #6600ee; font-weight: bold;">4.0f</span>));
  }
  printf(<span style="background-color: #fff0f0;">"Max error: %f</span><span style="background-color: #fff0f0; color: #666666; font-weight: bold;">\n</span><span style="background-color: #fff0f0;">"</span>, maxError);

  <span style="color: #008800; font-weight: bold;">return</span> <span style="color: #0000dd; font-weight: bold;">0</span>;
}
</pre>
</td></tr>
</tbody></table>
</div>

<p style='text-align: justify'>
Saving the codes above as, e. g. 'caller.c'.

<br />

Then the callee CUDA codes,
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp3')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: rgb(255, 255, 255); border: solid gray; overflow: auto; padding: 0.0em 0.0em; width: auto;">
<table><tbody>
<tr><td><pre style="color: grey; line-height: 125%; margin-bottom: 0px; margin-right: 5px; margin-top: 0px;"> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27</pre>
</td><td id="csp3"><pre style="line-height: 125%; margin: 0px;"><span style="color: #557799;">#include &lt;stdio.h&gt;</span>

__global__
<span style="color: #333399; font-weight: bold;">void</span> <span style="color: #0066bb; font-weight: bold;">saxpy</span>(<span style="color: #333399; font-weight: bold;">int</span> n, <span style="color: #333399; font-weight: bold;">float</span> a, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>y)
{
  <span style="color: #333399; font-weight: bold;">int</span> i <span style="color: #333333;">=</span> blockIdx.x<span style="color: #333333;">*</span>blockDim.x <span style="color: #333333;">+</span> threadIdx.x;
  <span style="color: #008800; font-weight: bold;">if</span> (i <span style="color: #333333;">&lt;</span> n) y[i] <span style="color: #333333;">=</span> a<span style="color: #333333;">*</span>x[i] <span style="color: #333333;">+</span> y[i];
}

<span style="color: #333399; font-weight: bold;">int</span> <span style="color: #0066bb; font-weight: bold;">cuda_kernel</span>(<span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>x, <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>y, <span style="color: #333399; font-weight: bold;">int</span> N)
{
  <span style="color: #333399; font-weight: bold;">float</span> <span style="color: #333333;">*</span>d_x, <span style="color: #333333;">*</span>d_y;

  cudaMalloc(<span style="color: #333333;">&amp;</span>d_x, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>)); 
  cudaMalloc(<span style="color: #333333;">&amp;</span>d_y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>));

  cudaMemcpy(d_x, x, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyHostToDevice);
  cudaMemcpy(d_y, y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyHostToDevice);

  <span style="color: #888888;">// Perform SAXPY on 1M elements</span>
  saxpy<span style="color: #333333;">&lt;&lt;&lt;</span>(N<span style="color: #333333;">+</span><span style="color: #0000dd; font-weight: bold;">255</span>)<span style="color: #333333;">/</span><span style="color: #0000dd; font-weight: bold;">256</span>, <span style="color: #0000dd; font-weight: bold;">256</span><span style="color: #333333;">&gt;&gt;&gt;</span>(N, <span style="color: #6600ee; font-weight: bold;">2.0f</span>, d_x, d_y);

  cudaMemcpy(y, d_y, N<span style="color: #333333;">*</span><span style="color: #008800; font-weight: bold;">sizeof</span>(<span style="color: #333399; font-weight: bold;">float</span>), cudaMemcpyDeviceToHost);

  cudaFree(d_x);
  cudaFree(d_y);
}
</pre>
</td></tr>
</tbody></table>
</div>

<p style='text-align: justify'>
Saving the callee CUDA codes above as, e. g., 'callee.cu'.

<br />

Then use the Makefile provided below to compile the executable,
</p>

<div align="right">
<button onclick="javascript:copytoclipboard('csp4')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: rgb(255, 255, 255); border: solid gray; height: auto; overflow: auto; padding: 0.0em 0.0em; width: auto;">
<table><tbody>
<tr><td><pre style="color: grey; line-height: 125%; margin-bottom: 0px; margin-right: 5px; margin-top: 0px;"> 1
 2
 3
 4
 5
 6
 7
 8
 9
10
11
12
13
14</pre>
</td><td id="csp4"><pre style="line-height: 125%; margin: 0px;"><span style="color: #996633;">CC</span><span style="color: #333333;">=</span>gcc
<span style="color: #996633;">NCC</span><span style="color: #333333;">=</span>nvcc

<span style="color: #996633;">OBJ</span><span style="color: #333333;">=</span>caller.o
<span style="color: #996633;">CUDA_OBJ</span><span style="color: #333333;">=</span>callee.o

<span style="color: #0066bb; font-weight: bold;">call_cuda</span><span style="color: #333333;">:</span> <span style="color: #6600ee; font-weight: bold;">$(OBJ) $(CUDA_OBJ)</span>
  <span style="color: #008800; font-weight: bold;">$(</span>CC<span style="color: #008800; font-weight: bold;">)</span> -o <span style="color: #996633;">$@</span> <span style="color: #996633;">$^</span>

<span style="color: #008800; font-weight: bold;">$(</span>OBJ<span style="color: #008800; font-weight: bold;">)</span>: %.o: %.c
  <span style="color: #008800; font-weight: bold;">$(</span>CC<span style="color: #008800; font-weight: bold;">)</span> -c <span style="color: #996633;">$&lt;</span>

<span style="color: #008800; font-weight: bold;">$(</span>CUDA_OBJ<span style="color: #008800; font-weight: bold;">)</span>: %.o: %.cu
   <span style="color: #008800; font-weight: bold;">$(</span>NCC<span style="color: #008800; font-weight: bold;">)</span> -c <span style="color: #996633;">$&lt;</span>
</pre>
</td></tr>
</tbody></table>
</div>

<p style='text-align: justify'>
Basically, during compiling, we can treat the CUDA codes just as normal C codes. Also, as usual, when library is used in the CUDA codes, we need to link to the libraries as we do for normal C codes (or whatever relevant codes, e. g., Fortran).

<br />

In the following blog, I will note down a slightly more complicated case - matrix multiplication with CUDA.
</p>

<br />

<b>References</b>

[1] [https://devblogs.nvidia.com/easy-introduction-cuda-c-and-c/](https://devblogs.nvidia.com/easy-introduction-cuda-c-and-c/)