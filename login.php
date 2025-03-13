<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

include 'pdo.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['email']) && isset($data['password'])) {
        $email = $data['email'];
        $password = $data['password'];
        
        try {
            
            // Préparer la requête
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE mail = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Vérifier le mot de passe
                if (password_verify($password, $user['passwordA'])) {
                    // Connexion réussie
                    echo json_encode([
                        'success' => true,
                        'message' => 'Connexion réussie',
                        'user' => [
                            'email' => $user['mail'],
                            'role' => $user['RefRole']
                        ]
                    ]);
                } else {
                    // Mot de passe incorrect
                    echo json_encode([
                        'success' => false,
                        'message' => 'Email ou mot de passe incorrect'
                    ]);
                }
            } else {
                // Utilisateur non trouvé
                echo json_encode([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ]);
            }
            
        } catch(PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de connexion à la base de données',
                'error' => $e->getMessage()
            ]);
        }
        
        $conn = null;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email et mot de passe requis'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>