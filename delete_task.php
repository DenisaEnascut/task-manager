<?php
session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        die("Cerere invalida.");
    }

    $task_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($task_id) {
        $stmt = $pdo->prepare(
            "DELETE FROM tasks WHERE id = :id AND user_id = :user_id"
        );

        $stmt->execute([
            "id" => $task_id,
            "user_id" => $_SESSION["user_id"]
        ]);
    }
}

header("Location: tasks.php");
exit;