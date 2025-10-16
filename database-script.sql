-- =====================================================================
-- Retail Stock System (Uganda) - Full Database Schema (MySQL 8 / MariaDB)
-- Money = UGX integers (no cents). Comments explain each field.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 0) System Settings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS system_settings (
    `key` VARCHAR(100) NOT NULL COMMENT 'Setting name (unique key)',
    `value` TEXT NOT NULL COMMENT 'Setting value (JSON or text)',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update time',
    PRIMARY KEY (`key`)
) ENGINE = InnoDB COMMENT = 'Key/value app-wide settings (e.g., shop name, flags)';
-- ---------------------------------------------------------------------
-- 1) RBAC: Roles, Permissions, Assignments, Users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Role ID',
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Role code/name (e.g., OWNER)',
    description VARCHAR(255) DEFAULT NULL COMMENT 'What this role is for',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'User roles (OWNER, MANAGER, SALES_ASSOCIATE, etc.)';
CREATE TABLE IF NOT EXISTS permissions (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Permission ID',
    code VARCHAR(80) NOT NULL UNIQUE COMMENT 'Machine code (e.g., PRODUCT_CREATE)',
    description VARCHAR(255) DEFAULT NULL COMMENT 'Human description of permission',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Fine-grained permissions used by RBAC';
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL COMMENT 'FK to roles.id',
    permission_id INT NOT NULL COMMENT 'FK to permissions.id',
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
) ENGINE = InnoDB COMMENT = 'Many-to-many: roles granted specific permissions';
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'User ID',
    name VARCHAR(120) NOT NULL COMMENT 'Full name',
    email VARCHAR(160) NOT NULL UNIQUE COMMENT 'Login email (unique)',
    phone VARCHAR(30) DEFAULT NULL COMMENT 'Phone contact',
    password_hash VARCHAR(255) NOT NULL COMMENT 'BCrypt/Argon2 hash',
    pin_hash VARCHAR(255) DEFAULT NULL COMMENT 'Optional POS PIN (hashed)',
    status TINYINT NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Disabled',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update time',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'User accounts';
CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT NOT NULL COMMENT 'FK to users.id',
    role_id INT NOT NULL COMMENT 'FK to roles.id',
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When role assigned',
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE = InnoDB COMMENT = 'Users can have multiple roles';
CREATE TABLE IF NOT EXISTS user_devices (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Device record ID',
    user_id INT NOT NULL COMMENT 'FK to users.id',
    platform ENUM('ANDROID', 'IOS', 'WEB') NOT NULL DEFAULT 'ANDROID' COMMENT 'Device platform',
    fcm_token VARCHAR(255) NOT NULL COMMENT 'Push token for notifications',
    last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last heartbeat',
    UNIQUE KEY uq_user_token (user_id, fcm_token),
    FOREIGN KEY (user_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Registered devices for push notifications';
CREATE TABLE IF NOT EXISTS refresh_tokens (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Refresh token ID',
    user_id INT NOT NULL COMMENT 'FK to users.id',
    token_hash CHAR(64) NOT NULL COMMENT 'SHA-256 of refresh token',
    expires_at DATETIME NOT NULL COMMENT 'Token expiry',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Issued at',
    revoked_at DATETIME DEFAULT NULL COMMENT 'If revoked, when',
    UNIQUE KEY uq_token_hash (token_hash),
    FOREIGN KEY (user_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Optional refresh tokens for auth flow';
-- ---------------------------------------------------------------------
-- 2) Master Data: Stores, Categories, Units, Products, Suppliers, Customers
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stores (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Store/branch ID',
    name VARCHAR(120) NOT NULL COMMENT 'Store name',
    location VARCHAR(255) DEFAULT NULL COMMENT 'Physical address/area',
    is_active TINYINT NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Closed',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Store/branch master';
CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Category ID',
    name VARCHAR(120) NOT NULL COMMENT 'Category name',
    parent_id INT DEFAULT NULL COMMENT 'Parent category for hierarchy',
    UNIQUE KEY uq_cat_name (name),
    FOREIGN KEY (parent_id) REFERENCES categories(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Product categories (can be hierarchical)';
-- Free-text units list for suggestions (no FK from products to keep units open)
CREATE TABLE IF NOT EXISTS units (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Unit ID',
    name VARCHAR(32) NOT NULL UNIQUE COMMENT 'Unit text (e.g., pcs, kg, litre)',
    is_system TINYINT NOT NULL DEFAULT 0 COMMENT '1=seeded/common unit',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Optional units list for UI suggestions (not enforced)';
CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Product ID',
    name VARCHAR(200) NOT NULL COMMENT 'Product display name',
    sku VARCHAR(80) DEFAULT NULL COMMENT 'Internal code/SKU (unique if used)',
    short_code VARCHAR(20) DEFAULT NULL COMMENT 'Quick code for fast search',
    barcode VARCHAR(64) DEFAULT NULL COMMENT 'Barcode if available',
    unit VARCHAR(32) NOT NULL DEFAULT 'pcs' COMMENT 'Unit of measure (free text)',
    pack_size INT DEFAULT NULL COMMENT 'Items per pack/carton (if any)',
    category_id INT DEFAULT NULL COMMENT 'FK to categories.id',
    reorder_level INT NOT NULL DEFAULT 0 COMMENT 'Minimum qty before alert',
    price INT NOT NULL DEFAULT 0 COMMENT 'Selling price (UGX per unit)',
    cost_method ENUM('MOVING_AVG', 'FIFO') NOT NULL DEFAULT 'MOVING_AVG' COMMENT 'COGS method',
    moving_avg_cost INT NOT NULL DEFAULT 0 COMMENT 'Current average cost (UGX)',
    is_active TINYINT NOT NULL DEFAULT 1 COMMENT '1=Sellable, 0=Archived',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update time',
    UNIQUE KEY uq_sku (sku),
    UNIQUE KEY uq_barcode (barcode),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Products (pricing, unit, category, costs)';
CREATE TABLE IF NOT EXISTS product_barcodes (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Row ID',
    product_id INT NOT NULL COMMENT 'FK to products.id',
    barcode VARCHAR(64) NOT NULL COMMENT 'Additional barcode',
    UNIQUE KEY uq_prod_barcode (product_id, barcode),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Optional multiple barcodes per product';
CREATE TABLE IF NOT EXISTS suppliers (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Supplier ID',
    name VARCHAR(160) NOT NULL COMMENT 'Supplier name',
    phone VARCHAR(30) DEFAULT NULL COMMENT 'Phone contact',
    contact_name VARCHAR(120) DEFAULT NULL COMMENT 'Person to contact',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Suppliers (for purchases)';
CREATE TABLE IF NOT EXISTS customers (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Customer ID',
    name VARCHAR(160) NOT NULL COMMENT 'Customer name',
    phone VARCHAR(30) DEFAULT NULL COMMENT 'Phone contact',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Customers (for credit sales)';
-- ---------------------------------------------------------------------
-- 3) Inventory & Purchasing
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS purchases (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Purchase (GRN) ID',
    store_id INT NOT NULL DEFAULT 1 COMMENT 'Store receiving stock',
    supplier_id INT NOT NULL COMMENT 'FK to suppliers.id',
    invoice_no VARCHAR(80) DEFAULT NULL COMMENT 'Supplier invoice number',
    invoice_date DATE NOT NULL COMMENT 'Invoice date',
    transport_cost INT NOT NULL DEFAULT 0 COMMENT 'Transport cost (UGX)',
    other_cost INT NOT NULL DEFAULT 0 COMMENT 'Other cost (UGX)',
    total_cost INT NOT NULL DEFAULT 0 COMMENT 'Total purchase cost (UGX)',
    created_by INT NOT NULL COMMENT 'User who recorded purchase',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    INDEX idx_purchases_supplier_date (supplier_id, invoice_date),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Stock-in from suppliers (purchase header)';
CREATE TABLE IF NOT EXISTS purchase_items (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Purchase line ID',
    purchase_id INT NOT NULL COMMENT 'FK to purchases.id',
    product_id INT NOT NULL COMMENT 'FK to products.id',
    qty INT NOT NULL COMMENT 'Qty received (units)',
    unit_cost INT NOT NULL COMMENT 'Unit cost (UGX)',
    INDEX idx_purchase_items_prod (product_id),
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Items within a purchase (lines)';
CREATE TABLE IF NOT EXISTS supplier_returns (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Return to supplier ID',
    store_id INT NOT NULL DEFAULT 1 COMMENT 'Store returning stock',
    supplier_id INT NOT NULL COMMENT 'FK to suppliers.id',
    return_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When returned',
    reason VARCHAR(255) DEFAULT NULL COMMENT 'Reason for return',
    created_by INT NOT NULL COMMENT 'User who recorded return',
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Returns back to supplier';
CREATE TABLE IF NOT EXISTS supplier_return_items (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Return line ID',
    supplier_return_id INT NOT NULL COMMENT 'FK to supplier_returns.id',
    product_id INT NOT NULL COMMENT 'FK to products.id',
    qty INT NOT NULL COMMENT 'Qty returned',
    unit_cost INT NOT NULL COMMENT 'Cost per unit (UGX)',
    FOREIGN KEY (supplier_return_id) REFERENCES supplier_returns(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Lines of items returned to supplier';
CREATE TABLE IF NOT EXISTS stock_adjustments (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Stock adjustment ID',
    store_id INT NOT NULL DEFAULT 1 COMMENT 'Store',
    product_id INT NOT NULL COMMENT 'FK to products.id',
    qty_diff INT NOT NULL COMMENT 'Positive=add, Negative=remove',
    reason ENUM('COUNT', 'DAMAGE', 'THEFT', 'EXPIRED', 'OTHER') NOT NULL DEFAULT 'COUNT' COMMENT 'Why adjust',
    note VARCHAR(255) DEFAULT NULL COMMENT 'Extra explanation',
    created_by INT NOT NULL COMMENT 'User who adjusted',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When adjusted',
    INDEX idx_adj_prod_time (product_id, created_at),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Manual corrections from counts/damages/etc.';
CREATE TABLE IF NOT EXISTS product_stock_balances (
    store_id INT NOT NULL COMMENT 'Store',
    product_id INT NOT NULL COMMENT 'Product',
    qty_on_hand INT NOT NULL DEFAULT 0 COMMENT 'Current on-hand qty',
    PRIMARY KEY (store_id, product_id),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE = InnoDB COMMENT = 'Materialized on-hand qty per store/product';
CREATE TABLE IF NOT EXISTS stock_ledger (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Ledger row ID',
    store_id INT NOT NULL DEFAULT 1 COMMENT 'Store',
    product_id INT NOT NULL COMMENT 'Product',
    source_type ENUM(
        'SALE',
        'PURCHASE',
        'ADJUST',
        'RETURN_SUPPLIER',
        'RETURN_CUSTOMER'
    ) NOT NULL COMMENT 'What caused movement',
    source_id INT NOT NULL COMMENT 'ID of the source document',
    qty_in INT NOT NULL DEFAULT 0 COMMENT 'Qty coming in',
    qty_out INT NOT NULL DEFAULT 0 COMMENT 'Qty going out',
    unit_cost INT NOT NULL DEFAULT 0 COMMENT 'Cost per unit (UGX)',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When posted',
    INDEX idx_ledger_prod_time (product_id, created_at),
    INDEX idx_ledger_source (source_type, source_id),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Audit trail of inventory movements';
-- ---------------------------------------------------------------------
-- 4) Sales, Returns & Customer Credit
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sales (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Sale ID',
    store_id INT NOT NULL COMMENT 'Store making the sale',
    cashier_id INT NOT NULL COMMENT 'User who posted sale',
    sale_no VARCHAR(40) NOT NULL COMMENT 'Human/printable sale number',
    sale_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When sold',
    pay_method ENUM('CASH', 'MOMO', 'OTHER', 'CREDIT') NOT NULL DEFAULT 'CASH' COMMENT 'Payment type',
    customer_id INT DEFAULT NULL COMMENT 'FK if CREDIT sale',
    paid_amount INT NOT NULL DEFAULT 0 COMMENT 'Amount received (UGX)',
    discount_total INT NOT NULL DEFAULT 0 COMMENT 'Total discount applied (UGX)',
    notes VARCHAR(255) DEFAULT NULL COMMENT 'Optional note',
    UNIQUE KEY uq_sale_no (sale_no),
    INDEX idx_sales_time_store (store_id, sale_time),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Sales header (one receipt)';
CREATE TABLE IF NOT EXISTS sale_items (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Sale line ID',
    sale_id INT NOT NULL COMMENT 'FK to sales.id',
    product_id INT NOT NULL COMMENT 'Product sold',
    qty INT NOT NULL COMMENT 'Qty sold',
    unit_price INT NOT NULL COMMENT 'Selling price per unit (UGX)',
    discount INT NOT NULL DEFAULT 0 COMMENT 'Line discount (UGX)',
    cost_at_sale INT NOT NULL DEFAULT 0 COMMENT 'Cost snapshot (UGX)',
    INDEX idx_sale_items_prod (product_id),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Items in a sale';
CREATE TABLE IF NOT EXISTS sales_returns (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Customer return ID',
    sale_id INT NOT NULL COMMENT 'Original sale header',
    return_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When returned',
    cashier_id INT NOT NULL COMMENT 'User who processed return',
    reason VARCHAR(255) DEFAULT NULL COMMENT 'Reason for return',
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Returns after sale';
CREATE TABLE IF NOT EXISTS sales_return_items (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Return line ID',
    sales_return_id INT NOT NULL COMMENT 'FK to sales_returns.id',
    product_id INT NOT NULL COMMENT 'Product returned',
    qty INT NOT NULL COMMENT 'Qty returned',
    refund_amount INT NOT NULL DEFAULT 0 COMMENT 'Refund/credit (UGX)',
    FOREIGN KEY (sales_return_id) REFERENCES sales_returns(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Lines within a customer return';
CREATE TABLE IF NOT EXISTS customer_credits (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Credit account entry ID',
    customer_id INT NOT NULL COMMENT 'Customer on credit',
    sale_id INT NOT NULL COMMENT 'Related sale',
    due_date DATE DEFAULT NULL COMMENT 'When payment is due',
    amount INT NOT NULL COMMENT 'Total credit amount (UGX)',
    balance INT NOT NULL COMMENT 'Outstanding balance (UGX)',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    INDEX idx_credit_customer (customer_id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Accounts receivable per credit sale';
CREATE TABLE IF NOT EXISTS customer_payments (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Customer payment ID',
    customer_id INT NOT NULL COMMENT 'Customer paying',
    credit_id INT NOT NULL COMMENT 'Which credit entry',
    paid_amount INT NOT NULL COMMENT 'Amount paid (UGX)',
    pay_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When paid',
    method ENUM('CASH', 'MOMO', 'OTHER') NOT NULL DEFAULT 'CASH' COMMENT 'Payment method',
    reference VARCHAR(80) DEFAULT NULL COMMENT 'Payment ref (e.g., MoMo txn)',
    received_by INT NOT NULL COMMENT 'User who received the money',
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (credit_id) REFERENCES customer_credits(id),
    FOREIGN KEY (received_by) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Payments made to reduce customer credit';
-- ---------------------------------------------------------------------
-- 5) Cash Management & Budgeting
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cash_sessions (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Cash drawer session ID',
    store_id INT NOT NULL COMMENT 'Store',
    cashier_id INT NOT NULL COMMENT 'User opening/closing session',
    opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Session start',
    opening_float INT NOT NULL DEFAULT 0 COMMENT 'Opening cash float (UGX)',
    closed_at DATETIME DEFAULT NULL COMMENT 'Session end',
    counted_cash INT DEFAULT NULL COMMENT 'Counted physical cash (UGX)',
    counted_momo INT DEFAULT NULL COMMENT 'Counted MobileMoney (UGX)',
    variance_cash INT DEFAULT NULL COMMENT 'Cash difference (UGX)',
    variance_momo INT DEFAULT NULL COMMENT 'MoMo difference (UGX)',
    INDEX idx_cash_sessions_store (store_id, opened_at),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Open/close till sessions with counts/variances';
CREATE TABLE IF NOT EXISTS cash_transactions (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Cash movement ID',
    store_id INT NOT NULL COMMENT 'Store',
    cashier_id INT NOT NULL COMMENT 'User posting tx',
    session_id INT DEFAULT NULL COMMENT 'Related cash session',
    tx_type ENUM('EXPENSE', 'DEPOSIT', 'WITHDRAWAL', 'ADJUSTMENT') NOT NULL COMMENT 'Type of movement',
    method ENUM('CASH', 'MOMO', 'OTHER') NOT NULL DEFAULT 'CASH' COMMENT 'Medium used',
    amount INT NOT NULL COMMENT 'Amount (UGX)',
    reason VARCHAR(255) DEFAULT NULL COMMENT 'Why (e.g., rent, float top-up)',
    reference VARCHAR(80) DEFAULT NULL COMMENT 'Ref/receipt number',
    tx_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When happened',
    approved_by INT DEFAULT NULL COMMENT 'Manager approval user ID',
    INDEX idx_cash_tx_store_time (store_id, tx_time),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    FOREIGN KEY (session_id) REFERENCES cash_sessions(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Non-sale cash movements (expenses, deposits, etc.)';
CREATE TABLE IF NOT EXISTS budgets (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Budget row ID',
    period_month CHAR(7) NOT NULL COMMENT 'YYYY-MM period',
    store_id INT NOT NULL COMMENT 'Store',
    category_id INT DEFAULT NULL COMMENT 'If targeting a category',
    sales_target INT NOT NULL DEFAULT 0 COMMENT 'Sales target (UGX)',
    cogs_target INT NOT NULL DEFAULT 0 COMMENT 'COGS target (UGX)',
    gm_target INT NOT NULL DEFAULT 0 COMMENT 'Gross margin target (UGX)',
    UNIQUE KEY uq_budget (period_month, store_id, category_id),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Monthly budgets per store/category';
-- ---------------------------------------------------------------------
-- 6) Payments Integration (future-proof)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Payment method ID',
    code VARCHAR(30) NOT NULL UNIQUE COMMENT 'Code (e.g., MTN_MOMO)',
    display_name VARCHAR(80) NOT NULL COMMENT 'Shown to users',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Payment methods supported';
CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Payment txn ID',
    sale_id INT DEFAULT NULL COMMENT 'Linked sale if any',
    method_id INT NOT NULL COMMENT 'FK to payment_methods.id',
    provider VARCHAR(40) DEFAULT NULL COMMENT 'Provider (MTN/AIRTEL/etc.)',
    provider_ref VARCHAR(120) DEFAULT NULL COMMENT 'Provider reference/txn id',
    payer_phone VARCHAR(30) DEFAULT NULL COMMENT 'Payer phone number',
    amount INT NOT NULL COMMENT 'Amount (UGX)',
    status ENUM('PENDING', 'SUCCESS', 'FAILED') NOT NULL DEFAULT 'PENDING' COMMENT 'State',
    raw_payload JSON DEFAULT NULL COMMENT 'Raw webhook/API payload',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created time',
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated time',
    INDEX idx_payment_status (status, created_at),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (method_id) REFERENCES payment_methods(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Individual payment transactions';
CREATE TABLE IF NOT EXISTS webhook_events (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Webhook log ID',
    provider VARCHAR(40) NOT NULL COMMENT 'Source system',
    event_type VARCHAR(60) NOT NULL COMMENT 'Type of event',
    payload_json JSON NOT NULL COMMENT 'Saved full payload',
    status ENUM('NEW', 'PROCESSED', 'ERROR') NOT NULL DEFAULT 'NEW' COMMENT 'Processing state',
    received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When received',
    processed_at DATETIME DEFAULT NULL COMMENT 'When handled',
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'All inbound webhook calls for auditing/retry';
-- ---------------------------------------------------------------------
-- 7) Notifications & Audit
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Notification ID',
    user_id INT NOT NULL COMMENT 'Recipient user',
    type VARCHAR(50) NOT NULL COMMENT 'Type (SALE_POSTED, LOW_STOCK, etc.)',
    payload_json JSON NOT NULL COMMENT 'Data for client',
    read_at DATETIME DEFAULT NULL COMMENT 'When marked read',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When created',
    FOREIGN KEY (user_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'In-app/push notifications addressed to users';
CREATE TABLE IF NOT EXISTS audit_log (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'Audit ID',
    user_id INT NOT NULL COMMENT 'Actor user',
    action VARCHAR(100) NOT NULL COMMENT 'What happened (CREATE_SALE, etc.)',
    entity VARCHAR(60) NOT NULL COMMENT 'Entity type (sale, product, etc.)',
    entity_id INT DEFAULT NULL COMMENT 'Entity row id',
    meta_json JSON DEFAULT NULL COMMENT 'Extra context (JSON)',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When',
    INDEX idx_audit_entity (entity, entity_id, created_at),
    FOREIGN KEY (user_id) REFERENCES users(id),
    PRIMARY KEY (id)
) ENGINE = InnoDB COMMENT = 'Who did what and when (compliance)';
-- ---------------------------------------------------------------------
-- 8) Reporting (pre-aggregated rollups for speed)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS report_daily_metrics (
    report_date DATE NOT NULL COMMENT 'Day (YYYY-MM-DD)',
    store_id INT NOT NULL COMMENT 'Store',
    total_sales INT NOT NULL DEFAULT 0 COMMENT 'Total sales (UGX)',
    total_cogs INT NOT NULL DEFAULT 0 COMMENT 'Total cost of goods sold (UGX)',
    gross_margin INT NOT NULL DEFAULT 0 COMMENT 'GM = sales - COGS (UGX)',
    sales_cash INT NOT NULL DEFAULT 0 COMMENT 'Cash sales (UGX)',
    sales_momo INT NOT NULL DEFAULT 0 COMMENT 'Mobile Money sales (UGX)',
    sales_other INT NOT NULL DEFAULT 0 COMMENT 'Other sales (UGX)',
    PRIMARY KEY (report_date, store_id),
    FOREIGN KEY (store_id) REFERENCES stores(id)
) ENGINE = InnoDB COMMENT = 'Daily KPIs per store';
CREATE TABLE IF NOT EXISTS report_monthly_metrics (
    period_month CHAR(7) NOT NULL COMMENT 'Month (YYYY-MM)',
    store_id INT NOT NULL COMMENT 'Store',
    total_sales INT NOT NULL DEFAULT 0 COMMENT 'Total sales (UGX)',
    total_cogs INT NOT NULL DEFAULT 0 COMMENT 'Total COGS (UGX)',
    gross_margin INT NOT NULL DEFAULT 0 COMMENT 'Gross margin (UGX)',
    sales_cash INT NOT NULL DEFAULT 0 COMMENT 'Cash sales (UGX)',
    sales_momo INT NOT NULL DEFAULT 0 COMMENT 'Mobile Money sales (UGX)',
    sales_other INT NOT NULL DEFAULT 0 COMMENT 'Other sales (UGX)',
    PRIMARY KEY (period_month, store_id),
    FOREIGN KEY (store_id) REFERENCES stores(id)
) ENGINE = InnoDB COMMENT = 'Monthly KPIs per store';
-- ---------------------------------------------------------------------
-- Seeds: Roles (including SALES_ASSOCIATE), Common Permissions, Units
-- ---------------------------------------------------------------------
INSERT IGNORE INTO roles (id, name, description)
VALUES (1, 'OWNER', 'System owner with full rights'),
    (2, 'MANAGER', 'Manages operations and approvals'),
    (3, 'CASHIER', 'Handles sales and cash'),
    (4, 'STOCKIST', 'Handles stock-in and counts'),
    (5, 'ACCOUNTANT', 'Finance and reports'),
    (6, 'SALES_ASSOCIATE', 'Frontline sales staff');
-- A comprehensive permission set covering the system
INSERT IGNORE INTO permissions (code, description)
VALUES -- Stores & Settings
    ('STORE_VIEW', 'View stores'),
    ('STORE_MANAGE', 'Create/edit stores'),
    ('SETTINGS_MANAGE', 'Change system settings'),
    ('SYSTEM_BACKUP', 'Export/backup data'),
    -- Categories & Units
    ('CATEGORY_VIEW', 'View categories'),
    ('CATEGORY_CREATE', 'Create categories'),
    ('CATEGORY_EDIT', 'Edit categories'),
    ('CATEGORY_DELETE', 'Delete categories'),
    ('UNIT_MANAGE', 'Manage suggested units'),
    -- Products
    ('PRODUCT_VIEW', 'View products'),
    ('PRODUCT_CREATE', 'Create products'),
    ('PRODUCT_EDIT', 'Edit products'),
    ('PRODUCT_DELETE', 'Archive/delete products'),
    ('PRODUCT_IMPORT', 'Bulk import products'),
    ('PRODUCT_EXPORT', 'Export products'),
    ('PRODUCT_VIEW_COST', 'View product cost fields'),
    -- Suppliers & Customers
    ('SUPPLIER_VIEW', 'View suppliers'),
    ('SUPPLIER_CREATE', 'Create suppliers'),
    ('SUPPLIER_EDIT', 'Edit suppliers'),
    ('SUPPLIER_DELETE', 'Delete suppliers'),
    ('CUSTOMER_VIEW', 'View customers'),
    ('CUSTOMER_CREATE', 'Create customers'),
    ('CUSTOMER_EDIT', 'Edit customers'),
    ('CUSTOMER_DELETE', 'Delete customers'),
    -- Purchasing
    ('PURCHASE_VIEW', 'View purchases'),
    ('PURCHASE_CREATE', 'Create purchases'),
    ('PURCHASE_EDIT', 'Edit purchases'),
    ('PURCHASE_DELETE', 'Delete purchases'),
    ('PURCHASE_APPROVE', 'Approve purchases/GRNs'),
    (
        'SUPPLIER_RETURN_CREATE',
        'Create supplier returns'
    ),
    ('SUPPLIER_RETURN_VIEW', 'View supplier returns'),
    -- Inventory / Stock
    ('INVENTORY_VIEW_BALANCE', 'View on-hand balances'),
    ('INVENTORY_VIEW_LEDGER', 'View stock ledger'),
    ('INVENTORY_ADJUST', 'Create stock adjustments'),
    ('INVENTORY_COUNT', 'Perform cycle counts'),
    -- Sales
    ('SALES_VIEW', 'View sales'),
    ('SALES_CREATE', 'Create sales'),
    ('SALES_EDIT', 'Edit sales'),
    ('SALES_VOID', 'Void sales'),
    ('SALES_REFUND', 'Process sales returns'),
    ('SALES_PRICE_OVERRIDE', 'Override selling price'),
    ('SALES_DISCOUNT_OVERRIDE', 'Apply high discount'),
    ('CREDIT_SALE_CREATE', 'Allow credit sales'),
    -- Cash
    ('CASH_SESSION_OPEN', 'Open cash session'),
    ('CASH_SESSION_CLOSE', 'Close cash session'),
    ('CASH_TX_CREATE', 'Record cash transaction'),
    ('CASH_TX_APPROVE', 'Approve cash transaction'),
    ('CASH_VARIANCE_APPROVE', 'Approve cash variances'),
    -- Budgets & Reports
    ('BUDGET_VIEW', 'View budgets'),
    ('BUDGET_CREATE', 'Create budgets'),
    ('BUDGET_EDIT', 'Edit budgets'),
    ('BUDGET_DELETE', 'Delete budgets'),
    ('BUDGET_APPROVE', 'Approve budgets'),
    ('REPORT_VIEW_DAILY', 'View daily reports'),
    ('REPORT_VIEW_WEEKLY', 'View weekly reports'),
    ('REPORT_VIEW_MONTHLY', 'View monthly reports'),
    ('REPORT_EXPORT', 'Export reports'),
    -- Payments & Webhooks
    ('PAYMENT_METHOD_MANAGE', 'Manage payment methods'),
    ('PAYMENT_TX_VIEW', 'View payment transactions'),
    ('PAYMENT_TX_REFUND', 'Refund payment transaction'),
    ('WEBHOOK_MANAGE', 'Manage webhooks and retries'),
    -- Notifications & Audit
    ('NOTIFICATION_SEND', 'Trigger notifications'),
    (
        'NOTIFICATION_MANAGE',
        'Manage notification settings'
    ),
    ('AUDIT_VIEW', 'View audit logs'),
    -- Users & Roles
    ('USER_VIEW', 'View users'),
    ('USER_CREATE', 'Create users'),
    ('USER_EDIT', 'Edit users'),
    ('USER_DISABLE', 'Disable users'),
    ('ROLE_VIEW', 'View roles'),
    ('ROLE_MANAGE', 'Create/edit roles'),
    (
        'PERMISSION_ASSIGN',
        'Assign permissions to roles/user'
    ),
    -- Data & Sync & AI
    (
        'IMPORT_EXPORT_DATA',
        'Import and export operational data'
    ),
    ('SYNC_RUN', 'Run background sync jobs'),
    (
        'AI_FEATURES_USE',
        'Access AI features when enabled'
    );
-- Optional: seed some common units (not enforced)
INSERT IGNORE INTO units (name, is_system)
VALUES ('pcs', 1),
    ('dozen', 1),
    ('kg', 1),
    ('litre', 1),
    ('bale', 1),
    ('carton', 1);