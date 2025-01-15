<?php 

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\BaseService;
use App\Http\Requests\StoreUserRequest;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
            
            $user = $this->userRepository->create($data);
            $user->refresh();
           //dd($user);
            return $user;
        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }




}