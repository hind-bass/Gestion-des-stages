<?php
/**
 * Vue du Dashboard Responsable
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Responsable - Gestion des Stages</title>
    <!-- Bootstrap CSS for layout/styles used in this page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- App global styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../layouts/header_responsable.php'; ?>
    <style>
    .sidebar {
        background: #1e40af;
        min-height: 100vh;
        padding-top: 20px;
    }
    .sidebar .nav-link {
        color: #e0e7ff;
        font-weight: 600;
        padding: 12px 16px;
        border-radius: 8px;
        margin: 6px 10px;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
        background: #1d4ed8;
        color: #fff;
    }
    .card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .card-title { color: #1f2937; }
    .h2 { color: #1e40af; font-weight: 700; }
    </style>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>responsable">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/stagiaires">
                                <i class="fas fa-users"></i> Mes Stagiaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/taches">
                                <i class="fas fa-tasks"></i> Tâches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/evaluations">
                                <i class="fas fa-star"></i> Évaluations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/rapports">
                                <i class="fas fa-file-alt"></i> Rapports
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Responsable</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="creerTache()">
                                <i class="fas fa-plus"></i> Nouvelle Tâche
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Informations du responsable -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user-tie"></i> 
                                    <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                                </h5>
                                <p class="card-text">
                                    <strong>Service :</strong> <?= htmlspecialchars($user['service']) ?><br>
                                    <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Stagiaires Assignés</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($stagiaires) ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Tâches Terminées</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['taches_terminees'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Tâches en Cours</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['taches_en_cours'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Tâches</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_taches'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mes stagiaires -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Mes Stagiaires</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stagiaires)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Établissement</th>
                                                    <th>Date Début</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($stagiaires as $stagiaire): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($stagiaire['nom_fr'] . ' ' . $stagiaire['prenom_fr']) ?></td>
                                                        <td><?= htmlspecialchars($stagiaire['email']) ?></td>
                                                        <td><?= htmlspecialchars($stagiaire['etablissement']) ?></td>
                                                        <td><?= date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="voirStagiaire(<?= $stagiaire['id'] ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-success" onclick="assignerTache(<?= $stagiaire['id'] ?>)">
                                                                <i class="fas fa-tasks"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-info" onclick="evaluations(<?= $stagiaire['id'] ?>)">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucun stagiaire assigné</h5>
                                        <p class="text-muted">Vous n'avez pas encore de stagiaires assignés à votre service.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tâches récentes -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">Tâches Récentes</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($tachesRecentes)): ?>
                                    <?php foreach ($tachesRecentes as $tache): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0"><?= htmlspecialchars($tache['titre']) ?></h6>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($tache['stagiaire_nom'] . ' ' . $tache['stagiaire_prenom']) ?>
                                                </small>
                                                <br>
                                                <span class="badge badge-<?= $tache['statut'] === 'terminee' ? 'success' : ($tache['statut'] === 'en_cours' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst($tache['statut']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucune tâche récente</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progression des stagiaires -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Progression des Stagiaires</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stagiaires)): ?>
                                    <?php foreach ($stagiaires as $stagiaire): ?>
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0"><?= htmlspecialchars($stagiaire['nom_fr'] . ' ' . $stagiaire['prenom_fr']) ?></h6>
                                                <span class="text-muted"><?= $stagiaire['progression'] ?? 0 ?>%</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?= $stagiaire['progression'] ?? 0 ?>%" 
                                                     aria-valuenow="<?= $stagiaire['progression'] ?? 0 ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucune donnée de progression disponible</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <!-- Modales -->
    <?php include __DIR__ . '/../components/responsable_modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>
    
    <script>
        // Fonctions pour les actions du dashboard responsable
        function voirStagiaire(id) {
            window.location.href = `<?php echo BASE_URL; ?>responsable/stagiaires/${id}`;
        }

        function assignerTache(stagiaireId) {
            // Ouvrir une modale pour créer une tâche
            const titre = prompt('Titre de la tâche:');
            const description = prompt('Description de la tâche:');
            const dateFin = prompt('Date de fin (YYYY-MM-DD):');
            
            if (titre && description && dateFin) {
                fetch('<?php echo BASE_URL; ?>responsable/creer-tache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        stagiaire_id: stagiaireId,
                        titre: titre,
                        description: description,
                        date_fin: dateFin
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tâche créée avec succès');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
            }
        }

        function evaluations(stagiaireId) {
            window.location.href = `<?php echo BASE_URL; ?>responsable/evaluations/${stagiaireId}`;
        }

        function creerTache() {
            // Rediriger vers la page de création de tâche
            window.location.href = '<?php echo BASE_URL; ?>responsable/taches/creer';
        }
    </script>
</body>
</html>
