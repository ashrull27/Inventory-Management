# Inventory Management System - Laravel 11

A comprehensive RESTful API for managing inventory with transaction tracking, reporting, and authentication.

## 🚀 Features

- ✅ Product catalog management (CRUD operations)
- ✅ Stock transaction tracking (IN/OUT with validation)
- ✅ Real-time inventory summary with value calculations
- ✅ Grouped reports by category and transaction type
- ✅ API authentication using Laravel Sanctum
- ✅ Repository pattern implementation for clean architecture
- ✅ Comprehensive validation with custom form requests
- ✅ Automated testing suite (21 tests included)
- ✅ Soft deletes for safe data management
- ✅ Historical price tracking at transaction time

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+
- Apache (XAMPP)

## 🛠️ Installation & Setup

### Step 1: Clone the Repository
```bash
git clone https://github.com/ashrull27/inventory-management.git
cd inventory-management
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Create Database
Using phpMyAdmin or MySQL command line:
```sql
CREATE DATABASE inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or via command line:
```bash
mysql -u root -p -e "CREATE DATABASE inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 6: Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

This will create all tables and seed initial data:
- **1 Test User**: admin@example.com / password
- **3 Categories**: Electronics, Accessories, Supplies
- **5 Products**: Laptop, Keyboard, Mouse, A4 Paper, LAN Cable

### Step 7: Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

---

## 🔐 Authentication

All endpoints except `/register` and `/login` require authentication using Bearer tokens.

### Register a New User
```http
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "1|your-api-token-here"
    },
    "message": "User registered successfully"
}
```

### Login
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com"
        },
        "token": "1|your-api-token-here"
    },
    "message": "Login successful"
}
```

**Use the token in subsequent requests:**
```http
Authorization: Bearer 1|your-api-token-here
```

---

## 📡 API Endpoints

### Authentication Endpoints (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login and get token |

### Authentication Endpoints (Protected)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout (revoke token) |
| GET | `/api/user` | Get authenticated user |

### Product Endpoints (Protected)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | Get all products |
| GET | `/api/products/{id}` | Get single product |
| POST | `/api/products` | Create new product |
| PUT | `/api/products/{id}` | Update product |
| DELETE | `/api/products/{id}` | Delete product (soft delete) |

### Transaction Endpoints (Protected)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/transactions` | Get all transactions |
| GET | `/api/transactions/{id}` | Get single transaction |
| POST | `/api/transactions` | Create transaction (IN/OUT) |

### Report Endpoints (Protected)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reports/inventory-summary` | Current stock with values |
| GET | `/api/reports/by-category` | Grouped by category |
| GET | `/api/reports/by-type` | Grouped by IN/OUT |

---

## 📝 Sample API Requests

### Create Product
```http
POST http://localhost:8000/api/products
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "name": "Gaming Monitor",
    "category_id": 1,
    "unit_price": 550.00,
    "stock_quantity": 15
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "id": 6,
        "category_id": 1,
        "name": "Gaming Monitor",
        "unit_price": "550.00",
        "stock_quantity": 15,
        "category": {
            "id": 1,
            "name": "Electronics"
        }
    },
    "message": "Product created successfully"
}
```

### Create Transaction (Stock IN)
```http
POST http://localhost:8000/api/transactions
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "product_id": 1,
    "transaction_type": "IN",
    "quantity": 10,
    "remarks": "Restocking from supplier",
    "reference_number": "PO-2025-001"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "product_id": 1,
        "transaction_type": "IN",
        "quantity": 10,
        "unit_price": "3500.00",
        "remarks": "Restocking from supplier",
        "reference_number": "PO-2025-001",
        "transaction_time": "2025-11-15T10:30:00.000000Z",
        "product": {
            "id": 1,
            "name": "Laptop",
            "stock_quantity": 30
        }
    },
    "message": "Transaction created successfully"
}
```

### Create Transaction (Stock OUT)
```http
POST http://localhost:8000/api/transactions
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "product_id": 1,
    "transaction_type": "OUT",
    "quantity": 5,
    "remarks": "Sold to customer",
    "reference_number": "SO-2025-001"
}
```

**Note:** Stock OUT will fail with error if insufficient stock is available.

### Get Inventory Summary
```http
GET http://localhost:8000/api/reports/inventory-summary
Authorization: Bearer {your-token}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Laptop",
            "category": "Electronics",
            "unit_price": "3500.00",
            "stock_quantity": 25,
            "total_value": "87500.00"
        }
    ],
    "summary": {
        "total_items": 5,
        "total_stock_value": "152350.00"
    }
}
```

---

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with coverage (if xdebug installed)
php artisan test --coverage
```

**Expected Output:**
```
Tests:  21 passed (125 assertions)
Duration: ~1.2s
```

---

## 📊 Additional Questions - Implementation Answers

### a. Extensibility – New Category

**Question:** How would you modify your system to allow new product categories via an admin panel or API?

**Answer:**

The system already supports dynamic category management. To implement this:

**1. Create Category API Endpoints:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});
```

**2. Implement CategoryController:**
```php
class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'description' => 'nullable|string'
        ]);
        
        $category = Category::create($validated);
        
        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }
}
```

**3. Add Authorization (Admin Only):**
```php
// Create policy
php artisan make:policy CategoryPolicy

// In CategoryPolicy
public function create(User $user)
{
    return $user->is_admin; // Assuming is_admin field exists
}
```

**Benefits:**
- Dynamic category creation without code changes
- Validation prevents duplicates
- Soft deletes prevent orphaned products
- Can be extended with role-based permissions

---

### b. Extensibility – Pricing Updates

**Question:** What changes are needed if product pricing changes over time (e.g., maintaining price history)?

**Answer:**

**Create Price History Table:**

```php
// Migration
Schema::create('price_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->decimal('old_price', 10, 2);
    $table->decimal('new_price', 10, 2);
    $table->foreignId('changed_by')->constrained('users');
    $table->timestamp('effective_from');
    $table->timestamp('effective_to')->nullable();
    $table->string('reason')->nullable();
    $table->timestamps();
    
    $table->index(['product_id', 'effective_from']);
});
```

**Implementation using Observer Pattern:**

```php
// app/Observers/ProductObserver.php
class ProductObserver
{
    public function updating(Product $product)
    {
        if ($product->isDirty('unit_price')) {
            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => $product->getOriginal('unit_price'),
                'new_price' => $product->unit_price,
                'changed_by' => auth()->id(),
                'effective_from' => now(),
            ]);
            
            // Close previous price history
            PriceHistory::where('product_id', $product->id)
                ->whereNull('effective_to')
                ->update(['effective_to' => now()]);
        }
    }
}
```

**Key Benefits:**
- **Historical Accuracy**: Transaction table already stores `unit_price` at transaction time
- **Audit Trail**: Complete price change history with user tracking
- **Profit Analysis**: Calculate margins based on purchase vs sale prices
- **Compliance**: Meets accounting and regulatory requirements
- **Reporting**: Query prices at any point in time

**API Endpoint:**
```http
GET /api/products/{id}/price-history
```

---

### c. Low Stock Alerts

**Question:** How would you extend your system to trigger alerts when stock levels drop below a threshold?

**Answer:**

**1. Database Schema Extension:**

```php
// Add to products table migration
$table->integer('reorder_level')->default(10);
$table->integer('minimum_stock')->default(5);
$table->boolean('alert_enabled')->default(true);
```

**2. Create Notification System:**

```php
// app/Notifications/LowStockAlert.php
class LowStockAlert extends Notification
{
    public function __construct(public Product $product) {}
    
    public function via($notifiable)
    {
        return ['mail', 'database', 'slack'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->line("Stock for {$this->product->name} is running low!")
            ->line("Current stock: {$this->product->stock_quantity}")
            ->line("Reorder level: {$this->product->reorder_level}")
            ->action('View Product', url('/products/' . $this->product->id));
    }
    
    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock_quantity,
            'reorder_level' => $this->product->reorder_level,
            'alert_level' => $this->product->stock_quantity < $this->product->minimum_stock 
                ? 'critical' : 'warning'
        ];
    }
}
```

**3. Event-Driven Implementation:**

```php
// app/Events/StockUpdated.php
class StockUpdated
{
    public function __construct(public Product $product) {}
}

// app/Listeners/CheckStockLevel.php
class CheckStockLevel
{
    public function handle(StockUpdated $event)
    {
        $product = $event->product;
        
        if ($product->alert_enabled && 
            $product->stock_quantity <= $product->reorder_level) {
            
            // Notify procurement team
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new LowStockAlert($product));
            
            // Log the alert
            Log::warning("Low stock alert: {$product->name}", [
                'product_id' => $product->id,
                'current_stock' => $product->stock_quantity,
                'reorder_level' => $product->reorder_level
            ]);
        }
    }
}
```

**4. Trigger in TransactionRepository:**

```php
// After updating stock
event(new StockUpdated($product));
```

**5. Scheduled Daily Check:**

```php
// app/Console/Commands/CheckLowStock.php
class CheckLowStock extends Command
{
    public function handle()
    {
        $lowStockProducts = Product::where('alert_enabled', true)
            ->whereRaw('stock_quantity <= reorder_level')
            ->get();
            
        foreach ($lowStockProducts as $product) {
            event(new StockUpdated($product));
        }
    }
}

// Schedule in app/Console/Kernel.php
$schedule->command('stock:check-levels')->daily();
```

**6. API Endpoints:**

```http
GET /api/alerts/low-stock - View all low stock items
PUT /api/products/{id}/reorder-level - Set threshold
GET /api/notifications - View all notifications
POST /api/notifications/{id}/mark-as-read
```

**Advanced Features:**
- **Predictive Alerts**: Analyze consumption patterns to predict stock-out dates
- **Multi-Channel**: Email, SMS, Slack, webhook notifications
- **Auto-Reorder**: Trigger purchase orders automatically
- **Dashboard Widget**: Real-time low stock monitoring
- **Supplier Integration**: Send alerts directly to suppliers

---

### d. (Optional) Multi-Branch Support

**Question:** How would you model inventory tracking for multiple branches or warehouses?

**Answer:**

**1. Database Schema:**

```php
// Branches Table
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // WH001, BR001, etc.
    $table->string('name');
    $table->enum('type', ['warehouse', 'retail', 'online']);
    $table->text('address');
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->foreignId('manager_id')->nullable()->constrained('users');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});

// Branch Inventory Table
Schema::create('branch_inventory', function (Blueprint $table) {
    $table->id();
    $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->integer('stock_quantity')->default(0);
    $table->integer('reserved_quantity')->default(0); // For pending orders
    $table->integer('reorder_level')->nullable();
    $table->timestamps();
    
    $table->unique(['branch_id', 'product_id']);
    $table->index('branch_id');
    $table->index('product_id');
});

// Enhanced Transactions Table
Schema::table('transactions', function (Blueprint $table) {
    $table->foreignId('from_branch_id')->nullable()->constrained('branches');
    $table->foreignId('to_branch_id')->nullable()->constrained('branches');
    $table->enum('transaction_type', ['IN', 'OUT', 'TRANSFER', 'ADJUSTMENT']);
});
```

**2. Transaction Types:**

- **IN**: Stock received from supplier → specific branch
- **OUT**: Stock sold/issued → from specific branch
- **TRANSFER**: Move stock between branches
- **ADJUSTMENT**: Inventory count corrections

**3. Models & Relationships:**

```php
// Branch Model
class Branch extends Model
{
    public function inventory()
    {
        return $this->hasMany(BranchInventory::class);
    }
    
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    
    public function transactionsFrom()
    {
        return $this->hasMany(Transaction::class, 'from_branch_id');
    }
    
    public function transactionsTo()
    {
        return $this->hasMany(Transaction::class, 'to_branch_id');
    }
}

// BranchInventory Model
class BranchInventory extends Model
{
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function getAvailableQuantityAttribute()
    {
        return $this->stock_quantity - $this->reserved_quantity;
    }
}
```

**4. Transfer Transaction Example:**

```php
// TransferController
public function transfer(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'from_branch_id' => 'required|exists:branches,id',
        'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
        'quantity' => 'required|integer|min:1',
        'remarks' => 'nullable|string',
        'reference_number' => 'nullable|string'
    ]);
    
    DB::transaction(function () use ($validated) {
        // Check source branch has sufficient stock
        $fromInventory = BranchInventory::where('branch_id', $validated['from_branch_id'])
            ->where('product_id', $validated['product_id'])
            ->lockForUpdate()
            ->firstOrFail();
            
        if ($fromInventory->available_quantity < $validated['quantity']) {
            throw new \Exception('Insufficient stock at source branch');
        }
        
        // Decrease from source
        $fromInventory->decrement('stock_quantity', $validated['quantity']);
        
        // Increase at destination
        BranchInventory::updateOrCreate(
            [
                'branch_id' => $validated['to_branch_id'],
                'product_id' => $validated['product_id']
            ],
            []
        )->increment('stock_quantity', $validated['quantity']);
        
        // Record transaction
        Transaction::create([
            'product_id' => $validated['product_id'],
            'from_branch_id' => $validated['from_branch_id'],
            'to_branch_id' => $validated['to_branch_id'],
            'transaction_type' => 'TRANSFER',
            'quantity' => $validated['quantity'],
            'unit_price' => Product::find($validated['product_id'])->unit_price,
            'remarks' => $validated['remarks'],
            'reference_number' => $validated['reference_number'],
            'transaction_time' => now()
        ]);
    });
    
    return response()->json([
        'success' => true,
        'message' => 'Transfer completed successfully'
    ]);
}
```

**5. API Endpoints:**

```http
GET /api/branches - List all branches
GET /api/branches/{id} - Get branch details
POST /api/branches - Create new branch
PUT /api/branches/{id} - Update branch
GET /api/branches/{id}/inventory - Branch-specific inventory
POST /api/transactions/transfer - Transfer between branches
GET /api/reports/inventory-by-branch - Multi-branch summary
GET /api/reports/branch-comparison - Compare branches
```

**6. Advanced Features:**

- **Smart Allocation**: Algorithm to distribute stock based on sales velocity
- **Automatic Transfer Requests**: When branch stock is low
- **Branch Performance Metrics**: Sales, turnover rate, profit per branch
- **Role-Based Access**: Branch managers see only their branch
- **Stock Reservation**: Hold stock for pending orders
- **Inter-branch Orders**: Branch can request from another branch

**Benefits:**
- Centralized inventory visibility across all locations
- Optimized stock distribution
- Reduced stockouts and overstocking
- Better demand forecasting per location
- Efficient inter-branch transfers
- Branch-level reporting and analytics

---

## 🗂️ Project Structure

```
inventory-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   ├── TransactionController.php
│   │   │   └── ReportController.php
│   │   └── Requests/
│   │       ├── RegisterRequest.php
│   │       ├── LoginRequest.php
│   │       └── StoreTransactionRequest.php
│   ├── Interfaces/
│   │   ├── ProductRepositoryInterface.php
│   │   └── TransactionRepositoryInterface.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Transaction.php
│   │   └── User.php
│   ├── Repositories/
│   │   ├── ProductRepository.php
│   │   └── TransactionRepository.php
│   └── Providers/
│       └── RepositoryServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_14_055225_create_categories_table.php
│   │   ├── 2025_11_14_055231_create_products_table.php
│   │   └── 2025_11_14_055236_create_transactions_table.php
│   ├── seeders/
│   │   ├── CategorySeeder.php
│   │   ├── ProductSeeder.php
│   │   └── DatabaseSeeder.php
│   └── factories/
│       ├── CategoryFactory.php
│       ├── ProductFactory.php
│       └── TransactionFactory.php
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
│       ├── ProductTest.php
│       ├── TransactionTest.php
│       └── ReportTest.php
├── .env.example
├── composer.json
├── README.md
└── Inventory_API.postman_collection.json
```


## 🎯 Design Patterns Used

1. **Repository Pattern**: Separates data access logic from business logic
2. **Dependency Injection**: Controllers depend on interfaces, not concrete classes
3. **Service Provider**: Custom RepositoryServiceProvider for binding
4. **Form Request Validation**: Dedicated validation classes
5. **Observer Pattern**: (Suggested for price history and alerts)

---

## 🔒 Security Features

- ✅ API authentication using Laravel Sanctum
- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Mass assignment protection
- ✅ Input validation and sanitization
- ✅ Password hashing (bcrypt)
- ✅ Rate limiting on API routes

---

## 📊 Database Schema

### Categories Table
```sql
- id (PK)
- name (unique)
- description
- timestamps
- soft_deletes
```

### Products Table
```sql
- id (PK)
- category_id (FK → categories)
- name
- unit_price (decimal 10,2)
- stock_quantity (integer)
- timestamps
- soft_deletes
```

### Transactions Table
```sql
- id (PK)
- product_id (FK → products)
- transaction_type (enum: IN/OUT)
- quantity (integer)
- unit_price (decimal 10,2) -- stored at transaction time
- remarks (nullable)
- reference_number (nullable)
- transaction_time (datetime)
- timestamps
```

### Users Table (Laravel Sanctum)
```sql
- id (PK)
- name
- email (unique)
- password (hashed)
- timestamps
```

## 📦 Postman Collection

Import `Inventory_API.postman_collection.json` into Postman for quick testing.

The collection includes:
- All authentication endpoints
- Product CRUD operations
- Transaction creation and listing
- All report endpoints
- Pre-configured environment variables

**How to use:**
1. Import the collection into Postman
2. Run the "Login" request first
3. Token will be automatically saved
4. Test other endpoints

---

## 📄 License

This project is created for assessment purposes.

## 👤 Author

Ashrull - Junior Developer Analyst Assessment (Candidate)

---
**Repository**: https://github.com/ashrull27/Inventory-Management
