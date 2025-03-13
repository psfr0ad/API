<?php
require_once "pdo.php";

$action = $_GET['action'] ?? '';

if ($action == "get") {
    $result = $pdo->query("SELECT * FROM cours ORDER BY Libcours, jour, HD, HF");
    $data = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
}

if ($action == "add") {
    $libcours = $_POST['Libcours'] ?? '';
    $jour = $_POST['jour'] ?? '';
    $HD = $_POST['HD'] ?? '';
    $HF = $_POST['HF'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO cours (Libcours, jour, HD, HF) VALUES (:libcours, :jour, :HD, :HF)");
    $stmt->execute(['libcours' => $libcours, 'jour' => $jour, 'HD' => $HD, 'HF' => $HF]);

    echo json_encode(["message" => "Cours ajouté"]);
}

if ($action == "update") {
    $id = $_POST['id'] ?? '';
    $libcours = $_POST['Libcours'] ?? '';
    $jour = $_POST['jour'] ?? '';
    $HD = $_POST['HD'] ?? '';
    $HF = $_POST['HF'] ?? '';

    $stmt = $pdo->prepare("UPDATE cours SET Libcours = :libcours, jour = :jour, HD = :HD, HF = :HF WHERE idCours = :id");
    $stmt->execute(['libcours' => $libcours, 'jour' => $jour, 'HD' => $HD, 'HF' => $HF, 'id' => $id]);

    echo json_encode(["message" => "Cours mis à jour"]);
}

if ($action == "delete") {
    $id = $_POST['id'] ?? '';

    $stmt = $pdo->prepare("DELETE FROM cours WHERE idCours = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(["message" => "Cours supprimé"]);
}
?>



