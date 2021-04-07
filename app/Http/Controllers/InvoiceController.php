<?php

namespace App\Http\Controllers;
use App\Budget;
use App\Invoice;
use App\InvoiceTerm;
use App\SchoolYear;
use App\InvoiceStatus;
use App\InvoiceType;
use App\PaymentStatus;
use App\PaymentType;
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

            $invoiceItems = Invoice::with('paymentStatus', 'paymentType', 'invoiceType', 'invoiceStatus', 'lineItem', 'allocation', 'fundSource')
                ->where('invoice.school_year_id', self::$currentYearId)
                ->join('school', 'school.id', '=', 'invoice.school_id')
                ->select('invoice.*', 'school.name as schoolName')
                ->orderBy('school.name', 'asc')
                ->skip($skip)->take($limit)
                ->get();

            $invoiceResponse = ResponseHelper::makeInvocieData($invoiceItems);
            $invoiceCount = Invoice::count();
            $pagesCount = ceil($invoiceCount / $limit);
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['items' => $invoiceResponse, 'totalCount' => $invoiceCount, 'success' => $success, 'errorMessage' => $errorMessage, 'pagesCount' => $pagesCount]);
    }

    public function getInvoiceItem($id)
    {
        $success = true;
        $errorMessage = '';
        $invoiceResponse = [];
        try {

            $invoiceItems = Invoice::with('paymentStatus', 'paymentType', 'invoiceType', 'invoiceStatus', 'lineItem', 'allocation', 'fundSource')
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

    public function getInvoiceStatuses()
    {
        $success = true;
        $errorMessage = '';
        $invoiceStatus = [];
        try {
            $invoiceStatus = InvoiceStatus::all();
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['invoiceStatus' => $invoiceStatus, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getPaymentStatuses()
    {
        $success = true;
        $errorMessage = '';
        $paymentStatus = [];
        try {
            $paymentStatus = PaymentStatus::all();
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['paymentStatus' => $paymentStatus, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getInvoiceType()
    {
        $success = true;
        $errorMessage = '';
        $invoiceType = [];
        try {
            $invoiceType = InvoiceType::all();
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['invoiceType' => $invoiceType, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getPaymentType()
    {
        $success = true;
        $errorMessage = '';
        $paymentType = [];
        try {
            $paymentType = PaymentType::all();
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['paymentType' => $paymentType, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getTerms()
    {
        $success = true;
        $errorMessage = '';
        $terms = [];
        try {
            $terms = InvoiceTerm::all();
        } catch (\Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['terms' => $terms, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getBudgetList($allocationType, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $itemsResponse = [];
        $pagesCount = 0;
        try {
            $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
            $page = $request->get('page') ? $request->get('page') : $this->page;
            $skip = (!$page) ? 0 : ($page - 1) * $limit;

            $startDate = $request->get('start_date') ? $request->get('start_date') : null;
            $endDate = $request->get('end_date') ? $request->get('end_date') : null;

            $budgetItems = Budget::with('category', 'subCategory', 'school', 'supplier', 'status', 'details', 'fundSource', 'fund')->
            where('allocation_type_id', $allocationType)->where('budget.details.is_invoiced', 1);
            if ($startDate) {
                $budgetItems->where('budget.start_date', $startDate);
            }
            if ($endDate) {
                $budgetItems->where('budget.end_date', $endDate);
            }
            $budgetItems->skip($skip)->take($limit)->orderBy('start_date', 'DESC')->get();
            $itemsResponse = ResponseHelper::makeBudgetData($budgetItems);
            $pagesCount = ceil(count($budgetItems) / $limit);

        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['items' => $itemsResponse, 'pagesCount' => $pagesCount, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function addInvoice($allocationType, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $itemsResponse = [];
        $pagesCount = 0;
        try {
            $budgetIds = $request->get('budgetIds');
            $budgets = Budget::whereIn('id', $budgetIds)->get();
            foreach ($budgets as $activity) {
                $invoiceData = [
                    'name' => $activity->name,
                    'description' => $activity->description,
                    'number' => 123546,
                    'note' => $activity->note,
                    'date' => date('Y-m-d'),
                    'created_by' => "Admin",
                    'school_year_id' => self::$currentYearId,
                    'school_id' => $activity->school_id,
                    'markup_fee' => $activity->markup_fee,
                    'markup_percentage' => $activity->markup_percentage,
                    'total_amount' => $activity->unit_total_cost,
                    'allocation_type_id' => $allocationType,
                    'fund_source_id' => $activity->fund_source_id,
                ];
                if ($invoice = Invoice::create($invoiceData)) {
                    $invoiceLineItem = ['item_id' => $activity->id, 'invoice_id' => $invoice->id];
                    InvoiceLineItem::create($invoiceLineItem);
                }
            }
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['items' => $itemsResponse, 'pagesCount' => $pagesCount, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

}
