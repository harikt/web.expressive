<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Exception\TypeMismatchException;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Core\Persistence\IRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Hashing\BcryptHasher;

/**
 * The generic user provider class for DMS.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GenericDmsUserProvider implements UserProviderInterface
{
    /**
     * @var \Dms\Core\Persistence\Db\Mapping\IEntityMapper
     */
    protected $mapper;

    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var callable
     */
    protected $wrapperCallback;

    /**
     * @var callable
     */
    protected $unwrapperCallback;

    /**
     * UserProvider constructor.
     *
     * @param IOrm        $orm
     * @param IConnection $connection
     * @param array       $config
     *
     * @throws InvalidArgumentException
     */
    public function __construct(IOrm $orm, IConnection $connection, array $config)
    {
        foreach (['class', 'password', 'remember_token'] as $parameter) {
            if (empty($config[$parameter])) {
                throw InvalidArgumentException::format('config/auth.php is missing the the \'%s\' parameter', $parameter);
            }
        }

        $this->mapper     = $orm->getEntityMapper($config['class']);
        $this->repository = new DbRepository($connection, $this->mapper);
        $this->config     = $config;

        $this->wrapperCallback = $config['wrapper'] ?? function (Authenticatable $authenticatable) {
            return $authenticatable;
        };

        $this->unwrapperCallback = $config['unwrapper'] ?? function (Authenticatable $authenticatable) {
            return $authenticatable;
        };
    }

    /**
     * @param $entity
     *
     * @return Authenticatable
     */
    protected function wrapUser($entity): Authenticatable
    {
        return call_user_func($this->wrapperCallback, $entity);
    }

    /**
     * @param Authenticatable $authenticatable
     *
     * @return mixed
     */
    protected function unwrapUser(Authenticatable $authenticatable)
    {
        return call_user_func($this->unwrapperCallback, $authenticatable);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($id)
    {
        return $this->wrapUser($this->repository->tryGet((int)$id));
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $users = $this->repository->matching(
            $this->repository->criteria()
                ->where(Entity::ID, '=', (int)$identifier)
                ->where($this->config['remember_token'], '=', $token)
        );

        return $users
            ? $this->wrapUser(reset($users))
            : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $user = $this->unwrapUser($user);
        $user = $this->validateUser($user);

        $this->repository->save($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $criteria = $this->criteriaFromCredentialsArray($credentials);

        $users = $this->repository->matching($criteria);

        return $users
            ? $this->wrapUser(reset($users))
            : null;
    }

    /**
     * @param array $credentials
     *
     * @return \Dms\Core\Model\Criteria\Criteria
     */
    private function criteriaFromCredentialsArray(array $credentials)
    {
        $criteria = $this->repository->criteria();

        foreach ($credentials as $column => $value) {
            if (strpos($column, 'api_token') !== false) {
                $criteria->where('apiToken', '=', $value);
            } elseif (strpos($column, 'email') !== false) {
                $criteria->where($column, '=', new EmailAddress($value));
            } elseif (strpos($column, 'password') === false) {
                $criteria->where($column, '=', $value);
            }
        }

        return $criteria;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $user = $this->unwrapUser($user);

        $user = $this->validateUser($user);

        /**
         * @var BcryptHasher $app
        */
        $app = app('hash');

        return $app->check($credentials['password'], $user->{$this->config['password']});
    }

    /**
     * @param object $user
     *
     * @return object
     * @throws TypeMismatchException
     */
    private function validateUser($user)
    {
        $userClass = $this->repository->getObjectType();

        if (!($user instanceof $userClass)) {
            throw TypeMismatchException::format('Expecting instance of %s, %s given', $userClass, get_class($user));
        }

        return $user;
    }
}
