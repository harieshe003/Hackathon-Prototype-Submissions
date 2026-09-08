<?php
// api/products/barcode.php

require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$code = sanitize($_GET['code'] ?? '');
if (empty($code)) {
    jsonResponse(['success' => false, 'error' => 'Barcode is required'], 400);
}

// Sample barcode lookup mock / Open Food Facts API query
$url = "https://world.openfoodfacts.org/api/v0/product/" . urlencode($code) . ".json";
$opts = [
    "http" => [
        "header" => "User-Agent: EcoLens/1.0\r\n",
        "timeout" => 4
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
];
$context = stream_context_create($opts);
$res = @file_get_contents($url, false, $context);

if ($res) {
    $json = json_decode($res, true);
    if (!empty($json['product'])) {
        $p = $json['product'];
        jsonResponse([
            'success' => true,
            'data' => [
                'name' => $p['product_name'] ?? $p['product_name_en'] ?? 'Barcode Item ' . $code,
                'brand' => $p['brands'] ?? 'Generic Brand',
                'material' => $p['packaging_text'] ?? $p['packaging'] ?? 'Recyclable Packaging'
            ]
        ]);
    }
}

// Fallback lookup response for demo barcodes
jsonResponse([
    'success' => true,
    'data' => [
        'name' => 'Eco Product (UPC ' . $code . ')',
        'brand' => 'Verified Brand',
        'material' => 'Eco-Friendly Composite'
    ]
]);
