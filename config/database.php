<?php
/**
 * WAM Tech POS & ERP Enterprise (v2.1 Egypt Edition)
 * ملف إعدادات قاعدة البيانات - Database Configuration
 * 
 * هندسة وبرمجة: المبرمج | Almubarmig - م/ وئام محمد السوداني
 */

return [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'port'      => 3306,
    'database'  => 'wamtech_pos_db',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // تعطيل المحاكاة لدعم Prepared Statements الحقيقية بنسبة 100%
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '+02:00'"
    ]
];
