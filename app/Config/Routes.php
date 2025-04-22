<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Start::index');

$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->add('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->add('forgot_password', 'Auth::forgot_password');
    $routes->get('/', 'Auth::index');
    $routes->add('create_user', 'Auth::create_user');
    $routes->add('deleteUser/(:num)', 'Auth::deleteUser/$1');
    $routes->add('edit_user/(:num)', 'Auth::edit_user/$1');
    $routes->add('edit_group/(:num)', 'Auth::edit_group/$1');
    $routes->get('activate/(:num)', 'Auth::activate/$1');
    $routes->get('activate/(:num)/(:hash)', 'Auth::activate/$1/$2');
    $routes->add('deactivate/(:num)', 'Auth::deactivate/$1');
    $routes->get('reset_password/(:hash)', 'Auth::reset_password/$1');
    $routes->post('reset_password/(:hash)', 'Auth::reset_password/$1');
    // ...
    $routes->add('UpdateDb', 'UpdateDb::index');
});
