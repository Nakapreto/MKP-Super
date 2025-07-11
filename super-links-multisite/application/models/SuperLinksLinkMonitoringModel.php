<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkMonitoringModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkMonitoring']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkMonitoringModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Métrica'),
            'idLink' => TranslateHelper::getTranslate('ID do Link'),
            'googleMonitoringID' => TranslateHelper::getTranslate('Código de acompanhamento do Google'),
            'pixelID' => TranslateHelper::getTranslate('ID do pixel do Facebook'),
            'track' => TranslateHelper::getTranslate('Nome do Track Facebook (opcional)'),
            'codeHeadPage' => TranslateHelper::getTranslate('Outros códigos para inserir no cabeçalho'),
            'codeBodyPage' => TranslateHelper::getTranslate('Outros códigos para inserir no corpo'),
            'codeFooterPage' => TranslateHelper::getTranslate('Outros códigos para inserir no rodapé'),
            'trackGoogle' => TranslateHelper::getTranslate('Código do evento de conversão do Google'),
            'enableApiFacebook' => TranslateHelper::getTranslate('Api de Conversão do Facebook'),
            'tokenApiFacebook' => TranslateHelper::getTranslate('Token da Api'),
            'testEventApiFacebook' => TranslateHelper::getTranslate('Código do Evento de teste'),
            'pixelApiFacebook' => TranslateHelper::getTranslate('Pixel do evento'),
        );
    }
}