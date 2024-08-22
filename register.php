<?php
// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'testdb';
$username = 'root';
$password = '';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);

    // Vérification de l'existence de l'utilisateur
    $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $username]);
    
    // Si un utilisateur existe déjà
    if ($stmt->rowCount() > 0) {
        echo "Cet utilisateur existe déjà dans notre base de données, veuillez en choisir un autre. \n Merci!";
    } else {
        // Si l'utilisateur n'existe pas, on peut insérer les données
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe

        // Préparation de la requête d'insertion
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);

        // Exécution de la requête avec les valeurs des champs du formulaire
        if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $password])) {
            echo "Inscription réussie !";
        } else {
            echo "Une erreur est survenue lors de l'inscription.";
        }
    }
}
?>
