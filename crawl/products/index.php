<?php
// Endpoint: /crawl/products
// Outputs a JSON list of products with key fields for AI training.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../app/core/Autoloader.php';

// Ensure classes load on all hosts
@include_once __DIR__ . '/../../app/models/product.php';
@include_once __DIR__ . '/../../app/controllers/CrawlController.php';

if (class_exists('ProductModel') && method_exists('ProductModel', 'init')) {
    ProductModel::init();
}

CrawlController::products();
