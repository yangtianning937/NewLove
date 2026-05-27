<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Colour Entity
 *
 * @property int $id
 * @property string $name
 *
 * @property \App\Model\Entity\Product[] $products
 * @property \App\Model\Entity\Rawmaterial[] $rawmaterials
 */
class Colour extends Entity
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
        'products' => true,
        'rawmaterials' => true,
    ];
}
