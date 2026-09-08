# 🌱 EcoLens — Ecological Product Comparison Platform

> **Five Lenses. One Sustainable Choice.**  
> *See Beyond the Product. Choose Better.*

EcoLens is a production-ready, full-featured PHP web application designed to evaluate products across their complete lifecycle:
**Raw Material → Manufacturing → Transportation → Packaging → Usage → End of Life**

---

## 🌟 Key Features

1. **Deterministic 9-Factor Scoring Engine**: Evaluates Carbon Footprint (25%), Material Intensity (15%), Recyclability (15%), Reusability (15%), Lifespan (10%), Packaging (5%), Transport (5%), Water (5%), and Energy (5%).
2. **EcoLens 5 Interpretive Thinai Landscapes**: Maps product impact into classical ecosystem dimensions — *Kurinji* (Mountain/Mining), *Mullai* (Forest/Bio), *Marutham* (Agri/Soil), *Neithal* (Coastal/Marine), and *Palai* (Dryland/Resource Stress).
3. **Location Intelligence**: GPS/Geocoding context alters factor weightings based on local environmental vulnerabilities (e.g. Coastal marine risks in Chennai).
4. **Multi-Product Comparison Matrix**: Compares 2 to 5 products side-by-side with automatic winner detection and differential analysis.
5. **EcoSwap Recommendation Engine**: Suggests greener product swaps with calculated score improvement points.
6. **Gemini AI Backend Proxy**: Proxies Gemini API requests through PHP backend endpoints (`/api/ai/explain.php`) so API keys are never exposed on client-side JS. Includes fallback structured AI synthesis.
7. **Business SaaS Portal (`/business/`)**: Features product inventory management, automated sustainability audits, real-time score simulators, and EcoLens Verified badge evidence applications.
8. **Admin Moderation Portal (`/admin/`)**: Full administrative control over user accounts, product catalogs, verification review workflows, and scoring weights.

---

## 🚀 Installation & Quick Start

### Prerequisites
- PHP 8.2+ with `pdo`, `pdo_sqlite`, or `pdo_mysql` extensions enabled.
- Web Server (Apache, Nginx, or PHP Built-in Server).

### Setup Instructions

1. **Clone or Copy Repository**:
   ```bash
   cd c:\Users\Niranjan\OneDrive\Desktop\Eco-lens
   ```

2. **Configure Environment Variables**:
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
   *(Optionally add your Gemini API Key in `.env` under `GEMINI_API_KEY=`)*

3. **Database Initialization**:
   - **Out-of-the-Box SQLite (Automatic)**: No database configuration required! The app automatically creates `database/ecolens.sqlite` and executes `database/ecolens.sql` schema and seed data on first request.
   - **MySQL / MariaDB (Production)**: Import `database/ecolens.sql` into your MySQL server and set `DB_DRIVER=mysql` in `.env`.

4. **Launch Application Server**:
   Run PHP built-in web server:
   ```bash
   php -S 127.0.0.1:8000
   ```
   Open your browser at: `http://localhost:8000`

---

## 🔑 Default Demo Credentials

| Role | Email | Password | Access Area |
| :--- | :--- | :--- | :--- |
| **Consumer User** | `user@ecolens.org` | `Password123` | `/dashboard.php` |
| **Business Producer** | `business@ecobrand.com` | `Password123` | `/business/dashboard.php` |
| **Administrator** | `admin@ecolens.org` | `Password123` | `/admin/dashboard.php` |

---

## 📡 REST API Documentation

All API endpoints return JSON formatted responses: `{"success": true, "data": {...}}` or `{"success": false, "error": "Reason"}`.

### 1. Public APIs
- **`GET /api/products/search.php?q=bottle&category=Beverage Containers&min_score=70`**: Search products.
- **`GET /api/products/get.php?id=2`**: Fetch product lifecycle factors and EcoLens 5 breakdown.
- **`GET /api/comparison/compare.php?ids=1,2,5`**: Side-by-side multi-product comparison matrix & winner calculation.
- **`GET /api/scoring/calculate.php?carbon_score=8.5&material_score=9.0`**: Calculate overall score & Thinai metrics.
- **`GET /api/location/profile.php?lat=13.0827&lng=80.2707`**: Get location intelligence profile & set session city.
- **`GET /api/ai/explain.php?product_id=2&location=Chennai`**: Generate Gemini AI sustainability explanation.
- **`GET /api/ecoswap/recommend.php?product_id=1`**: Fetch EcoSwap greener product alternatives.

### 2. Business & Verification APIs
- **`POST /api/products/create.php`**: Submit new 18-field environmental product entry.
- **`GET /api/business/audit.php?product_id=2`**: Automated product audit identifying top score improvement opportunity.
- **`POST /api/business/simulate.php`**: Real-time score simulator endpoint for parameter adjustments.
- **`POST /api/verification/submit.php`**: Submit ISO 14040 evidence for EcoLens Verified badge.

---

## 🛡 Security & Best Practices

- **PDO Prepared Statements**: All SQL queries utilize prepared statements for full SQL injection immunity.
- **XSS Protection**: All user inputs sanitized with `htmlspecialchars` output escaping.
- **Password Security**: Passwords hashed using `PASSWORD_BCRYPT` via `password_hash()`.
- **API Key Security**: Sensitive keys (`GEMINI_API_KEY`) remain strictly encapsulated on the PHP server.

---

## 📜 Methodology Disclaimer

> EcoLens scores are based on available environmental data and the platform's defined methodology. EcoLens 5 is an interpretive ecological framework inspired by the five Tamil Thinai landscapes and is not itself a scientific certification standard.
