<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Model\Table\CollectionsTable;
use App\Model\Table\ColoursTable;
use App\Model\Table\RawmaterialsTable;
use App\Model\Table\ProductMaterialBridgeTable;

/**
 * Product Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $allowedActions = ['index', 'view'];

        if ($this->firestoreRepository()->isEnabled()) {
            $allowedActions = ['index', 'view', 'add', 'edit', 'delete'];
        }

        $this->Authentication->allowUnauthenticated($allowedActions);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $productsName = $this->request->getQuery('name');
        $colourID = $this->request->getQuery('colour_id');
        $collectionID = $this->request->getQuery('collection_id');
        $productsDesc = $this->request->getQuery('description');

        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $product = $firestoreRepository->products($productsName, $colourID, $collectionID, $productsDesc);
            $colourName = $firestoreRepository->colourList();
            $collectionName = $firestoreRepository->collectionList();
            $usingFirestore = true;

            $this->set(compact('product', 'colourName', 'collectionName', 'usingFirestore'));
            $this->set(compact('productsName', 'colourID', 'collectionID', 'productsDesc'));

            return;
        }

        $productsTable = TableRegistry::getTableLocator()->get('Products');

        $conditions = [];

        if (!empty($productsName)) {
            $conditions['name LIKE'] = '%' . $productsName . '%';
        }

        if (!empty($productsDesc)) {
            $conditions['description LIKE'] = '%' . $productsDesc . '%';
        }


        $query = $productsTable->find('all')
            ->where($conditions)
            ->contain(['ProductInventories']);

        if (!empty($colourID)) {
            $query->where(['colour_id' => $colourID]);
        }

        if (!empty($collectionID)) {
            $query->where(['collection_id' => $collectionID]);
        }

        $product = $query->toList();

        $ColoursTable = new ColoursTable();
        $colourName = $ColoursTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $CollectionsTable = new CollectionsTable();
        $collectionName = $CollectionsTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $this->set(compact('product', 'colourName', 'collectionName'));
        $this->set(compact('productsName', 'colourID', 'collectionID', 'productsDesc'));
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $product = $firestoreRepository->product((string)$id);

            if ($product === null) {
                throw new NotFoundException(__('Product not found'));
            }

            $collectionName = !empty($product->collection) ? $product->collection->name : 'null';
            $productColour = !empty($product->colour) ? $product->colour->name : 'null';
            $usingFirestore = true;

            $this->set(compact('product', 'collectionName', 'productColour', 'usingFirestore'));

            return;
        }

        $product = $this->Products->get($id, [
            'contain' => [],
        ]);

        $product = $this->Products->get($id, [
            'contain' => [
                'Collections',
                'Colours',
                'MaterialsProducts.Rawmaterials'
            ]
        ]);

        if (!empty($product->collection)) {
            $collectionName = $product->collection->name;
        } else {
            $collectionName = "null";
        }

        if (!empty($product->colour)) {
            $productColour = $product->colour->name;
        } else {
            $productColour = "null";
        }

        $this->set(compact('product', 'collectionName', 'productColour'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            return $this->addWithFirestore($firestoreRepository);
        }

        $product = $this->Products->newEmptyEntity();

        $CollectionsTable = new CollectionsTable();
        $collectionNames = $CollectionsTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        $this->set(compact('product', 'collectionNames'));

        $ColoursTable = new ColoursTable();
        $colourName = $ColoursTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        $this->set(compact('product', 'colourName'));

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $file = $this->request->getData('photo');


            if (empty($file->getClientFilename())) {
                $this->Flash->error(__('Please upload a product image.'));
                return;
            }


            $existingProduct = $this->Products->find('all', [
                'conditions' => ['name' => $data['name']]
            ])->first();

            if ($existingProduct) {
                if ($existingProduct->colour_id == $data['colour_id']) {
                    $this->Flash->error(__('You cannot add the same product with the same colour.'));
                    return;
                }
            }


            $filename = $file->getClientFilename();
            $file->moveTo(WWW_ROOT . 'img' . DS . $filename);

            $product = $this->Products->patchEntity($product, $data);
            $product->photo = $filename;

            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));
                return $this->redirect(['action' => 'view', $product->id]);
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('product'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            return $this->editWithFirestore($firestoreRepository, (string)$id);
        }

        $product = $this->Products->get($id, [
            'contain' => [],
        ]);
        $originalImage = $product->photo;

        $productCollectionTable = new CollectionsTable();
        $collectionNames = $productCollectionTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        $this->set(compact('product', 'collectionNames'));

        $productColourTable = new ColoursTable();
        $colourName = $productColourTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        $this->set(compact('product', 'colourName'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            $existingProduct = $this->Products->find('all', [
                'conditions' => ['name' => $data['name'], 'id !=' => $id]
            ])->first();

            if ($existingProduct) {
                if ($existingProduct->colour_id == $data['colour_id']) {
                    $this->Flash->error(__('You cannot edit to have the same product name and colour as another product.'));
                    return $this->redirect($this->referer());
                }
            }

            $file = $data['photo'] ?? null;

            if ($file && !empty($file->getClientFilename())) {
                $filename = $file->getClientFilename();
                $file->moveTo(WWW_ROOT . 'img' . DS . $filename);
                $product->photo = $filename;
            } else {
                $data['photo'] = $originalImage;
            }

            $product = $this->Products->patchEntity($product, $data);

            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));
                return $this->redirect(['action' => 'view', $product->id]);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }

        $this->set(compact('product'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $firestoreRepository->deleteProduct((string)$id);
            $this->Flash->success(__('The product has been deleted.'));

            return $this->redirect(['action' => 'index']);
        }

        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function firestoreRepository(): NewLoveFirestoreRepository
    {
        return new NewLoveFirestoreRepository();
    }

    private function addWithFirestore(NewLoveFirestoreRepository $firestoreRepository)
    {
        $product = $this->productEntity($firestoreRepository->emptyProduct());
        $collectionNames = $firestoreRepository->collectionList();
        $colourName = $firestoreRepository->colourList();
        $usingFirestore = true;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $file = $this->request->getData('photo');
            $filename = $this->uploadedFileName($file);

            if (trim((string)($data['name'] ?? '')) === '') {
                $this->Flash->error(__('Please enter a product name.'));
            } elseif (empty($data['colour_id'])) {
                $this->Flash->error(__('Please choose a colour.'));
            } elseif ($filename === null) {
                $this->Flash->error(__('Please upload a product image.'));
            } elseif ($firestoreRepository->productExistsWithNameAndColour((string)$data['name'], $data['colour_id'])) {
                $this->Flash->error(__('You cannot add the same product with the same colour.'));
            } else {
                $file->moveTo(WWW_ROOT . 'img' . DS . $filename);
                $savedProduct = $firestoreRepository->createProduct($data, $filename);
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'view', $savedProduct->id]);
            }

            $product = $this->productEntity((object)$data);
        }

        $this->set(compact('product', 'collectionNames', 'colourName', 'usingFirestore'));
    }

    private function editWithFirestore(NewLoveFirestoreRepository $firestoreRepository, string $id)
    {
        $existingProduct = $firestoreRepository->product($id);

        if ($existingProduct === null) {
            throw new NotFoundException(__('Product not found'));
        }

        $product = $this->productEntity($existingProduct);
        $collectionNames = $firestoreRepository->collectionList();
        $colourName = $firestoreRepository->colourList();
        $usingFirestore = true;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if (trim((string)($data['name'] ?? '')) === '') {
                $this->Flash->error(__('Please enter a product name.'));
            } elseif (empty($data['colour_id'])) {
                $this->Flash->error(__('Please choose a colour.'));
            } elseif ($firestoreRepository->productExistsWithNameAndColour((string)$data['name'], $data['colour_id'], $id)) {
                $this->Flash->error(__('You cannot edit to have the same product name and colour as another product.'));
            } else {
                $file = $data['photo'] ?? null;
                $filename = $this->uploadedFileName($file);

                if ($filename !== null) {
                    $file->moveTo(WWW_ROOT . 'img' . DS . $filename);
                }

                $savedProduct = $firestoreRepository->updateProduct($id, $data, $filename);
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'view', $savedProduct->id]);
            }

            $product = $this->productEntity((object)array_merge(get_object_vars($existingProduct), $data));
        }

        $this->set(compact('product', 'collectionNames', 'colourName', 'usingFirestore'));
    }

    private function uploadedFileName($file): ?string
    {
        if (!is_object($file) || !method_exists($file, 'getClientFilename')) {
            return null;
        }

        $filename = trim((string)$file->getClientFilename());

        return $filename !== '' ? $filename : null;
    }

    private function productEntity(object $product): object
    {
        return (object)get_object_vars($product);
    }
}
