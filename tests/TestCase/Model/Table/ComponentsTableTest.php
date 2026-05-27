<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ComponentsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ComponentsTable Test Case
 */
class ComponentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ComponentsTable
     */
    protected $Components;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Components',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Components') ? [] : ['className' => ComponentsTable::class];
        $this->Components = $this->getTableLocator()->get('Components', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Components);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ComponentsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
