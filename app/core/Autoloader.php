<?php

spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'App\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    // We lowercase the relative class part to match directory structure if needed,
    // but Windows/Mac are case insensitive. For Linux, we will keep case exact but map top level dirs.
    $parts = explode('\\', $relative_class);
    // Lowercase only the directory parts, keep class name casing
    for ($i = 0; $i < count($parts) - 1; $i++) {
        // Handle CamelCase controllers/models to lowercase dir names
        // Convert camelCase to dash if needed, but in our case dirs are lowercase
        $parts[$i] = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $parts[$i]));
    }

    // For services/middleware/controllers etc, just lowercasing the word works
    // For example App\Controllers\AuthController -> app/controllers/AuthController.php

    // reset to simple strtolower
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $parts[$i] = strtolower($parts[$i]);
    }

    $file = $base_dir . implode('/', $parts) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
