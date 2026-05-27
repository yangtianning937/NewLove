<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ColoursTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ColoursTable Test Case
 */
class ColoursTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ColoursTable
     */
    protected $Colours;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Colours',
        'app.Products',
        'app.Rawmaterials',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Colours') ? [] : ['className' => ColoursTable::class];
        $this->Colours = $this->getTableLocator()->get('Colours', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Colours);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ColoursTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
