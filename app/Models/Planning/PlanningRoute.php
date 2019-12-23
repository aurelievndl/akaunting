<?php

namespace App\Models\Planning;

use App\Abstracts\Model;

class PlanningRoute extends Model
{

    protected $table = 'planning_routes';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['title'];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name'];

    /**
     * Get the name including rate.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        $title = $this->name . ' (';

        if (setting('localisation.percent_position', 'after') == 'after') {
            $title .= $this->getAttribute('type') == 'fixed' ?  $this->rate : $this->rate . '%';
        } else {
            $title .= $this->getAttribute('type') == 'fixed' ?  $this->rate : '%' . $this->rate;
        }
        $title .= ')';

        return $title;
    }
}
