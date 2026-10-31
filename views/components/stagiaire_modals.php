<!-- Modale Upload Document -->
<div id="modal-upload-document" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Déposer un document</h3>
                <button onclick="closeModal('modal-upload-document')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-upload-document" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="type_document" class="block text-sm font-medium text-gray-700 mb-2">Type de document</label>
                    <select id="type_document" name="type_document" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un type</option>
                        <option value="cv">CV</option>
                        <option value="attestation_admin">Attestation de stage donnée par l'administration</option>
                        <option value="carte_identite">Photocopie de la carte d'identité</option>
                        <option value="demande_stage">Demande de stage destinée au directeur de la commune</option>
                        <option value="engagement">Document d'engagement</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="document_file" class="block text-sm font-medium text-gray-700 mb-2">Fichier</label>
                    <input type="file" id="document_file" name="document_file" required 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 5MB)</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-upload-document')" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-upload mr-2"></i>Déposer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale Déposer Rapport -->
<div id="modal-deposer-rapport" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Déposer mon rapport de stage</h3>
                <button onclick="closeModal('modal-deposer-rapport')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-deposer-rapport" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="rapport_titre" class="block text-sm font-medium text-gray-700 mb-2">Titre du rapport</label>
                    <input type="text" id="rapport_titre" name="rapport_titre" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: Rapport de stage - Service Informatique">
                </div>
                
                <div class="mb-4">
                    <label for="rapport_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="rapport_description" name="rapport_description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Décrivez brièvement le contenu de votre rapport..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="rapport_file" class="block text-sm font-medium text-gray-700 mb-2">Fichier du rapport</label>
                    <input type="file" id="rapport_file" name="rapport_file" required 
                           accept=".pdf,.doc,.docx"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX (Max: 10MB)</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-deposer-rapport')" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-upload mr-2"></i>Déposer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale de confirmation -->
<div id="modal-confirmation" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-80 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="confirmation-title">Confirmation</h3>
            <p class="text-sm text-gray-500 mb-4" id="confirmation-message">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeModal('modal-confirmation')" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Annuler
                </button>
                <button id="btn-confirm-action" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale de notification -->
<div id="modal-notification" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-80 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="notification-icon">
                <i class="fas fa-info text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="notification-title">Information</h3>
            <p class="text-sm text-gray-500 mb-4" id="notification-message">Message de notification</p>
            <div class="flex justify-center">
                <button onclick="closeModal('modal-notification')" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fonctions pour les modales
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function showConfirmation(title, message, onConfirm) {
    document.getElementById('confirmation-title').textContent = title;
    document.getElementById('confirmation-message').textContent = message;
    
    const confirmBtn = document.getElementById('btn-confirm-action');
    confirmBtn.onclick = function() {
        closeModal('modal-confirmation');
        if (onConfirm) onConfirm();
    };
    
    showModal('modal-confirmation');
}

function showNotification(title, message, type = 'info') {
    document.getElementById('notification-title').textContent = title;
    document.getElementById('notification-message').textContent = message;
    
    const icon = document.getElementById('notification-icon');
    const iconElement = icon.querySelector('i');
    
    // Changer l'icône et la couleur selon le type
    icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4';
    iconElement.className = 'fas text-xl';
    
    switch(type) {
        case 'success':
            icon.classList.add('bg-green-100');
            iconElement.classList.add('fa-check', 'text-green-600');
            break;
        case 'error':
            icon.classList.add('bg-red-100');
            iconElement.classList.add('fa-exclamation-triangle', 'text-red-600');
            break;
        case 'warning':
            icon.classList.add('bg-yellow-100');
            iconElement.classList.add('fa-exclamation-triangle', 'text-yellow-600');
            break;
        default:
            icon.classList.add('bg-blue-100');
            iconElement.classList.add('fa-info', 'text-blue-600');
    }
    
    showModal('modal-notification');
}

// Gestion des formulaires
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire upload document
    document.getElementById('form-upload-document').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/stagiaire_upload_document.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Document déposé avec succès!', 'success');
                closeModal('modal-upload-document');
                this.reset();
                loadDocuments(); // Recharger la liste des documents
            } else {
                showNotification('Erreur', data.message || 'Erreur lors du dépôt du document', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors du dépôt du document', 'error');
        });
    });
    
    // Formulaire déposer rapport
    document.getElementById('form-deposer-rapport').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/stagiaire_deposer_rapport.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Rapport déposé avec succès!', 'success');
                closeModal('modal-deposer-rapport');
                this.reset();
                loadRapport(); // Recharger les informations du rapport
            } else {
                showNotification('Erreur', data.message || 'Erreur lors du dépôt du rapport', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors du dépôt du rapport', 'error');
        });
    });
});

// Fermer les modales en cliquant à l'extérieur
window.addEventListener('click', function(e) {
    const modals = ['modal-upload-document', 'modal-deposer-rapport', 'modal-confirmation', 'modal-notification'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            closeModal(modalId);
        }
    });
});
</script> 