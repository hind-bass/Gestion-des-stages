<?php
/**
 * Vue du Dashboard Administrateur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Gestion des Stages</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../layouts/header_admin.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>admin">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/stagiaires">
                                <i class="fas fa-users"></i> Stagiaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/responsables">
                                <i class="fas fa-user-tie"></i> Responsables
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/statistiques">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/attestations">
                                <i class="fas fa-file-alt"></i> Attestations
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Administrateur</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportData('csv')">Exporter CSV</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportData('json')">Exporter JSON</button>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="row">
                    <!-- Administrateurs -->
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Administrateurs</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['totalAdmins'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsables -->
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Responsables</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['totalResponsables'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stagiaires -->
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Stagiaires</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['totalStagiaires'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- En attente -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Stagiaires en attente</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['stagiairesEnAttente'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validés -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Stagiaires validés</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['stagiairesValides'] ?? 0 ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Derniers administrateurs -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-danger">
                                    <i class="fas fa-user-shield me-2"></i>Derniers Administrateurs
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Date de création</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentData['admins'])): ?>
                                                <?php foreach ($recentData['admins'] as $admin): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) ?></td>
                                                        <td><?= htmlspecialchars($admin['email']) ?></td>
                                                        <td><?= date('d/m/Y', strtotime($admin['date_creation'])) ?></td>
                                                        <td><span class="badge bg-success">Actif</span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Aucun administrateur trouvé</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Derniers stagiaires -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Derniers Stagiaires Inscrits</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Statut</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($derniersStagiaires)): ?>
                                                <?php foreach ($derniersStagiaires as $stagiaire): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($stagiaire['nom_fr'] . ' ' . $stagiaire['prenom_fr']) ?></td>
                                                        <td><?= htmlspecialchars($stagiaire['email']) ?></td>
                                                        <td>
                                                            <span class="badge badge-<?= $stagiaire['statut'] === 'valide' ? 'success' : ($stagiaire['statut'] === 'en_attente' ? 'warning' : 'danger') ?>">
                                                                <?= ucfirst($stagiaire['statut']) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d/m/Y', strtotime($stagiaire['date_inscription'])) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="voirStagiaire(<?= $stagiaire['id'] ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <?php if ($stagiaire['statut'] === 'en_attente'): ?>
                                                                <button class="btn btn-sm btn-success" onclick="validerStagiaire(<?= $stagiaire['id'] ?>)">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" onclick="refuserStagiaire(<?= $stagiaire['id'] ?>)">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Aucun stagiaire trouvé</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stagiaires en attente -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">Stagiaires en Attente</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stagiairesEnAttente)): ?>
                                    <?php foreach ($stagiairesEnAttente as $stagiaire): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0"><?= htmlspecialchars($stagiaire['nom_fr'] . ' ' . $stagiaire['prenom_fr']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($stagiaire['email']) ?></small>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-sm btn-success" onclick="validerStagiaire(<?= $stagiaire['id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucun stagiaire en attente</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques par service -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Répartition par Service</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php if (!empty($statsParService)): ?>
                                        <?php foreach ($statsParService as $service): ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-left-info">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?= htmlspecialchars($service['service_demande']) ?></h6>
                                                        <p class="card-text">
                                                            <strong><?= $service['total'] ?></strong> stagiaires
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p class="text-muted">Aucune donnée disponible</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
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
    <?php include __DIR__ . '/../components/admin_modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/dashboard.js"></script>
    <script src="/assets/js/admin-stagiaires.js"></script>
    
    <script>
        // Fonctions pour les actions du dashboard
        function voirStagiaire(id) {
            window.location.href = `/admin/stagiaires/${id}`;
        }

        function validerStagiaire(id) {
            if (confirm('Êtes-vous sûr de vouloir valider ce stagiaire ?')) {
                fetch('/admin/valider-stagiaire', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ stagiaire_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Stagiaire validé avec succès');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
            }
        }

        function refuserStagiaire(id) {
            const motif = prompt('Motif du refus:');
            if (motif) {
                fetch('/admin/refuser-stagiaire', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        stagiaire_id: id,
                        motif: motif 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Stagiaire refusé');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
            }
        }

        function exportData(format) {
            window.location.href = `/admin/export?format=${format}`;
        }
    </script>
</body>
</html>
