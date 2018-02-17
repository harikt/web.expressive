<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Exception\TypeMismatchException;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * The custom user provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminDmsUserProvider implements UserProvider
{
    /**
     * @var IAdminRepository
     */
    protected $repository;

    /**
     * @var IPasswordHasherFactory
     */
    protected $passwordHasherFactory;

    /**
     * DmsUserProvider constructor.
     *
     * @param IAdminRepository       $repository
     * @param IPasswordHasherFactory $passwordHasherFactory
     */
    public function __construct(IAdminRepository $repository, IPasswordHasherFactory $passwordHasherFactory)
    {
        $this->repository            = $repository;
        $this->passwordHasherFactory = $passwordHasherFactory;
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
        return $this->repository->tryGet((int)$id);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $id
     * @param string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($id, $token)
    {
        /**
         * @var LocalAdmin[] $admins
        */
        $admins = $this->repository->matching(
            $this->repository->criteria()
                ->whereInstanceOf(LocalAdmin::class)
                ->where(LocalAdmin::ID, '=', (int)$id)
        );

        foreach ($admins as $key => $admin) {
            if ($admin->getRememberToken() !== $token) {
                unset($admins[$key]);
            }
        }

        return reset($admins) ?: null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $admin
     * @param string                                     $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $admin, $token)
    {
        if (!($admin instanceof LocalAdmin)) {
            return;
        }

        $admin->setRememberToken($token);
        $this->repository->save($admin);
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

        $admins = $this->repository->matching($criteria);

        return reset($admins) ?: null;
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
            if (strpos($column, 'password') === false) {
                $criteria->where($column, '=', $value);
            }
        }

        return $criteria;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $admin
     * @param array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $admin, array $credentials) : bool
    {
        $admin = $this->validateLocalAdmin($admin);

        $passwordHasher = $this->passwordHasherFactory->buildFor($admin->getPassword());

        return $passwordHasher->verify($credentials['password'], $admin->getPassword());
    }

    /**
     * @param Authenticatable $admin
     *
     * @return LocalAdmin|Authenticatable
     * @throws TypeMismatchException
     */
    private function validateLocalAdmin(Authenticatable $admin)
    {
        if (!($admin instanceof LocalAdmin)) {
            throw TypeMismatchException::format('Expecting instance of %s, %s given', LocalAdmin::class, get_class($admin));
        }

        return $admin;
    }
}
