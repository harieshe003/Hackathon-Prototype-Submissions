<?php
// api/products/search.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$q = sanitize($_GET['q'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$minScore = isset($_GET['min_score']) ? floatval($_GET['min_score']) : null;
$limit = intval($_GET['limit'] ?? 20);

$engine = new ProductEngine();
$products = $engine->searchProducts($q, $category, $minScore, $limit);

jsonResponse(['success' => true, 'count' => count($products), 'data' => $products]);
