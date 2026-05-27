<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RawmaterialInventoriesFixture
 */
class RawmaterialInventoriesFixture extends TestFixture
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
                'rawmaterial_id' => 1,
                'quantity' => 1,
            ],
        ];
        parent::init();
    }
}
