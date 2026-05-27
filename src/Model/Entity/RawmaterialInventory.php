<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RawmaterialInventory Entity
 *
 * @property int $id
 * @property int $rawmaterial_id
 * @property int $quantity
 *
 * @property \App\Model\Entity\Rawmaterial $rawmaterial
 */
class RawmaterialInventory extends Entity
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
        'rawmaterial_id' => true,
        'quantity' => true,
        'rawmaterial' => true,
        'lowStockLimit' => true
    ];
}
