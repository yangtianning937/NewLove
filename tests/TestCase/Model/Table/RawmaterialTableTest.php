<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RawmaterialTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RawmaterialTable Test Case
 */
class RawmaterialTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RawmaterialTable
     */
    protected $Rawmaterial;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Rawmaterial',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Rawmaterial') ? [] : ['className' => RawmaterialTable::class];
        $this->Rawmaterial = $this->getTableLocator()->get('Rawmaterial', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Rawmaterial);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RawmaterialTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
