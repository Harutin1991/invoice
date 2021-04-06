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
        }
        return $invoiceResponse;
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
