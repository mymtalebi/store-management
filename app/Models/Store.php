<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use Traits\NestedSetTrait;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'lft',
        'rgt',
        'parent_id',
        'root_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'lft', 'rgt', 'parent_id', 'root_id'];

    /**
     * Add a basic where clause to the query.
     * Note: this is just to make store class mockable and tesable.
     *
     * @param string|array|\Closure $column
     * @param mixed                 $operator
     * @param mixed                 $value
     * @param string                $boolean
     *
     * @return $this
     */
    public function where($column, $operator = NULL, $value = NULL, $boolean = 'and')
    {
        return parent::where($column, $operator, $value, $boolean);
    }

    /**
     * Add a where between statement to the query.
     * Note: this is just to make store class mockable and tesable.
     *
     * @param string $column
     * @param array  $values
     * @param string $boolean
     * @param bool   $not
     *
     * @return $this
     *
     * This will suppress BooleanArgumentFlag warnings in this method
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = FALSE)
    {
        return parent::whereBetween($column, $values, $boolean, $not);
    }
}
