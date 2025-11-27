# Simple Native PHP CRUD (SQLite)

This is a minimal, dependency-free PHP CRUD example using SQLite. It is intended to be dropped into an Apache/PHP environment (like XAMPP) or run with PHP's built-in server.

Files added:
- `db.php` - PDO wrapper, creates `database.sqlite` and `notes` table automatically.
- `index.php` - list notes.
- `create.php` - create a new note.
- `edit.php` - edit an existing note.
- `view.php` - view a note.
- `delete.php` - delete confirmation and action.
- `assets/style.css` - minimal styling.

How to run

- Using XAMPP/Apache:
  1. Place this folder under your htdocs (for example `c:\xampp\htdocs\nativephp`).
  2. Start Apache from XAMPP Control Panel.
  3. Open http://localhost/nativephp in your browser.

- Using PHP built-in server (for quick testing):
  Open PowerShell in the project folder (`c:\xampp\htdocs\nativephp`) and run:

```
php -S localhost:8000
```

Then open http://localhost:8000 in your browser.

Notes and edge-cases
- The app uses an SQLite file `database.sqlite` created in the project directory. Ensure the PHP process has write permissions for this folder.
- No authentication or CSRF protection is implemented. Do not use this in production as-is.
- Basic server-side validation is present (title required). Further validation/escaping is applied with `htmlspecialchars` when rendering.

MySQL usage
---------------

This project can use MySQL instead of SQLite. To enable MySQL:

1. Create a MySQL database and user. Example SQL (run as a privileged user):

```sql
CREATE DATABASE nativephp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nativephp_user'@'localhost' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON nativephp.* TO 'nativephp_user'@'localhost';
FLUSH PRIVILEGES;
```

2. Edit `config.php` (a template is included) and set the correct credentials and `driver` => `'mysql'`.

3. The app will automatically create the `notes` table in MySQL if it doesn't exist. The table schema used for MySQL:

```sql
CREATE TABLE IF NOT EXISTS notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

4. Restart Apache/PHP-FPM if needed, then open the app in your browser. The app will connect to MySQL using the settings in `config.php`.

Notes on permissions and troubleshooting
- Ensure the MySQL user has privileges to create tables or have the table created manually before using the app.
- If you prefer to stick with SQLite, set `driver` => `'sqlite'` or remove `config.php` entirely.


Next steps you might want:
- Add pagination and search on `index.php`.
- Add CSRF tokens and simple auth.
- Switch to MySQL/MariaDB if you prefer a server DB; update `db.php`'s PDO DSN accordingly.
