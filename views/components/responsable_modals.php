<!-- Modal Ajouter une tâche -->
<div id="modal-ajouter-tache" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter une tâche</h3>
                <button onclick="closeModal('modal-ajouter-tache')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-ajouter-tache" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stagiaire</label>
                    <select name="tache_stagiaire_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Sélectionner un stagiaire</option>
                        <!-- Les options seront chargées via AJAX -->
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Titre de la tâche</label>
                    <input type="text" name="tache_titre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="tache_description" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date d'échéance</label>
                    <input type="date" name="tache_date_echeance" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priorité</label>
                    <select name="tache_priorite" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="normale">Normale</option>
                        <option value="haute">Haute</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-ajouter-tache')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter une remarque -->
<div id="modal-ajouter-remarque" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter une remarque</h3>
                <button onclick="closeModal('modal-ajouter-remarque')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-ajouter-remarque" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stagiaire</label>
                    <select name="remarque_stagiaire_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Sélectionner un stagiaire</option>
                        <!-- Les options seront chargées via AJAX -->
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type de remarque</label>
                    <select name="remarque_type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="neutre">Neutre</option>
                        <option value="positif">Positif</option>
                        <option value="negatif">Négatif</option>
                        <option value="recommandation">Recommandation</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contenu</label>
                    <textarea name="remarque_contenu" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Décrivez votre remarque..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-ajouter-remarque')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Générer attestation -->
<div id="modal-generer-attestation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Générer une attestation</h3>
                <button onclick="closeModal('modal-generer-attestation')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-generer-attestation" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stagiaire</label>
                    <select name="attestation_stagiaire_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Sélectionner un stagiaire</option>
                        <!-- Les options seront chargées via AJAX -->
                    </select>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                L'attestation de fin de stage ne peut être générée que pour les stages terminés.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-generer-attestation')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        <i class="fas fa-file-pdf mr-2"></i>Générer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmation -->
<div id="modal-confirmation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="confirmation-title">Confirmation</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500" id="confirmation-message">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button id="confirmation-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Annuler
                </button>
                <button id="confirmation-confirm" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter une décision -->
<div id="modal-ajouter-decision" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Prendre une décision</h3>
                <button onclick="closeModal('modal-ajouter-decision')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-ajouter-decision" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stagiaire</label>
                    <select name="decision_stagiaire_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Sélectionner un stagiaire</option>
                        <!-- Les options seront chargées via AJAX -->
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Décision</label>
                    <select name="decision_type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Choisir une décision</option>
                        <option value="valide">Valider</option>
                        <option value="refuse">Refuser</option>
                    </select>
                </div>
                
                <div id="note-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">Note finale (/20)</label>
                    <input type="number" name="decision_note" min="0" max="20" step="0.5" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea name="decision_commentaire" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Justifiez votre décision..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-ajouter-decision')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        <i class="fas fa-gavel mr-2"></i>Prendre la décision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Notification -->
<div id="modal-notification" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="notification-icon">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="notification-title">Succès</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500" id="notification-message">L'opération a été effectuée avec succès.</p>
            </div>
            <div class="mt-4">
                <button onclick="closeModal('modal-notification')" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion des modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        
        // Charger les données si nécessaire
        if (modalId === 'modal-ajouter-tache' || modalId === 'modal-ajouter-remarque' || modalId === 'modal-generer-attestation' || modalId === 'modal-ajouter-decision') {
            loadStagiairesForModal(modalId);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        
        // Réinitialiser les formulaires
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Charger les stagiaires pour les modals
function loadStagiairesForModal(modalId) {
    fetch('api/responsable_stagiaires.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.querySelector(`#${modalId} select[name$="_stagiaire_id"]`);
                if (select) {
                    select.innerHTML = '<option value="">Sélectionner un stagiaire</option>';
                    data.stagiaires.forEach(stagiaire => {
                        const option = document.createElement('option');
                        option.value = stagiaire.id;
                        option.textContent = stagiaire.nom_complet;
                        select.appendChild(option);
                    });
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Gestion des formulaires
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire d'ajout de tâche
    document.getElementById('form-ajouter-tache')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/responsable_ajouter_tache.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-ajouter-tache');
                showNotification('Succès', data.message, 'success');
                loadTachesData(); // Recharger les données
            } else {
                showNotification('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    });
    
    // Formulaire d'ajout de remarque
    document.getElementById('form-ajouter-remarque')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/responsable_ajouter_remarque.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-ajouter-remarque');
                showNotification('Succès', data.message, 'success');
                loadRemarquesData(); // Recharger les données
            } else {
                showNotification('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    });
    
    // Formulaire de génération d'attestation
    document.getElementById('form-generer-attestation')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const stagiaireId = this.querySelector('select[name="attestation_stagiaire_id"]').value;
        
        fetch('api/responsable_generer_attestation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ stagiaire_id: stagiaireId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-generer-attestation');
                showNotification('Succès', data.message, 'success');
                loadAttestationsData(); // Recharger les données
            } else {
                showNotification('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    });
    
    // Formulaire d'ajout de décision
    document.getElementById('form-ajouter-decision')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/responsable_decision.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-ajouter-decision');
                showNotification('Succès', data.message, 'success');
                loadDecisionsData(); // Recharger les données
            } else {
                showNotification('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    });
    
    // Gestion de l'affichage de la note selon la décision
    document.querySelector('select[name="decision_type"]')?.addEventListener('change', function() {
        const noteContainer = document.getElementById('note-container');
        const noteInput = document.querySelector('input[name="decision_note"]');
        
        if (this.value === 'valide') {
            noteContainer.classList.remove('hidden');
            noteInput.required = true;
        } else {
            noteContainer.classList.add('hidden');
            noteInput.required = false;
            noteInput.value = '';
        }
    });
});

// Fonctions utilitaires
function showNotification(title, message, type = 'success') {
    const modal = document.getElementById('modal-notification');
    const icon = document.getElementById('notification-icon');
    const titleEl = document.getElementById('notification-title');
    const messageEl = document.getElementById('notification-message');
    
    // Configurer l'apparence selon le type
    if (type === 'success') {
        icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100';
        icon.innerHTML = '<i class="fas fa-check text-green-600"></i>';
    } else {
        icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100';
        icon.innerHTML = '<i class="fas fa-exclamation-triangle text-red-600"></i>';
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    modal.classList.remove('hidden');
}

function showConfirmation(title, message, onConfirm) {
    const modal = document.getElementById('modal-confirmation');
    const titleEl = document.getElementById('confirmation-title');
    const messageEl = document.getElementById('confirmation-message');
    const confirmBtn = document.getElementById('confirmation-confirm');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Supprimer les anciens event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Ajouter le nouveau event listener
    newConfirmBtn.addEventListener('click', function() {
        closeModal('modal-confirmation');
        if (onConfirm) onConfirm();
    });
    
    modal.classList.remove('hidden');
}
</script> 