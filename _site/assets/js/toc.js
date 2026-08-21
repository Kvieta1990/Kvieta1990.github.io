(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var article = document.querySelector('.blog-post');
    var tocContainer = document.getElementById('toc-container');
    var tocList = document.getElementById('toc-list');

    if (!article || !tocContainer || !tocList) {
      return;
    }

    var headings = Array.prototype.slice.call(
      article.querySelectorAll('h2, h3, h4')
    );

    if (headings.length < 2) {
      tocContainer.style.display = 'none';
      return;
    }

    var baseLevel = Math.min.apply(
      null,
      headings.map(function (h) {
        return parseInt(h.tagName.substring(1), 10);
      })
    );

    var usedIds = {};
    headings.forEach(function (heading, index) {
      if (!heading.id) {
        heading.id = 'toc-heading-' + index;
      }
      usedIds[heading.id] = true;

      var level = parseInt(heading.tagName.substring(1), 10) - baseLevel;
      var link = document.createElement('a');
      link.href = '#' + heading.id;
      link.textContent = heading.textContent;
      link.className = 'toc-link toc-level-' + level;

      var item = document.createElement('li');
      item.className = 'toc-item';
      item.appendChild(link);
      tocList.appendChild(item);
    });

    var tocLinks = Array.prototype.slice.call(tocList.querySelectorAll('.toc-link'));

    if (!('IntersectionObserver' in window)) {
      return;
    }

    var activateLink = function (id) {
      tocLinks.forEach(function (link) {
        link.classList.toggle('active', link.getAttribute('href') === '#' + id);
      });
    };

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            activateLink(entry.target.id);
          }
        });
      },
      { rootMargin: '-85px 0px -70% 0px', threshold: 0 }
    );

    headings.forEach(function (heading) {
      observer.observe(heading);
    });
  });
})();
