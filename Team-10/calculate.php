<?php
// api/scoring/calculate.php

require_once __DIR__ . '/../../classes/ScoringEngine.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: $_POST;

$engine = new ScoringEngine();
$factorScores = $engine->computeFactorsFromRawProduct($data);
$baseScore = $engine->calculateOverallScore($factorScores);

jsonResponse([
    'success' => true,
    'data'    => [
        'factorScores' => $factorScores,
        'baseEcoScore' => $baseScore
    ]
]);
