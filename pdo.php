<?php
try{
        $db = "haras_des_neuilles";
        $User = "unknowAdsupervisorC";
        $Pass = "tofjuf-Sigkiq-8fanqe";
        $Serveur = "localhost";
        $pdo = new PDO("mysql:host=$Serveur;dbname=$db", $User, $Pass);
        return $pdo;
        //new PDO("mysql:host=$Serveur;dbname=$db,$User",$Pass);
    } catch (PDOException $e) {
        print "Erreur de connexion PDO ";
        die();
    }
