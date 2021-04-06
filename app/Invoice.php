<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'sea_id', 'lea_id', 'ses_id', 'id', 'name', 'description', 'number', 'note', 'date',
        'invoice_term_id', 'due_date', 'created_at', 'created_by', 'school_year_id', 'school_id',
        'bill_to_id', 'ship_to_id', 'subtotal', 'markup_fee', 'markup_percentage', 'tax', 'shipping_fee',
        'total_amount', 'invoice_status_id', 'payment_status_id', 'payment_type_id', 'invoice_type_id'
    ];

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        // TODO: Implement resolveChildRouteBinding() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentStatus()
    {
        return $this->hasOne('App\PaymentStatus', 'id','payment_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->hasOne('App\School', 'id','school_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentType()
    {
        return $this->hasOne('App\PaymentType'); //, 'id','payment_type_id'
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceType()
    {
        return $this->hasOne('App\InvoiceType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceStatus()
    {
        return $this->hasOne('App\InvoiceStatus');
    }

}
