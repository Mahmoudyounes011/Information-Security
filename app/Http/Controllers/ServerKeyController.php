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
            return response()->json(['error' => 'Public key already exists for this user'], 400);
        }

        ClientKey::create([
            'user_id' => $request->user()->id,
            'public_key' => $validated['public_key'],
        ]);


        $serverKey = ServerKey::latest()->first(); 
        $publicKey = $serverKey->public_key;
        $formattedKey = str_replace(["-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----", "\r", "\n"], '', $publicKey);

        return response()->json([
            'public_key' => $formattedKey,
        ]);
    }
}
