<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksImportModel extends SuperLinksCoreModel {

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_importLinks']
        );
    }

    public function getModelName(){
        return 'SuperLinksImportModel';
    }

    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Importação'),
            'idLink' => TranslateHelper::getTranslate('Link'),
            'pluginToImport' => TranslateHelper::getTranslate('Importar do plugin'),
            'idLinkInPlugin' => TranslateHelper::getTranslate('Link do plugin importado')
        );
    }

    //Verifica se a tabela do plugin existe no banco
    private function existPlugin($plugin = ''){
        global $wpdb;

        $coreModel = new SuperLinksImportCoreModel();
        $tableName = $coreModel->getTableNameByPluginSlug($plugin);
        $dbname = $wpdb->dbname;

        $sql = "SELECT COUNT(1) as existPlugin FROM information_schema.tables WHERE table_schema='$dbname' AND table_name='$tableName'";
        $existTable = $wpdb->get_results($sql);
        $existTable = array_shift($existTable);

        if($existTable->existPlugin){
            return true;
        }

        return false;
    }

    public function pluginsToImport(){
        $plugins = [
            'prettyLinks' => 'Pretty Links',
            'hotLinksPlus' => 'Hot Links Ninja'
        ];

        $allPlugins = [];

        foreach($plugins as $key => $plugin){
            if($this->existPlugin($key)) {
                $allPlugins[$key] = $plugin;
            }
        }

        return $allPlugins;
    }

    public function getPluginOptionsForSelect(){
        $plugins = $this->pluginsToImport();
        $options[] = ['selected' => true, 'text' => 'Selecione', 'val' => ''];

        foreach($plugins as $key => $plugin){
            $options[] = ['text' => $plugin, 'val' => $key];
        }

        return $options;
    }

}