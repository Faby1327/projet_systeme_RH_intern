<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('admin/_sidebar', ['active' => 'employes']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Gestion des employes</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('admin/dashboard') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employes</div>
</div>
<div class="topbar-actions">
    <a href="#form-add" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = $errors ?? []; ?>
<div class="form-section" id="form-add">
    <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employe</h3>
    <form action="<?= site_url('admin/employes/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
                <label class="f-label">Prenom</label>
                <input type="text" name="prenom" class="f-input" placeholder="Jean" value="<?= esc(old('prenom') ?? '') ?>" />
                <?php if (isset($errors['prenom'])): ?><div class="f-error"><?= esc($errors['prenom']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label">Nom</label>
                <input type="text" name="nom" class="f-input" placeholder="Rakoto" value="<?= esc(old('nom') ?? '') ?>" />
                <?php if (isset($errors['nom'])): ?><div class="f-error"><?= esc($errors['nom']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" name="email" class="f-input" placeholder="jean.rakoto@techmada.mg" value="<?= esc(old('email') ?? '') ?>" />
                <?php if (isset($errors['email'])): ?><div class="f-error"><?= esc($errors['email']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label">Mot de passe initial</label>
                <input type="password" name="password" class="f-input" placeholder="A communiquer a l'employe" />
                <?php if (isset($errors['password'])): ?><div class="f-error"><?= esc($errors['password']) ?></div><?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label">Departement</label>
                <select class="f-select" name="departement_id">
                    <?php foreach ($departements as $departement): ?>
                        <option value="<?= esc($departement['id']) ?>" <?= old('departement_id') == $departement['id'] ? 'selected' : '' ?>><?= esc($departement['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="f-group">
                <label class="f-label">Date d'embauche</label>
                <input type="date" name="date_embauche" class="f-input" value="<?= esc(old('date_embauche') ?? date('Y-m-d')) ?>" />
            </div>
        </div>
        <div class="flash flash-info" style="margin-bottom:1rem">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.82rem">Les soldes de conges seront initialises automatiquement selon les types de conge configures.</span>
        </div>
        <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Creer l'employe</button>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Tous les employes</h3>
    </div>
    <table class="tbl">
        <thead>
            <tr><th>Employe</th><th>Departement</th><th>Embauche</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($employes as $employe): ?>
                <tr>
                    <td>
                        <div class="profile-row">
                            <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem">
                                <?= esc(strtoupper(substr($employe['prenom'], 0, 1) . substr($employe['nom'], 0, 1))) ?>
                            </div>
                            <div class="profile-info">
                                <div class="pname"><?= esc($employe['prenom'] . ' ' . $employe['nom']) ?></div>
                                <div class="pdept"><?= esc($employe['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="td-muted"><?= esc($employe['departement_nom'] ?? '—') ?></td>
                    <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($employe['date_embauche']) ?></td>
                    <td>
                        <?php if ((int) $employe['actif'] === 1): ?>
                            <span class="statut s-approuvee" style="font-size:.68rem">actif</span>
                        <?php else: ?>
                            <span class="statut s-annulee" style="font-size:.68rem">inactif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <form action="<?= site_url('admin/employes/' . $employe['id'] . '/edit') ?>" method="post" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center">
                                <?= csrf_field() ?>
                                <input class="f-input" type="text" name="prenom" value="<?= esc($employe['prenom']) ?>" style="width:100px;padding:6px 8px;font-size:.75rem" />
                                <input class="f-input" type="text" name="nom" value="<?= esc($employe['nom']) ?>" style="width:100px;padding:6px 8px;font-size:.75rem" />
                                <input class="f-input" type="email" name="email" value="<?= esc($employe['email']) ?>" style="width:160px;padding:6px 8px;font-size:.75rem" />
                                <input class="f-input" type="password" name="password" placeholder="Nouveau mdp" style="width:120px;padding:6px 8px;font-size:.75rem" />
                                <select class="f-select" name="departement_id" style="width:110px;padding:6px 8px;font-size:.75rem">
                                    <?php foreach ($departements as $departement): ?>
                                        <option value="<?= esc($departement['id']) ?>" <?= (int) $employe['departement_id'] === (int) $departement['id'] ? 'selected' : '' ?>><?= esc($departement['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input class="f-input" type="date" name="date_embauche" value="<?= esc($employe['date_embauche']) ?>" style="width:130px;padding:6px 8px;font-size:.75rem" />
                                <button class="btn-sm btn-edit" type="submit"><i class="bi bi-save"></i> Editer</button>
                            </form>
                            <form action="<?= site_url('admin/employes/' . $employe['id'] . '/desactiver') ?>" method="post">
                                <?= csrf_field() ?>
                                <?php if ((int) $employe['actif'] === 1): ?>
                                    <button class="btn-sm btn-del" type="submit"><i class="bi bi-slash-circle"></i> Desactiver</button>
                                <?php else: ?>
                                    <button class="btn-sm btn-view" type="submit"><i class="bi bi-arrow-counterclockwise"></i> Reactiver</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
