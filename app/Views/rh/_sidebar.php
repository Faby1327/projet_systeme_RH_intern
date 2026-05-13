<?php
$active = $active ?? 'dashboard';
$prenom = (string) (session('prenom') ?? '');
$nom = (string) (session('nom') ?? '');
$initials = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? $initials : 'RH';
?>
<div class="sidebar-brand">
    <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
    <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
</div>
<div class="sidebar-section">Menu</div>
<ul class="sidebar-nav">
    <li><a href="<?= site_url('rh/dashboard') ?>" class="<?= $active === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
    <li><a href="<?= site_url('rh/demandes') ?>" class="<?= $active === 'demandes' ? 'active' : '' ?>"><i class="bi bi-inbox"></i> Demandes a traiter</a></li>
</ul>
<div class="sidebar-user">
    <div class="s-user-row">
        <div class="avatar av-blue"><?= esc($initials) ?></div>
        <div>
            <div class="user-name"><?= esc(trim($prenom . ' ' . $nom)) ?></div>
            <div class="user-role">Responsable RH</div>
        </div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Deconnexion">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>
