<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * RawmaterialColour Controller
 *
 * @property \App\Model\Table\RawmaterialColourTable $RawmaterialColour
 * @method \App\Model\Entity\RawmaterialColour[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RawmaterialColourController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->loadModel('RawmaterialColour');

        $rawmaterialColour = $this->paginate($this->RawmaterialColour);

        $this->set(compact('rawmaterialColour'));
    }

    /**
     * View method
     *
     * @param string|null $id Rawmaterial Colour id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->loadModel('RawmaterialColour');

        $rawmaterialColour = $this->RawmaterialColour->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('rawmaterialColour'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->loadModel('RawmaterialColour');

        $rawmaterialColour = $this->RawmaterialColour->newEmptyEntity();
        if ($this->request->is('post')) {
            $rawmaterialColour = $this->RawmaterialColour->patchEntity($rawmaterialColour, $this->request->getData());
            if ($this->RawmaterialColour->save($rawmaterialColour)) {
                $this->Flash->success(__('The rawmaterial colour has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rawmaterial colour could not be saved. Please, try again.'));
        }
        $this->set(compact('rawmaterialColour'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rawmaterial Colour id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModel('RawmaterialColour');

        $rawmaterialColour = $this->RawmaterialColour->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rawmaterialColour = $this->RawmaterialColour->patchEntity($rawmaterialColour, $this->request->getData());
            if ($this->RawmaterialColour->save($rawmaterialColour)) {
                $this->Flash->success(__('The rawmaterial colour has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rawmaterial colour could not be saved. Please, try again.'));
        }
        $this->set(compact('rawmaterialColour'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rawmaterial Colour id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->loadModel('RawmaterialColour');

        $this->request->allowMethod(['post', 'delete']);
        $rawmaterialColour = $this->RawmaterialColour->get($id);
        if ($this->RawmaterialColour->delete($rawmaterialColour)) {
            $this->Flash->success(__('The rawmaterial colour has been deleted.'));
        } else {
            $this->Flash->error(__('The rawmaterial colour could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
