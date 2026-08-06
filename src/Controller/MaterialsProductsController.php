<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\MaterialsProduct;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * MaterialsProducts Controller
 *
 * @property \App\Model\Table\MaterialsProductsTable $MaterialsProducts
 * @method \App\Model\Entity\MaterialsProduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MaterialsProductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($productId = null)
    {
        if ($this->usesFirestore()) {
            $materialsProducts = array_map(
                [$this, 'firestoreMaterialsProductEntity'],
                (new NewLoveFirestoreRepository())->materialsProducts($productId !== null ? (string)$productId : null)
            );
            $usingFirestore = true;

            $this->set(compact('productId', 'materialsProducts', 'usingFirestore'));

            return;
        }

        $this->paginate = [
            'contain' => ['Products', 'Rawmaterials'],
        ];

        $query = $this->MaterialsProducts->find()->contain(['Products', 'Rawmaterials']);

        if ($productId) {
            $productEntity = $this->MaterialsProducts->Products->get($productId);
            if ($productEntity) {
                $productName = $productEntity->name;
                $query->where(['Products.name' => $productName]);
            }
        }

        $materialsProducts = $this->paginate($query);

        $this->set(compact('productId'));
        $this->set(compact('materialsProducts'));
    }

    /**
     * View method
     *
     * @param string|null $productId Product id.
     * @param string|null $rawmaterialId Raw material id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($productId = null, $rawmaterialId = null)
    {
        if ($this->usesFirestore()) {
            $materialsProduct = $this->firestoreMaterialsProductOrFail((string)$productId, (string)$rawmaterialId);
            $usingFirestore = true;

            $this->set(compact('materialsProduct', 'usingFirestore'));

            return;
        }

        $materialsProduct = $this->MaterialsProducts->get([$productId, $rawmaterialId], [
            'contain' => ['Products', 'Rawmaterials'],
        ]);

        $this->set(compact('materialsProduct'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($productId = null)
    {
        if ($this->usesFirestore()) {
            return $this->addWithFirestore($productId !== null ? (string)$productId : null);
        }

        $materialsProduct = $this->MaterialsProducts->newEmptyEntity();

        if ($productId) {
            $materialsProduct->product_id = $productId;
        }

        if ($this->request->is('post')) {
            $materialsProduct = $this->MaterialsProducts->patchEntity($materialsProduct, $this->request->getData());
            if ($this->MaterialsProducts->save($materialsProduct)) {
                $this->Flash->success(__('The materials product has been saved.'));
                return $this->redirect(['controller' => 'Products', 'action' => 'view', $materialsProduct->product_id]);
            }
            $this->Flash->error(__('The materials product could not be saved. Please, try again.'));
        }

        $products = $this->MaterialsProducts->Products->find('list', ['limit' => 200])->all();
        $rawmaterials = $this->MaterialsProducts->Rawmaterials->find('list', ['limit' => 200])->all();

        $this->set(compact('materialsProduct', 'products', 'rawmaterials'));
    }

    /**
     * Edit method
     *
     * @param string|null $productId Product id.
     * @param string|null $rawMaterialId Raw material id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($productId = null, $rawMaterialId = null)
    {
        if ($this->usesFirestore()) {
            return $this->editWithFirestore((string)$productId, (string)$rawMaterialId);
        }

        $materialsProduct = $this->MaterialsProducts->get([$productId, $rawMaterialId], [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $materialsProduct = $this->MaterialsProducts->patchEntity($materialsProduct, $this->request->getData());
            if ($this->MaterialsProducts->save($materialsProduct)) {
                $this->Flash->success(__('The materials product has been saved.'));
                return $this->redirect(['action' => 'index', $materialsProduct->product_id]);
            }
            $this->Flash->error(__('The materials product could not be saved. Please, try again.'));
        }

        $products = $this->MaterialsProducts->Products->find('list', ['limit' => 200])->all();
        $rawmaterials = $this->MaterialsProducts->Rawmaterials->find('list', ['limit' => 200])->all();

        $this->set(compact('materialsProduct', 'products', 'rawmaterials'));
    }

    /**
     * Delete method
     *
     * @param string|null $productId Product id.
     * @param string|null $rawMaterialId Raw material id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($productId = null, $rawMaterialId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteMaterialsProduct((string)$productId, (string)$rawMaterialId);
                $this->Flash->success(__('The materials product has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The materials product could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index', $productId]);
        }

        $materialsProduct = $this->MaterialsProducts->get([$productId, $rawMaterialId]);

        if ($this->MaterialsProducts->delete($materialsProduct)) {
            $this->Flash->success(__('The materials product has been deleted.'));
        } else {
            $this->Flash->error(__('The materials product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index', $materialsProduct->product_id]);
    }

    private function addWithFirestore(?string $productId)
    {
        $repository = new NewLoveFirestoreRepository();
        $products = $repository->productInventoryProductList();
        $rawmaterials = $repository->rawmaterialInventoryRawmaterialList();
        $materialsProduct = $this->firestoreMaterialsProductEntity([
            'product_id' => $productId ?? '',
            'rawmaterial_id' => '',
            'quantity' => '',
        ], true);
        $usingFirestore = true;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $materialsProduct = $this->firestoreMaterialsProductEntity($data, true);
            $errors = $this->validateFirestoreMaterialsProduct($data, $products, $rawmaterials);

            if ($errors === []) {
                try {
                    $saved = $repository->createMaterialsProduct($data);
                    $this->Flash->success(__('The materials product has been saved.'));

                    return $this->redirect(['controller' => 'Products', 'action' => 'view', $saved['product_id']]);
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__('The materials product could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('materialsProduct', 'products', 'rawmaterials', 'usingFirestore'));
    }

    private function editWithFirestore(string $productId, string $rawMaterialId)
    {
        $repository = new NewLoveFirestoreRepository();
        $materialsProductData = $repository->materialsProduct($productId, $rawMaterialId);

        if ($materialsProductData === null) {
            throw new NotFoundException(__('Materials product not found'));
        }

        $products = $repository->productInventoryProductList();
        $rawmaterials = $repository->rawmaterialInventoryRawmaterialList();
        $materialsProduct = $this->firestoreMaterialsProductEntity($materialsProductData);
        $usingFirestore = true;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $materialsProduct = $this->firestoreMaterialsProductEntity(array_merge($materialsProductData, $data));
            $errors = $this->validateFirestoreMaterialsProduct($data, $products, $rawmaterials, $productId, $rawMaterialId);

            if ($errors === []) {
                try {
                    $saved = $repository->updateMaterialsProduct($productId, $rawMaterialId, $data);
                    $this->Flash->success(__('The materials product has been saved.'));

                    return $this->redirect(['action' => 'index', $saved['product_id']]);
                } catch (\RuntimeException $exception) {
                    $this->Flash->error(__('The materials product could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(implode(' ', $errors));
            }
        }

        $this->set(compact('materialsProduct', 'products', 'rawmaterials', 'usingFirestore'));
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreMaterialsProductOrFail(string $productId, string $rawmaterialId): MaterialsProduct
    {
        $materialsProduct = (new NewLoveFirestoreRepository())->materialsProduct($productId, $rawmaterialId);

        if ($materialsProduct === null) {
            throw new NotFoundException(__('Materials product not found'));
        }

        return $this->firestoreMaterialsProductEntity($materialsProduct);
    }

    private function firestoreMaterialsProductEntity(array $data = [], bool $isNew = false): MaterialsProduct
    {
        unset($data['_document_id']);
        $product = $data['product'] ?? null;
        $rawmaterial = $data['rawmaterial'] ?? null;

        return (new MaterialsProduct($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]))
            ->set('product', $product, ['guard' => false])
            ->set('rawmaterial', $rawmaterial, ['guard' => false]);
    }

    private function validateFirestoreMaterialsProduct(
        array $data,
        array $products,
        array $rawmaterials,
        ?string $excludeProductId = null,
        ?string $excludeRawmaterialId = null
    ): array {
        $errors = [];
        $repository = new NewLoveFirestoreRepository();
        $productId = $data['product_id'] ?? '';
        $rawmaterialId = $data['rawmaterial_id'] ?? '';

        if ($productId === '' || !array_key_exists($productId, $products)) {
            $errors[] = 'Please select a product.';
        }

        if ($rawmaterialId === '' || !array_key_exists($rawmaterialId, $rawmaterials)) {
            $errors[] = 'Please select a raw material.';
        }

        if (!$this->isIntegerInRange($data['quantity'] ?? null, 0, 99999)) {
            $errors[] = 'Quantity must be between 0 and 99,999.';
        }

        if (
            $productId !== '' &&
            $rawmaterialId !== '' &&
            $repository->materialsProductExists(
                (string)$productId,
                (string)$rawmaterialId,
                $excludeProductId,
                $excludeRawmaterialId
            )
        ) {
            $errors[] = 'This material is already assigned to the selected product.';
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
