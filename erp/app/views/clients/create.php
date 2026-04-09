<h2 class="mb-4">Nuevo Cliente</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="index.php?action=clients_create">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="mb-3">
                <label class="form-label">Nombre / Razón Social *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">CIF / NIF</label>
                    <input type="text" name="tax_id" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="index.php?action=clients" class="btn btn-secondary">⬅ Volver</a>
                <button type="submit" class="btn btn-primary">💾 Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>