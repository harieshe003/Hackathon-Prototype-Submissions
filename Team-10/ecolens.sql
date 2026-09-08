-- EcoLens Database Schema & Seed Data
-- Supports MySQL 8.0+ and SQLite 3

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user', -- 'user', 'business', 'admin'
    location_city VARCHAR(100) DEFAULT 'Chennai',
    location_country VARCHAR(100) DEFAULT 'India',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) DEFAULT '',
    country VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT 13.0827,
    longitude DECIMAL(11, 8) DEFAULT 80.2707,
    coastal_proximity VARCHAR(20) DEFAULT 'MEDIUM', -- HIGH, MEDIUM, LOW
    water_stress VARCHAR(20) DEFAULT 'MEDIUM',
    urbanization VARCHAR(20) DEFAULT 'HIGH',
    forest_context VARCHAR(20) DEFAULT 'LOW',
    agricultural_context VARCHAR(20) DEFAULT 'LOW',
    kurinji_relevance DECIMAL(5,2) DEFAULT 20.0,
    mullai_relevance DECIMAL(5,2) DEFAULT 20.0,
    marutham_relevance DECIMAL(5,2) DEFAULT 20.0,
    neithal_relevance DECIMAL(5,2) DEFAULT 60.0,
    palai_relevance DECIMAL(5,2) DEFAULT 40.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'box'
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    material VARCHAR(100) NOT NULL,
    weight DECIMAL(10, 2) DEFAULT 0.0, -- in grams or kg
    manufacturing_location VARCHAR(150) DEFAULT 'Unknown',
    packaging_material VARCHAR(100) DEFAULT 'Single-use Plastic',
    packaging_weight DECIMAL(10, 2) DEFAULT 0.0,
    reusable TINYINT(1) DEFAULT 0,
    lifespan VARCHAR(50) DEFAULT '1 year', -- expected lifespan
    recyclable TINYINT(1) DEFAULT 0,
    repairable TINYINT(1) DEFAULT 0,
    energy_usage DECIMAL(10, 2) DEFAULT 0.0, -- kWh
    water_usage DECIMAL(10, 2) DEFAULT 0.0, -- Liters
    transport_distance DECIMAL(10, 2) DEFAULT 0.0, -- km
    carbon_footprint DECIMAL(10, 2) DEFAULT 0.0, -- kg CO2e
    end_of_life VARCHAR(100) DEFAULT 'Landfill',
    image VARCHAR(255) DEFAULT 'assets/images/steel-bottle.png',
    source_url VARCHAR(255) DEFAULT '',
    data_confidence VARCHAR(20) DEFAULT 'Medium', -- High, Medium, Low
    is_verified TINYINT(1) DEFAULT 0,
    is_demo TINYINT(1) DEFAULT 0,
    created_by INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS environmental_factors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    carbon_score DECIMAL(4, 2) DEFAULT 5.0,
    material_score DECIMAL(4, 2) DEFAULT 5.0,
    recyclability_score DECIMAL(4, 2) DEFAULT 5.0,
    reusability_score DECIMAL(4, 2) DEFAULT 5.0,
    lifespan_score DECIMAL(4, 2) DEFAULT 5.0,
    packaging_score DECIMAL(4, 2) DEFAULT 5.0,
    transportation_score DECIMAL(4, 2) DEFAULT 5.0,
    water_score DECIMAL(4, 2) DEFAULT 5.0,
    energy_score DECIMAL(4, 2) DEFAULT 5.0,
    manufacturing_score DECIMAL(4, 2) DEFAULT 5.0,
    evidence TEXT,
    confidence VARCHAR(20) DEFAULT 'Medium',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ecolens_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    overall_score DECIMAL(5, 2) NOT NULL,
    rating VARCHAR(50) NOT NULL, -- Exceptional, Excellent, Good, Moderate, Low, Poor
    kurinji_score DECIMAL(5, 2) DEFAULT 50.0,
    mullai_score DECIMAL(5, 2) DEFAULT 50.0,
    marutham_score DECIMAL(5, 2) DEFAULT 50.0,
    neithal_score DECIMAL(5, 2) DEFAULT 50.0,
    palai_score DECIMAL(5, 2) DEFAULT 50.0,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comparisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100) DEFAULT NULL,
    name VARCHAR(200) DEFAULT 'Product Comparison',
    winner_product_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comparison_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comparison_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (comparison_id) REFERENCES comparisons(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS saved_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ecoswap_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_product_id INT NOT NULL,
    recommended_product_id INT NOT NULL,
    score_difference DECIMAL(5, 2) DEFAULT 0.0,
    reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (original_product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (recommended_product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    registration_no VARCHAR(100) DEFAULT '',
    website VARCHAR(200) DEFAULT '',
    contact_email VARCHAR(150) NOT NULL,
    industry VARCHAR(100) DEFAULT 'Manufacturing',
    is_verified TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS business_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    product_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Draft', -- Active, Draft, Archived
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    product_id INT NOT NULL,
    overall_score DECIMAL(5,2) NOT NULL,
    top_opportunity VARCHAR(200) NOT NULL,
    suggested_action TEXT NOT NULL,
    estimated_impact_points DECIMAL(4,2) DEFAULT 5.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS improvement_simulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    business_id INT DEFAULT NULL,
    original_score DECIMAL(5,2) NOT NULL,
    simulated_score DECIMAL(5,2) NOT NULL,
    changes_json TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS verification_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    business_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending', -- Pending, Under Review, Approved, Rejected
    reviewer_notes TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS verification_evidence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verification_id INT NOT NULL,
    document_name VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    source_url VARCHAR(255) DEFAULT '',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (verification_id) REFERENCES verification_requests(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS scoring_weights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factor_name VARCHAR(50) NOT NULL UNIQUE,
    weight_percentage DECIMAL(5,2) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS ai_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    prompt_hash VARCHAR(64) DEFAULT '',
    response_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Seed Categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Beverage Containers', 'beverage-containers', 'Reusable and single-use bottles, cups, and flasks', 'bottle-water'),
('Packaging & Shipping', 'packaging-shipping', 'Boxes, mailers, cushions, and wrapping materials', 'box'),
('Personal Care', 'personal-care', 'Soaps, toothbrushes, shampoos, and hygiene products', 'sparkles'),
('Home & Kitchen', 'home-kitchen', 'Cookware, food storage, cleaning supplies', 'home');

-- Seed Default Scoring Weights
INSERT INTO scoring_weights (factor_name, weight_percentage, description) VALUES
('carbon', 25.0, 'Carbon footprint across lifecycle'),
('material', 15.0, 'Raw material eco-intensity and sourcing'),
('recyclability', 15.0, 'Post-consumer recyclability percentage'),
('reusability', 15.0, 'Multi-use capability and replacement rate'),
('lifespan', 10.0, 'Product durability and expected service life'),
('packaging', 5.0, 'Packaging material toxicity and recyclability'),
('transportation', 5.0, 'Transport distance and freight mode impact'),
('water', 5.0, 'Direct and indirect water consumption'),
('energy', 5.0, 'Manufacturing energy demand');

-- Seed Default Admin & Business & User (Passwords: Password123)
-- Hash generated for 'Password123': $2y$10$e84W/3Wv4WJkHqM.lP3aNeJ5Y6aZ3A9eX0W0g6C1Z.F8wO5eYqG3a
INSERT INTO users (id, name, email, password, role, location_city, location_country) VALUES
(1, 'EcoLens Admin', 'admin@ecolens.org', '$2y$10$e84W/3Wv4WJkHqM.lP3aNeJ5Y6aZ3A9eX0W0g6C1Z.F8wO5eYqG3a', 'admin', 'Chennai', 'India'),
(2, 'EcoBrand Producer', 'business@ecobrand.com', '$2y$10$e84W/3Wv4WJkHqM.lP3aNeJ5Y6aZ3A9eX0W0g6C1Z.F8wO5eYqG3a', 'business', 'Chennai', 'India'),
(3, 'Green Consumer', 'user@ecolens.org', '$2y$10$e84W/3Wv4WJkHqM.lP3aNeJ5Y6aZ3A9eX0W0g6C1Z.F8wO5eYqG3a', 'user', 'Chennai', 'India');

INSERT INTO businesses (id, user_id, company_name, registration_no, website, contact_email, industry, is_verified) VALUES
(1, 2, 'EcoBrand Solutions Pvt Ltd', 'REG-2026-8891', 'https://ecobrand.com', 'business@ecobrand.com', 'Consumer Goods', 1);

-- Seed Demo Locations
INSERT INTO locations (city, state, country, latitude, longitude, coastal_proximity, water_stress, urbanization, forest_context, agricultural_context, kurinji_relevance, mullai_relevance, marutham_relevance, neithal_relevance, palai_relevance) VALUES
('Chennai', 'Tamil Nadu', 'India', 13.0827, 80.2707, 'HIGH', 'HIGH', 'HIGH', 'LOW', 'LOW', 25.0, 30.0, 35.0, 95.0, 75.0),
('Coimbatore', 'Tamil Nadu', 'India', 11.0168, 76.9558, 'LOW', 'MEDIUM', 'HIGH', 'MEDIUM', 'HIGH', 85.0, 70.0, 80.0, 20.0, 40.0),
('Bengaluru', 'Karnataka', 'India', 12.9716, 77.5946, 'LOW', 'HIGH', 'HIGH', 'LOW', 'LOW', 60.0, 45.0, 40.0, 15.0, 85.0);

-- Seed Demo Products (5 Products required by spec)
INSERT INTO products (id, name, brand, category, description, material, weight, manufacturing_location, packaging_material, packaging_weight, reusable, lifespan, recyclable, repairable, energy_usage, water_usage, transport_distance, carbon_footprint, end_of_life, image, source_url, data_confidence, is_verified, created_by) VALUES
(1, 'Single-use Plastic Water Bottle (500ml)', 'AquaClear', 'Beverage Containers', 'Standard single-use virgin PET plastic water bottle with plastic cap and film label.', 'PET Plastic', 18.0, 'Local Bottler', 'Plastic Shrink Wrap', 5.0, 0, 'Single-use', 0, 0, 1.2, 3.5, 450.0, 0.28, 'Landfill / Ocean', 'assets/images/plastic-bottle.svg', 'https://example.com/pet-spec', 'High', 0, 1),
(2, 'Stainless Steel Thermal Flask (750ml)', 'EcoHydro', 'Beverage Containers', 'Double-walled food-grade 18/8 stainless steel insulated bottle designed for decades of daily reuse.', '18/8 Stainless Steel', 350.0, 'Coimbatore, India', 'Recycled Cardboard Box', 25.0, 1, '10+ years', 1, 1, 8.5, 12.0, 220.0, 2.10, 'Recycled Scrap Metal', 'assets/images/steel-bottle.svg', 'https://example.com/steel-spec', 'High', 1, 2),
(3, 'Glass Beverage Bottle with Rubber Sleeve (600ml)', 'PureGlass', 'Beverage Containers', 'Borosilicate glass bottle with protective natural rubber sleeve and bamboo lid.', 'Borosilicate Glass', 410.0, 'Bengaluru, India', 'Kraft Paper Wrap', 30.0, 1, '5+ years', 1, 0, 6.2, 8.5, 380.0, 1.45, 'Glass Recycling Stream', 'assets/images/glass-bottle.svg', 'https://example.com/glass-spec', 'High', 1, 1),
(4, 'Recycled rPET Water Bottle (500ml)', 'ReBottled', 'Beverage Containers', '100% post-consumer recycled PET plastic bottle reducing virgin plastic footprint.', 'Recycled rPET', 16.0, 'Chennai, India', 'Recycled Paper Sleeve', 8.0, 0, 'Single-use', 1, 0, 0.7, 1.8, 180.0, 0.12, 'rPET Recycling Stream', 'assets/images/rpet-bottle.svg', 'https://example.com/rpet-spec', 'Medium', 0, 2),
(5, 'Bamboo Fiber Composite Tumbler (450ml)', 'BioVessel', 'Beverage Containers', 'Biodegradable bamboo fiber composite cup with plant-derived PLA lining.', 'Bamboo Fiber & PLA', 120.0, 'Western Ghats, India', 'Unbleached Cardboard', 15.0, 1, '3+ years', 0, 0, 3.1, 4.2, 120.0, 0.65, 'Industrial Compost', 'assets/images/bamboo-bottle.svg', 'https://example.com/bamboo-spec', 'High', 1, 2);

-- Seed Environmental Factors (Ratings 0-10)
INSERT INTO environmental_factors (id, product_id, carbon_score, material_score, recyclability_score, reusability_score, lifespan_score, packaging_score, transportation_score, water_score, energy_score, manufacturing_score, evidence, confidence) VALUES
(1, 1, 3.5, 2.0, 4.0, 1.0, 1.5, 3.0, 6.0, 7.0, 7.5, 3.5, 'Virgin PET synthesis emissions; landfill accumulation data.', 'High'),
(2, 2, 8.8, 8.5, 9.2, 10.0, 9.5, 8.5, 7.8, 7.2, 7.0, 8.0, 'ISO 14040 Life Cycle Assessment; 100% recyclable steel grade 304.', 'High'),
(3, 3, 7.5, 8.0, 9.0, 8.5, 8.0, 8.0, 5.5, 6.8, 6.5, 7.2, 'Borosilicate glass lifecycle report; natural rubber sleeve biodegradability.', 'High'),
(4, 4, 6.8, 6.5, 7.2, 2.0, 2.5, 7.5, 8.2, 8.0, 8.5, 6.9, 'Post-consumer rPET resin energy audit; reduced virgin monomer processing.', 'Medium'),
(5, 5, 8.2, 9.0, 5.0, 8.0, 7.5, 9.0, 8.8, 8.2, 8.0, 8.5, 'ASTM D6400 industrial composting test report; bio-based carbon index.', 'High');

-- Seed EcoLens Calculated Scores
INSERT INTO ecolens_scores (id, product_id, overall_score, rating, kurinji_score, mullai_score, marutham_score, neithal_score, palai_score) VALUES
(1, 1, 42.00, 'Low', 45.0, 38.0, 52.0, 22.0, 55.0),
(2, 2, 87.00, 'Excellent', 82.0, 88.0, 85.0, 92.0, 88.0),
(3, 3, 78.00, 'Good', 74.0, 80.0, 76.0, 84.0, 78.0),
(4, 4, 69.00, 'Moderate', 68.0, 65.0, 70.0, 62.0, 72.0),
(5, 5, 82.00, 'Excellent', 86.0, 89.0, 80.0, 78.0, 82.0);

-- Seed EcoSwap Recommendations
INSERT INTO ecoswap_recommendations (original_product_id, recommended_product_id, score_difference, reason) VALUES
(1, 2, 45.00, 'Replacing single-use plastic with stainless steel thermal flask avoids ~300 plastic bottles per year and eliminates ocean microplastic risks.'),
(1, 5, 40.00, 'Bamboo fiber tumbler offers high renewable bio-content and zero virgin petro-chemical requirement.'),
(1, 3, 36.00, 'Borosilicate glass bottle delivers pure taste, zero chemical leaching, and infinite glass stream recyclability.');

-- Link business product portfolio
INSERT INTO business_products (business_id, product_id, status) VALUES
(1, 2, 'Active'),
(1, 5, 'Active'),
(1, 4, 'Active');

-- Seed Initial Verification Request
INSERT INTO verification_requests (id, product_id, business_id, status, reviewer_notes) VALUES
(1, 2, 1, 'Approved', 'Full ISO 14040 LCA documentation verified by EcoLens Admin.');

INSERT INTO verification_evidence (verification_id, document_name, file_path, source_url, notes) VALUES
(1, 'ISO 14040 LCA Steel Flask.pdf', 'uploads/evidence_iso_14040.pdf', 'https://ecobrand.com/lca-cert', 'Official third-party lifecycle assessment report.');
