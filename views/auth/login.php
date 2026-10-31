<?php
// Déterminer le rôle à partir du paramètre GET, avec 'stagiaire' par défaut
$role = $_GET['role'] ?? 'stagiaire';

// Configuration basée sur le rôle
if ($role === 'responsable') {
    $title = 'Espace Responsable';
    $title_class = 'title-responsable';
    $btn_class = 'btn-responsable';
    $form_action = BASE_URL . 'responsable/login';
    $subtitle = 'Connexion à la plateforme de gestion';
    $other_spaces = [
        ['role' => 'stagiaire', 'name' => 'Stagiaire', 'icon' => 'fa-user-graduate', 'url' => BASE_URL . 'login'],
        ['role' => 'admin', 'name' => 'Administrateur', 'icon' => 'fa-user-shield', 'url' => BASE_URL . 'admin/login']
    ];
    $register_link = false;
} elseif ($role === 'admin') {
    $title = 'Espace Administrateur';
    $title_class = 'title-admin';
    $btn_class = 'btn-admin';
    $form_action = BASE_URL . 'admin/login';
    $subtitle = 'Connexion à la plateforme de gestion';
    $other_spaces = [
        ['role' => 'stagiaire', 'name' => 'Stagiaire', 'icon' => 'fa-user-graduate', 'url' => BASE_URL . 'login?role=stagiaire'],
        ['role' => 'responsable', 'name' => 'Responsable', 'icon' => 'fa-user-tie', 'url' => BASE_URL . 'responsable/login']
    ];
    $register_link = false;
} else { // Stagiaire par défaut
    $title = 'Espace Stagiaire';
    $title_class = 'title-stagiaire';
    $btn_class = 'btn-stagiaire';
    $form_action = BASE_URL . 'login';
    $subtitle = 'Connexion à votre espace personnel';
    $other_spaces = [
        ['role' => 'responsable', 'name' => 'Responsable', 'icon' => 'fa-user-tie', 'url' => BASE_URL . 'responsable/login'],
        ['role' => 'admin', 'name' => 'Administrateur', 'icon' => 'fa-user-shield', 'url' => BASE_URL . 'admin/login']
    ];
    $register_link = true;
}

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/constants.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title <?php echo $title_class; ?>"><?php echo $title; ?></h1>
            <p class="auth-subtitle"><?php echo $subtitle; ?></p>
        </div>

        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form action="<?php echo $form_action; ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Votre email">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
            </div>
            <button type="submit" class="btn-submit <?php echo $btn_class; ?>">
                <span>Se connecter</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <?php if ($register_link) { ?>
            <div class="register-section">
                <p class="register-question">Nouveau stagiaire ?</p>
                <a href="<?php echo BASE_URL; ?>inscription" class="btn-register">
                    <i class="fas fa-plus"></i>
                    <span>Créer un compte</span>
                </a>
            </div>
        <?php } ?>

        <div class="divider">
            <span>Autres espaces</span>
        </div>

        <div class="other-spaces">
            <?php foreach ($other_spaces as $space) { ?>
                <a href="<?php echo $space['url']; ?>" class="space-link">
                    <i class="fas <?php echo $space['icon']; ?>"></i>
                    <span><?php echo $space['name']; ?></span>
                </a>
            <?php } ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>