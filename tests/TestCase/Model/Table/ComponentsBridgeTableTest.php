<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ComponentsBridgeTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ComponentsBridgeTable Test Case
 */
class ComponentsBridgeTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ComponentsBridgeTable
     */
    protected $ComponentsBridge;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ComponentsBridge',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ComponentsBridge') ? [] : ['className' => ComponentsBridgeTable::class];
        $this->ComponentsBridge = $this->getTableLocator()->get('ComponentsBridge', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ComponentsBridge);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ComponentsBridgeTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
