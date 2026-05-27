<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ComponentsBridgeFixture
 */
class ComponentsBridgeFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'components_bridge';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'componentsID' => 1,
                'quantity' => 1,
                'productID' => 1,
                'rawmaterialID' => 1,
            ],
        ];
        parent::init();
    }
}
