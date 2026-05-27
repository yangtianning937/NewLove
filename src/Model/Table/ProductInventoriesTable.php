<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class ProductInventoriesTable extends Table
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

        $this->setTable('product_inventories');
        $this->setDisplayField('product_id');
        $this->setPrimaryKey('product_id');

        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
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
            ->notEmptyString('product_id', 'Please select a product.');

        $validator
            ->integer('quantity')
            ->requirePresence('quantity', 'create')
            ->notEmptyString('quantity','please enter the quantity')
            ->add('quantity', 'custom', [
                'rule' => function ($value) {
                    return $value >= 0 && $value <= 99999;
                },
                'message' => 'Quantity must be between 0 and 99,999'
            ]);

        return $validator;
    }

    /**
     * Callback method that gets triggered after a save operation is executed.
     *
     * @param Event $event The afterSave event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity that was saved.
     * @param ArrayObject $options Array of options.
     */
    public function afterSave(Event $event, $entity, $options)
    {
        $quantityChanged = $entity->quantity - ($entity->getOriginal('quantity') ?? 0);
        $this->updateRawMaterialInventory($entity->product_id, $quantityChanged);
    }

    public function updateRawMaterialInventory(int $productId, int $quantityChange): ?string
    {

        $materialProducts = TableRegistry::getTableLocator()->get('MaterialsProducts');
        $rawMaterialInventories = TableRegistry::getTableLocator()->get('RawMaterialInventories');

        $materialsNeeded = $materialProducts->find('all')->contain(['Rawmaterials'])->where(['product_id' => $productId]);

        foreach ($materialsNeeded as $rawmaterial) {
            $rawMaterialInventory = $rawMaterialInventories->findByRawmaterialId($rawmaterial->rawmaterial_id)->first();

            if ($rawMaterialInventory) {
                $totalNeeded = $rawmaterial->quantity * $quantityChange;
                if ($rawMaterialInventory->quantity < $totalNeeded) {
                    return "Not enough inventory for raw material named '{$rawmaterial->rawmaterial->name}'. Please add more rawmaterials.";
                }
                $rawMaterialInventory->quantity -= $totalNeeded;
                $rawMaterialInventories->save($rawMaterialInventory);
            }
        }

        return null; // No errors occurred
    }


}
