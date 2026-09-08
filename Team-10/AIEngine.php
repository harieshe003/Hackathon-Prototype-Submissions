<?php
// classes/AIEngine.php

require_once __DIR__ . '/../config/gemini.php';

class AIEngine {

    public function generateProductSummary(array $analysis): array {
        $productData = $analysis['product'] ?? [];
        $prodName = $productData['name'] ?? 'Product';
        $baseScore = $analysis['baseEcoScore']['score'] ?? 50.0;
        $contextScore = $analysis['contextualEcoScore']['contextual_score'] ?? $baseScore;
        $location = $analysis['location']['city'] ?? 'Chennai';
        $classification = $analysis['baseEcoScore']['classification'] ?? 'Moderate';

        // Extract strengths & weaknesses from 9 factor scores
        $strengths = [];
        $weaknesses = [];
        foreach ($analysis['factorScores'] as $key => $f) {
            $score = floatval($f['score']);
            $name = $f['name'] ?? $key;
            if ($score >= 7.5) {
                $strengths[] = "High {$name} ({$score}/10)";
            } elseif ($score < 5.5) {
                $weaknesses[] = "Improvement needed in {$name} ({$score}/10)";
            }
        }

        // Find dominant Thinai lens
        $maxThinaiScore = -1.0;
        $dominantThinai = 'Neithal';
        foreach ($analysis['thinaiScores'] as $key => $t) {
            if ($t['score'] > $maxThinaiScore) {
                $maxThinaiScore = $t['score'];
                $dominantThinai = $t['name'] . " (" . $t['label'] . ")";
            }
        }

        $contextReasons = implode(' ', $analysis['contextualEcoScore']['reasons'] ?? []);
        if (empty($contextReasons)) {
            $contextReasons = "Local environmental priorities in {$location} align well with this product's lifecycle footprint.";
        }

        // Use GeminiClient
        try {
            $client = new GeminiClient();
            $geminiRes = $client->generateProductExplanation(
                $productData,
                $analysis['factors'] ?? [],
                $analysis['scores'] ?? ['overall_score' => $baseScore, 'rating' => $classification, 'kurinji_score' => 50, 'mullai_score' => 50, 'marutham_score' => 50, 'neithal_score' => 50, 'palai_score' => 50],
                $location
            );

            if (!empty($geminiRes['summary'])) {
                return [
                    'product_name'       => $prodName,
                    'base_score'         => $baseScore,
                    'contextual_score'   => $contextScore,
                    'location'           => $location,
                    'summary'            => $geminiRes['summary'],
                    'strengths'          => !empty($geminiRes['strengths']) ? $geminiRes['strengths'] : $strengths,
                    'weaknesses'         => !empty($geminiRes['weaknesses']) ? $geminiRes['weaknesses'] : $weaknesses,
                    'dominant_thinai'    => $dominantThinai,
                    'disclaimer'         => 'AI interpretations are strictly generated from deterministic calculated engine scores.'
                ];
            }
        } catch (Exception $e) {
            // Fallback handled below
        }

        $summaryText = "Your product '{$prodName}' scored {$baseScore}/100 on its base environmental profile, classified as {$classification}.\n\n";
        $summaryText .= "Key Strengths: " . (!empty($strengths) ? implode(', ', $strengths) : "Balanced basic environmental metrics") . ".\n";
        $summaryText .= "Key Weaknesses: " . (!empty($weaknesses) ? implode(', ', $weaknesses) : "No critical low-scoring factors identified") . ".\n\n";
        $summaryText .= "Dominant Ecological Lens: {$dominantThinai}.\n";
        $summaryText .= "Location Context ({$location}): Contextual score is {$contextScore}/100. {$contextReasons}\n\n";
        $summaryText .= "Recommendation: Switching to circular packaging or sourcing local raw materials could further optimize the contextual score.";

        return [
            'product_name'       => $prodName,
            'base_score'         => $baseScore,
            'contextual_score'   => $contextScore,
            'location'           => $location,
            'summary'            => $summaryText,
            'strengths'          => $strengths,
            'weaknesses'         => $weaknesses,
            'dominant_thinai'    => $dominantThinai,
            'disclaimer'         => 'AI interpretations are strictly generated from deterministic calculated engine scores.'
        ];
    }
}
