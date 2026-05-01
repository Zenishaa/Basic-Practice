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
5. Open this URL in your browser:
   http://localhost/pract/

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

Important learning note:
This is much better than saving passwords directly in a text file.
For a production-level app, forgot password should use email OTP or reset links.
