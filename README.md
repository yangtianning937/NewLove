# NewLove

NewLove is a CakePHP web application for managing product materials, suppliers, and inventory information. It is designed as a student project and focuses on simple inventory management workflows.

## Main Features

- User login and authentication
- Product management
- Raw material management
- Supplier management
- Product inventory and raw material inventory records
- Low-stock material checking
- Database export included for local setup

## Technology Stack

- PHP 8.3 recommended
- CakePHP 4.4
- MySQL-compatible local database
- Composer

## Project Structure

```text
.
├── config/          # CakePHP configuration
├── database/        # SQL database export
├── src/             # Controllers, models, entities, table classes
├── templates/       # Page templates
├── tests/           # PHPUnit test files
├── vendor/          # Composer dependencies, ignored by Git
└── webroot/         # Public CSS, JS, images, and index.php
```

## Database

The database export is stored at:

```text
database/newLove.sql
```

Main tables:

- `users`: application users
- `products`: product records
- `collections`: product collection categories
- `colours`: colour records
- `rawmaterials`: raw material records
- `suppliers`: supplier records
- `product_inventories`: product stock records
- `rawmaterial_inventories`: raw material stock records
- `materials_products`: relationship table between materials and products

To import the database:

```bash
mysql -u newLove -p < database/newLove.sql
```

The local configuration file is:

```text
config/app_local.php
```

For this machine, the database connection uses:

```text
host: 127.0.0.1
database: newLove
username: newLove
password: password
```

`config/app_local.php` is ignored by Git because it contains local database settings.

## Run Locally

Install dependencies if `vendor/` is missing:

```bash
composer install
```

Run the project with PHP 8.3:

```bash
/opt/homebrew/opt/php@8.3/bin/php -S 127.0.0.1:8765 -t webroot webroot/index.php
```

Open:

```text
http://127.0.0.1:8765
```

The home page redirects to the login page when the user is not logged in. This is expected because the main inventory pages are protected.

## Useful Pages

```text
/auth/login
/products
/rawmaterials
/suppliers
```

## Notes

- PHP 8.3 is recommended for this project.
- PHP 8.5 may cause compatibility errors with older CakePHP dependencies.
- If the database connection fails with `localhost`, use `127.0.0.1` in `config/app_local.php`.
- PHPUnit tests currently depend on test database tables. The included `tests/schema.sql` is still a template, so the full test suite may fail until test schema setup is completed.
