<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocations extends Model
{
    protected $table = 'allocation';

    public static $allocationTypes = [
        'title1' => 1,
        'title2' => 2,
        'title3' => 3,
        'title4' => 4,
        'esser' => 5,
        'geer' => 6,
    ];

    public static $status = ['fn' => 1, 'pr' => 0];
    public static $allocationTypesRegular = [5, 6];
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'school_id', 'school_year_id', 'allocation_type_id', 'total_allocation', 'is_final', 'creation_date', 'note'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function school()
    {
        return $this->belongsTo('App\School', 'school_id');
    }

    public function allocationFund()
    {
        return $this->hasOne('App\AllocationFund', 'id','allocation_id');
    }
}
