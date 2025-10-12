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

    public function index()
    {
       return view('Backend.Pages.Customer.Lead.index');
    }
    public function create()
    {
        return view('Backend.Pages.Customer.Lead.create');
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
}
