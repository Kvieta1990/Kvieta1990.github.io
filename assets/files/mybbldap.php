<?php
/** the next two lines are for error suppression, when error reporting is turned on in php.ini **/
//error_reporting(0);
//@ini_set("display_errors",0);

/**
 * This program is distributed under the terms of the GNU General Public License
 * You should have received a copy of the license with this program
 *
 * CHANGELOG
 * 
 * mybbldap from code originally written for Time Tracker
 * The first mybbldap.php was written in 2010, 2011 by Al Klein (Rukbat)
 * Code was extensively modified in Sept 2013 by John Kurrle (jkurrle@yahoo.com)
 * Fix from forum posts by deejay added July 2015
 * Rewrite of LDAP handling code by Mark van Reijn (mvreijn@idfocus.nl) added Sept 2017
 * Update to 1.8.x plugin in Sept 2017 by mvreijn
 * Added proxy user and ldaps support in Sept 2018 by mvreijn
 * Fixed proxy support bug reported by Sebijk in version 0.0.8 by mvreijn
 **/

/**
 * IMPORTANT NOTE: In order to use TLS protocol support, you need to have mod_ssl working in Apache.
 * in Ubuntu, the command to do this is (without quotes) "sudo a2enmod ssl", then restart the Apache
 * server.  Example command: "sudo /etc/init.d/apache2 restart"
 *
 * There still appears to be a bug, when TLS support is enabled and the user changes their local 
 * password, which forces the passwords to be out of sync.  When TLS is turned off, the LDAP password
 * is the only viable password, and it will overwrite the local password with the LDAP password.  If
 * you want to "fix" this under TLS, then refer to the next following section.
 **/

/**
 * DISABLING OR REDIRECTING REGISTRATION/PASSWORD CHANGE:
 *
 * To disable a user from changing their local password, you must modify the usercp.php file.  Search for
 * input['action'] == "password" (around line 1108) and comment out the plugins and eval lines.  Make a 
 * copy of the output_page line, then comment it out too.  Change $editpassword to a html meta refresh to
 * where you want it to go to.  <meta http-equiv="refresh" content="0;url=http://somepage.url" />  Make 
 * sure the hard coded meta refresh is in quotes that are alternate to the quotes within the html meta tag
 * itself, i.e., if using double quotes within the tag, wrap the tag itself in single quotes.
 *
 * To redirect the Registration link to point to a LDAP registration page instead, first, go into the 
 * Admin Control Panel, Configuration, User Registration and Profile Options section, and set "Disable 
 * Registrations" to "Yes".  Then, in member.php, search for "error($lang->registrations_disabled);" 
 * (around line 62).  Comment out the line and replace it with:
 *  output_page("<meta http-equiv=\"refresh\" content=\"0;url=http://somepage.url\" />");
 **/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
  die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("member_do_login_start", "mybbldap_check", 1);

function mybbldap_info()
{
  //the data showing in Plugins
  return array
  (
    "name" => 'MyBB LDAP Authentication',
    "description" => 'LDAP Authentication and Provisioning for MyBB',
    "website" => "https://community.mybb.com/mods.php?action=view&pid=1043",
    "author" => "mvreijn(current), deejay(July 2015), jkurrle(September 2013), Rukbat(October 2011)",
    "authorsite" => "https://www.idfocus.nl",
    "version" => "0.0.8",
    "guid" => "6ea4199cf78903a1d76e0ae59c4530f6",
    "codename" => "mybbldap",
    "compatibility" => "18*"
  );
}// end function mybbldap_info()

function mybbldap_install()
{
  global $db, $mybb;

  //the data showing in Settings
  $mybbldap_settinggroup = array
  (
    "name" => "mybbldap_settings",
    "title" => 'MyBB LDAP Authentication and Provisioning Settings',
    "description" => 'Settings to connect to the ldap server',
    "disporder" => 1,
    "isdefault" => 0
  );
  $db->insert_query("settinggroups", $mybbldap_settinggroup);
  $gid = $db->insert_id();

  $mybbldap_ldapenabled = array
  (
    "name" => "mybbldap_ldapenabled",
    "title" => 'LDAP Validation Main Switch',
    "description" => 'Use LDAP validation?<br />(If this causes problems during installation, disable it in the database settings table)',
    "optionscode" => "yesno",
    "value" => "yes",
    "disporder" => 0,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapenabled); 

  $mybbldap_ldapserver = array
  (
    "name" => "mybbldap_ldapserver",
    "title" => 'LDAP Server Name',
    "description" => "What is the name of the LDAP server?<br />If you don\'t know the name, try using the IP address instead.",
    "optionscode" => "text",
    "value" => "myldapserver",
    "disporder" => 1,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapserver); 

  $mybbldap_ldapport = array
  (
    "name" => "mybbldap_ldapport",
    "title" => 'LDAP Port number',
    "description" => "What is the port number that the LDAP server is listening on?<br />(Typically, 389 for Plaintext or StartTLS and 636 for SSL)",
    "optionscode" => "text",
    "value" => "389",
    "disporder" => 2,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapport); 

  $mybbldap_confidentiality = array
  (
    "name" => "mybbldap_confidentiality",
    "title" => 'Connection confidentiality',
    "description" => 'Should the connection be encrypted?<br />When you choose SSL, you have to add the LDAP server CA certificate to the trusted CA certificates properly.<br/>(Use StartTLS if you\'\'re not sure.)',
    "optionscode" => 'select
      plain= Plaintext (no encryption)
      tls= StartTLS (recommended)
      ssl= SSL (ldaps)',
    value => 'tls',
    "disporder" => 3,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_confidentiality); 

  $mybbldap_protocol = array
  (
    "name" => "mybbldap_protocol",
    "title" => 'LDAP Protocol Version',
    "description" => 'Which LDAP protocol version should we use?<br />(Use 3 if you\'\'re not sure)',
    "optionscode" => 'select
      2= 2
      3= 3',
    value => '3',
    "disporder" => 4,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_protocol); 

  $mybbldap_proxy_usr = array
  (
    "name" => "mybbldap_proxy_usr",
    "title" => 'Proxy user DN',
    "description" => 'What is the full DN of the LDAP proxy user?<br />This user will be used for DN resolving during authentication. Leave blank to disable.',
    "optionscode" => "text",
    "value" => "",
    "disporder" => 5,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_proxy_usr);
  
  $mybbldap_proxy_pwd = array
  (
    "name" => "mybbldap_proxy_pwd",
    "title" => 'Proxy user password',
    "description" => 'What is the password of the LDAP proxy user?<br />',
    "optionscode" => "text",
    "value" => "",
    "disporder" => 6,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_proxy_pwd);
  
  $mybbldap_ou = array
  (
    "name" => "mybbldap_ou",
    "title" => 'Organizational Unit (OU) of users',
    "description" => 'What is the base OU of the user population?<br />If you use Active Directory, you may leave this blank to use the UPN.',
    "optionscode" => "text",
    "value" => "ou=users",
    "disporder" => 10,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ou);
  
  $mybbldap_basedn = array
  (
    "name" => "mybbldap_basedn",
    "title" => 'Base DN of the LDAP Server',
    "description" => 'What is the base DN of the LDAP server?',
    "optionscode" => "text",
    "value" => "dc=myldapserver,dc=com",
    "disporder" => 11,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_basedn);
  
  $mybbldap_ldapdomain = array
  (
    "name" => "mybbldap_ldapdomain",
    "title" => 'LDAP Server Domain Name',
    "description" => 'If you run Active Directory and want to use the UPN for authentication,<br/> specify the domain suffix here. Otherwise, leave blank.',
    "optionscode" => "text",
    "value" => "myldapserver.com",
    "disporder" => 12,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapdomain);

  $mybbldap_ldapnaming = array
  (
    "name" => "mybbldap_ldapnaming",
    "title" => 'LDAP Naming Attribute',
    "description" => "What is the naming attribute of the LDAP server?<br />This will be the MyBB username of LDAP users.<br/>(Normally, it is cn or uid but you may want users to log in with their email address for example)",
    "optionscode" => "text",
    "value" => "cn",
    "disporder" => 14,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapnaming);

  $mybbldap_ldapmail = array
  (
    "name" => "mybbldap_ldapmail",
    "title" => 'LDAP Mail Attribute',
    "description" => "What is the email attribute name of the LDAP server?<br/>This will be used as the MyBB email address for LDAP users.<br />(Normally, it is mail)",
    "optionscode" => "text",
    "value" => "mail",
    "disporder" => 15,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_ldapmail);

  $mybbldap_inclusionattribute = array
  (
    "name" => "mybbldap_inclusionattribute",
    "title" => 'LDAP Inclusion Attribute',
    "description" => "What is the attribute name used for inclusion checks?<br />LDAP users with this attribute and the corresponding value below will be granted access to MyBB.<br/>(Leave blank to disable this feature)",
    "optionscode" => "text",
    "value" => "ou",
    "disporder" => 16,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_inclusionattribute);

  $mybbldap_inclusionvalue = array
  (
    "name" => "mybbldap_inclusionvalue",
    "title" => 'LDAP Inclusion Attribute value',
    "description" => "What is the value of the Inclusion Attribute that grants access to MyBB?<br />(Not used if the inclusion attribute is left blank)",
    "optionscode" => "text",
    "value" => "forumusers",
    "disporder" => 17,
    "gid" => intval($gid)
  );
  $db->insert_query("settings", $mybbldap_inclusionvalue);

  rebuild_settings();
}

function mybbldap_is_installed()
{
  global $db;
    
  $query = $db->simple_select('settings','*','name="mybbldap_ldapserver"');
  $installed = $db->fetch_array($query);

  if ($installed)
  {
    return true;
  } 
  else return false;
}// end function mybbldap_is_installed()

function mybbldap_uninstall()
{
  global $db, $mybb;

  $query = $db->simple_select('settinggroups','gid','name="mybbldap_settings"');
  $mybbldap = $db->fetch_array($query);
  $db->delete_query('settinggroups',"gid='".$mybbldap['gid']."'");
  $db->delete_query('settings',"gid='".$mybbldap['gid']."'");
  rebuild_settings();

}// end function mybbldap_uninstall()

// On activation, check the settings for possible upgrade
function mybbldap_activate()
{
  if (mybbldap_is_installed())
  {
    // migrate settings
  }
}// end function mybbldap_activate()

$enabled = '';
$account_exists = false;
$correct = false;
$userEntry;

function mybbldap_check()
{
  global $mybb, $db, $lang, $session, $plugins, $inline_errors, $errors;
  global $enabled, $account_exists, $correct;
  $enabled = fetch_config_value("ldapenabled", $db);  

  if($mybb->input['action'] != "do_login" || $mybb->request_method != "post" || !$enabled)
  {
    return;
  }

  // Checks to make sure the user hasn't exceeded max # of logins
  $login_attempts = login_attempt_check();
  $login_text = '';

  // Did we come from the quick login form?
  if($mybb->input['quick_login'] == "1" && $mybb->input['quick_password'] && $mybb->input['quick_username'])
  {
    $mybb->input['password'] = $mybb->input['quick_password'];
    $mybb->input['username'] = $mybb->input['quick_username'];
  }// end if

  //set up some ldap variables
  $query = $db->simple_select('settings','value','name="maxloginattempts"');
  $maxloginattempts = $db->fetch_field($query, 'value');

  // make the LDAP connection
  $ld = connect_to_ldap_server($db);

  if (!username_exists($mybb->input['username']))
  {
    $isAuthenticated = check_ldap_credentials($ld, false);

    // if we pass, create a new account
    if ($isAuthenticated)
    {
      $plugins->run_hooks("member_do_register_start");

      // Set up user handler.
      require_once MYBB_ROOT."inc/datahandlers/user.php";
      $userhandler = new UserDataHandler("insert");

      $LDAPmail = read_user_email($db, $ld, $mybb->input['username']);
      // Set the data for the new user.
      $user = array
      (
        "username" => $mybb->input['username'],
        "password" => $mybb->input['password'],
        "password2" => $mybb->input['password'],
        "email" => $LDAPmail,
        "email2" => $LDAPmail,
        "usergroup" => 2,
        "timezone" => $mybb->input['timezoneoffset'],
        "language" => $mybb->input['language'],
        "profile_fields" => $mybb->input['profile_fields'],
        "regip" => $session->ipaddress,
        "longregip" => my_ip2long($session->ipaddress),
        "coppa_user" => intval($mybb->cookies['coppauser']),
        "regcheck1" => $mybb->input['regcheck1'],
        "regcheck2" => $mybb->input['regcheck2'],
        "regdate" => TIME_NOW
      );

      // Do we have a saved COPPA DOB?
      if($mybb->cookies['coppadob'])
      {
        list($dob_day, $dob_month, $dob_year) = explode("-", $mybb->cookies['coppadob']);
        $user['birthday'] = array
          (
          "day" => $dob_day,
          "month" => $dob_month,
          "year" => $dob_year
          );
      }// end if

      $user['options'] = array
      (
        "allownotices" => $mybb->input['allownotices'],
        "hideemail" => $mybb->input['hideemail'],
        "subscriptionmethod" => $mybb->input['subscriptionmethod'],
        "receivepms" => $mybb->input['receivepms'],
        "pmnotice" => $mybb->input['pmnotice'],
        "emailpmnotify" => $mybb->input['emailpmnotify'],
        "invisible" => $mybb->input['invisible'],
        "dstcorrection" => $mybb->input['dstcorrection']
      );

      $userhandler->set_data($user);
      $errors = "";
      $userhandler->set_validated(true);
      $user_info = $userhandler->insert_user();
      $plugins->run_hooks("member_register_start");
      $validator_extra = '';
      $timezoneoffset = $mybb->settings['timezoneoffset'];
      $tzselect = build_timezone_select("timezoneoffset", $timezoneoffset, true);
      $stylelist = build_theme_select("style");
      $referrer = '';
      // JS validator extra
      $lang->js_validator_password_length = $lang->sprintf($lang->js_validator_password_length, $mybb->settings['minpasswordlength']);
      $validator_extra .= "\tregValidator.register('password', 'length', {match_field:'password2', min: {$mybb->settings['minpasswordlength']}, failure_message:'{$lang->js_validator_password_length}'});\n";

      $languages = $lang->get_languages();
      $langoptions = '';
      foreach($languages as $lname => $language)
      {
        $language = htmlspecialchars_uni($language);
        if($user['language'] == $lname)
        {
          $langoptions .= "<option value=\"$lname\" selected=\"selected\">$language</option>\n";
        }
        else
        {
          $langoptions .= "<option value=\"$lname\">$language</option>\n";
        }
      }// end foreach

      $plugins->run_hooks("member_register_end");
      $plugins->run_hooks("member_activate_start");

      switch($mybb->settings['username'])
      {
        case 0:
          $query = $db->simple_select("users", "*", "LOWER(username)='".$db->escape_string(my_strtolower($mybb->input['username']))."'", array('limit' => 1));
          break;
        case 1:
          $query = $db->simple_select("users", "*", "LOWER(email)='".$db->escape_string(my_strtolower($mybb->input['username']))."'", array('limit' => 1));
          break;
        case 2:
          $query = $db->simple_select("users", "*", "LOWER(username)='".$db->escape_string(my_strtolower($mybb->input['username']))."' OR LOWER(email)='".$db->escape_string(my_strtolower($mybb->input['username']))."'", array('limit' => 1));
          break;
        default:
          $query = $db->simple_select("users", "*", "LOWER(username)='".$db->escape_string(my_strtolower($mybb->input['username']))."'", array('limit' => 1));
          break;
      }// end switch

      $user = $db->fetch_array($query);
      $uid = $user['uid'];
      $db->delete_query("awaitingactivation", "uid='".$user['uid']."' AND (type='r' OR type='e')");
      $plugins->run_hooks("member_activate_accountactivated");

      $username = $user['username'];

      // Generate a new password, then update it
      $password_length = intval($mybb->settings['minpasswordlength']);
      if($password_length < 8)
      {
        $password_length = 8;
      }
      $salt = random_str($password_length);
      $encryptedString=md5(md5($salt).md5($mybb->input['password']));
      $passwordArray = array('password'=>$encryptedString,'salt'=>$salt);
      $db->update_query("users",$passwordArray,"uid='".$user['uid']."'");
      $newemail = array
      (
        "email" => $db->escape_string($user['email']),
      );
      $db->update_query("users", $newemail, "uid='".$user['uid']."'");

      $plugins->run_hooks("member_activate_emailupdated");
    }// end if
  }// end if
  else // username exists locally
  {
    $query = $db->simple_select("users", "loginattempts", "LOWER(username)='".$db->escape_string(my_strtolower($mybb->input['username']))."'", array('limit' => 1));
    $loginattempts = $db->fetch_field($query, "loginattempts");
    $errors = array();
    $user = ldaplogin_validate_password_from_username($mybb->input['username'], $mybb->input['password']);
    if (!$user['uid'])
    {
      $query = $db->simple_select('users','uid, salt','username="'.$mybb->input['username'].'"');
      $hasAccount = $db->fetch_array($query);
      if($acct)
      {
        $uid = $db->fetch_field($query, 'uid');
        $salt = $db->fetch_field($query, 'salt');
      }
      if(check_ldap_credentials($ld, $hasAccount))
      {
        $account_exists = $hasAccount;
        $correct = false;
        $loginattempts = 1;
      }
    }// end if
  }// end else

  if ($loginattempts > $maxloginattempts || intval($mybb->cookies['loginattempts']) > $maxloginattempts)
  {
    // Show captcha image for guests if enabled
    if($mybb->settings['captchaimage'] == 1 && function_exists("imagepng") && !$mybb->user['uid'])
    {
      // If previewing a post - check their current captcha input - if correct, hide the captcha input area
      if($mybb->input['imagestring'])
      {
        $imagehash = $db->escape_string($mybb->input['imagehash']);
        $imagestring = $db->escape_string($mybb->input['imagestring']);
        $query = $db->simple_select("captcha", "*", "imagehash='{$imagehash}' AND imagestring='{$imagestring}'");
        $imgcheck = $db->fetch_array($query);
        if($imgcheck['dateline'] > 0)
        {
          $correct = true;
        }
        else
        {
          $db->delete_query("captcha", "imagehash='{$imagehash}'");
          $errors[] = $lang->error_regimageinvalid;
        }
      }
      else if($mybb->input['quick_login'] == 1 && $mybb->input['quick_password'] && $mybb->input['quick_username'])
      {
        $errors[] = $lang->error_regimagerequired;
      }
      else
      {
        $errors[] = $lang->error_regimagerequired;
      }
    }
    $do_captcha = true;
  }
  //
  if (!empty($errors))
  {
    $mybb->input['action'] = "login";
    $mybb->input['request_method'] = "get";
    $inline_errors = inline_error($errors);
  }
  else if ($correct)
  {
    if($user['coppauser'])
    {
      error($lang->error_awaitingcoppa);
    }
    my_setcookie('loginattempts', 1);
    $db->delete_query("sessions", "ip='".$db->escape_string($session->ipaddress)."' AND sid != '".$session->sid."'");
    $newsession = array
    (
      "uid" => $user['uid'],
    );
    $db->update_query("sessions", $newsession, "sid='".$session->sid."'");

    $db->update_query("users", array("loginattempts" => 1), "uid='{$user['uid']}'");

    // Temporarily set the cookie remember option for the login cookies
    $mybb->user['remember'] = $user['remember'];

    my_setcookie("mybbuser", $user['uid']."_".$user['loginkey'], null, true);
    my_setcookie("sid", $session->sid, -1, true);

    $plugins->run_hooks("member_do_login_end");
    if($mybb->input['url'] != "" && my_strpos(basename($mybb->input['url']), 'member.php') === false)
    {
      if ((my_strpos(basename($mybb->input['url']), 'newthread.php') !== false || my_strpos(basename($mybb->input['url']), 'newreply.php') !== false) && my_strpos($mybb->input['url'], '&processed=1') !== false)
      {
        $mybb->input['url'] = str_replace('&processed=1', '', $mybb->input['url']);
      }

      $mybb->input['url'] = str_replace('&amp;', '&', $mybb->input['url']);

      // Redirect to the URL if it is not member.php
      redirect(htmlentities($mybb->input['url']), $lang->redirect_loggedin);
    }
    else
    {
      redirect("index.php", $lang->redirect_loggedin);
    }
  }
  else
  {
    $mybb->input['action'] = "login";
    $mybb->input['request_method'] = "get";
  }
}

/**
 * Checks a login/password against an ldap server
 *
 * @at return $account_exists and $correct are set
 *
 * if the login/password is correct in ldap, but the password is wrong
 * in MyBB, it will be changed in MyBB
 */
function check_ldap_credentials($ld, $acct)
{
  global $mybb, $db, $lang, $correct, $errors, $login_attempts, $login_text, $userEntry;

  $correct = true;
  $username = $mybb->input['username'];
  $password = $mybb->input['password'];

  $login = bind_to_ldap_server($db, $ld, $username, $password);
  //
  if ($login == false) // if the password isn't correct...
  {
    my_setcookie('loginattempts', $login_attempts + 1);
    if ($acct)
    {
      $db->write_query("UPDATE ".TABLE_PREFIX."users SET loginattempts=loginattempts+1 WHERE LOWER(username) = '".$db->escape_string(my_strtolower($username))."'");
    }
    $mybb->input['action'] = "login";
    $mybb->input['request_method'] = "get";

    if ($mybb->settings['failedlogintext'] == 1)
    {
      $login_text = $lang->sprintf($lang->failed_login_again, $mybb->settings['failedlogincount'] - $login_attempts);
    }
    $errors[] = $lang->error_invalidpworusername.$login_text;
    return false;
  } 
  else // the password is correct 
  {
    // Does the user have access?
    if (validate_user_access($db, $ld, $username))
    {
      $query = $db->simple_select("users", "*", "LOWER(username)='".$db->escape_string(my_strtolower($username))."'");
      $user = $db->fetch_array($query);
      $logindetails = update_password($user['uid'], md5($password));
      $correct = false;
      return true;  
    } else {
      $errors[] = $lang->error_invalidpworusername."Not authorized";
      return false;      
    }
  }
}

/**
 * Checks a password with a supplied username.
 *
 * @param string The username of the user.
 * @param string The md5()'ed password.
 * @return boolean|array False when no match, array with user info when match.
 */
function ldaplogin_validate_password_from_username($username, $password)
{
  global $db;

  $query = $db->simple_select("users", "uid,username,password,salt,loginkey", "username='".$db->escape_string($username)."'", array('limit' => 1));
  $user = $db->fetch_array($query);
  if (!$user['uid'])
  {
    return false;
  }
  else
  {
    return ldaplogin_validate_password_from_uid($user['uid'], $password, $user);
  }
}// end function ldaplogin_validate_password_from_username($username, $password)

/**
 * Checks a password with a supplied uid.
 *
 * @param int The user id.
 * @param string The md5()'ed password.
 * @param string An optional user data array.
 * @return boolean|array False when not valid, user data array when valid.
 */
function ldaplogin_validate_password_from_uid($uid, $password, $user = array())
{
  global $db, $mybb;

  if ($mybb->user['uid'] == $uid)
  {
    $user = $mybb->user;
  }
  
  if (!$user['password'])
  {
    $query = $db->simple_select("users", "uid,username,password,salt,loginkey", "uid='".intval($uid)."'", array('limit' => 1));
    $user = $db->fetch_array($query);
  }

  if (!$user['salt'] && trim($user['password']) != '')
  {
    // Generate a salt for this user and assume the password stored in db is a plain md5 password
    $user['salt'] = generate_salt();
    $user['password'] = salt_password($user['password'], $user['salt']);
    $sql_array = array
    (
      "salt" => $user['salt'],
      "password" => $user['password']
    );
    $db->update_query("users", $sql_array, "uid='".$user['uid']."'", 1);
  }// end if(!$user['salt'] && trim($user['password']) != '')

  if (!$user['loginkey'])
  {
    $user['loginkey'] = generate_loginkey();
    $sql_array = array
    (
      "loginkey" => $user['loginkey']
    );
    $db->update_query("users", $sql_array, "uid = ".$user['uid'], 1);
  }// end if(!$user['loginkey'])

  if (salt_password(md5($password), $user['salt']) == $user['password'])
  {
    return $user;
  }
  else
  {
    return false;
  }
}// end function ldaplogin_validate_password_from_uid($uid, $password, $user = array())

/**
 * Fetch a value from the mybb configuration store
 */
function fetch_config_value($value, $db)
{
  $prefix = "mybbldap_";
  $query = $db->simple_select('settings','value','name="'.$prefix.$value.'"');
  return $db->fetch_field($query, 'value');
}

/**
 * Connect to the LDAP server using the configured values
 */
function connect_to_ldap_server($db)
{
  $server = fetch_config_value("ldapserver", $db);
  $port = fetch_config_value("ldapport", $db);
  $useSSL = fetch_config_value("confidentiality", $db);
  $useSSL = "ssl";
  if ($useSSL == "ssl")
  {
    $conn = ldap_connect("ldaps://".$server.":".$port);
    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    return $conn;
  }
  $conn = ldap_connect($server, $port);
  $protocol = fetch_config_value("protocol", $db);
  ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
  return $conn;
}

/*
 * Try the different bind options and return true / false for success / failure
 */
function bind_to_ldap_server($db, $ld, $username, $password)
{
  $ldConnection = false;
  $useTLS = fetch_config_value("confidentiality", $db);
  if ($useTLS == "tls") 
  {
    ldap_start_tls($ld);
  }
  $ldConnection = true;

  $ou     = fetch_config_value("ou", $db);
  $basedn = fetch_config_value("basedn", $db);
  $naming = fetch_config_value("ldapnaming", $db);
  $domain = fetch_config_value("ldapdomain", $db);
  $proxy_usr = fetch_config_value("proxy_usr", $db);
  $proxy_pwd = fetch_config_value("proxy_pwd", $db);

  // TODO retrieve LDAP extended error message on bind fail
  // Try AD login with UPN if OU field is empty
  if ($ou == "" && $ldConnection) 
  {
    $login = @ldap_bind($ld, $username.'@'.$domain, $password);
  }
  // Try with proxy user
  elseif ($proxy_usr != "")
  {
    // Bind with proxy
    $proxy = @ldap_bind($ld, $proxy_usr, $proxy_pwd);
    if (!$proxy) return $proxy;
    // Resolve DN with search
    $user = read_ldap_entry($db, $ld, $username);
    if ($user)
    {
      $dn = ldap_get_dn($ld, $user);
      // Bind with DN
      $login = @ldap_bind($ld, $dn, $password);  
    } 
    else
    {
      $login = false;
    }
  }
  // Try bind with fixed DN
  elseif ($ldConnection)
  {
    $params = $naming."=".$username.",".$ou.",".$basedn;
    $login = @ldap_bind($ld, $params, $password);
  }
  else 
  {
    $login = false;
  }
  return $login;
}

function read_ldap_entry($db, $ld, $username)
{
  // Fetch needed config values 
  // TODO maybe just query once? Some caching in the fetch method?
  $ou     = fetch_config_value("ou", $db);
  $basedn = fetch_config_value("basedn", $db);
  $naming = fetch_config_value("ldapnaming", $db);
  $domain = fetch_config_value("ldapdomain", $db);
  if ($ou == "") {
    $filter = "userPrincipalName=".$username.'@'.$domain;
    $dn = $basedn;
  } else {
    $filter = $naming."=".$username;
    $dn = $ou.",".$basedn;
  }
  $sr = ldap_search($ld, $dn, $filter);
  return ldap_first_entry($ld, $sr);
}

function read_user_email($db, $ld, $username)
{
  global $userEntry;
  if (!$userEntry)
  {
    $userEntry = read_ldap_entry($db, $ld, $username);        
  }
  $mailAttr = fetch_config_value("ldapmail", $db);
  $mailValue = ldap_get_values($ld, $userEntry, $mailAttr);
  error_log("Retrieved mail values: ".print_r($mailValue, TRUE));
  return $mailValue[0];
}

/**
 * Check the user's attribute values against the configured inclusion value.
 * If the attribute name is empty, returns true.
 */
function validate_user_access($db, $ld, $username)
{
  $incAttr = fetch_config_value("inclusionattribute", $db);
  if (!$incAttr) {
    return true;
  }
  global $userEntry;
  if (!$userEntry)
  {
    $userEntry = read_ldap_entry($db, $ld, $username);        
  }
  $incValue = fetch_config_value("inclusionvalue", $db);
  $actual = ldap_get_values($ld, $userEntry, $incAttr);
  error_log("Retrieved inclusion values: ".print_r($actual, TRUE));
  if ($actual["count"] > 0) 
  {
    for ($i=0; $i < $actual["count"]; $i++) {
      if (strtolower($incValue) == strtolower($actual[$i])) {
        return true;
      }
    }
  } else {
    return false;
  }
}
?>
