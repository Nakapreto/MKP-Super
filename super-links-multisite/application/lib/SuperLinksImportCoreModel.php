<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksImportCoreModel {

    protected $tables= [];
    protected $attributes = [];
    private $tableName = null;

    public function __construct() {
        global $wpdb;

        $this->tables['spl_prettyLinks_Link'] = $wpdb->prefix . "prli_links";
        $this->tables['spl_hotLinks_Link'] = $wpdb->prefix . "Anderson_Makiyama_Hot_Links_Plus_links";
        $this->tables['spl_hotLinks_Project'] = $wpdb->prefix . "Anderson_Makiyama_Hot_Links_Plus_proj";
    }

    public function getTableNameByPluginSlug($plugin = ''){
        switch ($plugin){
            case 'hotLinksPlus':
                return $this->tables['spl_hotLinks_Link'];
            default:
                return $this->tables['spl_prettyLinks_Link'];
        }
    }

    protected function setTableName($tableName){
        $this->tableName = trim($tableName);
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getAttribute($name)
    {
        if(isset($this->attributes[$name])){
            return $this->attributes[$name];
        }
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function loadDataByID($id = null){
        $tableName = $this->getTableName();

        if(is_null($tableName) || is_null($id)){
            return [];
        }

        global $wpdb;

        $dataArray = [];
        $dataObject = $wpdb->get_row( $wpdb->prepare(" SELECT * FROM $tableName where id = %d ", $id));

        if($dataObject) {
            $dataArray = get_object_vars($dataObject);
        }

        $this->setAttributes($dataArray);
    }

    public function getAllDataByParam($val = null, $param = '', $order = '', $limit = '', $offset = ''){
        global $wpdb;
        $tableName = $this->getTableName();

        if(is_null($tableName) || is_null($val) || empty($param)){
            return [];
        }

        $tmp = '%d';

        if(is_string($val)){
            $tmp = '%s';
        }

        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName where $param = $tmp $order $limit $offset", $val));
    }

    public function getAllData($param = 'id'){
        $tableName = $this->getTableName();

        if(is_null($tableName)){
            return [];
        }

        global $wpdb;

        return $wpdb->get_results(" SELECT * FROM $tableName ORDER BY $param ASC");
    }

    public function setAttributesKeys($attributesLabel = []){
        $this->attributesKeys = array_keys($attributesLabel);
    }
}