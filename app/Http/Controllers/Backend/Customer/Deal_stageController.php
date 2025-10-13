<?php
namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\Deal_stageService;
use App\Http\Requests\Store_deal_stage_request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Deal_stageController extends Controller
{
 protected $deal_stage_service;

    public function __construct(Deal_stageService $deal_stage_service)
    {
        $this->deal_stage_service = $deal_stage_service;
    }

    public function index(Request $request)
    {
        $query = $this->deal_stage_service->getAll();

        /*-------------- Filter by status if selected------------*/
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        /*------------Search functionality----------*/
        if ($request->has('q') && $request->q !== '') {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('full_name', 'like', '%' . $request->q . '%')
                    ->orWhere('phone', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        /*-----Paginate results-------*/
        $deal_stages = $query->paginate(10);
        return view('Backend.Pages.Customer.Deal.Stages.index', compact('deal_stages'));
    }
    public function create()
    {
        $stage=null;
        return view('Backend.Pages.Customer.Deal.Stages.create',compact('stage'));
    }
    public function edit($id)
    {
        $deal_stages=$this->deal_stage_service->find($id);
        return view('Backend.Pages.Customer.Deal.Stages.edit',compact('deal_stages'));
    }
    public function update(Store_deal_stage_request $request , $id){
        $validatedData = $request->validated();
        $this->deal_stage_service->update($id , $validatedData);
         return response()->json([
            'success'=>true,
            'message' => 'Lead Update successfully!',
        ]);
    }

    public function store(Store_deal_stage_request $request)
    {
        $validatedData = $request->validated();
        $this->leadService->createLead($validatedData);
        return response()->json([
            'success'=>true,
            'message' => 'Deal Stage Created',
        ]);
    }
    public function delete(Request $request)
    {
        $this->deal_stage_service->delete($request->id);
        return response()->json([
            'success'=>true,
            'message' => 'Delete successfully!',
        ]);
    }
}
