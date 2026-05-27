<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Supplier Entity
 *
 * @property string $name
 * @property int $id
 * @property string|null $email
 * @property string|null $phone_no
 * @property string|null $website
 * @property string|null $location
 *
 * @property \App\Model\Entity\Rawmaterial[] $rawmaterials
 */
class Supplier extends Entity
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
        'email' => true,
        'phone_no' => true,
        'website' => true,
        'location' => true,
        'rawmaterials' => true,
    ];
}
