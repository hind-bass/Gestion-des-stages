<?php
namespace controllers;

use core\BaseController;

class AccueilController extends BaseController {
    public function index() {
        $this->view('accueil'); // charge views/accueil.php
    }

    public function contact() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            $name = $data['nom_complet'] ?? '';
            $email = $data['email'] ?? '';
            $subject = $data['sujet'] ?? '';
            $message = $data['message'] ?? '';

            if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($subject) && !empty($message)) {
                // La logique pour envoyer un e-mail serait ici
                // Pour l'instant, nous simulons une réponse de succès
                echo json_encode(['success' => true, 'message' => 'Votre message a été envoyé avec succès.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs correctement.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        }
        exit;
    }
}
?>