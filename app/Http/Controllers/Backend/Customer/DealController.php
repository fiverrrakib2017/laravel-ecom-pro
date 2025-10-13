<?php
namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\DealService;
use App\Http\Requests\deal_request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DealController extends Controller
{
 protected $dealService;

    public function __construct(DealService $dealService)
    {
        $this->dealService = $dealService;
    }

    public function index(Request $request)
    {
       $deals = $this->dealService->getAll()
                ->with(['lead', 'client', 'stage', 'user'])->get();
        return view('Backend.Pages.Customer.Deal.index', compact('deals'));
    }
    public function create()
    {
        return view('Backend.Pages.Customer.Deal.create');
    }
    public function edit($id)
    {
        $deals=$this->dealService->find($id);
        return view('Backend.Pages.Customer.Deal.edit',compact('deals'));
    }
    public function update(deal_request $request , $id){
        $validatedData = $request->validated();
        $this->dealService->update($id , $validatedData);
         return response()->json([
            'success'=>true,
            'message' => 'Lead Update successfully!',
        ]);
    }

    public function store(deal_request $request)
    {
        $validatedData = $request->validated();
        $this->dealService->create($validatedData);
        return response()->json([
            'success'=>true,
            'message' => 'Deal created successfully!',
        ]);
    }
    public function delete(Request $request)
    {
        $this->dealService->delete($request->id);
        return response()->json([
            'success'=>true,
            'message' => 'Delete successfully!',
        ]);
    }
    public function view($id){
        $lead=$this->dealService->find($id);
        if ($lead) {
            return response()->json([
                'success' => true,
                'data' => $lead
            ]);
        }

        /*------If lead not found------*/
        return response()->json([
            'success' => false,
            'message' => 'Lead not found'
        ]);
    }
}
