<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rawmaterial Entity
 *
 * @property int $id
 * @property string $name
 * @property string $delivery_time
 * @property string $desc
 * @property string $cost_price
 * @property int|null $supplier_id
 * @property string|null $image
 * @property int|null $colour_id
 *
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\Colour $colour
 * @property \App\Model\Entity\MaterialsProduct[] $materials_products
 * @property \App\Model\Entity\RawmaterialInventory[] $rawmaterial_inventories
 */
class Rawmaterial extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'name' => true,
        'delivery_time' => true,
        'description' => true,
        'cost_price' => true,
        'supplier_id' => true,
        'photo' => true,
        'colour_id' => true,
        'supplier' => true,
        'colour' => true,
        'materials_products' => true,
        'rawmaterial_inventories' => true,
    ];
}
