<?php

namespace App\Http\Controllers;
use App\Invoice;
use App\SchoolYear;
use App\Helper\ResponseHelper;
use Illuminate\Http\Request;
use Throwable;

class InvoiceController extends Controller
{
    private $limit = 10;
    private $page = 1;
    private static $currentYearId = NULL;
    private $schoolAllocation = null;
    private $schoolId = null;
    private static $allocationType = 1;

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
        $invoiceResponse = [];
        $pagesCount = 0;
        $invoiceCount = 0;
        try {
            $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
            $page = $request->get('page') ? $request->get('page') : $this->page;
            $skip = (!$page) ? 0 : ($page - 1) * $limit;

            $invoiceItems = Invoice::with('paymentStatus','paymentType','invoiceType','invoiceStatus')
                ->where('invoice.school_year_id',self::$currentYearId)
                ->join('school', 'school.id', '=', 'invoice.school_id')
                ->select('invoice.*','school.name as schoolName')
                ->orderBy('school.name','asc')
                ->skip($skip)->take($limit)
                ->get();

            $invoiceResponse = ResponseHelper::makeInvocieData($invoiceItems);
            $invoiceCount = Invoice::count();
            $pagesCount = ceil($invoiceCount / $limit);
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['items' => $invoiceResponse,'totalCount'=>$invoiceCount, 'success' => $success, 'errorMessage' => $errorMessage, 'pagesCount' => $pagesCount]);
    }

    public function getInvoiceItem($id)
    {
        $success = true;
        $errorMessage = '';
        $invoiceResponse = [];
        try {

            $invoiceItems = Invoice::with('paymentStatus', 'paymentType', 'invoiceType', 'invoiceStatus')
                ->where('invoice.id', $id)
                ->join('school', 'school.id', '=', 'invoice.school_id')
                ->select('invoice.*', 'school.name as schoolName')
                ->get();

            $invoiceResponse = ResponseHelper::makeInvocieData($invoiceItems);
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['item' => $invoiceResponse, 'success' => $success, 'errorMessage' => $errorMessage]);
    }
}
