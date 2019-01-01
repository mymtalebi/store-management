<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

return [
    /*
     * Default Database Connection Name
     *
     * Here you may specify which of the database connections below you wish
     * to use as your default connection for all database work. Of course
     * you may use many connections at once using the Database library.
     */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
     * Database Connections
     *
     * Here are each of the database connections setup for your application.
     * Of course, examples of configuring each database platform that is
     * supported by Laravel is shown below to make development simple.
     *
     * All database work in Laravel is done through the PHP PDO facilities
     * so make sure you have the driver for your particular database of
     * choice installed on your machine before you begin development.
     */
    'connections' => [
        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => env('DB_PREFIX', ''),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'stores'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', TRUE),
            'engine' => env('DB_ENGINE', 'InnoDB'),
            'timezone' => env('DB_TIMEZONE', '+00:00'),
        ],
    ],

    /*
     * Migration Repository Table
     *
     * This table keeps track of all the migrations that have already run for
     * your application. Using this information, we can determine which of
     * the migrations on disk haven't actually been run in the database.
     */
    'migrations' => 'migrations',
];
