<?php
// classes/ScoringEngine.php

require_once __DIR__ . '/../config/scoring.config.php';

class ScoringEngine {

    public function computeFactorsFromRawProduct(array $p): array {
        $factors = [];

        // 1. Primary Material Impact (0-10)
        if (isset($p['material']) && trim($p['material']) !== '' && $p['material'] !== 'Unknown') {
            $mat = strtolower($p['material']);
            if (str_contains($mat, 'bamboo') || str_contains($mat, 'hemp') || str_contains($mat, 'organic cotton') || str_contains($mat, 'jute')) {
                $matScore = 9.5;
            } elseif (str_contains($mat, 'steel') || str_contains($mat, 'glass') || str_contains($mat, 'aluminum')) {
                $matScore = 8.2;
            } elseif (str_contains($mat, 'rpet') || str_contains($mat, 'recycled') || str_contains($mat, 'bioplastic')) {
                $matScore = 7.0;
            } elseif (str_contains($mat, 'plastic') || str_contains($mat, 'pvc') || str_contains($mat, 'polyester')) {
                $matScore = 2.5;
            } else {
                $matScore = 5.0;
            }
            $matStatus = isset($p['is_demo']) && $p['is_demo'] ? 'KNOWN' : 'ESTIMATED';
            $matSource = !empty($p['source_url']) ? 'Manufacturer Disclosure' : 'Material Specification';
        } else {
            $matScore = 5.0;
            $matStatus = 'UNKNOWN';
            $matSource = 'Not Provided';
        }
        $factors['material'] = [
            'key'    => 'material_score',
            'name'   => 'Primary Material Impact',
            'score'  => round($matScore, 1),
            'status' => $matStatus,
            'source' => $matSource
        ];

        // 2. Carbon Footprint (0-10)
        if (isset($p['carbon_footprint']) && $p['carbon_footprint'] !== '' && floatval($p['carbon_footprint']) >= 0) {
            $carb = floatval($p['carbon_footprint']);
            // Lower kg CO2e is better. 0.2kg -> 9.5, 2.0kg -> 6.0, 5.0kg+ -> 1.0
            $carbScore = max(1.0, min(10.0, 10.0 - ($carb * 1.8)));
            $carbStatus = 'KNOWN';
            $carbSource = 'LCA Carbon Metric';
        } else {
            $carbScore = 5.0;
            $carbStatus = 'UNKNOWN';
            $carbSource = 'Not Provided';
        }
        $factors['carbon'] = [
            'key'    => 'carbon_score',
            'name'   => 'Carbon Footprint',
            'score'  => round($carbScore, 1),
            'status' => $carbStatus,
            'source' => $carbSource
        ];

        // 3. Recyclability & Circularity (0-10)
        if (isset($p['recyclable'])) {
            $recycScore = !empty($p['recyclable']) ? 9.0 : 2.0;
            $recycStatus = 'KNOWN';
            $recycSource = 'Product Spec';
        } else {
            $recycScore = 5.0;
            $recycStatus = 'UNKNOWN';
            $recycSource = 'Not Provided';
        }
        $factors['recyclability'] = [
            'key'    => 'recyclability_score',
            'name'   => 'Recyclability & Circularity',
            'score'  => round($recycScore, 1),
            'status' => $recycStatus,
            'source' => $recycSource
        ];

        // 4. Reusability Rate (0-10)
        if (isset($p['reusable'])) {
            $reuseScore = !empty($p['reusable']) ? 9.5 : 1.5;
            $reuseStatus = 'KNOWN';
            $reuseSource = 'Design Architecture';
        } else {
            $reuseScore = 5.0;
            $reuseStatus = 'UNKNOWN';
            $reuseSource = 'Not Provided';
        }
        $factors['reusability'] = [
            'key'    => 'reusability_score',
            'name'   => 'Reusability Rate',
            'score'  => round($reuseScore, 1),
            'status' => $reuseStatus,
            'source' => $reuseSource
        ];

        // 5. Expected Lifespan (0-10)
        if (isset($p['lifespan']) && trim($p['lifespan']) !== '') {
            $ls = strtolower($p['lifespan']);
            if (str_contains($ls, '10+') || str_contains($ls, 'decade') || str_contains($ls, '15')) {
                $lsScore = 9.5;
            } elseif (str_contains($ls, '5+')) {
                $lsScore = 8.5;
            } elseif (str_contains($ls, '3+')) {
                $lsScore = 7.0;
            } elseif (str_contains($ls, '1 year') || str_contains($ls, 'single')) {
                $lsScore = 2.0;
            } else {
                $lsScore = 6.0;
            }
            $lsStatus = 'KNOWN';
            $lsSource = 'Durability Spec';
        } else {
            $lsScore = 5.0;
            $lsStatus = 'UNKNOWN';
            $lsSource = 'Not Provided';
        }
        $factors['lifespan'] = [
            'key'    => 'lifespan_score',
            'name'   => 'Expected Lifespan',
            'score'  => round($lsScore, 1),
            'status' => $lsStatus,
            'source' => $lsSource
        ];

        // 6. Packaging Eco-Friendliness (0-10)
        if (isset($p['packaging_material']) && trim($p['packaging_material']) !== '' && $p['packaging_material'] !== 'Unknown') {
            $pkg = strtolower($p['packaging_material']);
            if (str_contains($pkg, 'paper') || str_contains($pkg, 'cardboard') || str_contains($pkg, 'kraft') || str_contains($pkg, 'none')) {
                $pkgScore = 9.0;
            } elseif (str_contains($pkg, 'recycled plastic') || str_contains($pkg, 'cornstarch')) {
                $pkgScore = 7.0;
            } else {
                $pkgScore = 3.0; // single-use plastic bubble wrap
            }
            $pkgStatus = 'KNOWN';
            $pkgSource = 'Packaging Spec';
        } else {
            $pkgScore = 5.0;
            $pkgStatus = 'UNKNOWN';
            $pkgSource = 'Not Provided';
        }
        $factors['packaging'] = [
            'key'    => 'packaging_score',
            'name'   => 'Packaging Eco-Friendliness',
            'score'  => round($pkgScore, 1),
            'status' => $pkgStatus,
            'source' => $pkgSource
        ];

        // 7. Transportation / Freight Mileage (0-10)
        if (isset($p['transport_distance']) && $p['transport_distance'] !== '' && floatval($p['transport_distance']) >= 0) {
            $dist = floatval($p['transport_distance']);
            // 50km -> 9.5, 300km -> 7.0, 1000km+ -> 2.0
            $transScore = max(1.0, min(10.0, 10.0 - ($dist / 100.0)));
            $transStatus = 'KNOWN';
            $transSource = 'Logistics Distance';
        } else {
            $transScore = 5.0;
            $transStatus = 'UNKNOWN';
            $transSource = 'Not Provided';
        }
        $factors['transportation'] = [
            'key'    => 'transportation_score',
            'name'   => 'Transportation / Freight',
            'score'  => round($transScore, 1),
            'status' => $transStatus,
            'source' => $transSource
        ];

        // 8. Water Footprint (0-10)
        if (isset($p['water_usage']) && floatval($p['water_usage']) > 0) {
            $w = floatval($p['water_usage']);
            $waterScore = max(1.0, min(10.0, 10.0 - ($w / 10.0)));
            $waterStatus = 'KNOWN';
            $waterSource = 'LCA Water Metric';
        } else {
            // Estimated based on material
            $waterScore = isset($matScore) && $matScore > 7.0 ? 8.0 : 5.0;
            $waterStatus = 'ESTIMATED';
            $waterSource = 'Material Benchmark';
        }
        $factors['water'] = [
            'key'    => 'water_score',
            'name'   => 'Water Footprint',
            'score'  => round($waterScore, 1),
            'status' => $waterStatus,
            'source' => $waterSource
        ];

        // 9. Manufacturing Energy Efficiency (0-10)
        if (isset($p['energy_usage']) && floatval($p['energy_usage']) > 0) {
            $e = floatval($p['energy_usage']);
            $energyScore = max(1.0, min(10.0, 10.0 - ($e / 5.0)));
            $energyStatus = 'KNOWN';
            $energySource = 'LCA Energy Metric';
        } else {
            $energyScore = isset($matScore) && $matScore > 7.0 ? 7.5 : 5.0;
            $energyStatus = 'ESTIMATED';
            $energySource = 'Industry Benchmark';
        }
        $factors['energy'] = [
            'key'    => 'energy_score',
            'name'   => 'Manufacturing Energy Efficiency',
            'score'  => round($energyScore, 1),
            'status' => $energyStatus,
            'source' => $energySource
        ];

        return $factors;
    }

    public function calculateOverallScore(array $factors): array {
        $weights = SCORING_CONFIG['weights'];
        
        $weightedSum = 0.0;
        $totalWeight = 0.0;

        foreach ($weights as $factorKey => $weight) {
            $item = $factors[$factorKey] ?? null;
            $factorScore = is_array($item) ? floatval($item['score']) : floatval($item ?? 5.0);
            
            // Score scale is 0.0 - 10.0; multiplied by weight fraction (0.25, 0.15, etc.) and scaled x10 -> 0-100
            $weightedSum += ($factorScore * $weight * 10.0);
            $totalWeight += $weight;
        }

        $overallScore = round(min(100.0, max(0.0, $weightedSum)), 1);
        $classification = $this->getClassification($overallScore);
        $confidence = $this->calculateDataConfidence($factors);

        return [
            'score'          => $overallScore,
            'classification' => $classification['label'],
            'label'          => $classification['description'],
            'badge'          => $classification['badge'],
            'color'          => $classification['color'],
            'confidence'     => $confidence
        ];
    }

    public function getClassification(float $score): array {
        foreach (SCORING_CONFIG['classifications'] as $c) {
            if ($score >= $c['min'] && $score <= $c['max']) {
                $c['class'] = $c['badge'];
                return $c;
            }
        }
        $poor = SCORING_CONFIG['classifications']['poor'];
        $poor['class'] = $poor['badge'];
        return $poor;
    }

    public function calculateDataConfidence(array $factors): array {
        $knownCount = 0;
        $estimatedCount = 0;
        $unknownCount = 0;

        foreach ($factors as $f) {
            $status = is_array($f) ? ($f['status'] ?? 'UNKNOWN') : 'KNOWN';
            if ($status === 'KNOWN') {
                $knownCount++;
            } elseif ($status === 'ESTIMATED') {
                $estimatedCount++;
            } else {
                $unknownCount++;
            }
        }

        if ($knownCount >= 7) {
            $level = 'HIGH';
        } elseif ($knownCount >= 4) {
            $level = 'MEDIUM';
        } else {
            $level = 'LOW';
        }

        return [
            'level'            => $level,
            'knownFactors'     => $knownCount,
            'estimatedFactors' => $estimatedCount,
            'unknownFactors'   => $unknownCount,
            'totalFactors'     => count($factors)
        ];
    }
}
