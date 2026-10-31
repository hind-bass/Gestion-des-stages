<?php
$contact_nom = '';
$contact_email = '';
if (isset($_SESSION['admin_id'])) {
    $contact_nom = $_SESSION['admin_prenom'] . ' ' . $_SESSION['admin_nom'];
    $contact_email = $_SESSION['admin_email'];
} elseif (isset($_SESSION['stagiaire_id'])) {
    $contact_nom = $_SESSION['stagiaire_prenom'] . ' ' . $_SESSION['stagiaire_nom'];
    $contact_email = $_SESSION['stagiaire_email'];
} elseif (isset($_SESSION['responsable_id'])) {
    $contact_nom = $_SESSION['responsable_prenom'] . ' ' . $_SESSION['responsable_nom'];
    $contact_email = $_SESSION['responsable_email'];
}
?>
<!-- Contact Modal -->
<div id="contactModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeContactModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-gradient-to-br from-blue-600 to-purple-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gradient-to-br from-blue-600 to-purple-700 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                <div class="section-header">
                <div class="section-logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo_commune.png" alt="Logo Commune Oujda" class="section-logo-img">
                </div>
            </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                            Contactez la Commune
                        </h3>
                        <p class="text-sm text-blue-100 mt-1">
                            Votre message sera envoyé à : <strong>contact@commune-oujda.ma</strong>
                        </p>
                        <div class="mt-4">
                            <form id="contactForm" class="space-y-4">
                                <div>
                                    <label for="contact-name" class="block text-sm font-medium text-white">Nom complet *</label>
                                    <input type="text" name="contact-name" id="contact-name" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Votre nom complet" value="<?php echo htmlspecialchars($contact_nom); ?>" <?php if($contact_nom) echo 'readonly'; ?>>
                                </div>
                                <div>
                                    <label for="contact-email" class="block text-sm font-medium text-white">Email *</label>
                                    <div class="relative">
                                        <input type="email" name="contact-email" id="contact-email" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 pr-10 border" placeholder="votre@email.com" value="<?php echo htmlspecialchars($contact_email); ?>" <?php if($contact_email) echo 'readonly'; ?>>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="contact-subject" class="block text-sm font-medium text-white">Sujet *</label>
                                    <select name="contact-subject" id="contact-subject" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md">
                                        <option value="">Choisir un sujet</option>
                                        <option value="demande-stage">Demande de stage</option>
                                        <option value="information">Demande d'information</option>
                                        <option value="probleme-technique">Problème technique</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="contact-message" class="block text-sm font-medium text-white">Message *</label>
                                    <textarea name="contact-message" id="contact-message" rows="4" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Votre message..."></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-700 to-purple-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitContactForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-white text-purple-700 text-base font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-300 transform hover:scale-105">
                    Envoyer le message
                </button>
                <button type="button" onclick="closeContactModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-white border-opacity-30 shadow-sm px-4 py-2 bg-transparent text-base font-medium text-white hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-300 transform hover:scale-105">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div> 