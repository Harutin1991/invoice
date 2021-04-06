<?php

namespace App\Http\Controllers;
use App\Allocations;
use App\Budget;
use App\Category;
use App\Fund;
use App\Invoice;
use App\School;
use App\SchoolYear;
use App\AllocationFundTemplate;
use Illuminate\Http\Request;
use Throwable;

class InvoiceController extends Controller
{
    private static $currentYearId = NULL;
    private static $currentTemplateId = 45;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        if (!self::$currentYearId) {
            $currentYear = SchoolYear::where('is_current', 1)->first();
            self::$currentYearId = $currentYear->id;
        }
    }

    public function index(Request $request)
    {
        $success = true;
        $errorMessage = '';
        $invocieResponse = [];
        $pagesCount = 0;
        try {
            $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
            $page = $request->get('page') ? $request->get('page') : $this->page;
            $skip = (!$page) ? 0 : ($page - 1) * $limit;

            $invoiceItems = Invoice::with('paymentStatus','paymentType','invoiceType','invoiceStatus')
                ->where('invoice.school_year_id',self::$currentYearId)
                ->join('school', 'school.id', '=', 'fund.school_id')
                ->select('invoice.*','school.name as schoolName')
                ->orderBy('school.name','asc')
                ->skip($skip)->take($limit)
                ->get();

            $invocieResponse = ResponseHelper::makeInvocieData($invoiceItems);
            $schoolsCount = School::where('is_active', 1)->count();
            $pagesCount = ceil($schoolsCount / $limit);
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['schools' => $schoolsResponse, 'success' => $success, 'errorMessage' => $errorMessage, 'pagesCount' => $pagesCount]);
    }
}
