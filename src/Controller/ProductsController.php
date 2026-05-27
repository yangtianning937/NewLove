<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\ORM\TableRegistry; // 导入 TableRegistry 类
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
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
