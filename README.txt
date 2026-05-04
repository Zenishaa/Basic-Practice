Real PHP Login Signup Project For Learning

Folder structure:

pract/
  index.php
  config/
    database.php
    session.php
  database/
    auth_learning.sql
  pages/
    login.php
    signup.php
    forgot.php
    home.php
    logout.php
  assets/
    css/
      style.css

How to run with XAMPP:

1. Start Apache and MySQL from XAMPP Control Panel.
2. Put this pract folder inside:
   C:\xampp\htdocs\
3. Open phpMyAdmin:
   http://localhost/phpmyadmin/
4. Create/import the database:
   Import database/auth_learning.sql

   If you already created the old users table before this SMTP update,
   run these SQL commands in phpMyAdmin:

   ALTER TABLE users ADD email_verified TINYINT(1) NOT NULL DEFAULT 0;
   ALTER TABLE users ADD email_verification_token VARCHAR(64) DEFAULT NULL;
   ALTER TABLE users ADD email_verification_expires DATETIME DEFAULT NULL;
   ALTER TABLE users ADD password_reset_token VARCHAR(64) DEFAULT NULL;
   ALTER TABLE users ADD password_reset_expires DATETIME DEFAULT NULL;

5. Open this URL in your browser:
   http://localhost/pract/

SMTP / Gmail setup:

1. Install Composer from:
   https://getcomposer.org/
2. Open terminal inside this project and run:
   composer install
3. Copy:
   config/mail.example.php
   to:
   config/mail.php
4. In config/mail.php, add your Gmail address and Gmail App Password.
   Do not use your normal Gmail password.
5. Gmail App Password prerequisite:
   Your Gmail account must have 2-Step Verification enabled.
   Then create an App Password from your Google Account security settings.

Database name:
auth_learning

Database table:
users

What this project teaches:

1. How to connect PHP with MySQL.
2. How to create a signup form.
3. How to save user details in a database.
4. How to hash passwords using password_hash().
5. How to check passwords using password_verify().
6. How to use sessions after login.
7. How to logout.
8. How to reset a password.
9. How to send email using Gmail SMTP.
10. How to verify Gmail before login.

Important learning note:
This is much better than saving passwords directly in a text file.
Forgot password now uses secure email reset links instead of directly changing the password.
