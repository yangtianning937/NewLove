<?php
declare(strict_types=1);

namespace App\Controller;

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
        $rawmaterialInventory = $this->RawmaterialInventories->get($id, [
            'contain' => ['Rawmaterials.Colours'],  // 确保加载与Rawmaterials关联的Colours数据
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
        $rawmaterialInventory = $this->RawmaterialInventories->get($id);
        if ($this->RawmaterialInventories->delete($rawmaterialInventory)) {
            $this->Flash->success(__('The rawmaterial inventory has been deleted.'));
        } else {
            $this->Flash->error(__('The rawmaterial inventory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
