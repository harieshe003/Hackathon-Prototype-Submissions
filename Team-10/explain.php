<?php
// api/ai/explain.php

require_once __DIR__ . '/../../classes/AIEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$productId = intval($_GET['product_id'] ?? $_POST['product_id'] ?? 0);
$location = sanitize($_GET['location'] ?? $_SESSION['user_location'] ?? 'Chennai');

if ($productId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Valid product_id is required'], 400);
}

$aiEngine = new AIEngine();
$explanation = $aiEngine->getExplanation($productId, $location);

if (isset($explanation['error'])) {
    jsonResponse(['success' => false, 'error' => $explanation['error']], 404);
}

jsonResponse(['success' => true, 'data' => $explanation]);
