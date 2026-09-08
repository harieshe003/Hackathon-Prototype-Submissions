<?php
// includes/auth.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function getCurrentUser(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    
    static $user = null;
    if ($user === null) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name, email, role, location_city, location_country FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function isLoggedIn(): bool {
    return getCurrentUser() !== null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect(APP_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireBusiness(): void {
    $user = getCurrentUser();
    if (!$user || ($user['role'] !== 'business' && $user['role'] !== 'admin')) {
        redirect(APP_URL . '/login.php?msg=business_required');
    }
}

function requireAdmin(): void {
    $user = getCurrentUser();
    if (!$user || $user['role'] !== 'admin') {
        redirect(APP_URL . '/login.php?msg=admin_required');
    }
}
