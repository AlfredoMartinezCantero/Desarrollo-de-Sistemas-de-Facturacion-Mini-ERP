<h2 class="mb-4">Nuevo producto / servicio</h2>

<form method="POST" action="index.php?action=products_create">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="mb-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Precio *</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">IVA (%)</label>
            <input type="number" step="0.01" name="vat_percent"
                   class="form-control" value="21">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Unidad</label>
            <input type="text" name="unit" class="form-control" value="ud">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Stock (opcional)</label>
        <input type="number" name="stock" class="form-control">
        <div class="form-text">
            Déjalo vacío si es un servicio.
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php?action=products" class="btn btn-secondary">
            ⬅ Volver
        </a>
        <button class="btn btn-primary">
            💾 Guardar producto
        </button>
    </div>
</form>