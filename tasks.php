
<?php

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$message = "";

// Adaugare sarcina
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalida.");
    }
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $dueDate = $_POST["due_date"] ?? "";

    if ($title === "") {
        $message = "Titlul este obligatoriu!";
    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO tasks (user_id, title, description, due_date)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $userId,
            $title,
            $description,
            $dueDate !== "" ? $dueDate : null
        ]);

        $message = "Sarcina a fost adaugata!";
    }
}

// Preluare sarcini
$stmt = $pdo->prepare(
    "SELECT * FROM tasks
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->execute([$userId]);

$tasks = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Task Manager - Sarcinile mele</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Sarcinile mele</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <h2>Adauga o sarcina noua</h2>

    <form method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >
        <label>Titlu:</label><br>
        <input type="text" name="title" required>

        <br><br>

        <label>Descriere:</label><br>
        <textarea name="description"></textarea>

        <br><br>

        <label>Data limita:</label><br>
        <input type="date" name="due_date">

        <br><br>

        <button type="submit">Adauga sarcina</button>

    </form>

    <hr>

    <h2>Lista sarcinilor</h2>

    <?php if (count($tasks) > 0): ?>

        <?php foreach ($tasks as $task): ?>

            <div class="task">
                <h3>
                    <?= htmlspecialchars($task["title"]) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($task["description"] ?? "") ?>
                </p>
                <p class="status <?= htmlspecialchars($task['status']) ?>">
                    <?php
                    if ($task['status'] === 'todo') {
                        echo "De facut";
                    } elseif ($task['status'] === 'in_progress') {
                        echo "In lucru";
                    } else {
                        echo "Finalizata";
                    }
                    ?>
                </p>
                <p>
                    Status: <?= htmlspecialchars($task["status"]) ?>
                </p>
                <a href="edit_task.php?id=<?= (int)$task['id'] ?>">
                   Editeaza sarcina
                </a>
                <form action="delete_task.php" method="POST">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                    >
                    <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                    <button type="submit">Sterge sarcina</button>
                </form>
                <p>
                    Data limita:
                    <?= htmlspecialchars($task["due_date"] ?? "Nespecificata") ?>
                </p>

                <hr>
            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>Nu ai nicio sarcina momentan.</p>

    <?php endif; ?>

    <a href="dashboard.php">Inapoi la Dashboard</a>

</body>
</html>
