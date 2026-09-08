<?php
// classes/LocationEngine.php

require_once __DIR__ . '/../config/database.php';

class LocationEngine {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getLocationProfile(?string $cityName = 'Chennai', ?float $lat = null, ?float $lng = null): array {
        if ($lat !== null && $lng !== null) {
            // Reverse geocode via OpenStreetMap Nominatim API
            $geoCity = $this->reverseGeocode($lat, $lng);
            if ($geoCity) {
                $cityName = $geoCity;
            }
        }

        if ($cityName) {
            $stmt = $this->db->prepare("SELECT * FROM locations WHERE LOWER(city) = LOWER(?) LIMIT 1");
            $stmt->execute([$cityName]);
            $loc = $stmt->fetch();
            if ($loc) {
                return $this->formatProfile($loc);
            }
        }

        // Dynamically compute ecosystem profile for newly detected coordinates
        $coastal = ($lat && abs($lat) < 30) ? 'HIGH' : 'MEDIUM';
        $waterStress = ($lat && $lat > 10 && $lat < 35) ? 'HIGH' : 'MEDIUM';

        return [
            'city'                 => $cityName ?: 'Chennai',
            'state'                => 'Detected Region',
            'country'              => 'India',
            'latitude'             => $lat ?: 13.0827,
            'longitude'            => $lng ?: 80.2707,
            'coastal_proximity'    => $coastal,
            'water_stress'         => $waterStress,
            'urbanization'         => 'HIGH',
            'forest_context'       => 'MEDIUM',
            'agricultural_context' => 'MEDIUM',
            'relevance' => [
                'neithal'  => ($coastal === 'HIGH') ? 90.0 : 50.0,
                'palai'    => ($waterStress === 'HIGH') ? 80.0 : 40.0,
                'marutham' => 45.0,
                'mullai'   => 40.0,
                'kurinji'  => 30.0
            ],
            'summary' => "{$cityName} contextual profile resolved via GPS: Coastal ({$coastal}), Water Stress ({$waterStress})."
        ];
    }

    private function reverseGeocode(float $lat, float $lng): ?string {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=10";
        $opts = [
            "http" => [
                "header" => "User-Agent: EcoLensApp/1.0\r\n",
                "timeout" => 3
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ];
        $context = stream_context_create($opts);
        $res = @file_get_contents($url, false, $context);
        if ($res) {
            $json = json_decode($res, true);
            $addr = $json['address'] ?? [];
            return $addr['city'] ?? $addr['town'] ?? $addr['suburb'] ?? $addr['county'] ?? $addr['state'] ?? null;
        }
        return null;
    }

    private function formatProfile(array $loc): array {
        return [
            'city'                 => $loc['city'],
            'state'                => $loc['state'],
            'country'              => $loc['country'],
            'latitude'             => floatval($loc['latitude']),
            'longitude'            => floatval($loc['longitude']),
            'coastal_proximity'    => $loc['coastal_proximity'],
            'water_stress'         => $loc['water_stress'],
            'urbanization'         => $loc['urbanization'],
            'forest_context'       => $loc['forest_context'],
            'agricultural_context' => $loc['agricultural_context'],
            'relevance' => [
                'neithal'  => floatval($loc['neithal_relevance']),
                'palai'    => floatval($loc['palai_relevance']),
                'marutham' => floatval($loc['marutham_relevance']),
                'mullai'   => floatval($loc['mullai_relevance']),
                'kurinji'  => floatval($loc['kurinji_relevance'])
            ],
            'summary' => "{$loc['city']} contextual profile: Coastal ({$loc['coastal_proximity']}), Water Stress ({$loc['water_stress']}), Urbanization ({$loc['urbanization']})."
        ];
    }

    public function calculateContextualScore(float $globalScore, array $factors, array $locationProfile): array {
        $neithalRel = $locationProfile['relevance']['neithal'] ?? 50.0;
        $palaiRel   = $locationProfile['relevance']['palai'] ?? 50.0;
        $pkgScore   = floatval($factors['packaging_score'] ?? 5.0);
        $waterScore = floatval($factors['water_score'] ?? 5.0);

        // Adjust score within controlled range (-5 to +5) based on local environmental factors
        $adjustment = 0.0;
        $reasons = [];

        if ($neithalRel > 70.0 && $pkgScore < 5.0) {
            $adjustment -= 4.0;
            $reasons[] = "High coastal marine vulnerability in {$locationProfile['city']} penalizes non-recyclable packaging waste (-4 pts).";
        } elseif ($neithalRel > 70.0 && $pkgScore >= 8.0) {
            $adjustment += 2.0;
            $reasons[] = "Eco-friendly packaging provides strong coastal protection in {$locationProfile['city']} (+2 pts).";
        }

        if ($palaiRel > 70.0 && $waterScore < 5.0) {
            $adjustment -= 3.0;
            $reasons[] = "Severe water stress in {$locationProfile['city']} penalizes high water-consumption manufacturing (-3 pts).";
        }

        $contextualScore = round(min(100.0, max(0.0, $globalScore + $adjustment)), 2);

        return [
            'global_score' => $globalScore,
            'contextual_score' => $contextualScore,
            'adjustment' => $adjustment,
            'location_name' => $locationProfile['city'],
            'reasons' => $reasons
        ];
    }
}
