<?php

namespace App\Models\Setting;

use App\Abstracts\Model;

class Service extends Model
{

    protected $table = 'services';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['title'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name', 'enabled'];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'enabled'];

    public function invoice_items()
    {
        return $this->hasMany('App\Models\Income\InvoiceItemService');
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->name;
    }
}
