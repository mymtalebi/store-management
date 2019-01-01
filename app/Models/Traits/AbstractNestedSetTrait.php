<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace App\Models\Traits;

trait AbstractNestedSetTrait
{
    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     *
     * @return $this
     */
    abstract public function fill(array $attributes);

    /**
     * Save the model to the database.
     *
     * @param array $options
     *
     * @return bool
     */
    abstract public function save(array $options = []);

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $ownerKey
     * @param string $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    abstract public function belongsTo($related, $foreignKey = NULL, $ownerKey = NULL, $relation = NULL);

    /**
     * Define a one-to-many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function hasMany($related, $foreignKey = NULL, $localKey = NULL);

    /**
     * Add a basic where clause to the query.
     *
     * @param string|array|\Closure $column
     * @param mixed                 $operator
     * @param mixed                 $value
     * @param string                $boolean
     *
     * @return $this
     */
    abstract public function where($column, $operator = NULL, $value = NULL, $boolean = 'and');

    /**
     * Add a where between statement to the query.
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
    abstract public function whereBetween($column, array $values, $boolean = 'and', $not = FALSE);

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    abstract public function refresh();
}
