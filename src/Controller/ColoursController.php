<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Colour;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * Colours Controller
 *
 * @property \App\Model\Table\ColoursTable $Colours
 * @method \App\Model\Entity\Colour[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ColoursController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->usesFirestore()) {
            $colours = array_map([$this, 'firestoreColourEntity'], (new NewLoveFirestoreRepository())->colours());

            $this->set(compact('colours'));

            return;
        }

        $colours = $this->paginate($this->Colours);

        $this->set(compact('colours'));
    }

    /**
     * View method
     *
     * @param string|null $id Colour id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if ($this->usesFirestore()) {
            $colour = $this->firestoreColourOrFail((string)$id);

            $this->set(compact('colour'));

            return;
        }

        $colour = $this->Colours->get($id, [
            'contain' => ['Products', 'Rawmaterials'],
        ]);

        $this->set(compact('colour'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->usesFirestore()) {
            $colour = $this->firestoreColourEntity([], true);

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $colour = $this->firestoreColourEntity($data, true);
                $errors = $this->validateFirestoreColour($data);

                if ($errors === []) {
                    try {
                        (new NewLoveFirestoreRepository())->createColour($data);
                        $this->Flash->success(__('The colour has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The colour could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('colour'));

            return;
        }

        $colour = $this->Colours->newEmptyEntity();
        if ($this->request->is('post')) {
            $colour = $this->Colours->patchEntity($colour, $this->request->getData());
            if ($this->Colours->save($colour)) {
                $this->Flash->success(__('The colour has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The colour could not be saved. Please, try again.'));
        }
        $this->set(compact('colour'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Colour id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->usesFirestore()) {
            $repository = new NewLoveFirestoreRepository();
            $colourData = $repository->colour((string)$id);

            if ($colourData === null) {
                throw new NotFoundException(__('Colour not found'));
            }

            $colour = $this->firestoreColourEntity($colourData);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $colour = $this->firestoreColourEntity(array_merge($colourData, $data));
                $errors = $this->validateFirestoreColour($data);

                if ($errors === []) {
                    try {
                        $repository->updateColour((string)$id, $data);
                        $this->Flash->success(__('The colour has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The colour could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('colour'));

            return;
        }

        $colour = $this->Colours->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $colour = $this->Colours->patchEntity($colour, $this->request->getData());
            if ($this->Colours->save($colour)) {
                $this->Flash->success(__('The colour has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The colour could not be saved. Please, try again.'));
        }
        $this->set(compact('colour'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Colour id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteColour((string)$id);
                $this->Flash->success(__('The colour has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The colour could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $colour = $this->Colours->get($id);
        if ($this->Colours->delete($colour)) {
            $this->Flash->success(__('The colour has been deleted.'));
        } else {
            $this->Flash->error(__('The colour could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreColourOrFail(string $id): Colour
    {
        $colour = (new NewLoveFirestoreRepository())->colour($id);

        if ($colour === null) {
            throw new NotFoundException(__('Colour not found'));
        }

        return $this->firestoreColourEntity($colour);
    }

    private function firestoreColourEntity(array $data = [], bool $isNew = false): Colour
    {
        unset($data['_document_id']);

        return new Colour($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]);
    }

    private function validateFirestoreColour(array $data): array
    {
        $errors = [];
        $name = trim((string)($data['name'] ?? ''));

        if ($name === '') {
            $errors[] = 'Please enter a name for the colours.';
        } elseif (strlen($name) > 25) {
            $errors[] = 'Colour name cannot exceed 25 characters.';
        }

        return $errors;
    }
}
