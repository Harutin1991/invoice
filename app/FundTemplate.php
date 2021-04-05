<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FundTemplate extends Model
{
    protected $table = 'fund_template';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'note'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }
}
