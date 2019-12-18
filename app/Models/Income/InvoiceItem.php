<?php

namespace App\Models\Income;

use App\Abstracts\Model;
use App\Traits\Currencies;
use Bkwld\Cloner\Cloneable;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class InvoiceItem extends Model
{

    use Cloneable, Currencies;

    protected $table = 'invoice_items';

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
    protected $fillable = ['company_id', 'invoice_id', 'item_id', 'name', 'quantity', 'price', 'total', 'tax'];

    /**
     * Attributes that should be casted to native types.
     *
     * @var array
     */
    public $casts = [
        'extra_attributes' => 'array',
    ];

    /**
     * Clonable relationships.
     *
     * @var array
     */
    public $cloneable_relations = ['taxes'];

    public function invoice()
    {
        return $this->belongsTo('App\Models\Income\Invoice');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Common\Item');
    }

    public function taxes()
    {
        return $this->hasMany('App\Models\Income\InvoiceItemTax', 'invoice_item_id', 'id');
    }

    /**
     * Convert price to double.
     *
     * @param  string  $value
     * @return void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (double) $value;
    }

    /**
     * Convert total to double.
     *
     * @param  string  $value
     * @return void
     */
    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = (double) $value;
    }

    /**
     * Convert tax to double.
     *
     * @param  string  $value
     * @return void
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = (double) $value;
    }

    /**
     * Get the name including attributes.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        $title = $this->attributes['name'];

        if ($this->extra_attributes->material['type']) {
            $title .= ' ' . $this->extra_attributes->material['type'];
        }

        if ($this->extra_attributes->amount['value']) {
            $title .= ' ' . $this->extra_attributes->amount['value'] . 'x ';
        }

        if ($this->extra_attributes->size['width']) {
            $title .= ' ' . $this->extra_attributes->size['width'] . ' cm';
        }

        if ($this->extra_attributes->size['height']) {
            $title .= '/' . $this->extra_attributes->size['height'] . ' cm';
        }

        return $title;
    }

    /**
     */
    public function getExtraAttributesAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    /**
     */
    public function scopeWithExtraAttributes(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('extra_attributes');
    }
}
