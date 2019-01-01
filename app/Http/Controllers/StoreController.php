<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Store model.
     *
     * @var Store
     */
    protected $store;

    /**
     * Create a new controller instance.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $roots = $this->store
            ->whereNull('parent_id')
            ->get();

        $trees = [];
        foreach ($roots as $root) {
            $trees[] = $root->getTree('branches');
        }

        return $this->success($trees);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function get(Request $request, string $id): JsonResponse
    {
        $store = $this->store->findOrFail($id);

        $branches = $request->input('branches', FALSE);

        return $this->success($branches ? $store->getTree('branches') : $store);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|string|unique:stores,name',
            'parent_id' => 'nullable|integer',
        ]);

        $parent = $request->has('parent_id')
            ? $this->store->findOrFail($request->input('parent_id'))
            : NULL;

        $store = $this->store->newInstance([
            'name' => $request->input('name'),
        ]);

        $store = isset($parent) ? $store->appendToNode($parent) : $store->createAsRoot();

        return $this->success($store);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        $store = $this->store->findOrFail($id);
        $store->deleteNode();

        return $this->success(['message' => 'Deleted Successfully']);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     *
     * This will suppress StaticAccess warnings in this method
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $store = $this->store->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($request->has('parent_id') && $request->input('parent_id') != $store->parent_id) {
                $parentStore = $this->store->findOrFail($request->input('parent_id'));
                $store->moveToNode($parentStore);
            }

            if ($request->has('name')) {
                $store->name = $request->input('name');
                $store->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $this->success($store);
    }
}
