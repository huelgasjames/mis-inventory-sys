<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== USER ACCOUNTS ===\n";
echo "Total users: " . App\Models\User::count() . "\n\n";

$users = App\Models\User::select('user_id', 'email', 'name', 'role')->get();

foreach ($users as $user) {
    echo "User ID: " . $user->user_id . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Role: " . $user->role . "\n";
    echo "---\n";
}
