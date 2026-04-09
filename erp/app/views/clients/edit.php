<h2 class="mb-4">Editar cliente</h2>

<form method="POST" action="index.php?action=clients_edit&id=<?= $client['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="mb-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($client['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">CIF / NIF</label>
        <input type="text" name="tax_id" class="form-control"
               value="<?= htmlspecialchars($client['tax_id']) ?>">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($client['email']) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($client['phone']) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($client['address']) ?></textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="index.php?action=clients" class="btn btn-secondary">
            ⬅ Volver
        </a>
        <button class="btn btn-primary">
            💾 Guardar cambios
        </button>
    </div>
</form>