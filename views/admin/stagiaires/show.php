<?php
/**
 * Vue pour afficher les détails d'un stagiaire.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Détails du Stagiaire') ?></title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../../layouts/header_admin.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Détails du Stagiaire</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($stagiaire) && !empty($stagiaire)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nom Complet :</strong> <?= htmlspecialchars($stagiaire['nom_fr'] . ' ' . $stagiaire['prenom_fr']) ?></p>
                                    <p><strong>Email :</strong> <?= htmlspecialchars($stagiaire['email']) ?></p>
                                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($stagiaire['tel']) ?></p>
                                    <p><strong>CIN :</strong> <?= htmlspecialchars($stagiaire['cin']) ?></p>
                                    <p><strong>Date de Naissance :</strong> <?= date('d/m/Y', strtotime($stagiaire['date_naissance'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Niveau d'Étude :</strong> <?= htmlspecialchars($stagiaire['niveau_etude']) ?></p>
                                    <p><strong>Filière :</strong> <?= htmlspecialchars($stagiaire['filiere']) ?></p>
                                    <p><strong>Service Demandé :</strong> <?= htmlspecialchars($stagiaire['service_demande']) ?></p>
                                    <p><strong>Date d'Inscription :</strong> <?= date('d/m/Y', strtotime($stagiaire['date_inscription'])) ?></p>
                                    <p><strong>Statut :</strong> <span class="badge bg-<?= $stagiaire['statut'] === 'valide' ? 'success' : 'warning' ?>"><?= ucfirst($stagiaire['statut']) ?></span></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-center">Aucun détail de stagiaire à afficher.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-end">
                        <a href="/admin/stagiaires" class="btn btn-secondary">Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>
