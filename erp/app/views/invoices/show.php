<h2>Factura <?= htmlspecialchars($invoice['invoice_number']) ?></h2>

<p>
<strong>Emisor:</strong><br>
<?= htmlspecialchars($company['company_name']) ?><br>
<?= htmlspecialchars($company['tax_id']) ?><br>
<?= nl2br(htmlspecialchars($company['address'])) ?>
</p>

<hr>

<p>
<strong>Cliente:</strong><br>
<?= htmlspecialchars($invoice['client_name']) ?><br>
<?= htmlspecialchars($invoice['tax_id']) ?><br>
<?= htmlspecialchars($invoice['email']) ?>
</p>

<table border="1" cellpadding="5">
    <tr>
        <th>Concepto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>IVA</th>
        <th>Total</th>
    </tr>

    <?php foreach ($items as $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= $item['unit_price'] ?> €</td>
        <td><?= $item['vat_percent'] ?>%</td>
        <td><?= $item['line_total'] ?> €</td>
    </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4" align="right"><strong>Total</strong></td>
        <td><strong><?= $invoice['total'] ?> €</strong></td>
    </tr>
</table>