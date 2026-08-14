# Flowra 🌿 — Simple Personal Finance Garden

> **"Grow Your Wealth Naturally"**
>
> Flowra is a modern, elegant, and intuitive personal finance management application built with Laravel, TailwindCSS, and Alpine.js. Designed with a calming botanical aesthetic, Flowra brings clarity, structure, and simplicity to daily wealth and cash flow tracking.

---

## ✨ Features

- 🌳 **Financial Dashboard & Net Worth Tracker**: Real-time asset & liability balancing (Accounts + Active Receivables - Active Debts).
- 🌸 **Unified Transaction Hub**: Clean logging for Income, Expense, and Inter-Account Transfers with instant category tagging and search.
- 🍃 **Multiple Accounts & Wallets**: Manage Bank accounts, E-wallets, and Cash with automated balance tracking.
- 🌱 **Monthly Budgets**: Set spending limits per category with visual vine progress meters and over-budget warnings.
- 🌿 **Debts & Receivables (Hutang & Piutang)**: Accurate cash-flow accounting for loans given and taken, complete with installment histories and settlement tracking.
- 🍃 **Recurring Transactions**: Track subscriptions, bills, and routine incomes with automated recurrence calculations.
- 💐 **Custom Categories**: Vibrant, customizable categories with botanical icons and custom color tags.
- 📊 **Reports & CSV Export**: In-depth cash flow dynamics, expense breakdowns, and UTF-8 Excel-ready CSV export.
- 🔒 **Data Reset & Security**: Start fresh anytime with one-click atomic data reset while preserving authentication credentials.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.2+ / Laravel 12
- **Frontend**: Blade, TailwindCSS, Alpine.js, Chart.js, Vite
- **Database**: MySQL / PostgreSQL / SQLite
- **Deployment**: Vercel Serverless (PHP Runtime) / VPS / Laravel Forge

---

## 🚀 Local Development Setup

1. **Clone repository**:
   ```bash
   git clone https://github.com/alfiardiansyah/flowra.git
   cd flowra
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node dependencies**:
   ```bash
   npm install
   ```

4. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Start Dev Servers**:
   ```bash
   npm run dev
   php artisan serve
   ```

---

## 🌐 Production Deployment on Vercel

Flowra is configured for serverless deployment on Vercel via `vercel-php`.

### Required Environment Variables on Vercel:

| Variable | Recommended Value / Description |
|---|---|
| `APP_NAME` | `Flowra` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(Generate using `php artisan key:generate --show`)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-flowra-domain.vercel.app` |
| `DB_CONNECTION` | `mysql` / `pgsql` |
| `DB_HOST` | *(Your cloud database host e.g., Supabase, Neon, PlanetScale, Railway)* |
| `DB_PORT` | `3306` / `5432` |
| `DB_DATABASE` | `flowra` |
| `DB_USERNAME` | *(Database username)* |
| `DB_PASSWORD` | *(Database password)* |
| `SESSION_DRIVER` | `cookie` / `database` |
| `CACHE_STORE` | `array` / `database` |
| `LOG_CHANNEL` | `stderr` |

---

## 📄 License

The Flowra application is open-sourced software licensed under the [MIT license](LICENSE).
