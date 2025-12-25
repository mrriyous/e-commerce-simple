# E-Commerce Test Project

A Laravel-based e-commerce application built with Inertia.js and Vue 3, featuring product management, shopping cart, transactions, and automated notifications.

## Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **npm**: 9.x or higher
- **Database**: SQLite (default) or MySQL/PostgreSQL

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd e-commerce-test
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

Create a `.env` file from the example (if available) or create one manually:

```bash
cp .env.example .env
```

If `.env.example` doesn't exist, create a `.env` file with the following minimum configuration:

```env
APP_NAME="E-Commerce Test"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Database Setup

#### Using SQLite (Default)

The project uses SQLite by default. The database file should already exist at `database/database.sqlite`. If it doesn't, create it:

```bash
touch database/database.sqlite
```

#### Using MySQL/PostgreSQL

If you prefer to use MySQL or PostgreSQL, update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_test
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed the Database

The project includes seeders for categories and products. Run the database seeder:

```bash
php artisan db:seed
```

This will:
- Create a test user with email: `test@example.com` (password will be generated)
- Seed 5 product categories (Living Room, Bedroom, Dining Room, Kitchen, Office)
- Seed 50 products across all categories

**Note**: If you need to reset and reseed the database:

```bash
php artisan migrate:fresh --seed
```

### 7. Install Node Dependencies

```bash
npm install
```

### 8. Build Frontend Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

## Running the Application

### Development Mode

The project includes a convenient development script that runs multiple services concurrently:

```bash
composer run dev
```

This will start:
- Laravel development server (http://localhost:8000)
- Queue worker
- Laravel Pail (log viewer)
- Vite development server

### Manual Setup

If you prefer to run services manually:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Queue Worker:**
```bash
php artisan queue:listen
```

**Terminal 3 - Vite Dev Server:**
```bash
npm run dev
```

### Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

## Default Test User

After running the database seeder, you can log in with:

- **Email**: `test@example.com`
- **Password**: Check the seeder output or reset it using Laravel's password reset functionality

## Database Seeders

### Available Seeders

1. **DatabaseSeeder** - Main seeder that calls all other seeders
2. **CategorySeeder** - Seeds 5 product categories
3. **ProductSeeder** - Seeds 50 products across all categories

### Running Individual Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

### Seeded Data

**Categories:**
- Living Room
- Bedroom
- Dining Room
- Kitchen
- Office

**Products:**
- 10 products per category
- Includes pricing, stock quantities, ratings, and reviews
- Mix of featured and regular products

## Scheduled Commands

The application includes scheduled commands that run automatically:

1. **Low Stock Check** (`products:check-low-stock`)
   - Runs every 30 minutes
   - Checks for products with low stock
   - Sends notifications via queue jobs

2. **Daily Sales Report** (`sales:generate-daily-report`)
   - Runs daily at 19:00 (7 PM)
   - Generates and emails daily sales reports

To run the scheduler, add this to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or for testing, you can run the scheduler manually:

```bash
php artisan schedule:run
```

## Queue Configuration

The application uses database queues for background jobs. Make sure to run the queue worker:

```bash
php artisan queue:work
```

Or use the development script which includes the queue worker.

## Testing

Run the test suite:

```bash
php artisan test
```

Or use the composer script:

```bash
composer test
```

## Project Structure

```
app/
├── Console/Commands/          # Artisan commands
├── Http/
│   ├── Controllers/          # Application controllers
│   ├── Middleware/           # Custom middleware
│   ├── Requests/             # Form request validation
│   └── Resources/            # API resources
├── Jobs/                     # Queue jobs
├── Mail/                     # Mail classes
└── Models/                   # Eloquent models

resources/
├── js/
│   ├── components/          # Vue components
│   ├── layouts/             # Layout components
│   ├── pages/               # Inertia pages
│   └── composables/         # Vue composables
└── views/
    └── emails/              # Email templates

database/
├── migrations/              # Database migrations
└── seeders/                # Database seeders
```

## Technologies Used

- **Backend**: Laravel 12
- **Frontend**: Vue 3, Inertia.js
- **Styling**: Tailwind CSS 4
- **Build Tool**: Vite
- **Authentication**: Laravel Fortify
- **Database**: SQLite (default), MySQL/PostgreSQL supported

## Additional Notes

- The application uses SQLite by default for easy setup
- Email notifications are logged to files by default (configure `MAIL_MAILER` in `.env` for production)
- Queue jobs are stored in the database (configure `QUEUE_CONNECTION` in `.env` if needed)
- Two-factor authentication is supported via Laravel Fortify

## Troubleshooting

### Database Issues

If you encounter database errors:
1. Ensure the SQLite file exists: `touch database/database.sqlite`
2. Check file permissions on the database file
3. Run migrations: `php artisan migrate:fresh`

### Frontend Build Issues

If assets aren't loading:
1. Clear cache: `php artisan cache:clear`
2. Rebuild assets: `npm run build`
3. Clear Vite cache: `rm -rf node_modules/.vite`

### Queue Not Processing

Ensure the queue worker is running:
```bash
php artisan queue:work
```

Check failed jobs:
```bash
php artisan queue:failed
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

