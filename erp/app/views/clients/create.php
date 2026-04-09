<h2 class="mb-4">Clientes</h2>

<div class="mb-3 text-end">
    <a href="index.php?action=clients_create" class="btn btn-primary">
        ➕ Nuevo cliente
    </a>
</div>

<?php if (empty($clients)): ?>
    <div class="alert alert-info">
        No tienes clientes registrados todavía.
    </div>
<?php else: ?>
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>CIF / NIF</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client['name']) ?></td>
            <td><?= htmlspecialchars($client['tax_id']) ?></td>
            <td><?= htmlspecialchars($client['email']) ?></td>
            <td><?= htmlspecialchars($client['phone']) ?></td>
            <td class="text-end">
                <a href="index.php?action=clients_edit&id=<?= $client['id'] ?>"
                   class="btn btn-sm btn-warning">
                    ✏️ Editar
                </a>
                <a href="index.php?action=clients_delete&id=<?= $client['id'] ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('¿Seguro que quieres eliminar este cliente?')">
                    🗑️ Eliminar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>