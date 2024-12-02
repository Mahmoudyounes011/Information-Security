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

    public  function findUserByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public  function createUser(StoreUserRequest $request)
    {
        try{
            
            $data = $request->validated();
            //here we hash the password in hash we cannot retrive to main string.

            $data['password'] = bcrypt($data['password']);

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

    public function getGroupsByUser($userId)
    {
        return $this->userRepository->getGroupsByUser($userId);
    }

    public function getUserInvitations($userId)
    {
        return $this->userRepository->getUserInvitations($userId);
    }

    public function searchByName( $value)
    {
        return $this->userRepository->searchByName($value);
    }

    public function updateUserImage($userId, $imageFile)
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception("User not found.");
        }
    
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }
    
        $imagePath = $this->uploadImage($imageFile, 'users');
        if (!$imagePath) {
            throw new \Exception("Failed to upload image.");
        }
    
        return $this->userRepository->updateImage($userId, $imagePath);
    }

    public function removeUserImage($userId)
    {
        return $this->userRepository->deleteImage($userId);
    }

    private function uploadImage($imageFile, $folder)
    {
        $fileName = uniqid() . '_' . $imageFile->getClientOriginalName();
        return $imageFile->storeAs($folder, $fileName, 'public');
    }

    public function allUsers()
    {
        return $this->userRepository->all();
    }



}