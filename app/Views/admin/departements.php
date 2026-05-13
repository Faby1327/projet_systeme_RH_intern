<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('admin/_sidebar', ['active' => 'departements']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Departements</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Departements</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = $errors ?? []; ?>
<div class="form-section">
    <h3>Ajouter un departement</h3>
    <form action="<?= site_url('admin/departements/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2">
            <div class="f-group">
                <label class="f-label" for="nom">Nom</label>
                <input class="f-input" id="nom" name="nom" type="text" value="<?= esc(old('nom') ?? '') ?>" />
                <?php if (isset($errors['nom'])): ?><div class="f-error"><?= esc($errors['nom']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="description">Description</label>
                <input class="f-input" id="description" name="description" type="text" value="<?= esc(old('description') ?? '') ?>" />
                <?php if (isset($errors['description'])): ?><div class="f-error"><?= esc($errors['description']) ?></div><?php endif; ?>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Ajouter</button>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Liste des departements</h3>
    </div>
    <table class="tbl">
        <thead>
            <tr><th>Nom</th><th>Description</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($departements as $departement): ?>
                <tr>
                    <td class="td-name"><?= esc($departement['nom']) ?></td>
                    <td class="td-muted"><?= esc($departement['description'] ?: '-') ?></td>
                    <td>
                        <div class="action-btns">
                            <form action="<?= site_url('admin/departements/' . $departement['id'] . '/edit') ?>" method="post" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
                                <?= csrf_field() ?>
                                <input class="f-input" type="text" name="nom" value="<?= esc($departement['nom']) ?>" style="width:140px;padding:6px 8px;font-size:.75rem" />
                                <input class="f-input" type="text" name="description" value="<?= esc($departement['description'] ?? '') ?>" style="width:200px;padding:6px 8px;font-size:.75rem" />
                                <button type="submit" class="btn-sm btn-edit"><i class="bi bi-save"></i> Enregistrer</button>
                            </form>
                            <form action="<?= site_url('admin/departements/' . $departement['id'] . '/delete') ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-sm btn-del"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
