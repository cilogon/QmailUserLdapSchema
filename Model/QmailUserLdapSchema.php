<?php
/**
* User plugin to export to the qmailUser mailAlternateAddress. It requires a an extendedtype
* email type called 'altListSub' to be added into COmanage. Also, your LDAP server will have
* to know about the qmailUser schema.
*
*/
class QmailUserLdapSchema extends AppModel
{

  // using qmail
  public $attributes = array(
    'qmailUser' => array(
      'objectclass' => array(
        'required' => true
      ),
      'attributes' => array(
        'mailAlternateAddress' => array(
          'required' => false,
          'multiple' => true,
          'extendedtype' => 'email_address_types',
          'defaulttype' => 'altListSub'
        )
      )
    )
  );

  // Required by COmanage Plugins
  public $cmPluginType = "ldapschema";

  // Document foreign keys
  public $cmPluginHasMany = array();

  /**
  * Assemble attributes to write. Required for LDAP schema plugin.
  *
  * @since COmanage Registry 1.1.0
  * @param array $configuredAttributes Array of configured attributes
  * @param array $provisioningData Array of provisioning data
  * @return array Array of attribute names and values to write
  */
  public function assemblePluginAttributes($configuredAttributes, $provisioningData)
  {
    //$this->log("Got this configuredAttributes" . print_r($configuredAttributes, true), LOG_DEBUG);
    //$this->log("Got this provisioningData" . print_r($provisioningData, true), LOG_DEBUG);

      $attrs = array( );
    //$this->log("array key has EmailAddress: " . array_key_exists('EmailAddress', $provisioningData), LOG_DEBUG);
      if (array_key_exists('EmailAddress', $provisioningData)) {
          //  return $attrs; There should be only but but we loop for forms sake.
          foreach ($configuredAttributes as $attr => $cfg) {
              if ($attr == 'mailAlternateAddress') {
                  $attrs[$attr] = array();
                  foreach ($provisioningData['EmailAddress'] as $emailAddy) {
                    //$this->log("Found EmailAddress " . print_r($emailAddy, true), LOG_DEBUG);

                      if (isset($cfg['export']) && $cfg['export'] == 1 && isset($emailAddy['type'])
                    && $emailAddy['type'] == $cfg['type']) {
                          $attrs[$attr][] = $emailAddy['mail'];
                      }
                  }
              }
          }
      }
    //$this->log("Returning these email addresses" . print_r($attrs, true) . " array size: " . count($attrs), LOG_DEBUG);
      return $attrs;
  }

  /**
   * Expose menu items.
   *
   * @ since COmanage Registry v0.9.2
   * @ return Array with menu location type as key and array of labels, controllers, actions as values.
   */
  public function cmPluginMenus()
  {
      return array();
  }
}
