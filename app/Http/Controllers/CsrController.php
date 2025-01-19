<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CsrController extends Controller
{
    // public function generateCSR(Request $request)
    // {
    //     $dn = [
    //         "countryName"            => "US",
    //         "stateOrProvinceName"    => "California",
    //         "localityName"           => "San Francisco",
    //         "organizationName"       => "Example Organization",
    //         "organizationalUnitName" => "Example Unit",
    //         "commonName"             => $request->input('common_name', 'example.com'),
    //         "emailAddress"           => $request->input('email', 'example@example.com'),
    //     ];

    //     // Generate a new private key
    //     $privateKey = openssl_pkey_new([
    //         "private_key_bits" => 2048,
    //         "private_key_type" => OPENSSL_KEYTYPE_RSA,
    //     ]);

    //     // Generate the CSR
    //     $csr = openssl_csr_new($dn, $privateKey);

    //     // Save the private key
    //     $privateKeyPath = 'certificates/client.key';
    //     openssl_pkey_export_to_file($privateKey, storage_path("app/$privateKeyPath"));

    //     // Self-sign the CSR (or use a CA to sign it)
    //     $caCertPath = storage_path('app/certificates/ca.crt');
    //     $caKeyPath = storage_path('app/certificates/ca.key');

    //     $caCert = file_get_contents($caCertPath);
    //     $caKey = file_get_contents($caKeyPath);
    //     $signedCert = openssl_csr_sign($csr, $caCert, openssl_pkey_get_private($caKey), 365, ['digest_alg' => 'sha256']);

        
    //     // Save the signed certificate
    //     $certificatePath = 'certificates/client.crt';
    //     openssl_x509_export_to_file($signedCert, storage_path("app/$certificatePath"));

    //     // Return CSR, private key, and certificate to the client
    //     return response()->json([
    //         "message" => "Certificate generated successfully",
    //         "private_key" => file_get_contents(storage_path("app/$privateKeyPath")),
    //         "certificate" => file_get_contents(storage_path("app/$certificatePath")),
    //     ]);
    // }
    public function generateCSR()
    {
    //    dd('jkj');
        // Paths for storing the files
        $userId = auth()->id(); // Ensure the user is authenticated before calling this


        // Dynamically generate paths for the files based on user ID
        $privateKeyPath = "certificates/{$userId}_client.key";
        $certificatePath = "certificates/{$userId}_client.crt";


        // Generate a private key
        $privateKeyResource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($privateKeyResource, $privateKey);

        // Generate a Certificate Signing Request (CSR)
        $csr = openssl_csr_new([
            "countryName" => "US",
            "stateOrProvinceName" => "California",
            "localityName" => "San Francisco",
            "organizationName" => "Example Organization",
            "organizationalUnitName" => "Example Unit",
            "commonName" => "example.com",
            "emailAddress" => "example@example.com",
        ], $privateKeyResource);

        // Sign the CSR to create a certificate
        $caCert = file_get_contents(storage_path('app/certificates/ca.crt'));
        $caKey = file_get_contents(storage_path('app/certificates/ca.key'));
        $caKeyResource = openssl_pkey_get_private($caKey);

        $certificate = openssl_csr_sign($csr, $caCert, $caKeyResource, 365, ['digest_alg' => 'sha256']);

        // Export the certificate
        openssl_x509_export($certificate, $certificateOut);

        // Save the private key and certificate to files
        Storage::put($privateKeyPath, $privateKey);
        Storage::put($certificatePath, $certificateOut);

        // Return download links
        return response()->json([
            'message' => 'Certificate generated successfully',
            'private_key_download_url' => route('download.certificate.file', ['type' => 'private_key']),
            'certificate_download_url' => route('download.certificate.file', ['type' => 'certificate']),
        ]);
    }

    public function downloadFile(Request $request)
    {
        $userId = auth()->id();
        $type = $request->get('type');
        $path = $type === 'private_key'
            ? "certificates/{$userId}_client.key"
            : "certificates/{$userId}_client.crt";
        

        if (Storage::exists($path)) {
            return response()->download(storage_path("app/private/$path"));
        }

        return response()->json(['message' => 'File not found'], 404);
    }
    public function test(){
        return response()->json([
            'message' => 'done',
        ]);
    }
}

