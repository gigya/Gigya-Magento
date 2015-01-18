<?php
/*
 * Create Map fields UI for admin-Gigya User management
 *
 */
include_once __DIR__ . '/../sdk/gigyaCMS.php';

class Gigya_Social_Block_Adminhtml_MapFields extends Mage_Adminhtml_Block_System_Config_Form_Field {

  protected $gigyaSchema;
  protected $mageCustomerFields;
  protected $mode;


  public function __construct() {
      $this->mode = Mage::getStoreConfig('gigya_login/gigya_user_management/login_modes') ;
      $this->getGigyaSchema();
      $this->getMageCustomerFields();
  }

  protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
  {
    $table = "<table class='field-mapping-table'>
                <tr>
                 <td>Source</td>
                 <td>Target</td>
                 <td>Remove</td>
                </tr>
                <tr>
                 <td>
                     <select id='gigya-map-source' class='gigya-map-source' name='gigya-map-source'>
                        <option selected='selected'> - Select - </option>
                        {$this->gigyaSchema}
                     </select>
                 </td>
                 <td>
                    <select id='gigya-map-target' class='gigya-map-target' name='gigya-map-target'>
                          <option selected='selected'> - Select - </option>
                          {$this->mageCustomerFields}
                     </select>
                 </td>
                 <td><button id='gigya-add-map-field'>Add</button></td>
                </tr>
              </table>";

    return $table;
  }

  /*
   * Create options list from Gigya profile and data fields
   * $return string $gigya_map (options list)
   */
  protected function getGigyaSchema() {
    if ($this->mode == 'raas') {
        $response =  Mage::helper('Gigya_Social')->utils->call('accounts.getSchema');
        // Create the profile options
        $profile = array_keys($response['profileSchema']['fields']);
        $profileList = "<optgroup label='Profile_fields'>";
        foreach ( $profile as $field  ) {
            $profileList .= "<option value='profile.$field'>$field</option>";
        }
        $profileList .= "</optgroup>";
        // Add the data options
        $data = array_keys($response['dataSchema']['fields']);
        $dataList = "<optgroup label='Data fields'>";
        foreach ( $data as $field ) {
            $dataList .= "<option value='data.$field'>$field</option>";
        }
        $dataList .= "</optgroup>";
        $fieldsList = $profileList . $dataList;
        $this->gigyaSchema = $fieldsList;
    }
  }

  /*
   * Create options list from Magento OOB and custom customer fields
   * @return string $mage_map
   */
  protected function getMageCustomerFields() {

  //    $attributes = Mage::getModel('customer/customer')->getAttributes();
      // Mage core user fields
      $optionsList = "<optgroup label='Magento core fields'>";
      $optionsList  .= "<option value='mage.firstname'>Firstname</option>";
      $optionsList  .= "<option value='mage.lastname'>Lastname</option>";
      $optionsList  .= "<option value='mage.prefix'>Prefix</option>";
      $optionsList  .= "<option value='mage.suffix'>Suffix</option>";
      $optionsList  .= "<option value='mage.dob'>dob (date of birth)</option>";
      $optionsList  .= "<option value='mage.taxvat'>Taxvat</option>";
      $optionsList  .= "<option value='mage.gender'>Gender</option>";
      $optionsList  .= "</optgroup>";
      // Address entity fields
      $addressFields = "<optgroup label='Magento Address fields'>";
      $addressFields.= "<option value='mage.city'>City</option>";
      $addressFields.= "<option value='mage.company'>Company</option>";
      $addressFields.= "<option value='mage.fax'>Fax</option>";
      $addressFields.= "<option value='mage.postcode'>Postcode</option>";
      $addressFields.= "<option value='mage.prefix'>Prefix</option>";
      $addressFields.= "<option value='mage.region'>Region</option>";
      $addressFields.= "<option value='mage.street'>Street</option>";
      $addressFields.= "<option value='mage.suffix'>Suffix</option>";
      $addressFields.= "<option value='mage.telephone'>Telephone</option>";
      $addressFields .= "</optgroup>";
        // newsletter subscription
      $newsletter = "<optgroup label='Newsletter subscription'>";
      $newsletter .= "<option value='mage.newsletter'>Newsletter subscription</option>";
      $newsletter .= "</optgroup>";

    $this->mageCustomerFields = $optionsList . $addressFields . $newsletter;
  }


}