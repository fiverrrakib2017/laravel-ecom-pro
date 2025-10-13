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

        $object =  Deal_stage::findOrFail($id);

        $name   = trim((string) Arr::get($data, 'name'));
        $isWon  = filter_var(Arr::get($data, 'is_won', false), FILTER_VALIDATE_BOOLEAN);
        $isLost = filter_var(Arr::get($data, 'is_lost', false), FILTER_VALIDATE_BOOLEAN);

        $object->name    = $name;
        $object->is_won  = $isWon;
        $object->is_lost = $isLost;

        $object->update();

        return $object;
    }

    public function delete($id)
    {
        $object = Deal_stage::findOrFail($id);
        return $object->delete();
    }
}
