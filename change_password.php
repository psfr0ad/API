<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

include 'pdo.php';

try {
    // Récupérer les données JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['current_password']) || !isset($data['new_password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Toutes les données requises ne sont pas fournies'
        ]);
        exit;
    }

    $email = $data['email'];
    $currentPassword = $data['current_password'];
    $newPassword = $data['new_password'];

    // Vérifier l'ancien mot de passe
    $stmt = $pdo->prepare("SELECT passwordA FROM utilisateurs WHERE mail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non trouvé'
        ]);
        exit;
    }

    if (!password_verify($currentPassword, $user['passwordA'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Mot de passe actuel incorrect'
        ]);
        exit;
    }

    // Hasher le nouveau mot de passe
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe
    $stmt = $pdo->prepare("UPDATE utilisateurs SET passwordA = ? WHERE mail = ?");
    
    if ($stmt->execute([$hashedNewPassword, $email])) {
        echo json_encode([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès'
        ]);
    } else {
        throw new Exception("Erreur lors de la modification du mot de passe");
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
        'message' => 'Erreur lors de la modification: ' . $e->getMessage()
    ]);
}
?>