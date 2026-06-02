<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Collection;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * Collections Controller
 *
 * @property \App\Model\Table\CollectionsTable $Collections
 * @method \App\Model\Entity\Collection[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CollectionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->usesFirestore()) {
            $collections = array_map([$this, 'firestoreCollectionEntity'], (new NewLoveFirestoreRepository())->collections());

            $this->set(compact('collections'));

            return;
        }

        $collections = $this->paginate($this->Collections);

        $this->set(compact('collections'));
    }

    /**
     * View method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if ($this->usesFirestore()) {
            $collection = $this->firestoreCollectionOrFail((string)$id);

            $this->set(compact('collection'));

            return;
        }

        $collection = $this->Collections->get($id, [
            'contain' => ['Products'],
        ]);

        $this->set(compact('collection'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->usesFirestore()) {
            $collection = $this->firestoreCollectionEntity([], true);

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $collection = $this->firestoreCollectionEntity($data, true);
                $errors = $this->validateFirestoreCollection($data);

                if ($errors === []) {
                    try {
                        (new NewLoveFirestoreRepository())->createCollection($data);
                        $this->Flash->success(__('The collection has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The collection could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('collection'));

            return;
        }

        $collection = $this->Collections->newEmptyEntity();
        if ($this->request->is('post')) {
            $collection = $this->Collections->patchEntity($collection, $this->request->getData());
            if ($this->Collections->save($collection)) {
                $this->Flash->success(__('The collection has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The collection could not be saved. Please, try again.'));
        }
        $this->set(compact('collection'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->usesFirestore()) {
            $repository = new NewLoveFirestoreRepository();
            $collectionData = $repository->collection((string)$id);

            if ($collectionData === null) {
                throw new NotFoundException(__('Collection not found'));
            }

            $collection = $this->firestoreCollectionEntity($collectionData);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $collection = $this->firestoreCollectionEntity(array_merge($collectionData, $data));
                $errors = $this->validateFirestoreCollection($data);

                if ($errors === []) {
                    try {
                        $repository->updateCollection((string)$id, $data);
                        $this->Flash->success(__('The collection has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The collection could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('collection'));

            return;
        }

        $collection = $this->Collections->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $collection = $this->Collections->patchEntity($collection, $this->request->getData());
            if ($this->Collections->save($collection)) {
                $this->Flash->success(__('The collection has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The collection could not be saved. Please, try again.'));
        }
        $this->set(compact('collection'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteCollection((string)$id);
                $this->Flash->success(__('The collection has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The collection could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $collection = $this->Collections->get($id);
        if ($this->Collections->delete($collection)) {
            $this->Flash->success(__('The collection has been deleted.'));
        } else {
            $this->Flash->error(__('The collection could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreCollectionOrFail(string $id): Collection
    {
        $collection = (new NewLoveFirestoreRepository())->collection($id);

        if ($collection === null) {
            throw new NotFoundException(__('Collection not found'));
        }

        return $this->firestoreCollectionEntity($collection);
    }

    private function firestoreCollectionEntity(array $data = [], bool $isNew = false): Collection
    {
        unset($data['_document_id']);

        return new Collection($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]);
    }

    private function validateFirestoreCollection(array $data): array
    {
        $errors = [];
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            $errors[] = 'Please enter a name for the collection.';
        } elseif (strlen($name) > 255) {
            $errors[] = 'Collection name cannot exceed 255 characters.';
        }

        return $errors;
    }
}
