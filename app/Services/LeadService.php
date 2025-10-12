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

    public function createLead(array $data)
    {
        return $this->leadRepository->store($data);
    }

    
}
