
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
$taskId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$taskId) {
    die("ID invalid!");
}

// Preluam sarcina
$stmt = $pdo->prepare(
    "SELECT * FROM tasks WHERE id = ? AND user_id = ?"
);
$stmt->execute([$taskId, $userId]);

$task = $stmt->fetch();

if (!$task) {
    http_response_code(404);
    die("Sarcina nu a fost gasita!");
}

$message = "";

// Actualizam sarcina
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalida.");
    }
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = $_POST["status"] ?? "";
    $dueDate = $_POST["due_date"] ?? "";

    $allowedStatuses = [
        "todo",
        "in_progress",
        "completed"
    ];

    if ($title === "") {
        $message = "Titlul este obligatoriu!";
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $message = "Status invalid!";
    } else {

        $stmt = $pdo->prepare(
            "UPDATE tasks
             SET title = ?, description = ?, status = ?, due_date = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([
            $title,
            $description,
            $status,
            $dueDate !== "" ? $dueDate : null,
            $taskId,
            $userId
        ]);

        header("Location: tasks.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editeaza sarcina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Editeaza sarcina</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >
        <label>Titlu:</label><br>
        <input
            type="text"
            name="title"
            value="<?= htmlspecialchars($task["title"]) ?>"
            required
        >

        <br><br>

        <label>Descriere:</label><br>
        <textarea name="description"><?= htmlspecialchars($task["description"] ?? "") ?></textarea>

        <br><br>

        <label>Status:</label><br>

        <select name="status">
            <option value="todo" <?= $task["status"] === "todo" ? "selected" : "" ?>>
                De facut
            </option>

            <option value="in_progress" <?= $task["status"] === "in_progress" ? "selected" : "" ?>>
                In desfasurare
            </option>

            <option value="completed" <?= $task["status"] === "completed" ? "selected" : "" ?>>
                Finalizata
            </option>
        </select>

        <br><br>

        <label>Data limita:</label><br>

        <input
            type="date"
            name="due_date"
            value="<?= htmlspecialchars($task["due_date"] ?? "") ?>"
        >

        <br><br>

        <button type="submit">Salveaza modificarile</button>

    </form>

    <p><a href="tasks.php">Inapoi la sarcini</a></p>

</body>
</html>
