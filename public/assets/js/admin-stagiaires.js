// Gestion des stagiaires dans l'interface administrateur
class AdminStagiaires {
    constructor() {
        this.init();
    }
    
    init() {
        this.loadStagiaires();
        this.bindEvents();
    }
    
    bindEvents() {
        // Recherche
        document.getElementById('search-stagiaires')?.addEventListener('input', (e) => {
            this.loadStagiaires(e.target.value);
        });
        
        // Filtre par statut
        document.getElementById('filter-statut')?.addEventListener('change', (e) => {
            this.loadStagiaires('', e.target.value);
        });
    }
    
    async loadStagiaires(search = '', statut = '') {
        try {
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (statut) params.append('statut', statut);
            
            const response = await fetch(`api/admin_stagiaires.php?action=list&${params}`);
            const data = await response.json();
            
            if (data.success) {
                console.log('Stagiaires reçus:', data.data);
                this.renderStagiaires(data.data);
            } else {
                console.error('Erreur:', data.error);
            }
        } catch (error) {
            console.error('Erreur lors du chargement:', error);
        }
    }
    
    renderStagiaires(stagiaires) {
        const tbody = document.getElementById('tbody-stagiaires');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        if (stagiaires.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun stagiaire trouvé</td></tr>';
            return;
        }
        console.log('Stagiaires à afficher:', stagiaires);
        stagiaires.forEach(stagiaire => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            const statusClass = this.getStatusClass(stagiaire.statut);
            const statusText = this.getStatusText(stagiaire.statut);
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">${stagiaire.prenom_fr.charAt(0)}${stagiaire.nom_fr.charAt(0)}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${stagiaire.prenom_fr} ${stagiaire.nom_fr}</div>
                            <div class="text-sm text-gray-500">${stagiaire.filiere} - ${stagiaire.niveau}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${stagiaire.email}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${stagiaire.etablissement}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${new Date(stagiaire.date_debut_souhaitee).toLocaleDateString('fr-FR')} - ${new Date(stagiaire.date_fin_souhaitee).toLocaleDateString('fr-FR')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="adminStagiaires.viewDetails(${stagiaire.id})" class="text-purple-600 hover:text-purple-900 mr-3">
                        <i class="fas fa-file-alt"></i> Vérifier documents
                    </button>
                    ${stagiaire.statut === 'en_attente' ? `
                        <button onclick="adminStagiaires.accepter(${stagiaire.id})" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-check"></i> Accepter
                        </button>
                        <button onclick="adminStagiaires.refuser(${stagiaire.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-times"></i> Refuser
                        </button>
                    ` : ''}
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
    
    getStatusClass(statut) {
        switch (statut) {
            case 'en_attente': return 'bg-yellow-100 text-yellow-800';
            case 'accepte': return 'bg-green-100 text-green-800';
            case 'refuse': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
    
    getStatusText(statut) {
        switch (statut) {
            case 'en_attente': return 'En attente';
            case 'accepte': return 'Accepté';
            case 'refuse': return 'Refusé';
            default: return 'Inconnu';
        }
    }
    
    async viewDetails(id) {
        try {
            const response = await fetch(`api/admin_stagiaires.php?action=details&id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                this.showModal(data.data);
            } else {
                alert('Erreur: ' + data.error);
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
    
    showModal(stagiaire) {
        const modal = document.getElementById('modal-stagiaire');
        const content = document.getElementById('modal-content');
        console.log('Documents reçus:', stagiaire.documents);
        content.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Informations personnelles</h4>
                    <div class="space-y-2">
                        <p><strong>Nom (FR):</strong> ${stagiaire.nom_fr}</p>
                        <p><strong>Prénom (FR):</strong> ${stagiaire.prenom_fr}</p>
                        <p><strong>Nom (AR):</strong> ${stagiaire.nom_ar}</p>
                        <p><strong>Prénom (AR):</strong> ${stagiaire.prenom_ar}</p>
                        <p><strong>Email:</strong> ${stagiaire.email}</p>
                        <p><strong>Téléphone:</strong> ${stagiaire.telephone}</p>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Informations académiques</h4>
                    <div class="space-y-2">
                        <p><strong>Établissement:</strong> ${stagiaire.etablissement}</p>
                        <p><strong>Filière:</strong> ${stagiaire.filiere}</p>
                        <p><strong>Niveau:</strong> ${stagiaire.niveau}</p>
                        <p><strong>Date début:</strong> ${new Date(stagiaire.date_debut_souhaitee).toLocaleDateString('fr-FR')}</p>
                        <p><strong>Date fin:</strong> ${new Date(stagiaire.date_fin_souhaitee).toLocaleDateString('fr-FR')}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="font-medium text-gray-900 mb-4">Documents fournis</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${stagiaire.documents && stagiaire.documents.length ? stagiaire.documents.map(doc => `
                        <div class="border rounded p-3">
                            <p class="font-medium">${doc.type_document}</p>
                            <a href="${doc.chemin_fichier}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-download mr-1"></i>Voir le document
                            </a>
                        </div>
                    `).join('') : '<div class="text-center text-gray-500">Aucun document</div>'}
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
    }
    
    async accepter(id) {
        if (!confirm('Êtes-vous sûr de vouloir accepter ce stagiaire ?')) return;
        
        try {
            const response = await fetch(`api/admin_stagiaires.php?action=accepter&id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                this.loadStagiaires();
            } else {
                alert('Erreur: ' + data.error);
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
    
    async refuser(id) {
        if (!confirm('Êtes-vous sûr de vouloir refuser ce stagiaire ?')) return;
        
        try {
            const response = await fetch(`api/admin_stagiaires.php?action=refuser&id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                this.loadStagiaires();
            } else {
                alert('Erreur: ' + data.error);
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }
}

// Initialiser quand la page est chargée
document.addEventListener('DOMContentLoaded', () => {
    window.adminStagiaires = new AdminStagiaires();
});

// Fonction pour fermer le modal
function closeModal() {
    document.getElementById('modal-stagiaire').classList.add('hidden');
} 

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        // Charger les données si nécessaire
        if (
            modalId === 'modal-ajouter-tache' ||
            modalId === 'modal-ajouter-remarque' ||
            modalId === 'modal-generer-attestation' ||
            modalId === 'modal-ajouter-decision'
        ) {
            loadStagiairesForModal(modalId);
        }
    }
}

// Exemple d'appel AJAX lors de la soumission du formulaire d'affectation
$('#form-affecter-responsable').on('submit', function(e) {
    e.preventDefault();
    $.post('api/admin_stagiaires.php?action=affecter_responsable', {
        stagiaire_id: $('#stagiaire_id').val(),
        responsable_id: $('#responsable_id').val(),
        service_demande: $('#service_demande').val()
    }, function(response) {
        if (response.success) {
            alert('Responsable affecté avec succès');
            location.reload();
        } else {
            alert('Erreur : ' + response.message);
        }
    }, 'json');
});
// Valider le document et accepter le stagiaire
$('.btn-valider-document').on('click', function() {
    var docId = $(this).data('doc-id');
    var stagiaireId = $(this).data('stagiaire-id');
    var commentaire = $('#commentaire_' + docId).val() || '';

    // 1. Valider le document
    $.post('api/admin_stagiaires.php?action=valider_document', {
        doc_id: docId,
        statut: 'valide',
        commentaire: commentaire
    }, function(response) {
        if (response.success) {
            // 2. Accepter le stagiaire après validation du document
            $.post('api/admin_stagiaires.php?action=accepter', {
                id: stagiaireId
            }, function(resp) {
                if (resp.success) {
                    alert('Document validé et stagiaire accepté !');
                    location.reload();
                } else {
                    alert('Erreur lors de l\'acceptation du stagiaire : ' + (resp.message || ''));
                }
            }, 'json');
        } else {
            alert('Erreur lors de la validation du document : ' + (response.message || ''));
        }
    }, 'json');
});

function loadStagiairesForModal(modalId) {
    fetch('api/responsable_stagiaires.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.querySelector(`#${modalId} select[name$="_stagiaire_id"]`);
                if (select) {
                    // Toujours vider le select avant de remplir
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
// Corrections pour le JavaScript dans administrateur.php

// Fonction corrigée pour charger les stagiaires
function loadStagiaires() {
    console.log('Chargement des stagiaires...');
    
    // Récupérer les filtres
    const search = document.getElementById('search-stagiaires')?.value || '';
    const status = document.getElementById('filter-statut')?.value || '';
    
    // Construire l'URL avec les paramètres
    let url = 'api/admin_stagiaires.php?action=list';
    if (search) url += '&search=' + encodeURIComponent(search);
    if (status) url += '&statut=' + encodeURIComponent(status);
    
    fetch(url)
        .then(response => {
            console.log('Réponse status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            
            if (data.success && data.data) {
                const container = document.getElementById('tbody-stagiaires');
                if (!container) {
                    console.error('Container tbody-stagiaires non trouvé');
                    return;
                }
                
                container.innerHTML = '';
                
                if (data.data.length === 0) {
                    container.innerHTML = '<tr><td colspan="6" class="text-center py-4">Aucun stagiaire trouvé</td></tr>';
                    return;
                }
                
                data.data.forEach(stagiaire => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${stagiaire.prenom_fr} ${stagiaire.nom_fr}</div>
                                    <div class="text-sm text-gray-500">${stagiaire.cin || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${stagiaire.email}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${stagiaire.service_demande_display || 'Non spécifié'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                ${getStatusClass(stagiaire.statut)}">
                                ${stagiaire.statut_display || stagiaire.statut}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="text-blue-600">${stagiaire.nombre_documents || 0} docs</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="verifierStagiaire(${stagiaire.id})" class="text-purple-600 hover:text-purple-900 mr-3" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="accepterStagiaire(${stagiaire.id})" class="text-green-600 hover:text-green-900 mr-3" title="Accepter">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="refuserStagiaire(${stagiaire.id})" class="text-red-600 hover:text-red-900 mr-3" title="Refuser">
                                <i class="fas fa-times"></i>
                            </button>
                            <button onclick="genererAttestation(${stagiaire.id})" class="text-blue-600 hover:text-blue-900" title="Générer attestation">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td>
                    `;
                    container.appendChild(row);
                });
                
                console.log(`${data.data.length} stagiaires chargés`);
            } else {
                console.error('Erreur dans les données:', data);
                showNotification('Erreur', data.error || 'Erreur lors du chargement des stagiaires', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des stagiaires:', error);
            showNotification('Erreur', 'Erreur de connexion lors du chargement', 'error');
        });
}

// Fonction pour obtenir la classe CSS selon le statut
function getStatusClass(statut) {
    switch (statut) {
        case 'accepte':
        case 'valide':
            return 'bg-green-100 text-green-800';
        case 'refuse':
            return 'bg-red-100 text-red-800';
        case 'en_attente':
        default:
            return 'bg-yellow-100 text-yellow-800';
    }
}

// Fonction corrigée pour accepter un stagiaire
function accepterStagiaire(id) {
    if (!confirm('Êtes-vous sûr de vouloir accepter ce stagiaire ?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('api/admin_stagiaires.php?action=accepter', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Succès', data.message, 'success');
            loadStagiaires(); // Recharger la liste
        } else {
            showNotification('Erreur', data.error || 'Erreur lors de l\'acceptation', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur', 'Erreur de connexion', 'error');
    });
}

// Fonction corrigée pour refuser un stagiaire
function refuserStagiaire(id) {
    const motif = prompt('Motif du refus (optionnel):');
    if (motif === null) return; // Annulé par l'utilisateur
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('motif', motif || 'Aucun motif spécifié');
    
    fetch('api/admin_stagiaires.php?action=refuser', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Succès', data.message, 'success');
            loadStagiaires(); // Recharger la liste
        } else {
            showNotification('Erreur', data.error || 'Erreur lors du refus', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur', 'Erreur de connexion', 'error');
    });
}

// Fonction corrigée pour voir les détails d'un stagiaire
function verifierStagiaire(id) {
    currentStagiaireId = id;

    fetch(`api/admin_stagiaires.php?action=details&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les détails du stagiaire
                const stagiaire = data.stagiaire;
                document.getElementById('details-nom').innerText = stagiaire.nom_fr;
                document.getElementById('details-prenom').innerText = stagiaire.prenom_fr;
                document.getElementById('details-email').innerText = stagiaire.email;
                document.getElementById('details-cin').innerText = stagiaire.cin;
                document.getElementById('details-service').innerText = stagiaire.service_demande;
                document.getElementById('details-date').innerText = stagiaire.date_inscription;

                // Afficher la modal
                const modal = document.getElementById('details-modal');
                modal.classList.remove('hidden');
            } else {
                showNotification('Erreur', data.error || 'Erreur lors de la récupération des détails', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur de connexion', 'error');
        });
}