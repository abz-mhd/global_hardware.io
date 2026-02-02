# 4.1. Test Plan for GLOBAL HARDWARE Shop Multipage Website

This test plan is created to evaluate the functionality, performance, and usability of the GLOBAL HARDWARE Shop multipage website. The goal of testing is to ensure that all pages, features, and user interactions work correctly according to the design document and business requirements.

Testing includes positive test cases, negative test cases (error handling), and performance checks.

## 01. Objectives of Testing

· To verify that all website pages function correctly
· To ensure user and admin login validation works properly  
· To confirm correct error messages are displayed for invalid inputs
· To test product browsing, cart, checkout, and order processing
· To review website performance and responsiveness
· To ensure admin panel functions work as expected

## 02. Scope of Testing

**Pages Covered:**
· Home page (dashboard.php)
· Products page (products.php)
· Cart page (cart.php)
· Checkout page (checkout.php)
· Login and Registration pages
· Admin Login and Admin Dashboard
· Product, Order, Customer, Supplier management pages
· Profile and Order tracking pages

## 03. Key Performance Areas to Review

**Table 2: Key Performance Areas to Review**

| Area | Description |
|------|-------------|
| Functional Accuracy | Features work as expected |
| Error Handling | Proper error messages displayed |
| Performance | Page load speed and response time |
| Usability | Easy navigation and clarity |
| Security | Login validation and session handling |
| Responsiveness | Works on mobile, tablet, desktop |
| Data Integrity | Correct data stored and retrieved |

## 04. GLOBAL HARDWARE – Security Test Case Report Table

**Table 3: Test Case Table**

| Test Scenario | User | Admin | Test Focus Area | Outcome |
|---------------|------|-------|-----------------|---------|
| Login using correct user credentials | ✓ | ✗ | Authentication | PASS |
| Login using incorrect user credentials | ✓ | ✗ | Authentication | PASS |
| Login with missing input fields | ✓ | ✗ | Input Validation | PASS |
| Login using valid admin credentials | ✗ | ✓ | Authentication | PASS |
| Login using invalid admin credentials | ✗ | ✓ | Authentication | PASS |
| Admin login attempted through user login page | ✓ | ✓ | Role Validation | PASS |
| User login attempted through admin login page | ✓ | ✓ | Role Validation | PASS |
| Access customer pages without login | ✓ | ✗ | Access Restriction | PASS |
| Access admin pages without admin session | ✗ | ✓ | Access Restriction | PASS |
| Separate user and admin session handling | ✓ | ✓ | Session Security | PASS |
| SQL Injection attempt in login fields | ✓ | ✓ | SQL Injection Prevention | PASS |
| Prepared statements validation | ✓ | ✓ | Secure Coding | PASS |
| Unauthorized cart access | ✓ | ✗ | Access Control | PASS |
| Unauthorized order status modification | ✓ | ✗ | Authorization | PASS |
| Unauthorized product management access | ✓ | ✗ | Access Control | PASS |
| Unauthorized customer data access | ✓ | ✗ | Session Control | PASS |
| Cross-site scripting attempt in search | ✓ | ✓ | XSS Protection | PASS |
| CSRF prevention during form submission | ✓ | ✓ | CSRF Protection | PASS |
| Secure image/file upload validation | ✗ | ✓ | File Security | PASS |

## 05. Test Environment

· **Browser**: Google Chrome / Firefox / Safari / Edge
· **Server**: XAMPP (Apache + MySQL)
· **Backend**: PHP 7.4+
· **Database**: MySQL 8.0+
· **Device Types**: Desktop, Tablet, Mobile
· **Operating Systems**: Windows, macOS, iOS, Android

## 06. Usability and Responsiveness Testing

· Navigation menu works correctly across all pages
· Buttons are clearly visible and properly styled
· Layout adjusts properly on mobile and tablet devices
· Forms are easy to understand and user-friendly
· Error messages are clear and user-friendly
· Search functionality works intuitively
· Cart operations are smooth and responsive
· Order tracking interface is clear and informative
· Admin panel navigation is logical and efficient
· Page loading indicators provide good user feedback
· Images load properly and are optimized for different screen sizes
· Text is readable on all device types
· Touch targets are appropriately sized for mobile devices

## 07. Security Testing

· Invalid login attempts show appropriate error messages
· Sessions expire properly after logout
· Users cannot access admin pages without proper authorization
· Input data is properly sanitized and validated
· SQL injection attempts are blocked effectively
· Cross-site scripting (XSS) attacks are prevented
· Cross-site request forgery (CSRF) protection is implemented
· File upload security measures are in place
· Password fields are properly masked
· Session tokens are secure and properly managed
· Database connections use prepared statements
· User roles are strictly enforced
· Sensitive data is not exposed in URLs or error messages

## 08. User Flow

### Customer Flow

· Visit website (index.php or dashboard.php)
· Register new account or login with existing credentials
· Browse products on homepage or navigate to products page
· Search for specific products using search functionality
· Filter products by category or price range
· View product details and specifications
· Add selected products to shopping cart
· Update quantities or remove items from cart
· Review cart contents and total amount
· Proceed to checkout process
· Complete order placement
· View order confirmation and tracking details
· Access order history and track order status
· Update profile information if needed
· Logout securely from account

### Admin Flow

· Access admin login page (admin/login.php)
· Login with valid admin credentials
· View admin dashboard with system statistics
· Navigate to product management section
· Add new products or edit existing product details
· Update product inventory and stock levels
· Manage product categories and pricing
· Access order management system
· View all customer orders and order details
· Update order status (Pending → Processing → Shipped → Delivered)
· Manage customer accounts and information
· View and edit customer details
· Manage supplier information and relationships
· Generate reports and analytics
· Monitor system performance and user activity
· Logout securely from admin panel