<?php

/**
 * Created by Caro Team (info@carocrm.com).
 * User: Jacky (jacky@carocrm.com).
 * Year: 2017
 * File: DropboxOauth2Redirect.php
 */

class EAPMViewDropboxOauth2Redirect extends SugarView
{
    public function __construct($bean = null, array $view_object_map = array())
    {
        $this->options = array();
        parent::__construct($bean, $view_object_map);
    }

    public function process()
    {
        global $sugar_config;

        require_once 'include/externalAPI/Dropbox/ExtAPIDropbox.php';
        $api = new ExtAPIDropbox();

        if ($_GET['code']) {
            $token = $api->authenticate([
                'state' => $_GET['state'],
                'code' => $_GET['code']
            ]);

            if ($token[0] == 1) {
                $response = array(
                    'result' => true,
                    'hasRefreshToken' => true,
                );
            } else {
                $response = array(
                    'result' => false,
                );
            }
        } else {
            $response = array(
                'result' => false,
            );
        }

        $this->ss->assign('response', $response);
        $this->ss->assign('siteUrl', $sugar_config['site_url']);
        $this->ss->display('custom/modules/EAPM/tpls/DropboxOauth2Redirect.tpl');
    }
}