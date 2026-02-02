<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Get customer info for personalization
$stmt = $pdo->prepare("SELECT name FROM customers WHERE customer_id = ?");
$stmt->execute([$_SESSION['customer_id']]);
$customer = $stmt->fetch();

// Get some stats for the dashboard
$totalProductsStmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'Available'");
$totalProducts = $totalProductsStmt->fetchColumn();

$categoriesStmt = $pdo->query("SELECT COUNT(DISTINCT category) FROM products WHERE category IS NOT NULL AND category != ''");
$totalCategories = $categoriesStmt->fetchColumn();

// Get recent orders count
$recentOrdersStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ? AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$recentOrdersStmt->execute([$_SESSION['customer_id']]);
$recentOrders = $recentOrdersStmt->fetchColumn();

// Get cart item count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="footer_styles.css">
    <link rel="stylesheet" href="common_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.95) 0%, rgba(39, 39, 42, 0.9) 100%), 
                        url('../Images/cordless-combo.jpg') center/cover no-repeat;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 80px 40px;
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(249, 115, 22, 0.15) 0%, transparent 70%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .hero-title {
            font-size: 52px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
            letter-spacing: -1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .hero-subtitle {
            font-size: 22px;
            color: #e5e5e5;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
            line-height: 1.5;
        }

        .hero-cta {
            display: inline-flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cta-btn {
            padding: 16px 32px;
            background: var(--orange);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cta-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.3);
        }

        .cta-btn.secondary {
            background: transparent;
            border: 2px solid var(--orange);
            color: var(--orange);
        }

        .cta-btn.secondary:hover {
            background: var(--orange);
            color: white;
        }

        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--orange);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 16px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Features Section */
        .features-section {
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 18px;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px 30px;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: var(--orange);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: var(--orange);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 20px;
            color: white;
        }

        .feature-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .feature-description {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Welcome Message */
        .welcome-message {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
            border-left: 4px solid var(--orange);
            border-right: 4px solid var(--orange);
            text-align: center;
        }

        .welcome-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .welcome-text {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-section {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-section {
                padding: 60px 30px;
                min-height: 400px;
            }
            
            .hero-content {
                padding: 30px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            
            .hero-subtitle {
                font-size: 18px;
            }
            
            .stats-section {
                grid-template-columns: 1fr;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .hero-section {
                padding: 40px 20px;
                min-height: 350px;
            }
            
            .hero-content {
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 28px;
            }
            
            .hero-subtitle {
                font-size: 16px;
            }
            
            .hero-section {
                padding: 30px 15px;
                min-height: 300px;
            }
            
            .hero-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Message -->
        <div class="welcome-message">
            <h2 class="welcome-title">WELCOME TO OUR STORE</h2>
            <p class="welcome-text">We're glad to have you here. Explore our extensive collection of quality hardware products and tools for all your project needs.</p>
        </div>

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">YOUR TRUSTED HARDWARE PARTNER</h1>
                <p class="hero-subtitle">Discover premium quality tools, equipment, and hardware solutions for professionals and DIY enthusiasts. From basic supplies to specialized equipment, we've got everything you need.</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-number"><?= number_format($totalProducts) ?>+</div>
                <div class="stat-label">Products Available</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-number"><?= $totalCategories ?>+</div>
                <div class="stat-label">Product Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number"><?= $recentOrders ?></div>
                <div class="stat-label">Your Recent Orders</div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <h2 class="section-title">Why Choose GLOBAL HARDWARE?</h2>
            <p class="section-subtitle">We're committed to providing the best hardware solutions with exceptional service and competitive prices.</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="feature-title">Premium Quality Products</h3>
                    <p class="feature-description">We source only the highest quality tools and hardware from trusted manufacturers. Every product is carefully selected to meet professional standards.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">Fast & Reliable Delivery</h3>
                    <p class="feature-description">Get your orders delivered quickly and safely. We work with trusted shipping partners to ensure your products arrive on time and in perfect condition.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Customer Support</h3>
                    <p class="feature-description">Our expert support team is always ready to help. Whether you need product advice or order assistance, we're here for you around the clock.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="feature-title">Competitive Pricing</h3>
                    <p class="feature-description">We offer the best prices in the market without compromising on quality. Take advantage of our bulk discounts and special offers.</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="hero-section" style="margin-bottom: 0;">
            <div class="hero-content">
                <h2 class="section-title" style="margin-bottom: 20px;">Ready to Get Started?</h2>
                <p class="section-subtitle" style="margin-bottom: 30px;">Explore our wide selection of quality hardware products and tools.</p>
                <div class="hero-cta">
                    <a href="products.php" class="cta-btn">
                        <i class="fas fa-shopping-bag"></i>
                        Shop Now
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php include 'footer.php'; ?>
