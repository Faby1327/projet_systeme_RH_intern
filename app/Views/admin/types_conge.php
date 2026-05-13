<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('admin/_sidebar', ['active' => 'types']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Types de conge</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Types de conge</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = $errors ?? []; ?>
<div class="form-section">
    <h3>Ajouter un type de conge</h3>
    <form action="<?= site_url('admin/types-conge/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2">
            <div class="f-group">
                <label class="f-label" for="libelle">Libelle</label>
                <input class="f-input" id="libelle" name="libelle" type="text" value="<?= esc(old('libelle') ?? '') ?>" />
                <?php if (isset($errors['libelle'])): ?><div class="f-error"><?= esc($errors['libelle']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="jours_annuels">Jours annuels</label>
                <input class="f-input" id="jours_annuels" name="jours_annuels" type="number" min="0" value="<?= esc(old('jours_annuels') ?? '0') ?>" />
                <?php if (isset($errors['jours_annuels'])): ?><div class="f-error"><?= esc($errors['jours_annuels']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="deductible">Deductible du solde</label>
                <select class="f-select" id="deductible" name="deductible">
                    <option value="1" <?= old('deductible', '1') === '1' ? 'selected' : '' ?>>Oui</option>
                    <option value="0" <?= old('deductible') === '0' ? 'selected' : '' ?>>Non</option>
                </select>
                <?php if (isset($errors['deductible'])): ?><div class="f-error"><?= esc($errors['deductible']) ?></div><?php endif; ?>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Ajouter</button>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Liste des types de conge</h3>
    </div>
    <table class="tbl">
        <thead>
            <tr><th>Libelle</th><th>Jours</th><th>Deductible</th><th>Mise a jour</th></tr>
        </thead>
        <tbody>
            <?php foreach ($typesConge as $type): ?>
                <tr>
                    <td class="td-name"><?= esc($type['libelle']) ?></td>
                    <td class="td-mono"><?= esc((string) $type['jours_annuels']) ?></td>
                    <td><?= (int) $type['deductible'] === 1 ? 'Oui' : 'Non' ?></td>
                    <td>
                        <form action="<?= site_url('admin/types-conge/' . $type['id'] . '/edit') ?>" method="post" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
                            <?= csrf_field() ?>
                            <input class="f-input" type="text" name="libelle" value="<?= esc($type['libelle']) ?>" style="width:180px;padding:6px 8px;font-size:.75rem" />
                            <input class="f-input" type="number" min="0" name="jours_annuels" value="<?= esc((string) $type['jours_annuels']) ?>" style="width:90px;padding:6px 8px;font-size:.75rem" />
                            <select class="f-select" name="deductible" style="width:90px;padding:6px 8px;font-size:.75rem">
                                <option value="1" <?= (int) $type['deductible'] === 1 ? 'selected' : '' ?>>Oui</option>
                                <option value="0" <?= (int) $type['deductible'] === 0 ? 'selected' : '' ?>>Non</option>
                            </select>
                            <button type="submit" class="btn-sm btn-edit"><i class="bi bi-save"></i> Enregistrer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
