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
        // এখানে তুমি চাইলে অতিরিক্ত লজিক রাখতে পারো
        $data['lead_score'] = $this->calculateScore($data);
        return $this->leadRepository->store($data);
    }

    private function calculateScore($data)
    {
        $score = 0;
        if ($data['priority'] == 'high') $score += 20;
        if ($data['interest_level'] == 'high') $score += 30;
        return $score;
    }
}
