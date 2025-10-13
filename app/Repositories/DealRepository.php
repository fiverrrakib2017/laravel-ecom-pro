<?php
namespace App\Repositories;
use App\Interfaces\DealRepositoryInterface;
use App\Models\Deal;

class DealRepository implements DealRepositoryInterface
{
    public function getAll()
    {
        return Deal::query();
    }

    public function store(array $data)
    {
        $deal = new Deal();

        $deal->title               = trim($data['title']);
        $deal->lead_id             = $data['lead_id'];
        $deal->client_id           = $data['client_id'];
        $deal->stage_id            = $data['stage_id'];
        $deal->amount              = $data['amount'];
        $deal->expected_close_date = $data['expected_close_date'];
        $deal->user_id             = auth()->guard('admin')->user()->id ?? null;

       return $deal->save();
    }


    public function find($id)
    {
        return Deal::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $deal = Deal::findOrFail($id);

        $deal->title               = trim($data['title']);
        $deal->lead_id             = $data['lead_id'];
        $deal->client_id           = $data['client_id'];
        $deal->stage_id            = $data['stage_id'];
        $deal->amount              = $data['amount'];
        $deal->expected_close_date = $data['expected_close_date'];
        $deal->user_id             = auth()->guard('admin')->user()->id ?? null;


        $deal->update();

        return $deal;
    }

    public function delete($id)
    {
        $object = Deal::findOrFail($id);
        return $object->delete();
    }
}
