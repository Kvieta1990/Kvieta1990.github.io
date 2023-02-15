<?php
//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
$ds = ldap_connect("ldaps://ldapx.ornl.gov:636");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
$person = "apw247";

$dn = "dc=xcams,dc=ornl,dc=gov";
$filter="(|(uid=$person*))";
$justthese = array("ou", "sn", "givenname", "mail");

$sr=ldap_search($ds, $dn, $filter, $justthese);

$info = ldap_get_entries($ds, $sr);

echo $ds;
echo $info["count"]." entries returned\n";
echo $info[0]["sn"][0];

$params = "uid=".$person.",ou=Users,".$dn;
echo $params;
$password = "my_passwd";
$login = @ldap_bind($ds, $params, $password);

if ($login == true) {
    echo "Hello!";
} else {
    echo "Failed!";
}

?>