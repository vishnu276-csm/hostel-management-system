```php
<?php

function checkLogin($role)
{
    

    if (session_status() === PHP_SESSION_NONE) {

        session_start();

    }

    if (!isset($_SESSION['user_id'])) {

        header("Location: ../login.php");
        exit;

    }

    if (
        !isset($_SESSION['role']) ||
        $_SESSION['role'] !== $role
    ) {

        header("Location: ../login.php");
        exit;

    }
}

?>
```
