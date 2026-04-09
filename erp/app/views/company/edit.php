<h2 class="mb-4">Datos fiscales del emisor</h2>

index.php?action=company">
    <div class="mb-3">
        <label class="form-label">Nombre fiscal</label>
        <input class="form-control" name="company_name"
               value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">CIF / NIF</label>
        <input class="form-control" name="tax_id"
               value="<?= htmlspecialchars($company['tax_id'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea class="form-control" name="address" required><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email"
                   value="<?= htmlspecialchars($company['email'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Teléfono</label>
            <input class="form-control" name="phone"
                   value="<?= htmlspecialchars($company['phone'] ?? '') ?>">
        </div>
    </div>

    <button class="btn btn-primary">Guardar</button>
</form>
