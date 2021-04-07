<?php


namespace App\Helper;

class ResponseHelper
{
    const OK = 200;
    const UNAUTHORIZED = 401;
    const UNPROCESSABLE_ENTITY_EXPLAINED = 422;

    public static function success($data, $pagination = null, $msg = null)
    {
        if($pagination == null) {
            $response = array(
                "message" => $msg ?? "",
                "data" => $data,
                "status" => true
            );
        } else {
            $data = $data->toArray();
            $response = array(
                "message" => $msg ?? "",
                "data" => array(
                    "list" => $data["data"],
                    "meta" => array(
                        "page" => $data['current_page'],
                        "limit" => intval($data['per_page']),
                        "total" => $data['total'],
                        "last_page" => $data["last_page"]
                    ),
                ),
                "status" => true
            );
        }
        return response()->json($response, 200);
    }

    public static function makeInvocieData($invoices)
    {
        $invoiceResponse = [];
        foreach ($invoices as $key => $invoice) {
            $invoiceResponse[$key] = $invoice;
            $invoiceResponse[$key]['due_date'] = date('d/m/Y',strtotime($invoice->due_date));
            $invoiceResponse[$key]['date'] = date('d/m/Y',strtotime($invoice->date));
        }
        return $invoiceResponse;
    }

    public static function makeBudgetData($budgetItems)
    {
        $activityResponse = [];
        foreach ($budgetItems as $key => $activity) {
            $activityResponse[$key] = $activity;
            $activityResponse[$key]['total_cost'] = round($activity->unit_total_cost,2);
            $activityResponse[$key]['cost'] = round($activity->unit_cost,2);

            $activityResponse[$key]['start_date'] = ($activity->start_date) ? date('m-d-Y',strtotime($activity->start_date)) : null;
            $activityResponse[$key]['end_date'] = ($activity->end_date) ? date('m-d-Y',strtotime($activity->end_date)) : null;
            $activityResponse[$key]['completed_date'] = ($activity->completed_date) ? date('m-d-Y',strtotime($activity->completed_date)) : null;
        }

        return $activityResponse;
    }

    public static function fail($msg, $code)
    {
        $response = array(
            "message" => $msg ?? "",
            "data" => array(),
            "status" => false
        );
        return response()->json($response, $code);
    }
}
