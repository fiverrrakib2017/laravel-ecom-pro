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
        return $lead->save();
    }


    public function find($id)
    {
        return Lead::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $lead = Lead::findOrFail($id);

        $lead->full_name          = $data['full_name'] ?? $lead->full_name;
        $lead->phone              = $data['phone'] ?? $lead->phone;
        $lead->email              = $data['email'] ?? $lead->email;
        $lead->address            = $data['address'] ?? $lead->address;
        $lead->source             = $data['source'] ?? $lead->source;
        $lead->status             = $data['status'] ?? $lead->status;
        $lead->priority           = $data['priority'] ?? $lead->priority;
        $lead->interest_level     = $data['interest_level'] ?? $lead->interest_level;
        $lead->service_interest   = $data['service_interest'] ?? $lead->service_interest;
        $lead->feedback           = $data['feedback'] ?? $lead->feedback;
        $lead->lead_score         = $data['lead_score'] ?? $lead->lead_score;
        $lead->user_id            = auth()->guard('admin')->user()->id ?? NULL;
        $lead->estimated_close_date = $data['estimated_close_date'] ?? $lead->estimated_close_date;
        $lead->follow_up_required = $data['follow_up_required'] ?? $lead->follow_up_required;
        $lead->first_contacted_at = $data['first_contacted_at'] ?? $lead->first_contacted_at;
        $lead->last_contacted_at  = $data['last_contacted_at'] ?? $lead->last_contacted_at;
        $lead->campaign_source    = $data['campaign_source'] ?? $lead->campaign_source;
        $lead->follow_up_count    = $data['follow_up_count'] ?? $lead->follow_up_count;
        $lead->internal_notes     = $data['internal_notes'] ?? $lead->internal_notes;


        $lead->update();

        return $lead;
    }

    public function delete($id)
    {
        $lead = Lead::findOrFail($id);
        return $lead->delete();
    }
}
