<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductCollectionTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductCollectionTable Test Case
 */
class ProductCollectionTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductCollectionTable
     */
    protected $ProductCollection;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ProductCollection',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ProductCollection') ? [] : ['className' => ProductCollectionTable::class];
        $this->ProductCollection = $this->getTableLocator()->get('ProductCollection', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ProductCollection);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductCollectionTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
