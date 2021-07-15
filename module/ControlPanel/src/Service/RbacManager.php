<?php

// ControlPanel/src\Service\AuthManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Authentication\AuthenticationService;
use ControlPanel\Model\Identity;

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
        'Viewer' => ['article.any.view', 'profile.own.manage',] ];
    
    private $permissions = ['user.manage', 'permission.manage', 'role.manage', 'profile.any.manage', 'profile.own.manage',];
    
    private $users = ['admin' => ['roles' => ['Admin',], 'permissions' => [], ],
                      'mario' => ['roles' => ['Editor', 'Author',], 'permissions' => [], ],
                      'shmario' => ['roles' => ['Viewer',], 'permissions' => [], ],];
    
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
    public function __construct($authService, $cache, $assertionManagers)
    {
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }
    
//    private function initRoles($userName = '')
//    {
//        $userRole = new Role($userName);
//        foreach($this->roles as $roleName => $permissions){
//            $userRole->addChild(new Role($roleName));
//            foreach($permissions as $permission) {
//                $userRole->addPermission($permission);
//            }
//        }
//        $this->rbac->addRole($userRole);
//    }
    private function initRoles()
    {
        foreach($this->roles as $roleName => $permissions){
            $role = new Role($roleName);
            foreach($permissions as $permission) {
                $role->addPermission($permission);
            }
            $this->rbac->addRole($role);
        }
    }
    
    private function initRoles2()
    {
        // Create role hierarchy
        $rbac = new Rbac();
        $this->rbac = $rbac;
        $editor = new Role('Editor');
        $editor->addPermission('post.edit');
        $rbac->addRole('Viewer', [$editor, new Role('Author')]);
        $rbac->addRole('Editor', [new Role('Administrator')]);
        $rbac->addRole('Author');
        $rbac->addRole('Administrator');

        // Assign permissions to the Viewer role.
        $rbac->getRole('Viewer')->addPermission('post.view');

        // Assign permissions to the Author role.
        $rbac->getRole('Author')->addPermission('post.own.edit');
        $rbac->getRole('Author')->addPermission('post.own.publish');

        // Assign permissions to the Editor role.
        //$rbac->getRole('Editor')->addPermission('post.edit');
        $rbac->getRole('Editor')->addPermission('post.publish');

        // Assign permissions to the Administrator role.
        $rbac->getRole('Administrator')->addPermission('post.delete');
    }

    /**
     * Initializes the RBAC container.
     * 
     * @param bool $forceCreate
     * @return void
     */
    public function init($forceCreate = false)
    {
//        $this->initRoles();
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

            $this->initRoles2();
            
//            $roles = $this->roles;//$this->entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
//            foreach ($roles as $r => $p) {
//
//                $role = new \Laminas\Permissions\Rbac\Role($r);
//                $roleName = $role->getName();
//
//                $parentRoleNames = [];
//                //foreach ($role->getParentRoles() as $parentRole) {
//                foreach ($role->getParents() as $parentRole) {
//                    $parentRoleNames[] = $parentRole->getName();
//                }
//
//                $rbac->addRole($roleName, $parentRoleNames);
//
//                foreach ($role->getPermissions() as $permission) {
//                    $rbac->getRole($roleName)->addPermission($permission->getName());
//                }
//            }

            // Save Rbac container to cache.
            $this->cache->setItem('rbac_container', $rbac);
        }
    }

}
