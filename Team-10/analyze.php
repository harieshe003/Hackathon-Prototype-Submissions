<?php
// api/products/analyze.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$productId = intval($_REQUEST['id'] ?? 0);
$city = sanitize($_REQUEST['city'] ?? 'Chennai');

if ($productId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Valid product ID is required.'], 400);
}

$engine = new ProductEngine();
$analysis = $engine->analyzeProduct($productId, $city);

if (!$analysis) {
    jsonResponse(['success' => false, 'error' => 'Product not found.'], 404);
}

jsonResponse([
    'success' => true,
    'data'    => $analysis
]);
