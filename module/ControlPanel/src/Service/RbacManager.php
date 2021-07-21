<?php

// ControlPanel/src\Service\AuthManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role;
use ControlPanel\Model\Entity\Role as CPRole;

/**
 * Description of RbacManager
 *
 * @author alex
 */
class RbacManager
{

    /**
     * File system cache.
     * @var Laminas\Cache\Storage\StorageInterface
     */
    private $cache;

    /**
     * RBAC service.
     * @var Laminas\Permissions\Rbac\Rbac
     */
    private $rbac;

    /**
     * Auth service.
     * @var Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Assertion managers.
     * @var array
     */
    private $assertionManagers = [];

    /**
     * Temp variable to be replaced later on after roles are loaded from a repository
     *
     * @var array
     */
    private $roles = ['Admin' => ['user.manage', 'permission.manage', 'role.manage', 'profile.any.manage', 'profile.own.manage',],
        'Editor' => ['profile.own.manage',], 'Author' => ['article.own.create', 'article.own.update', 'article.any.view',],
        'Viewer' => ['article.any.view', 'profile.own.manage',]];
    private $permissions = ['user.manage', 'permission.manage', 'role.manage', 'profile.any.manage', 'profile.own.manage',];
    private $users = ['admin' => ['roles' => ['Admin',], 'permissions' => [],],
        'mario' => ['roles' => ['Editor', 'Author',], 'permissions' => [],],
        'shmario' => ['roles' => ['Viewer',], 'permissions' => [],],];
    private $defaultPermissions = [
        'user.manage' => 'Manage users',
        'permission.manage' => 'Manage permissions',
        'role.manage' => 'Manage roles',
        'profile.any.view' => 'View anyone\'s profile',
        'profile.own.view' => 'View own profile',
    ];

    /**
     * Constructs the service.
     *
     * @param Laminas\Authentication\AuthenticationService $authService
     * @param type $cache
     * @param array $assertionManagers
     */
    public function __construct($entityManager, $authService, $cache, $assertionManagers)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }

//    private function initRoles()
//    {
//        foreach($this->roles as $roleName => $permissions){
//            $role = new Role($roleName);
//            foreach($permissions as $permission) {
//                $role->addPermission($permission);
//            }
//            $this->rbac->addRole($role);
//        }
//    }
//

    /**
     * Initializes the RBAC container.
     *
     * @param bool $forceCreate
     * @return void
     */
    public function init($forceCreate = false)
    {
        if ($this->rbac != null && !$forceCreate) {
            // Already initialized; do nothing.
            return;
        }

        // If user wants us to reinit RBAC container, clear cache now.
        if ($forceCreate) {
            $this->cache->removeItem('rbac_container');
        }

        // Try to load Rbac container from cache.
        $result = false;
        $this->rbac = $this->cache->getItem('rbac_container', $result);
        if (!$result) {
            // Create Rbac container.
            $rbac = new Rbac();
            $this->rbac = $rbac;

            // Construct role hierarchy by loading roles and permissions from database.

            $rbac->setCreateMissingRoles(true);

            $repo = $this->entityManager->getRepository(CPRole::class);
            $roles = $repo->findAll([], ['id' => 'ASC'])->toArray();

            $parents = [];
            foreach ($roles as $role) {
                $roleName = $role['name'];
                // get parents
                $parents = \Application\Helper\ArrayHelper::getParents(['id' => $role['id'], 'parent_role_id' => $role['parent_role_id']], $roles, [], 'id', 'parent_role_id');
                $parentRoleNames = [];
                foreach ($parents as $parentId) {
                    $parentRole = $repo->find(['id' => $parentId]);
                    $parentRoleNames[] = $parentRole->getName();
                }

                $rolePermissions = $repo->getPermissions($role['id']);

                $rbac->addRole($roleName, $parentRoleNames);

                foreach ($rolePermissions as $permission) {
                    $rbac->getRole($roleName)->addPermission($permission->getPermissionName());
                }
            }
//            $this->isGranted(null, []);
            //$this->isGranted(null, 'general');
            // Save Rbac container to cache.
            $this->cache->setItem('rbac_container', $rbac);
        }
    }

    /**
     * Checks whether the given user has permission.
     * @param User|null $user
     * @param string $permission
     * @param array|null $params
     */
    public function isGranted($user, $permission, $params = null)
    {
        if ($this->rbac == null) {
            $this->init();
        }
        
//        echo 'admin: delete.personal = '. $this->rbac->isGranted('admin', 'delete.personal').'<br/>';
//        echo 'editor: view.profile = '. $this->rbac->isGranted('editor', 'view.profile').'<br/>';
//        echo 'supervisor: edit.profile = '. $this->rbac->isGranted('supervisor', 'edit.profile').'<br/>';
//        echo 'guest: view.profile = '. $this->rbac->isGranted('guest', 'view.profile').'<br/>';
//        echo 'guest: general = '. $this->rbac->isGranted('guest', 'general').'<br/>';
//        
//        
//        exit;
        

        if ($user == null) {

            $identity = $this->authService->getIdentity();
            if ($identity == null) {
                return false;
            }

            $user = $this->entityManager->getRepository(User::class)
                    ->findOneByEmail($identity);
            if ($user == null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                throw new \Exception('There is no user with such identity');
            }
        }

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role->getName(), $permission)) {

                if ($params == null) {
                    return true;
                }

                foreach ($this->assertionManagers as $assertionManager) {
                    if ($assertionManager->assert($this->rbac, $permission, $params)) {
                        return true;
                    }
                }
            }

            // Since we are pulling the user from the database again the init() function above is overridden?
            // we don't seem to be taking into account the parent roles without the following code
            $parentRoles = $role->getParentRoles();
            foreach ($parentRoles as $parentRole) {
                if ($this->rbac->isGranted($parentRole->getName(), $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

}
