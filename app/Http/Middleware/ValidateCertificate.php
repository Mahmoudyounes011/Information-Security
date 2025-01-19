<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCertificate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if a certificate is provided
        $clientCert = $request->only('crt')['crt'];
        if (!$clientCert) {
            return response()->json(['error' => 'Client certificate is missing'], Response::HTTP_FORBIDDEN);
        }

        // Path to the CA certificate
        $caCertPath = storage_path('app/certificates/ca.crt');



       // dd($clientCert['crt']);

        // Verify the client certificate against the CA
        $isValid = openssl_x509_checkpurpose($clientCert , X509_PURPOSE_SSL_CLIENT, [$caCertPath]);

        if (!$isValid) {
            return response()->json(['error' => 'Invalid client certificate'], Response::HTTP_FORBIDDEN);
        }

        // // Extract certificate information (optional)
        // $certData = openssl_x509_parse($clientCert);
        // if (!$certData) {
        //     return response()->json(['error' => 'Unable to parse client certificate'], Response::HTTP_FORBIDDEN);
        // }

        // // Optional: Validate certificate expiration
        // $currentTime = time();
        // if ($certData['validTo_time_t'] < $currentTime || $certData['validFrom_time_t'] > $currentTime) {
        //     return response()->json(['error' => 'Client certificate has expired or is not yet valid'], Response::HTTP_FORBIDDEN);
        // }

        // // Optional: Attach certificate info to the request for further processing
        // $request->attributes->add(['certData' => $certData]);

        return $next($request);
    }
}
