<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/constants.php';
}
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <div class="bg-purple-500 text-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-tie text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Connexion Responsable</h2>
            <p class="text-gray-600">Accédez à votre espace de gestion des stagiaires</p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if (isset($error) && $error): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Adresse email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se connecter
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Retour à l'accueil</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="/" class="text-purple-600 hover:text-purple-500 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus sur le premier champ
    document.getElementById('email').focus();
    
    // Validation en temps réel
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    function validateForm() {
        let isValid = true;
        
        // Validation email
        if (!emailInput.value || !emailInput.value.includes('@')) {
            emailInput.classList.add('border-red-500');
            isValid = false;
        } else {
            emailInput.classList.remove('border-red-500');
        }
        
        // Validation mot de passe
        if (!passwordInput.value || passwordInput.value.length < 6) {
            passwordInput.classList.add('border-red-500');
            isValid = false;
        } else {
            passwordInput.classList.remove('border-red-500');
        }
        
        return isValid;
    }
    
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs dans le formulaire.');
        }
    });
    
    // Validation en temps réel
    emailInput.addEventListener('blur', validateForm);
    passwordInput.addEventListener('blur', validateForm);
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
