<h2 class="mb-4">Editar producto / servicio</h2>

<form method="POST" action="index.php?action=products_edit&id=<?= $product['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="mb-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Precio *</label>
            <input type="number" step="0.01" name="price" class="form-control"
                   value="<?= $product['price'] ?>" required>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">IVA (%)</label>
            <input type="number" step="0.01" name="vat_percent"
                   class="form-control" value="<?= $product['vat_percent'] ?>">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Unidad</label>
            <input type="text" name="unit" class="form-control"
                   value="<?= htmlspecialchars($product['unit']) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Stock (opcional)</label>
        <input type="number" name="stock" class="form-control"
               value="<?= htmlspecialchars($product['stock']) ?>">
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php?action=products" class="btn btn-secondary">
            ⬅ Volver
        </a>
        <button class="btn btn-primary">
            💾 Guardar cambios
        </button>
    </div>
</form>