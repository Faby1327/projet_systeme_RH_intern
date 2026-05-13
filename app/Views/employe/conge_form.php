<?= $this->extend('layouts/app') ?>

<?= $this->section('sidebar') ?>
<?= view('employe/_sidebar', ['active' => 'create']) ?>
<?= $this->endSection() ?>

<?= $this->section('topbar') ?>
<div>
    <div class="topbar-title">Nouvelle demande</div>
    <div class="topbar-breadcrumb"><a href="<?= site_url('employe/dashboard') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="form-section">
    <h3>Soumettre une demande de conge</h3>
    <form action="<?= site_url('employe/conges/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2">
            <div class="f-group">
                <label class="f-label" for="type_conge_id">Type de conge</label>
                <select class="f-select" id="type_conge_id" name="type_conge_id">
                    <option value="">Choisir un type</option>
                    <?php foreach ($typesConge as $type): ?>
                        <option value="<?= esc($type['id']) ?>" <?= old('type_conge_id') == $type['id'] ? 'selected' : '' ?>>
                            <?= esc($type['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['type_conge_id'])): ?>
                    <div class="f-error"><?= esc($errors['type_conge_id']) ?></div>
                <?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="motif">Motif</label>
                <input class="f-input" id="motif" name="motif" type="text" value="<?= esc(old('motif') ?? '') ?>" placeholder="Motif optionnel" />
                <?php if (isset($errors['motif'])): ?>
                    <div class="f-error"><?= esc($errors['motif']) ?></div>
                <?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="date_debut">Date debut</label>
                <input class="f-input" id="date_debut" name="date_debut" type="date" value="<?= esc(old('date_debut') ?? '') ?>" />
                <?php if (isset($errors['date_debut'])): ?>
                    <div class="f-error"><?= esc($errors['date_debut']) ?></div>
                <?php endif; ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="date_fin">Date fin</label>
                <input class="f-input" id="date_fin" name="date_fin" type="date" value="<?= esc(old('date_fin') ?? '') ?>" />
                <?php if (isset($errors['date_fin'])): ?>
                    <div class="f-error"><?= esc($errors['date_fin']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flash flash-info" style="margin-top:1rem;margin-bottom:0">
            <i class="bi bi-info-circle-fill"></i>
            Les demandes sont calculees en jours ouvrables, hors samedi et dimanche.
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-send"></i> Envoyer la demande</button>
            <a href="<?= site_url('employe/conges') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
        </div>
    </form>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Mes soldes <?= esc($annee) ?></h3>
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
<?= $this->endSection() ?>
