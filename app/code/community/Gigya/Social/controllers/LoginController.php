<?php
if (defined('COMPILER_INCLUDE_PATH')) {
  include_once 'Gigya_Social_sdk_GSSDK.php';
}
else {
  include_once __DIR__ . '/../sdk/GSSDK.php';
}

require_once('Mage/Customer/controllers/AccountController.php');

/**
 * Class Gigya_Social_IndexController
 *
 * @author
 */
class Gigya_Social_LoginController extends Mage_Customer_AccountController {

  private $helper;
  private $userMode;
  private $gigyaData;

  public function indexAction() {
    $this->loadLayout();
    $this->renderLayout();
  }

  /**
   * Action predispatch
   *
   * Check customer authentication for some actions
   */
  public function preDispatch() {
    // a brute-force protection here would be nice

    parent::preDispatch();

    if (!$this->getRequest()->isDispatched()) {
      return;
    }

    $action      = $this->getRequest()->getActionName();
    $openActions = array(
      'create',
      'login',
      'logout',
      'loginPost',
      'logoutsuccess',
      'forgotpassword',
      'forgotpasswordpost',
      'resetpassword',
      'resetpasswordpost',
      'confirm',
      'confirmation'
    );
    $pattern     = '/^(' . implode('|', $openActions) . ')/i';

    if (!preg_match($pattern, $action)) {
      if (!$this->_getSession()->authenticate($this)) {
        $this->setFlag('', 'no-dispatch', TRUE);
      }
    }
    else {
      $this->_getSession()->setNoReferer(TRUE);
    }
  }

  /*
   * Login Gateway
   * Check which login mode is enabled
   * activate social Login or Raas accordingly
   */
  public function loginAction() {
    $this->helper   = Mage::helper("Gigya_Social");
    $this->userMode = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes');
    $session        = $this->_getSession();
    $req            = $this->getRequest()->getPost('json');
    $post           = json_decode($req, TRUE);
    $this->getResponse()->setHeader('Content-type', 'application/json');

    if (!empty($post)) {
      if ($this->userMode === 'social') {
        $this->_socialLoginRegister($session, $post);
        $this->gigyaData = $post;
      }
      elseif ($this->userMode === 'raas') {
        $this->_raasLoginRegister($session, $post);
      }
      else {
        $this->_getSession()->addError($this->__('Gigya login is disabled'));
      }
    }
    else {
      Mage::log('No data arrived in post ' . __FILE__ . ' ' . __LINE__);
    }

  }

  /*
   * Handle Raas login process:
   * at any stage if an error occurs and login/registration fails, create js response message and skip to func end
   * test UID sig validation
   * validate user authenticity
   * get account info from gigya
   * check if account exists in magento
   *  if not create account, create login and reload
   *  if yes create login and reload
   */
  protected function _raasLoginRegister($session, $post) {
    $valid = FALSE;
    if (isset($post['UIDSignature'])) {
      $valid = Mage::helper('Gigya_Social')
        ->validateGigyaUid($post['UID'], $post['UIDSignature'],
          $post['signatureTimestamp']);
    }
    else {
      Mage::log('Gigya UIDSignature missing ' . __FILE__ . ' ' . __LINE__);
    }
    ////
    if ($valid) {
      $accountInfo = $this->_raasAccountInfo($post['UID']); // account info from gigya
      if ($accountInfo) { // if $accountInfo is false skip this and continue with response to ajax
        $this->gigyaData = $accountInfo;
        $cust_session    = Mage::getSingleton('customer/session');
        $email           = $this->gigyaData['profile']['email'];
        $cust
                         = $this->_customerExists($email); // if customer exists in Magento receive obj, else false
        // customer email exists login flow
        if ($cust != FALSE) {
          // create event hook
          Mage::dispatchEvent('gigya_raas_pre_login', array(
            'customer'  => $cust,
            'gigyaData' => $this->gigyaData
          ));
          $cust->firstname = $accountInfo['profile']['firstName'];
          $cust->lastname  = $accountInfo['profile']['lastName'];
          $cust->save();  // save customer details in magento
          $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($this->gigyaData);
          if ($updater->isMapped()) {
            $updater->updateMagentoAccount($cust);
          }
          $cust_session->setCustomerAsLoggedIn($cust);
          Mage::dispatchEvent('gigya_raas_post_login', array(
            'customer_session' => $cust_session,
            'gigyaData'        => $this->gigyaData
          ));
          $url = Mage::getUrl('*/*/*',
            array('_current' => TRUE)); // url for reload after creating logged in user
          $cust_session->setData('gigyaAccount',
            $accountInfo); // add all gigya accountinfo to customer session
          $res = array(
            'result'   => 'login',
            'redirect' => $url
          );
          $this->getResponse()->setBody(Mage::helper('core')
            ->jsonEncode($res)); // js will create the redirect after login
        }
        else {
          // create a user in magento
          $firstName = $accountInfo['profile']['firstName'] ? $accountInfo['profile']['firstName']
            : $accountInfo['profile']['nickname'];
          $lastName  = $accountInfo['profile']['lastName'] ? $accountInfo['profile']['lastName']
            : $accountInfo['profile']['nickname'];
          $this->_createCustomer($email, $firstName, $lastName, $accountInfo);
        }
      }
    }
    else {
      $res = array(
        'result'       => 'error',
        "errorMessage" => "User not valid"
      );
      $this->getResponse()->setBody(Mage::helper('core')
        ->jsonEncode($res)); // js will create the redirect after login
    }
  }

  /*
  * get Raas account info from gigya server
  * @param int id
  * $return mixed obj/false $accountInfo
  */
  protected function _raasAccountInfo($uid) {
    $accountInfo = $this->helper->utils->getAccount($uid); // (utils is set to GigyaCMS.php)
    // getAccount in gigyaCMS is calling call().
    // if call fails, it Checks if retry flag is set, if not, Retry once and set one retry flag.
    // if it fails it returns error
    if (is_numeric($accountInfo)) {
      // js should log out from gigya
      $res = array(
        'result'  => 'message',
        'message' => "Oops! Something went wrong during your login/registration process. Please try to login/register again."
      );
      $this->getResponse()->setBody(Mage::helper('core')
        ->jsonEncode($res)); // *js should log out from gigya
      Mage::log('Could not retrieve site account infoError ' . __FILE__ . ' ' . __LINE__);

      return FALSE;
    }
    else {
      /** @var stdClass $accountObject
       * we cast the array to object so it would be passed by reference and data
       * could be added to it.
       */
      $accountObject = (object) $accountInfo;
      Mage::dispatchEvent("gigya_post_account_fetch", array("gigyaAccount" => $accountObject));
      return (array) $accountObject;
    }
  }

  protected function _socialLoginRegister($session, $post) {
    // validate user signature authenticity
    $valid = Mage::helper('Gigya_Social')
      ->validateGigyaUid($post['UID'], $post['UIDSignature'],
        $post['signatureTimestamp']);
    if ($valid == TRUE) {
      // check if user exists in magento
      // social is using 'UID' as common id with magento (while raas is using email)
      // (on first Gigya reg. Gigya creates temp UID. after site registration, the UID in gigya is updated to site UID and isSiteUID is set to true)
      if ($post['isSiteUID'] && is_numeric($post['UID'])) {
        $this->_doSocialLogin($post);
      }
      else { // no siteID.
        $this->_doSocialRegistration($post);
      }
    }
    else {
      Mage::log('User sig not valid ' . __FILE__ . ' ' . __LINE__);
    }
  }

  /*
   * Register Gigya user as new site user (Gigya user does not exist in Magento).
   * 2 options: email exists (in post) or not.
   *   no email (e.g Twitter user) - flow to create form to get missing email and resubmit process -
   * (can be moved to FE JS)
   *
   * @param array $post
   * $return bool $registered
   */
  protected function _doSocialRegistration($post) {
    if (empty($post['user']['email'])) {
      $this->_socialEmailForm(); // create email form and return to FE Ajax
    }
    else {
      $customer = $this->_customerExists($post['user']['email']);
      if ($customer === FALSE) {
        // create new customer
        // first and last name should be set to required by Gigya. if they are still missing, create placeholder for them
        $firstName = $post['user']['firstName'];
        $lastName  = $post['user']['lastName'];
        $email     = $post['user']['email'];

        $this->_createCustomer($email, $firstName, $lastName, $post['user']);
        $this->getResponse()->setHeader('Content-type', 'application/json');
      }
      else {
        //email exists - link accounts
        $this->_socialLinkAccounts($post['UID']);
      }
    }
  }

  /*
   * Create Link accounts form and return to ajax
   *
   * @param int $uid
   */
  protected function _socialLinkAccounts($uid) {
    try {
      //return login form
      $block = $this->getLayout()->createBlock(
        'Mage_Core_Block_Template',
        'Loginform',
        array('template' => 'gigya/form/mini.login.phtml')
      );
      $form  = $block->renderView();
      $res   = array(
        'result'   => 'emailExsists',
        'html'     => $form,
        'id'       => Mage::helper('Gigya_Social')
          ->getPluginContainerId('gigya_login/gigya_login_conf'),
        'headline' => $this->__('Link Accounts'),
      );
      Mage::getSingleton('customer/session')
        ->setData('gigyaAction', 'linkAccount');
      Mage::getSingleton('customer/session')->setData('gigyaUid', $uid);
      $this->getResponse()->setHeader('Content-type', 'application/json');
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
    } catch (Exception $e) {
      //TODO:add error handeling
      Mage::log('Link accounts failed. exception details: ' . $e . __FILE__ . ' ' . __LINE__);
    }
  }

  /*
   * Create missing email form and return to Ajax response
   */
  protected function _socialEmailForm() {
    $block = $this->getLayout()->createBlock(
      'Mage_Core_Block_Template',
      'Emailform',
      array('template' => 'gigya/form/emailForm.phtml')
    );
    $form  = $block->renderView();
    $res   = array(
      'result'   => 'noEmail',
      'html'     => $form,
      'id'       => Mage::helper('Gigya_Social')
        ->getPluginContainerId('gigya_login/gigya_login_conf'),
      'headline' => $this->__('Fill-in missing required info'),
    );

    Mage::log('Gigya user Missing email field, created add email form. ' . __FILE__ . ' ' . __LINE__);
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
  }

  /*
   * Login Gigya social user to magento
   * @param array $post
   * @return login result as Ajax result array
  */
  protected function _doSocialLogin($post) {

    $cust_session = Mage::getSingleton('customer/session');
    $cust_session->setData('gigyaAction',
      'login'); // add gigya login data to customer session
    // the data will be used by observers set in model/customer/observer.php (and registered in config.php events). such as notify_login
    Mage::dispatchEvent('gigya_social_pre_login', array(
      'customer_session' => $cust_session,
      'gigyaData'        => $this->gigyaData
    ));
    $login_success = $cust_session->loginById($post['user']['UID']); // log in gigya user to magento
    if ($login_success) {
      Mage::dispatchEvent('gigya_social_post_login', array(
        'customer_session' => $cust_session,
        'gigyaData'        => $this->gigyaData
      ));
      $cust_session->setData('gigyaAccount', $post);
      //$url = Mage::getUrl('customer/account');
      $url = Mage::getUrl('*/*/*', array('_current' => TRUE));
      $res = array(
        'result'   => 'login',
        'redirect' => $url
      );
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
    }
    else {
      $res = array(
        'result'  => 'loginFailed',
        'message' => 'Login to site did not succeed.'
      );
      Mage::log('Login to Magento failed (_doSocialLogin()). probably UID does not exists or DB connection problem '
        . __FILE__ . ' ' . __LINE__);
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
    }

  }

  /*
   * Check if customer exists in Magento
   * @param string @email
   *
   * @return mixed $customer obj / False
   */
  protected function _customerExists($email, $websiteId = NULL) {
    $customer = Mage::getModel('customer/customer');
    if ($websiteId) {
      $customer->setWebsiteId($websiteId);
    }
    else {
      $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
      return $customer;
    }
    else {
      Mage::log('Customer could not be found in Magento DB using email from Gigya user. creating new user '
        . __FILE__ . ' ' . __LINE__);

      return FALSE;
    }

  }

  /**
   * Create Magento customer with Gigya user details (Raas + Social)
   *
   * @param string $email
   * @param string $firstName
   * @param string $lastName
   * @param obj    $gigyaUser
   */
  protected function _createCustomer(
    $email,
    $firstName = NULL,
    $lastName = NULL,
    $gigyaUser
  ) {
    $customer = Mage::getModel('customer/customer')->setId(NULL);
    $customer->getGroupId();
    $customer->setFirstname($firstName);
    $customer->setLastname($lastName);
    $customer->setEmail($email);
    if (!empty($gigyaUser['missInfo'])) {
      $missing_info = $gigyaUser['missInfo'];
      if (array_key_exists('dob', $missing_info)) {
        $this->buildDob($missing_info);
      }
      foreach ($missing_info as $key => $val) {
        $k = 'set' . ucfirst($key);
        $customer->{$k}($val);
      }
    }
    $password                      = Mage::helper('Gigya_Social')
      ->_getPassword();
    $_POST['password']             = $password;
    $_POST['confirmation']         = $password;
    $_POST['passwordConfirmation'] = $password; // since Magento 1.9.1.0 field is called passwordConfirmation.
    if ($this->userMode == 'social') {
      $customer->setData('gigyaUser', $gigyaUser);
    }
    else {
      if ($this->userMode == 'raas') {
        $cust_session = Mage::getSingleton('customer/session');
        $cust_session->setData('gigyaAccount', $gigyaUser);
        $customer->setData('gigya_uid', $gigyaUser['UID']);
      }
    }
    Mage::register('current_customer', $customer); // throws core exception
    $this->_forward('createPost', NULL, NULL, array('gigyaData' => $gigyaUser));
    // forward is magento way to call createPost function.
    // createPost creates the actual registration by posting to magento and reloading.
  }

  private function buildDob(&$info) {
    $info['dob'] = $info['year'] . "-" . $info['month'] . "-" . $info['day'];
    unset($info['year'], $info['month'], $info['day']);
  }

  /*
   * Copied magento action
   */
  public function createPostAction() {
    $session = $this->_getSession();
    if ($session->isLoggedIn()) {
      Mage::log('loggedIn ' . __FILE__ . ' ' . __LINE__);

      return;
    }
    $session->setEscapeMessages(TRUE); // prevent XSS injection in user input
    if ($this->getRequest()->isPost()) {
      $errors = array();

      if (!$customer = Mage::registry('current_customer')) {
        $customer = Mage::getModel('customer/customer')->setId(NULL);
      }

      /* @var $customerForm Mage_Customer_Model_Form */
      $customerForm = Mage::getModel('customer/form');
      $customerForm->setFormCode('customer_account_create')
        ->setEntity($customer);

      $customerData = $customerForm->extractData($this->getRequest());

      if ($this->getRequest()->getParam('is_subscribed', FALSE)) {
        $customer->setIsSubscribed(1);
      }

      /**
       * Initialize customer group id
       */
      $customer->getGroupId();

      if ($this->getRequest()->getPost('create_address')) {
        /* @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
          ->setEntity($address);

        $addressData   = $addressForm->extractData($this->getRequest(),
          'address', FALSE);
        $addressErrors = $addressForm->validateData($addressData);
        if ($addressErrors === TRUE) {
          $address->setId(NULL)
            ->setIsDefaultBilling($this->getRequest()
              ->getParam('default_billing', FALSE))
            ->setIsDefaultShipping($this->getRequest()
              ->getParam('default_shipping', FALSE));
          $addressForm->compactData($addressData);
          $customer->addAddress($address);

          $addressErrors = $address->validate();
          if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
          }
        }
        else {
          $errors = array_merge($errors, $addressErrors);
        }
      }

      try {
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== TRUE) {
          $fields = $customerForm->getAttributes();
          foreach ($fields as $field) {
            $requireds[$field->getAttributeCode()] = $field->getIsRequired();
          }
          //remove fields that we have data for
          unset($requireds['firstname'], $requireds['lastname'], $requireds['email']);
          $requireds = array_filter($requireds);
          $html      = '<div class="gigyaMoreInfo"><form action="' . Mage::getBaseUrl()
            . 'gigyalogin/login" name="moreInfo" id="gigyaMoreInfoForm">';
          foreach ($requireds as $key => $r) {
            $requireds[$key] = $fields[$key]->getStoreLabel();
            if (!$fields[$key]->getIsUserDefined()
              && is_object($this->getLayout()
                ->createBlock('customer/widget_' . $key))
            ) {
              $html .= $this->getLayout()
                ->createBlock('customer/widget_' . $key)
                ->toHtml();
            }
            else {
              $html
                .= '<div class="field">
                        <label for="' . $key . '">' . $fields[$key]->getStoreLabel() . '</label>
                        <div class="input-box">
                            <input type="text" name="' . $key . '" id="' . $key . '" value="" class="input-text" />
                        </div>
                    </div>';
            }
          }
          $html .= '<input class="button" id="gigyaMoreInfoSubmit" type="button" value="Send" onclick="gigyaFunctions.moreInfoSubmit()" "></form>';
          $html .= '</div>';

          $res = array(
            'result' => 'moreInfo',
            'fields' => $requireds,
            'html'   => $html,
          );
          $this->getResponse()->setHeader('Content-type', 'application/json');
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));

          return;
        }
        else {
          $customerForm->compactData($customerData);
          $customer->setPassword($this->getRequest()->getPost('password'));
          $customer->setConfirmation($this->getRequest()
            ->getPost('confirmation'));
          $customer->setPasswordConfirmation($this->getRequest()
            ->getPost('confirmation'));
          $customerErrors = $customer->validate();
          if (is_array($customerErrors)) {
            $errors = array_merge($customerErrors, $errors);
          }
        }

        $validationResult = count($errors) == 0;

        if (TRUE === $validationResult) {
          $params = $this->getRequest()->getParams();
          Mage::dispatchEvent('gigya_pre_user_create', array(
            'customer'   => $customer,
            'gigya_data' => $params['gigyaData']
          ));
          $customer->save();
          $updater = new Gigya_Social_Helper_FieldMapping_MagentoUpdater($params['gigyaData']);
          if ($updater->isMapped()) {
            $updater->updateMagentoAccount($customer);
          }
          Mage::dispatchEvent('customer_register_success',
            array('account_controller' => $this, 'customer' => $customer)
          );

          if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail(
              'confirmation',
              $session->getBeforeAuthUrl(),
              Mage::app()->getStore()->getId()
            );
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
              Mage::helper('customer')
                ->getEmailConfirmationUrl($customer->getEmail())));

            return;
          }
          else {
            $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);
            //$url = Mage::getUrl('customer/account');
            //$this->_redirectSuccess($url);
            $res = array(
              'result'   => 'newUser',
              'redirect' => $url
            );
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')
              ->jsonEncode($res));
          }
        }
        else {
          Mage::log($errors . __FILE__ . ' ' . __LINE__);
          $session->setCustomerFormData($this->getRequest()->getPost());
          $error = '';
          if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
              $session->addError($errorMessage);
              $error .= $errorMessage . "\n";
            }
            $res['result']  = 'error';
            $res['message'] = $error;
          }
          else {
            $res['result']  = 'error';
            $res['message'] = $this->__('Invalid customer data');
          }
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
      } catch (Mage_Core_Exception $e) {
        $session->setCustomerFormData($this->getRequest()->getPost());
        if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
          $message = $this->__('There is already an account with this email address.');
        }
        else {
          $message = $e->getMessage();
        }
        $res['result']  = 'error';
        $res['message'] = $message;
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
      } catch (Exception $e) {
        $session->setCustomerFormData($this->getRequest()->getPost())
          ->addException($e, $this->__('Cannot save the customer.'));
        $message        = $this->__('Cannot save the customer.');
        $res['result']  = 'error';
        $res['message'] = $message;
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
      }
    }

    Mage::log('error ' . __FILE__ . ' ' . __LINE__);
    //$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
  }

  /**
   * Login post action
   */
  public function loginPostAction() {
    if ($this->_getSession()->isLoggedIn()) {
      $res['result']  = 'error';
      $res['message'] = $this->__('User is logged in.');
      $this->getResponse()->setHeader('Content-type', 'application/json');
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));

      return;
    }
    $session = $this->_getSession();

    if ($this->getRequest()->isPost()) {
      $login = Mage::helper('core')->jsonDecode($this->getRequest()
        ->getPost('login'));
      if (!empty($login['username']) && !empty($login['password'])) {
        $res = array();
        try {
          $session->login($login['username'], $login['password']);
          if ($session->getCustomer()->getIsJustConfirmed()) {
            $this->_welcomeCustomer($session->getCustomer(), TRUE);
          }
          $res['result'] = 'success';
          $this->getResponse()->setHeader('Content-type', 'application/json');
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
        } catch (Mage_Core_Exception $e) {
          switch ($e->getCode()) {
            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
              $value   = Mage::helper('customer')
                ->getEmailConfirmationUrl($login['username']);
              $message = Mage::helper('customer')
                ->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.',
                  $value);
              break;
            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
              $message = $e->getMessage();
              break;
            default:
              $message = $e->getMessage();
          }
          $res['result']  = 'error';
          $res['message'] = $message;
          $this->getResponse()->setHeader('Content-type', 'application/json');
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
        } catch (Exception $e) {
          // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
      }
      else {
        $res['result']  = 'error';
        $res['message'] = $this->__('Login and password are required.');
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
      }
    }
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
  }

  public function logoutAction() {
    $cust = $this->_getSession()->logout()->setBeforeAuthUrl(NULL);
    if ($cust->getId() === NULL) {
      $res['result'] = 'success';
    }
    else {
      $res['result'] = 'error';
    }

    $this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($res));
    exit();
  }

}

