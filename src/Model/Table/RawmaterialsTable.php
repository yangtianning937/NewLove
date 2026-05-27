<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Rawmaterials Model
 *
 * @property \App\Model\Table\SuppliersTable&\Cake\ORM\Association\BelongsTo $Suppliers
 * @property \App\Model\Table\ColoursTable&\Cake\ORM\Association\BelongsTo $Colours
 * @property \App\Model\Table\MaterialsProductsTable&\Cake\ORM\Association\HasMany $MaterialsProducts
 * @property \App\Model\Table\RawmaterialInventoriesTable&\Cake\ORM\Association\HasMany $RawmaterialInventories
 *
 * @method \App\Model\Entity\Rawmaterial newEmptyEntity()
 * @method \App\Model\Entity\Rawmaterial newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Rawmaterial[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rawmaterial get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rawmaterial findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Rawmaterial patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rawmaterial[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rawmaterial|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rawmaterial saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rawmaterial[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rawmaterial[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rawmaterial[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Rawmaterial[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class RawmaterialsTable extends Table
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

        $this->setTable('rawmaterials');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
        ]);
        $this->belongsTo('Colours', [
            'foreignKey' => 'colour_id',
        ]);
        $this->hasMany('MaterialsProducts', [
            'foreignKey' => 'rawmaterial_id',
        ]);
        $this->hasOne('RawmaterialInventories', [
            'foreignKey' => 'rawmaterial_id',
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
            ->maxLength('name', 100, 'The name cannot exceed 100 characters.')
            ->requirePresence('name', 'create', 'Name is required when creating a record.')
            ->notEmptyString('name', 'The name cannot be empty.');

        $validator
            ->scalar('delivery_time', 'The delivery time must be a scalar value.')
            ->maxLength('delivery_time', 100, 'The delivery time cannot exceed 100 characters.')
            ->requirePresence('delivery_time', 'create', 'Delivery time is required when creating a record.')
            ->notEmptyString('delivery_time', 'The delivery time cannot be empty.');

        $validator
            ->scalar('description', 'The description must be a scalar value.')
            ->maxLength('description', 255, 'The description cannot exceed 255 characters.')
            ->requirePresence('description', 'create', 'Description is required when creating a record.')
            ->notEmptyString('description', 'The description cannot be empty.');

        $validator
            ->decimal('cost_price')
            ->requirePresence('cost_price', 'create', 'Cost price is required when creating a record.')
            ->notEmptyString('cost_price', 'The cost price cannot be empty.')
            ->add('cost_price', 'validRange', [
                'rule' => ['range', 0, 9999.99],
                'message' => 'Material Price must be between 0 and 9,999.99'
            ]);

        $validator
            ->integer('supplier_id', 'The supplier ID must be an integer.')
            ->allowEmptyString('supplier_id', 'Supplier ID can be left empty.');

        $validator
            ->scalar('photo', 'The photo name must be a scalar value.')
            ->maxLength('photo', 200, 'The photo name cannot exceed 200 characters.')
            ->allowEmptyFile('photo', 'A photo file is optional.');

        $validator
            ->integer('colour_id', 'The colour ID must be an integer.')
            ->allowEmptyString('colour_id', 'Colour ID can be left empty.');

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
        $rules->add($rules->existsIn('supplier_id', 'Suppliers'), ['errorField' => 'supplier_id']);
        $rules->add($rules->existsIn('colour_id', 'Colours'), ['errorField' => 'colour_id']);

        return $rules;
    }

    public function findLowStockRawMaterials()
    {
        return $this->find()
            ->where(['stock <' => 'your_low_stock_threshold'])
            ->all();
    }
}
