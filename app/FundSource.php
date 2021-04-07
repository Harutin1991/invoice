<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class FundSource extends Model
{
    protected $table = 'fund_source';

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

}
