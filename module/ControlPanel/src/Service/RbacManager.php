<?php

// ControlPanel/src\Service\AuthManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Authentication\AuthenticationService;
use ControlPanel\Model\Identity;
use ControlPanel\Model\Entity\Role as CPRole;
use ControlPanel\Model\Entity\RoleHierarchy;
use ControlPanel\Model\Repository\RoleRepository;
use ControlPanel\Model\Repository\RoleHierarchyRepository;
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
    public function __construct($entityManager, $authService, $cache, $assertionManagers)
    {
        $this->entityManager = $entityManager;
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
//        $elements[] = ['id' => 1, 'parent_id' => 0];
//        $elements[] = ['id' => 2, 'parent_id' => 1];
//        $elements[] = ['id' => 3, 'parent_id' => 1];
//        $elements[] = ['id' => 4, 'parent_id' => 2];
//        $elements[] = ['id' => 5, 'parent_id' => 4];
//        $elements[] = ['id' => 6, 'parent_id' => 5];

//        $elements[] = ['id' => 1, 'parent_id' => 1, 'terminal' => 1];
//        $elements[] = ['id' => 2, 'parent_id' => 1, 'terminal' => 0];
//        $elements[] = ['id' => 3, 'parent_id' => 1, 'terminal' => 0];
//        $elements[] = ['id' => 4, 'parent_id' => 2, 'terminal' => 0];
//        $elements[] = ['id' => 5, 'parent_id' => 4, 'terminal' => 0];
//        $elements[] = ['id' => 6, 'parent_id' => 5, 'terminal' => 0];
//        $elements[] = ['id' => 7, 'parent_id' => 5, 'terminal' => 0];
        
        
        
        $roleHierarchy = $this->entityManager->getRepository(RoleHierarchy::class)->findAll([], ['id' => 'ASC']);
        foreach($roleHierarchy as $element) {
            $terminal = $element->getTerminal();
            $elements[] = ['id' => $element->getId(), 'parent_id' => 1 == $terminal ? 0 : $element->getParentRoleId()];
        }        

        $tree = \Application\Helper\ArrayHelper::buildTree($elements, 0);
        echo '<pre>';
        print_r($tree);
        echo '</pre>';
        
        $parents = \Application\Helper\ArrayHelper::getParents(['id' => 7, 'parent_id' => 3], $elements);
        echo '<pre>';
        print_r($parents);
        echo '</pre>';
        exit;
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
            
            // $roles = $this->roles;
            // $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
            $roles = $this->entityManager->getRepository(CPRole::class)->findAll([], ['id' => 'ASC']);
//            foreach ($roles as $r1) {
//                print_r($r1);
//            }
//            foreach ($roles as $r => $p) {
            foreach ($roles as $r) {
                  $role = $r;
//                $role = new \Laminas\Permissions\Rbac\Role($r->getName());
                $roleName = $role->getName();

                $parentRoleNames = [];
                $role->getParentRoles();
                foreach ($role->receiveParantRoles() as $parentRole) {
//                foreach ($role->getParents() as $parentRole) {
                    $parentRoleNames[] = $parentRole->getName();
                }
//
//                $rbac->addRole($roleName, $parentRoleNames);
//
//                foreach ($role->getPermissions() as $permission) {
//                    $rbac->getRole($roleName)->addPermission($permission->getName());
//                }
            }

            // Save Rbac container to cache.
            $this->cache->setItem('rbac_container', $rbac);
        }
    }

}
