<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Products Model
 *
 * @property \App\Model\Table\CollectionsTable&\Cake\ORM\Association\BelongsTo $Collections
 * @property \App\Model\Table\ColoursTable&\Cake\ORM\Association\BelongsTo $Colours
 * @property \App\Model\Table\MaterialsProductsTable&\Cake\ORM\Association\HasMany $MaterialsProducts
 * @property \App\Model\Table\ProductInventoriesTable&\Cake\ORM\Association\HasMany $ProductInventories
 *
 * @method \App\Model\Entity\Product newEmptyEntity()
 * @method \App\Model\Entity\Product newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Product[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Product get($primaryKey, $options = [])
 * @method \App\Model\Entity\Product findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Product patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Product[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Product|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Product saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProductsTable extends Table
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

        $this->setTable('products');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Collections', [
            'foreignKey' => 'collection_id',
        ]);
        $this->belongsTo('Colours', [
            'foreignKey' => 'colour_id',
        ]);
        $this->hasMany('MaterialsProducts', [
            'foreignKey' => 'product_id',
        ]);
        $this->hasMany('ProductInventories', [
            'foreignKey' => 'product_id',
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
            ->maxLength('name', 50, 'The name cannot exceed 50 characters.')
            ->requirePresence('name', 'create', 'Name is required when creating a record.')
            ->notEmptyString('name', 'The name cannot be empty.');

        $validator
            ->scalar('description', 'The description must be a scalar value.')
            ->maxLength('description', 255, 'The description cannot exceed 255 characters.')
            ->allowEmptyString('description', 'Description can be left empty.');

        $validator
            ->scalar('photo', 'The photo name must be a scalar value.')
            ->maxLength('photo', 200, 'The photo name cannot exceed 200 characters.')
            ->allowEmptyFile('photo', 'A photo file is optional.');

        $validator
            ->integer('collection_id', 'The collection ID must be an integer.')
            ->allowEmptyString('collection_id', 'Collection ID can be left empty.');

        $validator
            ->integer('colour_id', 'The colour ID must be an integer.')
            ->notEmptyString('colour_id', 'Please select product colour.');

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
        $rules->add($rules->existsIn('collection_id', 'Collections'), ['errorField' => 'collection_id']);
        $rules->add($rules->existsIn('colour_id', 'Colours'), ['errorField' => 'colour_id']);

        return $rules;
    }
}
