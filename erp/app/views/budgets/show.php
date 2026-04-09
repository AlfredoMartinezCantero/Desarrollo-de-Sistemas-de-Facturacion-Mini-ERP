<h2 class="mb-4">Presupuesto <?= htmlspecialchars($budget['number']) ?></h2>

<div class="mb-3">
    <strong>Cliente:</strong><br>
    <?= htmlspecialchars($budget['client_name']) ?>
</div>

<div class="mb-3">
    <strong>Estado:</strong>
    <?php if ($budget['status'] === 'approved'): ?>
        <span class="badge bg-success">Aprobado</span>
    <?php elseif ($budget['status'] === 'rejected'): ?>
        <span class="badge bg-danger">Rechazado</span>
    <?php else: ?>
        <span class="badge bg-secondary">Borrador</span>
    <?php endif; ?>
</div>

<table class="table table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Concepto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>IVA</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['unit_price'],2) ?> €</td>
            <td><?= number_format($item['vat_percent'],2) ?>%</td>
            <td><?= number_format($item['line_total'],2) ?> €</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">Subtotal</th>
            <th><?= number_format($budget['subtotal'],2) ?> €</th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">IVA</th>
            <th><?= number_format($budget['vat_total'],2) ?> €</th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">Total</th>
            <th><?= number_format($budget['total'],2) ?> €</th>
        </tr>
    </tfoot>
</table>

<div class="d-flex justify-content-between mt-4">
    index.php?action=budgets
        ⬅ Volver
    </a>

    <?php if ($budget['status'] === 'approved'): ?>
        index.php?action=budget_to_invoice&id=<?= $budget['id'] ?>
            🧾 Convertir en factura
        </a>
    <?php endif; ?>
</div>
