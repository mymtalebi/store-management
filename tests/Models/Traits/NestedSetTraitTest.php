<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace Tests\Models\Traits;

use App\Models\Traits\NestedSetTrait;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NestedSetTraitTest extends TestCase
{
    /**
     * @group UnitTests
     */
    public function testCreateAsRootSuccess()
    {
        $node = $this->getMockForTrait(NestedSetTrait::class);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $node->expects($this->once())
             ->method('fill')
             ->with([
                'parent_id' => NULL,
                'lft' => 1,
                'rgt' => 2,
            ]);

        $node->expects($this->exactly(2))
             ->method('save')
             ->will($this->returnCallback(function () use ($node) {
                 $node->id = 1;

                 return $node;
             }));

        $node->createAsRoot();

        $this->assertEquals(1, $node->id);
        $this->assertEquals(1, $node->root_id);
    }

    /**
     * @group UnitTests
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Failed for test.
     */
    public function testCreateAsRootFailure()
    {
        $node = $this->getMockForTrait(NestedSetTrait::class);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollback')->once();

        $node->expects($this->once())
             ->method('fill')
             ->will($this->throwException(new \Exception('Failed for test.')));

        $node->expects($this->never())
             ->method('save');

        $node->createAsRoot();
    }
}
