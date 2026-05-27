<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductFixture
 */
class ProductFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'product';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'productID' => 1,
                'productName' => 'Lorem ipsum dolor sit amet',
                'productDesc' => 'Lorem ipsum dolor sit amet',
                'productImage' => 'Lorem ipsum dolor sit amet',
                'collectionID' => 1,
                'colourID' => 1,
            ],
        ];
        parent::init();
    }
}
