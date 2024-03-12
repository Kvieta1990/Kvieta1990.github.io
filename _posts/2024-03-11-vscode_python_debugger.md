---
layout: post
title: Run Python Debugger in VSCode
subtitle:
tags: [IDE, software, tool, VSCode]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Running the debug mode for coding in IDE is a very convenient feature, as we can put in break points and check variables in our program without killing the running process. In `VSCode`, to run the debug mode - here I am taking Python as the example - we need to first download the debugger extension. For Python debugger, we can just search in the extension sore within `VSCode` and we should be able to find the Python debugger listed to be installed. Once installed, we can go to the `Run and Debug` option in the left sidebar and try to run the program in the debug mode. Initially, it will ask us to populate the configuration file, if we have never used the debug before, as shown [here](https://code.visualstudio.com/assets/docs/python/debugging/debug-start.png). Clicking on the link as shown in the picture, we will be brought up with the configuration JSON file to populate. We can put something like below in the file,

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Python Debugger: Current File",
            "type": "debugpy",
            "request": "launch",
            "python": "/Users/y8z/opt/anaconda3/envs/g2python311/bin/python",
            "program": "GSASII.py",
            "console": "integratedTerminal"
        }
    ]
}
```

Here, the `program` option will specify the relative path (with respective to the workspace folder) to the main Python file to be executed. By default, the `python` entry will not be created but it is actually necessary as it specifies the full path to the Python executable (which then determines which environment we will be using) to be used for running the Python script. Once all the entries above are populated properly, we can then hit the `Run` button in the `Run and Debug` option to start the debug mode. Here, we can first insert some breakpoints in our codes (by clicking on the left edge of the editor corresponding to a certain like and red circle will be shown to indicate the breakpoints). Then when the running program hits the breakpoint, it will bump into the debug mode, allowing us to bring up the command console -- we can use ``` ctrl+` ``` to bring up the terminal panel where we can find the `DEBUG CONSOLE` among the top option tabs in the terminal panel. We can also go to the top part of the editor to find the several options to control the flow of the debug mode, e.g., to move forward, i.e., to skip the current break point.