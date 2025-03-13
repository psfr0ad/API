<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include 'pdo.php';

error_log("Requête reçue - GET params: " . print_r($_GET, true));

if (isset($_GET['email']) && !empty($_GET['email'])) {
    try {
        $email = $_GET['email'];
        error_log("Recherche de l'utilisateur avec l'email: " . $email);
        
        // Modifié pour utiliser la colonne mail
        $stmt = $pdo->prepare("SELECT mail, RefRole FROM utilisateurs WHERE mail = ?");
        $stmt->execute([$email]);
        
        error_log("Requête SQL exécutée: SELECT mail, RefRole FROM utilisateurs WHERE mail = '$email'");
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            error_log("Utilisateur trouvé: " . print_r($user, true));
            echo json_encode([
                'mail' => $user['mail'],
                'RefRole' => intval($user['RefRole'])
            ]);
        } else {
            error_log("Aucun utilisateur trouvé pour l'email: $email");
            echo json_encode([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ]);
        }
    } catch(PDOException $e) {
        error_log("Erreur PDO: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de base de données',
            'error' => $e->getMessage()
        ]);
    }
} else {
    error_log("Paramètre email manquant ou vide");
    echo json_encode([
        'success' => false,
        'message' => 'Email utilisateur requis'
    ]);
}
?>