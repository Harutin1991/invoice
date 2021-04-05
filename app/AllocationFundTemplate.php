<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationFundTemplate extends Model
{
    protected $table = 'allocation_fund_template';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
