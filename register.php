
<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $message = "Toate campurile sunt obligatorii!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresa de email nu este valida!";
    } elseif (strlen($password) < 8) {
        $message = "Parola trebuie sa aiba cel putin 8 caractere!";
    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "Acest email este deja folosit!";
        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);

            $message = "Cont creat cu succes!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Inregistrare - Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Creeaza un cont</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">

        <label>Nume:</label>
        <input type="text" name="name" required>

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Parola:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Inregistreaza-te</button>

    </form>

</body>
</html>
