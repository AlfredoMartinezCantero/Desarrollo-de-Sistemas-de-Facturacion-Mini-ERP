<h2 class="mb-4">Nuevo presupuesto</h2>

index.php?action=budgets_create
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="mb-3">
        <label class="form-label">Cliente *</label>
        <select name="client_id" class="form-select" required>
            <option value="">— Selecciona cliente —</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <h5 class="mt-4 mb-2">Líneas del presupuesto</h5>

    <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="row mb-2">
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
                <input type="number" step="0.01"
                       name="items[<?= $i ?>][qty]"
                       class="form-control"
                       placeholder="Cantidad">
            </div>
        </div>
    <?php endfor; ?>

    <div class="d-flex justify-content-between mt-4">
        index.php?action=budgets
            ⬅ Volver
        </a>
        <button class="btn btn-primary">
            💾 Guardar presupuesto
        </button>
    </div>
</form>