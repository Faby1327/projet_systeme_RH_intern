<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('admin/_sidebar', ['active' => 'dashboard']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Vue d'ensemble</div>
    <div class="topbar-breadcrumb">Administration</div>
</div>
<div class="topbar-actions">
    <a href="<?= site_url('admin/employes') ?>#form-add" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employe</a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="metrics">
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
        <div class="metric-val"><?= esc((string) $employesActifs) ?></div>
        <div class="metric-label">Employes actifs</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
        <div class="metric-val"><?= esc((string) $pendingDemandes) ?></div>
        <div class="metric-label">Demandes en attente</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
        <div class="metric-val"><?= esc((string) $approvedThisMonth) ?></div>
        <div class="metric-label">Approuvees ce mois</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
        <div class="metric-val"><?= esc((string) $departementsCount) ?></div>
        <div class="metric-label">Departements</div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Demandes recentes</h3>
        <a href="<?= site_url('rh/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir -></a>
    </div>
    <?php if ($recentDemandes === []): ?>
        <div class="empty">
            <i class="bi bi-inbox"></i>
            <p>Aucune demande recente pour le moment.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr><th>Employe</th><th>Type</th><th>Duree</th><th>Statut</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentDemandes as $conge): ?>
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
                        <td><?= esc($conge['type_conge_libelle']) ?></td>
                        <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
                        <td><span class="statut <?= esc($statutClass) ?>"><?= esc($conge['statut']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
