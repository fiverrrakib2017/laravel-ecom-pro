<?php
namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\DealService;
use App\Http\Requests\StoreDealRequest;
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
        $query = $this->dealService->getAll();

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
        $deals = $query->paginate(10);
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
    public function update(StoreDealRequest $request , $id){
        $validatedData = $request->validated();
        $this->dealService->update($id , $validatedData);
         return response()->json([
            'success'=>true,
            'message' => 'Lead Update successfully!',
        ]);
    }

    public function store(StoreDealRequest $request)
    {
        $validatedData = $request->validated();
        $this->leadService->createLead($validatedData);
        return response()->json([
            'success'=>true,
            'message' => 'Lead created successfully!',
        ]);
    }
    public function delete(Request $request)
    {
        $this->leadService->delete($request->id);
        return response()->json([
            'success'=>true,
            'message' => 'Delete successfully!',
        ]);
    }
    public function view($id){
        $lead=$this->leadService->find($id);
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
