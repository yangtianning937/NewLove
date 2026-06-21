<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\ProductInventory;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

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
        if ($this->usesFirestore()) {
            $productInventories = array_map(
                [$this, 'firestoreProductInventoryEntity'],
                (new NewLoveFirestoreRepository())->productInventories()
            );
            $usingFirestore = true;

            $this->set(compact('productInventories', 'usingFirestore'));

            return;
        }

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
        if ($this->usesFirestore()) {
            $productInventory = $this->firestoreProductInventoryOrFail((string)$id);
            $usingFirestore = true;

            $this->set(compact('productInventory', 'usingFirestore'));

            return;
        }

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
        if ($this->usesFirestore()) {
            return $this->addWithFirestore();
        }

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
        if ($this->usesFirestore()) {
            return $this->editWithFirestore((string)$id);
        }

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

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteProductInventory((string)$id);
                $this->Flash->success(__('The product inventory has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The product inventory could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $productInventory = $this->ProductInventories->get($id);
        if ($this->ProductInventories->delete($productInventory)) {
            $this->Flash->success(__('The product inventory has been deleted.'));
        } else {
            $this->Flash->error(__('The product inventory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function addWithFirestore()
    {
        $repository = new NewLoveFirestoreRepository();
        $productInventory = $this->firestoreProductInventoryEntity([
            'product_id' => '',
            'quantity' => '',
        ], true);
        $products = $repository->productInventoryProductList();
        $showConfirm = false;
        $usingFirestore = true;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $productInventory = $this->firestoreProductInventoryEntity($data, true);
            $errors = $this->validateFirestoreProductInventory($data, $products);

            if ($errors === []) {
                $existingProduct = $repository->productInventory((string)$data['product_id']);

                try {
                    if ($existingProduct !== null && !$this->request->getData('confirmed')) {
                        $showConfirm = true;
                    } elseif ($existingProduct !== null) {
                        $repository->increaseProductInventory((string)$data['product_id'], (int)$data['quantity']);
                        $this->Flash->success(__('The product inventory quantity has been updated.'));

                        return $this->redirect(['action' => 'index']);
                    } else {
                        $repository->createProductInventory($data);
                        $this->Flash->success(__('The product inventory has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    }
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__($exception->getMessage()));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('productInventory', 'products', 'showConfirm', 'usingFirestore'));
    }

    private function editWithFirestore(string $id)
    {
        $repository = new NewLoveFirestoreRepository();
        $inventoryData = $repository->productInventory($id);

        if ($inventoryData === null) {
            throw new NotFoundException(__('Product inventory not found'));
        }

        $productInventory = $this->firestoreProductInventoryEntity($inventoryData);
        $products = $repository->productInventoryProductList();
        $usingFirestore = true;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $productInventory = $this->firestoreProductInventoryEntity(array_merge($inventoryData, $data));
            $errors = $this->validateFirestoreProductInventory(array_merge($inventoryData, $data), $products);

            if ($errors === []) {
                try {
                    $repository->updateProductInventory($id, $data);
                    $this->Flash->success(__('The product inventory has been saved.'));

                    return $this->redirect(['action' => 'index']);
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__($exception->getMessage()));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('productInventory', 'products', 'usingFirestore'));
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreProductInventoryOrFail(string $id): ProductInventory
    {
        $productInventory = (new NewLoveFirestoreRepository())->productInventory($id);

        if ($productInventory === null) {
            throw new NotFoundException(__('Product inventory not found'));
        }

        return $this->firestoreProductInventoryEntity($productInventory);
    }

    private function firestoreProductInventoryEntity(array $data = [], bool $isNew = false): ProductInventory
    {
        unset($data['_document_id']);
        $product = $data['product'] ?? null;

        return (new ProductInventory($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]))->set('product', $product, ['guard' => false]);
    }

    private function validateFirestoreProductInventory(array $data, array $products): array
    {
        $errors = [];
        $productId = $data['product_id'] ?? '';

        if ($productId === '' || !array_key_exists($productId, $products)) {
            $errors[] = 'Please select a product.';
        }

        if (!$this->isIntegerInRange($data['quantity'] ?? null, 0, 99999)) {
            $errors[] = 'Quantity must be between 0 and 99,999.';
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
