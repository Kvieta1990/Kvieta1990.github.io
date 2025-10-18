---
layout: post
title: Instrument Geometry in Mantid Framework
subtitle:
tags: [scattering, dev, technical, diffraction]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/mantid_logo_light.png"
   style="border:none;"
   width="300"
   alt="mantid"
   title="mantid" />
</p>

For time of flight (TOF) neutorn diffractometers, the raw data are recorded in `events` -- an `event` means a certain detector is having a neutron hitting at a certain time point. Sayting this, to pin down an event, we need two pieces of basic information, namely `time` and `location`, defining `when` and `where` the event was happening, respectively. So, the raw TOF diffraction data is nothing but a full record of all such events. In the upstream while collecting data, such event information would be written into the raw data file (these days, the NeXus format is often used). Then in the downstream, we load in the event information and construct the TOF diffraction pattern for each single detector. This may be followed by properly converting TOF to `d` or `Q` and focusing patterns from individual detectors in a certain manner to accumulate statistics. Regarding the writting and reading of those neutron events, the pivot point of connection is the insturment geometry which is something we want to talk a bit about here in the post.

At Spallation Neutron Source (SNS), ORNL, the nexus raw data file records events by time offset & zero point (from which we can figure out the time of flight) and event ID (which is basically just the detector ID). That means no detector location information would be recorded in the raw nexus data file. There comes the necessity to have an instrument definition file (IDF) which maps those detector IDs to the actual physical location of detectors. In the downstream data loading process, we then know where events are happening. Meanwhile, it is necessary to guarantee the consistent IDF is being used for both the upstream data acquisition and downstream data loading. Otherwise, it is possible that the downstream data loading process maps detector IDs to locations different from what the upstream data collection was assuming, in which case everything will be messed up.

At SNS, ORNL, most of the time, the Mantid framework would be used for data processing, where the IDF files are in the `XML` format. Ref. [1] is the GitHub respository hosting the IDF files (and scripts used for creating them) for neutron instruments that are using the Mantid framework for their data processing. Ref. [2] is the forked repo originally from Dr. [Bogdan Vacaliuc](https://www.ornl.gov/staff-profile/bogdan-vacaliuc). The forked repo contains the script for creating the IDF for the POWGEN detector upgrade test. For example, the script [here](https://github.com/Kvieta1990/mantidgeometry/blob/powgen_gen2/nowd_geometry_154x7.py) can be used to generate the IDF for the `154x7` pixelization for a single detector panel -- in the POWGEN instrument view presented below, each single rectangle region is what we mean by a 'panel' here.

<p align='center'>
<img src="/assets/img/posts/powgen_instrument_view.png"
   style="border:none;"
   width="1000"
   alt="powgen"
   title="powgen" />
</p>

In fact, the panel does not actually have physical pixels -- instead it is just a region of detectors and it is realy on us to decide how we want to pixelize the whole detector panel. The script mentioned above does the `154x7` pixelization and in Ref. [2], we can also find the script for other types of pixelization, e.g., `525x28`. Checking the IDF generation script, we can see that the script is reading in some CSV files containing some coordinates, like [here](https://github.com/Kvieta1990/mantidgeometry/blob/2897d2cfbec19ea997d8f5b8b0d692efcfbda086/nowd_geometry_154x7.py#L168). Those coordinates are the XYZ coordinates of the four corners for panels, given in a certain order. If we look from the negative to positive z direction, the order `1-4` goes like `bottom-left` $$\rightarrow$$ `buttom-right` $$\rightarrow$$ `top-right` $$\rightarrow$$ `top-left`, as shown below,

<p align='center'>
<img src="/assets/img/posts/view_direction.png"
   style="border:none;"
   width="400"
   alt="panel_view"
   title="panel_view" />
</p>

Then the script will figure out the location of each single pixel according to the intended pixelization scheme and properly number them. To change the pixelization scheme, we want to go to the lines [here](https://github.com/Kvieta1990/mantidgeometry/blob/2897d2cfbec19ea997d8f5b8b0d692efcfbda086/nowd_geometry_154x7.py#L10-L16) in the script and change the intended number of pixels along each direction, together with the pixel size. The obvious principle is that the overall panel size should not change no matter what pixelization scheme is to be used. So, depending on the pixelization scheme to use, we may have to recalculate the pixel size according to the existing values in the script. To run the script, we want to first have `mamba` installed [3]. Then we want to run,

```bash
git clone https://github.com/Kvieta1990/mantidgeometry.git
cd mantidgeometry
mamba env create -f mantidgeometry.yml
mamba activate mantidgeometry
python nowd_geometry_154x7.py
```

which will create the IDF file `NOWD_Definition_154x7.xml`, where `NOWD` is the instrument name for detector testing purpose. Unfortunately we have to manually make some changes to the generated XML file to make it working with Mantid. To demonstrate, here I will be using a simpler case, where we only have a single panel from POWGEN (bank-64 belonging to column-19 on the North side of the POWGEN instrument). For such a purpose, I simply go to the CSV file [here](https://github.com/Kvieta1990/mantidgeometry/blob/powgen_gen2/SNS/POWGEN/NOWD_geom_left_2025A.csv), delete all those panel lines, except those corresponding to the `D573` panel ([here](https://github.com/Kvieta1990/mantidgeometry/blob/2897d2cfbec19ea997d8f5b8b0d692efcfbda086/SNS/POWGEN/NOWD_geom_left_2025A.csv#L10C1-L13)) and run `nowd_geometry_154x7.py` script. What I want to do here is to load an existing nexus data collected on POWGEN using the IDF we just generated here. Before proceeding, there is some background knowledge to mention. In the nexus raw data collected on SNS diffractometers, there is an embedded IDF file and by default Mantid will be using it while loading the data. This does make a lot of sense since the simple assumption would be that the embedded IDF should be the one that was used upstream while writting events into the nexus data file. Though, Mantid does provide the flexibilty to load in the nexus data file while using a separate IDF. To use a separate IDF in Mantid for data loading, we can use the [`LoadEventNexus`](https://docs.mantidproject.org/nightly/algorithms/LoadEventNexus-v1.html) algorithm while setting `LoadNexusInstrumentXML=False`, telling the program not to use the embedded IDF in the nexus file to be loaded. When doing this, Mantid will then try to find the proper IDF to use for a specific instrument the name of which is hard coded in the nexus file. The first location in the search list will be the `instrument` directory in the Mantid code base. Depending on the Mantid framework being used, the location could vary. For example, on ORNL Analysis nodes, if the `Mantid Workbench` is being used and is to be launched via the `mantidworkbench` command, the `instrument` directory will be located at `/opt/anaconda/envs/mantid/instrument`. The second location in the search list will be `~/.mantid/instrument` where `~` represents our home directory. The second search location will only be used if a proper IDF cannot be found in the first search location, e.g., the defined instrument in the nexus file is not defined in any of the IDF inside the first search location. Another possibility is the time stamp in any of the existing IDFs in the first search location does not match the time stamp of the nexus data file.

> In a valid IDF, one of the header lines should specify the range of time period that the IDF should be applied -- see [here](https://github.com/Kvieta1990/mantidgeometry/blob/2897d2cfbec19ea997d8f5b8b0d692efcfbda086/NOWD_Definition_154x7.chk#L2).

In a moment, I will be talking about replacing the `NOWD` instrument name with `PG3` (the short name for POWGEN, at SNS) which is the instrument I want to worry about for my intended test here. But before that, there is something worthwhile mentioning. If we are going to work with the instrument name `NOWD` (which, as mentioned above, is created only for the testing purpose), the Mantid framework will not be able to find the IDF for the instrument no matter where we put it since the instrument name is not defined yet in Mantid. To get around with this, here is the trick. First, we want to put the IDF in the second search location mentioned above, i.e., `~/.mantid/instrument`, as `NOWD_Definition.xml`.

> My observation is that file names like `NOWD_Definition_2020-05-05.xml` would also be accepted but the front part `NOWD_Definition` should necessarily be there.

Then in our Mantid script, we want to use the following codes for data loading,

```python
import mantid
mantid.config.updateFacilities('~/.mantid/instrument/Facilities.xml')
LoadEventNexus(
    Filename="Full_path_to_nexus_data_file.nxs.h5",
    LoadNexusInstrumentXML=False
)
```

What we are doing here is to define a new instrument called `NOWD` in `~/.mantid/instrument/Facilities.xml` and update the facility definition through `mantid.config.updateFacilities`.

> Somewhere in the `Facilities.xml` file, we want to put in the following section sitting parallel to the existing instrument definitions,

```xml
<instrument name="NOWD">
    <technique>Neutron Diffraction</technique>
</instrument>
```

> One thing I noticed, which I am not sure is something accidentally happening or related to the facility updating above, is that the data loading in Mantid with something like `Load("PG3_61233")` may stop working (failed to find the data) after running the code above to update facilities. Anyhow, in case this is happening, we can try to reset the Mantid configuration. First, remove all files under `~/.mantid/instrument`, or we can just do `mv ~/.mantid ~/.mantid_backup`. Second, and probably optionally, we do `mv ~/.config/mantidproject ~/.config/mantidproject_backup`. Then we launch Mantid Workbench and set the facility (and probably the instrument) selection like shown below (in Mantid Workbench, we go to `Files` $$\rightarrow$$ `Settings`),

<p align='center'>
<img src="/assets/img/posts/mantid_settings.png"
   style="border:none;"
   width="800"
   alt="mantid_settings"
   title="mantid_settings" />
</p>

Getting back to our business -- as mentioned, I want to load in an existing POWGEN nexus data file but with the IDF generated by the script above, just to make sure that the generated IDF guarantees a reasonable output. By 'reasonable', I mean, we know the embedded IDF in an existing normal POWGEN run (e.g., diamond run `61233` from cycle `2025B`) is working well with the data processing routines. We want to check whether the IDF generation script mentioned in the current post which was created dedicatedly for testing out the new detectors to be installed would generate consistent output for the same panel with the same pixelization as what POWGEN is currently having. For such a testing, we cannot use the trick above, i.e., create a new instrument since as mentioned earlier, the instrument name is hard coded into the nexus raw data file. Therefore, we can only change the instrument name inside the nexus raw data, which may be possible but I don't know how. In fact, we don't have to do that. Similar trick can be used here -- while loading the data, we use the flag `LoadNexusInstrumentXML=False` for `LoadEventNexus`. Then we can replace the existing IDF for POWGEN in the primary search location for Mantid. For such a purpose, the deployed Mantid on ORNL Analysis nodes cannot be used since we don't have the write permission to the central IDF file. Instead, we have to build our own version of Mantid so we can replace the existing instrument IDF. To build Mantid locally, we first clone the source code from Ref. [4]. Then `cd` into the `mantid` source code directory and follow the steps below to build Mantid,

```bash
mamba create -n mantid-developer mantid/label/nightly::mantid-developer -c neutrons
mamba activate mantid-developer
cmake --preset=linux
ninja
```

where we are assuming that `mamba` has already been installed. After the `ninja` step, the built Mantid Workbench can be launched via,

```
./build/bin/launch_mantidworkbench.sh
```

where I am assuming we are still located in the `mantid` source code directory. In this case, we can find the IDF files for instruments definition inside the `mantid/instrument` directory. We can then copy the IDF file generated above into this directory and name it as `POWGEN_Definition.xml`. Now, we get back to the issue mentioned at the very beginning -- while running the script above to generate the IDF file, the generated version has some issues in the header part and also is inconsistent with the naming convensions in Mantid. We can refer to the following working version for a single POWGEN panel as a template and see how we can make changes on top of the generated version to make it compatible with Mantid.

```
<?xml version='1.0' encoding='ASCII'?>
<instrument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.mantidproject.org/IDF/1.0" last-modified="2019-05-03 11:30:00" name="PG3" valid-from="2018-05-05 00:00:01" valid-to="2100-01-31 23:59:59" xsi:schemaLocation="http://www.mantidproject.org/IDF/1.0 http://schema.mantidproject.org/IDF/1.0/IDFSchema.xsd">
  <!--Created by Peter Peterson, Stuart Campbell, Vickie Lynch, Janik Zikovsky-->
  <!--DEFAULTS-->
  <defaults>
    <length unit="metre"/>
    <angle unit="degree"/>
    <reference-frame>
      <along-beam axis="z"/>
      <pointing-up axis="y"/>
      <handedness val="right"/>
      <theta-sign axis="x"/>
    </reference-frame>
  </defaults>
  <!--SOURCE-->
  <component type="moderator">
    <location z="-60.0"/>
  </component>
  <type is="Source" name="moderator"/>
  <!--SAMPLE-->
  <component type="sample-position">
    <location x="0.0" y="0.0" z="0.0"/>
  </component>
  <type is="SamplePos" name="sample-position"/>
  <!--MONITORS-->
  <component idlist="monitors" type="monitors">
    <location/>
  </component>
  <type name="monitors">
    <component type="monitor">
      <location name="monitor1" z="-1.5077"/>
    </component>
  </type>
  <component type="North">
    <location/>
  </component>
  <type name="North">
    <component type="Column19">
      <location/>
    </component>
  </type>
  <type name="Column19">
    <component type="panel_v2" idstart="945000" idfillbyfirst="y" idstepbyrow="7">
      <location x="3.12909225" y="-0.002533500000000001" z="1.4632537499999998" name="bank64">
        <rot val="-227.86240522620878" axis-x="0" axis-y="1" axis-z="0">
          <rot val="-0.03482584795584898">
            <rot val="-231.36366852720275" axis-x="0" axis-y="1" axis-z="0"/>
          </rot>
        </rot>
      </location>
    </component>
  </type>
  <!-- Gen2 Detector Panel (7x154)-->
  <type name="panel_v2" is="rectangular_detector" type="pixel_v2" xpixels="154" xstart="-0.3825" xstep="0.005" ypixels="7" ystart="-0.1629" ystep="0.0543">
    <properties/>
  </type>
  <!-- Shape for Monitors-->
  <!-- TODO: Update to real shape -->
  <type name="monitor" is="monitor">
    <cylinder id="cyl-approx">
      <centre-of-bottom-base p="0.0" r="0.0" t="0.0"/>
      <axis x="0.0" y="0.0" z="1.0"/>
      <radius val="0.01"/>
      <height val="0.03"/>
    </cylinder>
    <algebra val="cyl-approx"/>
  </type>
  <!-- Pixel for Gen2 Detectors (7x154)-->
  <type name="pixel_v2" is="detector">
    <cuboid id="pixel-shape">
      <left-front-bottom-point x="-0.0025" y="-0.02715" z="0.0"/>
      <left-front-top-point x="-0.0025" y="0.02715" z="0.0"/>
      <left-back-bottom-point x="-0.0025" y="-0.02715" z="-0.0001"/>
      <right-front-bottom-point x="0.0025" y="-0.02715" z="0.0"/>
    </cuboid>
    <algebra val="pixel-shape"/>
  </type>
  <!--MONITOR IDs-->
  <idlist idname="monitors">
    <id val="-1"/>
  </idlist>
</instrument>
```

Basically, we can replace the whole top part (contents above the `<type name="Column19">` line) in the generated IDF with the top part shared above. Then if we load the nexus data like this,

```python
LoadEventNexus(
    Filename="Full_path_to_nexus_data_file.nxs.h5",
    LoadNexusInstrumentXML=False
)
```

Only the events collected on the single panel will be loaded and all the other events will be ignored.

References
===

[1] [https://github.com/mantidproject/mantidgeometry/tree/main](https://github.com/mantidproject/mantidgeometry/tree/main)

[2] [https://github.com/Kvieta1990/mantidgeometry/tree/powgen_gen2](https://github.com/Kvieta1990/mantidgeometry/tree/powgen_gen2)

[3] [https://mamba.readthedocs.io/en/latest/installation/mamba-installation.html](https://mamba.readthedocs.io/en/latest/installation/mamba-installation.html)

[4] [https://github.com/mantidproject/mantid](https://github.com/mantidproject/mantid)

[5] [https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#setup-the-mantid-conda-environment](https://developer.mantidproject.org/GettingStarted/GettingStartedCondaLinux.html#setup-the-mantid-conda-environment)