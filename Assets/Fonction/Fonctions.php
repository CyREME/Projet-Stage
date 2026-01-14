<?php

function connectDb() {
  $protocole = "pgsql";
  
  $host = getenv('DB_HOST');
  $dbname = getenv('DB_NAME');
  $user = getenv('DB_USER');
  $password = getenv('DB_PASS');
  $port = "5432";

  if (!$host || !$dbname || !$user || !$password) {
    die("Erreur : Les variables d'environnement de la base de données ne sont pas définies.");
  }
  
  $dsn = $protocole . ":host=" . $host . ";port=" . $port . ";dbname=" . $dbname . ";user=" . $user . ";password=" . $password;
  
  try {
      $pdo = new PDO($dsn);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // On retire le "echo" de succès pour ne pas polluer l'affichage des autres pages
      return $pdo; 
  } catch (PDOException $e) {
      // En production, on évite d'afficher le message d'erreur précis à l'utilisateur
      die("Erreur de connexion à la base de données.");
  }
}



function getData(){
    $pdo = connectDb();
    $sql = 'SELECT * FROM "Cotrans" ORDER BY "Nom" ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}

?>