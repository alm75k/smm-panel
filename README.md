# SMM Panel (PHP / MySQL / Bootstrap)

Simple **SMM Panel** développé en **PHP 8**, **MySQL** et **Bootstrap 5**, connecté à une **API SMM externe** pour la gestion des services, commandes et du solde.

---

## Features

* User authentication (register / login / logout)
* Dashboard with balance and order stats
* Services list from SMM API
* Search & filter by category
* One-click order (service auto-selected)
* Real-time price calculation
* Balance verification before order
* Order history
* Responsive UI with Bootstrap 5

---

## Tech Stack

* PHP 8+
* MySQL
* PDO
* Bootstrap 5
* Nginx
* External SMM API (v2)

---

## Project Structure

```
smmpanel/
├── config/
│   └── database.php
├── core/
│   ├── api.php
│   └── auth.php
├── public/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── services.php
│   ├── new_order.php
│   ├── orders.php
│   ├── add_funds.php
│   └── header.php
├── sql/
│   └── schema.sql
└── README.md
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/smm-panel.git
cd smm-panel
```

---

### 2. Create the database

```sql
CREATE DATABASE smm_panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import the SQL schema:

```
sql/schema.sql
```

---

### 3. Configure database connection

Edit `config/database.php`:

```php
$host = 'localhost';
$db   = 'smm_panel';
$user = 'root';
$pass = '';
```

---

### 4. Configure SMM API

Edit `core/api.php`:

```php
public $api_url = 'https://smmpakpanel.com/api/v2';
public $api_key = 'YOUR_API_KEY';
```

**Never commit your API key to GitHub.**

---

## Security Notes

* PDO with prepared statements
* Session-based authentication
* Server-side validation
* No API keys exposed to clients

---

## Limitations

* No payment gateway integration
* No admin panel
* No automatic order status sync

---

## Roadmap

* [ ] Payment gateways (Stripe, PayPal, Crypto)
* [ ] Admin panel
* [ ] Cron job for order sync
* [ ] Refill / Cancel automation
* [ ] Multi-provider API support
* [ ] Two-factor authentication

---

## License

This project is provided for educational purposes.
Use at your own risk.

---

### Built with ❤️ using PHP
