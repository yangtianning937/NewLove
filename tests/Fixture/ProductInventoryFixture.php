<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductInventoryFixture
 */
class ProductInventoryFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'product_inventory';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'inventoryID' => 1,
                'productID' => 1,
                'productquantity' => 1,
            ],
        ];
        parent::init();
    }
}
