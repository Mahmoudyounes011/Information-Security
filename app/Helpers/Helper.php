<?php
namespace App\Helpers;
use phpseclib3\Crypt\RSA;use Illuminate\Support\Facades\DB;
class Helper{
   


         // ابو عبدو هذا التابع لكي تشفر فيه سترينغاية وحدة يعني دخل واحد 

    public function encrypt($originalText)
    {
        
        $key = openssl_random_pseudo_bytes(32); 
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); 

        $encryptedText = openssl_encrypt($originalText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        $encryptedTextBase64 = base64_encode($iv . $encryptedText);

        
        return [
            'key' => base64_encode($key),
            'iv' => base64_encode($iv),
            'encryptedText' => $encryptedTextBase64
        ];
    }

        public  function decrypt($keyBase64, $ivBase64, $encryptedTextBase64)
        {
    

            $key = base64_decode($keyBase64);
            $iv = base64_decode($ivBase64);
    
            $decodedData = base64_decode($encryptedTextBase64);
            $extractedEncryptedText = substr($decodedData, openssl_cipher_iv_length('aes-256-cbc'));
    
            $decryptedText = openssl_decrypt($extractedEncryptedText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    
            return [
                'decryptedText' => $decryptedText
            ];
        }

        //هذا التابع يقوم بتشفير مصفوفة من المدخلات وارجاع مصفوفة مشفرة 
    public  function encryptArray(array $originalTexts)
    {
     

        $key = openssl_random_pseudo_bytes(32); 
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc')); 

        $encryptedTexts = [];

        foreach ($originalTexts as $originalText) {
            $encryptedText = openssl_encrypt($originalText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

            $encryptedTexts[] = base64_encode($encryptedText);
        }

        return [
            'key' => base64_encode($key),
            'iv' => base64_encode($iv),
            'encryptedTexts' => $encryptedTexts
        ];
    }

    
public  function decryptArray($keyBase64, $ivBase64, $encryptedTextsBase64)
{

    $key = base64_decode($keyBase64); 
    $iv = base64_decode($ivBase64);    

    if (is_array($encryptedTextsBase64)) {
        $decryptedTexts = [];
        foreach ($encryptedTextsBase64 as $encryptedTextBase64) {
            $decodedData = base64_decode($encryptedTextBase64); 
            $decryptedText = openssl_decrypt($decodedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv); // فك التشفير
            $decryptedTexts[] = $decryptedText; 
        }
        return json_encode(['decryptedTexts' => $decryptedTexts]);
    } else {
        $decodedData = base64_decode($encryptedTextsBase64); 
        $decryptedText = openssl_decrypt($decodedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv); // فك التشفير
        return ['decryptedText' => $decryptedText]; 
    }
}

public function encryptRSA($text)
{
        $publicKey = DB::table('server_keys')->value('public_key');
    if (!$publicKey) {
        throw new \Exception('Public key not found in database');
    }
    
    $rsaPublic = RSA::loadPublicKey($publicKey);
    
    return base64_encode($rsaPublic->encrypt($text));
}

public function decryptRSA($encryptedText)
{
    $privateKey = DB::table('server_keys')->value('private_key');
    if (!$privateKey) {
        throw new \Exception('Private key not found in database');
    }
    
    $rsaPrivate = RSA::loadPrivateKey($privateKey);
    return $rsaPrivate->decrypt(base64_decode($encryptedText));
}

}