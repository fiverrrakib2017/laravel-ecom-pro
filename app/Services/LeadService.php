<?php

namespace App\Services;

use App\Interfaces\LeadRepositoryInterface;

class LeadService
{
    protected $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function getAll()
    {
        return $this->leadRepository->getAll();
    }
    public function createLead(array $data)
    {
        return $this->leadRepository->store($data);
    }
    public function delete($id)
    {
        return $this->leadRepository->delete($id);
    }


}
