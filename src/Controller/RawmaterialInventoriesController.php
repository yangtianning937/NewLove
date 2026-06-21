<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\RawmaterialInventory;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * RawmaterialInventories Controller
 *
 * @property \App\Model\Table\RawmaterialInventoriesTable $RawmaterialInventories
 * @method \App\Model\Entity\RawmaterialInventory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RawmaterialInventoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->usesFirestore()) {
            $rawmaterialInventories = array_map(
                [$this, 'firestoreRawmaterialInventoryEntity'],
                (new NewLoveFirestoreRepository())->rawmaterialInventories()
            );
            $usingFirestore = true;

            $this->set(compact('rawmaterialInventories', 'usingFirestore'));

            return;
        }

        $this->paginate = [
            'contain' => ['Rawmaterials','Rawmaterials.Colours'],
        ];
        $rawmaterialInventories = $this->paginate($this->RawmaterialInventories);

        $this->set(compact('rawmaterialInventories'));
    }

    /**
     * View method
     *
     * @param string|null $id Rawmaterial Inventory id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if ($this->usesFirestore()) {
            $rawmaterialInventory = $this->firestoreRawmaterialInventoryOrFail((string)$id);
            $usingFirestore = true;

            $this->set(compact('rawmaterialInventory', 'usingFirestore'));

            return;
        }

        $rawmaterialInventory = $this->RawmaterialInventories->get($id, [
            'contain' => ['Rawmaterials'],
        ]);

        $this->set(compact('rawmaterialInventory'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->usesFirestore()) {
            return $this->addWithFirestore();
        }

        $rawmaterialInventory = $this->RawmaterialInventories->newEmptyEntity();
        $showConfirm = false;

        if ($this->request->is('post')) {
            $existingRawmaterial = $this->RawmaterialInventories->findByRawmaterialId($this->request->getData('rawmaterial_id'))->first();

            if ($existingRawmaterial && !$this->request->getData('confirmed')) {
                $showConfirm = true;
            } elseif ($existingRawmaterial && $this->request->getData('confirmed')) {
                $newQuantity = $this->request->getData('quantity');
                $existingRawmaterial->quantity += $newQuantity;

                if ($this->RawmaterialInventories->save($existingRawmaterial)) {
                    $this->Flash->success(__('The raw material inventory quantity has been updated.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The raw material inventory could not be updated. Please, try again.'));
                }
            } else {
                $rawmaterialInventory = $this->RawmaterialInventories->patchEntity($rawmaterialInventory, $this->request->getData());

                if ($this->RawmaterialInventories->save($rawmaterialInventory)) {
                    $this->Flash->success(__('The raw material inventory has been saved.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The raw material inventory could not be saved. Please, try again.'));
                }
            }
        }

        $this->loadModel('Rawmaterials');
        $rawmaterialsData = $this->Rawmaterials->find('all', [
            'contain' => ['Colours']
        ])->toList();

        $rawmaterials = [];
        foreach ($rawmaterialsData as $rawmaterial) {
            $rawmaterials[$rawmaterial->id] = $rawmaterial->name . ' - ' . $rawmaterial->colour->name;
        }

        $this->set(compact('rawmaterialInventory', 'rawmaterials', 'showConfirm'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rawmaterial Inventory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->usesFirestore()) {
            return $this->editWithFirestore((string)$id);
        }

        $rawmaterialInventory = $this->RawmaterialInventories->get($id, [
            'contain' => ['Rawmaterials.Colours'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rawmaterialInventory = $this->RawmaterialInventories->patchEntity($rawmaterialInventory, $this->request->getData());
            if ($this->RawmaterialInventories->save($rawmaterialInventory)) {
                $this->Flash->success(__('The rawmaterial inventory has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rawmaterial inventory could not be saved. Please, try again.'));
        }
        $rawmaterials = $this->RawmaterialInventories->Rawmaterials->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return $row['name'] . ' - ' . $row['colour']['name'];
            },
            'contain' => ['Colours'],
            'limit' => 200
        ])->all();
        $this->set(compact('rawmaterialInventory', 'rawmaterials'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rawmaterial Inventory id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteRawmaterialInventory((string)$id);
                $this->Flash->success(__('The rawmaterial inventory has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The rawmaterial inventory could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $rawmaterialInventory = $this->RawmaterialInventories->get($id);
        if ($this->RawmaterialInventories->delete($rawmaterialInventory)) {
            $this->Flash->success(__('The rawmaterial inventory has been deleted.'));
        } else {
            $this->Flash->error(__('The rawmaterial inventory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function addWithFirestore()
    {
        $repository = new NewLoveFirestoreRepository();
        $rawmaterialInventory = $this->firestoreRawmaterialInventoryEntity([
            'rawmaterial_id' => '',
            'quantity' => '',
            'lowStockLimit' => '',
        ], true);
        $rawmaterials = $repository->rawmaterialInventoryRawmaterialList();
        $showConfirm = false;
        $usingFirestore = true;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $rawmaterialInventory = $this->firestoreRawmaterialInventoryEntity($data, true);
            $errors = $this->validateFirestoreRawmaterialInventory($data, $rawmaterials, null, true);

            if ($errors === []) {
                $existingRawmaterial = $repository->rawmaterialInventoryByRawmaterialId((string)$data['rawmaterial_id']);

                try {
                    if ($existingRawmaterial !== null && !$this->request->getData('confirmed')) {
                        $showConfirm = true;
                    } elseif ($existingRawmaterial !== null) {
                        $repository->increaseRawmaterialInventory((string)$existingRawmaterial['id'], (int)$data['quantity']);
                        $this->Flash->success(__('The raw material inventory quantity has been updated.'));

                        return $this->redirect(['action' => 'index']);
                    } else {
                        $repository->createRawmaterialInventory($data);
                        $this->Flash->success(__('The raw material inventory has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    }
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__($exception->getMessage()));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('rawmaterialInventory', 'rawmaterials', 'showConfirm', 'usingFirestore'));
    }

    private function editWithFirestore(string $id)
    {
        $repository = new NewLoveFirestoreRepository();
        $inventoryData = $repository->rawmaterialInventory($id);

        if ($inventoryData === null) {
            throw new NotFoundException(__('Raw material inventory not found'));
        }

        $rawmaterialInventory = $this->firestoreRawmaterialInventoryEntity($inventoryData);
        $rawmaterials = $repository->rawmaterialInventoryRawmaterialList();
        $usingFirestore = true;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $rawmaterialInventory = $this->firestoreRawmaterialInventoryEntity(array_merge($inventoryData, $data));
            $errors = $this->validateFirestoreRawmaterialInventory($data, $rawmaterials, $id);

            if ($errors === []) {
                try {
                    $repository->updateRawmaterialInventory($id, $data);
                    $this->Flash->success(__('The rawmaterial inventory has been saved.'));

                    return $this->redirect(['action' => 'index']);
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__($exception->getMessage()));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('rawmaterialInventory', 'rawmaterials', 'usingFirestore'));
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreRawmaterialInventoryOrFail(string $id): RawmaterialInventory
    {
        $rawmaterialInventory = (new NewLoveFirestoreRepository())->rawmaterialInventory($id);

        if ($rawmaterialInventory === null) {
            throw new NotFoundException(__('Raw material inventory not found'));
        }

        return $this->firestoreRawmaterialInventoryEntity($rawmaterialInventory);
    }

    private function firestoreRawmaterialInventoryEntity(array $data = [], bool $isNew = false): RawmaterialInventory
    {
        unset($data['_document_id']);
        $rawmaterial = $data['rawmaterial'] ?? null;

        return (new RawmaterialInventory($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]))->set('rawmaterial', $rawmaterial, ['guard' => false]);
    }

    private function validateFirestoreRawmaterialInventory(
        array $data,
        array $rawmaterials,
        ?string $existingId = null,
        bool $allowExistingForConfirm = false
    ): array {
        $errors = [];
        $repository = new NewLoveFirestoreRepository();
        $rawmaterialId = $data['rawmaterial_id'] ?? '';

        if ($rawmaterialId === '' || !array_key_exists($rawmaterialId, $rawmaterials)) {
            $errors[] = 'Please select a rawmaterial.';
        }

        if (!$this->isIntegerInRange($data['quantity'] ?? null, 0, 99999)) {
            $errors[] = 'Quantity must be between 0 and 99,999.';
        }

        if (!$this->isIntegerInRange($data['lowStockLimit'] ?? null, 0, 99999)) {
            $errors[] = 'Low Stock Threshold must be between 0 and 99,999.';
        }

        if ($rawmaterialId !== '' && !$allowExistingForConfirm) {
            $existingInventory = $repository->rawmaterialInventoryByRawmaterialId((string)$rawmaterialId);

            if ($existingInventory !== null && (string)$existingInventory['id'] !== (string)$existingId) {
                $errors[] = 'This raw material already has an inventory record.';
            }
        }

        return $errors;
    }

    private function isIntegerInRange($value, int $min, int $max): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        $integer = filter_var($value, FILTER_VALIDATE_INT);

        return $integer !== false && $integer >= $min && $integer <= $max;
    }
}
