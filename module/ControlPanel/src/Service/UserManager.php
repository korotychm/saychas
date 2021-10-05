<?php

// ControlPanel\src\Service\UserManager.php

namespace ControlPanel\Service;

use Laminas\Hydrator\ClassMethodsHydrator;
use ControlPanel\Service\CurlRequestManager;
use ControlPanel\Model\Entity\User;

/**
 * Description of UserManager
 *
 * @author alex
 */
class UserManager
{

    public const MAX_PASSWORD_LENGTH = 100;

    protected $config;
    protected $curlRequestManager;

    public function __construct($config, CurlRequestManager $curlRequestManager)
    {
        $this->config = $config;
        $this->curlRequestManager = $curlRequestManager;
    }

    /**
     * Login
     *
     *   Example parameter to login a user:
     *   $content structure to be sent
     *   $content = [
     *       "provider_id" => "00002",
     *       "login" => "Petya 3",
     *       "password" => "ODx7hdsGXJ",
     *       "roles" => ["000000002", "000000003",],
     *   ];
     * @param array $content
     * @return array
     * @throws \Exception
     */
    public function loginUser($content)
    {
        // Development
        // return $this->curlRequestManager->getFakeAnswer('lk_provider_login');

        if (strlen($content['password']) > self::MAX_PASSWORD_LENGTH) {
            throw new \Exception('The password must be less or equal ' . self::MAX_PASSWORD_LENGTH);
        }

        $url = $this->config['parameters']['1c_provider_links']['lk_provider_login'];
        $answer = $this->curlRequestManager->sendCurlRequest($url, $content);
        if (!$answer['result']) {
            return $answer;
        }
        $answer['roles'] = implode(',', $answer['roles']);
        if (null == $answer['roles']) {
            throw new \Exception('A provider must have a role; null roles are not leagal');
        }
        return $answer;
    }

    /**
     * Create a user
     *
     *   Example parameter to create a user:
     *   $content structure to be sent
     *   $content = [
     *       "provider_id" => "00002",
     *       "login" => "Vasya",
     *       "password" => "ODx7hdsGXJ",
     *       "access_is_allowed" => true,
     *   ];
     * @param array $content
     * @return array
     * @throws \Exception
     */
    public function createUser($content)
    {
        // Development
        // return $this->curlRequestManager->getFakeAnswer('lk_create_user');

        if (strlen($content['password']) > self::MAX_PASSWORD_LENGTH) {
            throw new \Exception('The password must be less or equal ' . self::MAX_PASSWORD_LENGTH);
        }

        $url = $this->config['parameters']['1c_provider_links']['lk_create_user'];
        $answer = $this->curlRequestManager->sendCurlRequest($url, $content);
        return $answer;
    }

    /**
     * Update a user
     *
     *   Example parameter to update a user:
     *   $content structure to be sent
     *   $content = [
     *       "provider_id" => "00002",
     *       "login" => "Petya",
     *       "password" => "ODx7hdsGXJ",
     *       "access_is_allowed" => true,
     *       "roles" => [
     *              "000000001",
     *              "000000002",
     *       ],
     *   ];
     * @param array $content
     * @return array
     * @throws \Exception
     */
    public function updateUser($content)
    {
        // Development
        // return $this->curlRequestManager->getFakeAnswer('lk_update_user');

        if (strlen($content['password']) > self::MAX_PASSWORD_LENGTH) {
            throw new \Exception('The password must be less or equal ' . self::MAX_PASSWORD_LENGTH);
        }

        $url = $this->config['parameters']['1c_provider_links']['lk_update_user'];
        $answer = $this->curlRequestManager->sendCurlRequest($url, $content);

        return $answer;
    }

    /**
     * Get all users for specified provider
     *
     *   Example parameter to update a user:
     *   $content structure to be sent
     *   $content = [
     *       "provider_id" => "00002",
     *   ];
     * @param type $content
     * @return array
     * @throws \Exception
     */
    public function getAllUsers($content)
    {
        // Development
        // return $this->curlRequestManager->getFakeAnswer('lk_get_all_users');

        if (strlen($content['password']) > self::MAX_PASSWORD_LENGTH) {
            throw new \Exception('The password must be less or equal ' . self::MAX_PASSWORD_LENGTH);
        }

        $url = $this->config['parameters']['1c_provider_links']['lk_get_all_users'];
        $answer = $this->curlRequestManager->sendCurlRequest($url, $content);

        return $answer;
    }

    /**
     * $content = [
     *      'provider_id' => '00002',
     *      'login' => 'Banzaii',
     * ],
     *
     * @param array $content
     */
    public function findOne($content)
    {
        $users = $this->getAllUsers($content)['info'];
        foreach ($users as $user) {
            if ($content['login'] === $user['login']) {
                return $user;
            }
        }
        return null;
    }

    public function findOneUserObject($content)
    {
        $user = $this->findOne($content);
        if (null == $user) {
            return null;
        }
        $user['roles'] = implode(',', $user['roles']);
        $hydrator = new ClassMethodsHydrator();
        return $hydrator->hydrate($user, new User());
    }
    
    public function confirmOffer($content)
    {
        $url = $this->config['parameters']['1c_provider_links']['lk_confirm_offer'];
        $answer = $this->curlRequestManager->sendCurlRequest($url, $content);
        
        return $answer;
    }

}
