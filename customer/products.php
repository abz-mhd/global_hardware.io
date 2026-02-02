<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

// Get search and category filter parameters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

// Build query for products
$query = "SELECT * FROM products WHERE status = 'Available'";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all categories for filter dropdown
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

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
    <title>Browse Products - GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="footer_styles.css">
    <link rel="stylesheet" href="common_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Filter Section */
        .filter-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 40px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 2fr 1.5fr auto;
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            background: var(--hover-bg);
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-btn {
            padding: 12px 24px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .search-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .clear-btn {
            padding: 12px 20px;
            background: var(--text-secondary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .clear-btn:hover {
            background: #475569;
            transform: translateY(-1px);
        }

        /* Responsive Filter */
        @media (max-width: 1024px) {
            .filter-form {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            
            .filter-actions {
                grid-column: 1 / -1;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .filter-section {
                padding: 24px;
            }
            
            .filter-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .search-btn,
            .clear-btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }

        .product-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.2);
            border-color: var(--orange);
        }

        /* Product Details Modal */
        .product-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .product-modal.show {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid var(--border-color);
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--orange);
        }

        .modal-image {
            width: 100%;
            height: 200px;
            background: var(--hover-bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .modal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .modal-category {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .modal-description {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .modal-price {
            font-size: 28px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 12px;
        }

        .modal-stock {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: var(--hover-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px 8px 0 0;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
            border-radius: 8px 8px 0 0;
            background: var(--hover-bg);
        }

        .product-image img:hover {
            cursor: pointer;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 0%, rgba(249, 115, 22, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-image::after {
            opacity: 1;
        }

        .no-image-placeholder {
            color: var(--text-muted);
            font-size: 12px;
            text-align: center;
            padding: 15px;
            background: var(--hover-bg);
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            margin: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 164px;
        }

        .no-image-placeholder i {
            font-size: 36px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .product-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            line-height: 1.4;
            min-height: 50px;
        }

        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 16px;
        }

        .product-stock {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .product-form {
            margin-top: auto;
        }

        .product-form input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 14px;
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 12px;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .add-to-cart-btn:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .add-to-cart-btn:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            transform: none;
        }

        /* No Products */
        .no-products {
            text-align: center;
            padding: 80px 40px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .no-products p {
            font-size: 16px;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title" style="text-align: center;">Browse Our Products</h1>
            <p class="page-subtitle" style="text-align: center;">Find the tools and equipment you need for your projects</p>
        </div>

        <!-- Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="message success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="products.php" class="filter-form">
                <div class="filter-group">
                    <label>Search Products</label>
                    <input type="text" name="search" placeholder="Search by name or description..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if (!empty($search) || !empty($category)): ?>
                        <a href="products.php" class="clear-btn">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="no-products">
                <p>No products found. Try adjusting your search or filters.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card" onclick="showProductDetails(<?= $product['product_id'] ?>)">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image\'></i><span>No Image Available</span></div>'">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-price">LKR <?= number_format($product['price'], 2) ?></div>
                            <div class="product-stock">Stock: <?= $product['stock'] ?> units</div>
                            <?php if ($product['stock'] > 0): ?>
                                <form action="cart_add.php" method="POST" class="product-form" onclick="event.stopPropagation();">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                    <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <button class="add-to-cart-btn" disabled onclick="event.stopPropagation();">Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Product Details Modal -->
        <div class="product-modal" id="productModal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeProductModal()">&times;</button>
                <div class="modal-image" id="modalImage"></div>
                <div class="modal-title" id="modalTitle"></div>
                <div class="modal-category" id="modalCategory"></div>
                <div class="modal-description" id="modalDescription"></div>
                <div class="modal-price" id="modalPrice"></div>
                <div class="modal-stock" id="modalStock"></div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // Product data for modal
        const products = <?= json_encode($products) ?>;

        // Show product details modal
        function showProductDetails(productId) {
            const product = products.find(p => p.product_id == productId);
            if (!product) return;

            document.getElementById('modalTitle').textContent = product.name;
            document.getElementById('modalCategory').textContent = product.category || '';
            document.getElementById('modalDescription').textContent = product.description || 'No description available';
            document.getElementById('modalPrice').textContent = 'LKR ' + parseFloat(product.price).toFixed(2);
            document.getElementById('modalStock').textContent = product.stock + ' units in stock';

            // Set image
            const modalImage = document.getElementById('modalImage');
            if (product.image) {
                modalImage.innerHTML = `<img src="../assets/images/${product.image}" alt="${product.name}">`;
            } else {
                modalImage.innerHTML = '<div class="no-image-placeholder"><i class="fas fa-image"></i><span>No Image Available</span></div>';
            }

            document.getElementById('productModal').classList.add('show');
        }

        // Close product modal
        function closeProductModal() {
            document.getElementById('productModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });

        // Add to cart with AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.product-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const button = this.querySelector('.add-to-cart-btn');
                    const originalText = button.innerHTML;
                    
                    // Show loading state
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                    button.disabled = true;
                    
                    fetch('cart_add.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                        
                        // Update cart count in header
                        updateCartCount();
                        
                        // Show success notification
                        showNotification('Product added to cart successfully!', 'success');
                        
                        // Reset quantity to 1
                        this.querySelector('input[name="quantity"]').value = 1;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.innerHTML = originalText;
                        button.disabled = false;
                        showNotification('Error adding product to cart', 'error');
                    });
                });
            });
        });

        // Update cart count
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    const cartLink = document.getElementById('cart-link');
                    const cartCount = document.getElementById('cart-count');
                    
                    if (data.count > 0) {
                        if (cartCount) {
                            cartCount.textContent = `(${data.count})`;
                        } else {
                            // Create count if it doesn't exist
                            const count = document.createElement('span');
                            count.className = 'cart-count';
                            count.id = 'cart-count';
                            count.textContent = `(${data.count})`;
                            cartLink.appendChild(count);
                        }
                    } else {
                        if (cartCount) {
                            cartCount.remove();
                        }
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            // Add notification styles
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? 'var(--success-color)' : 'var(--danger-color)'};
                color: white;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                animation: slideIn 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>