<?php 

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\BaseService;
use App\Http\Requests\StoreUserRequest;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;


class UserService extends BaseService{

    protected  UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        Parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    public  function findByUserName(string $user_name)
    {
        return $this->userRepository->findByUserName($user_name);
    }

    public  function createUser(StoreUserRequest $request)
    {
        try{
            
            $data = $request->validated();
            //here we hash the password in hash we cannot retrive to main string.

            
            $data['password'] = bcrypt($data['password']);
            $data['balance'] = Crypt::encryptString( $data['balance']); 

            //dd($data['balance']);
            //here we encryptthe password and decrypted so we can after the encrypt to retrive the main value.
            
            // $encryptedPassword = Crypt::encryptString($data['password']);
            // $decryptedPassword = Crypt::decryptString($encryptedPassword);
            // dd($encryptedPassword,$decryptedPassword);

            $user = $this->userRepository->create($data);
            return $user;
        }
        catch (Exception $e) {
            dd($e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }




}