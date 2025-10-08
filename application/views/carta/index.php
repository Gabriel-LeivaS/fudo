<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Carta - Fudo</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg-image: url('https://images.unsplash.com/photo-1544511916-0148ccdeb877?auto=format&fit=crop&w=1600&q=60');
      --accent: #b08c6a;
      --accent-2: #a3c06b;
      --muted: #6c6c6c;
      --card-radius: 14px;
      --shadow: 0 14px 36px rgba(11,11,11,0.06);
      --container-max: 1100px;
    }
    html,body{height:100%; margin:0; font-family:"Montserrat",system-ui,Segoe UI,Arial; color:#222; background:#fbf8f6}
    .wrap{ max-width:var(--container-max); margin:28px auto; padding:18px; }
    .bg{ position:fixed; inset:0; z-index:-2; }
    .header{display:flex; gap:18px; align-items:center; background:rgba(255,255,255,0.94); padding:14px; border-radius:12px; box-shadow:var(--shadow);} 
    .logo{ width:72px; height:72px; border-radius:12px; background:#efe8de; display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--accent); font-size:22px; }
    .hdr-info{ flex:1; }
    .hdr-title{ font-weight:800; font-size:20px; }
    .hdr-sub{ color:var(--muted); font-size:13px; margin-top:6px; }
    .hdr-contact{ display:flex; flex-direction:column; align-items:flex-end; gap:4px; }
    .hdr-contact-label{ font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; font-weight:600; }
    .hdr-contact-icons{ display:flex; gap:12px; font-size:20px; }
    .search{ margin-top:16px; display:flex; gap:10px; align-items:center; }
    .search input{ flex:1; background:#fff; border-radius:28px; padding:12px 16px; box-shadow:0 12px 36px rgba(0,0,0,0.06); border:none; font-size:15px; }
    .scroller-wrap{ position:relative; margin-top:14px; }
    .scroller{ display:flex; gap:18px; overflow-x:auto; padding:12px 8px; scroll-behavior:smooth; }
    .cat-pill{ min-width:110px; flex:0 0 auto; text-align:center; cursor:pointer; }
    .cat-icon{ width:76px; height:76px; border-radius:50%; background:#efe8de; display:inline-grid; place-items:center; box-shadow:0 8px 22px rgba(0,0,0,0.06); }
    .cat-label{ margin-top:8px; font-weight:700; font-size:11px; text-transform:uppercase; color:#2f2f2f; }
    .scroller-arrow{ position:absolute; top:50%; transform:translateY(-50%); background:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 22px rgba(0,0,0,0.08); cursor:pointer; }
    .scroller-arrow.left{ left:-6px; } .scroller-arrow.right{ right:-6px; }
    .grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-top:22px; }
    .card{ background:#fff; border-radius:var(--card-radius); padding:18px; box-shadow:var(--shadow); display:flex; flex-direction:column; justify-content:space-between; min-height:170px; }
    .card-title{ text-transform:uppercase; font-weight:800; font-size:14px; margin:0 0 8px; }
    .card-desc{ color:var(--muted); font-size:13px; margin-bottom:8px; min-height:36px; line-height:1.4; }
    .card-footer{ display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
    .price{ font-weight:800; color:#23311e; font-size:16px; }
    .tax{ font-size:12px; color:#9f9f9f; margin-top:6px; }
    .btn-add{ background:transparent; border:1px solid rgba(0,0,0,0.06); padding:8px 12px; border-radius:10px; color:var(--accent-2); cursor:pointer; transition:transform .18s ease, box-shadow .18s ease; }
    .btn-add:hover{ transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,0.06); }
    .cart-widget{ position:fixed; right:18px; bottom:18px; z-index:1400; }
    .cart-fab{ width:64px; height:64px; border-radius:50%; background:#a3c06b; color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 8px 24px rgba(163,192,107,0.35); font-size:24px; position:relative; transition:transform .2s ease; }
    .cart-fab:hover{ transform:scale(1.08); }
    .cart-badge{ position:absolute; top:-4px; right:-4px; background:#e74c3c; color:#fff; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; }
    .cart-card{ background:#fff; border-radius:12px; padding:14px; box-shadow:0 18px 36px rgba(0,0,0,0.14); width:340px; max-height:420px; overflow-y:auto; display:none; }
    .cart-card.expanded{ display:block; }
    .cart-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
    .cart-close{ cursor:pointer; font-size:22px; color:#999; line-height:1; }
    .cart-close:hover{ color:#333; }
    /* Custom modal */
    .modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; display:none; align-items:center; justify-content:center; }
    .modal-overlay.show{ display:flex; }
    .modal-box{ background:#fff; border-radius:14px; padding:24px; max-width:380px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalSlideIn 0.3s ease; }
    @keyframes modalSlideIn{ from{ opacity:0; transform:scale(0.9) translateY(-20px); } to{ opacity:1; transform:scale(1) translateY(0); } }
    .modal-title{ font-size:18px; font-weight:700; margin-bottom:12px; color:#333; }
    .modal-text{ font-size:14px; color:#666; margin-bottom:20px; line-height:1.5; }
    .modal-buttons{ display:flex; gap:10px; justify-content:flex-end; }
    .modal-btn{ padding:10px 20px; border-radius:8px; border:none; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.2s ease; }
    .modal-btn-cancel{ background:#f0f0f0; color:#333; }
    .modal-btn-cancel:hover{ background:#e0e0e0; }
    .modal-btn-confirm{ background:#e74c3c; color:#fff; }
    .modal-btn-confirm:hover{ background:#c0392b; transform:translateY(-1px); box-shadow:0 4px 12px rgba(231,76,60,0.3); }
    .modal-btn-primary{ background:#a3c06b; color:#fff; }
    .modal-btn-primary:hover{ background:#94b35e; transform:translateY(-1px); box-shadow:0 4px 12px rgba(163,192,107,0.3); }
    /* Form styles */
    .form-group{ margin-bottom:16px; }
    .form-label{ display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:8px; }
    .form-input{ width:100%; box-sizing:border-box; padding:12px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; font-family:'Montserrat',inherit; transition:all 0.2s ease; background:#fff; }
    .form-input:focus{ outline:none; border-color:#a3c06b; box-shadow:0 0 0 3px rgba(163,192,107,0.15); }
    .form-input::placeholder{ color:#aaa; font-size:13px; }
    .form-error{ border-color:#e74c3c !important; box-shadow:0 0 0 3px rgba(231,76,60,0.1) !important; }
    .form-help{ font-size:11px; color:#e74c3c; margin-top:6px; display:none; font-weight:500; }
    .form-help.show{ display:block; }
    @keyframes pop {0%{transform:scale(1)}50%{transform:scale(1.08)}100%{transform:scale(1)}}
    @keyframes pulse {0%{box-shadow:0 14px 36px rgba(0,0,0,0.06)}50%{box-shadow:0 20px 48px rgba(163,192,107,0.18)}100%{box-shadow:0 14px 36px rgba(0,0,0,0.06)}}
    .pop { animation: pop 260ms ease-in-out; } .pulse { animation: pulse 520ms ease-in-out; }
    @media (max-width:900px){ .grid{ grid-template-columns:repeat(2,1fr);} .scroller-arrow{ display:none; } .wrap{ padding:12px; } .cart-card{ width:320px; right:8px; } .hdr-contact{ display:none; } }
    @media (max-width:520px){ .grid{ grid-template-columns:1fr; } .header{ flex-direction:column; align-items:flex-start; gap:12px; padding:16px; } .logo{ width:56px; height:56px; font-size:18px; } .hdr-title{ font-size:18px; } .hdr-sub{ font-size:12px; } .search input{ font-size:14px; } .cart-card{ width:calc(100vw - 32px); right:8px; } }
  </style>
</head>
<body>
  <div class="bg"></div>
  <main class="wrap">

    <header class="header" role="banner" aria-label="Datos del local">
      <div class="logo"><?= strtoupper(substr($mesa->nombre_sucursal ?? 'FUDO', 0, 3)) ?></div>
      <div class="hdr-info">
        <div class="hdr-title"><?= htmlspecialchars($mesa->nombre_sucursal ?? 'FUDO') ?></div>
        <div class="hdr-sub">📍 <?= htmlspecialchars($mesa->direccion ?? 'Dirección no disponible') ?></div>
        <div class="hdr-sub">☎️ <?= htmlspecialchars($mesa->telefono ?? 'Teléfono no disponible') ?> · 🪑 Mesa #<?= $id_mesa ?></div>
      </div>
      <div class="hdr-contact">
        <div class="hdr-contact-label">Síguenos</div>
        <div class="hdr-contact-icons">
          <a href="tel:5491168718038" style="color:#a3c06b;text-decoration:none;" title="Llamar">📞</a>
          <a href="https://wa.me/5491168718038" target="_blank" style="color:#25D366;text-decoration:none;" title="WhatsApp">💬</a>
          <a href="#" style="color:#E4405F;text-decoration:none;" title="Instagram">📷</a>
        </div>
      </div>
    </header>

    <div class="search" role="search">
      <input id="searchInput" placeholder="Buscar productos por nombre" aria-label="Buscar productos" />
    </div>

    <div class="scroller-wrap" aria-label="Categorías">
      <div class="scroller-arrow left" id="sc-left" aria-hidden="true">‹</div>
      <div class="scroller" id="cats" role="list">
        <?php foreach($categorias as $c): ?>
          <div class="cat-pill" role="button" tabindex="0" data-id="<?= $c->id_categoria ?>" data-cat-name="<?= htmlspecialchars($c->nombre) ?>">
            <div class="cat-icon cat-icon-dynamic" aria-hidden="true" data-category="<?= htmlspecialchars($c->nombre) ?>">
              <!-- Icon injected by JS -->
            </div>
            <div class="cat-label"><?= htmlspecialchars($c->nombre) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="scroller-arrow right" id="sc-right" aria-hidden="true">›</div>
    </div>

    <section class="grid" id="products">
      <?php foreach($categorias as $c): ?>
        <?php foreach($productos_por_categoria[$c->id_categoria] as $p): ?>
          <article class="card product-card" data-name="<?= htmlspecialchars($p->nombre) ?>" data-id="<?= $p->id_producto ?>" data-precio="<?= $p->precio ?>">
            <div>
              <h3 class="card-title"><?= $p->nombre ?></h3>
              <p class="card-desc"><?= $p->descripcion ?></p>
            </div>
            <div class="card-footer">
              <div>
                <div class="price">$<?= number_format($p->precio,2,',','.') ?></div>
                <div class="tax">Sin impuestos nacionales: $ <?= number_format(($p->precio/1.21),2,',','.') ?></div>
              </div>
              <div>
                <button class="btn-add btn-agregar-producto">Agregar</button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </section>

    <div class="cart-widget" id="cartWidget" aria-live="polite">
      <div class="cart-fab" id="cartFab" title="Ver carrito">
        🛒
        <span class="cart-badge" id="cartBadge" style="display:none;">0</span>
      </div>
      <div class="cart-card" id="cartCard"></div>
    </div>

  </main>

  <div id="mock-toast" style="position:fixed; top:18px; right:18px; z-index:1600;"></div>

  <!-- Custom confirm modal -->
  <div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
      <div class="modal-title" id="modalTitle">Confirmar acción</div>
      <div class="modal-text" id="modalText">¿Estás seguro?</div>
      <div class="modal-buttons">
        <button class="modal-btn modal-btn-cancel" id="modalCancel">Cancelar</button>
        <button class="modal-btn modal-btn-confirm" id="modalConfirm">Confirmar</button>
      </div>
    </div>
  </div>

  <!-- Customer data modal -->
  <div class="modal-overlay" id="customerModal">
    <div class="modal-box" style="max-width:480px;">
      <div class="modal-title">Datos del cliente</div>
      <div class="modal-text" style="margin-bottom:16px;">Por favor, completa tus datos para confirmar el pedido:</div>
      
      <form id="customerForm">
        <div class="form-group">
          <label class="form-label" for="customerName">Nombre completo *</label>
          <input type="text" id="customerName" class="form-input" placeholder="Ej: Juan Pérez" required />
          <div class="form-help" id="nameError">Por favor ingresa tu nombre</div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="customerRut">RUT *</label>
          <input type="text" id="customerRut" class="form-input" placeholder="Ej: 12.345.678-9" required maxlength="12" />
          <div class="form-help" id="rutError">Por favor ingresa un RUT válido</div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="customerPhone">Teléfono *</label>
          <input type="tel" id="customerPhone" class="form-input" placeholder="Ej: 11 1234-5678" required />
          <div class="form-help" id="phoneError">Por favor ingresa un teléfono válido</div>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="customerEmail">Email *</label>
          <input type="email" id="customerEmail" class="form-input" placeholder="Ej: cliente@email.com" required />
          <div class="form-help" id="emailError">Por favor ingresa un email válido</div>
        </div>
        
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" for="customerNotes">Notas especiales (opcional)</label>
          <input type="text" id="customerNotes" class="form-input" placeholder="Ej: Sin cebolla, alérgico a..." />
        </div>
      </form>
      
      <div class="modal-buttons" style="margin-top:20px;">
        <button class="modal-btn modal-btn-cancel" id="customerCancel">Cancelar</button>
        <button class="modal-btn modal-btn-primary" id="customerConfirm">Confirmar pedido</button>
      </div>
    </div>
  </div>

  <script>
    // Category icons map (SVG paths)
    const categoryIcons = {
      'bebidas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>',
      'comidas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>',
      'postres': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 21h10M12 21v-8M5.5 13a5.5 5.5 0 0 1 0-11H7a5.5 5.5 0 0 1 0 11M17 2h1.5a5.5 5.5 0 0 1 0 11H17"/></svg>',
      'entradas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
      'pizzas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L22 12 L12 22 L2 12 Z"/><circle cx="12" cy="10" r="1.5" fill="#b08c6a"/><circle cx="15" cy="14" r="1.5" fill="#b08c6a"/><circle cx="9" cy="14" r="1.5" fill="#b08c6a"/></svg>',
      'hamburguesas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11h16M4 15h16M7 7a5 5 0 0 1 10 0H4"/><rect x="3" y="15" width="18" height="4" rx="2"/></svg>',
      'pastas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 12h14M5 16h14M9 4v16M15 4v16"/></svg>',
      'ensaladas': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M12 14v8M8 18h8"/></svg>',
      'carnes': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6 L18 6 L18 18 L6 18 Z"/><path d="M9 6 L9 18 M12 6 L12 18 M15 6 L15 18"/></svg>',
      'cafeteria': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 1 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="2" x2="6" y2="8"/><line x1="10" y1="2" x2="10" y2="8"/><line x1="14" y1="2" x2="14" y2="8"/></svg>',
      'desayunos': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>',
      'especiales': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15 8.5 22 9.3 17 14 18.5 21 12 17.5 5.5 21 7 14 2 9.3 9 8.5 12 2" fill="none"/></svg>',
      'default': '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b08c6a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>'
    };

    // Function to get icon for category
    function getCategoryIcon(categoryName) {
      const name = categoryName.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
      return categoryIcons[name] || categoryIcons['default'];
    }

    // scroller arrows
    (function(){ const cont = document.getElementById('cats'); document.getElementById('sc-left').addEventListener('click', ()=>cont.scrollBy({left:-240,behavior:'smooth'})); document.getElementById('sc-right').addEventListener('click', ()=>cont.scrollBy({left:240,behavior:'smooth'})); })();

    // search filter
    document.getElementById('searchInput').addEventListener('input', function(){ const q = this.value.toLowerCase().trim(); document.querySelectorAll('.product-card').forEach(card=>{ const name = card.dataset.name.toLowerCase(); card.style.display = name.includes(q) ? '' : 'none'; }); });

    // cart
    const CART_KEY = 'fudo_cart_v1'; 
    let carrito = JSON.parse(localStorage.getItem(CART_KEY) || '[]'); 
    console.log('[FUDO DEBUG] Carrito inicial cargado desde localStorage:', carrito);
    
    function saveCart(){ 
      localStorage.setItem(CART_KEY, JSON.stringify(carrito)); 
      console.log('[FUDO DEBUG] Carrito guardado:', carrito);
      renderCart(); 
    }

    function eliminarItemCarrito(idx) {
      if(idx >= 0 && idx < carrito.length) {
        const item = carrito[idx];
        console.log('[FUDO DEBUG] Eliminando item del carrito:', item);
        carrito.splice(idx, 1);
        saveCart();
        showToast(`${item.nombre} eliminado del carrito`, 1400, 'info');
      }
    }

    function cambiarCantidad(idx, delta) {
      if(idx >= 0 && idx < carrito.length) {
        const item = carrito[idx];
        const nuevaCantidad = item.cantidad + delta;
        
        if(nuevaCantidad <= 0) {
          // Si la cantidad llega a 0, eliminar el item
          eliminarItemCarrito(idx);
        } else {
          item.cantidad = nuevaCantidad;
          item.subtotal = item.cantidad * item.precio;
          console.log('[FUDO DEBUG] Cantidad cambiada:', item);
          saveCart();
          // No mostrar toast para no saturar (el usuario ve el cambio visualmente)
        }
      }
    }

    function vaciarCarrito() {
      console.log('[FUDO DEBUG] Vaciando carrito completo');
      carrito = [];
      saveCart();
      showToast('Carrito vaciado', 1400, 'info');
    }

    function agregarCarrito(id,nombre,precio,srcEl=null){ 
      console.log('[FUDO DEBUG] agregarCarrito llamado:', {id, nombre, precio});
      let item = carrito.find(i=>i.id_producto===id); 
      if(!item) {
        carrito.push({ id_producto:id, nombre:nombre, precio:precio, cantidad:1, subtotal:precio });
        console.log('[FUDO DEBUG] Producto nuevo agregado al carrito');
      } else { 
        item.cantidad++; 
        item.subtotal = item.cantidad * item.precio; 
        console.log('[FUDO DEBUG] Cantidad incrementada para producto existente');
      }
      saveCart(); 
      showToast(`${nombre} agregado al carrito`, 1600, 'success'); 
      try{ 
        const btn = srcEl || document.querySelector(`button[onclick*="agregarCarrito(${id}"]`); 
        if(btn){ btn.classList.add('pop'); setTimeout(()=>btn.classList.remove('pop'),300); } 
        const card = btn?.closest('.product-card') || document.querySelector(`.product-card[data-name="${nombre.replace(/"/g,'\"')}"]`); 
        if(card){ card.classList.add('pulse'); setTimeout(()=>card.classList.remove('pulse'),600); } 
      }catch(e){ console.error('[FUDO DEBUG] Error en animaciones:', e); }
    }

    function renderCart(){ 
      console.log('[FUDO DEBUG] renderCart llamado. Items en carrito:', carrito.length);
      const badge = document.getElementById('cartBadge');
      const card = document.getElementById('cartCard');
      
      if(!badge || !card) {
        console.error('[FUDO DEBUG] ERROR: No se encontraron elementos del carrito');
        return;
      }
      
      // Update badge
      const totalItems = carrito.reduce((sum, it) => sum + it.cantidad, 0);
      if(totalItems > 0) {
        badge.textContent = totalItems;
        badge.style.display = 'flex';
      } else {
        badge.style.display = 'none';
      }
      
      // Render cart content
      if(!carrito.length){ 
        card.innerHTML = '<div class="cart-header"><strong>Carrito</strong><span class="cart-close" onclick="toggleCart()">✕</span></div><div style="font-size:13px;color:#999;padding:10px 0;">Tu carrito está vacío</div>'; 
        console.log('[FUDO DEBUG] Carrito vacío renderizado');
        return; 
      } 
      
      let total = carrito.reduce((s,it)=>s+it.precio*it.cantidad,0); 
      let html = '<div class="cart-header"><strong>Carrito</strong><span class="cart-close" onclick="toggleCart()">✕</span></div><div style="margin-top:8px">'; 
      
      carrito.forEach((it, idx) => {
        html += `<div style="display:flex;justify-content:space-between;align-items:center;margin:10px 0;padding:8px;background:#f9f9f9;border-radius:8px;gap:8px;">
          <div style="flex:1;">
            <div style="font-weight:600;font-size:13px;">${it.nombre}</div>
            <div style="color:#999;font-size:11px;margin-top:2px;">$${(it.precio).toLocaleString('es-CL',{minimumFractionDigits:2})} c/u</div>
          </div>
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="display:flex;align-items:center;gap:6px;background:#fff;border-radius:6px;padding:2px 4px;border:1px solid #e0e0e0;">
              <button class="btn-qty-minus" data-idx="${idx}" style="background:transparent;border:none;color:#666;cursor:pointer;font-size:16px;padding:2px 6px;line-height:1;font-weight:700;" title="Reducir cantidad">−</button>
              <span style="font-weight:600;min-width:20px;text-align:center;font-size:14px;">${it.cantidad}</span>
              <button class="btn-qty-plus" data-idx="${idx}" style="background:transparent;border:none;color:#a3c06b;cursor:pointer;font-size:16px;padding:2px 6px;line-height:1;font-weight:700;" title="Aumentar cantidad">+</button>
            </div>
            <div style="font-weight:700;color:#23311e;min-width:70px;text-align:right;font-size:14px;">$${(it.precio * it.cantidad).toLocaleString('es-CL',{minimumFractionDigits:2})}</div>
            <button class="btn-remove-item" data-idx="${idx}" style="background:transparent;border:none;color:#e74c3c;cursor:pointer;font-size:16px;padding:4px 6px;line-height:1;" title="Eliminar">🗑️</button>
          </div>
        </div>`;
      });
      
      html += `</div><hr style="margin:12px 0;border:none;border-top:1px solid #eee;"/>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
          <div style="font-size:16px;"><strong>Total:</strong> <span style="color:#23311e;font-weight:800;">$${total.toLocaleString('es-CL',{minimumFractionDigits:2})}</span></div>
          <button class="btn-clear-cart" style="background:transparent;border:1px solid #e74c3c;color:#e74c3c;padding:6px 12px;border-radius:8px;font-size:12px;cursor:pointer;" title="Vaciar carrito">Vaciar</button>
        </div>
        <button class="btn-add btn-enviar-pedido" style="width:100%;margin-top:12px;background:#a3c06b;color:#fff;border:none;padding:12px;font-weight:700;border-radius:10px;cursor:pointer;">Enviar pedido</button>`; 
      
      card.innerHTML = html; 
      console.log('[FUDO DEBUG] Carrito con items renderizado. Total: $' + total);
      
      // Attach event to "Enviar pedido" button
      const btnEnviar = card.querySelector('.btn-enviar-pedido');
      if(btnEnviar) {
        btnEnviar.addEventListener('click', function() {
          console.log('[FUDO DEBUG] Click en Enviar pedido');
          enviarPedido(this);
        });
      }
      
      // Attach events to quantity buttons (+ and -)
      card.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
          const idx = parseInt(this.dataset.idx);
          cambiarCantidad(idx, 1);
        });
      });
      
      card.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
          const idx = parseInt(this.dataset.idx);
          cambiarCantidad(idx, -1);
        });
      });
      
      // Attach events to remove item buttons
      card.querySelectorAll('.btn-remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
          const idx = parseInt(this.dataset.idx);
          eliminarItemCarrito(idx);
        });
      });
      
      // Attach event to clear cart button
      const btnClear = card.querySelector('.btn-clear-cart');
      if(btnClear) {
        btnClear.addEventListener('click', async function() {
          const confirmed = await showConfirm('Vaciar carrito', '¿Estás seguro de que quieres eliminar todos los productos del carrito?');
          if(confirmed) {
            vaciarCarrito();
          }
        });
      }
    }

    async function enviarPedido(btn){
      // Primero, solicitar datos del cliente
      const customerData = await showCustomerModal();
      
      if(!customerData) {
        console.log('[FUDO DEBUG] Usuario canceló ingreso de datos');
        return;
      }
      
      console.log('[FUDO DEBUG] Datos del cliente:', customerData);
      
      // Añadimos resiliencia: intentamos enviar JSON, si falla (backend que espera POST form), reintentamos con FormData
      try{
        if(btn){ btn.classList.add('pop'); setTimeout(()=>btn.classList.remove('pop'),260); }
        const payload = { 
          id_mesa: <?= json_encode($this->session->userdata('id_mesa') ?? null) ?>, 
          detalle: carrito.map(i=>({ id_producto:i.id_producto, cantidad:i.cantidad, subtotal:i.subtotal })),
          cliente: customerData
        };

        // Intento 1: JSON
        try{
          const res = await fetch('<?= site_url('pedidos/crear') ?>',{
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
          });

          // Intentar parsear JSON; si falla, lanzamos para activar el fallback
          const text = await res.text();
          let json;
          try{ json = text ? JSON.parse(text) : null; }
          catch(parseErr){ throw new Error('no-json'); }

          if(json && json.ok){
            showToast('Pedido creado. ID: ' + json.id_pedido, 2400, 'success');
            carrito = []; try{ localStorage.removeItem(CART_KEY); }catch(e){}
            renderCart();
            return;
          }

          // Si el backend devolvió JSON con ok=false, mostramos el error
          if(json && !json.ok){ showToast('Error al crear pedido: ' + (json.error||'desconocido'), 4000, 'danger'); return; }

          // Si llegamos aquí y no hay json, forzamos fallback
          throw new Error('fallback');
        }catch(errJson){
          // Fallback: enviar como FormData (backend que espera campos POST tradicionales)
          try{
            const form = new URLSearchParams();
            form.append('id_mesa', payload.id_mesa || '');
            form.append('detalle', JSON.stringify(payload.detalle));
            form.append('cliente_nombre', customerData.nombre);
            form.append('cliente_rut', customerData.rut);
            form.append('cliente_telefono', customerData.telefono);
            form.append('cliente_email', customerData.email);
            if(customerData.notas) form.append('cliente_notas', customerData.notas);

            const res2 = await fetch('<?= site_url('pedidos/crear') ?>',{
              method: 'POST',
              headers: { 'X-Requested-With': 'XMLHttpRequest' },
              body: form,
              credentials: 'same-origin'
            });
            const txt = await res2.text();
            let json2 = null;
            try{ json2 = txt ? JSON.parse(txt) : null; } catch(e){ /* backend devolvió texto plano */ }

            if(json2 && json2.ok){ showToast('Pedido creado. ID: ' + json2.id_pedido, 2400, 'success'); carrito = []; try{ localStorage.removeItem(CART_KEY); }catch(e){} renderCart(); return; }
            // Si no hay JSON, pero HTTP 200 y texto, asumimos éxito (por compatibilidad)
            if(res2.ok){ showToast('Pedido enviado (respuesta no-JSON del servidor).', 2600, 'success'); carrito = []; try{ localStorage.removeItem(CART_KEY); }catch(e){} renderCart(); return; }

            // Finalmente mostramos error
            showToast('Error al crear pedido (fallback): ' + (json2 && json2.error ? json2.error : txt || 'desconocido'), 4500, 'danger');
            return;
          }catch(errFallback){ console.error('fallback error', errFallback); showToast('Error al enviar pedido (intento fallback falló)', 3600, 'danger'); return; }
        }

      }catch(e){ console.error(e); showToast('Error al enviar pedido', 3500, 'danger'); }
    }

    function showToast(msg, timeout=1800, type='info'){ 
      const container = document.getElementById('mock-toast'); 
      const el = document.createElement('div'); 
      el.style.minWidth='240px'; 
      el.style.maxWidth='320px';
      el.style.marginBottom='8px'; 
      el.style.padding='12px 16px'; 
      el.style.borderRadius='10px'; 
      el.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';
      el.style.opacity='0';
      el.style.transform='translateY(-10px)';
      el.style.transition='all 0.3s ease';
      el.style.fontSize='14px';
      el.style.fontWeight='500';
      
      if(type==='success'){ 
        el.style.background='#e6f6ea'; 
        el.style.color='#1b5e20'; 
        el.style.border='1px solid rgba(163,192,107,0.35)'; 
      } else if(type==='danger'){ 
        el.style.background='#ffecec'; 
        el.style.color='#7a1515'; 
        el.style.border='1px solid rgba(200,50,50,0.25)'; 
      } else { 
        el.style.background='#ffffff'; 
        el.style.color='#333'; 
        el.style.border='1px solid rgba(0,0,0,0.08)'; 
      } 
      
      el.textContent = msg; 
      container.appendChild(el); 
      
      // Trigger animation
      requestAnimationFrame(() => {
        el.style.opacity='1';
        el.style.transform='translateY(0)';
      });
      
      setTimeout(()=>{ 
        el.style.opacity='0'; 
        el.style.transform='translateY(-10px)'; 
        setTimeout(()=>el.remove(), 300); 
      }, timeout); 
    }

    // Toggle cart panel
    function toggleCart() {
      const card = document.getElementById('cartCard');
      if(card) {
        card.classList.toggle('expanded');
        console.log('[FUDO DEBUG] Carrito toggled. Expandido:', card.classList.contains('expanded'));
      }
    }

    // Custom confirm dialog
    function showConfirm(title, message) {
      return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalText = document.getElementById('modalText');
        const btnCancel = document.getElementById('modalCancel');
        const btnConfirm = document.getElementById('modalConfirm');
        
        modalTitle.textContent = title;
        modalText.textContent = message;
        modal.classList.add('show');
        
        const handleConfirm = () => {
          modal.classList.remove('show');
          cleanup();
          resolve(true);
        };
        
        const handleCancel = () => {
          modal.classList.remove('show');
          cleanup();
          resolve(false);
        };
        
        const cleanup = () => {
          btnConfirm.removeEventListener('click', handleConfirm);
          btnCancel.removeEventListener('click', handleCancel);
          modal.removeEventListener('click', handleOverlayClick);
        };
        
        const handleOverlayClick = (e) => {
          if(e.target === modal) handleCancel();
        };
        
        btnConfirm.addEventListener('click', handleConfirm);
        btnCancel.addEventListener('click', handleCancel);
        modal.addEventListener('click', handleOverlayClick);
      });
    }

    // Customer data modal
    function showCustomerModal() {
      return new Promise((resolve) => {
        const modal = document.getElementById('customerModal');
        const form = document.getElementById('customerForm');
        const btnCancel = document.getElementById('customerCancel');
        const btnConfirm = document.getElementById('customerConfirm');
        
        const nameInput = document.getElementById('customerName');
        const rutInput = document.getElementById('customerRut');
        const phoneInput = document.getElementById('customerPhone');
        const emailInput = document.getElementById('customerEmail');
        const notesInput = document.getElementById('customerNotes');
        
        // RUT formatting and validation
        const formatRut = (value) => {
          // Remove all non-digits and non-K
          let rut = value.replace(/[^0-9kK]/g, '').toUpperCase();
          
          if(rut.length === 0) return '';
          
          // Separate body and verifier
          let body = rut.slice(0, -1);
          let verifier = rut.slice(-1);
          
          // Format body with dots
          body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
          
          return body ? `${body}-${verifier}` : '';
        };
        
        const validateRut = (rut) => {
          // Remove formatting
          rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
          
          if(rut.length < 2) return false;
          
          let body = rut.slice(0, -1);
          let verifier = rut.slice(-1);
          
          // Calculate verifier
          let sum = 0;
          let multiplier = 2;
          
          for(let i = body.length - 1; i >= 0; i--) {
            sum += parseInt(body[i]) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
          }
          
          let calculatedVerifier = 11 - (sum % 11);
          calculatedVerifier = calculatedVerifier === 11 ? '0' : calculatedVerifier === 10 ? 'K' : calculatedVerifier.toString();
          
          return verifier === calculatedVerifier;
        };
        
        // Auto-format RUT on input
        rutInput.addEventListener('input', function(e) {
          const cursorPos = this.selectionStart;
          const oldValue = this.value;
          const formatted = formatRut(this.value);
          this.value = formatted;
          
          // Adjust cursor position
          if(formatted.length > oldValue.length) {
            this.setSelectionRange(cursorPos + 1, cursorPos + 1);
          }
        });
        
        // Reset form
        form.reset();
        document.querySelectorAll('.form-input').forEach(input => input.classList.remove('form-error'));
        document.querySelectorAll('.form-help').forEach(help => help.classList.remove('show'));
        
        modal.classList.add('show');
        setTimeout(() => nameInput.focus(), 300);
        
        const validateForm = () => {
          let isValid = true;
          
          // Validate name
          if(!nameInput.value.trim()) {
            nameInput.classList.add('form-error');
            document.getElementById('nameError').classList.add('show');
            isValid = false;
          } else {
            nameInput.classList.remove('form-error');
            document.getElementById('nameError').classList.remove('show');
          }
          
          // Validate RUT
          if(!rutInput.value.trim() || !validateRut(rutInput.value)) {
            rutInput.classList.add('form-error');
            document.getElementById('rutError').classList.add('show');
            isValid = false;
          } else {
            rutInput.classList.remove('form-error');
            document.getElementById('rutError').classList.remove('show');
          }
          
          // Validate phone
          const phoneRegex = /^[\d\s\-\+\(\)]{8,}$/;
          if(!phoneInput.value.trim() || !phoneRegex.test(phoneInput.value)) {
            phoneInput.classList.add('form-error');
            document.getElementById('phoneError').classList.add('show');
            isValid = false;
          } else {
            phoneInput.classList.remove('form-error');
            document.getElementById('phoneError').classList.remove('show');
          }
          
          // Validate email (required)
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if(!emailInput.value.trim() || !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('form-error');
            document.getElementById('emailError').classList.add('show');
            isValid = false;
          } else {
            emailInput.classList.remove('form-error');
            document.getElementById('emailError').classList.remove('show');
          }
          
          return isValid;
        };
        
        const handleConfirm = () => {
          if(validateForm()) {
            const customerData = {
              nombre: nameInput.value.trim(),
              rut: rutInput.value.trim(),
              telefono: phoneInput.value.trim(),
              email: emailInput.value.trim(),
              notas: notesInput.value.trim() || null
            };
            modal.classList.remove('show');
            cleanup();
            resolve(customerData);
          }
        };
        
        const handleCancel = () => {
          modal.classList.remove('show');
          cleanup();
          resolve(null);
        };
        
        const cleanup = () => {
          btnConfirm.removeEventListener('click', handleConfirm);
          btnCancel.removeEventListener('click', handleCancel);
          modal.removeEventListener('click', handleOverlayClick);
          form.removeEventListener('submit', handleSubmit);
          rutInput.removeEventListener('input', arguments.callee);
        };
        
        const handleOverlayClick = (e) => {
          if(e.target === modal) handleCancel();
        };
        
        const handleSubmit = (e) => {
          e.preventDefault();
          handleConfirm();
        };
        
        btnConfirm.addEventListener('click', handleConfirm);
        btnCancel.addEventListener('click', handleCancel);
        modal.addEventListener('click', handleOverlayClick);
        form.addEventListener('submit', handleSubmit);
      });
    }

    // init
    console.log('[FUDO DEBUG] Script cargado correctamente');
    console.log('[FUDO DEBUG] Elemento cartWidget existe:', !!document.getElementById('cartWidget'));
    console.log('[FUDO DEBUG] Elemento mock-toast existe:', !!document.getElementById('mock-toast'));
    
    // Inject category icons
    document.querySelectorAll('.cat-icon-dynamic').forEach(iconEl => {
      const catName = iconEl.dataset.category || '';
      iconEl.innerHTML = getCategoryIcon(catName);
    });
    
    renderCart();

    // Cart FAB click handler
    const cartFab = document.getElementById('cartFab');
    if(cartFab) {
      cartFab.addEventListener('click', toggleCart);
    }

    // Close cart when clicking outside
    document.addEventListener('click', function(e) {
      const cartWidget = document.getElementById('cartWidget');
      const cartCard = document.getElementById('cartCard');
      
      // Si el carrito está expandido y el click no es dentro del widget del carrito
      if(cartCard && cartCard.classList.contains('expanded')) {
        // Verificar si el click fue fuera del widget completo
        if(!cartWidget.contains(e.target)) {
          console.log('[FUDO DEBUG] Click fuera del carrito, cerrando...');
          cartCard.classList.remove('expanded');
        }
      }
    });

    // Attach event listeners to all "Agregar" buttons
    document.querySelectorAll('.btn-agregar-producto').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const card = this.closest('.product-card');
        if(!card) {
          console.error('[FUDO DEBUG] No se encontró card para botón');
          return;
        }
        const id = parseInt(card.dataset.id);
        const nombre = card.dataset.name;
        const precio = parseFloat(card.dataset.precio);
        console.log('[FUDO DEBUG] Click en botón agregar:', {id, nombre, precio});
        agregarCarrito(id, nombre, precio, this);
      });
    });
  </script>
</body>
</html>
