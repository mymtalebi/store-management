<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace Tests\Http\Controllers;

use App\Models\Store;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * Creates a tree with a root and a branch and returns created models in an array.
     *
     * @param int $rootId
     * @param int $branchId
     *
     * @return \App\Models\Store[]
     */
    private function createTree($rootId = 1, $branchId = 2): array
    {
        $rootStore = factory(Store::class, 'root')->create([
            'id' => $rootId,
            'root_id' => $rootId,
            'rgt' => 4, ]);
        $store = factory(Store::class)->create([
            'id' => $branchId,
            'parent_id' => $rootId,
            'root_id' => $rootId,
        ]);

        $this->seeInDatabase('stores', [
            'id' => $rootStore->id,
        ]);
        $this->seeInDatabase('stores', [
            'id' => $store->id,
            'parent_id' => $rootStore->id,
        ]);

        return [$rootStore, $store];
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testGetAllSuccess(): void
    {
        $this->createTree(1, 2);
        $this->createTree(3, 4);

        $response = $this->json('GET', '/store/');

        $this->assertResponseOk();
        $response->shouldReturnJson();
        $response->seeJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'branches',
                ],
            ],
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testGetWithoutBranchSuccess(): void
    {
        $store = factory(Store::class, 'root')->create();

        $response = $this->json('GET', '/store/'.$store->id);

        $this->assertResponseOk();
        $response->shouldReturnJson();
        $response->seeJson([
            'data' => [
                'id' => $store->id,
                'name' => $store->name,
            ],
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testGetWithBranchesSuccess(): void
    {
        list($rootStore, $store) = $this->createTree();

        $response = $this->json('GET', '/store/'.$rootStore->id, ['branches' => 1]);

        $this->assertResponseOk();
        $response->shouldReturnJson();
        $response->seeJson([
            'data' => [
                'id' => $rootStore->id,
                'name' => $rootStore->name,
                'branches' => [
                    [
                       'id' => $store->id,
                       'name' => $store->name,
                       'branches' => [],
                    ],
                ],
            ],
        ])
        ->seeJsonStructure([
            'data' => [
                'id',
                'name',
                'branches',
            ],
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testGetFailure(): void
    {
        $response = $this->json('GET', '/store/1');

        $this->assertResponseStatus(404);
        $response->shouldReturnJson();
        $response->seeJsonStructure([
            'error',
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testCreateRoot(): void
    {
        $name = 'Main Branch';
        $postData = [
            'name' => $name,
        ];
        $response = $this->json('POST', '/store', $postData);

        $this->assertResponseStatus(200);
        $response->shouldReturnJson();
        $response->seeJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);

        $this->seeInDatabase('stores', [
            'name' => $name,
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testDeletWithBraches(): void
    {
        list($rootStore, $store) = $this->createTree();

        $response = $this->json('DELETE', '/store/'.$rootStore->id);

        $this->assertResponseStatus(200);
        $response->shouldReturnJson();
        $response->seeJsonStructure([
            'data' => [
                'message',
            ],
        ]);

        $this->notSeeInDatabase('stores', [
            'id' => $rootStore->id,
        ]);
        $this->notSeeInDatabase('stores', [
            'id' => $store->id,
        ]);
    }

    /**
     * @group IntegrationTests
     * @group API
     */
    public function testMoveBrach(): void
    {
        list($rootStore) = $this->createTree(1, 2);
        list($rootStore2, $store2) = $this->createTree(3, 4);

        $name = 'Moved Branch';
        $response = $this->json('PUT', '/store/'.$rootStore2->id, [
            'name' => $name,
            'parent_id' => $rootStore->id,
        ]);

        $this->assertResponseStatus(200);
        $response->shouldReturnJson();
        $response->seeJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);

        $this->seeInDatabase('stores', [
            'id' => $rootStore2->id,
            'name' => $name,
            'root_id' => $rootStore->id,
            'parent_id' => $rootStore->id,
            'lft' => 4,
            'rgt' => 7,
        ]);
        $this->seeInDatabase('stores', [
            'id' => $store2->id,
            'root_id' => $rootStore->id,
            'parent_id' => $rootStore2->id,
            'lft' => 5,
            'rgt' => 6,
        ]);
    }
}
