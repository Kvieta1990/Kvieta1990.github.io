---
layout: post
title: Installation of GSAS-II on Linux
subtitle:
tags: [scattering, crystallography, programming, dev, technical, diffraction]
author: Yuanpeng Zhang
comments: true
use_math: true
---

GSAS-II is a powerful software for analyzing the crystallographic structure from diffraction data. It is written in Python, with some underlying routines in Fortran and C++. Conveniently, it provides the scriptable interface so that we can perform the refinement in a programming way. This is pretty handy when some batch processing is in need.

The scriptable way of running GSAS-II refinement is basically about writing a Python script, which involves setting up the fitting recipe and conduct the refinement. Detailed documentation can be found in Ref. [1]. To run the scriptable refinement, we need to first install GSAS-II on the machine. Typical installation instructions can be found in Ref. [2]. For Windows and MacOS, there shouldn't be that much issue in the installation. For Linux, due to the wild variations of Linux flavors (Ubuntu, Centos, OpenSUSE, Arch, Manjaro, Debian, Fedora, you name it...), it is very difficult to come up with a uniform installation solution suitable for all platforms. Though, it is possible to build the codes from the source which is probably the most generic solution. Detailed instructions can be found in Ref. [3].

Once GSAS-II is successfully installed, we want to find out where the codes are installed. For example, on my Linux machine, GSAS-II was installed following Ref. [3] and the installation location is `/home/y8z/Dev/gsasii/GSAS-II`. Inside the directory, I could see the following file tree,

```
.
├── backcompat
├── docs
├── GSASII
├── GSASII-bin
├── LICENSE
├── meson.build
├── noxfile.py
├── pixi
├── pyproject.toml
├── README.md
├── sources
└── tests
```

Now, we can create a conda environment (`mamba` can be used as well, to replace all `conda` instances in the following commands) and install some modules (not all of them will be needed but it is safe to install them all),

```bash
conda create -n gsasii_dev
conda activate gsasii_dev
conda install python numpy matplotlib wxpython pyopengl scipy git gitpython PyCifRW pillow conda requests hdf5 h5py imageio zarr xmltodict pybaselines seekpath pywin32 -c conda-forge -y
```

Next, we prepare the Python script for running the GSAS-II refinement, and with the conda environment active from previous step, we should be able to run the script. In the script, we need to provide the full path to where the GSAS-II scriptable file is located -- see the example below.

> N.B. In my case, my path inserting line would be `sys.path.insert(0, '/home/y8z/Dev/gsasii/GSAS-II/GSASII')`.

<div align="right">
<button onclick="javascript:copytoclipboard('csp1')" style="border: none">Copy snippet to clipboard!</button>
</div>
<div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;"><table><tr><td><pre style="margin: 0; line-height: 125%;">  1
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
 42
 43
 44
 45
 46
 47
 48
 49
 50
 51
 52
 53
 54
 55
 56
 57
 58
 59
 60
 61
 62
 63
 64
 65
 66
 67
 68
 69
 70
 71
 72
 73
 74
 75
 76
 77
 78
 79
 80
 81
 82
 83
 84
 85
 86
 87
 88
 89
 90
 91
 92
 93
 94
 95
 96
 97
 98
 99
100
101
102
103
104
105
106
107
108
109
110
111
112
113
114
115
116
117
118
119
120
121
122
123
124
125
126
127
128
129
130
131
132
133
134
135
136
137
138
139
140
141
142
143
144
145
146
147
148
149
150
151
152
153
154
155
156
157
158
159
160
161
162
163
164
165
166
167
168
169
170
171
172
173
174
175
176
177
178
179
180
181
182
183
184
185
186
187
188
189
190
191
192
193
194
195
196
197
198
199
200
201
202
203
204
205
206
207
208
209
210
211
212
213
214
215
216
217
218
219
220
221
222
223
224
225
226
227
228
229
230
231
232
233
234
235
236
237
238
239
240
241
242
243
244
245
246
247
248
249
250
251
252
253
254
255
256
257
258
259
260
261
262
263
264
265
266
267
268
269
270
271
272
273
274
275
276
277
278
279
280
281
282
283
284
285
286
287
288
289
290
291
292
293
294
295
296
297
298
299
300
301
302
303</pre></td><td id="csp1"><pre style="margin: 0; line-height: 125%;"><span></span><span style="color: #080; font-weight: bold">import</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">os</span>
<span style="color: #080; font-weight: bold">import</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">sys</span>
<span style="color: #080; font-weight: bold">import</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">numpy</span><span style="color: #BBB"> </span><span style="color: #080; font-weight: bold">as</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">np</span>
sys<span style="color: #333">.</span>path<span style="color: #333">.</span>insert(<span style="color: #00D; font-weight: bold">0</span>, <span style="background-color: #FFF0F0">&#39;/full/path/to/GSAS-II/GSASII&#39;</span>)
<span style="color: #080; font-weight: bold">import</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">GSASIIscriptable</span><span style="color: #BBB"> </span><span style="color: #080; font-weight: bold">as</span><span style="color: #BBB"> </span><span style="color: #0E84B5; font-weight: bold">G2sc</span>  <span style="color: #888"># noqa: E402</span>

<span style="color: #888"># +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+</span>
<span style="color: #888"># Input parameters</span>
<span style="color: #888"># +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+</span>
gpx_loc <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;/full/path/to/where/gsasii/project/file/will/be/saved/&quot;</span>
structure_fn <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;/full/path/to/structure.cif&quot;</span>
gsa_fn <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;/full/path/to/gsa/data/file&quot;</span>
prm_fn <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;/full/path/to/instrument/parameter/file&quot;</span>
output_stem_fn <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;output_stem&quot;</span>
stype <span style="color: #333">=</span> <span style="background-color: #FFF0F0">&quot;N&quot;</span>
bank <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">5</span>
xmin <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">300</span>
xmax <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">16667</span>
num_banks <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">4</span>
xmin_all <span style="color: #333">=</span> [<span style="color: #00D; font-weight: bold">300</span>, <span style="color: #00D; font-weight: bold">1500</span>, <span style="color: #00D; font-weight: bold">2000</span>, <span style="color: #00D; font-weight: bold">3500</span>]
xmax_all <span style="color: #333">=</span> [<span style="color: #00D; font-weight: bold">9000</span>, <span style="color: #00D; font-weight: bold">10000</span>, <span style="color: #00D; font-weight: bold">12000</span>, <span style="color: #00D; font-weight: bold">16667</span>]
<span style="color: #888"># +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+</span>


<span style="color: #080; font-weight: bold">def</span><span style="color: #BBB"> </span><span style="color: #06B; font-weight: bold">run_gsas2_fit</span>(
        structure_fn, gsa_fn, prm_fn, output_stem_fn,
        stype, bank, xmin, xmax):
<span style="color: #BBB">    </span><span style="color: #D42">&#39;&#39;&#39;</span>
<span style="color: #D42">    Parameters</span>
<span style="color: #D42">    ----------</span>
<span style="color: #D42">    structure_fn: str</span>
<span style="color: #D42">        input structure cif filename.</span>
<span style="color: #D42">    gsa_fn: str</span>
<span style="color: #D42">        input gsa filename.</span>
<span style="color: #D42">    instprm_fn: str</span>
<span style="color: #D42">        input instrument profile filename.</span>
<span style="color: #D42">    output_stem_fn: str</span>
<span style="color: #D42">        output stem filename.</span>
<span style="color: #D42">    banks: str</span>
<span style="color: #D42">        bank 1-6.</span>

<span style="color: #D42">    Returns</span>
<span style="color: #D42">    -------</span>
<span style="color: #D42">    gsas2_poj : str</span>
<span style="color: #D42">        gsas2 .gpx project file</span>
<span style="color: #D42">    &#39;&#39;&#39;</span>
    <span style="color: #080; font-weight: bold">def</span><span style="color: #BBB"> </span><span style="color: #06B; font-weight: bold">HistStats</span>(gpx):
<span style="color: #BBB">        </span><span style="color: #D42">&#39;&#39;&#39;prints profile rfactors for all histograms&#39;&#39;&#39;</span>
        <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">u&quot;*** profile Rwp, &quot;</span> <span style="color: #333">+</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>split(gpx<span style="color: #333">.</span>filename)[<span style="color: #00D; font-weight: bold">1</span>])
        <span style="color: #080; font-weight: bold">for</span> hist <span style="color: #000; font-weight: bold">in</span> gpx<span style="color: #333">.</span>histograms():
            <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;</span><span style="color: #666; font-weight: bold; background-color: #FFF0F0">\t</span><span style="background-color: #EEE">{:20s}</span><span style="background-color: #FFF0F0">: </span><span style="background-color: #EEE">{:.2f}</span><span style="background-color: #FFF0F0">&quot;</span><span style="color: #333">.</span>format(hist<span style="color: #333">.</span>name, hist<span style="color: #333">.</span>get_wR()))
        <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;&quot;</span>)

    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;INFO: Build GSAS-II Project File.&quot;</span>)
    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;******************************&quot;</span>)

    <span style="color: #888"># start GSAS-II refinement</span>
    <span style="color: #888"># create a project file</span>
    init_gpx <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc, output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_initial.gpx&quot;</span>
    )
    <span style="color: #080; font-weight: bold">if</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>exists(init_gpx):
        os<span style="color: #333">.</span>remove(init_gpx)
    gpx <span style="color: #333">=</span> G2sc<span style="color: #333">.</span>G2Project(newgpx<span style="color: #333">=</span>init_gpx)

    <span style="color: #888"># add a bank histogram to the project</span>
    hists <span style="color: #333">=</span> []
    <span style="color: #080; font-weight: bold">if</span> stype <span style="color: #333">==</span> <span style="background-color: #FFF0F0">&quot;N&quot;</span>:
        hist1 <span style="color: #333">=</span> gpx<span style="color: #333">.</span>add_powder_histogram(
            gsa_fn, prm_fn, fmthint<span style="color: #333">=</span><span style="background-color: #FFF0F0">&quot;GSAS powder&quot;</span>,
            databank<span style="color: #333">=</span>bank, instbank<span style="color: #333">=</span>bank
        )
        hist1<span style="color: #333">.</span>set_refinements({<span style="background-color: #FFF0F0">&#39;Limits&#39;</span>: [xmin, xmax]})
    <span style="color: #080; font-weight: bold">if</span> stype <span style="color: #333">==</span> <span style="background-color: #FFF0F0">&quot;X&quot;</span>:
        hist1 <span style="color: #333">=</span> gpx<span style="color: #333">.</span>add_powder_histogram(
            gsa_fn,
            prm_fn,
            fmthint<span style="color: #333">=</span><span style="background-color: #FFF0F0">&quot;GSAS powder&quot;</span>
        )
        hist1<span style="color: #333">.</span>set_refinements({<span style="background-color: #FFF0F0">&#39;Limits&#39;</span>: [xmin, xmax]})

    hists<span style="color: #333">.</span>append(hist1)

    <span style="color: #888"># step 2: add a phase and link it to the previous histograms</span>
    _ <span style="color: #333">=</span> gpx<span style="color: #333">.</span>add_phase(
        structure_fn,
        phasename<span style="color: #333">=</span><span style="background-color: #FFF0F0">&#39;structure&#39;</span>,
        fmthint<span style="color: #333">=</span><span style="background-color: #FFF0F0">&#39;CIF&#39;</span>,
        histograms<span style="color: #333">=</span>hists
    )

    cell_i <span style="color: #333">=</span> gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>get_cell()

    <span style="color: #888"># step 3: increase # of cycles to improve convergence</span>
    gpx<span style="color: #333">.</span>data[<span style="background-color: #FFF0F0">&#39;Controls&#39;</span>][<span style="background-color: #FFF0F0">&#39;data&#39;</span>][<span style="background-color: #FFF0F0">&#39;max cyc&#39;</span>] <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">5</span>

    <span style="color: #888"># step 4: start refinement</span>
    <span style="color: #888"># refinement step 1: turn on  Histogram Scale factor</span>
    refdict1 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Sample Parameters&quot;</span>: [<span style="background-color: #FFF0F0">&quot;Scale&quot;</span>]
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
    }
    <span style="color: #888"># refinement step 2: turn on background refinement (Hist)</span>
    refdict2 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Background&quot;</span>: {
                <span style="background-color: #FFF0F0">&quot;type&quot;</span>: <span style="background-color: #FFF0F0">&quot;chebyschev&quot;</span>,
                <span style="background-color: #FFF0F0">&quot;no. coeffs&quot;</span>: <span style="color: #00D; font-weight: bold">6</span>,
                <span style="background-color: #FFF0F0">&quot;refine&quot;</span>: <span style="color: #080; font-weight: bold">True</span>
            }
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
    }
    <span style="color: #888"># refinement step 3: refine lattice parameter and Uiso refinement (Phase)</span>
    refdict3 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Atoms&quot;</span>: {<span style="background-color: #FFF0F0">&quot;all&quot;</span>: <span style="background-color: #FFF0F0">&quot;U&quot;</span>},
            <span style="background-color: #FFF0F0">&quot;Cell&quot;</span>: <span style="color: #080; font-weight: bold">True</span>
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
    }

    dictList <span style="color: #333">=</span> [refdict1, refdict2, refdict3]

    <span style="color: #888"># before fit, save project file first. Then in the future,</span>
    <span style="color: #888"># the refined project file will update this one.</span>
    ref_gpx <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc, output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_refined.gpx&quot;</span>
    )
    gpx<span style="color: #333">.</span>save(ref_gpx)

    gpx<span style="color: #333">.</span>do_refinements(dictList)
    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;================&quot;</span>)

    <span style="color: #888"># save results data</span>

    rw <span style="color: #333">=</span> gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>get_wR() <span style="color: #333">*</span> <span style="color: #60E; font-weight: bold">0.01</span>
    x <span style="color: #333">=</span> np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;X&#39;</span>))
    y <span style="color: #333">=</span> np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Yobs&#39;</span>))
    ycalc <span style="color: #333">=</span> np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Ycalc&#39;</span>))
    dy <span style="color: #333">=</span> np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Residual&#39;</span>))
    bkg <span style="color: #333">=</span> np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(<span style="color: #00D; font-weight: bold">0</span>)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Background&#39;</span>))

    output_cif_fn <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc,
        output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_refined.cif&quot;</span>)

    gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>export_CIF(output_cif_fn)
    cell_r <span style="color: #333">=</span> gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>get_cell()

    <span style="color: #080; font-weight: bold">return</span> rw, x, y, ycalc, dy, bkg, cell_i, cell_r


<span style="color: #080; font-weight: bold">def</span><span style="color: #BBB"> </span><span style="color: #06B; font-weight: bold">run_gsas2_fit_all</span>(
        structure_fn, gsa_fn, prm_fn, output_stem_fn,
        stype, num_banks, xmin_all, xmax_all):
<span style="color: #BBB">    </span><span style="color: #D42">&#39;&#39;&#39;</span>
<span style="color: #D42">    Parameters</span>
<span style="color: #D42">    ----------</span>
<span style="color: #D42">    structure_fn: str</span>
<span style="color: #D42">        input structure cif filename.</span>
<span style="color: #D42">    gsa_fn: str</span>
<span style="color: #D42">        input gsa filename.</span>
<span style="color: #D42">    instprm_fn: str</span>
<span style="color: #D42">        input instrument profile filename.</span>
<span style="color: #D42">    output_stem_fn: str</span>
<span style="color: #D42">        output stem filename.</span>
<span style="color: #D42">    banks: str</span>
<span style="color: #D42">        bank 1-6.</span>

<span style="color: #D42">    Returns</span>
<span style="color: #D42">    -------</span>
<span style="color: #D42">    gsas2_poj : str</span>
<span style="color: #D42">        gsas2 .gpx project file</span>
<span style="color: #D42">    &#39;&#39;&#39;</span>
    <span style="color: #080; font-weight: bold">def</span><span style="color: #BBB"> </span><span style="color: #06B; font-weight: bold">HistStats</span>(gpx):
<span style="color: #BBB">        </span><span style="color: #D42">&#39;&#39;&#39;prints profile rfactors for all histograms&#39;&#39;&#39;</span>
        <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">u&quot;*** profile Rwp, &quot;</span> <span style="color: #333">+</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>split(gpx<span style="color: #333">.</span>filename)[<span style="color: #00D; font-weight: bold">1</span>])
        <span style="color: #080; font-weight: bold">for</span> hist <span style="color: #000; font-weight: bold">in</span> gpx<span style="color: #333">.</span>histograms():
            <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;</span><span style="color: #666; font-weight: bold; background-color: #FFF0F0">\t</span><span style="background-color: #EEE">{:20s}</span><span style="background-color: #FFF0F0">: </span><span style="background-color: #EEE">{:.2f}</span><span style="background-color: #FFF0F0">&quot;</span><span style="color: #333">.</span>format(hist<span style="color: #333">.</span>name, hist<span style="color: #333">.</span>get_wR()))
        <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;&quot;</span>)

    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;INFO: Build GSAS-II Project File.&quot;</span>)
    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;******************************&quot;</span>)

    <span style="color: #888"># start GSAS-II refinement</span>
    <span style="color: #888"># create a project file</span>
    init_gpx <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc, output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_initial.gpx&quot;</span>
    )
    <span style="color: #080; font-weight: bold">if</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>exists(init_gpx):
        os<span style="color: #333">.</span>remove(init_gpx)
    gpx <span style="color: #333">=</span> G2sc<span style="color: #333">.</span>G2Project(newgpx<span style="color: #333">=</span>init_gpx)

    hists <span style="color: #333">=</span> []
    <span style="color: #080; font-weight: bold">if</span> stype <span style="color: #333">==</span> <span style="background-color: #FFF0F0">&quot;N&quot;</span>:
        <span style="color: #080; font-weight: bold">for</span> bank <span style="color: #000; font-weight: bold">in</span> <span style="color: #007020">range</span>(num_banks):
            hist_tmp <span style="color: #333">=</span> gpx<span style="color: #333">.</span>add_powder_histogram(
                gsa_fn, prm_fn,
                fmthint<span style="color: #333">=</span><span style="background-color: #FFF0F0">&quot;GSAS powder&quot;</span>,
                databank<span style="color: #333">=</span>bank <span style="color: #333">+</span> <span style="color: #00D; font-weight: bold">1</span>,
                instbank<span style="color: #333">=</span>bank <span style="color: #333">+</span> <span style="color: #00D; font-weight: bold">1</span>
            )
            hist_tmp<span style="color: #333">.</span>set_refinements({<span style="background-color: #FFF0F0">&#39;Limits&#39;</span>: [xmin_all[bank <span style="color: #333">+</span> <span style="color: #00D; font-weight: bold">1</span>], xmax_all[bank <span style="color: #333">+</span> <span style="color: #00D; font-weight: bold">1</span>]]})
            hists<span style="color: #333">.</span>append(hist_tmp)

    <span style="color: #888"># step 2: add a phase and link it to the previous histograms</span>
    _ <span style="color: #333">=</span> gpx<span style="color: #333">.</span>add_phase(
        structure_fn,
        phasename<span style="color: #333">=</span><span style="background-color: #FFF0F0">&#39;structure&#39;</span>,
        fmthint<span style="color: #333">=</span><span style="background-color: #FFF0F0">&#39;CIF&#39;</span>,
        histograms<span style="color: #333">=</span>hists
    )

    cell_i <span style="color: #333">=</span> gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>get_cell()

    <span style="color: #888"># step 3: increase # of cycles to improve convergence</span>
    gpx<span style="color: #333">.</span>data[<span style="background-color: #FFF0F0">&#39;Controls&#39;</span>][<span style="background-color: #FFF0F0">&#39;data&#39;</span>][<span style="background-color: #FFF0F0">&#39;max cyc&#39;</span>] <span style="color: #333">=</span> <span style="color: #00D; font-weight: bold">5</span>

    <span style="color: #888"># step 4: start refinement</span>
    <span style="color: #888"># refinement step 1: turn on  Histogram Scale factor</span>
    refdict1 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Sample Parameters&quot;</span>: [<span style="background-color: #FFF0F0">&quot;Scale&quot;</span>]
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
        <span style="background-color: #FFF0F0">&quot;histograms&quot;</span>: hists
    }
    <span style="color: #888"># refinement step 2: turn on background refinement (Hist)</span>
    refdict2 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Background&quot;</span>: {
                <span style="background-color: #FFF0F0">&quot;type&quot;</span>: <span style="background-color: #FFF0F0">&quot;chebyschev&quot;</span>,
                <span style="background-color: #FFF0F0">&quot;no. coeffs&quot;</span>: <span style="color: #00D; font-weight: bold">6</span>,
                <span style="background-color: #FFF0F0">&quot;refine&quot;</span>: <span style="color: #080; font-weight: bold">True</span>
            }
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
        <span style="background-color: #FFF0F0">&quot;histograms&quot;</span>: hists
    }
    <span style="color: #888"># refinement step 3: refine lattice parameter and Uiso refinement (Phase)</span>
    refdict3 <span style="color: #333">=</span> {
        <span style="background-color: #FFF0F0">&quot;set&quot;</span>: {
            <span style="background-color: #FFF0F0">&quot;Atoms&quot;</span>: {
                <span style="background-color: #FFF0F0">&quot;all&quot;</span>: <span style="background-color: #FFF0F0">&quot;U&quot;</span>
            },
            <span style="background-color: #FFF0F0">&quot;Cell&quot;</span>: <span style="color: #080; font-weight: bold">True</span>
        },
        <span style="background-color: #FFF0F0">&quot;call&quot;</span>: HistStats,
        <span style="background-color: #FFF0F0">&quot;histograms&quot;</span>: hists
    }

    dictList <span style="color: #333">=</span> [refdict1, refdict2, refdict3]

    <span style="color: #888"># before fit, save project file first. Then in the future,</span>
    <span style="color: #888"># the refined project file will update this one.</span>
    ref_gpx <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc, output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_refined.gpx&quot;</span>
    )
    gpx<span style="color: #333">.</span>save(ref_gpx)

    gpx<span style="color: #333">.</span>do_refinements(dictList)
    <span style="color: #007020">print</span>(<span style="background-color: #FFF0F0">&quot;================&quot;</span>)

    <span style="color: #888"># save results data</span>

    rw <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    x <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    y <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    ycalc <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    dy <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    bkg <span style="color: #333">=</span> <span style="color: #007020">list</span>()
    <span style="color: #080; font-weight: bold">for</span> bank <span style="color: #000; font-weight: bold">in</span> <span style="color: #007020">range</span>(num_banks):
        rw<span style="color: #333">.</span>append(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>get_wR() <span style="color: #333">*</span> <span style="color: #60E; font-weight: bold">0.01</span>)
        x<span style="color: #333">.</span>append(np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;X&#39;</span>)))
        y<span style="color: #333">.</span>append(np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Yobs&#39;</span>)))
        ycalc<span style="color: #333">.</span>append(np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Ycalc&#39;</span>)))
        dy<span style="color: #333">.</span>append(np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Residual&#39;</span>)))
        bkg<span style="color: #333">.</span>append(np<span style="color: #333">.</span>array(gpx<span style="color: #333">.</span>histogram(bank)<span style="color: #333">.</span>getdata(<span style="background-color: #FFF0F0">&#39;Background&#39;</span>)))

    output_cif_fn <span style="color: #333">=</span> os<span style="color: #333">.</span>path<span style="color: #333">.</span>join(
        gpx_loc,
        output_stem_fn <span style="color: #333">+</span> <span style="background-color: #FFF0F0">&quot;_refined.cif&quot;</span>
    )

    gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>export_CIF(output_cif_fn)
    cell_r <span style="color: #333">=</span> gpx<span style="color: #333">.</span>phase(<span style="background-color: #FFF0F0">&#39;structure&#39;</span>)<span style="color: #333">.</span>get_cell()

    <span style="color: #080; font-weight: bold">return</span> rw, x, y, ycalc, dy, bkg, cell_i, cell_r


<span style="color: #080; font-weight: bold">if</span> <span style="color: #963">__name__</span> <span style="color: #333">==</span> <span style="background-color: #FFF0F0">&quot;__main__&quot;</span>:
    run_gsas2_fit(
        structure_fn, gsa_fn, prm_fn, output_stem_fn,
        stype, bank, xmin, xmax
    )

    run_gsas2_fit_all(
        structure_fn, gsa_fn, prm_fn, output_stem_fn,
        stype, num_banks, xmin_all, xmax_all
    )
</pre></td></tr></table></div>


References
===

[1] [https://gsas-ii.readthedocs.io/en/latest/GSASIIscriptable.html](https://gsas-ii.readthedocs.io/en/latest/GSASIIscriptable.html)

[2] [https://advancedphotonsource.github.io/GSAS-II-tutorials/install.html](https://advancedphotonsource.github.io/GSAS-II-tutorials/install.html)

[3] [https://iris2020.net/2025-08-01-gsasii_on_linux/](https://iris2020.net/2025-08-01-gsasii_on_linux/)