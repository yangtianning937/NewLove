<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RawmaterialsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RawmaterialsTable Test Case
 */
class RawmaterialsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RawmaterialsTable
     */
    protected $Rawmaterials;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Rawmaterials',
        'app.Suppliers',
        'app.Colours',
        'app.MaterialsProducts',
        'app.RawmaterialInventories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Rawmaterials') ? [] : ['className' => RawmaterialsTable::class];
        $this->Rawmaterials = $this->getTableLocator()->get('Rawmaterials', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Rawmaterials);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
