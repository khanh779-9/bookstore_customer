<?php
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../';

    $relative_class = $class;
 
    $candidates = [
        __DIR__ . '/'. $relative_class . '.php',                      // app/core/Class.php
        __DIR__ . '/../' . $relative_class . '.php',                   // app/Class.php
        __DIR__ . '/../core/' . $relative_class . '.php',             // app/core/Class.php (fallback)
        __DIR__ . '/../controllers/' . $relative_class . '.php',      // app/controllers/Class.php
        __DIR__ . '/../models/' . $relative_class . '.php',           // app/models/Class.php
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

?>