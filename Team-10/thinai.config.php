<?php
// config/thinai.config.php
// Centralized EcoLens Sangam 5-Thinai Ecological Mapping Configuration

define('THINAI_FACTOR_MAPPING', [
    'kurinji' => [
        'name'        => 'Kurinji',
        'label'       => 'Mountain & Mineral',
        'description' => 'Raw material extraction & mountain land disturbance impact',
        'icon'        => '⛰️',
        'weights'     => [
            'material' => 0.40,
            'energy'   => 0.30,
            'carbon'   => 0.20,
            'lifespan' => 0.10
        ]
    ],
    'mullai' => [
        'name'        => 'Mullai',
        'label'       => 'Forest & Bio-reserves',
        'description' => 'Forestry resources, biodiversity protection & renewable materials',
        'icon'        => '🌳',
        'weights'     => [
            'material'      => 0.35,
            'carbon'        => 0.30,
            'reusability'   => 0.20,
            'recyclability' => 0.15
        ]
    ],
    'marutham' => [
        'name'        => 'Marutham',
        'label'       => 'Agri & Soil Health',
        'description' => 'Agricultural resource consumption & soil chemical impact',
        'icon'        => '🌾',
        'weights'     => [
            'water'    => 0.45,
            'energy'   => 0.25,
            'material' => 0.20,
            'carbon'   => 0.10
        ]
    ],
    'neithal' => [
        'name'        => 'Neithal',
        'label'       => 'Coastal & Marine',
        'description' => 'Marine plastic accumulation & waterway waste runoff risks',
        'icon'        => '🌊',
        'weights'     => [
            'packaging'      => 0.40,
            'recyclability'  => 0.30,
            'reusability'    => 0.15,
            'transportation' => 0.15
        ]
    ],
    'palai' => [
        'name'        => 'Palai',
        'label'       => 'Dryland & Water Stress',
        'description' => 'Resource consumption pressure under dryland water stress',
        'icon'        => '🏜️',
        'weights'     => [
            'water'    => 0.45,
            'lifespan' => 0.25,
            'carbon'   => 0.15,
            'energy'   => 0.15
        ]
    ]
]);
