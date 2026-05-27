<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductMaterialBridgeTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductMaterialBridgeTable Test Case
 */
class ProductMaterialBridgeTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductMaterialBridgeTable
     */
    protected $ProductMaterialBridge;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ProductMaterialBridge',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ProductMaterialBridge') ? [] : ['className' => ProductMaterialBridgeTable::class];
        $this->ProductMaterialBridge = $this->getTableLocator()->get('ProductMaterialBridge', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ProductMaterialBridge);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductMaterialBridgeTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
