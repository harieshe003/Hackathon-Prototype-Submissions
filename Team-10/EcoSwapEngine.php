<?php
// classes/EcoSwapEngine.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ScoringEngine.php';

class EcoSwapEngine {
    private PDO $db;
    private ScoringEngine $scoringEngine;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->scoringEngine = new ScoringEngine();
    }

    public function recommendSwap(array $product, array $factorScores, int $limit = 3): array {
        // 1. Identify weak factors (< 6.0 score)
        $weakFactors = [];
        foreach ($factorScores as $key => $f) {
            $score = is_array($f) ? floatval($f['score']) : floatval($f);
            if ($score < 6.0) {
                $name = is_array($f) ? ($f['name'] ?? $key) : $key;
                $weakFactors[] = [
                    'key'   => $key,
                    'name'  => $name,
                    'score' => $score
                ];
            }
        }

        $currentScore = floatval($product['scores']['overall_score'] ?? $product['overall_score'] ?? 50.0);
        $category = $product['category'] ?? '';

        // 2. Query database for higher scoring alternatives in same category
        $stmt = $this->db->prepare("
            SELECT p.*, s.overall_score, s.rating
            FROM products p
            JOIN ecolens_scores s ON p.id = s.product_id
            WHERE p.id != ? AND s.overall_score > ?
            ORDER BY s.overall_score DESC
            LIMIT ?
        ");
        $stmt->execute([$product['id'] ?? 0, $currentScore, $limit * 2]);
        $candidates = $stmt->fetchAll();

        // If no products in exact category, fetch top overall eco products
        if (empty($candidates)) {
            $stmt = $this->db->prepare("
                SELECT p.*, s.overall_score, s.rating
                FROM products p
                JOIN ecolens_scores s ON p.id = s.product_id
                WHERE p.id != ? AND s.overall_score > ?
                ORDER BY s.overall_score DESC
                LIMIT ?
            ");
            $stmt->execute([$product['id'] ?? 0, $currentScore, $limit]);
            $candidates = $stmt->fetchAll();
        }

        $recommendations = [];
        foreach (array_slice($candidates, 0, $limit) as $cand) {
            $candScore = floatval($cand['overall_score']);
            $delta = round($candScore - $currentScore, 1);

            $reasons = [];
            if (!empty($cand['reusable']) && empty($product['reusable'])) {
                $reasons[] = "Switches from single-use to reusable architecture (+reusability score).";
            }
            if (!empty($cand['recyclable']) && empty($product['recyclable'])) {
                $reasons[] = "High post-consumer recyclability stream.";
            }
            if (!empty($cand['packaging_material']) && str_contains(strtolower($cand['packaging_material']), 'cardboard')) {
                $reasons[] = "Replaces plastic packaging with FSC recycled cardboard.";
            }
            if (empty($reasons)) {
                $reasons[] = "Higher overall eco-efficiency across material sourcing and carbon footprint.";
            }

            $recommendations[] = [
                'alternative'       => [
                    'id'            => $cand['id'],
                    'name'          => $cand['name'],
                    'brand'         => $cand['brand'],
                    'category'      => $cand['category'],
                    'material'      => $cand['material'],
                    'image'         => $cand['image'],
                    'overall_score' => $candScore,
                    'rating'        => $cand['rating']
                ],
                'current_score'     => $currentScore,
                'alternative_score' => $candScore,
                'score_improvement' => "+" . $delta . " points",
                'improvement_delta' => $delta,
                'reasons'           => $reasons,
                'disclaimer'        => 'Based on the available environmental data.'
            ];
        }

        return [
            'product_id'      => $product['id'] ?? 0,
            'product_name'    => $product['name'] ?? 'Product',
            'current_score'   => $currentScore,
            'weak_factors'    => $weakFactors,
            'recommendations' => $recommendations
        ];
    }
}
