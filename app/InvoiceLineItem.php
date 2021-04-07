<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLineItem extends Model
{
    protected $table = 'invoice_line_item';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id', 'lea_id', 'ses_id', 'item_id','invoice_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budget()
    {
        return $this->hasMany('App\Budget', 'id','item_id')->with('allocation');
    }

}
