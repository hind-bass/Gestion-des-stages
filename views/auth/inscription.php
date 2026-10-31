<?php 
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    .auth-card-large {
        max-width: 800px;
    }
    .grid-cols-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .other-links {
        text-align: center;
        margin-top: 1.5rem;
    }
    .other-links a {
        display: inline-block;
        background: #4caf50;
        color: white;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.3s ease;
        margin-top: 0.5rem;
    }
    .other-links a:hover {
        background: #45a049;
    }
</style>

<main class="auth-container">
    <div class="auth-card auth-card-large">
        <h1 class="auth-title title-stagiaire">Inscription Stagiaire</h1>
        <p class="auth-subtitle">Créez votre compte pour accéder à votre espace personnel</p>

        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form action="<?php echo BASE_URL; ?>inscription" method="POST" enctype="multipart/form-data">
            
            <h2 class="section-title">Informations personnelles</h2>
            <div class="grid-cols-2">
                <div class="form-group"><label for="nom_fr">Nom (FR) *</label><input type="text" id="nom_fr" name="nom_fr" required></div>
                <div class="form-group"><label for="prenom_fr">Prénom (FR) *</label><input type="text" id="prenom_fr" name="prenom_fr" required></div>
                <div class="form-group"><label for="nom_ar">Nom (AR) *</label><input type="text" id="nom_ar" name="nom_ar" required></div>
                <div class="form-group"><label for="prenom_ar">Prénom (AR) *</label><input type="text" id="prenom_ar" name="prenom_ar" required></div>
                <div class="form-group"><label for="email">Email *</label><input type="email" id="email" name="email" required></div>
                <div class="form-group"><label for="telephone">Téléphone</label><input type="tel" id="telephone" name="telephone"></div>
                <div class="form-group"><label for="date_debut_souhaitee">Date de début de stage souhaitée *</label><input type="date" id="date_debut_souhaitee" name="date_debut_souhaitee" required></div>
                <div class="form-group"><label for="date_fin_souhaitee">Date de fin de stage souhaitée *</label><input type="date" id="date_fin_souhaitee" name="date_fin_souhaitee" required></div>
                <div class="form-group"><label for="mot_de_passe">Mot de passe *</label><input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8"></div>
                <div class="form-group"><label for="confirm_password">Confirmer le mot de passe *</label><input type="password" id="confirm_password" name="confirm_password" required></div>
            </div>

            <h2 class="section-title">Informations académiques</h2>
            <div class="grid-cols-2">
                <div class="form-group"><label for="etablissement">Établissement *</label><input type="text" id="etablissement" name="etablissement" required></div>
                <div class="form-group"><label for="filiere">Filière *</label><input type="text" id="filiere" name="filiere" required></div>
                <div class="form-group"><label for="niveau">Niveau *</label><select id="niveau" name="niveau" required><option value="">Sélectionnez un niveau</option><option value="Bac+2">Bac+2</option><option value="Bac+3">Bac+3</option><option value="Bac+5">Bac+5</option><option value="Autre">Autre</option></select></div>
            </div>

            <h2 class="section-title">Documents à fournir (obligatoires)</h2>
            <div class="grid-cols-2">
                <div class="form-group"><label for="cv">CV (PDF, JPG, PNG) *</label><input type="file" id="cv" name="cv" accept=".pdf,.jpg,.jpeg,.png" required></div>
                <div class="form-group"><label for="demande_stage">Demande de stage (PDF, JPG, PNG) *</label><input type="file" id="demande_stage" name="demande_stage" accept=".pdf,.jpg,.jpeg,.png" required></div>
                <div class="form-group"><label for="assurance">Attestation d'assurance (PDF, JPG, PNG) *</label><input type="file" id="assurance" name="assurance" accept=".pdf,.jpg,.jpeg,.png" required></div>
                <div class="form-group"><label for="cin_scan">Photocopie de la carte nationale (recto verso) (PDF, JPG, PNG) *</label><input type="file" id="cin_scan" name="cin_scan" accept=".pdf,.jpg,.jpeg,.png" required></div>
            </div>

            <button type="submit" class="btn-submit btn-stagiaire" style="margin-top: 2rem;">Créer mon compte</button>
        </form>

        <div class="divider">Déjà inscrit ?</div>
        <div class="other-links">
            <a href="<?php echo BASE_URL; ?>login">Se connecter</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
