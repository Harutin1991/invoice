<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceTerm extends Model
{
    protected $table = 'invoice_term';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
