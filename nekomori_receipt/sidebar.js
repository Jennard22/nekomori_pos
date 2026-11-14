function renderSidebar(activePage, username) {
  const pages = [
    {
      id: 'home', label: 'Home', href: 'home.php',
      icon: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>`
    },
    {
      id: 'new_order', label: 'New Order', href: 'new_order.php',
      icon: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 6H17.82C17.4 4.84 16.3 4 15 4H9C7.7 4 6.6 4.84 6.18 6H5C3.9 6 3 6.9 3 8V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V8C21 6.9 20.1 6 19 6ZM9 6H15V7H9V6ZM12 17L7 12H10V9H14V12H17L12 17Z"/></svg>`
    },
    {
      id: 'transaction', label: 'Transaction', href: 'transaction.php',
      icon: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>`
    },
    {
      id: 'inventory', label: 'Inventory', href: 'inventory.php',
      icon: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm0 12H4V9h16v10zM4 5h16V3H4v2z"/></svg>`
    }
  ];

  const navItems = pages.map(p => `
    <a href="${p.href}" class="nav-item ${activePage === p.id ? 'active' : ''}">
      ${p.icon} ${p.label}
    </a>
  `).join('');

  document.getElementById('sidebar').innerHTML = `
    <div class="sidebar-logo">
      <img src="nekomori_logo.png" alt="NekoMori Logo"
        onerror="this.style.display='none';this.parentElement.querySelector('.sidebar-logo-placeholder').style.display='block'">
      <span class="sidebar-logo-placeholder" style="display:none">S</span>
    </div>
    <div class="sidebar-title">NekoMori Collectibles<br>Point of Sale system</div>
    ${navItems}

    
    <div class="nav-spacer"></div>
    <div class="sidebar-bottom">
      <div class="hello-user">Hello, <span>${username || 'User'}</span></div>
      <a href="logout.php" class="nav-logout">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
    Logout
</a>
    </div>
  `;
}
