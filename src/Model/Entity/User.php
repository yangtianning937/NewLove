<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string|null $avatar
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string|null $nonce
 * @property \Cake\I18n\FrozenTime $nonce_expiry
 *
 * // Virtual Fields
 * @property string $user_full_display
 * @property string $full_name
 *
 * @property \App\Model\Entity\BlogArticle[] $blog_articles
 */
class User extends Entity {
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
        'email' => true,
        'password' => true,
        'first_name' => true,
        'last_name' => true,
        'avatar' => true,
        'created' => false,
        'modified' => false,
        'nonce' => false,  // Nonce and expiry dates are to be set in Controller directly, not through patching
        'nonce_expiry' => false,
        'blog_articles' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Generate display field for User entity
     * @return string Display field
     */
    protected function _getUserFullDisplay(): string {
        return $this->first_name . ' ' . $this->last_name . ' (' . $this->email . ')';
    }

    /**
     * Generate Full Name of a user
     * @return string Full Name
     */
    protected function _getFullName(): string {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Hashing password for User entity
     * @param string $password Password field
     * @return string|null hashed password
     */
    protected function _setPassword(string $password): ?string {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
        return $password;
    }
}

