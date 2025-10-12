<?php
namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\LeadService;
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
        $this->leadService->createLead($request->validated());

        return redirect()->back()->with('success', 'Lead created successfully!');
    }
}
