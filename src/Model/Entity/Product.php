<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Product Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $desc
 * @property string|null $image
 * @property int|null $collection_id
 * @property int|null $colour_id
 *
 * @property \App\Model\Entity\Collection $collection
 * @property \App\Model\Entity\Colour $colour
 * @property \App\Model\Entity\MaterialsProduct[] $materials_products
 * @property \App\Model\Entity\ProductInventory[] $product_inventories
 */
class Product extends Entity
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
        'description' => true,
        'photo' => true,
        'collection_id' => true,
        'colour_id' => true,
        'collection' => true,
        'colour' => true,
        'materials_products' => true,
        'product_inventories' => true,
    ];
}
