<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Auth/ActionData.php';
require_once 'Auth/SessionData.php';
require_once 'Auth/UserActionData.php';

class Auth {

    private $sessionObj;
    private $actionObj;
    private $userActionObj;

    function __construct() {
        $this->sessionObj = new SessionData();
        $this->actionObj = new ActionData();
        $this->userActionObj = new UserActionData();
    }

    public function tryLogin() {

    }

    public function logout() {

    }

    public function isLoggedIn() {

    }

    public function getLoggedInUserId() {

    }


    public function checkAccess($controller, $action) {
        $hasAccess = false;

        $action = $this->actionObj->getActionByControllerAndAction($controller, $action);
        if($action == null) {
            return $hasAccess; //action not found, so no access
        }

        if($action['isAnonymous'] === TRUE) {
            $hasAccess = TRUE;
            return $hasAccess;  //action is anonymous, so access granted
        } else {
            $allowGet = $action['allowGet'];
            $allowPost = $action['allowPost'];
            if(!$allowPost && $_SERVER['REQUEST_METHOD'] === 'POST') {
                return $hasAccess;  //no access
            }
            elseif(!$allowGet && $_SERVER['REQUEST_METHOD'] === 'GET') {
                return $hasAccess;  //no access
            }
            //now check if user is logged in
            $isLoggedIn = $this->sessionObj->isLoggedIn();
            if(!$isLoggedIn) {
                return $hasAccess;  //no access for not logged in user
            }
            //now check if user has really access to this action
            $appUserId = $this->sessionObj->getLoggedInUserId();
            $actionId = $action['id'];
            $hasAccessToAction = $this->userActionObj->checkUserAccessToAction($appUserId, $actionId);
            $hasAccess = $hasAccessToAction;
        }
        return $hasAccess;
    }
}