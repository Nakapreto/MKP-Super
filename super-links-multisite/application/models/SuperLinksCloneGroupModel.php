<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksCloneGroupModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_cloneGroup']
        );
    }

    public function getModelName(){
        return 'SuperLinksCloneGroupModel';
    }

    public function rules()
    {
        return [
            [
                'groupName', 'uniqueGroup'
            ]
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('Categoria do link'),
            'groupName' => TranslateHelper::getTranslate('Nome da categoria'),
            'defaultGroup' => TranslateHelper::getTranslate('Categoria padrão?'),
            'description' => TranslateHelper::getTranslate('Descrição'),
            'addLinksCategory' => TranslateHelper::getTranslate('Selecione uma categoria para os links')
        );
    }

    public function getAllGroupsValues(){
        $values = [
            ['selected' => true, 'text' => TranslateHelper::getTranslate('Sem categoria'), 'val' => '']
        ];

        $allGroups = $this->getAllData();

        foreach($allGroups as $group){
            $values[] = ['selected' => false, 'text' => $group->groupName, 'val' => $group->id];
        }

        return $values;
    }

    public function uniqueGroup($attribute = ''){
        $attributeVal = $this->getAttribute($attribute);
        return (empty($this->getGroupByGroupName($attributeVal)) || $this->isTheSameGroup())? true : false;
    }

    public function getGroupByGroupName($groupName = ''){
        if(empty($groupName)){
            return [];
        }

        return $this->getAllDataByParam($groupName,'groupName');
    }

    private function isTheSameGroup(){

        $groupName = $this->getAttribute('groupName');
        $id = $this->getAttribute('id');

        if(!$id) {
            return false;
        }

        $groupData = $this->getAllDataByParam($id,'id');
        if($groupData) {
            $groupData = array_shift($groupData);
        }

        if(isset($groupData->groupName) && (trim($groupData->groupName) == trim($groupName))){
            return true;
        }

        return false;
    }
}