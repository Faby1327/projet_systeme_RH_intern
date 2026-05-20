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
        <h3>Calendrier des demandes</h3>
        <a href="<?= site_url('employe/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout -></a>
    </div>
    <link href="<?= base_url('calendar-chart/vendor/fullcalendar/index.global.min.css') ?>" rel="stylesheet">
    <style>
        .employee-calendar-shell {
            padding: 1rem 1.25rem 1.25rem;
        }

        #employee-calendar {
            max-width: 100%;
            margin: 0 auto;
        }

        #employee-calendar .fc {
            font-family: 'DM Sans', sans-serif;
        }

        #employee-calendar .fc-toolbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--ink);
        }

        #employee-calendar .fc-button {
            background: var(--forest);
            border-color: var(--forest);
            color: var(--white);
            box-shadow: none;
        }

        #employee-calendar .fc-button:hover {
            background: var(--forest2);
            border-color: var(--forest2);
        }

        #employee-calendar .fc-button-primary:not(:disabled).fc-button-active,
        #employee-calendar .fc-button-primary:not(:disabled):active {
            background: var(--ink);
            border-color: var(--ink);
        }

        #employee-calendar .fc-theme-standard td,
        #employee-calendar .fc-theme-standard th,
        #employee-calendar .fc-theme-standard .fc-scrollgrid {
            border-color: var(--border);
        }

        #employee-calendar .fc-day-today {
            background: rgba(95, 168, 118, .12);
        }

        #employee-calendar .fc-event {
            border: none;
            border-radius: 8px;
            padding: 3px 5px;
            opacity: 1;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.45), 0 1px 2px rgba(28,43,30,.08);
        }

        #employee-calendar .fc-event-main {
            padding: 0;
        }

        #employee-calendar .fc-col-header-cell-cushion,
        #employee-calendar .fc-daygrid-day-number {
            color: var(--ink);
            text-decoration: none;
        }

        .employee-event-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .employee-event-title {
            font-size: .74rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .employee-event-meta {
            font-size: .68rem;
            opacity: 1;
            line-height: 1.2;
        }

        #employee-calendar .fc-status-pending {
            background: #ffd46b;
            color: #5f3a00;
        }

        #employee-calendar .fc-status-approved {
            background: #9fe0b6;
            color: #0f3d22;
        }

        #employee-calendar .fc-status-refused {
            background: #ffb8ad;
            color: #74180f;
        }

        #employee-calendar .fc-status-cancelled {
            background: #d9d4c8;
            color: #38423c;
        }

        @media (max-width: 900px) {
            #employee-calendar .fc-toolbar {
                flex-direction: column;
                align-items: flex-start;
                gap: .75rem;
            }
        }
    </style>
    <div class="employee-calendar-shell">
        <div id="employee-calendar"></div>
    </div>
</div>

<div class="data-card">
    <div class="data-card-head">
        <h3>Total des demandes par type</h3>
    </div>
    <style>
        .employee-chart-shell {
            padding: 1rem 1.25rem 1.25rem;
        }

        .employee-chart-card {
            max-width: 760px;
            min-height: 340px;
        }

        #employee-conges-chart {
            width: 100%;
            height: 320px;
        }
    </style>
    <div class="employee-chart-shell">
        <div class="employee-chart-card">
            <canvas id="employee-conges-chart"></canvas>
        </div>
    </div>
</div>

<?php
$calendarConges = [];
foreach (($dashboardConges ?? []) as $conge) {
    $statusLabel = match ((string) $conge['statut']) {
        'approuvee' => 'Approuvee',
        'refusee' => 'Refusee',
        'annulee' => 'Annulee',
        default => 'En attente',
    };

    $statusClass = match ((string) $conge['statut']) {
        'approuvee' => 'fc-status-approved',
        'refusee' => 'fc-status-refused',
        'annulee' => 'fc-status-cancelled',
        default => 'fc-status-pending',
    };

    $startDate = new DateTimeImmutable((string) $conge['date_debut']);
    $endDate = new DateTimeImmutable((string) $conge['date_fin']);
    $eventIndex = 0;

    for ($current = $startDate; $current <= $endDate; $current = $current->modify('+1 day')) {
        $dayOfWeek = (int) $current->format('N');
        if ($dayOfWeek >= 6) {
            continue;
        }

        $isoDate = $current->format('Y-m-d');
        $calendarConges[] = [
            'id' => (int) $conge['id'] . '-' . $eventIndex,
            'title' => (string) $conge['type_conge_libelle'],
            'start' => $isoDate,
            'end' => $isoDate,
            'allDay' => true,
            'classNames' => [$statusClass],
            'extendedProps' => [
                'statut' => $statusLabel,
                'periode' => date('d/m/Y', strtotime((string) $conge['date_debut'])) . ' - ' . date('d/m/Y', strtotime((string) $conge['date_fin'])),
                'nbJours' => (int) $conge['nb_jours'],
                'commentaireRh' => (string) ($conge['commentaire_rh'] ?? ''),
            ],
        ];
        $eventIndex++;
    }
}

$congesTypeLabels = array_map(
    static fn (array $row): string => (string) $row['libelle'],
    $congesByType ?? []
);

$congesTypeTotals = array_map(
    static fn (array $row): int => (int) $row['total'],
    $congesByType ?? []
);
?>
<script src="<?= base_url('calendar-chart/vendor/fullcalendar/index.global.min.js') ?>"></script>
<script src="<?= base_url('calendar-chart/vendor/fullcalendar/locales/fr.global.min.js') ?>"></script>
<script src="<?= base_url('calendar-chart/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
    const calendarEl = document.getElementById('employee-calendar');

    if (calendarEl && typeof FullCalendar !== 'undefined') {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: {
                today: 'Aujourd hui',
                month: 'Mois',
                week: 'Semaine'
            },
            firstDay: 1,
            events: <?= json_encode($calendarConges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            eventDidMount: function(info) {
                const props = info.event.extendedProps;
                info.el.setAttribute(
                    'title',
                    `${info.event.title}\n${props.statut}\n${props.periode}\n${props.nbJours} jour(s)${props.commentaireRh ? '\n' + props.commentaireRh : ''}`
                );
            },
            eventContent: function(arg) {
                const props = arg.event.extendedProps;
                const wrapper = document.createElement('div');
                wrapper.className = 'employee-event-content';
                wrapper.innerHTML = `
                    <div class="employee-event-title">${arg.event.title}</div>
                    <div class="employee-event-meta">${props.statut} · ${props.nbJours}j</div>
                `;
                return { domNodes: [wrapper] };
            }
        });

        calendar.render();
    }

    const chartEl = document.getElementById('employee-conges-chart');

    if (chartEl && typeof Chart !== 'undefined') {
        const chartLabels = <?= json_encode($congesTypeLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const chartTotals = <?= json_encode($congesTypeTotals, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        new Chart(chartEl, {
            type: 'pie',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Demandes',
                    data: chartTotals,
                    backgroundColor: [
                        '#9fe0b6',
                        '#ffd46b',
                        '#8fc4ff',
                        '#ffb8ad',
                        '#d9d4c8'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#1c2b1e',
                            boxWidth: 14,
                            boxHeight: 14,
                            generateLabels: function(chart) {
                                const labels = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                return labels.map((label, index) => ({
                                    ...label,
                                    text: `${chartLabels[index]} : ${chartTotals[index]}`
                                }));
                            },
                            font: {
                                family: 'DM Sans'
                            },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ' : ' + context.parsed + ' demande(s)';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
