<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductCollectionFixture
 */
class ProductCollectionFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'product_collection';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'collectionID' => 1,
                'collectionName' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
