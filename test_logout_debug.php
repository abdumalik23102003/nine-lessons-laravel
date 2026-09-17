<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class);

$user = User::factory()->create();
$token = $user->createToken('test')->plainTextToken;

echo "Created token: " . substr($token, 0, 20) . "...\n";
echo "Tokens before logout: " . $user->tokens()->count() . "\n";

// Simulate logout (delete all tokens)
$user->tokens()->delete();

echo "Tokens after logout: " . $user->tokens()->count() . "\n";

// Check if token still exists in DB
$exists = \Laravel\Sanctum\PersonalAccessToken::where('token', hash('sha256', $token))->exists();
echo "Token exists in DB: " . ($exists ? 'yes' : 'no') . "\n";
