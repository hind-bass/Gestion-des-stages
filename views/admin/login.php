<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title title-admin">Espace Administrateur</h1>
        <p class="auth-subtitle">Connexion à la plateforme de gestion</p>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="/admin/login" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit btn-admin">Se connecter</button>
        </form>

        <div class="divider">Autres espaces</div>

        <div class="other-spaces">
            <a href="<?php echo BASE_URL; ?>login" class="space-link">
                <i class="fas fa-user-graduate"></i> Stagiaire
            </a>
            <a href="<?php echo BASE_URL; ?>responsable/login" class="space-link">
                <i class="fas fa-user-tie"></i> Responsable
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
