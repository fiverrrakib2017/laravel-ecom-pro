<?php

namespace App\Services;

use App\Interfaces\DealRepositoryInterface;

class DealService
{
    protected $dealRepository;

    public function __construct(DealRepositoryInterface $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    public function getAll()
    {
        return $this->dealRepository->getAll();
    }
    public function create(array $data)
    {
        return $this->dealRepository->store($data);
    }
    public function delete($id)
    {
        return $this->dealRepository->delete($id);
    }
    public function find($id)
    {
        return $this->dealRepository->find($id);
    }
    public function update( $id , array $data){
        return $this->dealRepository->update($id ,  $data);
    }


}
