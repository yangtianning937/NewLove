<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->usesFirestore()) {
            $users = array_map([$this, 'firestoreUserEntity'], (new NewLoveFirestoreRepository())->users());

            $this->set(compact('users'));

            return;
        }

        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if ($this->usesFirestore()) {
            $user = $this->firestoreUserOrFail((string)$id);

            $this->set(compact('user'));

            return;
        }

        $user = $this->Users->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->usesFirestore()) {
            $user = $this->firestoreUserEntity([], true);

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $user = $this->firestoreUserEntity($data, true);
                $errors = $this->validateFirestoreUser($data, null, true);

                if ($errors === []) {
                    try {
                        (new NewLoveFirestoreRepository())->createUser($data);
                        $this->Flash->success(__('The user has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The user could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('user'));

            return;
        }

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->usesFirestore()) {
            $repository = new NewLoveFirestoreRepository();
            $userData = $repository->userById((string)$id);

            if ($userData === null) {
                throw new NotFoundException(__('User not found'));
            }

            $user = $this->firestoreUserEntity($userData);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $user = $this->firestoreUserEntity(array_merge($userData, $data));
                $errors = $this->validateFirestoreUser($data, (string)$id, false);

                if ($errors === []) {
                    try {
                        $repository->updateUser((string)$id, $data);
                        $this->Flash->success(__('The user has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The user could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('user'));

            return;
        }

        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteUser((string)$id);
                $this->Flash->success(__('The user has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The user could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreUserOrFail(string $id): User
    {
        $user = (new NewLoveFirestoreRepository())->userById($id);

        if ($user === null) {
            throw new NotFoundException(__('User not found'));
        }

        return $this->firestoreUserEntity($user);
    }

    private function firestoreUserEntity(array $data = [], bool $isNew = false): User
    {
        unset($data['password'], $data['_document_id']);

        return new User($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]);
    }

    private function validateFirestoreUser(array $data, ?string $existingId, bool $requirePassword): array
    {
        $errors = [];
        $repository = new NewLoveFirestoreRepository();
        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName = trim((string)($data['last_name'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $password = (string)($data['password'] ?? '');
        $existingUser = $email !== '' ? $repository->userByEmail($email) : null;

        if ($firstName === '') {
            $errors[] = 'First name cannot be empty.';
        } elseif (strlen($firstName) > 64) {
            $errors[] = 'First name cannot exceed 64 characters.';
        }

        if ($lastName === '') {
            $errors[] = 'Last name cannot be empty.';
        } elseif (strlen($lastName) > 64) {
            $errors[] = 'Last name cannot exceed 64 characters.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ($existingUser !== null && (string)$existingUser['id'] !== (string)$existingId) {
            $errors[] = 'Email address already in use.';
        }

        if ($requirePassword && $password === '') {
            $errors[] = 'Password cannot be empty.';
        }

        if ($password !== '' && strlen($password) < 8) {
            $errors[] = 'Password should be at least 8 characters long.';
        } elseif (strlen($password) > 64) {
            $errors[] = 'Password cannot exceed 64 characters.';
        }

        return $errors;
    }
}
