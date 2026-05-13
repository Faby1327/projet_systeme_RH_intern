<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('employe/_sidebar', ['active' => 'dashboard']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Tableau de bord</div>
    <div class="topbar-breadcrumb">Accueil</div>
</div>
<div class="topbar-actions">
    <a href="<?= site_url('employe/conges/new') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
        <i class="bi bi-plus-lg"></i> Nouvelle demande
    </a>
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
        <div class="metric-val"><?= esc((string) ($stats['approuvee'] ?? 0)) ?></div>
        <div class="metric-label">Approuvees</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
        <div class="metric-val"><?= esc((string) $joursRestants) ?></div>
        <div class="metric-label">Jours restants</div>
        <div class="metric-sub">sur <?= esc((string) $joursAttribues) ?> cette annee</div>
    </div>
    <div class="metric">
        <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
        <div class="metric-val"><?= esc((string) ($stats['refusee'] ?? 0)) ?></div>
        <div class="metric-label">Refusee</div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Mes soldes <?= esc((string) $annee) ?></h3>
        <a href="<?= site_url('employe/conges/new') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Nouvelle demande -></a>
    </div>
    <div style="padding:1rem 1.25rem">
        <?php foreach ($soldes as $solde): ?>
            <?php
            $attribues = (int) $solde['jours_attribues'];
            $pris = (int) $solde['jours_pris'];
            $restant = (int) $solde['jours_restant'];
            $ratio = $attribues > 0 ? max(0, min(100, (int) round(($restant / $attribues) * 100))) : 100;
            $fillClass = $ratio <= 25 ? 'danger' : ($ratio <= 50 ? 'warn' : '');
            ?>
            <div class="solde-card">
                <div class="solde-header">
                    <div class="solde-type"><?= esc($solde['type_conge_libelle']) ?></div>
                    <div class="solde-nums"><strong><?= esc((string) $restant) ?>j</strong> restants / <?= esc((string) $attribues) ?>j</div>
                </div>
                <div class="solde-bar"><div class="solde-fill <?= esc($fillClass) ?>" style="width:<?= esc((string) $ratio) ?>%"></div></div>
                <div class="solde-label"><?= esc((string) $pris) ?> jour(s) deja pris cette annee.</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Mes dernieres demandes</h3>
        <a href="<?= site_url('employe/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout -></a>
    </div>
    <?php if ($recentConges === []): ?>
        <div class="empty">
            <i class="bi bi-calendar3"></i>
            <p>Aucune demande enregistree pour le moment.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Periode</th>
                    <th>Jours</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentConges as $conge): ?>
                    <?php
                    $statutClass = match ($conge['statut']) {
                        'approuvee' => 's-approuvee',
                        'refusee' => 's-refusee',
                        'annulee' => 's-annulee',
                        default => 's-attente',
                    };
                    ?>
                    <tr>
                        <td><span class="type-badge"><?= esc($conge['type_conge_libelle']) ?></span></td>
                        <td class="td-muted">
                            <?= esc(date('d/m/Y', strtotime($conge['date_debut']))) ?>
                            -
                            <?= esc(date('d/m/Y', strtotime($conge['date_fin']))) ?>
                        </td>
                        <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
                        <td><span class="statut <?= esc($statutClass) ?>"><?= esc($conge['statut']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
