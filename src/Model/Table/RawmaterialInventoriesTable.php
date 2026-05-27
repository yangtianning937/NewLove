<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RawmaterialInventories Model
 *
 * @property \App\Model\Table\RawmaterialsTable&\Cake\ORM\Association\BelongsTo $Rawmaterials
 *
 * @method \App\Model\Entity\RawmaterialInventory newEmptyEntity()
 * @method \App\Model\Entity\RawmaterialInventory newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RawmaterialInventory get($primaryKey, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RawmaterialInventory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\RawmaterialInventory[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RawmaterialInventoriesTable extends Table
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

        $this->setTable('rawmaterial_inventories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Rawmaterials', [
            'foreignKey' => 'rawmaterial_id',
            'joinType' => 'INNER',
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
            ->integer('rawmaterial_id')
            ->notEmptyString('rawmaterial_id','Please select a rawmaterial.');

        $validator
            ->integer('quantity', 'Quantity must be an integer.')
            ->requirePresence('quantity', 'create', 'Quantity is required when creating a record.')
            ->notEmptyString('quantity', 'Quantity cannot be empty.')

            // Using the custom rule (as you provided)
            ->add('quantity', 'custom', [
                'rule' => function ($value) {
                    return $value >= 0 && $value <= 99999;
                },
                'message' => 'Quantity must be between 0 and 99,999'
            ]);

        $validator
            ->integer('lowStockLimit')
            ->requirePresence('lowStockLimit', 'create')
            ->notEmptyString('lowStockLimit')
            ->add('lowStockLimit', 'custom', [
                'rule' => function ($value) {
                    return $value >= 0 && $value <= 99999;
                },
                'message' => 'Low Stock Threshold must be between 0 and 99,999'
            ]);    

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('rawmaterial_id', 'Rawmaterials'), ['errorField' => 'rawmaterial_id']);

        return $rules;
    }
}
