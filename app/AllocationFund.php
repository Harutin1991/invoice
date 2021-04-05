<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationFund extends Model
{
    protected $table = 'allocation_fund';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'fund_id', 'allocation_id', 'allocation_type_id', 'category_id', 'sub_category_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    public function fund()
    {
        return $this->hasOne('App\Fund', 'id','fund_id');
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id','category_id')->where('is_subcategory',null);
    }

    public function subCategory()
    {
        return $this->hasOne('App\Category', 'id','sub_category_id');
    }

    public function type()
    {
        return $this->hasOne('App\AllocationType', 'id','allocation_type_id');
    }
}
