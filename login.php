
<?php

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT id, name, email, password
         FROM users
         WHERE email = ?"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: dashboard.php");
        exit;

    } else {
        $message = "Email sau parola incorecta!";
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Autentificare</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">

        <label>Email:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Parola:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Login</button>

    </form>

    <p>
        Nu ai cont?
        <a href="register.php">Inregistreaza-te</a>
    </p>

</body>
</html>
