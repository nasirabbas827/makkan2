# makkan2_final

A PHP‑based real‑estate platform that enables users to browse, favorite, and report properties, while providing dedicated admin and seller dashboards for managing listings, images, and user interactions.

---

## Overview

`makkan2_final` is a full‑stack web application built with PHP and MySQL. It offers:

* A public marketplace where visitors can search and view property details.  
* User authentication (login / register) with profile management.  
* Favorite‑listing functionality for logged‑in users.  
* Reporting mechanism for inappropriate or inaccurate property listings.  
* Separate admin and seller panels for managing properties, images, reports, and users.  

The repository contains all source code, CSS styling, and a sample database dump to get the project up and running quickly.

---

## Features

| Feature | Description |
|---------|-------------|
| **Public Listings** | Browse properties, view images, and read details. |
| **Favorites** | Authenticated users can add/remove properties to a personal favorites list (`add_to_favorites.php`). |
| **Report Property** | Users can flag listings for review (`report_property.php`). |
| **Admin Dashboard** | Full control over properties, reports, and site settings (`admin/admin_dashboard.php`). |
| **Seller Dashboard** | Sellers can create, edit, and delete their own listings, manage images, and update their profile (`seller/`). |
| **Authentication** | Secure login / registration flow with session handling (`login.php`, `register.php`, `logout.php`). |
| **Responsive UI** | Simple, clean styling (`CSS/style.css`). |
| **Database Schema** | Ready‑to‑import SQL dump (`Database/makkandatabase.sql`). |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **Frontend** | HTML5, CSS3 (custom stylesheet) |
| **Server** | Apache / Nginx (compatible with standard LAMP stack) |
| **Version Control** | Git (GitHub) |

---

## Installation

### Prerequisites

* PHP 7.4 or newer with `mysqli` extension enabled.  
* MySQL server (or MariaDB) with access credentials.  
* A web server (Apache/Nginx) configured to serve PHP files.  
* Composer (optional, only if you add third‑party packages later).

### Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/makkan2_final.git
   cd makkan2_final
   ```

2. **Create the database**

   ```bash
   mysql -u root -p < Database/makkandatabase.sql
   ```

   > Adjust the command according to your MySQL user/password.

3. **Configure database connection**

   Edit `config.php` (and `admin/config.php`, `seller/config.php` if needed) and replace the placeholder values with your own credentials:

   ```php
   // config.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'YOUR_DB_USERNAME');
   define('DB_PASS', 'YOUR_DB_PASSWORD');
   define('DB_NAME', 'YOUR_DB_NAME');
   ```

4. **Set up file permissions**

   Ensure the `seller/uploads/` directory is writable by the web server so images can be uploaded:

   ```bash
   chmod -R 755 seller/uploads/
   ```

5. **Start the server**

   *For a quick local test*:

   ```bash
   php -S localhost:8000
   ```

   Then open `http://localhost:8000/index.php` in your browser.

   *Or configure a virtual host* in Apache/Nginx pointing to the project root.

---

## Usage

### Public Users

1. Visit the home page (`index.php`) to browse listings.  
2. Register (`register.php`) or log in (`login.php`)