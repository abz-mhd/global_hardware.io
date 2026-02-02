# GLOBAL HARDWARE Shop System Diagram

```
                            GLOBAL HARDWARE Shop System
    ┌─────────────────────────────────────────────────────────────────────────────────┐
    │                                                                                 │
    │                                                                                 │
    │        ┌─────────────────┐                    ┌─────────────────┐              │
    │        │  Register/Login │                    │   Admin Login   │              │
    │        └─────────────────┘                    └─────────────────┘              │
    │                │                                       │                       │
    │        ┌─────────────────┐                    ┌─────────────────┐              │
    │        │ Browse Products │                    │ Manage Products │              │
    │        └─────────────────┘                    └─────────────────┘              │
    │                │                                       │                       │
    │        ┌─────────────────┐                    ┌─────────────────┐              │
    │        │ Search Products │                    │  Manage Orders  │              │
    │        └─────────────────┘                    └─────────────────┘              │
    │                │                                       │                       │
┌───┤        ┌─────────────────┐                    ┌─────────────────┐              ├───┐
│   │        │   Add to Cart   │                    │Manage Customers │              │   │
│   │        └─────────────────┘                    └─────────────────┘              │   │
│   │                │                                       │                       │   │
│   │        ┌─────────────────┐                    ┌─────────────────┐              │   │
│   │        │ View Cart Items │                    │Manage Suppliers │              │   │
│   │        └─────────────────┘                    └─────────────────┘              │   │
│   │                │                                       │                       │   │
│   │        ┌─────────────────┐                    ┌─────────────────┐              │   │
│   │        │Checkout & Order │                    │Manage Inventory │              │   │
│   │        └─────────────────┘                    └─────────────────┘              │   │
│   │                │                                       │                       │   │
│   │        ┌─────────────────┐                    ┌─────────────────┐              │   │
│   │        │  Track Orders   │                    │  View Dashboard │              │   │
│   │        └─────────────────┘                    └─────────────────┘              │   │
│   │                │                                       │                       │   │
│   │        ┌─────────────────┐                    ┌─────────────────┐              │   │
│   │        │ Update Profile  │                    │ Generate Reports│              │   │
│   │        └─────────────────┘                    └─────────────────┘              │   │
│   │                                                                                 │   │
│   └─────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                         │
│                                                                                         │
┌─────┐                                                                             ┌─────┐
│     │                                                                             │     │
│  C  │                                                                             │  A  │
│  U  │                                                                             │  D  │
│  S  │                                                                             │  M  │
│  T  │                                                                             │  I  │
│  O  │                                                                             │  N  │
│  M  │                                                                             │     │
│  E  │                                                                             │     │
│  R  │                                                                             │     │
└─────┘                                                                             └─────┘
```

## System Flow Description

### Customer Side (Left):
1. **Register/Login** → Access customer portal
2. **Browse Products** → View product catalog
3. **Search Products** → Find specific items
4. **Add to Cart** → Select products for purchase
5. **View Cart Items** → Review selected products
6. **Checkout & Order** → Complete purchase process
7. **Track Orders** → Monitor order status
8. **Update Profile** → Manage account information

### Admin Side (Right):
1. **Admin Login** → Access admin panel
2. **Manage Products** → Add/edit/delete products
3. **Manage Orders** → Process customer orders
4. **Manage Customers** → Handle customer accounts
5. **Manage Suppliers** → Maintain supplier relationships
6. **Manage Inventory** → Control stock levels
7. **View Dashboard** → Monitor system statistics
8. **Generate Reports** → Create business analytics

### Key Features:
- **Dual Access System**: Separate customer and admin interfaces
- **Complete E-commerce Flow**: From browsing to order completion
- **Comprehensive Management**: Full admin control over all aspects
- **User-Friendly Design**: Intuitive navigation for both user types
- **Secure Authentication**: Role-based access control
- **Order Tracking**: Real-time status updates
- **Inventory Management**: Stock control and monitoring