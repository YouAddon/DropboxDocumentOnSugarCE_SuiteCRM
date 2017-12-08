<?php
/**
 * Created by Caro Team (info@carocrm.com).
 * User: Jacky (jacky@carocrm.com).
 * Year: 2017
 * File: dropbox.php
 */

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once 'include/connectors/sources/default/source.php';

/**
 * Class ext_eapm_dropbox
 */
class ext_eapm_dropbox extends source
{
    protected $_enable_in_wizard = false;
    protected $_enable_in_hover = false;
    protected $_has_testing_enabled = false;
    protected $_required_config_fields = [
        'oauth2_client_id',
        'oauth2_client_secret'
    ];

    public $field_types = [
        'upload_to_admin' => 'checkbox'
    ];

    /** {@inheritdoc} */
    public function getItem($args = array(), $module = null)
    {

    }

    /** {@inheritdoc} */
    public function getList($args = array(), $module = null)
    {

    }

}