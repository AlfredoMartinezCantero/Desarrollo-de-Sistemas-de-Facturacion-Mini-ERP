<h2 class="mb-4">Productos / Servicios</h2>

<div class="mb-3 text-end">
    <a href="index.php?action=products_create" class="btn btn-success">
        ➕ Nuevo producto
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        No tienes productos registrados todavía.
    </div>
<?php else: ?>
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>IVA</th>
            <th>Unidad</th>
            <th>Stock</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= number_format($product['price'], 2) ?> €</td>
            <td><?= number_format($product['vat_percent'], 2) ?>%</td>
            <td><?= htmlspecialchars($product['unit']) ?></td>
            <td>
                <?= $product['stock'] !== null ? (int)$product['stock'] : '—' ?>
            </td>
            <td class="text-end">
                <a href="index.php?action=products_edit&id=<?= $product['id'] ?>"
                   class="btn btn-sm btn-warning">
                    ✏️ Editar
                </a>
                <a href="index.php?action=products_delete&id=<?= $product['id'] ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('¿Seguro que quieres eliminar este producto?')">
                    🗑️ Eliminar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>