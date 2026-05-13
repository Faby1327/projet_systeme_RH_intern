<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('employe/_sidebar', ['active' => 'list']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Mes demandes de conge</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
</div>
<div class="topbar-actions">
    <a href="<?= site_url('employe/conges/new') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
        <i class="bi bi-plus-lg"></i> Nouvelle demande
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="data-card">
    <div class="data-card-head">
        <h3>Toutes mes demandes</h3>
    </div>
    <?php if ($conges === []): ?>
        <div class="empty">
            <i class="bi bi-calendar3"></i>
            <p>Aucune demande de conge pour le moment.</p>
        </div>
    <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Periode</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th>Commentaire RH</th>
                    <th>Actions</th>
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
                        <td><span class="type-badge"><?= esc($conge['type_conge_libelle']) ?></span></td>
                        <td class="td-muted">
                            <?= esc(date('d/m/Y', strtotime($conge['date_debut']))) ?>
                            -
                            <?= esc(date('d/m/Y', strtotime($conge['date_fin']))) ?>
                        </td>
                        <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
                        <td><span class="statut <?= esc($statutClass) ?>"><?= esc($conge['statut']) ?></span></td>
                        <td class="td-muted"><?= esc($conge['commentaire_rh'] ?: '-') ?></td>
                        <td>
                            <?php if ($conge['statut'] === 'en_attente'): ?>
                                <form action="<?= site_url('employe/conges/' . $conge['id'] . '/annuler') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-sm btn-cancel"><i class="bi bi-x-circle"></i> Annuler</button>
                                </form>
                            <?php else: ?>
                                <span class="td-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
