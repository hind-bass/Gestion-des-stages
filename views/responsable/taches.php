<?php
/**
 * Vue : Tâches du responsable
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches - Espace Responsable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../layouts/header_responsable.php'; ?>
    <style>
    .sidebar { background: #1e40af; min-height: 100vh; padding-top: 20px; }
    .sidebar .nav-link { color: #e0e7ff; font-weight: 600; padding: 12px 16px; border-radius: 8px; margin: 6px 10px; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #1d4ed8; color: #fff; }
    .card { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
    .h2 { color: #1e40af; font-weight: 700; }
    .badge-status { border-radius: 20px; padding: 6px 10px; font-weight: 600; }
    .badge-terminee { background: #10b981; color: white; }
    .badge-en_cours { background: #f59e0b; color: white; }
    .badge-a_faire { background: #64748b; color: white; }
    </style>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable"><i class="fas fa-tachometer-alt me-2"></i><span class="nav-text">Dashboard</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/stagiaires"><i class="fas fa-users me-2"></i><span class="nav-text">Stagiaires</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>responsable/taches"><i class="fas fa-tasks me-2"></i><span class="nav-text">Tâches</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/evaluations"><i class="fas fa-star me-2"></i><span class="nav-text">Évaluations</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/rapports"><i class="fas fa-file-alt me-2"></i><span class="nav-text">Rapports</span></a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tâches</h1>
                    <button type="button" class="btn btn-primary" onclick="creerTache()"><i class="fas fa-plus me-2"></i>Nouvelle tâche</button>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <?php $taches = $taches ?? []; ?>
                        <?php if (!empty($taches)) { ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Stagiaire</th>
                                            <th>Titre</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Statut</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($taches as $t) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars(($t['stagiaire_nom'] ?? '') . ' ' . ($t['stagiaire_prenom'] ?? '')) ?></td>
                                                <td><?= htmlspecialchars($t['titre'] ?? '') ?></td>
                                                <td><?= !empty($t['date_debut']) ? date('d/m/Y', strtotime($t['date_debut'])) : '' ?></td>
                                                <td><?= !empty($t['date_fin']) ? date('d/m/Y', strtotime($t['date_fin'])) : '' ?></td>
                                                <?php $st = $t['statut'] ?? 'a_faire'; ?>
                                                <td><span class="badge badge-status badge-<?= $st ?>"><?= ucfirst(str_replace('_',' ', $st)) ?></span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="voirStagiaire(<?= (int)($t['stagiaire_id'] ?? 0) ?>)"><i class="fas fa-user me-1"></i>Stagiaire</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-muted py-4">Aucune tâche disponible</div>
                        <?php } ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function voirStagiaire(id) {
        if (!id) return;
        window.location.href = '<?php echo BASE_URL; ?>responsable/stagiaires/' + id;
    }
    function creerTache() {
        window.location.href = '<?php echo BASE_URL; ?>responsable/taches/creer';
    }
    </script>
</body>
</html>


