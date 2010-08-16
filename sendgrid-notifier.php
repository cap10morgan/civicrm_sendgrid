<?php
/* Sendgrid integration for CiviCRM, by Carl Tashian, Participatory Politics Foundation.
     Full explanation here: http://civicrm.org/blogs/ctashian/integrating-sendgrid-civimail
     See license below. */

/* Modified by Wes Morgan to use the CiviCRM settings file key */

// Bootstrap CiviCRM config
$civicrm_root = 'sites/all/modules/civicrm';
$civicrm_config_path = $civicrm_root . '/civicrm.config.php';
require_once $civicrm_config_path;
$api_key = CIVICRM_SENDGRID_KEY;

if ( $_GET['key'] == $api_key && $api_key !== null) {

  // Get the config singleton
  require_once $civicrm_root . '/CRM/Core/Config.php';
  $config =& CRM_Core_Config::singleton();

	// Include any Modules that you may want to extend
	require_once $civicrm_root.'/api/v2/Contact.php';
	require_once $civicrm_root.'/api/v2/Location.php';

	switch( $_POST['event'] )
	{
	/* We have those disabled tracking of clicks, opens, and unsubscribes in our sendgrid settings.

	  case 'click':
	    $emailText = $_POST['email'] . ' clicked on ' . $_POST['url'];
	    break;
	 
	  case 'open':
	    $emailText = $_POST['email'] . ' opened email';
	    break;
	 
	  case 'unsubscribe':
	    $emailText = $_POST['email'] . ' unsubscribed';
	    break;
	*/
	  case 'bounce':
	  case 'spamreport':
		// give the search criteria in params array
		$params = array ( 'email' => $_POST['email'] );

		$myContacts = civicrm_contact_search( $params );

		if ( $myContacts ) {
		foreach ( $myContacts as $myContact ) {
			// Mark them as bounced!
			$email = array('email' => $myContact['email'],
					'on_hold' => 1);

			$contactUpdate = array(
				'contact_id' => $myContact['contact_id'],
				'contact_type' => $myContact['contact_type'],
				'location_type_id' => 1, // Home -- the only location type we ever use.
				'email' => array($email)
			);
			// print_r($contactUpdate);

			// Update the contact in CiviCRM
			$c2 =& civicrm_location_update($contactUpdate);
			// print_r($c2);
		}
		} else {
			echo 'No contact found with that email address.';
		}

		break;
	}
}

/* 
Copyright (c) 2010 Participatory Politics Foundation

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>
