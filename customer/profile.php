<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$customerId = $_SESSION['customer_id'];

// Get cart item count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

// Fetch customer details
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch();

if (!$customer) {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GLOBAL HARDWARE Store</title>
    <link rel="stylesheet" href="footer_styles.css">
    <link rel="stylesheet" href="common_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Profile Page Specific Styles */
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .profile-header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-info h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .profile-info p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .profile-status {
            display: inline-block;
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        /* Profile Sections */
        .profile-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .profile-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.3s;
        }

        .profile-section:hover {
            border-color: var(--orange);
            transform: translateY(-2px);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: var(--orange);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group.half {
            display: inline-block;
            width: 48%;
            margin-right: 4%;
        }

        .form-group.half:nth-child(even) {
            margin-right: 0;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }

        input[type=text], input[type=email], input[type=tel], input[type=password], textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 14px;
            transition: all 0.2s;
            font-family: inherit;
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            transform: translateY(-1px);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Button Styling */
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--orange);
            color: white;
        }

        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            border-color: var(--orange);
            color: var(--orange);
        }

        /* Messages */
        .message {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-left-color: var(--danger-color);
        }

        .message.success {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
            border-left-color: var(--success-color);
        }

        .password-note {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            font-style: italic;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-sections {
                grid-template-columns: 1fr;
            }

            .profile-header-content {
                flex-direction: column;
                text-align: center;
            }

            .form-group.half {
                width: 100%;
                margin-right: 0;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-content">
                    <div class="profile-avatar">
                        <?php if (!empty($customer['profile_image'])): ?>
                            <img src="../assets/images/profiles/<?= htmlspecialchars($customer['profile_image']) ?>" 
                                 alt="Profile Picture"
                                 onerror="this.parentElement.innerHTML='<i class=\'fas fa-user\'></i>'">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h1><?= htmlspecialchars($customer['name']) ?></h1>
                        <p><i class="fas fa-envelope" style="margin-right: 8px;"></i><?= htmlspecialchars($customer['email']) ?></p>
                        <?php if (!empty($customer['phone'])): ?>
                            <p><i class="fas fa-phone" style="margin-right: 8px;"></i><?= htmlspecialchars($customer['phone']) ?></p>
                        <?php endif; ?>
                        <span class="profile-status">Active Member</span>
                    </div>
                </div>
            </div>

            <!-- Profile Sections -->
            <div class="profile-sections">
                <!-- Personal Information -->
                <div class="profile-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h2 class="section-title">Personal Information</h2>
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($_GET['error']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="message success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($_GET['success']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="profile_update.php" method="POST">
                        <div class="form-group half">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>
                        </div>

                        <div class="form-group half">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" required>
                        </div>

                        <div class="form-group half">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" rows="3" placeholder="Enter your full address"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Profile
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Security Settings -->
                <div class="profile-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2 class="section-title">Security Settings</h2>
                    </div>

                    <?php if (isset($_GET['pwd_error'])): ?>
                        <div class="message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($_GET['pwd_error']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['pwd_success'])): ?>
                        <div class="message success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($_GET['pwd_success']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="password_change.php" method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required minlength="6">
                            <div class="password-note">Password must be at least 6 characters long</div>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
