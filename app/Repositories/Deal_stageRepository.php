<?php
namespace App\Repositories;
use App\Interfaces\Deal_stage_repository_Interface;
use App\Models\Deal_stage;
use Illuminate\Support\Arr;
class Deal_stageRepository implements Deal_stage_repository_Interface
{
    public function getAll()
    {
        return Deal_stage::query();
    }

    public function store(array $data)
    {
        $object = new Deal_stage();

        $name   = trim((string) Arr::get($data, 'name'));
        $isWon  = filter_var(Arr::get($data, 'is_won', false), FILTER_VALIDATE_BOOLEAN);
        $isLost = filter_var(Arr::get($data, 'is_lost', false), FILTER_VALIDATE_BOOLEAN);

        $object->name    = $name;
        $object->is_won  = $isWon;
        $object->is_lost = $isLost;

        $object->save();

        return $object;
    }


    public function find($id)
    {
        return Deal_stage::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $lead = Deal_stage::findOrFail($id);

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
        $object = Deal_stage::findOrFail($id);
        return $object->delete();
    }
}
