<?php 

namespace App\Services;

use App\Contract\MainInterface;
use App\Repositories\BaseRepository;

class BaseService{

    protected MainInterface $repository;

    public function __construct(MainInterface $repository){
        $this->repository = $repository;
    }

    public function all(){
        return $this->repository->all();
    }

    public function create(array $data){
        return $this->repository->create($data);
    }

    public function update(array $data,$id){
    return $this->repository->update($data,$id);
    }

    public function delete($id)
    {
    return $this->repository->delete($id);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }
}