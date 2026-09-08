<?php
// api/auth/register.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Invalid request method'], 405);
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = sanitize($_POST['role'] ?? 'user'); // 'user' or 'business'
$city = sanitize($_POST['city'] ?? 'Chennai');

if (empty($name) || empty($email) || empty($password)) {
    jsonResponse(['success' => false, 'error' => 'Name, email, and password are required'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'Invalid email address format'], 400);
}

$db = Database::getInstance();
$stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Email address is already registered'], 409);
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$allowedRoles = ['user', 'business'];
if (!in_array($role, $allowedRoles)) {
    $role = 'user';
}

$stmtIns = $db->prepare("INSERT INTO users (name, email, password, role, location_city) VALUES (?, ?, ?, ?, ?)");
$stmtIns->execute([$name, $email, $hashedPassword, $role, $city]);
$userId = (int)$db->lastInsertId();

if ($role === 'business') {
    $companyName = sanitize($_POST['company_name'] ?? $name . ' Enterprises');
    $stmtBiz = $db->prepare("INSERT INTO businesses (user_id, company_name, contact_email) VALUES (?, ?, ?)");
    $stmtBiz->execute([$userId, $companyName, $email]);
}

$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = $role;
$_SESSION['user_location'] = $city;

jsonResponse([
    'success' => true,
    'message' => 'Account created successfully',
    'user' => [
        'id' => $userId,
        'name' => $name,
        'email' => $email,
        'role' => $role
    ]
]);
