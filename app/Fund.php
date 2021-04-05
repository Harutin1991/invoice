<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $table = 'fund';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'allocation_fund_template_id', 'percentage', 'amount','allocation_type_id',
        'school_id', 'allocation_id', 'school_year_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function allocation()
    {
        return $this->hasOne('App\Allocations', 'id','allocation_id');
    }

    public function allocationFund()
    {
        return $this->hasOne('App\AllocationFund', 'allocation_fund_template_id','allocation_fund_template_id');
    }

    public function school()
    {
        return $this->hasOne('App\School', 'id','school_id');
    }
}
