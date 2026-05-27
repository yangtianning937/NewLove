<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RawmaterialsFixture
 */
class RawmaterialsFixture extends TestFixture
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
                'id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'delivery_time' => 'Lorem ipsum dolor sit amet',
                'desc' => 'Lorem ipsum dolor sit amet',
                'cost_price' => 1.5,
                'supplier_id' => 1,
                'image' => 'Lorem ipsum dolor sit amet',
                'colour_id' => 1,
            ],
        ];
        parent::init();
    }
}
