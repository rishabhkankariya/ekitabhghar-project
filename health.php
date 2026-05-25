<?php
/**
 * Health Check Endpoint for Render
 * Returns 200 OK if the application is running
 */

header('Content-Type: application/json');
http_response_code(200);

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'E-Kitabghar Portal'
];

// Optional: Check database connection
try {
    require_once 'php/connection.php';
    if ($pdo) {
        $health['database'] = 'connected';
    }
} catch (Exception $e) {
    $health['database'] = 'error';
    $health['db_message'] = 'Database connection failed';
    // Still return 200 to prevent service restart
}

echo json_encode($health, JSON_PRETTY_PRINT);
