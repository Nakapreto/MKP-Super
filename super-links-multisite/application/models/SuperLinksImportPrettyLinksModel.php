<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

/**
 * Class SuperLinksImportPrettyLinksModel
 * Pretty Links Last Version Tested: 3.1.1
 */
class SuperLinksImportPrettyLinksModel extends SuperLinksImportCoreModel {

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_prettyLinks_Link']
        );
    }

    public function getModelName(){
        return 'SuperLinksImportPrettyLinksModel';
    }

    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return array(
            'id' => '',
            'name' => '',
            'description' => '',
            'url' => '',
            'slug' => '',
            'nofollow' => '',
            'sponsored' => '',
            'track_me' => '',
            'param_forwarding' => '',
            'param_struct' => '',
            'redirect_type' => '',
            'link_status' => '',
            'created_at' => '',
            'updated_at' => '',
            'group_id' => '',
            'link_cpt_id' => '',
        );
    }


    public function getDataPrettyLinks(){
        $data['links'] = $this->getAllData();

        return $data;
    }
}