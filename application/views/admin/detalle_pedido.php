<!DOCTYPE html>
<html>
<head>
    <title>Detalle Pedido - Fudo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2 class="mb-4 text-center">Detalle Pedido</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $total=0; foreach($detalle as $d): $total+=$d->subtotal; ?>
            <tr>
                <td><?= $d->nombre ?></td>
                <td><?= $d->cantidad ?></td>
                <td>$<?= number_format($d->precio,0,',','.') ?></td>
                <td>$<?= number_format($d->subtotal,0,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="table-dark">
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td><strong>$<?= number_format($total,0,',','.') ?></strong></td>
            </tr>
        </tbody>
    </table>
    <div class="text-center">
        <a href="<?= site_url('admin') ?>" class="btn btn-secondary mt-3">Volver</a>
    </div>
</body>
</html>
