<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductColourTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductColourTable Test Case
 */
class ProductColourTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductColourTable
     */
    protected $ProductColour;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ProductColour',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ProductColour') ? [] : ['className' => ProductColourTable::class];
        $this->ProductColour = $this->getTableLocator()->get('ProductColour', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ProductColour);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProductColourTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
