<?php

namespace App\Services;

use App\Interfaces\Deal_stage_repository_Interface;

class Deal_stageService
{
    protected $deal_stageRepository;

    public function __construct(Deal_stage_repository_Interface $deal_stageRepository)
    {
        $this->deal_stageRepository = $deal_stageRepository;
    }

    public function getAll()
    {
        return $this->deal_stageRepository->getAll();
    }
    public function create(array $data)
    {
        return $this->deal_stageRepository->store($data);
    }
    public function delete($id)
    {
        return $this->deal_stageRepository->delete($id);
    }
    public function find($id)
    {
        return $this->deal_stageRepository->find($id);
    }
    public function update( $id , array $data){
        return $this->deal_stageRepository->update($id,$data);
    }


}
