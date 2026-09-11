<<<<<<< HEAD
<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
=======
session_unset();
session_destroy();
header("Location: login.php");
>>>>>>> b4d6fbe7badd2433dbdbb3553ffc627837a984e2

