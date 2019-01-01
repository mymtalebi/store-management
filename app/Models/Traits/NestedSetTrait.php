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

use App\Exceptions\InvalidOperationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait NestedSetTrait
{
    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->lft + 1 === $this->right;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function root(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * Returns direct children of a node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'stores_parent_id_foreign');
    }

    /**
     *  Returns all descendents of a node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descendents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'stores_root_id_foreign');
    }

    /**
     * Appends this node to the given node.
     *
     * @param  self
     *
     * @return self
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function appendToNode(self $node): self
    {
        $rgt = $node->rgt;

        try {
            DB::beginTransaction();

            $this->where('rgt', '>', $rgt)
                ->update([
                    'rgt' => DB::raw('rgt + 2'),
                ]);

            $this->where('lft', '>', $rgt)
                ->update([
                    'lft' => DB::raw('lft + 2'),
                ]);

            $node->fill([
                'rgt' => DB::raw('rgt + 2'),
            ])->save();

            $this->fill([
                'lft' => $rgt,
                'rgt' => $rgt + 1,
                'parent_id' => $node->id,
                'root_id' => $node->root_id ?: $node->id,
            ])->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $this;
    }

    /**
     * Creates this node as root.
     *
     * @return self
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createAsRoot(): self
    {
        try {
            DB::beginTransaction();
            $this->fill([
                'parent_id' => NULL,
                'lft' => 1,
                'rgt' => 2,
            ]);

            $this->save();
            $this->root_id = $this->id;
            $this->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $this;
    }

    /**
     * @return bool
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function deleteNode(): bool
    {
        $lft = $this->lft;
        $rgt = $this->rgt;
        $width = $rgt - $lft + 1;

        try {
            DB::beginTransaction();

            // delete decendents
            $this->whereBetween('lft', [$lft, $rgt])->delete();

            // update subsequent nodes
            $this->where('rgt', '>', $rgt)->update([
                'rgt' => DB::raw('rgt - '.$width),
            ]);

            $this->where('lft', '>', $rgt)->update([
                'lft' => DB::raw('lft - '.$width),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return TRUE;
    }

    /**
     * @param self $node
     * @param int  $width
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createSpace(self $node, int $width)
    {
        try {
            DB::beginTransaction();

            $this->where('root_id', '=', $node->root_id)
                ->where('lft', '>=', $node->rgt)
                ->update([
                    'lft' => DB::raw('lft + '.$width),
                ]);

            $this->where('root_id', '=', $node->root_id)
                ->where('rgt', '>=', $node->rgt)
                ->update([
                    'rgt' => DB::raw('rgt + '.$width),
                ]);

            $this->refresh();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * Move this node with all of its descendents into the new space.
     *
     * @param self $node
     * @param int  $position
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function moveToSpace(self $node, int $position)
    {
        try {
            DB::beginTransaction();

            $width = $this->rgt - $this->lft + 1;
            $distance = $position - $this->lft;

            $this->where('root_id', '=', $this->root_id)
                ->where('lft', '>=', $this->lft)
                ->where('rgt', '<', $this->lft + $width)
                ->update([
                    'lft' => DB::raw('lft + '.$distance),
                    'rgt' => DB::raw('rgt + '.$distance),
                    'root_id' => $node->root_id ?: $node->id,
                ]);

            $this->parent_id = $node->id;
            $this->save();

            $this->refresh();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * Removes old space vacated by subtree.
     *
     * @param int $position
     * @param int $width
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function removeSpace(int $position, int $width)
    {
        try {
            DB::beginTransaction();
            $this->where('root_id', '=', $this->root_id)
                ->where('lft', '>', $position)
                ->update([
                    'lft' => DB::raw('lft - '.$width),
                ]);
            $this->where('root_id', '=', $this->root_id)
                ->where('rgt', '>=', $position + $width - 1)
                ->update([
                    'rgt' => DB::raw('rgt - '.$width),
                ]);

            $this->refresh();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * Moves this node with all its descendents to the given node.
     *
     * @param self $node
     *
     * @return self
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function moveToNode(self $node): self
    {
        if ($this->root_id === $node->root_id
            && $node->lft > $this->lft
            && $node->rgt < $this->rgt
        ) {
            throw new InvalidOperationException('Destination cannot be descendent of the source');
        }

        if ($this->parent_id === $node->id) {
            return $this;
        }

        try {
            DB::beginTransaction();

            $width = $this->rgt - $this->lft + 1;
            $parent = $this->parent()->getRelated();
            // Node will be appended to the destination node.
            $position = $node->rgt;

            $this->createSpace($node, $width);
            // Node might have moved during space creation, so get new position.
            $tmpPosition = $this->lft;

            $this->moveToSpace($node, $position);

            $parent->removeSpace($tmpPosition, $width);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $this;
    }

    /**
     * Returns tree structure of this node with all of its descendents.
     *
     * @param string $childrenKeyName Key name for the children array
     *
     * @return array the tree of the node and its children
     */
    public function getTree($childrenKeyName = 'children'): array
    {
        $selfAndDescendents = $this->where('lft', '>=', $this->lft)
            ->where('rgt', '<=', $this->rgt)
            ->where('root_id', '=', $this->root_id)
            ->orderBy('lft')
            ->get();

        return current($this->createTree($selfAndDescendents, $this->lft, $this->rgt, $childrenKeyName));
    }

    /**
     * Returns given nodes tree structure.
     *
     * @param Collection $nodes
     * @param int        $left
     * @param int        $right
     * @param string     $childrenKeyName Key name for the children array
     *
     * @return array the tree of the nodes
     */
    private function createTree(
        Collection $nodes,
        int $left,
        int $right,
        string $childrenKeyName = 'children'
    ): array {
        $tree = [];
        foreach ($nodes as $key => $node) {
            if ($node->lft >= $left && $node->rgt <= $right) {
                // Must unset to avoid traversing same nodes and fall into loop
                unset($nodes[$key]);

                $nodeArr = $node->toArray();
                $nodeArr[$childrenKeyName] = $this->createTree(
                    $nodes,
                    $node->lft,
                    $node->rgt,
                    $childrenKeyName
                );
                $tree[] = $nodeArr;

                $left = $node->rgt;
            }
        }

        return $tree;
    }
}
