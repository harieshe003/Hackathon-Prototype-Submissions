<?php
// classes/RecommendationEngine.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ProductEngine.php';

class RecommendationEngine {
    private PDO $db;
    private ProductEngine $productEngine;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->productEngine = new ProductEngine();
    }

    public function getRecommendations(int $productId, int $limit = 4): array {
        $current = $this->productEngine->getProductById($productId);
        if (!$current) {
            return [];
        }

        $currentScore = $current['scores']['overall_score'];

        // Find products in same or related category with higher overall score
        $stmt = $this->db->prepare("
            SELECT p.id, s.overall_score
            FROM products p
            JOIN ecolens_scores s ON p.id = s.product_id
            WHERE p.id != ? 
              AND (p.category = ? OR p.category LIKE '%Container%' OR p.category LIKE '%Bottle%')
              AND s.overall_score > ?
            ORDER BY s.overall_score DESC
            LIMIT ?
        ");
        $stmt->execute([$productId, $current['category'], $currentScore, $limit]);
        $rows = $stmt->fetchAll();

        $recommendations = [];
        foreach ($rows as $row) {
            $alt = $this->productEngine->getProductById($row['id']);
            if ($alt) {
                $diff = round($alt['scores']['overall_score'] - $currentScore, 1);
                $recommendations[] = [
                    'original' => [
                        'id' => $current['id'],
                        'name' => $current['name'],
                        'score' => $currentScore
                    ],
                    'alternative' => $alt,
                    'score_improvement' => $diff,
                    'reason' => "Upgrading to {$alt['name']} provides a +{$diff} Eco Score improvement by eliminating single-use waste."
                ];
            }
        }

        return $recommendations;
    }
}
