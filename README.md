# Daily Devotion Discussion App

A PHP MVC web application for a private Christian devotional discussion group with date-based devotions, nested comments (up to 3 levels), and role-based permissions.

## Features

- **Date-based Devotions**: Each devotion is tied to a specific date
- **Nested Comments**: Comments support up to 3 levels of nesting (top-level + 2 reply levels)
- **Role-based Access**: Three roles - site_pastor (admin), brother, and sister
- **Copyright-Safe Design**: Optional text fields are NULL by default, can be enabled when permission is granted
- **Comment Management**: Users can edit their own comments within 30 minutes, soft delete functionality
- **Admin Panel**: Site pastors can manage devotions, users, and moderate comments

## Requirements

- PHP 8.0 or higher
- MySQL/MariaDB 5.7+ (or MySQL 8.0+ for CHECK constraint support)
- Apache with mod_rewrite enabled (or Nginx with equivalent rewrite rules)
- PDO extension enabled

## Installation

1. **Clone or download the project**

2. **Configure Database**
   - Create a MySQL database for the application
   - Update `config/database.php` with your database credentials:
     ```php
     return [
         'host' => 'localhost',
         'dbname' => 'your_database_name',
         'username' => 'your_username',
         'password' => 'your_password',
         // ...
     ];
     ```

3. **Run Migrations**
   ```bash
   php migrations/run.php
   ```
   This will create all necessary tables.

4. **Create Initial Admin User**
   - Generate a password hash:
     ```php
     php -r "echo password_hash('YourSecurePassword123!', PASSWORD_ARGON2ID);"
     ```
   - Update `sql/seeds/001_admin_user.sql` with the generated hash
   - Run the seed file or manually insert the admin user

5. **Configure Web Server**

   **Apache (.htaccess already included)**
   - Point document root to `public/` directory
   - Ensure mod_rewrite is enabled

   **Nginx**
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

6. **Set Permissions**
   ```bash
   chmod 755 public/
   chmod 644 public/.htaccess
   ```

## Configuration

### Copyright Mode

Edit `config/app.php`:
- `'copyright_mode' => 'safe'` - Hides all verse text and paragraph content (default)
- `'copyright_mode' => 'enabled'` - Shows text when `author_paragraphs_enabled` is TRUE for a devotion

### Other Settings

- `comment_edit_window_minutes`: Time window for editing comments (default: 30)
- `session_timeout_hours`: Session timeout (default: 24)

## Usage

1. **Login**: Navigate to `/login` and use your admin credentials
2. **View Devotion**: Home page redirects to today's devotion
3. **Create Devotion** (site_pastor only): Go to `/admin/devotions` and click "Create New Devotion"
4. **Post Comments**: Users can post comments and replies (up to 3 levels deep)
5. **Manage Users** (site_pastor only): Go to `/admin/users` to change user roles

## Database Schema

- **users**: User accounts with roles
- **devotions**: Date-based devotion entries
- **devotion_paragraphs**: Optional paragraph content (nullable for copyright safety)
- **readings**: Scripture references for each devotion
- **comments**: Nested comments with depth tracking (0-2)

## Security Features

- Password hashing with Argon2ID (fallback to bcrypt)
- CSRF protection on all forms
- XSS prevention with output escaping
- SQL injection prevention with prepared statements
- Session security (httponly, secure cookies)
- Role-based authorization checks

## File Structure

```
daily devotion/
├── config/          # Configuration files
├── controllers/     # Request handlers
├── includes/        # Helper functions (auth, CSRF, validation)
├── migrations/      # Database migration files
├── models/          # Data models
├── public/          # Web root (index.php, assets)
├── sql/             # Seed files
└── views/           # HTML templates
```

## Development Notes

- All database queries use prepared statements
- Comments use adjacency list pattern with depth field
- Depth validation enforced in application layer (CommentService)
- Soft delete for comments preserves thread structure
- Copyright-safe mode: NULL fields by default, enabled via feature flag

## Troubleshooting

**Database connection errors**: Check `config/database.php` credentials

**404 errors**: Ensure mod_rewrite is enabled and `.htaccess` is in `public/` directory

**Session issues**: Check PHP session configuration and file permissions

**Migration errors**: Ensure database user has CREATE TABLE permissions

## License

Private use only - for internal devotional group.

