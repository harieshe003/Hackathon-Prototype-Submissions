<?php
// classes/ComparisonEngine.php

require_once __DIR__ . '/ProductEngine.php';
require_once __DIR__ . '/LocationIntelligenceEngine.php';
require_once __DIR__ . '/EcoLensEngine.php';

class ComparisonEngine {
    private ProductEngine $productEngine;
    private LocationIntelligenceEngine $locationEngine;
    private EcoLensEngine $ecoLensEngine;

    public function __construct() {
        $this->productEngine = new ProductEngine();
        $this->locationEngine = new LocationIntelligenceEngine();
        $this->ecoLensEngine = new EcoLensEngine();
    }

    public function compareProducts(array $productIds, ?string $cityName = 'Chennai'): array {
        if (count($productIds) < 2 || count($productIds) > 5) {
            throw new Exception("Comparison requires between 2 and 5 products.");
        }

        $locationProfile = $this->locationEngine->getLocationProfile($cityName);
        $products = [];
        $highestContextualScore = -1.0;
        $winnerProduct = null;
        $winnerReason = "";

        foreach ($productIds as $id) {
            $analysis = $this->productEngine->analyzeProduct((int)$id, $locationProfile['city']);
            if ($analysis) {
                $products[] = $analysis;

                $contextScore = floatval($analysis['contextualEcoScore']['contextual_score']);
                if ($contextScore > $highestContextualScore) {
                    $highestContextualScore = $contextScore;
                    $winnerProduct = $analysis;
                }
            }
        }

        if ($winnerProduct) {
            $baseScore = $winnerProduct['baseEcoScore']['score'];
            $reasons = $winnerProduct['contextualEcoScore']['reasons'] ?? [];
            $reasonText = !empty($reasons) ? implode(' ', $reasons) : "Superior overall durability, reusability, and material circularity.";
            $winnerReason = "{$winnerProduct['product']['name']} leads with a contextual score of {$highestContextualScore}/100 in {$locationProfile['city']}. {$reasonText}";
        }

        return [
            'location'           => $locationProfile,
            'count'              => count($products),
            'products'           => $products,
            'winner'             => $winnerProduct ? [
                'id'               => $winnerProduct['product']['id'],
                'name'             => $winnerProduct['product']['name'],
                'brand'            => $winnerProduct['product']['brand'],
                'material'         => $winnerProduct['product']['material'] ?? 'Eco-Friendly Material',
                'lifespan'         => $winnerProduct['product']['lifespan'] ?? 'Multi-year',
                'image'            => $winnerProduct['product']['image'],
                'base_score'       => $winnerProduct['baseEcoScore']['score'],
                'contextual_score' => $winnerProduct['contextualEcoScore']['contextual_score'],
                'overall_score'    => $winnerProduct['contextualEcoScore']['contextual_score'],
                'rating'           => $winnerProduct['contextualEcoScore']['classification'],
                'rating_badge'     => ['class' => 'badge-excellent', 'color' => $winnerProduct['contextualEcoScore']['color']],
                'confidence'       => $winnerProduct['confidence']['level'],
                'factorScores'     => $winnerProduct['factorScores'],
                'reason'           => $winnerReason
            ] : null,
            'disclaimer'         => 'Comparison metrics are derived deterministically based on available product spec data.'
        ];
    }

    /**
     * Automatically find the direct non-eco-friendly conventional market counterpart for a given product.
     */
    public function getMarketCounterpartId(int $productId): int {
        $product = $this->productEngine->getProductById($productId);
        if (!$product) {
            return 1;
        }

        $allProducts = $this->productEngine->searchProducts('', '', null, 100);
        $matName = strtolower(($product['material'] ?? '') . ' ' . ($product['name'] ?? ''));
        $isEco = floatval($product['overall_score'] ?? 50) >= 60 || preg_match('/bamboo|glass|steel|bio|recycled|rpet|refill|organic/i', $matName);

        $fallbackId = null;

        foreach ($allProducts as $p) {
            if ($p['id'] == $productId) continue;

            $pScore = floatval($p['overall_score'] ?? 50);
            $pIsPoor = $pScore < 50 || strtolower($p['rating'] ?? '') === 'poor' || strtolower($p['rating'] ?? '') === 'low';

            if ($isEco && $pIsPoor) {
                // If same category match, return immediately
                if (!empty($p['category']) && !empty($product['category']) && $p['category'] === $product['category']) {
                    return (int)$p['id'];
                }
                if ($fallbackId === null) {
                    $fallbackId = (int)$p['id'];
                }
            } elseif (!$isEco && !$pIsPoor) {
                if (!empty($p['category']) && !empty($product['category']) && $p['category'] === $product['category']) {
                    return (int)$p['id'];
                }
                if ($fallbackId === null) {
                    $fallbackId = (int)$p['id'];
                }
            }
        }

        if ($fallbackId !== null) {
            return $fallbackId;
        }

        return ($productId === 1) ? 2 : 1;
    }
}
