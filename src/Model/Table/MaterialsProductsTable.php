<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MaterialsProducts Model
 *
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\BelongsTo $Products
 * @property \App\Model\Table\RawmaterialsTable&\Cake\ORM\Association\BelongsTo $Rawmaterials
 *
 * @method \App\Model\Entity\MaterialsProduct newEmptyEntity()
 * @method \App\Model\Entity\MaterialsProduct newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\MaterialsProduct[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MaterialsProduct get($primaryKey, $options = [])
 * @method \App\Model\Entity\MaterialsProduct findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\MaterialsProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MaterialsProduct[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MaterialsProduct|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MaterialsProduct saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MaterialsProduct[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MaterialsProduct[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\MaterialsProduct[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MaterialsProduct[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class MaterialsProductsTable extends Table
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

        $this->setTable('materials_products');
        $this->setDisplayField(['product_id', 'rawmaterial_id']);
        $this->setPrimaryKey(['product_id', 'rawmaterial_id']);

        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('quantity', 'Quantity must be an integer.')
            ->requirePresence('quantity', 'create')
            ->notEmptyString('quantity', 'Quantity cannot be empty.')
            ->greaterThanOrEqual('quantity', 0, 'Quantity must be zero or a positive number.');

        $validator
            ->notEmptyString('rawmaterial_id', 'Please select a raw material.');

        $validator
            ->notEmptyString('product_id', 'Please select a product.');




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
        $rules->add($rules->existsIn('product_id', 'Products'), ['errorField' => 'product_id']);
        $rules->add($rules->existsIn('rawmaterial_id', 'Rawmaterials'), ['errorField' => 'rawmaterial_id']);

        return $rules;
    }
}
