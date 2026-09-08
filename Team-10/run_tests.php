<?php
// tests/run_tests.php
// EcoLens Environmental Scoring Workflow Test Suite

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/LocationIntelligenceEngine.php';
require_once __DIR__ . '/../classes/ScoringEngine.php';
require_once __DIR__ . '/../classes/EcoLensEngine.php';
require_once __DIR__ . '/../classes/EcoSwapEngine.php';
require_once __DIR__ . '/../classes/ComparisonEngine.php';
require_once __DIR__ . '/../classes/ProductEngine.php';

class EcoLensTestSuite {
    private int $passed = 0;
    private int $failed = 0;

    private function assert($condition, string $message): void {
        if ($condition) {
            $this->passed++;
            echo "  \033[32m✓ PASS:\033[0m {$message}\n";
        } else {
            $this->failed++;
            echo "  \033[31m✗ FAIL:\033[0m {$message}\n";
        }
    }

    public function run(): void {
        echo "========================================================\n";
        echo "🧪 Running EcoLens Environmental Scoring Workflow Test Suite\n";
        echo "========================================================\n\n";

        $this->testLocationIntelligence();
        $this->testScoringEngine();
        $this->testMissingDataConfidence();
        $this->testEcoLensThinaiMapping();
        $this->testLocationAwareContextualScoring();
        $this->testDemoProductCases();
        $this->testComparisonEngine();
        $this->testEcoSwapEngine();

        echo "\n========================================================\n";
        echo "RESULTS: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "========================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function testLocationIntelligence(): void {
        echo "1. Testing Location Intelligence Engine...\n";
        $locEngine = new LocationIntelligenceEngine();

        // Chennai (Coastal)
        $chennai = $locEngine->getLocationProfile('Chennai');
        $this->assert($chennai['coastalProximity'] === 'HIGH', "Chennai profile has coastalProximity = HIGH");
        $this->assert($chennai['relevance']['neithal'] >= 80.0, "Chennai Neithal relevance is >= 80%");

        // Ooty (Mountain)
        $ooty = $locEngine->getLocationProfile('Ooty');
        $this->assert($ooty['elevation'] > 1000, "Ooty elevation is > 1000m");
        $this->assert($ooty['relevance']['kurinji'] >= 80.0, "Ooty Kurinji relevance is >= 80%");

        // Thanjavur (Agricultural)
        $thanjavur = $locEngine->getLocationProfile('Thanjavur');
        $this->assert($thanjavur['agriculturalContext'] === 'HIGH', "Thanjavur agriculturalContext = HIGH");
        $this->assert($thanjavur['relevance']['marutham'] >= 80.0, "Thanjavur Marutham relevance is >= 80%");
    }

    private function testScoringEngine(): void {
        echo "\n2. Testing Scoring Engine & 9 Factor Derivation...\n";
        $scoringEngine = new ScoringEngine();

        $sampleProduct = [
            'name'               => 'Test Steel Flask',
            'material'           => '18/8 Stainless Steel',
            'carbon_footprint'   => 1.5,
            'recyclable'         => 1,
            'reusable'           => 1,
            'lifespan'           => '10+ years',
            'packaging_material' => 'Recycled Cardboard',
            'transport_distance' => 150
        ];

        $factors = $scoringEngine->computeFactorsFromRawProduct($sampleProduct);
        $this->assert(count($factors) === 9, "9 environmental factors generated");
        $this->assert($factors['material']['score'] >= 8.0, "Steel material score is >= 8.0");
        $this->assert($factors['reusability']['score'] === 9.5, "Reusable product reusability score is 9.5");

        $baseScore = $scoringEngine->calculateOverallScore($factors);
        $this->assert($baseScore['score'] >= 80.0, "High sustainability product gets Base Eco Score >= 80.0");
        $this->assert($baseScore['classification'] === 'Exceptional', "Classification is Exceptional for score >= 80");
    }

    private function testMissingDataConfidence(): void {
        echo "\n3. Testing Missing Data & Confidence Handling...\n";
        $scoringEngine = new ScoringEngine();

        $sparseProduct = [
            'name' => 'Sparse Product'
            // No material, carbon, recyclable, reusable, lifespan, packaging, or transport provided
        ];

        $factors = $scoringEngine->computeFactorsFromRawProduct($sparseProduct);
        $this->assert($factors['material']['status'] === 'UNKNOWN', "Missing material status is UNKNOWN");
        $this->assert($factors['carbon']['status'] === 'UNKNOWN', "Missing carbon status is UNKNOWN");

        $baseScore = $scoringEngine->calculateOverallScore($factors);
        $this->assert($baseScore['confidence']['level'] === 'LOW', "Sparse product yields LOW data confidence");
        $this->assert($baseScore['confidence']['unknownFactors'] >= 5, "Missing parameters recorded as unknownFactors");
    }

    private function testEcoLensThinaiMapping(): void {
        echo "\n4. Testing EcoLens 5 Thinai Ecological Lenses...\n";
        $scoringEngine = new ScoringEngine();
        $ecoLensEngine = new EcoLensEngine();

        $sampleProduct = [
            'material' => '18/8 Stainless Steel',
            'reusable' => 1,
            'recyclable' => 1,
            'packaging_material' => 'Recycled Cardboard',
            'carbon_footprint' => 1.0
        ];

        $factors = $scoringEngine->computeFactorsFromRawProduct($sampleProduct);
        $thinai = $ecoLensEngine->calculateThinaiScores($factors);

        $this->assert(isset($thinai['kurinji']), "Kurinji lens calculated");
        $this->assert(isset($thinai['mullai']), "Mullai lens calculated");
        $this->assert(isset($thinai['marutham']), "Marutham lens calculated");
        $this->assert(isset($thinai['neithal']), "Neithal lens calculated");
        $this->assert(isset($thinai['palai']), "Palai lens calculated");
        $this->assert($thinai['neithal']['score'] >= 75.0, "High packaging score yields strong Neithal score");
    }

    private function testLocationAwareContextualScoring(): void {
        echo "\n5. Testing Location-Aware Contextual Eco Scoring...\n";
        $scoringEngine = new ScoringEngine();
        $ecoLensEngine = new EcoLensEngine();
        $locEngine = new LocationIntelligenceEngine();

        // Single-use plastic item in Chennai (Coastal HIGH)
        $plasticItem = [
            'material' => 'Plastic',
            'packaging_material' => 'Single-use Plastic Film',
            'recyclable' => 0,
            'reusable' => 0
        ];

        $factors = $scoringEngine->computeFactorsFromRawProduct($plasticItem);
        $baseScore = $scoringEngine->calculateOverallScore($factors);
        $chennai = $locEngine->getLocationProfile('Chennai');

        $contextual = $ecoLensEngine->calculateContextualScore($baseScore['score'], $factors, $chennai);
        $this->assert($contextual['contextual_score'] < $baseScore['score'], "Single-use plastic penalized in coastal Chennai (-4 pts)");
        $this->assert(count($contextual['reasons']) > 0, "Contextual adjustment reasons generated");
    }

    private function testDemoProductCases(): void {
        echo "\n6. Testing Required Demo Product Cases...\n";
        $prodEngine = new ProductEngine();

        // 1. Plastic Bottle
        $p1 = $prodEngine->analyzeProduct(1, 'Chennai');
        $this->assert($p1 !== null, "Demo Plastic Bottle analyzed");
        $this->assert($p1['contextualEcoScore']['contextual_score'] < 50.0, "Plastic bottle score is < 50 (Poor)");

        // 2. Stainless Steel Bottle
        $p2 = $prodEngine->analyzeProduct(2, 'Chennai');
        $this->assert($p2 !== null, "Demo Stainless Steel Bottle analyzed");
        $this->assert($p2['baseEcoScore']['score'] >= 80.0, "Steel bottle base score >= 80 (Exceptional)");

        // 3. Bamboo Toothbrush / Tumbler
        $p5 = $prodEngine->analyzeProduct(5, 'Chennai');
        $this->assert($p5 !== null, "Demo Bamboo Composite Tumbler analyzed");
        $this->assert($p5['factorScores']['material']['score'] >= 8.5, "Bamboo material score >= 8.5");
    }

    private function testComparisonEngine(): void {
        echo "\n7. Testing Product Comparison Engine...\n";
        $compEngine = new ComparisonEngine();

        $comp = $compEngine->compareProducts([1, 2], 'Chennai');
        $this->assert($comp['count'] === 2, "Comparison matrix contains 2 products");
        $this->assert($comp['winner']['id'] === 2, "Stainless steel bottle wins over plastic bottle");
        $this->assert(!empty($comp['winner']['reason']), "Winner explanation rationale included");
    }

    private function testEcoSwapEngine(): void {
        echo "\n8. Testing EcoSwap Recommendation Engine...\n";
        $prodEngine = new ProductEngine();
        $swapEngine = new EcoSwapEngine();

        $p1Analysis = $prodEngine->analyzeProduct(1, 'Chennai');
        $swaps = $swapEngine->recommendSwap($p1Analysis['product'], $p1Analysis['factorScores'], 2);

        $this->assert(!empty($swaps['recommendations']), "EcoSwap recommendations generated for plastic bottle");
        $this->assert($swaps['recommendations'][0]['improvement_delta'] > 30.0, "Improvement delta is > +30 points");
        $this->assert($swaps['recommendations'][0]['disclaimer'] === 'Based on the available environmental data.', "Environmental disclaimer included");
    }
}

$testRunner = new EcoLensTestSuite();
$testRunner->run();
