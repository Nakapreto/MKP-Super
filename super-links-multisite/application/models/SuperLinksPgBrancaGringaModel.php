<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksPgBrancaGringaModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkGringaPage']
        );
    }

    public function getModelName(){
        return 'SuperLinksPgBrancaGringaModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Métrica'),
            'idLink' => TranslateHelper::getTranslate('ID do Link ou Página'),
            'checkoutProdutor' => TranslateHelper::getTranslate('Link do Checkout do produtor'),
            'linkPaginaVenda' => TranslateHelper::getTranslate('Link de afiliado para marcação do cookie'),
            'tempoRedirecionamentoCheckout' => TranslateHelper::getTranslate('Tempo para redirecionar'),
            'textoTempoRedirecionamento' => TranslateHelper::getTranslate('Texto para ser exibido no redirecionamento'),
            'abrirPaginaBranca' => TranslateHelper::getTranslate('Mostrar página do Produtor durante o redirecionamento?')
        );
    }

    public function getPaginaByIds($idLink, $idPgBranca){
        if(!$idLink || !$idPgBranca){
            return false;
        }

        $dadosPaginaBranca = $this->getAllDataByParam($idPgBranca,'id');

        if($dadosPaginaBranca){
            $dadosPaginaBranca = array_shift($dadosPaginaBranca);
            if($dadosPaginaBranca->idLink == $idLink){
                return $dadosPaginaBranca;
            }
        }

        return false;
    }

    public function updateLinkPgBranca(){
        $idSuperLink = $this->getAttribute('idLink');

        $cloneLinks = $this->getAllDataByParam($idSuperLink,'idLink');

        if($cloneLinks){
            $cloneLinks = array_shift($cloneLinks);
            $this->setIsNewRecord(false);
            $this->setAttribute('id', $cloneLinks->id);
        }

        $this->save();
    }
}