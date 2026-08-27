---
layout: page
title: Yuanpeng Zhang
subtitle: Neutron Scattering Scientist
---

About Me
===

<!-- <div align='center' class='card' data-tilt data-tilt-scale="0.9">
<img src="/assets/img/ornl.jpg"
   alt="SNS"
   title="SNS" />
   <img src="/assets/img/SNS.jpg" class="img-top" alt="ORNL">
<div class="centered-text"><h1>Spallation Neutron Source</h1></div>
<div class="centered-text-1"><h1>Oak Ridge National Laboratory</h1></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>

<p style='text-align: center;color: red; font-size: 25px;'>
<b>Hover your mouse over the picture to see something interesting</b>
</p> -->



<div style="text-align: center;">
  <img src="/assets/img/ai_human.png" alt="AI Human" style="max-width: 100%; height: auto;" />
</div>

<p style='text-align: justify;'>
I am a neutron scattering scientist specialized in powder diffraction. My research focuses on the employment of neutron scattering for the atomic level structure studies of functional materials, such as those for energy storage, magnetoelectrics, and among others. I also apply the state-of-the-art AI/ML method, algorithm and framework in the structure-property link studies, such as the combination of neutron diffraction and machine learning force field (MLFF) for structure model refinement, the combination of exhaustive symmetry search and supervised machine learning for phase transition studies. Meanwhile, I have been actively involved in software development to support the neutron powder diffraction data processing and analysis, such as the ADDIE environment for neutron total scattering data reduction, ADDIE web interface for structure mining and web-based Bragg and pair distribution function (PDF) fitting, RMCProfile package for fitting scattering data based on supercell approach.
</p>

<br />

<p style='text-align: justify;'>
I am a member of American Crystallography Association (ACA) and the International Centre for Diffraction Data (ICDD). I am also serving as the guest editor for the Materials journal and I has been serving as reviewers for various peer review journals such as Advanced Science, Angew Chemie, Physical Review B, Physical Review M, etc.
</p>

<div id="codestats-widget" style="max-width: 1000px; margin: 2rem auto; padding: 1.5rem; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
  <h3 style="margin-top: 0; text-align: center;">Code::Stats</h3>
  <p style="text-align: center; margin: 0.5rem 0;">
    Total XP: <strong id="cs-total-xp">…</strong>
  </p>
  <p style="text-align: center; margin: 0.5rem 0; font-size: 0.9em; color: #888;">
    +<span id="cs-new-xp">…</span> XP in last 12h
  </p>
  <div id="cs-languages" style="margin-top: 1rem;"></div>
  <p style="text-align: center; margin-top: 1rem;">
    <a href="https://codestats.net/users/apw247" target="_blank">View full profile →</a>
  </p>
</div>

<script>
fetch('https://codestats.net/api/users/apw247')
  .then(res => res.json())
  .then(data => {
    document.getElementById('cs-total-xp').textContent = data.total_xp.toLocaleString();
    document.getElementById('cs-new-xp').textContent = data.new_xp.toLocaleString();

    const topLangs = Object.entries(data.languages)
      .sort((a, b) => b[1].xps - a[1].xps)
      .slice(0, 5);

    const maxXp = topLangs[0][1].xps;
    const container = document.getElementById('cs-languages');

    topLangs.forEach(([lang, stats]) => {
      const pct = (stats.xps / maxXp) * 100;
      const row = document.createElement('div');
      row.style.margin = '0.4rem 0';
      row.innerHTML = `
        <div style="display: flex; justify-content: space-between; font-size: 0.85em;">
          <span>${lang}</span>
          <span>${stats.xps.toLocaleString()} XP</span>
        </div>
        <div style="background: #eee; border-radius: 4px; height: 6px; overflow: hidden;">
          <div style="background: #4a90d9; height: 100%; width: ${pct}%;"></div>
        </div>
      `;
      container.appendChild(row);
    });
  })
  .catch(err => {
    document.getElementById('codestats-widget').innerHTML = '<p style="text-align:center;">Could not load Code::Stats data.</p>';
  });
</script>

More
===

- <a target="_blank" href="https://scholar.google.com/citations?user=NgqIgO0AAAAJ&hl=en">Yuanpeng's Google scholar</a>

- <a target="_blank" href="https://orcid.org/0000-0003-4224-3361">Yuanpeng's ORCID</a>

- <a target="_blank" href="https://dh.iris-home.net">Yuanpeng's Dashboard</a>

- <a target="_blank" href="https://ha.iris-2020.us">Yuanpeng's Collection</a>

- <a target="_blank" href="https://glance.iris-2020.us">Yuanpeng's Glance</a>

- <a target="_blank" href="https://github.com/Kvieta1990">Yuanpeng's GitHub</a>

- <a target="_blank" href="https://rmcprofile.ornl.gov/">RMCProfile website</a>

- <a target="_blank" href="https://mybinder.org/v2/gh/Kvieta1990/Jup_Notes/master">Binder for Jupyter notebooks</a>

- <a target="_blank" href="https://github.com/Kvieta1990/Iris">Learning notes on GitHub</a>

- <a target="_blank" href="https://github.com/Kvieta1990/Kvieta1990.github.io">GitHub repo for current blog</a>