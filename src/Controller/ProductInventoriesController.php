<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ProductInventories Controller
 *
 * @property \App\Model\Table\ProductInventoriesTable $ProductInventories
 * @method \App\Model\Entity\ProductInventory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductInventoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Products', 'Products.Colours'],
        ];
        $productInventories = $this->paginate($this->ProductInventories);

        $this->set(compact('productInventories'));
    }

    /**
     * View method
     *
     * @param string|null $id Product Inventory id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $productInventory = $this->ProductInventories->get($id, [
            'contain' => ['Products'],
        ]);

        $this->set(compact('productInventory'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $productInventory = $this->ProductInventories->newEmptyEntity();
        $showConfirm = false;

        if ($this->request->is('post')) {
            $existingProduct = $this->ProductInventories->findByProductId($this->request->getData('product_id'))->first();

            if ($existingProduct && !$this->request->getData('confirmed')) {
                $showConfirm = true;
            } elseif ($existingProduct && $this->request->getData('confirmed')) {
                $newQuantity = $this->request->getData('quantity');
                $existingProduct->quantity += $newQuantity;

                if ($this->ProductInventories->save($existingProduct)) {
                    $this->Flash->success(__('The product inventory quantity has been updated.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The product inventory could not be updated. Please, try again.'));
                }
            } else {
                $productInventory = $this->ProductInventories->patchEntity($productInventory, $this->request->getData());

                if ($this->ProductInventories->save($productInventory)) {
                    $this->Flash->success(__('The product inventory has been saved.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The product inventory could not be saved. Please, try again.'));
                }
            }
        }

        $this->loadModel('Products');
        $productsData = $this->Products->find('all', [
            'contain' => ['Colours']
        ])->toList();

        $products = [];
        foreach ($productsData as $product) {
            $products[$product->id] = $product->name . ' - ' . $product->colour->name;
        }

        $this->set(compact('productInventory', 'products', 'showConfirm'));
    }






    /**
     * Edit method
     *
     * @param string|null $id Product Inventory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $productInventory = $this->ProductInventories->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $productInventory = $this->ProductInventories->patchEntity($productInventory, $this->request->getData());

            if ($this->ProductInventories->save($productInventory)) {
                $this->Flash->success(__('The product inventory has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The product inventory could not be saved. Please, try again.'));
            }
        }
        $products = $this->ProductInventories->Products->find('list', ['limit' => 200])->all();
        $this->set(compact('productInventory', 'products'));
    }




    /**
     * Delete method
     *
     * @param string|null $id Product Inventory id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $productInventory = $this->ProductInventories->get($id);
        if ($this->ProductInventories->delete($productInventory)) {
            $this->Flash->success(__('The product inventory has been deleted.'));
        } else {
            $this->Flash->error(__('The product inventory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
