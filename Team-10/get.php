<?php
// api/products/get.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    jsonResponse(['success' => false, 'error' => 'Valid product ID is required'], 400);
}

$engine = new ProductEngine();
$product = $engine->getProductById($id);

if (!$product) {
    jsonResponse(['success' => false, 'error' => 'Product not found'], 404);
}

jsonResponse(['success' => true, 'data' => $product]);
