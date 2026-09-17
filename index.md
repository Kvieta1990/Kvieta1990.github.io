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



<div style="position: relative; max-width: 760px; margin: 0 auto;">
  <span id="symmetry-reminder" style="position: absolute; top: 33%; left: 50%; z-index: 1; transform: translate(-50%, -50%); color: #b00020; font-size: 1.6em; font-weight: bold; white-space: nowrap; pointer-events: none;">Hover your mouse over me</span>
  <canvas id="symmetry-canvas" width="760" height="650" role="img" aria-label="Interactive three-fold rotational, mirror, and 3_1 screw symmetry figure" style="display: block; width: 100%; height: auto; cursor: none; touch-action: none;"></canvas>
</div>

<p style="text-align: center; color: #b00020; font-style: italic; font-weight: bold">Life is all symmetric, we all have moments up and down. Though, among all images, we can always choose to look at those up pointing ones.</p>

<script>
(function () {
  const canvas = document.getElementById('symmetry-canvas');
  if (!canvas) return;
  const reminder = document.getElementById('symmetry-reminder');

  const context = canvas.getContext('2d');
  const width = canvas.width;
  const height = canvas.height;
  const center = { x: width / 2, y: height / 2 };
  const hexagonRadius = Math.min(width * 0.36, height * 0.42);
  const vertices = Array.from({ length: 6 }, function (_, index) {
    const angle = -Math.PI / 3 * index - Math.PI / 3;
    return {
      x: center.x + hexagonRadius * Math.cos(angle),
      y: center.y + hexagonRadius * Math.sin(angle)
    };
  });
  const screwCenters = vertices.map(function (vertex, index) {
    const nextVertex = vertices[(index + 1) % vertices.length];
    return {
      x: (center.x + vertex.x + nextVertex.x) / 3,
      y: (center.y + vertex.y + nextVertex.y) / 3
    };
  });
  // In canvas coordinates, positive angles are visually clockwise.
  // Centers are ordered: top-left, bottom-left, bottom, bottom-right, top-right, top.
  const screwDirections = [1, -1, 1, -1, 1, -1];
  let pointer = null;

  function rotate(point, angle) {
    return rotateAround(point, center, angle);
  }

  function rotateAround(point, pivot, angle) {
    const sine = Math.sin(angle);
    const cosine = Math.cos(angle);
    const x = point.x - pivot.x;
    const y = point.y - pivot.y;
    return {
      x: pivot.x + x * cosine - y * sine,
      y: pivot.y + x * sine + y * cosine
    };
  }

  function reflectAcrossHorizontal(point) {
    return { x: point.x, y: 2 * center.y - point.y };
  }

  function transform(point, angle) {
    return rotate(point, angle);
  }

  function nearestScrewCenter(point) {
    return screwCenters.reduce(function (nearest, screwCenter, index) {
      const nearestDistance = Math.hypot(point.x - nearest.center.x, point.y - nearest.center.y);
      const currentDistance = Math.hypot(point.x - screwCenter.x, point.y - screwCenter.y);
      return currentDistance < nearestDistance
        ? { center: screwCenter, index: index }
        : nearest;
    }, { center: screwCenters[0], index: 0 });
  }

  function trianglePoints(point, size, angle) {
    return [
      { x: point.x, y: point.y - size },
      { x: point.x - size * Math.sqrt(3) / 2, y: point.y + size * 0.5 },
      { x: point.x + size * Math.sqrt(3) / 2, y: point.y + size * 0.5 }
    ].map(function (trianglePoint) {
      return rotateAround(trianglePoint, point, angle);
    });
  }

  function drawTriangle(point, size, color, angle, fill) {
    const points = trianglePoints(point, size, angle);

    context.beginPath();
    context.moveTo(points[0].x, points[0].y);
    context.lineTo(points[1].x, points[1].y);
    context.lineTo(points[2].x, points[2].y);
    context.closePath();
    context.fillStyle = color;
    context.strokeStyle = color;
    context.lineWidth = 2;
    if (fill === false) {
      context.stroke();
    } else {
      context.fill();
    }
  }

  function drawScrewCopies(point, angle) {
    const nearest = nearestScrewCenter(point);
    const screwCenter = nearest.center;
    const direction = screwDirections[nearest.index];
    const copies = [
      { offset: { x: 0, y: -8 }, angle: direction * (2 * Math.PI) / 3, label: '+1/3' },
      { offset: { x: 0, y: -16 }, angle: direction * (4 * Math.PI) / 3, label: '+2/3' }
    ];

    copies.forEach(function (copy) {
      const copyPoint = rotateAround(point, screwCenter, copy.angle);
      copyPoint.x += copy.offset.x;
      copyPoint.y += copy.offset.y;
      drawTriangle(copyPoint, 18, '#d71920', angle + copy.angle);
      context.font = 'bold 12px sans-serif';
      context.textAlign = 'center';
      context.textBaseline = 'bottom';
      context.fillStyle = '#b0141b';
      context.fillText(copy.label, copyPoint.x, copyPoint.y - 20);
    });

    drawTriangle(point, 18, '#d71920', angle);
  }

  function draw() {
    context.clearRect(0, 0, width, height);
    context.lineWidth = 4;
    context.lineCap = 'round';
    context.strokeStyle = '#202d61';
    context.beginPath();
    context.moveTo(vertices[0].x, vertices[0].y);
    vertices.slice(1).forEach(function (vertex) {
      context.lineTo(vertex.x, vertex.y);
    });
    context.closePath();
    context.stroke();

    context.strokeStyle = '#000';
    context.lineWidth = 4;
    [[0, 3], [4, 1], [5, 2]].forEach(function (axis) {
      context.beginPath();
      context.moveTo(vertices[axis[0]].x, vertices[axis[0]].y);
      context.lineTo(vertices[axis[1]].x, vertices[axis[1]].y);
      context.stroke();
    });

    vertices.forEach(function (vertex) {
      drawTriangle(vertex, 42, '#000', 0);
    });
    drawTriangle(center, 42, '#000', 0);

    if (pointer) {
      const symmetryAngles = [0, (2 * Math.PI) / 3, (4 * Math.PI) / 3];
      symmetryAngles.forEach(function (angle) {
        drawScrewCopies(transform(pointer, angle), angle + Math.PI);
        drawScrewCopies(rotate(reflectAcrossHorizontal(pointer), angle), angle);
      });
    }
  }

  function updatePointer(event) {
    const bounds = canvas.getBoundingClientRect();
    pointer = {
      x: (event.clientX - bounds.left) * width / bounds.width,
      y: (event.clientY - bounds.top) * height / bounds.height
    };
    draw();
  }

  canvas.addEventListener('pointermove', updatePointer);
  canvas.addEventListener('pointerenter', function () {
    if (reminder) reminder.style.visibility = 'hidden';
  });
  canvas.addEventListener('pointerleave', function () {
    pointer = null;
    if (reminder) reminder.style.visibility = 'visible';
    draw();
  });
  draw();
})();
</script>



<p style='text-align: justify;'>
I am a neutron scattering scientist specialized in powder diffraction. My research focuses on the employment of neutron scattering for the atomic level structure studies of functional materials, such as those for energy storage, magnetoelectrics, and among others. I also apply the state-of-the-art AI/ML method, algorithm and framework in the structure-property link studies, such as the combination of neutron diffraction and machine learning force field (MLFF) for structure model refinement, the combination of exhaustive symmetry search and supervised machine learning for phase transition studies. Meanwhile, I have been actively involved in software development to support the neutron powder diffraction data processing and analysis, such as the ADDIE environment for neutron total scattering data reduction, ADDIE web interface for structure mining and web-based Bragg and pair distribution function (PDF) fitting, RMCProfile package for fitting scattering data based on supercell approach.
</p>

<br />

<p style='text-align: justify;'>
I am a member of American Crystallography Association (ACA) and the International Centre for Diffraction Data (ICDD). I am also serving as the guest editor for the Materials journal and I has been serving as reviewers for various peer review journals such as Advanced Science, Angew Chemie, Physical Review B, Physical Review M, etc.
</p>

Coding Stats
===

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

<div id="github-contrib-widget" style="max-width: 1000px; margin: 2rem auto; padding: 1.5rem; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
  <h3 style="margin-top: 0; text-align: center;">GitHub Contributions</h3>
  <p style="text-align: center; margin: 0.5rem 0;">
    <strong id="gh-total">…</strong> contributions in the last year
  </p>
  <div id="gh-contrib-grid" style="overflow-x: auto; margin-top: 1rem; text-align: center;"></div>
  <p style="text-align: center; margin-top: 1rem;">
    <a href="https://github.com/Kvieta1990" target="_blank">View GitHub profile →</a>
  </p>
</div>

<script>
fetch('https://github-contributions-api.jogruber.de/v4/Kvieta1990?y=last')
  .then(res => res.json())
  .then(data => {
    document.getElementById('gh-total').textContent = data.total.lastYear.toLocaleString();

    const contributions = data.contributions;
    const countByDate = {};
    const levelByDate = {};
    contributions.forEach(c => {
      countByDate[c.date] = c.count;
      levelByDate[c.date] = c.level;
    });

    const colors = ['#ebedf0', '#c6dcf1', '#8fbce6', '#5599d9', '#2b6cb0'];
    const cell = 11;
    const gap = 3;
    const step = cell + gap;

    const first = new Date(contributions[0].date + 'T00:00:00Z');
    const last = new Date(contributions[contributions.length - 1].date + 'T00:00:00Z');

    const start = new Date(first);
    start.setUTCDate(start.getUTCDate() - start.getUTCDay());

    const weeks = Math.floor((last - start) / (7 * 86400000)) + 1;
    const width = weeks * step;
    const height = 7 * step;

    let rects = '';
    for (let cur = new Date(start); cur <= last; cur.setUTCDate(cur.getUTCDate() + 1)) {
      const dateStr = cur.toISOString().slice(0, 10);
      const row = cur.getUTCDay();
      const col = Math.floor((cur - start) / (7 * 86400000));
      const level = levelByDate[dateStr] || 0;
      const count = countByDate[dateStr] || 0;
      const x = col * step;
      const y = row * step;
      rects += '<rect x="' + x + '" y="' + y + '" width="' + cell + '" height="' + cell +
        '" rx="2" fill="' + colors[level] + '"><title>' + dateStr + ': ' + count +
        ' contribution' + (count === 1 ? '' : 's') + '</title></rect>';
    }

    const svg = '<svg width="' + width + '" height="' + height + '" viewBox="0 0 ' + width + ' ' + height +
      '" xmlns="http://www.w3.org/2000/svg">' + rects + '</svg>';
    document.getElementById('gh-contrib-grid').innerHTML = svg;
  })
  .catch(err => {
    document.getElementById('github-contrib-widget').innerHTML = '<p style="text-align:center;">Could not load GitHub contributions data.</p>';
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