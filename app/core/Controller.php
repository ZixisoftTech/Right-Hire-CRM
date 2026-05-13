<?php

namespace App\Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewPath)) {
            // Include main layout which will then include the specific view
            require_once __DIR__ . '/../views/layouts/main.php';
        } else {
            die("View does not exist: " . $viewPath);
        }
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}
