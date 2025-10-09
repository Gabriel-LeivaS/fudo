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
                <?php if($p->disponible): ?>
                  <span style="background:#a3c06b; color:#fff; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:700;">✓ Disponible</span>
                <?php else: ?>
                  <span style="background:#e74c3c; color:#fff; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:700;">✗ No disponible</span>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </section>

  </main>

  <!-- Botón flotante para volver -->
  <a href="<?= site_url('admin') ?>" style="position:fixed; bottom:20px; right:20px; background:#667eea; color:#fff; text-decoration:none; padding:14px 24px; border-radius:50px; font-weight:700; box-shadow:0 8px 24px rgba(102,126,234,0.35); z-index:1500; transition:transform 0.2s ease; display:flex; align-items:center; gap:8px; font-size:14px;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
    ⬅️ Volver al Panel
  </a>

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

    // Inject category icons
    document.querySelectorAll('.cat-icon-dynamic').forEach(iconEl => {
      const catName = iconEl.dataset.category || '';
      iconEl.innerHTML = getCategoryIcon(catName);
    });
  </script>
</body>
</html>