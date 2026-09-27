document.addEventListener('DOMContentLoaded', function(){
  const sidebar = document.querySelector('.gema-sidebar');
  const topbar = document.querySelector('.gema-topbar');
  const layout = document.querySelector('.gema-layout');
  if(!sidebar || !topbar) return;

  // Inject hamburger if not exists
  if(!document.getElementById('hamburger')){
    const btn = document.createElement('button');
    btn.id = 'hamburger';
    btn.className = 'hamburger';
    btn.setAttribute('aria-label','Buka menu');
    btn.setAttribute('type','button');
    btn.innerHTML = '<i class="bi bi-list"></i>';
    topbar.insertBefore(btn, topbar.firstChild);
  }
  // Inject backdrop if not exists
  if(!document.getElementById('sidebarBackdrop')){
    const bd = document.createElement('div');
    bd.id = 'sidebarBackdrop';
    bd.className = 'sidebar-backdrop';
    (layout || document.body).appendChild(bd);
  }
  const btn = document.getElementById('hamburger');
  const backdrop = document.getElementById('sidebarBackdrop');
  function openMenu(){
    sidebar.classList.add('open');
    backdrop.classList.add('show');
    document.body.style.overflow='hidden';
    btn.setAttribute('aria-expanded','true');
  }
  function closeMenu(){
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
    document.body.style.overflow='';
    btn.setAttribute('aria-expanded','false');
  }
  btn.addEventListener('click', function(e){
    e.stopPropagation();
    if(sidebar.classList.contains('open')) closeMenu();
    else openMenu();
  });
  backdrop.addEventListener('click', closeMenu);
  window.addEventListener('resize', function(){
    if(window.innerWidth > 900) closeMenu();
  });
  sidebar.querySelectorAll('a').forEach(function(a){
    a.addEventListener('click', function(){
      if(window.innerWidth <= 900) closeMenu();
    });
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape') closeMenu();
  });
});
