<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = $_POST['email'];
    
    $sql = "UPDATE utilisateurs SET  email=?";
    $params = [$email];
    
    // Si un nouveau mot de passe est fourni
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password=?";
        $params[] = $password;
    }
    
    $sql .= " WHERE id=?";
    $params[] = $id;
    
    try {
        $stmt = $connexion->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>