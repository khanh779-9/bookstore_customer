<?php
// Components moved to central helpers. This file kept for backward-compatibility
// so views that include it will still have access to the functions via the
// shared helpers file.

if (!defined('APP_HELPERS_INCLUDED')) {
    define('APP_HELPERS_INCLUDED', true);
    include_once __DIR__ . '/../helpers.php';
}

