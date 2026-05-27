<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RawmaterialCostTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RawmaterialCostTable Test Case
 */
class RawmaterialCostTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RawmaterialCostTable
     */
    protected $RawmaterialCost;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.RawmaterialCost',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('RawmaterialCost') ? [] : ['className' => RawmaterialCostTable::class];
        $this->RawmaterialCost = $this->getTableLocator()->get('RawmaterialCost', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->RawmaterialCost);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialCostTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
