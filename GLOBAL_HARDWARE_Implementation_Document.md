# 1.1. Use of Design Document to Produce a Branded Multipage Website

This section explains how the GLOBAL HARDWARE Shop website was developed using the previously created design document. The website follows proper design principles, web standards, and usability guidelines to produce a branded, multipage e-commerce website with realistic and meaningful content for both customers and administrators.

## 01. Use of the Design Document

The design document was used as a blueprint for developing the website.
All planned elements such as:
· Page structure and layout organization
· E-commerce features and functionality
· User interface design concepts
· Customer and admin user flows
· Technology stack selection
· Database schema design
· Security implementation requirements

were carefully followed during implementation.

This ensured that the final website matches the original requirements of the business and users, providing a complete hardware store management system with customer portal and administrative backend.

## 02. Branding Implementation

The website clearly reflects the GLOBAL HARDWARE Shop brand.
· **Consistent logo placement** on all pages using GLOBAL.png
· **Orange and dark theme color scheme** (#f97316 orange, #000000 black) used throughout the site
· **Same font family** (Inter, Segoe UI) across frontend and admin panel
· **Uniform button styles** with orange primary color and consistent hover effects
· **Professional typography** with proper font weights and spacing
· **Brand tagline** "YOUR TRUSTED HARDWARE PARTNER" consistently displayed
· **Cohesive visual identity** across customer and admin interfaces

This consistency helps users easily recognize the brand and improves trust and professionalism throughout the entire system.

## 03. Application of Design Principles, Standards and Guidelines

The website follows standard web design principles:

### Design Principles:
· **Consistency**: Same layout, colors, navigation, and styling patterns on all pages
· **Usability**: Intuitive navigation menu, clear call-to-action buttons, and logical user flows
· **Responsiveness**: Fully responsive design that works on mobile, tablet, and desktop devices
· **Accessibility**: Readable fonts, proper color contrast, clear form labels, and semantic HTML
· **Visual hierarchy**: Important content highlighted using typography, color, and spacing
· **User-centered design**: Features designed around customer and admin needs

### Web Standards:
· **HTML5 structure** with semantic elements and proper document structure
· **CSS3 responsiveness** using flexbox, grid, and media queries
· **JavaScript interactivity** for dynamic content and form validation
· **PHP best practices** with prepared statements and secure coding
· **Database normalization** following relational database principles

Web standards ensure proper performance, security, and cross-browser compatibility.

## 04. Multipage Website Implementation

The website is a fully multipage system with comprehensive functionality.

### Customer Pages:
· **Home** (dashboard.php) - Marketing homepage with featured products and company information
· **Products** (products.php) - Complete product catalog with search and filtering
· **Cart** (cart.php) - Shopping cart with quantity management and total calculations
· **Checkout** (checkout.php) - Secure order placement and payment processing
· **Login & Registration** (login.php, register.php) - User authentication system
· **Profile** (profile.php) - Customer account management and information updates
· **Orders** (orders.php) - Order history and tracking functionality
· **Order Success** (order_success.php) - Order confirmation and tracking interface

### Admin Pages:
· **Admin Dashboard** (admin/dashboard.php) - System overview with statistics and metrics
· **Product Management** (admin/products.php) - Add, edit, delete, and manage product inventory
· **Orders Management** (admin/orders.php) - Process orders and update order status
· **Customers** (admin/customers.php) - Manage customer accounts and information
· **Suppliers** (admin/suppliers.php) - Supplier relationship and contact management
· **Product Editing** (admin/product_edit.php) - Detailed product information management
· **Customer Editing** (admin/customer_edit.php) - Individual customer account management
· **Order Processing** (admin/order_process.php) - Order status update functionality

Each page serves a specific purpose and is connected through a clear, intuitive navigation system with proper user flow and security controls.

## 05. Realistic Content Usage

The website uses realistic and meaningful content, not dummy text or placeholder data.

### Product Information:
· **Real hardware products** with actual names like "13mm 1000W Tool Master Drill Machine"
· **Accurate pricing** in LKR currency reflecting Sri Lankan market
· **Detailed descriptions** with specifications and product features
· **Professional product images** stored in organized directory structure
· **Proper categorization** (Tools, Paint, Hardware, etc.)

### Business Data:
· **Order management** with real quantities, totals, and status tracking
· **Customer information** with proper contact details and account management
· **Supplier relationships** with realistic business contact information
· **Inventory tracking** with actual stock levels and availability status
· **Admin dashboard** displaying genuine statistics from database operations

### Content Quality:
· **Professional copywriting** for brand messaging and product descriptions
· **Consistent terminology** throughout the system
· **User-friendly language** for error messages and instructions
· **Business-appropriate tone** for all customer-facing content

This makes the website suitable for real-world deployment and actual business operations.

## 06. Technologies and Structure Used

### Frontend Technologies:
· **HTML5** for structured and semantic page layout with proper document structure
· **CSS3** for advanced styling, animations, responsive design, and visual effects
· **JavaScript** for client-side interactivity, form validation, and dynamic content updates
· **Font Awesome** for consistent iconography throughout the interface
· **Responsive Design** using CSS Grid, Flexbox, and media queries

### Backend Technologies:
· **PHP 7.4+** for server-side processing, business logic, and database interactions
· **MySQL 8.0+** for relational data storage, user management, and transaction processing
· **PDO (PHP Data Objects)** for secure database connections and prepared statements
· **Session Management** for user authentication and security
· **File Upload Handling** for product images and media management

### Security Implementation:
· **Prepared Statements** to prevent SQL injection attacks
· **Input Validation** and sanitization on all user inputs
· **Session Security** with proper timeout and isolation
· **Role-based Access Control** separating customer and admin privileges
· **Password Security** with proper hashing and validation
· **CSRF Protection** on form submissions
· **XSS Prevention** through proper output escaping

### Development Standards:
· **MVC-inspired architecture** with separation of concerns
· **Consistent coding standards** across all PHP files
· **Modular design** with reusable components (header.php, footer.php)
· **Database normalization** with proper table relationships
· **Error handling** with user-friendly messages and logging
· **Performance optimization** with efficient queries and image optimization

All technologies follow modern web development standards and industry best practices, ensuring scalability, maintainability, and security for the GLOBAL HARDWARE e-commerce platform.

## 07. Key Features Implemented

### Customer Features:
· **User Registration and Authentication** with email validation
· **Product Browsing and Search** with category filtering
· **Shopping Cart Management** with real-time updates
· **Secure Checkout Process** with inventory validation
· **Order Tracking System** with visual progress indicators
· **Profile Management** with account information updates
· **Responsive Design** optimized for all devices

### Admin Features:
· **Comprehensive Dashboard** with business metrics
· **Product Management System** with image upload capabilities
· **Order Processing Workflow** with status management
· **Customer Account Management** with detailed information
· **Supplier Relationship Management** with contact tracking
· **Inventory Control System** with stock level monitoring
· **Secure Admin Authentication** with role-based access

### Technical Features:
· **Database Integration** with MySQL for data persistence
· **Session Management** for user state maintenance
· **File Upload System** for product images
· **AJAX Functionality** for dynamic content updates
· **Form Validation** both client-side and server-side
· **Error Handling** with appropriate user feedback
· **Security Measures** protecting against common vulnerabilities

This comprehensive implementation demonstrates professional web development practices and creates a fully functional e-commerce platform suitable for real business operations.