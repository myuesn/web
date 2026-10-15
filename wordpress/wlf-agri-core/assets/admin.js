(function () {
  'use strict';
  const rows = document.getElementById('wlf-spec-rows');
  const template = document.getElementById('wlf-spec-template');
  const add = document.getElementById('wlf-add-spec');
  if (!rows || !template || !add) return;
  let next = rows.children.length;
  add.addEventListener('click', function () {
    if (rows.children.length >= 50) return;
    rows.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, String(next++)));
    const input = rows.lastElementChild.querySelector('input');
    if (input) input.focus();
  });
  rows.addEventListener('click', function (event) {
    const remove = event.target.closest('.wlf-remove-spec');
    if (remove) remove.closest('.wlf-spec-row').remove();
  });
})();
