<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MaterialsProductsFixture
 */
class MaterialsProductsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'quantity' => 1,
                'product_id' => 1,
                'rawmaterial_id' => 1,
            ],
        ];
        parent::init();
    }
}
