<?php
namespace App\Http\Controllers;

use App\Models\ClientKey;
use App\Models\ServerKey;
use Illuminate\Http\Request;

class ServerKeyController extends Controller
{
    public function getPublicKey(Request $request)
{
    $validated = $request->validate([
        'public_key' => 'required|string',
    ]);

    $existingKey = $request->user()->clientKey;

    
    if ($existingKey) {
        $existingKey->update([
            'public_key' => $validated['public_key'],
        ]);
    } else {
    
        ClientKey::create([
            'user_id' => $request->user()->id,
            'public_key' => $validated['public_key'],
        ]);
    }

    $serverKey = ServerKey::latest()->first(); 
    $publicKey = $serverKey->public_key;

    return response()->json([
        'public_key' => $publicKey,
    ]);
}

}
