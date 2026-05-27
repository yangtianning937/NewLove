<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RawmaterialColourTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RawmaterialColourTable Test Case
 */
class RawmaterialColourTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RawmaterialColourTable
     */
    protected $RawmaterialColour;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.RawmaterialColour',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('RawmaterialColour') ? [] : ['className' => RawmaterialColourTable::class];
        $this->RawmaterialColour = $this->getTableLocator()->get('RawmaterialColour', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->RawmaterialColour);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialColourTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
