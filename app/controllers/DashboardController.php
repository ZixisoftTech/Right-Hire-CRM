<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;

class DashboardController extends Controller
{
    public function index()
    {
        // Simple placeholder for authenticated area
        $this->view('dashboard/index', [
            'userId' => Session::get('user_id')
        ]);
    }
}
