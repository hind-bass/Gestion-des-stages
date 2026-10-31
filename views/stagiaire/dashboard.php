<?php
/**
 * Vue du Dashboard Stagiaire
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Stagiaire - Gestion des Stages</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="/stagiaire">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stagiaire/profil">
                                <i class="fas fa-user"></i> Mon Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stagiaire/taches">
                                <i class="fas fa-tasks"></i> Mes Tâches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stagiaire/documents">
                                <i class="fas fa-file-alt"></i> Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stagiaire/attestations">
                                <i class="fas fa-certificate"></i> Attestations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stagiaire/evaluations">
                                <i class="fas fa-star"></i> Évaluations
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Espace Stagiaire</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="telechargerAttestation()">
                                <i class="fas fa-download"></i> Télécharger Attestation
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Informations du stagiaire -->
                <?php if (!empty($stagiaire)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="card-title">
                                            <i class="fas fa-user-graduate"></i> 
                                            <?= htmlspecialchars(($stagiaire['nom_fr'] ?? '') . ' ' . ($stagiaire['prenom_fr'] ?? '')) ?>
                                        </h5>
                                        <p class="card-text">
                                            <strong>Numéro de stagiaire :</strong> <?= htmlspecialchars($stagiaire['numero_stagiaire'] ?? 'N/A') ?><br>
                                            <strong>Établissement :</strong> <?= htmlspecialchars($stagiaire['etablissement'] ?? 'N/A') ?><br>
                                            <strong>Service :</strong> <?= htmlspecialchars($stagiaire['service_demande'] ?? 'N/A') ?><br>
                                            <strong>Email :</strong> <?= htmlspecialchars($stagiaire['email'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge badge-<?= ($stagiaire['statut'] ?? 'inconnu') === 'valide' ? 'success' : (($stagiaire['statut'] ?? 'inconnu') === 'en_attente' ? 'warning' : 'danger') ?> fs-6">
                                            <?= ucfirst($stagiaire['statut'] ?? 'Inconnu') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">Impossible de charger les informations du stagiaire.</div>
                <?php endif; ?>

                <!-- Statistiques -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Tâches Assignées</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($taches) ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
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
                                            Progression</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['progression'] ?? 0 ?>%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mes tâches -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Mes Tâches</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($taches)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Titre</th>
                                                    <th>Description</th>
                                                    <th>Statut</th>
                                                    <th>Date Fin</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($taches as $tache): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($tache['titre']) ?></td>
                                                        <td><?= htmlspecialchars(substr($tache['description'], 0, 50)) ?>...</td>
                                                        <td>
                                                            <span class="badge badge-<?= $tache['statut'] === 'terminee' ? 'success' : ($tache['statut'] === 'en_cours' ? 'warning' : 'secondary') ?>">
                                                                <?= ucfirst($tache['statut']) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d/m/Y', strtotime($tache['date_fin'])) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="voirTache(<?= $tache['id'] ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <?php if ($tache['statut'] !== 'terminee'): ?>
                                                                <button class="btn btn-sm btn-success" onclick="changerStatut(<?= $tache['id'] ?>, 'en_cours')">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-info" onclick="changerStatut(<?= $tache['id'] ?>, 'terminee')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Aucune tâche assignée</h5>
                                        <p class="text-muted">Votre responsable ne vous a pas encore assigné de tâches.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Documents et attestations -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">Documents & Attestations</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="/stagiaire/documents" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt text-primary"></i> Mes Documents
                                    </a>
                                    <a href="/stagiaire/attestations" class="list-group-item list-group-item-action">
                                        <i class="fas fa-certificate text-success"></i> Attestation de Début
                                    </a>
                                    <a href="/stagiaire/attestations/fin" class="list-group-item list-group-item-action">
                                        <i class="fas fa-certificate text-info"></i> Attestation de Fin
                                    </a>
                                    <a href="/stagiaire/evaluations" class="list-group-item list-group-item-action">
                                        <i class="fas fa-star text-warning"></i> Mes Évaluations
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Progression générale -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Progression Générale</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Progression du stage</span>
                                        <span class="text-muted"><?= $stats['progression'] ?? 0 ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= $stats['progression'] ?? 0 ?>%" 
                                             aria-valuenow="<?= $stats['progression'] ?? 0 ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Tâches terminées</span>
                                        <span class="text-muted"><?= $stats['taches_terminees'] ?? 0 ?>/<?= count($taches) ?></span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= count($taches) > 0 ? ($stats['taches_terminees'] ?? 0) / count($taches) * 100 : 0 ?>%" 
                                             aria-valuenow="<?= $stats['taches_terminees'] ?? 0 ?>" 
                                             aria-valuemin="0" aria-valuemax="<?= count($taches) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dernières remarques -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Dernières Remarques du Responsable</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($remarques)): ?>
                                    <?php foreach ($remarques as $remarque): ?>
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?= htmlspecialchars($remarque['titre']) ?></h6>
                                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($remarque['date_creation'])) ?></small>
                                            </div>
                                            <p class="mb-1"><?= htmlspecialchars($remarque['contenu']) ?></p>
                                            <small class="text-muted">
                                                Responsable: <?= htmlspecialchars($remarque['responsable_nom'] . ' ' . $remarque['responsable_prenom']) ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Aucune remarque pour le moment</p>
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
    <?php include __DIR__ . '/../components/stagiaire_modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/dashboard.js"></script>
    
    <script>
        // Fonctions pour les actions du dashboard stagiaire
        function voirTache(id) {
            window.location.href = `/stagiaire/taches/${id}`;
        }

        function changerStatut(tacheId, statut) {
            const statutText = statut === 'en_cours' ? 'en cours' : 'terminée';
            
            if (confirm(`Êtes-vous sûr de vouloir marquer cette tâche comme ${statutText} ?`)) {
                fetch('/stagiaire/changer-statut-tache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        tache_id: tacheId,
                        statut: statut
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Tâche marquée comme ${statutText}`);
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                });
            }
        }

        function telechargerAttestation() {
            const type = prompt('Type d\'attestation (debut/fin):');
            if (type) {
                window.location.href = `/stagiaire/telecharger-attestation?type=${type}`;
            }
        }
    </script>
</body>
</html>
