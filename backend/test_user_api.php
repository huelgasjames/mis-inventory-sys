<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

use Illuminate\Http\Request;
use App\Http\Controllers\InventoryController;

// Create a test request
$request = Request::create('/api/users', 'POST', [], [], [], [], [], [], json_encode([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'role' => 'student',
    'address' => 'Test Address'
]));

// Create controller instance
$controller = new InventoryController();

try {
    $response = $controller->createUser($request);
    echo "Success: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
