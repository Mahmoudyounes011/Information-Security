<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Invitation;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByUserName(string $user_name)
    {
        return $this->model->where('phone_num', $user_name)->first();
    }

    public function getGroupsByUser($userId)
    {
        $user = $this->find($userId);
        return $user->groups;
    }

    public function searchByName($value)
    {
        return $this->model->where(function($query) use($value){
            $query->where('first_name','like',"%".$value."%") 
            ->orWhere('last_name','like',"%".$value."%");
        })->get();
    }
    public function getUserInvitations($userId)
    {
        $user = $this->model->find($userId);
        return $user->invitations;
    }

    public function updateImage($userId, $imagePath)
    {
        //dd($imagePath);
        return $this->model->where('id', $userId)->update(['image' => $imagePath]);
    }

    public function deleteImage($userId)
    {
        $user = User::findOrFail($userId);
        $user->image = null;
        $user->save();
        return $user;
    }

    
}
