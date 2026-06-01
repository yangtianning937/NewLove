<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\NewLoveFirestoreRepository;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
use Cake\Utility\Security;

/**
 * Auth Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AuthController extends AppController {

    /**
     * @var \App\Model\Table\UsersTable $Users
     */
    private $Users;

    /**
     * Controller initialize override
     *
     * @return void
     */
    public function initialize(): void {
        parent::initialize();

        // By default, CakePHP will (sensibly) default to preventing users from accessing any actions on a controller.
        // These actions, however, are typically required for users who have not yet logged in.
        $this->Authentication->allowUnauthenticated(['login', 'register', 'forgetPassword', 'resetPassword']);

        if (!$this->usesFirestoreAuth()) {
            // CakePHP loads the model with the same name as the controller by default.
            // Since we don't have an Auth model, we'll need to load "Users" model when starting the controller manually.
            $this->Users = $this->fetchTable('Users');
        }
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function register() {
        if ($this->usesFirestoreAuth()) {
            $user = null;

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $errors = $this->validateFirestoreRegistration($data);

                if ($errors === []) {
                    try {
                        (new NewLoveFirestoreRepository())->createUser($data);
                        $this->Flash->success('You have been registered. Please log in. ');

                        return $this->redirect(['action' => 'login']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error('The user could not be registered. Please, try again.');
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
                $this->Flash->success('You have been registered. Please log in. ');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The user could not be registered. Please, try again.');
        }
        $this->set(compact('user'));
    }

    /**
     * Forget Password method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful email send, renders view otherwise.
     */
    public function forgetPassword() {
        if ($this->usesFirestoreAuth()) {
            $this->Flash->error('Password reset is not available while Firebase login is enabled.');

            return $this->redirect(['action' => 'login']);
        }

        if ($this->request->is('post')) {
            // Retrieve the user entity by provided email address
            $user = $this->Users->findByEmail($this->request->getData('email'))->first();
            if ($user) {
                // Set nonce and expiry date
                $user->nonce = Security::randomString(128);
                $user->nonce_expiry = new FrozenTime('7 days');
                if ($this->Users->save($user)) {
                    // Now let's send the password reset email
                    $mailer = new Mailer('default');

                    // email basic config
                    $mailer
                        ->setEmailFormat('both')
                        ->setTo($user->email)
                        ->setSubject('Reset your account password');

                    // select email template
                    $mailer
                        ->viewBuilder()
                        ->setTemplate('reset_password');

                    // transfer required view variables to email template
                    $mailer
                        ->setViewVars([
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'nonce' => $user->nonce,
                            'email' => $user->email
                        ]);

                    //Send email
                    if (!$mailer->deliver()) {
                        // Just in case something goes wrong when sending emails
                        $this->Flash->error('We have encountered an issue when sending you emails. Please try again. ');
                        return $this->render();  // Skip the rest of the controller and render the view
                    }
                } else {
                    // Just in case something goes wrong when saving nonce and expiry
                    $this->Flash->error('We are having issue to reset your password. Please try again. ');
                    return $this->render();  // Skip the rest of the controller and render the view
                }
            }

            /*
             * **This is a bit of a special design**
             * We don't tell the user if their account exists, or if the email has been sent,
             * because it may be used by someone with malicious intent. We only need to tell
             * the user that they'll get an email.
             */
            $this->Flash->success('Please check your inbox (or spam folder) for an email regarding how to reset your account password. ');
            return $this->redirect(['action' => 'login']);

        }
    }

    /**
     * Reset Password method
     *
     * @param string|null $nonce Reset password nonce
     * @return \Cake\Http\Response|null|void Redirects on successful password reset, renders view otherwise.
     */
    public function resetPassword($nonce = null) {
        if ($this->usesFirestoreAuth()) {
            $this->Flash->error('Password reset is not available while Firebase login is enabled.');

            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->findByNonce($nonce)->first();

        // If nonce cannot find the user, or nonce is expired, prompt for re-reset password
        if (!$user || $user->nonce_expiry < FrozenTime::now()) {
            $this->Flash->error('Your link is invalid or expired. Please try again.');
            return $this->redirect(['action' => 'forgetPassword']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Used a different validation set in Model/Table file to ensure both fields are filled
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'resetPassword']);

            // Also clear the nonce-related fields on successful password resets.
            // This ensures that the reset link can't be used a second time.
            $user->nonce = null;
            $user->nonce_expiry = null;

            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been successfully reset. Please login with new password. ');
                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('The password cannot be reset. Please try again.');
        }

        $this->set(compact('user'));
    }

    /**
     * Change Password method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePassword($id = null) {
        if ($this->usesFirestoreAuth()) {
            $repository = new NewLoveFirestoreRepository();
            $identity = $this->Authentication->getIdentity();
            $currentUserId = $identity ? (string)$identity->getIdentifier() : '';
            $targetUserId = $id !== null ? (string)$id : $currentUserId;

            if ($currentUserId === '' || $targetUserId === '' || $targetUserId !== $currentUserId) {
                $this->Flash->error('You can only change your own password.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
            }

            $user = $repository->userById($targetUserId);
            if ($user === null) {
                $this->Flash->error('The user could not be found.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
            }

            if ($this->request->is(['patch', 'post', 'put'])) {
                $errors = $this->validateFirestorePasswordChange($this->request->getData());

                if ($errors === []) {
                    try {
                        $repository->updateUserPassword($targetUserId, (string)$this->request->getData('password'));
                        $this->Flash->success('Your password has been successfully changed.');

                        return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error('The password could not be changed. Please try again.');
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
            // Used a different validation set in Model/Table file to ensure both fields are filled
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'resetPassword']);
            if ($this->Users->save($user)) {
                $this->Flash->success('The user has been saved.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'home']);
            }
            $this->Flash->error('The user could not be saved. Please, try again.');
        }
        $this->set(compact('user'));
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|void|null Redirect to location before authentication
     */
    public function login() {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        // if user passes authentication, grant access to the system
        if ($result && $result->isValid()) {
            // set a fallback location in case user logged in without triggering 'unauthenticatedRedirect'
            $fallbackLocation = ['controller' => '#', 'action' => '#'];

            // and redirect user to the location they're trying to access
            return $this->redirect($this->Authentication->getLoginRedirect() ?? $fallbackLocation);
        }

        // display error if user submitted their credentials but authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Email address and/or Password is incorrect. Please try again. ');
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function logout() {
        // We only need to log out a user when they're logged in
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Authentication->logout();

            $this->Flash->success('You have been logged out successfully. ');
        }

        // Otherwise just send them to the login page
        return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
    }

    private function usesFirestoreAuth(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function validateFirestoreRegistration(array $data): array
    {
        $errors = [];
        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName = trim((string)($data['last_name'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $password = (string)($data['password'] ?? '');
        $passwordConfirm = (string)($data['password_confirm'] ?? '');

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
        } elseif ((new NewLoveFirestoreRepository())->userExistsWithEmail($email)) {
            $errors[] = 'Email address already in use.';
        }

        if ($password === '') {
            $errors[] = 'Password cannot be empty.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password should be at least 8 characters long.';
        } elseif (strlen($password) > 64) {
            $errors[] = 'Password cannot exceed 64 characters.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Retyped password does not match.';
        }

        return $errors;
    }

    private function validateFirestorePasswordChange(array $data): array
    {
        $errors = [];
        $password = (string)($data['password'] ?? '');
        $passwordConfirm = (string)($data['password_confirm'] ?? '');

        if ($password === '') {
            $errors[] = 'Password cannot be empty.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password should be at least 8 characters long.';
        } elseif (strlen($password) > 64) {
            $errors[] = 'Password cannot exceed 64 characters.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Retyped password does not match.';
        }

        return $errors;
    }

}
