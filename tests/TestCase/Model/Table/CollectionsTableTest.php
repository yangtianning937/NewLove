<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CollectionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CollectionsTable Test Case
 */
class CollectionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CollectionsTable
     */
    protected $Collections;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Collections',
        'app.Products',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Collections') ? [] : ['className' => CollectionsTable::class];
        $this->Collections = $this->getTableLocator()->get('Collections', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Collections);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CollectionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
