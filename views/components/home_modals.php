<!-- Login Selection Modal -->
<div id="loginModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden" style="display: none;">
    <div class="bg-white rounded-lg shadow-2xl p-8 m-4 max-w-2xl w-full relative transform transition-all duration-300 ease-in-out scale-95">
        <button id="closeLoginModal" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Connexion</h2>
            <p class="text-gray-600">Choisissez votre type de compte pour vous connecter</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Connexion Stagiaire -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow">
                <div class="bg-green-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-green-800 mb-2">Stagiaire</h3>
                <p class="text-green-600 mb-4 text-sm">Accédez à votre espace stagiaire</p>
                <a href="<?php echo BASE_URL; ?>login?role=stagiaire" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg w-full block transition">
                    Se connecter
                </a>
            </div>

            <!-- Connexion Responsable -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow">
                <div class="bg-purple-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-purple-800 mb-2">Responsable</h3>
                <p class="text-purple-600 mb-4 text-sm">Gérez vos stagiaires et tâches</p>
                <a href="<?php echo BASE_URL; ?>responsable/login" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg w-full block transition">
                    Se connecter
                </a>
            </div>

            <!-- Connexion Administrateur -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center hover:shadow-lg transition-shadow">
                <div class="bg-red-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-red-800 mb-2">Administrateur</h3>
                <p class="text-red-600 mb-4 text-sm">Administration de la plateforme</p>
                <a href="<?php echo BASE_URL; ?>admin/login" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg w-full block transition">
                    Se connecter
                </a>
            </div>
        </div>

        <div class="text-center mt-8 pt-6 border-t border-gray-200">
            <p class="text-gray-600 mb-4">Vous n'avez pas encore de compte ?</p>
            <a href="<?php echo BASE_URL; ?>inscription" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Créer un compte stagiaire
            </a>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden" style="display: none;">
    <div class="contact-modal-container">
        <div class="contact-modal-header">
            <div class="contact-modal-logo">
                <img src="<?php echo BASE_URL; ?>assets/images/logo_commune.png" alt="Logo Commune" class="contact-logo-img">
            </div>
            <div class="contact-modal-title-section">
                <h2 class="contact-modal-title">Contactez la Commune</h2>
                <p class="contact-modal-subtitle">Votre message sera envoyé à : <span class="contact-email">contact@commune-oujda.ma</span></p>
            </div>
            <button id="closeContactModal" class="contact-modal-close">
                <i class="fas fa-times"></i>
        </button>
        </div>
        
        <form id="contactForm" class="contact-form">
            <div class="form-group">
                <label for="nom_complet" class="form-label">Nom complet *</label>
                <input type="text" id="nom_complet" name="nom_complet" class="form-input" placeholder="Votre nom complet" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email *</label>
                <div class="form-input-container">
                    <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
                    <i class="fas fa-envelope form-input-icon"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sujet" class="form-label">Sujet *</label>
                <div class="form-select-container">
                    <select id="sujet" name="sujet" class="form-select" required>
                    <option value="">Choisir un sujet</option>
                    <option value="Demande d'information">Demande d'information</option>
                    <option value="Support technique">Support technique</option>
                        <option value="Problème de connexion">Problème de connexion</option>
                        <option value="Demande de stage">Demande de stage</option>
                    <option value="Autre">Autre</option>
                </select>
                    <i class="fas fa-chevron-down form-select-icon"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="message" class="form-label">Message *</label>
                <textarea id="message" name="message" rows="4" class="form-textarea" placeholder="Votre message..." required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" id="cancelContact" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-send">Envoyer le message</button>
            </div>
        </form>
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden" style="display: none;">
    <div class="about-modal-container">
        <button id="closeAboutModal" class="about-modal-close">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="about-modal-title">À propos de notre application</h2>
        
        <div class="about-content">
            <div class="about-section">
                <div class="about-section-header">
                    <div class="about-icon objective-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="about-section-title">Objectif de l'application</h3>
                </div>
                <p class="about-section-text">
                    Notre plateforme de gestion des stages vise à digitaliser et simplifier le processus de gestion des stages au sein de la Commune Urbaine d'Oujda. Elle permet une meilleure traçabilité, une réduction des délais de traitement et une amélioration de l'expérience utilisateur pour tous les acteurs.
                </p>
            </div>
            
            <div class="about-section">
                <div class="about-section-header">
                    <div class="about-icon roles-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="about-section-title">Rôles et espaces</h3>
                </div>
                <div class="roles-list">
                    <div class="role-item">
                        <div class="role-indicator stagiaire-indicator"></div>
                        <div class="role-content">
                            <h4 class="role-title">Espace Stagiaire</h4>
                            <p class="role-description">Inscription en ligne, suivi des tâches, consultation des remarques, génération d'attestations</p>
                        </div>
                    </div>
                    <div class="role-item">
                        <div class="role-indicator responsable-indicator"></div>
                        <div class="role-content">
                            <h4 class="role-title">Espace Responsable</h4>
                            <p class="role-description">Gestion des stagiaires, attribution de tâches, ajout de remarques, génération d'attestations</p>
                        </div>
                    </div>
                    <div class="role-item">
                        <div class="role-indicator admin-indicator"></div>
                        <div class="role-content">
                            <h4 class="role-title">Espace Administrateur</h4>
                            <p class="role-description">Configuration du système, gestion des utilisateurs, validation des dossiers, statistiques</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div id="processModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden" style="display: none;">
    <div class="process-modal-container">
        <button id="closeProcessModal" class="process-modal-close">
            <i class="fas fa-times"></i>
        </button>
        <div class="process-modal-header">
            <div class="process-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
            <h2 class="process-modal-title">Processus de stage</h2>
        </div>
        
        <ol class="process-steps">
            <li class="process-step">Inscription en ligne du stagiaire</li>
            <li class="process-step">Validation administrative du dossier</li>
            <li class="process-step">Affectation au service et désignation du tuteur</li>
            <li class="process-step">Suivi et évaluation du stage</li>
            <li class="process-step">Génération de l'attestation finale</li>
        </ol>
        
        <div class="process-actions">
            <button id="okProcess" class="btn-process-close">Fermer</button>
        </div>
    </div>
</div>