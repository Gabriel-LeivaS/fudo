<div class="row">
    <?php if(empty($productos)): ?>
        <div class="col-12"><p class="text-muted">No hay productos en esta categoría.</p></div>
    <?php endif; ?>
    <?php foreach($productos as $p): ?>
        <div class="col-md-4 mb-3">
            <div class="card product-card" data-name="<?= htmlspecialchars($p->nombre) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= $p->nombre ?></h5>
                    <p class="card-text"><?= $p->descripcion ?></p>
                    <p><strong>$<?= number_format($p->precio,0,',','.') ?></strong></p>
                    <button class="btn btn-success" onclick="window.parent.postMessage({ action:'addToCart', product: { id:<?= $p->id_producto ?>, nombre: <?= json_encode($p->nombre) ?>, precio: <?= json_encode($p->precio) ?> } }, '*')">Agregar</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
