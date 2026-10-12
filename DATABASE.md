# Database Schema & Entity Catalog: pos_saas

## 1. Overview
The database `pos_saas` utilizes MySQL 8.x with `utf8mb4_unicode_ci` encoding. It enforces strict foreign keys, cascading deletes where appropriate, and indexed columns for fast queries.

---

## 2. Table Catalog

### A. Authentication & Multitenancy
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `tenants` | Primary business entity in SaaS platform | None |
| `tenant_settings` | Tax, currency, and custom configuration | `tenant_id -> tenants.id` |
| `branches` | Physical stores / outlets (Dhaka, CTG, Sylhet) | `tenant_id -> tenants.id` |
| `warehouses` | Storage godowns and fulfillment centers | `tenant_id`, `branch_id` |
| `users` | Staff, cashiers, managers, and owners | `tenant_id`, `branch_id` |

### B. SaaS Plans, Billing & Subscriptions
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `subscription_plans` | SaaS tiers (Starter, Business, Pro) with quota limits | None |
| `addons` | Extra branches, extra users, extra SMS packs | None |
| `subscriptions` | Active tenant subscriptions and renewal dates | `tenant_id`, `plan_id` |
| `subscription_invoices` | Billed subscription invoices | `tenant_id`, `plan_id` |
| `payment_transactions` | bKash, Nagad, Rocket, SSLCommerz transactions | `tenant_id` |

### C. Catalog & Branch Inventory
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `categories` | Product categories | `tenant_id` |
| `brands` | Product brands | `tenant_id` |
| `units` | Measurement units (pcs, kg, ltr) | `tenant_id` |
| `products` | Core catalog with SKU and barcode | `tenant_id`, `category_id`, `brand_id`, `unit_id` |
| `branch_product_prices` | Branch-specific price overrides | `tenant_id`, `branch_id`, `product_id` |
| `branch_product_stocks` | Physical inventory per branch | `tenant_id`, `branch_id`, `product_id` |
| `stock_movements` | Immutable audit log of stock movements | `tenant_id`, `branch_id`, `product_id` |
| `stock_transfers` | Branch-to-branch inventory transfers | `tenant_id`, `from_branch_id`, `to_branch_id` |

### D. CRM, Suppliers & Purchases
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `suppliers` | Vendors and wholesalers | `tenant_id` |
| `supplier_ledgers` | Double-entry supplier transactions and dues | `tenant_id`, `supplier_id` |
| `customers` | Retail and wholesale customers | `tenant_id` |
| `customer_ledgers` | Due tracking and ledger history | `tenant_id`, `customer_id` |
| `purchases` | Inward purchase orders and bills | `tenant_id`, `branch_id`, `supplier_id` |
| `purchase_items` | Line items for purchases | `purchase_id`, `product_id` |

### E. Point of Sale (POS) & Sales Records
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `cash_shifts` | Cash drawer shifts with opening/closing cash | `tenant_id`, `branch_id`, `user_id` |
| `sales` | Completed retail transactions and invoices | `tenant_id`, `branch_id`, `customer_id`, `sold_by` |
| `sale_items` | Items sold in transaction with cost & price snapshot | `sale_id`, `product_id` |
| `sale_payments` | Split payments (Cash, bKash, Card) per sale | `sale_id`, `tenant_id` |
| `sale_returns` | Product returns and refunds | `sale_id`, `tenant_id`, `branch_id` |

### F. Accounting, Employees & Notifications
| Table Name | Description | Key Foreign Keys |
| :--- | :--- | :--- |
| `employees` | Staff profiles, salaries, and commission rates | `tenant_id`, `branch_id`, `user_id` |
| `accounts` | Cash in hand, bank accounts, mobile wallets | `tenant_id`, `branch_id` |
| `expenses` | Operating expenses (Rent, utilities, salaries) | `tenant_id`, `branch_id`, `category_id` |
| `sms_templates` | Bengali & English customizable SMS templates | `tenant_id` |
| `audit_logs` | Security and compliance action log | `tenant_id`, `user_id` |
