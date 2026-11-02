
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <a href="logout.php">Logout</a>
</head>
<body>

    <h1>
        Bun venit, <?= htmlspecialchars($_SESSION["user_name"]) ?>!
    </h1>

    <p>Ai intrat cu succes in aplicatia Task Manager.</p>

    <a href="logout.php">Deconectare</a>
    <p>
    <a href="tasks.php">Gestioneaza sarcinile mele</a>
</p>

</body>
</html>
