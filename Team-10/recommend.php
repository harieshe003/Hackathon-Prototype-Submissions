<?php
// api/ecoswap/recommend.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../classes/EcoSwapEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$productId = intval($_REQUEST['product_id'] ?? 0);
$city = sanitize($_REQUEST['city'] ?? 'Chennai');

if ($productId <= 0) {
    jsonResponse(['success' => false, 'error' => 'Valid product ID is required.'], 400);
}

$prodEngine = new ProductEngine();
$analysis = $prodEngine->analyzeProduct($productId, $city);

if (!$analysis) {
    jsonResponse(['success' => false, 'error' => 'Product not found.'], 404);
}

$swapEngine = new EcoSwapEngine();
$result = $swapEngine->recommendSwap($analysis['product'], $analysis['factorScores']);

jsonResponse([
    'success' => true,
    'data'    => $result
]);
