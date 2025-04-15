# Recipe Management System

A web-based recipe management system built with PHP and MySQL, allowing users to create, share, and manage their favorite recipes.

## Features

- User authentication (register, login, logout)
- Recipe management (create, edit, delete)
- Recipe categories and cuisines
- Image upload for recipes
- Rating and review system
- Search and filter recipes
- Responsive design with Tailwind CSS

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Zan-pakto/recipe.git
   ```

2. Create a MySQL database and import the schema:
   ```bash
   mysql -u your_username -p your_database_name < database/schema.sql
   ```

3. Configure the database connection:
   - Copy `includes/config.example.php` to `includes/config.php`
   - Update the database credentials in `config.php`

4. Set up the web server:
   - Point your web server's document root to the project directory
   - Ensure the `uploads` directory is writable by the web server

5. Access the application through your web browser

## Directory Structure

```
recipe/
├── css/              # CSS stylesheets
├── includes/         # PHP includes and configuration
├── uploads/          # Recipe images
├── database/         # Database schema and migrations
├── index.php         # Homepage
├── login.php         # Login page
├── register.php      # Registration page
├── recipe.php        # Recipe view page
├── add-recipe.php    # Add new recipe
├── edit-recipe.php   # Edit existing recipe
└── README.md         # This file
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Font Awesome for icons
- Tailwind CSS for styling
- PHP PDO for database operations 