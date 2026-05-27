<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RawmaterialFixture
 */
class RawmaterialFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'rawmaterial';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'rawmaterialID' => 1,
                'rawmaterialName' => 'Lorem ipsum dolor sit amet',
                'rawmaterialDeliveryTime' => 'Lorem ipsum dolor sit amet',
                'rawmaterialDesc' => 'Lorem ipsum dolor sit amet',
                'supplierID' => 1,
                'rawmaterialImage' => 'Lorem ipsum dolor sit amet',
                'colourID' => 1,
            ],
        ];
        parent::init();
    }
}
