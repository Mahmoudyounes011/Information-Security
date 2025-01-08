<?php

namespace App\Console\Commands;

use App\Models\ServerKey;
use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class GenerateServerKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:server-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate public and private keys for the server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rsa = RSA::createKey(1024); 
        $privateKey = $rsa->toString('PKCS8');
        $publicKey = $rsa->getPublicKey()->toString('PKCS8'); 

        ServerKey::create([
            'public_key' => $publicKey,
            'private_key' => $privateKey,
        ]);

        $this->info('Server keys generated and stored in the database!');

    }
}
