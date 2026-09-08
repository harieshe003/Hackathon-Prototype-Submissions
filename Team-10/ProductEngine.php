<?php
// classes/ProductEngine.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ScoringEngine.php';
require_once __DIR__ . '/EcoLensEngine.php';
require_once __DIR__ . '/LocationIntelligenceEngine.php';
require_once __DIR__ . '/EcoSwapEngine.php';

class ProductEngine {
    private PDO $db;
    private ScoringEngine $scoringEngine;
    private EcoLensEngine $ecoLensEngine;
    private LocationIntelligenceEngine $locationEngine;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->scoringEngine = new ScoringEngine();
        $this->ecoLensEngine = new EcoLensEngine();
        $this->locationEngine = new LocationIntelligenceEngine();
    }

    public function validateRawProduct(array $data): array {
        $errors = [];

        if (empty($data['name']) || trim($data['name']) === '') {
            $errors[] = "Product Name is required.";
        }

        if (isset($data['weight']) && floatval($data['weight']) < 0) {
            $errors[] = "Product Weight cannot be negative.";
        }

        if (isset($data['packaging_weight']) && floatval($data['packaging_weight']) < 0) {
            $errors[] = "Packaging Weight cannot be negative.";
        }

        if (isset($data['transport_distance']) && floatval($data['transport_distance']) < 0) {
            $errors[] = "Transport Distance cannot be negative.";
        }

        if (isset($data['carbon_footprint']) && floatval($data['carbon_footprint']) < 0) {
            $errors[] = "Carbon Footprint cannot be negative.";
        }

        return $errors;
    }

    public function getProductById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if (!$prod) {
            return null;
        }
        return $prod;
    }

    public function analyzeProduct(int $id, ?string $cityName = 'Chennai'): ?array {
        $prod = $this->getProductById($id);
        if (!$prod) {
            return null;
        }

        // 1. Resolve Location Intelligence Profile
        $locationProfile = $this->locationEngine->getLocationProfile($cityName);

        // 2. Compute 9 Environmental Factors with status (KNOWN / ESTIMATED / UNKNOWN)
        $factorScores = $this->scoringEngine->computeFactorsFromRawProduct($prod);

        // 3. Compute Deterministic Base Eco Score & Classification
        $baseEcoScore = $this->scoringEngine->calculateOverallScore($factorScores);

        // 4. Compute 5 Thinai Ecological Lens Scores
        $thinaiScores = $this->ecoLensEngine->calculateThinaiScores($factorScores);

        // 5. Compute Location-Aware Contextual Score
        $contextualEcoScore = $this->ecoLensEngine->calculateContextualScore($baseEcoScore['score'], $factorScores, $locationProfile);

        // 6. Scientific Safety Label Determination
        if (!empty($prod['is_demo'])) {
            $safetyLabel = "Demo Data — Illustrative Score";
        } elseif (!empty($prod['is_verified'])) {
            $safetyLabel = "Third-Party Data";
        } else {
            $safetyLabel = "Estimated";
        }

        // Format legacy factors flat array for backward compatibility
        $legacyFactors = [
            'carbon_score'         => $factorScores['carbon']['score'],
            'material_score'       => $factorScores['material']['score'],
            'recyclability_score'  => $factorScores['recyclability']['score'],
            'reusability_score'    => $factorScores['reusability']['score'],
            'lifespan_score'       => $factorScores['lifespan']['score'],
            'packaging_score'      => $factorScores['packaging']['score'],
            'transportation_score' => $factorScores['transportation']['score'],
            'water_score'          => $factorScores['water']['score'],
            'energy_score'         => $factorScores['energy']['score']
        ];

        return [
            'product'              => $prod,
            'id'                   => $prod['id'],
            'name'                 => $prod['name'],
            'brand'                => $prod['brand'],
            'category'             => $prod['category'],
            'material'             => $prod['material'],
            'image'                => $prod['image'],
            'overall_score'        => $contextualEcoScore['contextual_score'],
            'rating'               => $contextualEcoScore['classification'],
            'rating_badge'         => ['label' => $contextualEcoScore['classification'], 'class' => 'badge-good', 'color' => $contextualEcoScore['color']],
            'safetyLabel'          => $safetyLabel,
            'location'             => $locationProfile,
            'environmentalProfile' => [
                'city'                 => $locationProfile['city'],
                'coastalProximity'     => $locationProfile['coastalProximity'],
                'waterStress'          => $locationProfile['waterStress'],
                'forestContext'        => $locationProfile['forestContext'],
                'agriculturalContext'  => $locationProfile['agriculturalContext'],
                'elevation'            => $locationProfile['elevation']
            ],
            'factorScores'         => $factorScores,
            'factors'              => $legacyFactors,
            'baseEcoScore'         => $baseEcoScore,
            'thinaiScores'         => $thinaiScores,
            'thinai'               => $thinaiScores,
            'scores'               => [
                'overall_score'    => $contextualEcoScore['contextual_score'],
                'base_score'       => $baseEcoScore['score'],
                'rating'           => $contextualEcoScore['classification']
            ],
            'contextualEcoScore'   => $contextualEcoScore,
            'confidence'           => $baseEcoScore['confidence']
        ];
    }

    public function searchProducts(string $query = '', string $category = '', ?float $minScore = null, int $limit = 20): array {
        $sql = "
            SELECT p.*, s.overall_score, s.rating
            FROM products p
            LEFT JOIN ecolens_scores s ON p.id = s.product_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.material LIKE ? OR p.category LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($category)) {
            $sql .= " AND p.category = ?";
            $params[] = $category;
        }

        if ($minScore !== null) {
            $sql .= " AND s.overall_score >= ?";
            $params[] = $minScore;
        }

        $sql .= " ORDER BY s.overall_score DESC LIMIT " . intval($limit);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        foreach ($products as &$p) {
            $score = floatval($p['overall_score'] ?? 50.0);
            $p['rating_badge'] = $this->scoringEngine->getClassification($score);
        }

        return $products;
    }

    public function createProduct(array $data, ?int $userId = null): int {
        $validationErrors = $this->validateRawProduct($data);
        if (!empty($validationErrors)) {
            throw new Exception(implode(' ', $validationErrors));
        }

        $stmt = $this->db->prepare("
            INSERT INTO products (
                name, brand, category, description, material, weight, 
                manufacturing_location, packaging_material, packaging_weight, 
                reusable, lifespan, recyclable, repairable, energy_usage, 
                water_usage, transport_distance, carbon_footprint, end_of_life, 
                image, source_url, data_confidence, created_by, is_demo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['brand'] ?? 'Generic',
            $data['category'] ?? 'Beverage Containers',
            $data['description'] ?? '',
            $data['material'] ?? 'Unknown',
            floatval($data['weight'] ?? 0.0),
            $data['manufacturing_location'] ?? 'Unknown',
            $data['packaging_material'] ?? 'Unknown',
            floatval($data['packaging_weight'] ?? 0.0),
            !empty($data['reusable']) ? 1 : 0,
            $data['lifespan'] ?? '1 year',
            !empty($data['recyclable']) ? 1 : 0,
            !empty($data['repairable']) ? 1 : 0,
            floatval($data['energy_usage'] ?? 0.0),
            floatval($data['water_usage'] ?? 0.0),
            floatval($data['transport_distance'] ?? 0.0),
            floatval($data['carbon_footprint'] ?? 0.0),
            $data['end_of_life'] ?? 'Landfill',
            $data['image'] ?? 'assets/images/placeholder.svg',
            $data['source_url'] ?? '',
            $data['data_confidence'] ?? 'Medium',
            $userId,
            !empty($data['is_demo']) ? 1 : 0
        ]);

        $productId = (int)$this->db->lastInsertId();

        // Calculate factors & scores
        $factorScores = $this->scoringEngine->computeFactorsFromRawProduct($data);
        $baseScore = $this->scoringEngine->calculateOverallScore($factorScores);
        $thinai = $this->ecoLensEngine->calculateThinaiScores($factorScores);

        // Store environmental factors
        $stmtFact = $this->db->prepare("
            INSERT INTO environmental_factors (
                product_id, carbon_score, material_score, recyclability_score, 
                reusability_score, lifespan_score, packaging_score, 
                transportation_score, water_score, energy_score, manufacturing_score, 
                evidence, confidence
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtFact->execute([
            $productId,
            $factorScores['carbon']['score'],
            $factorScores['material']['score'],
            $factorScores['recyclability']['score'],
            $factorScores['reusability']['score'],
            $factorScores['lifespan']['score'],
            $factorScores['packaging']['score'],
            $factorScores['transportation']['score'],
            $factorScores['water']['score'],
            $factorScores['energy']['score'],
            7.0,
            'Automated factor synthesis',
            $baseScore['confidence']['level']
        ]);

        // Store EcoLens Scores
        $stmtScore = $this->db->prepare("
            INSERT INTO ecolens_scores (
                product_id, overall_score, rating, kurinji_score, 
                mullai_score, marutham_score, neithal_score, palai_score
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtScore->execute([
            $productId,
            $baseScore['score'],
            $baseScore['classification'],
            $thinai['kurinji']['score'],
            $thinai['mullai']['score'],
            $thinai['marutham']['score'],
            $thinai['neithal']['score'],
            $thinai['palai']['score']
        ]);

        return $productId;
    }
}
