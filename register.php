<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

// Activer le rapport d'erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

include 'pdo.php';

try {
    // Récupérer les données JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON invalide: ' . json_last_error_msg());
    }

    if (!isset($data['email']) || !isset($data['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Email et mot de passe requis'
        ]);
        exit;
    }

    $email = trim($data['email']);
    $password = $data['password'];

    // Validation de l'email
    if (strlen($email) > 25) {
        echo json_encode([
            'success' => false,
            'message' => 'L\'email ne doit pas dépasser 25 caractères'
        ]);
        exit;
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT mail FROM utilisateurs WHERE mail = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cet email est déjà utilisé'
        ]);
        exit;
    }

    // Hasher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insérer le nouvel utilisateur
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (mail, passwordA, RefRole) VALUES (?, ?, 2)");
    
    if ($stmt->execute([$email, $hashedPassword])) {
        echo json_encode([
            'success' => true,
            'message' => 'Inscription réussie'
        ]);
    } else {
        throw new Exception("Erreur lors de l'insertion");
    }

} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données'
    ]);
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
    ]);
}
?>