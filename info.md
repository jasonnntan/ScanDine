<!-- install breeze

composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
php artisan migrate -->

# DineFlow — Restaurant QR Food Ordering System

## Project Goal

Build a restaurant food ordering system using Laravel 11, Blade, and MySQL.
Customers scan a QR code at their table, browse the menu, add items to a cart, and submit an order. No customer login or online payment gateway is required.
Restaurant staff receive and process orders. Managers and owners manage menu items, staff accounts, and sales reports.

## Technology Stack

- Backend: Laravel 11
- Database: MySQL
- Frontend: Laravel Blade + CSS/JavaScript
- Authentication: Laravel Breeze (for restaurant staff only)
- Charts: ECharts via CDN
- Image uploads: Laravel storage

## User Roles

| Role     | Permissions                                                                                 |
| -------- | ------------------------------------------------------------------------------------------- |
| Customer | Scan QR, browse menu, add items to cart, place an order                                     |
| Staff    | View orders, update order status, mark food as out of stock                                 |
| Manager  | Staff permissions + manage menu, categories, prices, and staff accounts; view sales reports |
| Owner    | Full access, including manager accounts, revenue, profit, and best‑seller analytics         |

> Use owner as the highest role. No separate admin role is needed unless required.

## Main Features

### Customer Side

- Scan a QR code assigned to a restaurant table
- View food and drink categories
- Browse menu items with image, description, price, and availability
- Add items to cart
- Add item notes, for example "less spicy" or "no ice"
- Submit order with optional customer name, phone number, and order note
- See order confirmation page
- No customer account and no payment gateway

### Staff Dashboard

- View incoming orders
- Filter orders by status, date, and table
- Update order flow:
  `Pending → Preparing → Ready → Served → Completed`
- Cancel an order if needed
- Mark food items as available or out of stock
- Cannot change food prices or access financial reports

### Manager Dashboard

- All staff features
- Manage categories
- Add, edit, and delete food items
- Upload food images
- Update prices and food costs
- Manage staff accounts
- View sales summaries and order reports

### Owner Dashboard

- All manager features
- Manage manager accounts
- View sales charts and reports:
    - Today's revenue
    - Number of orders
    - Best‑selling food items
    - Sales by category
    - Daily/weekly/monthly sales
    - Estimated profit

## Database Design

### users

Laravel Breeze's default users table, with an added role column.

- id
- name
- email
- password
- role // staff, manager, owner
- created_at
- updated_at

### restaurant_tables

Each table has a QR token. The QR code opens the menu for the correct table.

- id
- table_number
- qr_token
- status // active, inactive
- created_at
- updated_at

### categories

- id
- name
- description
- created_at
- updated_at

> Examples: Main Course, Snacks, Desserts, Drinks.

### food_items

- id
- category_id
- name
- description
- price
- unit_cost // needed to calculate estimated profit
- image
- status // available, out_of_stock
- created_at
- updated_at

### customer_orders

- id
- restaurant_table_id
- customer_name // optional
- customer_phone // optional
- customer_note // optional
- total_amount
- order_status // pending, preparing, ready, served, completed, cancelled
- payment_status // unpaid, paid_at_counter, paid
- created_at
- updated_at

### order_items

> Important: save the food name and price at the time of ordering. Old order records must not change when a manager changes the menu price later.

- id
- customer_order_id
- food_item_id
- item_name_snapshot
- unit_price
- quantity
- item_note
- created_at
- updated_at

## Sales Calculation

- Revenue = total of completed orders
- Estimated profit = (item selling price - item unit cost) × quantity
- If unit_cost is not included, show revenue only—not profit.

## Development Plan

### Phase 1 — Project Setup

- Create Laravel 11 project
- Configure MySQL connection in .env
- Initialize Git repository
- Add .gitignore

### Phase 2 — Authentication

- Install Laravel Breeze with Blade stack
- Run migrations
- Confirm registration, login, logout, and protected routes work
- Add role column to users table

### Phase 3 — Database Design

- Create migrations for:
    - restaurant tables
    - categories
    - food items
    - customer orders
    - order items
- Define model relationships

### Phase 4 — Seed Data

- Create test accounts:
    - one owner
    - one manager
    - one staff member
- Seed categories, food items, tables, and sample orders

### Phase 5 — Role‑Based Access

- Create middleware or policies for staff, manager, and owner permissions
- Restrict financial reports to manager and owner
- Restrict food price/menu management to manager and owner

### Phase 6 — Customer Ordering Logic

- Generate QR links for restaurant tables
- Display menu using table QR token
- Build cart using session or JavaScript
- Validate and save orders
- Save order‑item price snapshots
- Show order confirmation page

### Phase 7 — Staff Features

- Order list and order detail page
- Update order status
- Filter orders by date, status, and table
- Toggle food availability

### Phase 8 — Manager and Owner Features

- Category CRUD
- Food item CRUD
- Image upload
- Staff account management
- Sales dashboard
- Revenue, top‑selling item, category sales, and estimated‑profit charts

### Phase 9 — UI and UX

- Responsive customer menu
- Clear food availability labels
- Simple cart and checkout experience
- Dashboard sidebar/navigation
- Success and error alerts
- Confirm dialog before deleting food items or categories

### Phase 10 — Testing and Documentation

Test this complete flow:

> Customer scans QR → views menu → places order → staff receives it → staff changes status → manager checks sales → owner views dashboard analytics

Write a README with:

- Project description
- Installation steps
- Database setup
- Test login accounts
- Screenshots
- Feature list
- Role permissions
