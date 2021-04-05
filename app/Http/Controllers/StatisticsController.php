<?php

namespace App\Http\Controllers;
use App\Allocations;
use App\Budget;
use App\Category;
use App\Fund;
use App\FundTemplate;
use App\School;
use App\SchoolYear;
use App\AllocationFundTemplate;
use Illuminate\Http\Request;
use Throwable;

class StatisticsController extends Controller
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

    public static function getCategory()
    {
        return Category::all();
    }

    public function getTotalAllocations($allocationType, $schoolYearId = null, Request $request)
    {
        return Allocations::where('allocation_type_id', $allocationType)->select('total_allocation', 'is_final')->get();
    }

    private function getAllocations($allocationType)
    {
        return AllocationFundTemplate
            ::where('allocation_fund_template.template_id', self::$currentTemplateId)
            ->where('allocation_fund_template.allocation_type_id', $allocationType)
            ->join('fund', 'fund.allocation_fund_template_id', '=', 'allocation_fund_template.id')
            ->select('fund.amount', 'allocation_fund_template.name', 'allocation_fund_template.id as allocationFundId')
            ->orderBy('allocation_fund_template.order', 'ASC')
            ->get();
    }

    public function getBudgetTotalsByCategory($allocationType, $schoolId = null, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $budgetBalance = [];
        $pagesCount = 0;
        $isFinal = false;
        try {
            //$allocations = $this->getAllocations($allocationType);
            $categories = self::getCategory();
            $budgetItems = Budget
                ::where('allocation_type_id', $allocationType)
                ->join('category', 'category.id', '=', 'budget_item.category_id')
                ->select('budget_item.unit_total_cost', 'category.name as categoryName', 'category.id as categoryId', 'budget_item.id as budgetId', 'budget_item.fund_id as fundId')
                ->get();
            $totals[] = 0;
            foreach ($budgetItems as $item) {
                $totals[$item->categoryId][] = $item->unit_total_cost;
            }

            foreach ($categories as $key => $category) {
                $budgetBalance[$key]['categoryName'] = $category->name;
                $budgetBalance[$key]['totals'] = isset($totals[$category->id]) ? array_sum($totals[$category->id]) : 0;
            }
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['budgetBalance' => $budgetBalance, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getListOfAllFunds($allocationType, $schoolId = null, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $itemsResponse = [];
        $pagesCount = 0;
        $isFinal = false;
        try {
            $totalsArray = [];
            $totalsResp = [];
            $allocations = $this->getAllocations($allocationType);
            foreach ($allocations as $allocation) {
                $totalsArray[$allocation->allocationFundId]['totals'][] = $allocation->amount;
                $totalsArray[$allocation->allocationFundId]['title'] = $allocation->name;
            }
            $i = 0;
            foreach ($totalsArray as $value) {
                $totalsResp[$i]['total'] = round(array_sum($value['totals']), 2);
                $totalsResp[$i]['title'] = $value['title'];
                $i++;
            }
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['item' => $totalsResp, 'pagesCount' => $pagesCount, 'isSchoolAllocationFinal' => $isFinal, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getRemainingBalance($allocationType)
    {
        $success = true;
        $errorMessage = '';
        try {
            $allocations = $this->getAllocations($allocationType);
            foreach ($allocations as $allocation) {
                $totalsArray[$allocation->allocationFundId]['totals'][] = $allocation->amount;
                $totalsArray[$allocation->allocationFundId]['title'] = $allocation->name;
            }

            foreach ($totalsArray as $key => $value) {
                $totalsResp[$key] = round(array_sum($value['totals']), 2);
            }

            $categories = self::getCategory();
            $budgetItems = Budget
                ::where('allocation_type_id', $allocationType)
                ->select('budget_item.unit_total_cost', 'budget_item.id as budgetId', 'budget_item.fund_id as fundId')
                ->get();
            $totals[] = 0;
            foreach ($budgetItems as $item) {
                if ($item->fundId) {
                    $totals[$item->fundId][] = $item->unit_total_cost;
                }
            }

            $totalSpent = [];
            foreach ($totals as $key => $total) {
                if (is_array($total)) {
                    $totalSpent[$key] = round(array_sum($total), 2);
                }
            }

            $remainingBalance = [];
            foreach ($totalsResp as $key => $totalAllocation) {
                if (isset($totalSpent[$key])) {
                    $remainingBalance[$key] =  $totalAllocation - $totalSpent[$key];
                }
            }
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }

        return response()->json(['remainingBalance' => $remainingBalance, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function getTotalSpentFunds($allocationType)
    {
        $success = true;
        $errorMessage = '';
        try {
            $allocations = $this->getAllocations($allocationType);
            foreach ($allocations as $allocation) {
                $totalsArray[$allocation->allocationFundId]['totals'][] = $allocation->amount;
                $totalsArray[$allocation->allocationFundId]['title'] = $allocation->name;
            }

            foreach ($totalsArray as $key => $value) {
                $totalsResp[$key] = round(array_sum($value['totals']), 2);
            }

            $categories = self::getCategory();
            $budgetItems = Budget
                ::where('allocation_type_id', $allocationType)
                ->select('budget_item.unit_total_cost', 'budget_item.id as budgetId', 'budget_item.fund_id as fundId')
                ->get();
            $totals[] = 0;
            foreach ($budgetItems as $item) {
                if ($item->fundId) {
                    $totals[$item->fundId][] = $item->unit_total_cost;
                }
            }

            $totalSpent = [];
            foreach ($totals as $key => $total) {
                if (is_array($total)) {
                    $totalSpent[$key] = round(array_sum($total), 2);
                }
            }

            $totalWithPercenatge = [];
            foreach ($totalsResp as $key => $totalAllocation) {
                if (isset($totalSpent[$key])) {
                    $totalWithPercenatge[$key] = ($totalSpent[$key] / $totalAllocation) * 100;
                }
            }
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }

        return response()->json(['item' => $totalWithPercenatge, 'success' => $success, 'errorMessage' => $errorMessage]);
    }
}
