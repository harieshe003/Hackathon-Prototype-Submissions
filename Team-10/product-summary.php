<?php
// api/ai/product-summary.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../classes/AIEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$productId = intval($_REQUEST['id'] ?? 0);
$city = sanitize($_REQUEST['city'] ?? 'Chennai');

if ($productId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Valid product ID is required.'], 400);
}

$prodEngine = new ProductEngine();
$analysis = $prodEngine->analyzeProduct($productId, $city);

if (!$analysis) {
    jsonResponse(['success' => false, 'error' => 'Product not found.'], 404);
}

$aiEngine = new AIEngine();
$summary = $aiEngine->generateProductSummary($analysis);

jsonResponse([
    'success' => true,
    'data'    => $summary
]);
