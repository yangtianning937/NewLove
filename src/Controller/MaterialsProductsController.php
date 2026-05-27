<?php
declare(strict_types=1);

namespace App\Controller;

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
    public function index($productId = null) // 设置$productId为可选参数
    {
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
     * @param string|null $id Materials Product id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($productId = null, $rawmaterialId = null)
    {
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
        $materialsProduct = $this->MaterialsProducts->newEmptyEntity();

        // 如果提供了产品ID，则设置为默认值
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
     * @param string|null $id Materials Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($productId = null, $rawMaterialId = null)
    {

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
     * @param string|null $id Materials Product id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($productId = null, $rawMaterialId = null)
    {
        $this->request->allowMethod(['post', 'delete']);


        $materialsProduct = $this->MaterialsProducts->get([$productId, $rawMaterialId]);

        if ($this->MaterialsProducts->delete($materialsProduct)) {
            $this->Flash->success(__('The materials product has been deleted.'));
        } else {
            $this->Flash->error(__('The materials product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index', $materialsProduct->product_id]);
    }

}
