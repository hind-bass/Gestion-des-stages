<?php
/**
 * Vue : Afficher les détails d'un stagiaire pour le responsable
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Stagiaire - Gestion des Stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../../layouts/header_responsable.php'; ?>
    <style>
    .sidebar { background: #1e40af; min-height: 100vh; padding-top: 20px; }
    .sidebar .nav-link { color: #e0e7ff; font-weight: 600; padding: 12px 16px; border-radius: 8px; margin: 6px 10px; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #1d4ed8; color: #fff; }
    .card { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
    .h2 { color: #1e40af; font-weight: 700; }
    </style>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_URL; ?>responsable/stagiaires">
                                <i class="fas fa-users"></i> Stagiaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/taches">
                                <i class="fas fa-tasks"></i> Tâches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>responsable/evaluations">
                                <i class="fas fa-clipboard-check"></i> Évaluations
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Détails du Stagiaire</h1>
                    <a href="<?php echo BASE_URL; ?>responsable/stagiaires" class="btn btn-secondary">Retour à la liste</a>
                </div>

                <?php if (isset($stagiaire) && $stagiaire): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5><?= htmlspecialchars(($stagiaire['prenom_fr'] ?? '') . ' ' . ($stagiaire['nom_fr'] ?? '')) ?></h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Email :</strong> <?= htmlspecialchars($stagiaire['email']) ?></p>
                            <p><strong>Téléphone :</strong> <?= htmlspecialchars($stagiaire['telephone']) ?></p>
                            <p><strong>Niveau d'étude :</strong> <?= htmlspecialchars($stagiaire['niveau'] ?? '') ?></p>
                            <p><strong>Établissement :</strong> <?= htmlspecialchars($stagiaire['etablissement']) ?></p>
                            <p><strong>Date de début :</strong> <?= isset($stagiaire['date_debut_souhaitee']) && $stagiaire['date_debut_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])) : '' ?></p>
                            <p><strong>Date de fin :</strong> <?= isset($stagiaire['date_fin_souhaitee']) && $stagiaire['date_fin_souhaitee'] ? date('d/m/Y', strtotime($stagiaire['date_fin_souhaitee'])) : '' ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Le stagiaire demandé n'a pas été trouvé.</div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>
