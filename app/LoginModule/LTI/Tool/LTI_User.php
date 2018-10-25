<?php

namespace App\LoginModule\LTI\Tool;

/**
 * Class to represent a tool consumer user
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.5.00
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class LTI_User {

/**
 * @var string User's first name.
 */
  public $firstname = '';
/**
 * @var string User's last name (surname or family name).
 */
  public $lastname = '';
/**
 * @var string User's fullname.
 */
  public $fullname = '';
/**
 * @var string User's email address.
 */
  public $email = '';
/**
 * @var array Roles for user.
 */
  public $roles = array();
/**
 * @var array Groups for user.
 */
  public $groups = array();
/**
 * @var string User's result sourcedid.
 */
  public $lti_result_sourcedid = NULL;
/**
 * @var object Date/time the record was created.
 */
  public $created = NULL;
/**
 * @var object Date/time the record was last updated.
 */
  public $updated = NULL;

/**
 * @var LTI_Resource_Link Resource link object.
 */
  private $resource_link = NULL;
/**
 * @var LTI_Context Resource link object.
 */
  private $context = NULL;
/**
 * @var string User ID value.
 */
  private $id = NULL;

/**
 * Class constructor.
 *
 * @param LTI_Resource_Link $resource_link Resource_Link object
 * @param string      $id      User ID value
 */
  public function __construct($resource_link, $id) {

    $this->initialise();
    $this->resource_link = $resource_link;
    $this->context = &$this->resource_link;
    $this->id = $id;
    $this->load();

  }

/**
 * Initialise the user.
 */
  public function initialise() {

    $this->firstname = '';
    $this->lastname = '';
    $this->fullname = '';
    $this->email = '';
    $this->roles = array();
    $this->groups = array();
    $this->lti_result_sourcedid = NULL;
    $this->created = NULL;
    $this->updated = NULL;

  }

/**
 * Load the user from the database.
 *
 * @return boolean True if the user object was successfully loaded
 */
  public function load() {

    $this->initialise();
    if (!is_null($this->resource_link)) {
      $this->resource_link->getConsumer()->getDataConnector()->User_load($this);
    }

  }

/**
 * Save the user to the database.
 *
 * @return boolean True if the user object was successfully saved
 */
  public function save() {

    if (!empty($this->lti_result_sourcedid) && !is_null($this->resource_link)) {
      $ok = $this->resource_link->getConsumer()->getDataConnector()->User_save($this);
    } else {
      $ok = TRUE;
    }

    return $ok;

  }

/**
 * Delete the user from the database.
 *
 * @return boolean True if the user object was successfully deleted
 */
  public function delete() {

    if (!is_null($this->resource_link)) {
      $ok = $this->resource_link->getConsumer()->getDataConnector()->User_delete($this);
    } else {
      $ok = TRUE;
    }

    return $ok;

  }

/**
 * Get resource link.
 *
 * @return LTI_Resource_Link Resource link object
 */
  public function getResourceLink() {

    return $this->resource_link;

  }

/**
 * Get context.
 *
 * @deprecated Use getResourceLink() instead
 * @see LTI_User::getResourceLink()
 *
 * @return LTI_Resource_Link Context object
 */
  public function getContext() {

    return $this->resource_link;

  }

/**
 * Get the user ID (which may be a compound of the tool consumer and resource link IDs).
 *
 * @param int $id_scope Scope to use for user ID (optional, default is null for consumer default setting)
 *
 * @return string User ID value
 */
  public function getId($id_scope = NULL) {

    if (empty($id_scope)) {
      if (!is_null($this->resource_link)) {
        $id_scope = $this->resource_link->getConsumer()->id_scope;
      } else {
        $id_scope = LTI_Tool_Provider::ID_SCOPE_ID_ONLY;
      }
    }
    switch ($id_scope) {
      case LTI_Tool_Provider::ID_SCOPE_GLOBAL:
        $id = $this->resource_link->getKey() . LTI_Tool_Provider::ID_SCOPE_SEPARATOR . $this->id;
        break;
      case LTI_Tool_Provider::ID_SCOPE_CONTEXT:
        $id = $this->resource_link->getKey();
        if ($this->resource_link->lti_context_id) {
          $id .= LTI_Tool_Provider::ID_SCOPE_SEPARATOR . $this->resource_link->lti_context_id;
        }
        $id .= LTI_Tool_Provider::ID_SCOPE_SEPARATOR . $this->id;
        break;
      case LTI_Tool_Provider::ID_SCOPE_RESOURCE:
        $id = $this->resource_link->getKey();
        if ($this->resource_link->lti_resource_id) {
          $id .= LTI_Tool_Provider::ID_SCOPE_SEPARATOR . $this->resource_link->lti_resource_id;
        }
        $id .= LTI_Tool_Provider::ID_SCOPE_SEPARATOR . $this->id;
        break;
      default:
        $id = $this->id;
        break;
    }

    return $id;

  }

/**
 * Set the user's name.
 *
 * @param string $firstname User's first name.
 * @param string $lastname User's last name.
 * @param string $fullname User's full name.
 */
  public function setNames($firstname, $lastname, $fullname) {

    $names = array(0 => '', 1 => '');
    if (!empty($fullname)) {
      $this->fullname = trim($fullname);
      $names = preg_split("/[\s]+/", $this->fullname, 2);
    }
    if (!empty($firstname)) {
      $this->firstname = trim($firstname);
      $names[0] = $this->firstname;
    } else if (!empty($names[0])) {
      $this->firstname = $names[0];
    } else {
      $this->firstname = 'User';
    }
    if (!empty($lastname)) {
      $this->lastname = trim($lastname);
      $names[1] = $this->lastname;
    } else if (!empty($names[1])) {
      $this->lastname = $names[1];
    } else {
      $this->lastname = $this->id;
    }
    if (empty($this->fullname)) {
      $this->fullname = "{$this->firstname} {$this->lastname}";
    }

  }

/**
 * Set the user's email address.
 *
 * @param string $email        Email address value
 * @param string $defaultEmail Value to use if no email is provided (optional, default is none)
 */
  public function setEmail($email, $defaultEmail = NULL) {

    if (!empty($email)) {
      $this->email = $email;
    } else if (!empty($defaultEmail)) {
      $this->email = $defaultEmail;
      if (substr($this->email, 0, 1) == '@') {
        $this->email = $this->getId() . $this->email;
      }
    } else {
      $this->email = '';
    }

  }

/**
 * Check if the user is an administrator (at any of the system, institution or context levels).
 *
 * @return boolean True if the user has a role of administrator
 */
  public function isAdmin() {

    return $this->hasRole('Administrator') || $this->hasRole('urn:lti:sysrole:ims/lis/SysAdmin') ||
           $this->hasRole('urn:lti:sysrole:ims/lis/Administrator') || $this->hasRole('urn:lti:instrole:ims/lis/Administrator');

  }

/**
 * Check if the user is staff.
 *
 * @return boolean True if the user has a role of instructor, contentdeveloper or teachingassistant
 */
  public function isStaff() {

    return ($this->hasRole('Instructor') || $this->hasRole('ContentDeveloper') || $this->hasRole('TeachingAssistant'));

  }

/**
 * Check if the user is a learner.
 *
 * @return boolean True if the user has a role of learner
 */
  public function isLearner() {

    return $this->hasRole('Learner');

  }

###
###  PRIVATE METHODS
###

/**
 * Check whether the user has a specified role name.
 *
 * @param string $role Name of role
 *
 * @return boolean True if the user has the specified role
 */
  private function hasRole($role) {

    if (substr($role, 0, 4) != 'urn:') {
      $role = 'urn:lti:role:ims/lis/' . $role;
    }

    return in_array($role, $this->roles);

  }

}