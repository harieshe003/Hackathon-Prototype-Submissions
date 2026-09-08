<?php
// classes/EcoLensEngine.php

require_once __DIR__ . '/../config/thinai.config.php';
require_once __DIR__ . '/../config/location.config.php';

class EcoLensEngine {

    public function calculateThinaiScores(array $factors): array {
        $thinaiScores = [];

        foreach (THINAI_FACTOR_MAPPING as $key => $config) {
            $sum = 0.0;
            $totalWeight = 0.0;

            foreach ($config['weights'] as $factorName => $w) {
                $item = $factors[$factorName] ?? null;
                $score = is_array($item) ? floatval($item['score']) : floatval($item ?? 5.0);
                $sum += ($score * $w * 10.0);
                $totalWeight += $w;
            }

            $finalScore = round(min(100.0, max(0.0, $sum)), 1);

            $thinaiScores[$key] = [
                'name'        => $config['name'],
                'label'       => $config['label'],
                'icon'        => $config['icon'],
                'score'       => $finalScore,
                'description' => $config['description']
            ];
        }

        return $thinaiScores;
    }

    public function calculateContextualScore(float $baseEcoScore, array $factors, array $locationProfile): array {
        $reasons = [];
        $adjustment = 0.0;

        $cityName = $locationProfile['city'] ?? 'Selected Location';
        $coastal = $locationProfile['coastalProximity'] ?? 'MEDIUM';
        $waterStress = $locationProfile['waterStress'] ?? 'MEDIUM';
        $elevation = floatval($locationProfile['elevation'] ?? 50.0);

        // Extract factor scores (0.0 - 10.0 scale)
        $pkgScore   = is_array($factors['packaging'] ?? null) ? floatval($factors['packaging']['score']) : floatval($factors['packaging_score'] ?? 5.0);
        $waterScore = is_array($factors['water'] ?? null) ? floatval($factors['water']['score']) : floatval($factors['water_score'] ?? 5.0);
        $matScore   = is_array($factors['material'] ?? null) ? floatval($factors['material']['score']) : floatval($factors['material_score'] ?? 5.0);

        // Rule 1: Coastal Packaging impact (Neithal priority)
        if ($coastal === 'HIGH' && $pkgScore < 5.0) {
            $adjustment -= 4.0;
            $reasons[] = "High coastal marine vulnerability in {$cityName} penalizes non-recyclable packaging waste (-4.0 pts).";
        } elseif ($coastal === 'HIGH' && $pkgScore >= 8.0) {
            $adjustment += 2.0;
            $reasons[] = "Eco-friendly packaging provides strong coastal protection in {$cityName} (+2.0 pts).";
        }

        // Rule 2: Water Stress impact (Palai priority)
        if ($waterStress === 'HIGH' && $waterScore < 5.0) {
            $adjustment -= 4.0;
            $reasons[] = "Severe dryland water stress in {$cityName} penalizes high water-consumption manufacturing (-4.0 pts).";
        } elseif ($waterStress === 'HIGH' && $waterScore >= 8.0) {
            $adjustment += 2.0;
            $reasons[] = "Low water footprint conserves critical water reserves in {$cityName} (+2.0 pts).";
        }

        // Rule 3: Mountain ecosystem impact (Kurinji priority)
        if ($elevation > 1000.0 && $matScore < 5.0) {
            $adjustment -= 3.0;
            $reasons[] = "Fragile mountain ecosystem in {$cityName} penalizes intensive raw material extraction (-3.0 pts).";
        }

        // Clamp adjustment within [-8.0, +5.0] bounds
        $adjustment = max(-8.0, min(5.0, $adjustment));
        $contextualScore = round(min(100.0, max(0.0, $baseEcoScore + $adjustment)), 1);

        $scoringEngine = new ScoringEngine();
        $classification = $scoringEngine->getClassification($contextualScore);

        $explanation = "Your base product score is {$baseEcoScore}/100. The contextual score is {$contextualScore}/100 because local environmental factors in {$cityName} adjust the relevance of packaging, water, and material impacts.";

        return [
            'base_score'       => $baseEcoScore,
            'contextual_score' => $contextualScore,
            'adjustment'       => $adjustment,
            'location_name'    => $cityName,
            'classification'   => $classification['label'],
            'label'            => $classification['description'],
            'color'            => $classification['color'],
            'reasons'          => $reasons,
            'explanation'      => $explanation
        ];
    }
}
