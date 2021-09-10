<?php

// ControlPanel/src\Service\RbacManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;
//use Laminas\Permissions\Rbac\Role;
use ControlPanel\Model\Entity\Role as CPRole;
use ControlPanel\Model\Entity\User;

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
    private $entityManager;
    private $userManager;

    /**
     * Constructs the service.
     *
     * @param Laminas\Authentication\AuthenticationService $authService
     * @param type $cache
     * @param array $assertionManagers
     */
    public function __construct($entityManager, $authService, $cache, $assertionManagers, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
        $this->userManager = $userManager;

        $this->entityManager->initRepository(CPRole::class);
    }

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
            $repoHierarchy = $this->entityManager->getRepository(\ControlPanel\Model\Entity\RoleHierarchy::class);
            $hierarchy = $repoHierarchy->findAll([])->toArray();
            $roles = $repo->findAll([], ['id' => 'ASC'])->toArray();

            $parents = [];
            foreach ($roles as $role) {
                // We try to comment this out
                // $roleName = $role['name'];
                // And use $role['role'];
                $roleName = $role['role'];

                // get parents
                //$parents = \Application\Helper\ArrayHelper::getParents(['id' => $role['id'], 'parent_role_id' => $role['parent_role_id']], $roles, [], 'id', 'parent_role_id');
                $parents = \Application\Helper\ArrayHelper::getParents(
                                ['child_role_id' => $role['id'], 'parent_role_id' => $role['parent_role_id']],
                                $hierarchy/* $roles */,
                                [],
                                'child_role_id',
                                'parent_role_id'
                );
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

        if ($user == null) {

            $identity = $this->authService->getIdentity();
            if ($identity == null) {
                return false;
            }

            $user = $this->userManager->findOne(['provider_id' => $identity['provider_id'], 'login' => $identity['login'],]);

//            $u = $user;
//
//            $u['roles'] = implode(',', $user['roles']);
//
//            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//
//            $userObject = $hydrator->hydrate($u, new \ControlPanel\Model\Entity\User());

            if ($user == null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                throw new \Exception('There is no user with such identity');
            }
        }

//        $roles = $user->getRoles();

        $roles = $user['roles'];

//        $this->entityManager->initRepository(CPRole::class);

        foreach ($roles as $role) {
            // if ($this->rbac->isGranted($role->getName(), $permission)) {
            if ($this->rbac->isGranted($role, $permission)) {

                if (null == $params) {
                    //$params['user']['login'] = 'Banzaii';
                    return true;
                }
                //Otherwise we need to conduct additional verification
                foreach ($this->assertionManagers as $assertionManager) {
                    if (!$assertionManager->assert($this->rbac, $permission, $params)) {
                        //return true;
                        return false;
                    }
                }
            }

            $roleObject = CPRole::findAll(['columns' => ['*'], 'where' => ['role' => $role]])->current();
            $parentRoles = $roleObject->getParentRoles();

            // Since we are pulling the user from the database again the init() function above is overridden?
            // we don't seem to be taking into account the parent roles without the following code
            //$parentRoles = $role->getParentRoles();
            foreach ($parentRoles as $parentRole) {
                if ($this->rbac->isGranted($parentRole->getName(), $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

}

//    private function initRoles2()
//    {
//        $roleHierarchy = $this->entityManager->getRepository(RoleHierarchy::class)->findAll([], ['id' => 'ASC']);
//        foreach($roleHierarchy as $element) {
//            $terminal = $element->getTerminal();
//            $elements[] = ['id' => $element->getId(), 'parent_id' => 1 == $terminal ? 0 : $element->getParentRoleId()];
//        }
//        echo '<pre>';
//        print_r($elements);
//        echo '</pre>';
//
//        $tree = \Application\Helper\ArrayHelper::buildTree($elements, 0);
//        echo '<pre>';
//        print_r($tree);
//        echo '</pre>';
//
//        $parents = \Application\Helper\ArrayHelper::getParents(['id' => 6, 'parent_id' => 5], $elements);
//        echo '<pre>';
//        print_r($parents);
//        echo '</pre>';
//        exit;
//        // Create role hierarchy
//        $rbac = new Rbac();
//        $this->rbac = $rbac;
//        $editor = new Role('Editor');
//        $editor->addPermission('post.edit');
//        $rbac->addRole('Viewer', [$editor, new Role('Author')]);
//        $rbac->addRole('Editor', [new Role('Administrator')]);
//        $rbac->addRole('Author');
//        $rbac->addRole('Administrator');
//
//        // Assign permissions to the Viewer role.
//        $rbac->getRole('Viewer')->addPermission('post.view');
//
//        // Assign permissions to the Author role.
//        $rbac->getRole('Author')->addPermission('post.own.edit');
//        $rbac->getRole('Author')->addPermission('post.own.publish');
//
//        // Assign permissions to the Editor role.
//        //$rbac->getRole('Editor')->addPermission('post.edit');
//        $rbac->getRole('Editor')->addPermission('post.publish');
//
//        // Assign permissions to the Administrator role.
//        $rbac->getRole('Administrator')->addPermission('post.delete');
//    }
//        echo 'admin: administrator = '. $this->rbac->isGranted('administrator', 'administrator').'<br/>';
//        echo 'developer: developer = '. $this->rbac->isGranted('developer', 'developer').'<br/>';
//        echo 'brand_manager: brand.manager = '. $this->rbac->isGranted('brand_manager', 'brand_manager').'<br/>';
//        echo 'store_manager: store.manager = '. $this->rbac->isGranted('store_manager', 'store.manager').'<br/>';
//        echo 'guest: guest = '. $this->rbac->isGranted('guest', 'guest1').'<br/>';
//
//
//        exit;
//        echo 'admin: delete.personal = '. $this->rbac->isGranted('admin', 'delete.personal').'<br/>';
//        echo 'editor: view.profile = '. $this->rbac->isGranted('editor', 'view.profile').'<br/>';
//        echo 'supervisor: edit.profile = '. $this->rbac->isGranted('supervisor', 'edit.profile').'<br/>';
//        echo 'guest: view.profile = '. $this->rbac->isGranted('guest', 'view.profile').'<br/>';
//        echo 'guest: general = '. $this->rbac->isGranted('guest', 'general').'<br/>';
//
//
//        exit;







    /**
     * Temp variable to be replaced later on after roles are loaded from a repository
     *
     * @var array
     */
//    private $roles = ['Admin' => ['user.manage', 'permission.manage', 'role.manage', 'profile.any.manage', 'profile.own.manage',],
//        'Editor' => ['profile.own.manage',], 'Author' => ['article.own.create', 'article.own.update', 'article.any.view',],
//        'Viewer' => ['article.any.view', 'profile.own.manage',]];
//    private $permissions = ['user.manage', 'permission.manage', 'role.manage', 'profile.any.manage', 'profile.own.manage',];
//    private $users = ['admin' => ['roles' => ['Admin',], 'permissions' => [],],
//        'mario' => ['roles' => ['Editor', 'Author',], 'permissions' => [],],
//        'shmario' => ['roles' => ['Viewer',], 'permissions' => [],],];
//    private $defaultPermissions = [
//        'user.manage' => 'Manage users',
//        'permission.manage' => 'Manage permissions',
//        'role.manage' => 'Manage roles',
//        'profile.any.view' => 'View anyone\'s profile',
//        'profile.own.view' => 'View own profile',
//    ];
//

















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

