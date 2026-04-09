<h2 class="mb-4">Nuevo presupuesto</h2>

<div class="card shadow-sm p-4">
    <form method="POST" action="index.php?action=budgets_create">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="mb-4">
            <label class="form-label fw-bold">Cliente *</label>
            <select name="client_id" class="form-select" required>
                <option value="">— Selecciona cliente —</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <h5 class="mb-3">Líneas del presupuesto</h5>
        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-8">
                    <select name="items[<?= $i ?>][product_id]" class="form-select">
                        <option value="">— Producto / servicio —</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price'],2) ?> €)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="items[<?= $i ?>][qty]" class="form-control" placeholder="Cant.">
                </div>
            </div>
        <?php endfor; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?action=budgets" class="btn btn-secondary">⬅ Volver</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Presupuesto</button>
        </div>
    </form>
</div>