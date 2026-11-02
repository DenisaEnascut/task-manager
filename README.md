# Task Manager – PHP & MySQL

A simple Task Management application built with PHP and MySQL.

## Features

- User registration and login
- Secure password hashing
- Session-based authentication
- Create, view, edit and delete tasks
- Task statuses: To Do, In Progress, Completed
- Due dates for tasks
- PDO database connection
- Prepared SQL statements
- CSRF protection for task operations
- Responsive styling with CSS

## Technologies Used

- PHP
- MySQL
- HTML
- CSS
- PDO
- XAMPP

## Installation

1. Install XAMPP.
2. Clone or download this repository.
3. Copy the project folder into `C:\xampp\htdocs`.
4. Start Apache and MySQL from XAMPP.
5. Open phpMyAdmin at `http://localhost/phpmyadmin`.
6. Create a database named `task_manager`.
7. Create the `users` and `tasks` tables using the SQL schema provided in the project.
8. Configure the database connection in `config/database.php`.
9. Open `http://localhost/task-manager/` in your browser.

## Database Configuration

The default local XAMPP configuration uses:

- Host: localhost
- Database: task_manager
- Username: root

Configure your own database credentials if needed.
