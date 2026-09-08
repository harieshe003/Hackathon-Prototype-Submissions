<?php
// config/scoring.config.php
// Centralized Scoring Weights and Factor Definitions for EcoLens

define('SCORING_CONFIG', [
    'weights' => [
        'carbon'         => 0.25, // 25%
        'material'       => 0.15, // 15%
        'recyclability'  => 0.15, // 15%
        'reusability'    => 0.15, // 15%
        'lifespan'       => 0.10, // 10%
        'packaging'      => 0.05, //  5%
        'transportation' => 0.05, //  5%
        'water'          => 0.05, //  5%
        'energy'         => 0.05  //  5%
    ],
    'classifications' => [
        'exceptional' => ['min' => 80.0, 'max' => 100.0, 'label' => 'Exceptional', 'badge' => 'badge-exceptional', 'color' => '#18A957', 'description' => 'Highly Sustainable'],
        'good'        => ['min' => 70.0, 'max' => 79.99, 'label' => 'Good',        'badge' => 'badge-good',        'color' => '#008C95', 'description' => 'Substantially Eco-Friendly'],
        'moderate'    => ['min' => 50.0, 'max' => 69.99, 'label' => 'Moderate',    'badge' => 'badge-moderate',    'color' => '#087CA3', 'description' => 'Moderate Environmental Impact'],
        'poor'        => ['min' => 0.0,  'max' => 49.99, 'label' => 'Poor',        'badge' => 'badge-poor',        'color' => '#DC2626', 'description' => 'High Ecological Footprint']
    ]
]);
