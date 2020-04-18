<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Sortable
 *
 * @package App\Traits
 *
 * @property int $sort
 */
trait Sortable
{
    /**
     * Sort records in the ascending order by 'sort' field.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy('sort');
    }
}
