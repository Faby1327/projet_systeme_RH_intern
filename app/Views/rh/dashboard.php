<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('rh/_sidebar', ['active' => 'dashboard']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Tableau de bord RH</div>
    <div class="topbar-breadcrumb">Accueil</div>
</div>
<div class="topbar-actions">
    <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
        <i class="bi bi-hourglass-split"></i> <?= esc((string) ($stats['en_attente'] ?? 0)) ?> en attente
    </span>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="metrics">
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
        <div class="metric-val"><?= esc((string) ($stats['en_attente'] ?? 0)) ?></div>
        <div class="metric-label">En attente</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
        <div class="metric-val"><?= esc((string) $approvedThisMonth) ?></div>
        <div class="metric-label">Approuvees ce mois</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
        <div class="metric-val"><?= esc((string) ($stats['refusee'] ?? 0)) ?></div>
        <div class="metric-label">Refusees</div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Dernieres demandes</h3>
        <a href="<?= site_url('rh/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout -></a>
    </div>
    <?php if ($recentDemandes === []): ?>
        <div class="empty">
            <i class="bi bi-inbox"></i>
            <p>Aucune demande enregistree pour le moment.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr><th>Employe</th><th>Type</th><th>Periode</th><th>Statut</th></tr>
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
                        <td class="td-muted">
                            <?= esc(date('d/m/Y', strtotime($conge['date_debut']))) ?>
                            -
                            <?= esc(date('d/m/Y', strtotime($conge['date_fin']))) ?>
                        </td>
                        <td><span class="statut <?= esc($statutClass) ?>"><?= esc($conge['statut']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
