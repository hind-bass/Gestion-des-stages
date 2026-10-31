document.addEventListener('DOMContentLoaded', function() {
    // Contact Modal
    const contactModal = document.getElementById('contactModal');
    const closeContactModal = document.getElementById('closeContactModal');
    const cancelContact = document.getElementById('cancelContact');
    const contactForm = document.getElementById('contactForm');

    document.querySelectorAll('a[href="#contact"]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            contactModal.classList.remove('hidden');
            contactModal.querySelector('.transform').classList.remove('scale-95');
        });
    });

    if (closeContactModal) {
        closeContactModal.addEventListener('click', () => contactModal.classList.add('hidden'));
    }
    if (cancelContact) {
        cancelContact.addEventListener('click', () => contactModal.classList.add('hidden'));
    }

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(contactForm);
            const data = Object.fromEntries(formData.entries());

            fetch('/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    contactModal.classList.add('hidden');
                    alert(result.message);
                    contactForm.reset();
                } else {
                    alert('Erreur: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            });
        });
    }

    // About Modal
    const aboutModal = document.getElementById('aboutModal');
    const openAboutModal = document.getElementById('openAboutModal');
    const closeAboutModal = document.getElementById('closeAboutModal');

    if (openAboutModal) {
        openAboutModal.addEventListener('click', () => {
            aboutModal.classList.remove('hidden');
            aboutModal.querySelector('.transform').classList.remove('scale-95');
        });
    }
    if (closeAboutModal) {
        closeAboutModal.addEventListener('click', () => aboutModal.classList.add('hidden'));
    }

    // Process Modal
    const processModal = document.getElementById('processModal');
    const closeProcessModal = document.getElementById('closeProcessModal');
    const okProcess = document.getElementById('okProcess');

    document.querySelectorAll('a[href="#processus"]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            processModal.classList.remove('hidden');
            processModal.querySelector('.transform').classList.remove('scale-95');
        });
    });

    if (closeProcessModal) {
        closeProcessModal.addEventListener('click', () => processModal.classList.add('hidden'));
    }
    if (okProcess) {
        okProcess.addEventListener('click', () => processModal.classList.add('hidden'));
    }

    // Fermer les modales en cliquant à l'extérieur
    [contactModal, aboutModal, processModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    modal.classList.add('hidden');
                }
            });
        }
    });
});
