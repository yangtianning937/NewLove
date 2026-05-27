<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

 	$this->addBehavior('CanAuthenticate');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('first_name', 'First name should be a scalar value.')
            ->maxLength('first_name', 64, 'First name cannot exceed 64 characters.')
            ->requirePresence('first_name', 'create', 'First name is required.')
            ->notEmptyString('first_name', 'First name cannot be empty.');

        $validator
            ->scalar('last_name', 'Last name should be a scalar value.')
            ->maxLength('last_name', 64, 'Last name cannot exceed 64 characters.')
            ->requirePresence('last_name', 'create', 'Last name is required.')
            ->notEmptyString('last_name', 'Last name cannot be empty.');

        $validator
            ->email('email')
            ->requirePresence('email', 'create', 'Email is required.')
            ->notEmptyString('email', 'Email cannot be empty.')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'Email address already in use.']);

        $validator
            ->scalar('password', 'Password should be a scalar value.')
            ->maxLength('password', 64, 'Password cannot exceed 64 characters.')
            ->minLength('password', 8, 'Password should be at least 8 characters long.')
            ->requirePresence('password', 'create', 'Password is required.')
            ->notEmptyString('password', 'Password cannot be empty.');

        $validator
            ->scalar('nonce', 'Nonce should be a scalar value.')
            ->maxLength('nonce', 128, 'Nonce cannot exceed 128 characters.')
            ->allowEmptyString('nonce', 'Nonce can be left empty.');

        $validator
            ->dateTime('nonce_expiry')
            ->allowEmptyDateTime('nonce_expiry', 'Nonce expiry can be left empty.');

        return $validator;
    }


    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
