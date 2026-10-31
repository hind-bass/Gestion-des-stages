<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/constants.php';
}
require_once __DIR__ . '/layouts/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <img src="<?php echo BASE_URL; ?>assets/images/la commune d'oujda.jpg" alt="Commune d'Oujda" class="hero-bg-image">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content">
            
            <h1 class="hero-title">Gestion des Stages<br>Commune Urbaine d'Oujda</h1>
            <p class="hero-subtitle">Une plateforme innovante pour simplifier et digitaliser la gestion des stages au sein de notre commune.</p>
            <div class="hero-buttons">
                <a href="<?php echo BASE_URL; ?>login" class="btn-primary">Connexion Stagiaire</a>
                <a href="<?php echo BASE_URL; ?>responsable/login" class="btn-secondary">Connexion Responsable</a>
                <a href="<?php echo BASE_URL; ?>admin/login" class="btn-secondary">Connexion Admin</a>
                <a href="<?php echo BASE_URL; ?>inscription" class="btn-secondary">Créer un compte</a>
                <button id="openAboutModal" class="btn-secondary">En savoir plus</button>
            </div>
        </div>
    </section>

    <!-- Espaces Section -->
    <section id="espaces" class="espaces-section">
        <div class="container">
            <div class="section-header">
                <div class="section-logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo_commune.png" alt="Logo Commune Oujda" class="section-logo-img">
                </div>
                <h2>Choisissez votre espace</h2>
                <p>Plateforme de gestion des stages - Commune d'Oujda</p>
            </div>
            <div class="espaces-grid">
                <!-- Espace Stagiaire -->
                <div class="espace-card">
                    <div class="card-image">
                        <img src="<?php echo BASE_URL; ?>assets/images/espace_stagiaire.png" alt="Espace Stagiaire">
                    </div>
                    <div class="card-content">
                        <h3>Espace Stagiaire</h3>
                        <p>Accédez à votre espace personnel pour suivre votre stage</p>
                        <div class="card-buttons">
                            <a href="<?php echo BASE_URL; ?>login" class="btn-stagiaire">J'ai déjà un compte</a>
                            <a href="<?php echo BASE_URL; ?>inscription" class="btn-secondary-card">Créer un nouveau compte</a>
                        </div>
                    </div>
                </div>

                <!-- Espace Responsable -->
                <div class="espace-card">
                    <div class="card-image">
                        <img src="<?php echo BASE_URL; ?>assets/images/espace_responsable.webp" alt="Espace Responsable">
                    </div>
                    <div class="card-content">
                        <h3>Espace Responsable</h3>
                        <p>Gérez vos stagiaires, tâches et remarques</p>
                        <div class="card-buttons">
                            <a href="<?php echo BASE_URL; ?>responsable/login" class="btn-responsable">Se connecter</a>
                        </div>
                    </div>
                </div>

                <!-- Espace Administrateur -->
                <div class="espace-card">
                    <div class="card-image">
                        <img src="<?php echo BASE_URL; ?>assets/images/espace_admin.jpg" alt="Espace Administrateur">
                    </div>
                    <div class="card-content">
                        <h3>Espace Administrateur</h3>
                        <p>Administration complète de la plateforme</p>
                        <div class="card-buttons">
                            <a href="<?php echo BASE_URL; ?>admin/login" class="btn-admin">Se connecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Localisation Section -->
    <section id="localisation" class="localisation-section">
        <div class="container">
            <div class="section-header">
                <h2>Notre Localisation</h2>
                <p>Commune Urbaine d'Oujda - Maroc</p>
            </div>
            <div class="localisation-content">
                <div class="localisation-info">
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Adresse</h3>
                            <p>Place du 16 Août, Oujda<br>Maroc</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Téléphone</h3>
                            <p>+212 536 68 20 00</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>contact@commune-oujda.ma</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h3>Horaires</h3>
                            <p>Lun - Ven: 8h30 - 16h30</p>
                        </div>
                    </div>
                </div>
                <div class="map-container">
                    <div id="map" class="map"></div>
                    <div class="map-overlay">
                        <button id="openMapModal" class="btn-map">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="currentColor"/>
                            </svg>
                            Voir sur Google Maps
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact-section">
        <div class="container">
            <div class="impact-header">
                <h2 class="impact-subtitle">CHIFFRES CLÉS</h2>
                <h3 class="impact-title">Notre impact en chiffres</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">250+</div>
                    <div class="stat-label">Stagiaires accueillis</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15</div>
                    <div class="stat-label">Services impliqués</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction stagiaires</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24h</div>
                    <div class="stat-label">Temps moyen de traitement</div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/components/home_modals.php'; ?>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>