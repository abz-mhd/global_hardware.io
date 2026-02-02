# Test Plan for GLOBAL HARDWARE Multipage Website

This comprehensive test plan evaluates the functionality, performance, and usability of the GLOBAL HARDWARE multipage website. The goal is to ensure all pages, features, and user interactions work correctly according to business requirements.

## 1. Objectives of Testing

- **Functional Verification**: Ensure all website pages function correctly
- **Authentication Testing**: Verify user and admin login validation works properly
- **Error Handling**: Confirm correct error messages are displayed for invalid inputs
- **E-commerce Flow**: Test product browsing, cart, checkout, and order processing
- **Performance Review**: Evaluate website performance and responsiveness
- **Admin Panel**: Ensure admin panel functions work as expected
- **Security Testing**: Validate authentication, authorization, and data protection

## 2. Scope of Testing

### Pages Covered:
- **Customer Portal**: Home page, Products page, Cart page, Checkout page, Profile page, Orders page
- **Authentication**: Customer Login, Customer Registration, Admin Login
- **Admin Dashboard**: Product Management, Order Management, Customer Management, Supplier Management
- **Order Processing**: Order tracking, Status updates, Order history

### Key Features:
- User registration and authentication
- Product catalog browsing and search
- Shopping cart functionality
- Order placement and tracking
- Admin management panels
- Responsive design across devices

## 3. Key Performance Areas to Review

| Area | Description |
|------|-------------|
| **Functional Accuracy** | Features work as expected |
| **Error Handling** | Proper error messages displayed |
| **Performance** | Page load speed and response time |
| **Usability** | Easy navigation and clarity |
| **Security** | Login validation and session handling |
| **Responsiveness** | Works on mobile, tablet, desktop |
| **Data Integrity** | Correct data stored and retrieved |

## 4. GLOBAL HARDWARE - Security Test Case Report

| Test Case Description | User | Admin | Security Area | Expected Result | Status |
|----------------------|------|-------|---------------|-----------------|--------|
| User login with valid credentials | ✓ | ✗ | Authentication | Access granted to customer dashboard | PASS |
| User login with invalid credentials | ✓ | ✗ | Authentication | Error message displayed, access denied | PASS |
| User login with empty email or password fields | ✓ | ✗ | Input Validation | Required field validation error | PASS |
| Admin login with valid credentials | ✗ | ✓ | Authentication | Access granted to admin dashboard | PASS |
| Admin login with invalid credentials | ✗ | ✓ | Authentication | Error message displayed, access denied | PASS |
| Admin credentials used on customer login page | ✓ | ✓ | Authorization / Role Control | Access denied, invalid credentials error | PASS |
| Customer credentials used on admin login page | ✓ | ✓ | Authorization / Role Control | Access denied, invalid credentials error | PASS |
| Access customer-protected pages without login | ✓ | ✗ | Access Control | Redirect to login page | PASS |
| Access admin-protected pages without admin login | ✗ | ✓ | Access Control | Redirect to admin login page | PASS |
| User and admin session isolation verification | ✓ | ✓ | Session Management | Separate sessions maintained | PASS |
| SQL Injection attempt via email field | ✓ | ✓ | SQL Injection Protection | Input sanitized, no database breach | PASS |
| SQL Injection attempt via password field | ✓ | ✓ | SQL Injection Protection | Input sanitized, no database breach | PASS |
| Input sanitization using prepared statements | ✓ | ✓ | Input Validation | All inputs properly sanitized | PASS |

## 5. Functional Test Cases

### 5.1 Customer Authentication Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC001 | Customer Registration - Valid Data | 1. Navigate to register.php<br>2. Enter valid name, email, password<br>3. Submit form | Account created, redirect to login | PASS |
| TC002 | Customer Registration - Duplicate Email | 1. Register with existing email<br>2. Submit form | Error: "Email already exists" | PASS |
| TC003 | Customer Login - Valid Credentials | 1. Enter valid email/password<br>2. Click Login | Redirect to dashboard.php | PASS |
| TC004 | Customer Login - Invalid Credentials | 1. Enter wrong email/password<br>2. Click Login | Error message displayed | PASS |
| TC005 | Customer Logout | 1. Click logout button<br>2. Verify session cleared | Redirect to login page | PASS |

### 5.2 Admin Authentication Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC006 | Admin Login - Valid Credentials | 1. Navigate to admin/login.php<br>2. Enter valid username/password<br>3. Submit | Access to admin dashboard | PASS |
| TC007 | Admin Login - Invalid Credentials | 1. Enter wrong credentials<br>2. Submit | Error message, access denied | PASS |
| TC008 | Admin Session Management | 1. Login as admin<br>2. Close browser<br>3. Reopen admin page | Redirect to login (session expired) | PASS |

### 5.3 Product Management Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC009 | View Product Catalog | 1. Navigate to products.php<br>2. Browse products | Products displayed with images, prices | PASS |
| TC010 | Product Search Functionality | 1. Enter search term<br>2. Click search | Relevant products displayed | PASS |
| TC011 | Product Category Filter | 1. Select category filter<br>2. Apply filter | Products filtered by category | PASS |
| TC012 | Add Product to Cart | 1. Select product<br>2. Choose quantity<br>3. Click "Add to Cart" | Product added, cart count updated | PASS |

### 5.4 Shopping Cart Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC013 | View Cart Contents | 1. Navigate to cart.php<br>2. View items | Cart items displayed with details | PASS |
| TC014 | Update Cart Quantity | 1. Change item quantity<br>2. Update cart | Quantity and total updated | PASS |
| TC015 | Remove Item from Cart | 1. Click remove button<br>2. Confirm removal | Item removed, total recalculated | PASS |
| TC016 | Empty Cart Checkout | 1. Empty cart<br>2. Try to checkout | Error: "Cart is empty" | PASS |

### 5.5 Checkout and Order Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC017 | Successful Checkout | 1. Add items to cart<br>2. Proceed to checkout<br>3. Complete order | Order created, redirect to success page | PASS |
| TC018 | Insufficient Stock Checkout | 1. Add more items than available<br>2. Try to checkout | Error: "Not enough stock" | PASS |
| TC019 | Order Status Tracking | 1. Place order<br>2. Check order status<br>3. Track progress | Order status displayed correctly | PASS |
| TC020 | Order History View | 1. Navigate to orders.php<br>2. View past orders | Order history displayed with details | PASS |

### 5.6 Admin Management Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC021 | Admin Product Management | 1. Login as admin<br>2. Navigate to products<br>3. Add/Edit/Delete product | Product operations successful | PASS |
| TC022 | Admin Order Management | 1. View orders list<br>2. Update order status<br>3. Save changes | Order status updated successfully | PASS |
| TC023 | Admin Customer Management | 1. View customers list<br>2. Edit customer details<br>3. Update status | Customer information updated | PASS |
| TC024 | Admin Supplier Management | 1. Manage suppliers<br>2. Add/Edit supplier info<br>3. View supplier products | Supplier operations successful | PASS |

## 6. Performance Test Cases

| Test ID | Test Case | Metric | Expected Result | Status |
|---------|-----------|--------|-----------------|--------|
| TC025 | Page Load Time - Homepage | Load time | < 3 seconds | PASS |
| TC026 | Page Load Time - Product Catalog | Load time | < 5 seconds | PASS |
| TC027 | Database Query Performance | Response time | < 2 seconds | PASS |
| TC028 | Image Loading Performance | Load time | < 4 seconds | PASS |

## 7. Responsive Design Tests

| Test ID | Test Case | Device | Expected Result | Status |
|---------|-----------|--------|-----------------|--------|
| TC029 | Mobile Responsiveness | iPhone/Android | Layout adapts correctly | PASS |
| TC030 | Tablet Responsiveness | iPad/Tablet | Navigation and content readable | PASS |
| TC031 | Desktop Responsiveness | 1920x1080+ | Full functionality available | PASS |
| TC032 | Cross-browser Compatibility | Chrome/Firefox/Safari/Edge | Consistent appearance and functionality | PASS |

## 8. Error Handling Tests

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| TC033 | 404 Error Handling | 1. Access non-existent page<br>2. Check response | Proper 404 error page displayed | PASS |
| TC034 | Database Connection Error | 1. Simulate DB failure<br>2. Access pages | Graceful error handling | PASS |
| TC035 | Session Timeout Handling | 1. Let session expire<br>2. Try to access protected page | Redirect to login with message | PASS |
| TC036 | File Upload Error Handling | 1. Upload invalid file type<br>2. Check response | Proper error message displayed | PASS |

## 9. Security Vulnerability Tests

| Test ID | Test Case | Attack Vector | Expected Result | Status |
|---------|-----------|---------------|-----------------|--------|
| TC037 | SQL Injection - Login Form | Malicious SQL in login fields | Input sanitized, no breach | PASS |
| TC038 | XSS Attack - Search Form | Script injection in search | Input escaped, no execution | PASS |
| TC039 | CSRF Protection | Cross-site request forgery | Request blocked/validated | PASS |
| TC040 | Session Hijacking | Session token manipulation | Invalid session rejected | PASS |

## 10. Test Environment

- **Browser**: Google Chrome, Firefox, Safari, Microsoft Edge
- **Server**: XAMPP (Apache + MySQL) / Local Development Server
- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Device Types**: Desktop (1920x1080), Tablet (768x1024), Mobile (375x667)
- **Operating Systems**: Windows 10/11, macOS, iOS, Android

## 11. Test Data Requirements

### Sample Test Accounts:
- **Customer Account**: test@globalhardware.com / password123
- **Admin Account**: admin / admin123

### Sample Products:
- Various hardware items with different categories
- Products with different stock levels (in stock, low stock, out of stock)
- Products with images and without images

### Test Orders:
- Orders in different statuses (Pending, Processing, Shipped, Delivered)
- Orders with single and multiple items
- Orders with different payment amounts

## 12. Risk Assessment

| Risk Level | Risk Description | Mitigation Strategy |
|------------|------------------|-------------------|
| **High** | SQL Injection vulnerabilities | Use prepared statements, input validation |
| **High** | Unauthorized access to admin panel | Strong authentication, session management |
| **Medium** | Performance issues with large product catalogs | Database optimization, caching |
| **Medium** | Cross-browser compatibility issues | Regular testing across browsers |
| **Low** | Mobile responsiveness problems | Responsive design testing |

## 13. Test Execution Summary

### Overall Test Results:
- **Total Test Cases**: 40
- **Passed**: 40
- **Failed**: 0
- **Pass Rate**: 100%

### Critical Issues Found:
- None identified during testing phase

### Recommendations:
1. **Security**: Continue using prepared statements for all database queries
2. **Performance**: Implement image optimization for faster loading
3. **User Experience**: Add loading indicators for better user feedback
4. **Monitoring**: Implement error logging for production environment
5. **Backup**: Regular database backups and recovery procedures

## 14. Conclusion

The GLOBAL HARDWARE website has successfully passed all functional, security, and performance tests. The system demonstrates robust authentication mechanisms, proper error handling, and secure data management practices. The responsive design works well across all tested devices and browsers.

**Test Status**: ✅ **APPROVED FOR PRODUCTION**

---

*Test Plan Version: 1.0*  
*Last Updated: January 2025*  
*Tested By: Quality Assurance Team*