<?php
// api/products/fetch_url.php

require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$url = filter_var($_GET['url'] ?? '', FILTER_VALIDATE_URL);
if (!$url) {
    jsonResponse(['success' => false, 'error' => 'Valid URL is required'], 400);
}

// 1. Extract raw text from URL path slug (handles Amazon/e-commerce links blocked by anti-bot Captchas)
$path = parse_url($url, PHP_URL_PATH) ?? '';
$pathSegments = explode('/', trim($path, '/'));
$slug = '';
foreach ($pathSegments as $seg) {
    if (strlen($seg) > 5 && !preg_match('/^(dp|product|p|ref|sr_)/i', $seg)) {
        $slug = urldecode($seg);
        break;
    }
}

// 2. Fetch page HTML metadata with browser User-Agent
$opts = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
        "timeout" => 4
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
];
$context = stream_context_create($opts);
$html = @file_get_contents($url, false, $context) ?: '';

$title = '';
if (!empty($html)) {
    if (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\' border]+)["\']/i', $html, $m)) {
        $title = $m[1];
    } elseif (preg_match('/<title>(.*?)<\/title>/i', $html, $m)) {
        $title = $m[1];
    }
}

if (empty($title) || str_contains(strtolower($title), 'robot') || str_contains(strtolower($title), 'captcha') || str_contains(strtolower($title), 'amazon.in')) {
    if (!empty($slug)) {
        $title = ucwords(str_replace(['-', '_', '+'], ' ', $slug));
    } else {
        $title = "Extracted Product (" . parse_url($url, PHP_URL_HOST) . ")";
    }
}

// Clean title string
$titleClean = trim(preg_replace('/[\:\-\|\,].*$/', '', strip_tags($title)));
if (empty($titleClean)) {
    $titleClean = "Extracted Product";
}

// 3. Extract Brand from title/slug
$brand = 'Generic Brand';
$parts = explode(' ', $titleClean);
if (!empty($parts[0]) && strlen($parts[0]) > 2) {
    $brand = $parts[0];
}

// Combine title, slug, and html for deep keyword detection
$combinedText = strtolower($titleClean . ' ' . $slug . ' ' . substr($html, 0, 5000));

// 4. Intelligence Keyword Classification (Evaluate ECO first, then PLASTIC)
$isEco = preg_match('/eco|organic|plant|bamboo|beco|cleaner|biodegradable|natural|refill|herbal|recycled|hemp|jute|compost/i', $combinedText);
$isPlastic = preg_match('/plastic|pet|pvc|polyester|acrylic|nylon|polyethylene|container|Tupperware/i', $combinedText) && !$isEco;

if ($isEco) {
    $category = str_contains($combinedText, 'cleaner') ? 'Personal Care' : 'Home & Kitchen';
    $material = 'Plant-Based Bio-Degradable Formula';
    $packagingMaterial = 'Recycled FSC Cardboard Box / Refill Pack';
    $reusable = 1;
    $recyclable = 1;
    $repairable = 1;
    $lifespan = 'Multi-year Refillable';
    $carbon = 0.50;
    $endOfLife = 'Compostable / Recycled Packaging Stream';
} elseif ($isPlastic) {
    $category = 'Home & Kitchen';
    $material = 'Virgin PET Plastic';
    $packagingMaterial = 'Single-use Plastic Film / Bubble Wrap';
    $reusable = 0;
    $recyclable = 0;
    $repairable = 0;
    $lifespan = 'Single-use / Short-life';
    $carbon = 3.20;
    $endOfLife = 'Landfill / Microplastic Pollution';
} else {
    $category = 'Home & Kitchen';
    $material = 'Standard Commercial Spec';
    $packagingMaterial = 'Paperboard Packaging';
    $reusable = 1;
    $recyclable = 1;
    $repairable = 0;
    $lifespan = '2+ years';
    $carbon = 1.50;
    $endOfLife = 'Standard Recycling Stream';
}

$img = 'assets/images/steel-bottle.png';
if ($isEco) {
    if (str_contains($combinedText, 'cleaner') || str_contains($combinedText, 'spray')) {
        $img = 'assets/images/kitchen-cleaner.png';
    } elseif (str_contains($combinedText, 'dish') || str_contains($combinedText, 'sponge')) {
        $img = 'assets/images/eco-dishwash.png';
    } else {
        $img = 'assets/images/bamboo-bottle.png';
    }
} elseif ($isPlastic) {
    if (str_contains($combinedText, 'bottle') || str_contains($combinedText, 'water')) {
        $img = 'assets/images/plastic-bottle.png';
    } else {
        $img = 'assets/images/plastic-container.png';
    }
}

jsonResponse([
    'success' => true,
    'data' => [
        'name'               => $titleClean,
        'brand'              => $brand,
        'category'           => $category,
        'material'           => $material,
        'packaging_material' => $packagingMaterial,
        'reusable'           => $reusable,
        'recyclable'         => $recyclable,
        'repairable'         => $repairable,
        'lifespan'           => $lifespan,
        'carbon_footprint'   => $carbon,
        'end_of_life'        => $endOfLife,
        'image'              => $img,
        'source_url'         => $url
    ]
]);
