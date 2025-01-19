<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\BalanceRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;


class UserController extends Controller
{
    use ApiResponse;

    private Helper $helper;
    public  function __construct(Helper $helper) {
        $this->helper = $helper;
    }

    
    public function withdraw(Request $request)
    {
        $resultKey = $this->helper->encryptRSA("Yn2MaEnHwCNnOHlf\/Dufd7r3su7EWaulucmfONUTT5k=");
//          $resultIv = $this->helper->encryptRSA("CA1yEW3dSwtc2siHMQrWtQ==");
//          dd($resultKey ,$resultIv);
        //here get encryption key from client
        $keyI = $request->input('key');

        //here get encryption iv from client
        $ivI = $request->input('iv');
        //here get encryption encryptedText from client
        $encryptedText = $request->input('encryptedText');
        
        //here decryptRSA for key
        $key = $this->helper->decryptRSA($keyI);
        //here decryptRSA for iv
        $iv = $this->helper->decryptRSA($ivI);
        dd($key,$iv);
        //here decrypt encryptedText using decryption (key,iv)
        $result = $this->helper->decrypt($key,$iv,$encryptedText);
        
        $amount = $result['decryptedText'];
        
        if (!is_numeric($amount) || $amount <= 0) {
            return response()->json(['message' => 'Invalid amount'], 400);
        }
            $user = Auth::user();

            $userBalance = $user->getRawOriginal('balance');
            
            $newBalance =  $userBalance - $amount ;

        
        $result =  User::where('id', $user->id)->update([
                'balance' => $newBalance 
            ]);
    
            return response()->json(['encrypted_balance' => 'success'], 201);
    }
   
    
}
