<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('rh/_sidebar', ['active' => 'demandes']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Demandes RH</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('rh/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="form-section">
    <h3>Filtrer les demandes</h3>
    <form method="get" action="<?= site_url('rh/demandes') ?>">
        <div class="form-grid-2">
            <div class="f-group">
                <label class="f-label" for="departement_id">Departement</label>
                <select class="f-select" id="departement_id" name="departement_id">
                    <option value="">Tous les departements</option>
                    <?php foreach ($departements as $departement): ?>
                        <option value="<?= esc($departement['id']) ?>" <?= $selectedDepartement === (string) $departement['id'] ? 'selected' : '' ?>>
                            <?= esc($departement['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="f-group">
                <label class="f-label" for="statut">Statut</label>
                <select class="f-select" id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <?php foreach (['en_attente', 'approuvee', 'refusee', 'annulee'] as $statut): ?>
                        <option value="<?= esc($statut) ?>" <?= $selectedStatut === $statut ? 'selected' : '' ?>><?= esc($statut) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-funnel"></i> Filtrer</button>
            <a href="<?= site_url('rh/demandes') ?>" class="btn-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reinitialiser</a>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Toutes les demandes</h3>
    </div>
    <?php if ($conges === []): ?>
        <div class="empty">
            <i class="bi bi-inbox"></i>
            <p>Aucune demande pour les filtres selectionnes.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Employe</th>
                    <th>Departement</th>
                    <th>Type</th>
                    <th>Periode</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th>Action RH</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conges as $conge): ?>
                    <?php
                    $statutClass = match ($conge['statut']) {
                        'approuvee' => 's-approuvee',
                        'refusee' => 's-refusee',
                        'annulee' => 's-annulee',
                        default => 's-attente',
                    };
                    ?>
                    <tr>
                        <td class="td-name"><?= esc(trim($conge['employe_prenom'] . ' ' . $conge['employe_nom'])) ?></td>
                        <td class="td-muted"><?= esc($conge['departement_nom'] ?? '-') ?></td>
                        <td><span class="type-badge"><?= esc($conge['type_conge_libelle']) ?></span></td>
                        <td class="td-muted">
                            <?= esc(date('d/m/Y', strtotime($conge['date_debut']))) ?>
                            -
                            <?= esc(date('d/m/Y', strtotime($conge['date_fin']))) ?>
                        </td>
                        <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
                        <td><span class="statut <?= esc($statutClass) ?>"><?= esc($conge['statut']) ?></span></td>
                        <td>
                            <?php if ($conge['statut'] === 'en_attente'): ?>
                                <div class="action-btns">
                                    <form action="<?= site_url('rh/demandes/' . $conge['id'] . '/approuver') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="text" class="f-input" name="commentaire_rh" placeholder="Commentaire optionnel" style="width:180px;padding:6px 8px;font-size:.75rem;margin-bottom:6px" />
                                        <button type="submit" class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Approuver</button>
                                    </form>
                                    <form action="<?= site_url('rh/demandes/' . $conge['id'] . '/refuser') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <input type="text" class="f-input" name="commentaire_rh" placeholder="Commentaire optionnel" style="width:180px;padding:6px 8px;font-size:.75rem;margin-bottom:6px" />
                                        <button type="submit" class="btn-sm btn-refuse"><i class="bi bi-x-lg"></i> Refuser</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="td-muted"><?= esc($conge['commentaire_rh'] ?: '-') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Soldes employes <?= esc((string) $annee) ?></h3>
    </div>
    <?php if ($soldes === []): ?>
        <div class="empty">
            <i class="bi bi-people"></i>
            <p>Aucun solde disponible pour cette annee.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Employe</th>
                    <th>Departement</th>
                    <th>Type</th>
                    <th>Attribues</th>
                    <th>Pris</th>
                    <th>Restants</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soldes as $solde): ?>
                    <tr>
                        <td class="td-name"><?= esc(trim($solde['employe_prenom'] . ' ' . $solde['employe_nom'])) ?></td>
                        <td class="td-muted"><?= esc($solde['departement_nom'] ?? '-') ?></td>
                        <td><?= esc($solde['type_conge_libelle']) ?></td>
                        <td class="td-mono"><?= esc((string) $solde['jours_attribues']) ?></td>
                        <td class="td-mono"><?= esc((string) $solde['jours_pris']) ?></td>
                        <td class="td-mono"><?= esc((string) $solde['jours_restant']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
