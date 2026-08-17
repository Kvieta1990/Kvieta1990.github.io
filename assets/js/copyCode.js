// Adds a "copy to clipboard" button to the top-right corner of every
// Rouge-highlighted code block (i.e. any fenced ``` code block).
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('div.highlighter-rouge').forEach(function (block) {
    var code = block.querySelector('pre code, pre');
    if (!code) return;

    block.classList.add('code-block-wrapper');

    var button = document.createElement('button');
    button.type = 'button';
    button.className = 'code-copy-btn';
    button.setAttribute('aria-label', 'Copy code to clipboard');
    button.innerHTML = '<i class="fas fa-copy"></i>';

    button.addEventListener('click', function () {
      navigator.clipboard.writeText(code.innerText).then(function () {
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('copied');

        setTimeout(function () {
          button.innerHTML = '<i class="fas fa-copy"></i>';
          button.classList.remove('copied');
        }, 1500);
      });
    });

    block.appendChild(button);
  });
});
