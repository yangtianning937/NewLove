<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Suppliers Model
 *
 * @property \App\Model\Table\RawmaterialsTable&\Cake\ORM\Association\HasMany $Rawmaterials
 *
 * @method \App\Model\Entity\Supplier newEmptyEntity()
 * @method \App\Model\Entity\Supplier newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Supplier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Supplier get($primaryKey, $options = [])
 * @method \App\Model\Entity\Supplier findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Supplier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Supplier[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Supplier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supplier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SuppliersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('suppliers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Rawmaterials', [
            'foreignKey' => 'supplier_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name', 'The name must be a scalar value.')
            ->maxLength('name', 56, 'The name cannot exceed 56 characters.')
            ->requirePresence('name', 'create', 'Name is required when creating a record.')
            ->notEmptyString('name', 'The name cannot be empty.');

        $validator
            ->email('email')
            ->allowEmptyString('email', 'Email can be left empty.');

        $validator
            ->numeric('phone_no', 'Phone number should be numeric.')
            ->maxLength('phone_no', 20, 'Phone number cannot exceed 20 digits.')
            ->minLength('phone_no', 6, 'Phone number should be at least 6 digits long.')
            ->allowEmptyString('phone_no', 'Phone number can be left empty.');

        $validator
            ->scalar('website', 'The website must be a scalar value.')
            ->maxLength('website', 255, 'The website cannot exceed 255 characters.')
            ->allowEmptyString('website', 'Website can be left empty.')
            ->add('website', 'custom', [
                'rule' => function ($value, $context) {
                    // Simplified website regex pattern
                    $pattern = '/^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w .-]*)*\/?$/';
                    return (bool)preg_match($pattern, $value);
                },
                'message' => "Please enter a valid website",
            ]);

        $validator
            ->scalar('location', 'The location must be a scalar value.')
            ->maxLength('location', 255, 'The location cannot exceed 255 characters.')
            ->allowEmptyString('location', 'Location can be left empty.');

        return $validator;
    }
}
