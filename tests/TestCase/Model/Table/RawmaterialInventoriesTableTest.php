<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RawmaterialInventoriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RawmaterialInventoriesTable Test Case
 */
class RawmaterialInventoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RawmaterialInventoriesTable
     */
    protected $RawmaterialInventories;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.RawmaterialInventories',
        'app.Rawmaterials',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('RawmaterialInventories') ? [] : ['className' => RawmaterialInventoriesTable::class];
        $this->RawmaterialInventories = $this->getTableLocator()->get('RawmaterialInventories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->RawmaterialInventories);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialInventoriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialInventoriesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
