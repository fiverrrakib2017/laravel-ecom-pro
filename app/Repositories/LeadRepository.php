<?php
namespace App\Repositories;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;

class LeadRepository implements LeadRepositoryInterface
{
    public function getAll()
    {
        return Lead::latest()->get();
    }

    public function store(array $data)
    {
        $lead = new Lead();

        $lead->full_name          = $data['full_name'] ?? null;
        $lead->phone              = $data['phone'] ?? null;
        $lead->email              = $data['email'] ?? null;
        $lead->address            = $data['address'] ?? null;
        $lead->source             = $data['source'] ?? 'other';
        $lead->status             = $data['status'] ?? 'new';
        $lead->priority           = $data['priority'] ?? 'medium';
        $lead->interest_level     = $data['interest_level'] ?? 'medium';
        $lead->service_interest   = $data['service_interest'] ?? null;
        $lead->feedback           = $data['feedback'] ?? null;
        $lead->lead_score         = $data['lead_score'] ?? 0;
        $lead->user_id            = auth()->guard('admin')->user()->id ?? NULL;
        $lead->estimated_close_date = $data['estimated_close_date'] ?? null;
        $lead->follow_up_required = $data['follow_up_required'] ?? false;
        $lead->first_contacted_at = $data['first_contacted_at'] ?? null;
        $lead->last_contacted_at  = $data['last_contacted_at'] ?? null;
        $lead->campaign_source    = $data['campaign_source'] ?? null;
        $lead->follow_up_count    = $data['follow_up_count'] ?? 0;
        $lead->internal_notes     = $data['internal_notes'] ?? null;

        $lead->save();

        return $lead;
    }


    public function find($id)
    {
        return Lead::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $lead = Lead::findOrFail($id);
        $lead->update($data);
        return $lead;
    }

    public function delete($id)
    {
        $lead = Lead::findOrFail($id);
        return $lead->delete();
    }
}
