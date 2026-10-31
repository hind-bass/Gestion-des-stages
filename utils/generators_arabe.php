<?php
include 'config.php';

function generer_attestation_arabe($stagiaire_id, $type = 'debut') {
    global $host, $dbname, $username, $password_db;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Récupérer les infos du stagiaire
        $stmt = $pdo->prepare("SELECT * FROM stagiaires WHERE id = ?");
        $stmt->execute([$stagiaire_id]);
        $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$stagiaire) {
            throw new Exception("Stagiaire non trouvé");
        }
        
        // Créer le contenu HTML de l'attestation en arabe
        $html = generer_html_attestation_arabe($stagiaire, $type);
        
        // Convertir en PDF
        $pdf_file = convertir_html_en_pdf_arabe($html, $stagiaire_id, $type);
        
        return $pdf_file;
        
    } catch (Exception $e) {
        error_log("Erreur génération attestation arabe: " . $e->getMessage());
        return false;
    }
}

function generer_html_attestation_arabe($stagiaire, $type) {
    $date_actuelle = date('d/m/Y');
    $numero_demande = str_pad($stagiaire['id'], 4, '0', STR_PAD_LEFT);
    
    if ($type === 'debut') {
        $titre = 'شهادة بداية التدريب';
        $contenu = 'تؤكد بلدية وجدة أن السيد(ة)';
        $action = 'قد بدأ(ت) التدريب في بلدية وجدة';
    } else {
        $titre = 'شهادة نهاية التدريب';
        $contenu = 'تؤكد بلدية وجدة أن السيد(ة)';
        $action = 'قد أنهى(ت) التدريب في بلدية وجدة';
    }
    
    $html = '
    <!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <title>Attestation de stage</title>
        <style>
            body { 
                font-family: "Arial", "Tahoma", sans-serif; 
                margin: 40px; 
                direction: rtl;
                text-align: right;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
            }
            .logo { 
                max-width: 100px; 
                margin-bottom: 10px;
            }
            .title { 
                font-size: 20px; 
                font-weight: bold; 
                text-align: center; 
                margin: 20px 0;
                color: #2c3e50;
            }
            .content { 
                line-height: 2; 
                font-size: 14px;
                margin: 30px 0;
            }
            .signature { 
                margin-top: 50px; 
                text-align: left;
                border-top: 1px solid #ccc;
                padding-top: 20px;
            }
            .footer { 
                margin-top: 30px; 
                font-size: 12px; 
                text-align: center;
                color: #7f8c8d;
            }
            .info-row {
                margin: 10px 0;
                padding: 5px 0;
            }
            .label {
                font-weight: bold;
                color: #2c3e50;
            }
            .value {
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="header">
                            <img src="assets/images/logo_commune.png" alt="شعار البلدية" class="logo">
            <h1>بلدية وجدة</h1>
            <p>مصلحة الموارد البشرية</p>
        </div>
        
        <div class="title">
            ' . $titre . '
        </div>
        
        <div class="content">
            <p>' . $contenu . ' :</p>
            
            <div class="info-row">
                <span class="label">الاسم واللقب :</span>
                <span class="value">' . htmlspecialchars($stagiaire['nom_ar']) . ' ' . htmlspecialchars($stagiaire['prenom_ar']) . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">المؤسسة التعليمية :</span>
                <span class="value">' . htmlspecialchars($stagiaire['etablissement']) . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">التخصص :</span>
                <span class="value">' . htmlspecialchars($stagiaire['filiere']) . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">المستوى :</span>
                <span class="value">' . htmlspecialchars($stagiaire['niveau']) . '</span>
            </div>
            
            <p>' . $action . ' :</p>
            
            <div class="info-row">
                <span class="label">تاريخ بداية التدريب :</span>
                <span class="value">' . date('d/m/Y', strtotime($stagiaire['date_debut_souhaitee'])) . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">تاريخ نهاية التدريب :</span>
                <span class="value">' . date('d/m/Y', strtotime($stagiaire['date_fin_souhaite'])) . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">رقم الطلب :</span>
                <span class="value">' . $numero_demande . '</span>
            </div>
            
            <div class="info-row">
                <span class="label">تاريخ إيداع الطلب :</span>
                <span class="value">' . $date_actuelle . '</span>
            </div>
        </div>
        
        <div class="signature">
            <p>وجدة، في ' . $date_actuelle . '</p>
            <p>مسؤول الموارد البشرية</p>
            <br><br><br>
            <p>_____________________</p>
        </div>
        
        <div class="footer">
            <p>هذه الشهادة صادرة للعمل بها حسب الأصول.</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

function convertir_html_en_pdf_arabe($html, $stagiaire_id, $type) {
    // Créer le dossier pour les attestations
    $attestations_dir = 'attestations/';
    if (!is_dir($attestations_dir)) {
        mkdir($attestations_dir, 0777, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $html_filename = 'attestation_' . $type . '_' . $stagiaire_id . '_' . $timestamp . '.html';
    $pdf_filename = 'attestation_' . $type . '_' . $stagiaire_id . '_' . $timestamp . '.pdf';
    
    $html_filepath = $attestations_dir . $html_filename;
    $pdf_filepath = $attestations_dir . $pdf_filename;
    
    // Sauvegarder le HTML
    file_put_contents($html_filepath, $html);
    
    // Convertir en PDF avec wkhtmltopdf
    $wkhtmltopdf_path = findWkhtmltopdfPath();
    $command = "\"$wkhtmltopdf_path\" --quiet --page-size A4 --margin-top 20 --margin-bottom 20 --margin-left 20 --margin-right 20 --encoding UTF-8 \"$html_filepath\" \"$pdf_filepath\" 2>&1";
    $result = shell_exec($command);
    
    if (file_exists($pdf_filepath)) {
        // Nettoyer le fichier HTML temporaire
        unlink($html_filepath);
        return $pdf_filepath;
    } else {
        // Si la conversion échoue, retourner le HTML
        error_log("Erreur conversion PDF arabe: $result");
        return $html_filepath;
    }
}

function findWkhtmltopdfPath() {
    // Chemins possibles pour wkhtmltopdf sur Windows
    $possible_paths = [
        'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'C:\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
        'wkhtmltopdf' // Essayer le PATH
    ];
    
    foreach ($possible_paths as $path) {
        if ($path === 'wkhtmltopdf') {
            $output = shell_exec('wkhtmltopdf --version 2>&1');
        } else {
            $output = shell_exec("\"$path\" --version 2>&1");
        }
        
        if ($output && strpos($output, 'wkhtmltopdf') !== false) {
            return $path;
        }
    }
    
    // Si aucun chemin trouvé, retourner le nom de commande par défaut
    return 'wkhtmltopdf';
}

// Test de la fonction
if (isset($_GET['test']) && isset($_GET['id'])) {
    $type = $_GET['type'] ?? 'debut';
    $result = generer_attestation_arabe($_GET['id'], $type);
    if ($result) {
        echo "Attestation arabe générée : <a href='$result' target='_blank'>Voir l'attestation</a>";
    } else {
        echo "Erreur lors de la génération";
    }
}
?> 