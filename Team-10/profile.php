<?php
// api/location/profile.php

require_once __DIR__ . '/../../classes/LocationIntelligenceEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$cityName = sanitize($_REQUEST['city'] ?? 'Chennai');
$lat = isset($_REQUEST['latitude']) ? floatval($_REQUEST['latitude']) : null;
$lng = isset($_REQUEST['longitude']) ? floatval($_REQUEST['longitude']) : null;

$engine = new LocationIntelligenceEngine();
$profile = $engine->getLocationProfile($cityName, $lat, $lng);

jsonResponse([
    'success' => true,
    'data'    => $profile
]);
