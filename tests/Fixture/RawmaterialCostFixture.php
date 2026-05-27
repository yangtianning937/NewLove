<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RawmaterialCostFixture
 */
class RawmaterialCostFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'rawmaterial_cost';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'costID' => 1,
                'rawmaterialID' => 1,
                'rawmaterialCostPrice' => 1.5,
            ],
        ];
        parent::init();
    }
}
