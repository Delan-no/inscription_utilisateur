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
    $login = htmlspecialchars($_POST['login']); // Peut être un email ou un nom d'utilisateur
    $password = $_POST['password'];

    // Recherche de l'utilisateur dans la base de données
    $sql = "SELECT * FROM users WHERE email = :login OR username = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification de l'utilisateur et du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        session_start(); // Démarrage de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirection vers la page d'accueil ou une autre page sécurisée
        header("Location: dashboard.php");
        exit();
    } else {
        // Connexion échouée
        echo "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
