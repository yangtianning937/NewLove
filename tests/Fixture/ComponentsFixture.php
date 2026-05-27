<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ComponentsFixture
 */
class ComponentsFixture extends TestFixture
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
                'compQuantity' => 1,
                'pID' => 1,
                'rmID' => 1,
            ],
        ];
        parent::init();
    }
}
