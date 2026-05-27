<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SupplierFixture
 */
class SupplierFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'supplier';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'supplierName' => 'Lorem ipsum dolor sit amet',
                'supplierID' => 1,
                'supplierEmail' => 'Lorem ipsum dolor sit amet',
                'supplierPhoneNo' => 'Lorem ipsum dolor sit amet',
                'supplierWebsite' => 'Lorem ipsum dolor sit amet',
                'supplierLocation' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
