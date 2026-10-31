// Gestion des modales et interactions de la page d'accueil

document.addEventListener('DOMContentLoaded', function() {
    // S'assurer que toutes les modales sont cachées au chargement
    const modals = ['loginModal', 'aboutModal', 'contactModal', 'processModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            modal.classList.remove('show');
        }
    });

    // Gestion du menu mobile
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Gestion des modales de connexion
    const openLoginModal = document.getElementById('openLoginModal');
    const openAboutModal = document.getElementById('openAboutModal');
    const openContactModal = document.getElementById('openContactModal');
    const openContactModalMobile = document.getElementById('openContactModalMobile');
    const openProcessModal = document.getElementById('openProcessModal');
    const openProcessModalMobile = document.getElementById('openProcessModalMobile');
    const openMapModal = document.getElementById('openMapModal');

    if (openLoginModal) {
        openLoginModal.addEventListener('click', function(e) {
            e.preventDefault();
            showLoginModal();
        });
    }

    if (openAboutModal) {
        openAboutModal.addEventListener('click', function(e) {
            e.preventDefault();
            showAboutModal();
        });
    }

    if (openContactModal) {
        openContactModal.addEventListener('click', function(e) {
            e.preventDefault();
            showContactModal();
        });
    }

    if (openContactModalMobile) {
        openContactModalMobile.addEventListener('click', function(e) {
            e.preventDefault();
            showContactModal();
            // Fermer le menu mobile
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    if (openProcessModal) {
        openProcessModal.addEventListener('click', function(e) {
            e.preventDefault();
            showProcessModal();
        });
    }

    if (openProcessModalMobile) {
        openProcessModalMobile.addEventListener('click', function(e) {
            e.preventDefault();
            showProcessModal();
            // Fermer le menu mobile
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    if (openMapModal) {
        openMapModal.addEventListener('click', function(e) {
            e.preventDefault();
            openGoogleMaps();
        });
    }

    // Gestion des fermetures de modales
    setupModalCloseHandlers();

    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer les cartes d'espaces
    const espaceCards = document.querySelectorAll('.espace-card');
    espaceCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Animation des statistiques
    const statNumbers = document.querySelectorAll('.stat-number');
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumber(entry.target);
            }
        });
    }, observerOptions);

    statNumbers.forEach(stat => {
        statsObserver.observe(stat);
    });
});

// Fonction pour ouvrir la modale de connexion
function showLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Fonction pour ouvrir la modale "En savoir plus"
function showAboutModal() {
    const modal = document.getElementById('aboutModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Fonction pour ouvrir la modale de contact
function showContactModal() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Fonction pour ouvrir la modale du processus
function showProcessModal() {
    const modal = document.getElementById('processModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Fonction pour fermer toutes les modales
function closeAllModals() {
    const modals = ['loginModal', 'aboutModal', 'contactModal', 'processModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            modal.classList.add('hidden');
        }
    });
    document.body.style.overflow = 'auto';
}

// Configuration des gestionnaires de fermeture des modales
function setupModalCloseHandlers() {
    // Fermeture par bouton X
    const closeButtons = [
        'closeLoginModal', 'closeAboutModal', 'closeContactModal', 'closeProcessModal'
    ];
    
    closeButtons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', closeAllModals);
        }
    });

    // Fermeture par clic sur l'overlay
    const modals = ['loginModal', 'aboutModal', 'contactModal', 'processModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAllModals();
                }
            });
        }
    });

    // Fermeture par touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
    
    // Gestion du formulaire de contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Ici vous pouvez ajouter la logique d'envoi du formulaire
            alert('Message envoyé avec succès !');
            closeAllModals();
        });
    }

    // Bouton Annuler du formulaire de contact
    const cancelContact = document.getElementById('cancelContact');
    if (cancelContact) {
        cancelContact.addEventListener('click', closeAllModals);
    }

    // Bouton OK du processus
    const okProcess = document.getElementById('okProcess');
    if (okProcess) {
        okProcess.addEventListener('click', closeAllModals);
    }
}

// Fonction pour ouvrir la modale de connexion spécifique (stagiaire/responsable)
function openLoginModal(type) {
    console.log('Ouverture de la modale de connexion pour:', type);
    showLoginModal();
}

// Animation des nombres dans les statistiques
function animateNumber(element) {
    const target = parseInt(element.textContent.replace(/\D/g, ''));
    const suffix = element.textContent.replace(/\d/g, '');
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + suffix;
    }, 30);
}

// Gestion du scroll fluide pour les ancres
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Effet parallaxe pour la section hero
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        const rate = scrolled * -0.5;
        heroSection.style.transform = `translateY(${rate}px)`;
    }
});

// Gestion des erreurs d'images
document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
        this.style.display = 'none';
        console.warn('Image non trouvée:', this.src);
    });
});

// Fonction pour ouvrir Google Maps
function openGoogleMaps() {
    // Coordonnées de la Commune d'Oujda (Place du 16 Août)
    const latitude = 34.6814;
    const longitude = -1.9086;
    const address = "Place du 16 Août, Oujda, Maroc";
    
    // URL pour Google Maps
    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
    
    // Ouvrir dans un nouvel onglet
    window.open(googleMapsUrl, '_blank');
    
    // Optionnel: Afficher une notification
    showNotification('Ouverture de Google Maps...', 'success');
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Styles inline pour la notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    `;
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Gestion de la fermeture
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    });
    
    // Fermeture automatique après 3 secondes
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, 3000);
}