<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductInventoryTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductInventoryTable Test Case
 */
class ProductInventoryTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductInventoryTable
     */
    protected $ProductInventory;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ProductInventory',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ProductInventory') ? [] : ['className' => ProductInventoryTable::class];
        $this->ProductInventory = $this->getTableLocator()->get('ProductInventory', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ProductInventory);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductInventoryTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
