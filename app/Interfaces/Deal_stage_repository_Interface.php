<?php
namespace App\Interfaces;
use Illuminate\Http\Request;
interface Deal_stage_repository_Interface
{
    public function getAll();
    public function store(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
}
