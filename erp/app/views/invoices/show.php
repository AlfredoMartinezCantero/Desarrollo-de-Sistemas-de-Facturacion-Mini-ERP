<h2 class="mb-4">Factura <?= htmlspecialchars($invoice['invoice_number']) ?></h2>

<div class="row mb-4">
    <div class="col-md-6">
        <h5>Emisor</h5>
        <p>
            <strong><?= htmlspecialchars($company['company_name']) ?></strong><br>
            <?= htmlspecialchars($company['tax_id']) ?><br>
            <?= nl2br(htmlspecialchars($company['address'])) ?>
        </p>
    </div>

    <div class="col-md-6">
        <h5>Cliente</h5>
        <p>
            <strong><?= htmlspecialchars($invoice['client_name']) ?></strong><br>
            <?= htmlspecialchars($invoice['tax_id']) ?><br>
            <?= htmlspecialchars($invoice['email']) ?>
        </p>
    </div>
</div>

<table class="table table-bordered align-middle mb-4">
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
            <td><?= number_format($item['unit_price'], 2) ?> €</td>
            <td><?= number_format($item['vat_percent'], 2) ?>%</td>
            <td><?= number_format($item['line_total'], 2) ?> €</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">Subtotal</th>
            <th><?= number_format($invoice['subtotal'], 2) ?> €</th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">IVA</th>
            <th><?= number_format($invoice['vat_total'], 2) ?> €</th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">Total</th>
            <th><?= number_format($invoice['total'], 2) ?> €</th>
        </tr>
    </tfoot>
</table>

<div class="mb-4">
    <strong>Estado:</strong>
    <?php if ($invoice['status'] === 'paid'): ?>
        <span class="badge bg-success">Pagada</span>
    <?php elseif ($invoice['status'] === 'pending'): ?>
        <span class="badge bg-warning text-dark">Pendiente</span>
    <?php else: ?>
        <span class="badge bg-danger">Cancelada</span>
    <?php endif; ?>
</div>

<hr>

<h5 class="mt-4">Pagos</h5>

<?php if (!empty($payments)): ?>
<table class="table table-sm table-bordered">
    <thead class="table-light">
        <tr>
            <th>Fecha</th>
            <th>Método</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['payment_date']) ?></td>
            <td><?= htmlspecialchars($p['method']) ?></td>
            <td><?= number_format($p['amount'], 2) ?> €</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-secondary">
        No hay pagos registrados todavía.
    </div>
<?php endif; ?>

<?php if ($pending > 0): ?>
<h6 class="mt-3">Registrar pago</h6>

<form method="POST" action="index.php?action=payment_store">
    <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">

    <div class="row">
        <div class="col-md-4 mb-2">
            <input type="number" step="0.01" name="amount"
                   class="form-control" placeholder="Importe" required>
        </div>
        <div class="col-md-4 mb-2">
            <input type="date" name="payment_date"
                   class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-4 mb-2">
            <input type="text" name="method"
                   class="form-control" placeholder="Método (transferencia, efectivo…)">
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-sm mt-2">
        💳 Registrar pago
    </button>
</form>
<?php endif; ?>

<hr>

<div class="d-flex justify-content-between mt-4">
    <a href="index.php?action=invoices">
        ⬅ Volver
    </a>

    <div>
        <a href="index.php?action=invoice_pdf&id=<?= $invoice['id'] ?>" class="btn btn-outline-secondary btn-sm">
            📄 PDF
        </a>

        <a href="index.php?action=invoice_email&id=<?= $invoice['id'] ?>" class="btn btn-outline-primary btn-sm">
            ✉️ Email
        </a>
    </div>
</div>