<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SupplierTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SupplierTable Test Case
 */
class SupplierTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SupplierTable
     */
    protected $Supplier;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Supplier',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Supplier') ? [] : ['className' => SupplierTable::class];
        $this->Supplier = $this->getTableLocator()->get('Supplier', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Supplier);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SupplierTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
