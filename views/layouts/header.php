<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de gestion de stages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>">
                    <img class="h-10 w-auto" src="<?php echo BASE_URL; ?>assets/images/logo_commune.png" alt="Logo Commune Oujda">
                </a>
                <a href="<?php echo BASE_URL; ?>" class="ml-4 text-lg font-semibold text-purple-700 hover:text-purple-900 transition">Accueil</a>
                <a href="#" id="openProcessModal" class="ml-4 text-lg font-semibold text-gray-700 hover:text-purple-700 transition">Processus</a>
                <a href="#" id="openContactModal" class="ml-4 text-lg font-semibold text-gray-700 hover:text-purple-700 transition">Contact</a>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                <?php
                if (!isset($_SESSION['admin_id']) && !isset($_SESSION['stagiaire_id']) && !isset($_SESSION['responsable_id'])) {
                    echo '<a href="' . BASE_URL . 'login" class="text-gray-700 hover:text-purple-700 px-2 text-sm font-medium transition">Connexion Stagiaire</a>';
                    echo '<a href="' . BASE_URL . 'responsable/login" class="text-gray-700 hover:text-purple-700 px-2 text-sm font-medium transition">Connexion Responsable</a>';
                    echo '<a href="' . BASE_URL . 'admin/login" class="text-gray-700 hover:text-purple-700 px-2 text-sm font-medium transition">Connexion Admin</a>';
                    echo '<a href="' . BASE_URL . 'inscription" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105">Inscrivez-vous dès maintenant</a>';
                } elseif (isset($_SESSION['admin_id'])) {
                    // Pour l'administrateur, utiliser les variables globales $admin si disponibles
                    $admin_nom = '';
                    if (isset($admin) && is_array($admin)) {
                        $admin_nom = htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']);
                    } elseif (isset($_SESSION['admin_prenom']) && isset($_SESSION['admin_nom'])) {
                        $admin_nom = htmlspecialchars($_SESSION['admin_prenom'] . ' ' . $_SESSION['admin_nom']);
                    } else {
                        $admin_nom = 'Administrateur';
                    }
                    echo '<span class="text-sm text-gray-700"><i class="fas fa-user-circle mr-2"></i>' . $admin_nom . '</span>';
                    echo '<a href="' . BASE_URL . 'admin/logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
                } elseif (isset($_SESSION['stagiaire_id'])) {
                    $stagiaire_nom = '';
                    if (isset($_SESSION['stagiaire_prenom']) && isset($_SESSION['stagiaire_nom'])) {
                        $stagiaire_nom = htmlspecialchars($_SESSION['stagiaire_prenom'] . ' ' . $_SESSION['stagiaire_nom']);
                    } else {
                        $stagiaire_nom = 'Stagiaire';
                    }
                    echo '<span class="text-sm text-gray-700"><i class="fas fa-user-circle mr-2"></i>' . $stagiaire_nom . '</span>';
                    echo '<a href="' . BASE_URL . 'logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
                } elseif (isset($_SESSION['responsable_id'])) {
                    $responsable_nom = '';
                    if (isset($_SESSION['responsable_prenom']) && isset($_SESSION['responsable_nom'])) {
                        $responsable_nom = htmlspecialchars($_SESSION['responsable_prenom'] . ' ' . $_SESSION['responsable_nom']);
                    } else {
                        $responsable_nom = 'Responsable';
                    }
                    echo '<span class="text-sm text-gray-700"><i class="fas fa-user-circle mr-2"></i>' . $responsable_nom . '</span>';
                    echo '<a href="' . BASE_URL . 'logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 transform hover:scale-105"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
                }
                ?>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="<?php echo BASE_URL; ?>" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Accueil</a>
            <a href="#" id="openProcessModalMobile" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Processus</a>
            <a href="#" id="openContactModalMobile" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Contact</a>
            <?php
            if (!isset($_SESSION['admin_id']) && !isset($_SESSION['stagiaire_id']) && !isset($_SESSION['responsable_id'])) {
                echo '<a href="' . BASE_URL . 'login" class="block pl-3 pr-4 py-2 text-base font-medium">Connexion Stagiaire</a>';
                echo '<a href="' . BASE_URL . 'responsable/login" class="block pl-3 pr-4 py-2 text-base font-medium">Connexion Responsable</a>';
                echo '<a href="' . BASE_URL . 'admin/login" class="block pl-3 pr-4 py-2 text-base font-medium">Connexion Admin</a>';
                echo '<a href="' . BASE_URL . 'inscription" class="w-full text-left bg-blue-600 hover:bg-blue-700 text-white block pl-3 pr-4 py-2 rounded mx-2 text-base font-medium">Inscrivez-vous dès maintenant</a>';
            } elseif (isset($_SESSION['admin_id'])) {
                echo '<a href="' . BASE_URL . 'admin/logout" class="w-full text-left bg-red-600 hover:bg-red-700 text-white block pl-3 pr-4 py-2 rounded mx-2 text-base font-medium"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
            } elseif (isset($_SESSION['stagiaire_id'])) {
                echo '<a href="' . BASE_URL . 'logout" class="w-full text-left bg-red-600 hover:bg-red-700 text-white block pl-3 pr-4 py-2 rounded mx-2 text-base font-medium"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
            } elseif (isset($_SESSION['responsable_id'])) {
                echo '<a href="' . BASE_URL . 'logout" class="w-full text-left bg-red-600 hover:bg-red-700 text-white block pl-3 pr-4 py-2 rounded mx-2 text-base font-medium"><i class="fas fa-sign-out-alt mr-2"></i>Déconnexion</a>';
            }
            ?>
        </div>
    </div>
</nav>
<script>
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});
</script>