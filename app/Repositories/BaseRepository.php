<?php

namespace App\Repositories;

use App\Contract\MainInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements MainInterface
{

    protected Model $model;

    public function __Construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->paginate(5);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    public function update(array $data, $id)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        $record->delete();
    }

    public function search($column, $value)
    {
        return $this->model->where($column,'like',"%".$value."%")->get();
    }


}
