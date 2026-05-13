<?php
$active = $active ?? 'dashboard';
$prenom = (string) (session('prenom') ?? '');
$nom = (string) (session('nom') ?? '');
$initials = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? $initials : 'AD';
?>
<div class="sidebar-brand">
    <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)">
        <i class="bi bi-shield-check" style="color:var(--leaf)"></i>
    </div>
    <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
</div>
<div class="sidebar-section">Gestion</div>
<ul class="sidebar-nav">
    <li><a href="<?= site_url('admin/dashboard') ?>" class="<?= $active === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
    <li><a href="<?= site_url('admin/employes') ?>" class="<?= $active === 'employes' ? 'active' : '' ?>"><i class="bi bi-people"></i> Employes</a></li>
    <li><a href="<?= site_url('admin/types-conge') ?>" class="<?= $active === 'types' ? 'active' : '' ?>"><i class="bi bi-tags"></i> Types de conge</a></li>
</ul>
<div class="sidebar-user">
    <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem"><?= esc($initials) ?></div>
        <div>
            <div class="user-name"><?= esc(trim($prenom . ' ' . $nom)) ?></div>
            <div class="user-role">Administrateur</div>
        </div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Deconnexion">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>
