<?php

$connector_strings = array(
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel">
Obtain a API Key and Secret from Dropbox by registering your Sugar instance as a new application.
<br/><br>Steps to register your instance:
<br/><br/>
<ol>
<li>Go to the Dropbox Developers site:
<a href=\'https://www.dropbox.com/developers/apps\'
target=\'_blank\'>https://www.dropbox.com/developers/apps</a>.</li>

<li>Sign In using the Dropbox account under which you would like to register the application.</li>
<li>Create App</li>
<li>Choose an API</li>
<li>Choose the type of access you need</li>
<li>Enter a Project Name and click create.</li>
<li>Add Redirect URIs: {$SITE_URL}/index.php?module=EAPM&action=DropboxOauth2Redirect</li>
<li>Copy the App key and App secret into the boxes below</li>

</li>
</ol>
</td></tr>
</table>',
    'oauth2_client_id' => 'App key',
    'oauth2_client_secret' => 'App secret',
    'upload_to_admin' => 'Only upload to admin Dropbox',
);
