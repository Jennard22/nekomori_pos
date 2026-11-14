// Client-side pagination + search for the Inventory and Transaction tables.
// Server loads up to 2000 rows; the table shows PAGE_SIZE at a time.
function initPager(tableId, pageSize) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
  const bar = document.createElement('div');
  bar.className = 'pager';
  table.closest('.table-card').after(bar);

  let filtered = rows, page = 1;

  function render() {
    const pages = Math.max(1, Math.ceil(filtered.length / pageSize));
    page = Math.min(page, pages);
    const start = (page - 1) * pageSize;
    rows.forEach(r => r.style.display = 'none');
    filtered.slice(start, start + pageSize).forEach(r => r.style.display = '');
    if (!rows.length) { bar.innerHTML = ''; return; }
    const from = filtered.length ? start + 1 : 0;
    const to = Math.min(start + pageSize, filtered.length);
    bar.innerHTML = `
      <span class="pager-info">Showing ${from}–${to} of ${filtered.length}</span>
      <div class="pager-btns">
        <button class="btn btn-outline btn-sm" ${page === 1 ? 'disabled' : ''} data-go="prev">‹ Prev</button>
        <span class="pager-page">Page ${page} / ${pages}</span>
        <button class="btn btn-outline btn-sm" ${page === pages ? 'disabled' : ''} data-go="next">Next ›</button>
      </div>`;
  }

  bar.addEventListener('click', e => {
    const go = e.target.dataset.go;
    if (go === 'prev') page--;
    if (go === 'next') page++;
    if (go) render();
  });

  render();
  return {
    search(q) {
      q = q.toLowerCase();
      filtered = rows.filter(r => r.textContent.toLowerCase().includes(q));
      page = 1;
      render();
    }
  };
}
