<!-- Modale Créer Responsable -->
<div id="modal-creer-responsable" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Créer un responsable</h3>
                <button onclick="closeModal('modal-creer-responsable')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-creer-responsable">
                <div class="mb-4">
                    <label for="responsable_nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" id="responsable_nom" name="responsable_nom" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="responsable_prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <input type="text" id="responsable_prenom" name="responsable_prenom" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="responsable_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="responsable_email" name="responsable_email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="responsable_password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" id="responsable_password" name="responsable_password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="responsable_service" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                    <select id="responsable_service" name="responsable_service" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Sélectionner un service</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Ressources Humaines">Ressources Humaines</option>
                        <option value="Finances">Finances</option>
                        <option value="Urbanisme">Urbanisme</option>
                        <option value="Communication">Communication</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="responsable_telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" id="responsable_telephone" name="responsable_telephone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-creer-responsable')" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <i class="fas fa-plus mr-2"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale Ajouter Service -->
<div id="modal-ajouter-service" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un service</h3>
                <button onclick="closeModal('modal-ajouter-service')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-ajouter-service">
                <div class="mb-4">
                    <label for="service_nom" class="block text-sm font-medium text-gray-700 mb-2">Nom du service</label>
                    <input type="text" id="service_nom" name="service_nom" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="service_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="service_description" name="service_description" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="service_responsable" class="block text-sm font-medium text-gray-700 mb-2">Responsable (optionnel)</label>
                    <select id="service_responsable" name="service_responsable" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Aucun responsable</option>
                        <!-- Les responsables seront chargés via AJAX -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="service_capacite" class="block text-sm font-medium text-gray-700 mb-2">Capacité maximale</label>
                    <input type="number" id="service_capacite" name="service_capacite" min="1" max="50" value="20" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-ajouter-service')" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale Vérifier Stagiaire -->
<div id="modal-verifier-stagiaire" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Vérifier le dossier stagiaire</h3>
                <button onclick="closeModal('modal-verifier-stagiaire')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="stagiaire-details" class="space-y-4">
                <!-- Les détails du stagiaire seront chargés via AJAX -->
            </div>
            
            <!-- Ajout du tableau de validation des documents dans la modale de vérification stagiaire -->
<div id="documents-validation" class="mt-6">
  <table class="min-w-full divide-y divide-gray-200">
    <thead>
      <tr>
        <th>Type</th>
        <th>Document</th>
        <th>Statut</th>
        <th>Commentaire</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="documents-validation-body">
      <!-- Rempli dynamiquement en JS -->
    </tbody>
  </table>
</div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeModal('modal-verifier-stagiaire')" 
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Fermer
                </button>
                <button id="btn-valider-stagiaire" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-check mr-2"></i>Valider
                </button>
                <button id="btn-refuser-stagiaire" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fas fa-times mr-2"></i>Refuser
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale Affecter Stagiaire -->
<div id="modal-affecter-stagiaire" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Affecter un stagiaire</h3>
                <button onclick="closeModal('modal-affecter-stagiaire')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-affecter-stagiaire">
                <input type="hidden" id="affectation_stagiaire_id" name="affectation_stagiaire_id">
                
                <div class="mb-4">
                    <label for="affectation_service" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                    <select id="affectation_service" name="affectation_service" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Sélectionner un service</option>
                        <!-- Les services seront chargés via AJAX -->
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="affectation_responsable" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select id="affectation_responsable" name="affectation_responsable" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Sélectionner un responsable</option>
                        <!-- Les responsables seront chargés via AJAX -->
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-affecter-stagiaire')" 
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <i class="fas fa-link mr-2"></i>Affecter
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
                <i class="fas fa-info text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="notification-title">Information</h3>
            <p class="text-sm text-gray-500 mb-4" id="notification-message">Message de notification</p>
            <div class="flex justify-center">
                <button onclick="closeModal('modal-notification')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale Modifier Responsable -->
<div id="modal-modifier-responsable" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Modifier un responsable</h3>
                <button onclick="closeModal('modal-modifier-responsable')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-modifier-responsable">
                <input type="hidden" id="modifier_responsable_id" name="responsable_id">
                <div class="mb-4">
                    <label for="modifier_responsable_nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" id="modifier_responsable_nom" name="responsable_nom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label for="modifier_responsable_prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <input type="text" id="modifier_responsable_prenom" name="responsable_prenom" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label for="modifier_responsable_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="modifier_responsable_email" name="responsable_email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label for="modifier_responsable_service" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                    <input type="text" id="modifier_responsable_service" name="responsable_service" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label for="modifier_responsable_telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" id="modifier_responsable_telephone" name="responsable_telephone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modal-modifier-responsable')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500"><i class="fas fa-save mr-2"></i>Enregistrer</button>
                </div>
            </form>
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
            icon.classList.add('bg-purple-100');
            iconElement.classList.add('fa-info', 'text-purple-600');
    }
    
    showModal('modal-notification');
}

// Gestion des formulaires
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire créer responsable
    document.getElementById('form-creer-responsable').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/admin_creer_responsable.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Responsable créé avec succès!', 'success');
                closeModal('modal-creer-responsable');
                this.reset();
                loadResponsables(); // Recharger la liste des responsables
            } else {
                showNotification('Erreur', data.message || 'Erreur lors de la création', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors de la création du responsable', 'error');
        });
    });
    
    // Formulaire ajouter service
    document.getElementById('form-ajouter-service').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/admin_ajouter_service.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Service ajouté avec succès!', 'success');
                closeModal('modal-ajouter-service');
                this.reset();
                loadServices(); // Recharger la liste des services
            } else {
                showNotification('Erreur', data.message || 'Erreur lors de l\'ajout', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors de l\'ajout du service', 'error');
        });
    });
    
    // Formulaire affecter stagiaire
    document.getElementById('form-affecter-stagiaire').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('api/admin_affecter_stagiaire.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Stagiaire affecté avec succès!', 'success');
                closeModal('modal-affecter-stagiaire');
                this.reset();
                loadStagiaires(); // Recharger la liste des stagiaires
            } else {
                showNotification('Erreur', data.message || 'Erreur lors de l\'affectation', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors de l\'affectation', 'error');
        });
    });

    // Formulaire modifier responsable
    document.getElementById('form-modifier-responsable').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('api/admin_modifier_responsable.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Succès', 'Responsable modifié avec succès!', 'success');
                closeModal('modal-modifier-responsable');
                this.reset();
                loadResponsables(); // Recharger la liste des responsables
            } else {
                showNotification('Erreur', data.message || 'Erreur lors de la modification', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors de la modification du responsable', 'error');
        });
    });
});

// Fermer les modales en cliquant à l'extérieur
window.addEventListener('click', function(e) {
    const modals = ['modal-creer-responsable', 'modal-ajouter-service', 'modal-verifier-stagiaire', 'modal-affecter-stagiaire', 'modal-confirmation', 'modal-notification'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            closeModal(modalId);
        }
    });
});

function validerDocument(docId) {
  const statut = document.querySelector(`[name='statut_doc_${docId}']`).value;
  const commentaire = document.querySelector(`[name='commentaire_doc_${docId}']`).value;
  fetch('api/admin_stagiaires.php?action=valider_document', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      doc_id: docId,
      statut: statut,
      commentaire: commentaire
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showNotification('Succès', 'Document mis à jour', 'success');
    }
  });
}
// Fonction pour remplir le tableau des documents (à appeler après chargement des détails du stagiaire)
function afficherDocumentsValidation(documents) {
  const tbody = document.getElementById('documents-validation-body');
  tbody.innerHTML = '';
  documents.forEach(doc => {
    tbody.innerHTML += `
      <tr>
        <td>${doc.type_document}</td>
        <td><a href="${doc.chemin_fichier}" target="_blank">Voir</a></td>
        <td>
          <select name="statut_doc_${doc.id}">
            <option value="en_attente" ${doc.statut === 'en_attente' ? 'selected' : ''}>En attente</option>
            <option value="valide" ${doc.statut === 'valide' ? 'selected' : ''}>Valide</option>
            <option value="refuse" ${doc.statut === 'refuse' ? 'selected' : ''}>Refusé</option>
          </select>
        </td>
        <td><input type="text" name="commentaire_doc_${doc.id}" value="${doc.commentaire || ''}" placeholder="Commentaire si refus"></td>
        <td><button type="button" onclick="validerDocument(${doc.id})">Enregistrer</button></td>
      </tr>
    `;
  });
}

// Supposons que tu as déjà une fonction qui charge les détails du stagiaire via AJAX, par exemple :
function chargerDetailsStagiaire(id) {
  fetch('api/admin_stagiaires.php?action=details&id=' + id)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Affichage des autres détails du stagiaire...
        // ...
        // Affichage du tableau de validation des documents :
        afficherDocumentsValidation(data.data.documents);
      }
    });
}

// Fonction pour ouvrir la modale de modification et pré-remplir les champs
function ouvrirModaleModifierResponsable(responsable) {
    document.getElementById('modifier_responsable_id').value = responsable.id;
    document.getElementById('modifier_responsable_nom').value = responsable.nom;
    document.getElementById('modifier_responsable_prenom').value = responsable.prenom;
    document.getElementById('modifier_responsable_email').value = responsable.email;
    document.getElementById('modifier_responsable_service').value = responsable.service;
    document.getElementById('modifier_responsable_telephone').value = responsable.telephone || '';
    showModal('modal-modifier-responsable');
}
</script> 