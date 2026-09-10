<!-- Phase 1 — Project Setup

Created Laravel 11 project (ScanDine)
Created MySQL database scandine_database
Configured .env with DB connection
git init, .gitignore, first commit + push

Phase 2 — Authentication
5. Installed Laravel Breeze (Blade stack) → composer require laravel/breeze --dev → php artisan breeze:install blade
6. Ran Breeze's migrations (added password_reset_tokens, etc. on top of default users/cache/jobs)
7. Tested register/login/logout worked
8. Created a new migration add_role_to_users_table → added role enum column (staff/manager/owner, default staff) to users
9. Added 'role' to $fillable in User.php (this was missing at first, caused the seeder error later)

Phase 3 — Database Design
10. Ran php artisan make:model X -m for 5 models: RestaurantTable, Category, FoodItem, CustomerOrder, OrderItem (creates model + migration together)
11. Filled in each migration's up() with the actual columns (table_number/qr_token, name/description, price/unit_cost/category_id, restaurant_table_id/order_status, customer_order_id/food_item_id/item_name_snapshot etc.)
12. Ran php artisan migrate (later had to run migrate:fresh once, after discovering the role migration was edited after it had already run once)
13. Added relationship methods to all 5 models (hasMany/belongsTo pairs)
14. Added $fillable arrays to all 5 models (also initially missing — second bug caught)

Phase 4 — Seed Data
15. Created 4 seeders: UserSeeder, CategorySeeder, RestaurantTableSeeder, FoodItemSeeder
16. Wrote fake data into each (3 test accounts, 3 categories, 5 tables, 3 food items)
17. Registered all 4 in DatabaseSeeder.php via $this->call([...])
18. Ran php artisan db:seed — succeeded after the role column fix
19. Verified the data visually in MySQL Workbench

Phase 5 — Role-Based Access
20. Created CheckRole middleware (php artisan make:middleware CheckRole)
21. Wrote the permission-check logic inside handle() (fixed a ...$roles spread-operator bug along the way)
22. Registered it as the 'role' alias in bootstrap/app.php
23. Added a test route /owner-test with middleware(['auth', 'role:owner'])
24. Verified it works both ways — owner passes, staff gets blocked with 403 -->
