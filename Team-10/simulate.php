<?php
// api/business/simulate.php

require_once __DIR__ . '/../../classes/ScoringEngine.php';
require_once __DIR__ . '/../../classes/EcoLensEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$factors = [
    'carbon_score'         => floatval($_POST['carbon_score'] ?? 5.0),
    'material_score'       => floatval($_POST['material_score'] ?? 5.0),
    'recyclability_score'  => floatval($_POST['recyclability_score'] ?? 5.0),
    'reusability_score'    => floatval($_POST['reusability_score'] ?? 5.0),
    'lifespan_score'       => floatval($_POST['lifespan_score'] ?? 5.0),
    'packaging_score'      => floatval($_POST['packaging_score'] ?? 5.0),
    'transportation_score' => floatval($_POST['transportation_score'] ?? 5.0),
    'water_score'          => floatval($_POST['water_score'] ?? 5.0),
    'energy_score'         => floatval($_POST['energy_score'] ?? 5.0)
];

$scoringEngine = new ScoringEngine();
$simulatedScore = $scoringEngine->calculateOverallScore($factors);

$ecoLensEngine = new EcoLensEngine();
$thinai = $ecoLensEngine->calculateThinaiScores($factors);

jsonResponse([
    'success' => true,
    'simulated_score' => $simulatedScore['overall_score'],
    'rating' => $simulatedScore['rating'],
    'thinai' => $thinai
]);
