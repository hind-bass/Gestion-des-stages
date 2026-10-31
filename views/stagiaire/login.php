<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="/assets/images/logo_commune.png" alt="Logo Commune d'Oujda" class="img-fluid mb-3" style="max-height: 80px;">
                        <h3 class="h4 mb-3">Espace Stagiaire</h3>
                        <p class="text-muted">Veuillez vous connecter pour accéder à votre espace</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/login" method="post" id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label">Mot de passe</label>
                                <a href="/mot-de-passe-oublie" class="small">Mot de passe oublié ?</a>
                            </div>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span class="btn-text">Se connecter</span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Vous n'avez pas de compte ? <a href="/inscription">S'inscrire</a></p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="/" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');
            
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Gestion de la soumission du formulaire
    const form = document.getElementById('loginForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            const btnText = submitBtn.querySelector('.btn-text');
            
            // Désactiver le bouton et afficher le spinner
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Connexion...';
            
            // Réactiver le bouton après 5 secondes au cas où la requête échouerait
            setTimeout(() => {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.textContent = 'Se connecter';
            }, 5000);
        });
    }
});
</script>

<style>
body {
    background-color: #f8f9fa;
}
.card {
    border: none;
    border-radius: 10px;
}
.toggle-password {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
