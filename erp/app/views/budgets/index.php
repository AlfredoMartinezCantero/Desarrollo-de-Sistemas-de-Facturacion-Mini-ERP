<h2 class="mb-4">Presupuestos</h2>

<div class="mb-3 text-end">
    index.php?action=budgets_create
        ➕ Nuevo presupuesto
    </a>
</div>

<?php if (empty($budgets)): ?>
    <div class="alert alert-info">
        No tienes presupuestos creados todavía.
    </div>
<?php else: ?>
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($budgets as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['number']) ?></td>
            <td><?= htmlspecialchars($b['client_name']) ?></td>
            <td><?= number_format($b['total'], 2) ?> €</td>
            <td>
                <?php if ($b['status'] === 'approved'): ?>
                    <span class="badge bg-success">Aprobado</span>
                <?php elseif ($b['status'] === 'rejected'): ?>
                    <span class="badge bg-danger">Rechazado</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Borrador</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                index.php?action=budget_show&id=<?= $b['id'] ?>                   class="btn btn-sm btn-primary">
                    👁 Ver
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>