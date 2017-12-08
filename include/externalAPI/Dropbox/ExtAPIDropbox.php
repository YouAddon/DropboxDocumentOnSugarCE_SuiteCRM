<?php
/**
 * Created by Caro Team (info@carocrm.com).
 * User: Jacky (jacky@carocrm.com).
 * Year: 2017
 * File: ExtAPIDropbox.php
 */

require_once 'include/externalAPI/Base/ExternalAPIBase.php';
require_once 'include/externalAPI/Base/WebDocument.php';
require_once "include/externalAPI/Dropbox/dropbox-sdk-php116/lib/Dropbox/autoload.php";

use \Dropbox as dbx;


class ExtAPIDropbox extends ExternalAPIBase implements WebDocument
{
    private $config;
    private $appInfo;
    private $webAuth;

    public $supportedModules = array('Documents', 'Import');
    public $authMethod = 'oauth2';
    public $connector = 'ext_eapm_google';

    public $useAuth = true;
    public $requireAuth = true;

    public $authorizeUrl = null;
    public $client = null;

    public function __construct()
    {
        $this->config = $this->getDropboxOauth2Config();

        $this->appInfo = dbx\AppInfo::loadFromJson([
            'key' => $this->config['properties']['oauth2_client_id'],
            'secret' => $this->config['properties']['oauth2_client_secret']
        ]);

        $redirectUri = $this->config['redirect_uri'];
        $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox_auth_csrf_token');

        $this->webAuth = new dbx\WebAuth($this->appInfo, 'YouAddOn', $redirectUri, $csrfTokenStore);
    }

    public function getLoginInfo($is_user = true)
    {
        if ($is_user) {
            return EAPM::getLoginInfo('Dropbox');
        }

        $config = $this->getDropboxOauth2Config();

        if (isset($config['properties']['upload_to_admin']) && $config['properties']['upload_to_admin']) {
            $eapmBean = new EAPM();
            $queryArray = array(
                'assigned_user_id' => '1',
                'application' => 'Dropbox',
                'deleted' => 0
            );

            $eapmBean = $eapmBean->retrieve_by_string_fields($queryArray, false);

            return $eapmBean;
        }

        $eapmBean = EAPM::getLoginInfo('Dropbox');

        return $eapmBean;
    }

    public function getDropboxOauth2Config()
    {
        $config = array();

        if (is_file('custom/modules/Connectors/connectors/sources/ext/eapm/dropbox/config.php')) {
            require 'custom/modules/Connectors/connectors/sources/ext/eapm/dropbox/config.php';
        } else {
            require 'modules/Connectors/connectors/sources/ext/eapm/dropbox/config.php';
        }

        $config['redirect_uri'] = rtrim(SugarConfig::getInstance()->get('site_url'), '/')
            . '/index.php?module=EAPM&action=DropboxOauth2Redirect';

        return $config;
    }

    public function createAuthUrl()
    {
        return $this->authorizeUrl;
    }

    public function setClient()
    {
        $eapm = $this->getLoginInfo(false);
        if ($eapm && !empty($eapm->api_data)) {
            $accessToken = $eapm->api_data;
            $this->client = new dbx\Client($accessToken, 'YouAddOn');
        }
    }

    public function getClient()
    {
        $this->authorizeUrl = $this->webAuth->start();

        $this->setClient();

        return $this;
    }

    protected function saveToken($accessToken)
    {
        global $current_user;
        $bean = $this->getLoginInfo();
        if (!$bean) {
            $bean = BeanFactory::getBean('EAPM');
            $bean->assigned_user_id = $current_user->id;
            $bean->application = 'Dropbox';
            $bean->validated = true;
        }

        $bean->api_data = $accessToken;
        $bean->save();
    }

    public function authenticate($authCode)
    {
        try {
            list($accessToken, $userId, $urlState) = $this->webAuth->finish($authCode);
            assert($urlState === null);
        } catch (dbx\WebAuthException_BadRequest $ex) {
            // Respond with an HTTP 400 and display error page...
            return [0, "400: bad request: " . $ex->getMessage()];
        } catch (dbx\WebAuthException_BadState $ex) {
            // Auth session expired.  Restart the auth process.
            return [0, '401 Auth session expired.  Restart the auth process.'];
        } catch (dbx\WebAuthException_Csrf $ex) {
            // Respond with HTTP 403 and display error page...
            return [0, "403 CSRF mismatch: " . $ex->getMessage()];
        } catch (dbx\WebAuthException_NotApproved $ex) {
            return [0, "Not approved: " . $ex->getMessage()];
        } catch (dbx\WebAuthException_Provider $ex) {
            return [0, "Error redirect from Dropbox: " . $ex->getMessage()];
        } catch (dbx\Exception $ex) {
            return [0, "Error communicating with Dropbox API: " . $ex->getMessage()];
        }

        if ($accessToken) {
            $this->saveToken($accessToken);
        }

        return [1, $accessToken];
    }

    public function uploadDoc($bean, $fileToUpload, $docName, $mineType)
    {
        $this->setClient();
        $f = fopen($fileToUpload, 'rb');
        $result = $this->client->uploadFile('/' . $docName, dbx\WriteMode::add(), $f);
        fclose($f);

        $bean->doc_id = $result['rev'];
        $bean->doc_url = $this->client->createShareableLink($result['path']);

        return array(
            'success' => true,
        );
    }

    public function downloadDoc($documentId, $documentFormat)
    {
        // TODO: Implement downloadDoc() method.
    }

    public function shareDoc($documentId, $emails)
    {
        // TODO: Implement shareDoc() method.
    }

    public function deleteDoc($documentId)
    {
        // TODO: Implement deleteDoc() method.
    }

    public function searchDoc($keywords, $flushDocCache = false)
    {
        // TODO: Implement searchDoc() method.
    }
}