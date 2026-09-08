<?php
// classes/LocationIntelligenceEngine.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/location.config.php';

class LocationIntelligenceEngine {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getLocationProfile(?string $cityName = 'Chennai', ?float $lat = null, ?float $lng = null): array {
        // 1. If lat/lng provided, reverse geocode to resolve city name if missing
        if ($lat !== null && $lng !== null && (empty($cityName) || $cityName === 'Detected Region')) {
            $geoCity = $this->reverseGeocode($lat, $lng);
            if ($geoCity) {
                $cityName = $geoCity;
            }
        }

        $cityKey = strtolower(trim($cityName ?? 'chennai'));

        // 2. Check centralized pre-configured profiles (e.g., Chennai, Ooty, Thanjavur, Madurai)
        if (isset(LOCATION_PROFILES[$cityKey])) {
            return LOCATION_PROFILES[$cityKey];
        }

        // 3. Query Database table 'locations'
        if (!empty($cityName)) {
            $stmt = $this->db->prepare("SELECT * FROM locations WHERE LOWER(city) = LOWER(?) LIMIT 1");
            $stmt->execute([$cityName]);
            $loc = $stmt->fetch();
            if ($loc) {
                return $this->formatDatabaseProfile($loc);
            }
        }

        // 4. Dynamic Rule-Based Profiler for any unlisted city or GPS coordinates
        return $this->buildDynamicProfile($cityName ?: 'Selected Location', $lat ?: 13.0827, $lng ?: 80.2707);
    }

    private function buildDynamicProfile(string $city, float $lat, float $lng): array {
        // Dynamic environmental feature resolution
        $isCoastal = (abs($lat) < 35 && (str_contains(strtolower($city), 'port') || str_contains(strtolower($city), 'coast') || str_contains(strtolower($city), 'beach') || abs($lng - 80.0) < 1.0));
        $coastalProximity = $isCoastal ? 'HIGH' : 'MEDIUM';

        $elevation = (str_contains(strtolower($city), 'hill') || str_contains(strtolower($city), 'mountain') || str_contains(strtolower($city), 'peak') || str_contains(strtolower($city), 'ooty')) ? 1500.0 : 100.0;
        $forestContext = ($elevation > 1000.0) ? 'HIGH' : 'LOW';

        $waterStress = ($lat > 8.0 && $lat < 30.0 && !$isCoastal) ? 'HIGH' : 'MEDIUM';
        $aridity = ($waterStress === 'HIGH') ? 'HIGH' : 'LOW';

        $agriContext = (!str_contains(strtolower($city), 'metro') && !str_contains(strtolower($city), 'city')) ? 'HIGH' : 'MEDIUM';

        // Calculate Thinai relevance percentages (0-100)
        $neithalRel  = ($coastalProximity === 'HIGH') ? 90.0 : 35.0;
        $kurinjiRel  = ($elevation > 1000.0) ? 90.0 : 25.0;
        $mullaiRel   = ($forestContext === 'HIGH') ? 85.0 : 40.0;
        $maruthamRel = ($agriContext === 'HIGH') ? 85.0 : 45.0;
        $palaiRel    = ($waterStress === 'HIGH') ? 85.0 : 30.0;

        return [
            'city'                   => $city,
            'state'                  => 'Regional Context',
            'country'                => 'India',
            'latitude'               => $lat,
            'longitude'              => $lng,
            'elevation'              => $elevation,
            'coastalProximity'       => $coastalProximity,
            'waterStress'            => $waterStress,
            'forestContext'          => $forestContext,
            'agriculturalContext'    => $agriContext,
            'aridity'                => $aridity,
            'urbanization'           => 'MEDIUM',
            'biodiversitySensitivity'=> ($forestContext === 'HIGH' || $coastalProximity === 'HIGH') ? 'HIGH' : 'MEDIUM',
            'environmentalSensitivity' => 'MEDIUM',
            'relevance' => [
                'neithal'  => $neithalRel,
                'palai'    => $palaiRel,
                'marutham' => $maruthamRel,
                'mullai'   => $mullaiRel,
                'kurinji'  => $kurinjiRel
            ]
        ];
    }

    private function formatDatabaseProfile(array $loc): array {
        return [
            'city'                   => $loc['city'],
            'state'                  => $loc['state'],
            'country'                => $loc['country'],
            'latitude'               => floatval($loc['latitude']),
            'longitude'              => floatval($loc['longitude']),
            'elevation'              => floatval($loc['elevation'] ?? 50.0),
            'coastalProximity'       => $loc['coastal_proximity'] ?? 'MEDIUM',
            'waterStress'            => $loc['water_stress'] ?? 'MEDIUM',
            'forestContext'          => $loc['forest_context'] ?? 'MEDIUM',
            'agriculturalContext'    => $loc['agricultural_context'] ?? 'MEDIUM',
            'aridity'                => $loc['aridity'] ?? 'LOW',
            'urbanization'           => $loc['urbanization'] ?? 'HIGH',
            'biodiversitySensitivity'=> 'MEDIUM',
            'environmentalSensitivity' => 'MEDIUM',
            'relevance' => [
                'neithal'  => floatval($loc['neithal_relevance'] ?? 50.0),
                'palai'    => floatval($loc['palai_relevance'] ?? 50.0),
                'marutham' => floatval($loc['marutham_relevance'] ?? 50.0),
                'mullai'   => floatval($loc['mullai_relevance'] ?? 50.0),
                'kurinji'  => floatval($loc['kurinji_relevance'] ?? 50.0)
            ]
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
}
