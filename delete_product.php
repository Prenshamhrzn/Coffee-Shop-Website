<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'admin_panel');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

header("Location: product.php");
exit;
?>
