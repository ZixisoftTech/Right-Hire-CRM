<?php

$router->add('GET', '/', 'AuthController@index', [\App\Middleware\GuestMiddleware::class]);
$router->add('POST', '/api/auth/login', 'AuthController@login', [\App\Middleware\CSRFMiddleware::class, \App\Middleware\RateLimitMiddleware::class]);

$router->add('GET', '/auth/email-otp', 'AuthController@showOTP', [\App\Middleware\GuestMiddleware::class]);
$router->add('POST', '/api/auth/verify-otp', 'AuthController@verifyOTP', [\App\Middleware\CSRFMiddleware::class, \App\Middleware\RateLimitMiddleware::class]);
$router->add('POST', '/api/auth/resend-otp', 'AuthController@resendOTP', [\App\Middleware\CSRFMiddleware::class, \App\Middleware\RateLimitMiddleware::class]);

$router->add('GET', '/auth/recover-password', 'AuthController@showRecoverPassword', [\App\Middleware\GuestMiddleware::class]);
$router->add('POST', '/api/auth/send-reset-link', 'AuthController@sendResetLink', [\App\Middleware\CSRFMiddleware::class, \App\Middleware\RateLimitMiddleware::class]);

$router->add('GET', '/auth/reset-password', 'AuthController@showResetPassword', [\App\Middleware\GuestMiddleware::class]);
$router->add('POST', '/api/auth/reset-password', 'AuthController@resetPassword', [\App\Middleware\CSRFMiddleware::class]);

$router->add('GET', '/api/auth/logout', 'AuthController@logout', []);

// Protected Routes
$router->add('GET', '/dashboard', 'DashboardController@index', [\App\Middleware\AuthMiddleware::class]);
