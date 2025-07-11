<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

/**
 * Class SuperLinksImportHotLinksModel
 * HotLinks Plus Last Version Tested: 100.2.0
 */

class SuperLinksImportHotLinksModel extends SuperLinksImportCoreModel {

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_hotLinks_Link']
        );
    }

    public function getModelName(){
        return 'SuperLinksImportHotLinksModel';
    }

    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return array(
          'id_link'  => '',
		  'url_afiliado'  => '',
		  'palavra_chave'  => '',
		  'descricao'  => '',
		  'auto_create'  => '',
		  'auto_create_' => '',
		  'popup'  => '',
		  'popup_code'  => '',
		  'total_acessos'  => '',
		  'face_pixel'  => '',
		  'nome_link'  => '',
		  'max_replaces'  => '',
		  'redirecionamento'  => '',
		  'popup_camuflagem'  => '',
		  'abrir_automatic'  => '',
		  'url_automatic' => '',
		  'ativar_automatic' => '',
		  'segundos_apos_popup'  => '',
		  'posicao_imagem_link'  => '',
		  'segundos_apos_banner'  => '',
		  'codigo_oculto'  => '',
		  'titulo'  => '',
		  'imagem'  => '',
		  'descricao_publica'  => '',
		  'redirecionar'  => '',
		  'ativar_cloak'  => '',
		  'url_fora_br' => '',
		  'url_no_br' => '',
		  'id_projeto'  => '',
		  'url_afiliado2' => '',
		  'ativar_parametros_url' => '',
		  'google_pixel'  => '',
		  'from_country'  => '',
		  'total_acessos_unicos'  => '',
		  'last_teste_ab'  => '',
		  'url_afiliado3' => '',
		  'ativar_metricas' => '',
		  'ativar_rastreio_cookie'  => '',
		  'redir_apos'  => '',
		  'so_ultima_origem'  => '',
		  'codigo_topo'  => '',
		  'redir_mensagem' => '',
		  'redir_gif'  => '',
		  'redir_codigo'  => '',
		  'ativar_share'  => '',
		  'share_gif'  => '',
		  'ativar_barra'  => '',
		  'ativar_contador'  => '',
		  'cor_barra'  => '',
		  'cor_contador'  => '',
		  'tempo_contador'  => '',
		  'texto_barra'  => '',
		  'texto_botao'  => '',
		  'cor_texto_barra'  => '',
		  'cor_texto_botao'  => '',
		  'link_botao' => '',
		  'cor_botao'  => '',
		  'ativar_back_redir'  => '',
		  'url_back_redir' => '',
		  'mob_redir'  => '',
		  'link_banner'  => '',
		  'ativar_intenc_sair'  => '',
		  'pop_intenc_sair'  => '',
		  'exibir_periodo'  => '',
		  'periodo_url' => '',
		  'dedata'  => '',
		  'atedata'  => '',
		  'url_intenc_sair' => '',
		  'tempo_barra'  => '',
		  'from_country2'  => '',
		  'from_country3'  => '',
		  'from_country4'  => '',
		  'ativar_turbo'  => '',
		  'turbo_url' => '',
		  'redirect_clique'  => '',
		  'clique_url' => '',
		  'redirect_segundo'  => '',
		  'segundo_url' => '',
		  'passar_dispositivo'  => '',
		  'acao_cloak_br'  => '',
		  'acao_cloak_fora_br'  => '',
		  'segundos_apos_turbo'  => '',
		  'tipo_modo'  => '',
		  'turbo_img'  => '',
        );
    }


    public function getDataHotLinks(){
        $data['links'] = $this->getAllData('id_link');
        $projects = $this->getDataFromProjects();
        $allProjects = [];

        if($projects) {
            foreach ($projects as $project) {
                $allProjects[$project->id_projeto] = $project->nm_projeto;
            }
        }

        $data['projects'] = $allProjects;
        return $data;
    }

    private function getDataFromProjects(){
        // id_projeto, nm_projeto, descricao
        $hotLinksProjects = new SuperLinksImportHotLinksModel();
        $hotLinksProjects->setTableName($this->tables['spl_hotLinks_Project']);
        return $hotLinksProjects->getAllData('id_projeto');
    }

    public function getRedirectTypeSuperLinks($redirectHotLinks = 0){
        switch ($redirectHotLinks) {
            case 1:
                return 'camuflador';
            case 2:
                return 'javascript';
            case 3:
                return 'php';
            default:
                return 'html';
        }
    }
}