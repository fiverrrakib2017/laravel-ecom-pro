<?php
namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\LeadService;
use App\Http\Requests\StoreLeadRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
 protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function index(Request $request)
    {
        $query = $this->leadService->getAll();

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
        $leads = $query->paginate(10);
        return view('Backend.Pages.Customer.Lead.index', compact('leads'));
    }
    public function create()
    {
        return view('Backend.Pages.Customer.Lead.create');
    }
    public function edit($id)
    {
        $lead=$this->leadService->find($id);
        return view('Backend.Pages.Customer.Lead.edit',compact('lead'));
    }

    public function store(StoreLeadRequest $request)
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
