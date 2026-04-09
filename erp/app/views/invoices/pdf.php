<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
        .right { text-align: right; }
        .invoice-header { margin-bottom: 30px; }
    </style>
</head>
<body>

<div class="invoice-header">
    <p>
        <strong>Emisor:</strong><br>
        <strong><?= $company['company_name'] ?></strong><br>
        <?= $company['tax_id'] ?><br>
        <?= nl2br($company['address']) ?>
    </p>
    <hr>
</div>

<h1>Factura <?= $invoice['invoice_number'] ?></h1>

<p>
    <strong>Cliente:</strong> <?= $invoice['client_name'] ?><br>
    <strong>CIF/NIF:</strong> <?= $invoice['tax_id'] ?><br>
    <strong>Email:</strong> <?= $invoice['email'] ?><br>
    <strong>Fecha:</strong> <?= $invoice['issue_date'] ?>
</p>

<table>
    <tr>
        <th>Concepto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>IVA %</th>
        <th>Total</th>
    </tr>

    <?php foreach ($items as $item): ?>
    <tr>
        <td><?= $item['product_name'] ?></td>
        <td class="right"><?= $item['quantity'] ?></td>
        <td class="right"><?= $item['unit_price'] ?> €</td>
        <td class="right"><?= $item['vat_percent'] ?>%</td>
        <td class="right"><?= $item['line_total'] ?> €</td>
    </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4" class="right"><strong>Subtotal</strong></td>
        <td class="right"><?= $invoice['subtotal'] ?> €</td>
    </tr>
    <tr>
        <td colspan="4" class="right"><strong>IVA</strong></td>
        <td class="right"><?= $invoice['vat_total'] ?> €</td>
    </tr>
    <tr>
        <td colspan="4" class="right"><strong>Total</strong></td>
        <td class="right"><strong><?= $invoice['total'] ?> €</strong></td>
    </tr>
</table>

</body>
</html>