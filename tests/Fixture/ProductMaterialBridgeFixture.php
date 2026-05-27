<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductMaterialBridgeFixture
 */
class ProductMaterialBridgeFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'product_material_bridge';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'productmaterialID' => 1,
                'quantity' => 1,
                'productID' => 1,
                'rawmaterialID' => 1,
            ],
        ];
        parent::init();
    }
}
