---
layout: post
title: FAQs about Mantid &ndash; Yuanpeng's Collection
subtitle:
tags: [tool, programming]
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
<img src="/assets/img/posts/mantid.png"
   style="border:none;"
   width="200"
   alt="mantid"
   title="mantid" />
</p>

`Mantid` is framework for processing and analyzing neutron and muon scattering data. Eevery now and then, I found myself having difficulties in using some of the algorithms in `Mantid` and every time I keep forgetting what the solution is. So, in this blog, I will try to note down the solution to those commonly encountered difficulties that I came across along the way, presented in the FAQ format.

<br>

<p class="faq-container">
    <a class="faq-item">
        <a class="faq-question" id="faq1">▶️ How to do the time filtering and save the filtered workspace properly?</a>
        <span class="faq-answer">
            <a href="#faq1"><b># Answer</b></a>: Time filtering can be done with the following snippet, by specifying the absolute time for start and end. Relative time can be used as well, but it seems that the algorithm is not behaving as documented in the `Mantid` documentation [1] -- when an integer is used as the input for the `StopTime` parameter, it seems that the algorithm still treat it as `seconds` but not `nanoseconds` as given in the documentation. 
            <br>
            <span class="callout multiline-span">
                &nbsp;FilterByTime(
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;InputWorkspace='dia',
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;OutputWorkspace='wsFiltered',
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;AbsoluteStartTime="2024-08-21T13:30:00",
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;AbsoluteStopTime="2024-08-21T14:00:00"
                &nbsp;    <br>
                &nbsp;)
            </span>
            To save the filtered to a NeXus file to be used later outside the currently running `Mantid Workbench` instance, we can use the following snippet,
            <span class="callout multiline-span">
                &nbsp;SaveNexusProcessed(
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;InputWorkspace="wsFiltered",
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;Filename="/SNS/NOM/IPTS-33585/shared/dia_filtered/diamond_198917.nxs",
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;Title="diamond",
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;Append=False,
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;PreserveEvents=True,
                &nbsp;    <br>
                &nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;WorkspaceIndexList=range(mtd["wsFiltered"].getNumberHistograms())
                &nbsp;    <br>
                &nbsp;)
            </span>
            The critical input parameter here for running the `SaveNexusProcessed` algorithm is `WorkspaceIndexList`. It seems that if we don't specify it explicitly, the algorithm would save the workspace to a very small file, and later on when we try to load in the saved NeXus file, `Mantid` would crash, throwing out `segmentation fault` error.
        </span>
    </a>
</p>

<p class="faq-container">
    <a class="faq-item">
        <a class="faq-question" id="faq2">▶️ The local build of Mantid framework sometimes odes not work well with the local build of other callers.</a>
        <span class="faq-answer">
            <a href="#faq2"><b># Answer</b></a>: The issue that I frequently encounter is with `MantidTotalScattering (MTS)`. The following steps were followed to set up the local build of `MTS` to use the local build of Mantid framework,
            <br>
            <span class="callout multiline-span">
                &nbsp; 1. virtualenv -p MANTID-DEVELOPER_ENV_LOCATION/bin/python --system-site-packages .venv
                &nbsp; 2. source .venv/bin/activate
                &nbsp; 3. python MANTID_REPO_DIR/build/bin/AddPythonPath.py
                &nbsp; 4. pip install -r requirements.txt -r requirements-dev.txt
                &nbsp; 5. python setup.py develop
            </span>
            Details about the instruction can be found [here](https://github.com/neutrons/mantid_total_scattering?tab=readme-ov-file#development). Usually, we can follow the instructions [here](https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#setup-the-mantid-conda-environment) for building the Mantid framework locally which would work well with the local build of `MTS` as detailed above. However, in the case of having a major upgrade of the Mantid developer environment (e.g., the Python version changed from 3.10 to 3.11), running through the instructions above from scratch (i.e., delete `.venv` and start again) would still not make it working. In this case, we have to remove the Mantid developer environment by running `conda remove -n mantid-developer --all` and re-configure the `mantid-developer` environment and run the instructions above from scratch so everything is synced.
        </span>
    </a>
</p>

<script>
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            question.nextElementSibling.style.display = question.nextElementSibling.style.display === 'block' ? 'none' : 'block';
        });
    });
</script>

<br>

References
===

[1] [Documentation for `Mantid` FilterByTime algorithm](https://docs.mantidproject.org/nightly/algorithms/FilterByTime-v1.html)