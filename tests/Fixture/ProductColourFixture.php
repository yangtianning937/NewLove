<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductColourFixture
 */
class ProductColourFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'product_colour';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'colourID' => 1,
                'colourName' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
