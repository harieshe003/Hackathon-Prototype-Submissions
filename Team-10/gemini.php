<?php
// config/gemini.php

require_once __DIR__ . '/config.php';

class GeminiClient {
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->apiKey = getenv('GEMINI_API_KEY') ?: '';
        $this->model = getenv('GEMINI_MODEL') ?: 'gemini-1.5-flash';
    }

    public function generateProductExplanation(array $productData, array $factors, array $scores, string $locationName = 'Chennai'): array {
        $prompt = $this->buildPrompt($productData, $factors, $scores, $locationName);

        if (!empty($this->apiKey)) {
            $response = $this->callGeminiApi($prompt);
            if ($response !== null) {
                return $response;
            }
        }

        // Fallback deterministic AI synthesis when API key is missing or call encounters limit/network error
        return $this->generateFallbackExplanation($productData, $factors, $scores, $locationName);
    }

    private function buildPrompt(array $p, array $f, array $s, string $loc): string {
        $carbonScore = $f['carbon_score'] ?? $f['carbon']['score'] ?? 5.0;
        $matScore    = $f['material_score'] ?? $f['material']['score'] ?? 5.0;
        $recycScore  = $f['recyclability_score'] ?? $f['recyclability']['score'] ?? 5.0;
        $reuseScore  = $f['reusability_score'] ?? $f['reusability']['score'] ?? 5.0;
        $lifeScore   = $f['lifespan_score'] ?? $f['lifespan']['score'] ?? 5.0;
        $pkgScore    = $f['packaging_score'] ?? $f['packaging']['score'] ?? 5.0;
        $transScore  = $f['transportation_score'] ?? $f['transportation']['score'] ?? 5.0;
        $waterScore  = $f['water_score'] ?? $f['water']['score'] ?? 5.0;
        $energyScore = $f['energy_score'] ?? $f['energy']['score'] ?? 5.0;

        $overallScore = $s['overall_score'] ?? 50.0;
        $rating       = $s['rating'] ?? 'Moderate';
        $kurinji      = $s['kurinji_score'] ?? $s['kurinji']['score'] ?? 50.0;
        $mullai       = $s['mullai_score'] ?? $s['mullai']['score'] ?? 50.0;
        $marutham     = $s['marutham_score'] ?? $s['marutham']['score'] ?? 50.0;
        $neithal      = $s['neithal_score'] ?? $s['neithal']['score'] ?? 50.0;
        $palai        = $s['palai_score'] ?? $s['palai']['score'] ?? 50.0;

        return "You are EcoLens AI, an expert environmental life cycle assessment assistant.
Analyze the following product environmental data objectively without inventing unverified facts.

Product: {$p['name']}
Brand: {$p['brand']}
Category: {$p['category']}
Material: {$p['material']} (Weight: {$p['weight']}g)
Packaging: {$p['packaging_material']}
Lifespan: {$p['lifespan']} (Reusable: " . (!empty($p['reusable']) ? 'Yes' : 'No') . ", Recyclable: " . (!empty($p['recyclable']) ? 'Yes' : 'No') . ")
Location Context: {$loc}

Environmental Factor Scores (0-10):
- Carbon Footprint: {$carbonScore}/10
- Material Intensity: {$matScore}/10
- Recyclability: {$recycScore}/10
- Reusability: {$reuseScore}/10
- Lifespan & Durability: {$lifeScore}/10
- Packaging Eco Score: {$pkgScore}/10
- Transport Distance: {$transScore}/10
- Water Usage: {$waterScore}/10
- Energy Usage: {$energyScore}/10

Calculated Eco Score: {$overallScore}/100 ({$rating})
EcoLens 5 Thinai Impact (0-100):
- Kurinji (Mountain/Mining): {$kurinji}
- Mullai (Forest/Bio): {$mullai}
- Marutham (Agri/Soil): {$marutham}
- Neithal (Coastal/Marine): {$neithal}
- Palai (Resource/Water Stress): {$palai}

Format your output as valid JSON with exact keys:
{
  \"summary\": \"Brief 2-3 sentence overview of why this product received this score\",
  \"strengths\": [\"Strength 1\", \"Strength 2\"],
  \"weaknesses\": [\"Weakness 1\", \"Weakness 2\"],
  \"location_relevance\": \"Explanation of location impact in {$loc}\",
  \"improvements\": [\"Actionable improvement 1\", \"Actionable improvement 2\"],
  \"better_alternatives_hint\": \"Insight on what type of greener material or design could be a better swap\"
}";
    }

    private function callGeminiApi(string $prompt): ?array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . urlencode($this->apiKey);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $json = json_decode($response, true);
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text) {
                $parsed = json_decode($text, true);
                if (is_array($parsed)) {
                    return $parsed;
                }
            }
        }
        return null;
    }

    private function generateFallbackExplanation(array $p, array $f, array $s, string $loc): array {
        $strengths = [];
        $weaknesses = [];

        if ($f['reusability_score'] >= 7.5) $strengths[] = "High reusability eliminates recurring single-use waste production.";
        if ($f['lifespan_score'] >= 7.5) $strengths[] = "Durable design with extended service lifespan ({$p['lifespan']}).";
        if ($f['recyclability_score'] >= 7.5) $strengths[] = "High end-of-life recyclability stream compatibility.";
        if ($f['material_score'] >= 7.5) $strengths[] = "Low-toxicity material selection ({$p['material']}).";

        if (empty($strengths)) {
            $strengths[] = "Sufficient material stability for basic utility.";
        }

        if ($f['carbon_score'] < 6.0) $weaknesses[] = "Elevated carbon footprint across raw material processing and transport.";
        if ($f['packaging_score'] < 6.0) $weaknesses[] = "High packaging impact from non-compostable packaging materials ({$p['packaging_material']}).";
        if ($f['reusability_score'] < 5.0) $weaknesses[] = "Single-use or short lifecycle design driving rapid municipal waste load.";
        if ($f['transportation_score'] < 6.0) $weaknesses[] = "Long freight transport distance ({$p['transport_distance']} km) increasing shipping emissions.";

        if (empty($weaknesses)) {
            $weaknesses[] = "Higher upfront manufacturing energy requirements.";
        }

        $summary = "{$p['name']} achieved an overall Eco Score of {$s['overall_score']}/100 ({$s['rating']}). The score is heavily influenced by its " .
                   (($p['reusable']) ? "multi-use reusable design and " : "single-use lifecycle profile and ") .
                   "material footprint ({$p['material']}).";

        $locRel = "In {$loc}, marine pollution (Neithal) and water scarcity (Palai) are critical factors. " .
                 (($p['recyclable']) ? "This product aligns well with urban recovery facilities." : "This product poses elevated land/coastal accumulation risks if unmanaged.");

        $improvements = [
            "Transition to 100% recycled or bio-based packaging materials.",
            "Optimize transport routes to decrease supply chain fuel consumption."
        ];

        return [
            'summary' => $summary,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'location_relevance' => $locRel,
            'improvements' => $improvements,
            'better_alternatives_hint' => "Products engineered with high-grade stainless steel, borosilicate glass, or organic compostable bamboo offer significant score increases."
        ];
    }
}
