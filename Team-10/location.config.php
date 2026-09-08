<?php
// config/location.config.php
// Centralized Location Profiling & Environmental Sensitivity Configuration

define('LOCATION_PROFILES', [
    'chennai' => [
        'city'                   => 'Chennai',
        'state'                  => 'Tamil Nadu',
        'country'                => 'India',
        'latitude'               => 13.0827,
        'longitude'              => 80.2707,
        'elevation'              => 6.0, // meters
        'coastalProximity'       => 'HIGH',
        'waterStress'            => 'MEDIUM',
        'forestContext'          => 'LOW',
        'agriculturalContext'    => 'LOW',
        'aridity'                => 'LOW',
        'urbanization'           => 'HIGH',
        'biodiversitySensitivity'=> 'MEDIUM',
        'environmentalSensitivity' => 'HIGH',
        'relevance' => [
            'neithal'  => 95.0, // Coastal/Marine high relevance
            'palai'    => 55.0, // Water stress medium
            'marutham' => 30.0,
            'mullai'   => 25.0,
            'kurinji'  => 20.0
        ]
    ],
    'ooty' => [
        'city'                   => 'Ooty',
        'state'                  => 'Tamil Nadu',
        'country'                => 'India',
        'latitude'               => 11.4102,
        'longitude'              => 76.6950,
        'elevation'              => 2240.0, // meters
        'coastalProximity'       => 'LOW',
        'waterStress'            => 'LOW',
        'forestContext'          => 'HIGH',
        'agriculturalContext'    => 'MEDIUM',
        'aridity'                => 'LOW',
        'urbanization'           => 'LOW',
        'biodiversitySensitivity'=> 'HIGH',
        'environmentalSensitivity' => 'HIGH',
        'relevance' => [
            'kurinji'  => 95.0, // Mountain high relevance
            'mullai'   => 90.0, // Forest high relevance
            'marutham' => 40.0,
            'palai'    => 20.0,
            'neithal'  => 15.0
        ]
    ],
    'thanjavur' => [
        'city'                   => 'Thanjavur',
        'state'                  => 'Tamil Nadu',
        'country'                => 'India',
        'latitude'               => 10.7870,
        'longitude'              => 79.1378,
        'elevation'              => 57.0, // meters
        'coastalProximity'       => 'LOW',
        'waterStress'            => 'MEDIUM',
        'forestContext'          => 'LOW',
        'agriculturalContext'    => 'HIGH',
        'aridity'                => 'LOW',
        'urbanization'           => 'MEDIUM',
        'biodiversitySensitivity'=> 'MEDIUM',
        'environmentalSensitivity' => 'MEDIUM',
        'relevance' => [
            'marutham' => 95.0, // Agricultural/Soil high relevance
            'mullai'   => 45.0,
            'palai'    => 40.0,
            'kurinji'  => 25.0,
            'neithal'  => 25.0
        ]
    ],
    'madurai' => [
        'city'                   => 'Madurai',
        'state'                  => 'Tamil Nadu',
        'country'                => 'India',
        'latitude'               => 9.9252,
        'longitude'              => 78.1198,
        'elevation'              => 101.0,
        'coastalProximity'       => 'LOW',
        'waterStress'            => 'HIGH',
        'forestContext'          => 'LOW',
        'agriculturalContext'    => 'MEDIUM',
        'aridity'                => 'HIGH',
        'urbanization'           => 'HIGH',
        'biodiversitySensitivity'=> 'MEDIUM',
        'environmentalSensitivity' => 'MEDIUM',
        'relevance' => [
            'palai'    => 90.0, // Arid / Water stress high
            'marutham' => 50.0,
            'mullai'   => 30.0,
            'kurinji'  => 30.0,
            'neithal'  => 20.0
        ]
    ]
]);

define('LOCATION_ADJUSTMENT_RULES', [
    'max_positive_adjustment' => 5.0,
    'max_negative_adjustment' => -8.0,
    'rules' => [
        'coastal_packaging_penalty' => [
            'condition' => 'coastalProximity === HIGH && packaging_score < 5.0',
            'adjustment' => -4.0,
            'reason' => 'High coastal marine vulnerability in %s penalizes non-recyclable packaging waste (-4 pts).'
        ],
        'coastal_packaging_bonus' => [
            'condition' => 'coastalProximity === HIGH && packaging_score >= 8.0',
            'adjustment' => +2.0,
            'reason' => 'Eco-friendly biodegradable packaging provides strong coastal protection in %s (+2 pts).'
        ],
        'water_stress_penalty' => [
            'condition' => 'waterStress === HIGH && water_score < 5.0',
            'adjustment' => -4.0,
            'reason' => 'Severe water stress in %s penalizes high water-consumption manufacturing (-4 pts).'
        ],
        'mountain_material_penalty' => [
            'condition' => 'elevation > 1000 && material_score < 5.0',
            'adjustment' => -3.0,
            'reason' => 'Fragile mountain ecosystem in %s penalizes high raw material extraction impact (-3 pts).'
        ]
    ]
]);
