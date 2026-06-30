---
layout: post
title: Output of the Reduced Time-of-Flight Powder Diffraction Bragg Data
subtitle:
tags: [data reduction, crystallography]
author: Yuanpeng Zhang
comments: true
use_math: true
---

<p align='center'>
<img src="/assets/img/posts/bragg_software.png"
   style="border:none;"
   width="400"
   alt="bragg_software"
   title="bragg_software" />
</p>

Fundamentally, the Bragg diffraction data that we will be using for the Rietveld analysis is nothing different as compared to the total scattering data -- it is just that for the Bragg data analysis, we focus only on the Bragg peaks, treating the diffuse scattering signal as purely the background. So, in principle, they should follow the same process of data reduction [1] and the reduced data should be output with the same form to keep consistent. For sure, due to the possible misalignment between the sample and the calibrant (e.g., diamond), we would normally need a second calibration while we are doing the Rietveld refinement, commonly using a standard silicon sample, with more calibration parameters as compared to the initial calibration for the total scattering data. As such, we would need the Bragg diffraction data output in time-of-flight (TOF) space. But that is just a unit conversion step -- once we successfully reduce the total scattering data and make sure it comes in its normalized form (i.e., the normalized $$S(Q)$$ which should go to 1 at the high $$Q$$ region), we can always convert the $$Q$$-space data to TOF-space using an arbitrary `DIFC` constant and in our Rietveld refinement process, we then worry about the proper calibration constant to be used for converting the TOF data into $$d$$- (or, $$q$$-) space.

<br>

However, in practice, quite often the Bragg diffraction data is not produced with the same procedure as used for the total scattering data production. Instead, only the necessary background subtraction and probably a vanadium normalization will be applied to produce the Bragg data, without worrying about the normalization process. This is fine for Bragg data analysis as 1) we don't care too much about the absolute scaling of the data, and 2) we don't care too much about the background since it is just the background anyways. One question,to ask is, though, what harm do we have if we produce the normalized version of the Bragg data with all the necessary corrections in place, as what we do for reducing the total scattering data? The answer is obvious -- no harm `at all`... It turns out to be just a convention thing, and to be honest, output Bragg data in its unnormalized form actually makes it impossible to compare in between different instruments, or even in between different banks for the same sample measured on the same instrument. Although, this is not a real harm that the unnormalized data incorporates -- as we said, we don't really care too much about the background and scaling of the data for a Rietveld analysis -- still it does not make any obvious sense why we choose the unnormalized version of the Bragg data against the normalized version which brings in only benefit but not harm `at all`...

<br>

Another thing to pay attention to when output the Bragg diffraction data is that different data analysis software does take different form of data. By this, not only we mean the format of the data file, but also the actual form of the data itself. For example, GSAS (I and II) takes the histogram data as the input and internally it will convert the data into distribution via dividing by the bin width. However, Topas takes in directly the distribution data and therefore the input data for Topas should already have the bin width divided out. This requires the developers for data reduction to pay attention to the bin width issue while producing the Bragg data output for different software. Depending on whether the internally reduced data is already a distribution data or not, we need to worry about whether multiplying the bin width, e.g., for producing the output Bragg data for GSAS (I or II). The `Mantid` algorithm `SaveGSS` [2] for the Bragg data output does come with a `MultiplyByBinWidth` parameter to control whether multiplying the output by the bin width. For GSAS data file, the bin width information is also necessary to be in the header line for each bank since the program will need it for converting the data internally to distribution type of data. The bin width is given as a constant which is basically $$\Delta T / T$$, which corresponds to a constant binning for the logarithmic axis. To see this, we simply have,

$$
d\,ln\, T = \frac{d\,T}{T}
$$

so that,

$$
\Delta ln\,T = Const = \frac{\Delta T}{T}
$$

Therefore, for each TOF data point in the Bragg output, we can use the $$\Delta T / T$$ constant for the corresponding bank to figure out the bin width that we need to divide or multiply by to convert in between the histogram and distribution type of data.

<br>

References
===

[1] [Documentation for total scattering data reduction with `MantidTotalScattering` framework](https://powder.ornl.gov/total_scattering/data_reduction/princple_implementation.html)

[2] [Documentation for the `Mantid` algorithm `SaveGSS`](https://docs.mantidproject.org/nightly/algorithms/SaveGSS-v1.html)