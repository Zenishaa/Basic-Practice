<?php
include "../config/session.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box home-card">
            <h1>Hello, Its "<?php echo htmlspecialchars($_SESSION["name"]); ?>" here.</h1>
            <p class="user-email"><?php echo htmlspecialchars($_SESSION["email"]); ?></p>

            <div class="links">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
