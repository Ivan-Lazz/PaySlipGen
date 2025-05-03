<?php

use Core\Router;

/**
 * API Routes
 */
$router = app('router');

$router->group(['prefix' => 'api'], function (Router $router) {
    // Auth routes
    $router->post('login', 'AuthController@login');
    $router->post('verify', 'AuthController@verify');
    $router->post('logout', 'AuthController@logout');
    
    // Protected routes
    $router->group(['middleware' => ['auth']], function (Router $router) {
        // User routes
        $router->get('users', 'UserController@index');
        $router->get('users/{id}', 'UserController@show');
        $router->post('users', 'UserController@store');
        $router->put('users/{id}', 'UserController@update');
        $router->delete('users/{id}', 'UserController@destroy');
        
        // Employee routes
        $router->get('employees', 'EmployeeController@index');
        $router->get('employees/{id}', 'EmployeeController@show');
        $router->post('employees', 'EmployeeController@store');
        $router->put('employees/{id}', 'EmployeeController@update');
        $router->delete('employees/{id}', 'EmployeeController@destroy');
        
        // Account routes
        $router->get('accounts', 'AccountController@index');
        $router->get('accounts/{id}', 'AccountController@show');
        $router->post('accounts', 'AccountController@store');
        $router->put('accounts/{id}', 'AccountController@update');
        $router->delete('accounts/{id}', 'AccountController@destroy');
        
        // Payslip routes
        $router->get('payslips', 'PayslipController@index');
        $router->get('payslips/{id}', 'PayslipController@show');
        $router->post('payslips', 'PayslipController@store');
        $router->put('payslips/{id}', 'PayslipController@update');
        $router->delete('payslips/{id}', 'PayslipController@destroy');
        
        // Report routes
        $router->get('reports/payroll', 'ReportController@payroll');
        $router->get('reports/employee/{id}', 'ReportController@employee');
    });
});