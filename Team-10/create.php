<?php
// api/products/create.php

require_once __DIR__ . '/../../classes/ProductEngine.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Invalid request method'], 405);
}

$user = getCurrentUser();
$userId = $user['id'] ?? null;

$name = sanitize($_POST['name'] ?? '');
if (empty($name)) {
    jsonResponse(['success' => false, 'error' => 'Product name is required'], 400);
}

$data = [
    'name'                   => $name,
    'brand'                  => sanitize($_POST['brand'] ?? 'Generic'),
    'category'               => sanitize($_POST['category'] ?? 'Beverage Containers'),
    'description'            => sanitize($_POST['description'] ?? ''),
    'material'               => sanitize($_POST['material'] ?? 'Unknown'),
    'weight'                 => floatval($_POST['weight'] ?? 0.0),
    'manufacturing_location' => sanitize($_POST['manufacturing_location'] ?? 'Unknown'),
    'packaging_material'     => sanitize($_POST['packaging_material'] ?? 'Unknown'),
    'packaging_weight'       => floatval($_POST['packaging_weight'] ?? 0.0),
    'reusable'               => !empty($_POST['reusable']) ? 1 : 0,
    'lifespan'               => sanitize($_POST['lifespan'] ?? '1 year'),
    'recyclable'             => !empty($_POST['recyclable']) ? 1 : 0,
    'repairable'             => !empty($_POST['repairable']) ? 1 : 0,
    'energy_usage'           => floatval($_POST['energy_usage'] ?? 0.0),
    'water_usage'            => floatval($_POST['water_usage'] ?? 0.0),
    'transport_distance'     => floatval($_POST['transport_distance'] ?? 0.0),
    'carbon_footprint'       => floatval($_POST['carbon_footprint'] ?? 0.0),
    'end_of_life'            => sanitize($_POST['end_of_life'] ?? 'Landfill'),
    'image'                  => !empty($_POST['image']) && $_POST['image'] !== 'assets/images/placeholder.svg' ? sanitize($_POST['image']) : 'assets/images/steel-bottle.png',
    'source_url'             => sanitize($_POST['source_url'] ?? ''),
    'data_confidence'        => sanitize($_POST['data_confidence'] ?? 'Medium')
];

$engine = new ProductEngine();
$productId = $engine->createProduct($data, $userId);

$product = $engine->getProductById($productId);

jsonResponse(['success' => true, 'message' => 'Product created successfully', 'data' => $product]);
