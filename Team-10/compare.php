<?php
// api/products/compare.php

require_once __DIR__ . '/../../classes/ComparisonEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$idsParam = $_REQUEST['ids'] ?? '';
$city = sanitize($_REQUEST['city'] ?? 'Chennai');

if (is_array($idsParam)) {
    $ids = array_map('intval', $idsParam);
} else {
    $ids = array_filter(array_map('intval', explode(',', (string)$idsParam)));
}

if (count($ids) < 2 || count($ids) > 5) {
    jsonResponse(['success' => false, 'error' => 'Comparison requires between 2 and 5 product IDs.'], 400);
}

try {
    $engine = new ComparisonEngine();
    $result = $engine->compareProducts($ids, $city);
    jsonResponse([
        'success' => true,
        'data'    => $result
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
