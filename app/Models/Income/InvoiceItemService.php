<?php

namespace App\Models\Income;

use App\Abstracts\Model;

class InvoiceItemService extends Model
{

    protected $table = 'invoice_item_services';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'invoice_id', 'invoice_item_id', 'service_id', 'name'];

    public function invoice()
    {
        return $this->belongsTo('App\Models\Income\Invoice');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Setting\Service');
    }
}
