<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class SuperLinksController extends SuperLinksFramework
{
    protected $superLinksModel;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->setScenario('super_links_activation');

        $this->setModel($model);
        $this->loadModel();

        $this->init($hooks, $filters);
    }

    public function init($hooks = [], $filters = []){
        $hooks = array_merge($hooks, $this->basicHooks());
        $filters = array_merge($filters, $this->basicFilters());

        parent::init($hooks, $filters);
    }

    private function basicHooks()
    {
        return [];
    }

    private function basicFilters()
    {
        return [];
    }

    public function index()
    {
        $superLinksModel = new SuperLinksModel();
        $lic = false;

        if($superLinksModel->isPluginActive()){
            $lic = $superLinksModel->verif6licspl();
            $this->pageData['lic'] = $lic;
            if (isset($_POST['scenario'])) {
                $superLinksModel->setAttributes($_POST[$superLinksModel->getModelName()]);

                $hp = $superLinksModel->getAttribute('hp_atualizacao');
                $dadosHp = $superLinksModel->lic6validspl($hp);
                if(isset($dadosHp['status']) && $dadosHp['status']) {
                    $toast = '';
                    $typeToast = 'success';
                    $timeToExpire = time() + 60;
                    $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links';
                    if ($hp) {
                        $hp = trim($hp);
                        $this->upVitllinks($hp);
                        $retorno = $superLinksModel->vapsplmodelHp();

                        if ($retorno) {
                            $toast = isset($retorno['msg']) ? $retorno['msg'] : 'Erro';
                            $typeToast = 'error';
                        } else {
                            $toast = TranslateHelper::getTranslate('O HP foi salvo com sucesso!');
                        }
                    } else {
                        $toast = TranslateHelper::getTranslate('Não foi digitado um HP válido. Por favor tente novamente.');
                        $typeToast = 'error';
                    }
                }else{
                    $msg = isset($dadosHp['msg'])? $dadosHp['msg'] : 'Houve algum erro. Por favor tente novamente.';
                    $toast = TranslateHelper::getTranslate($msg);
                    $typeToast = 'error';
                }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.cookie = \"typeToastSPL=$typeToast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }
        }

        $this->render(SUPER_LINKS_VIEWS_PATH . '/admin/index.php');
    }

    public function activation(){
        // Método de ativação desabilitado na versão multisite
        // Plugin sempre ativo - redireciona para a página principal
        wp_redirect(admin_url("admin.php?page=super_links"));
        exit();
    }

    // Métodos de ativação e desativação comentados para versão multisite
    // O plugin não requer mais ativação por licença
    /*
    private function doActivation(){
        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Ativação');
        // ... código de ativação removido para versão multisite
    }

    private function doDeactivation(){
        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desativação');
        // ... código de desativação removido para versão multisite
    }
    */

    private function upllinks($licence = null){
        if(!is_null($licence)) {
            update_option('spl_code_top', $licence);
            wp_cache_delete('alloptions', 'options');
        }
    }

    private function upVitllinks($hp = null){
        if(!is_null($hp)) {
            update_option('spl_hpvit_top', $hp);
            wp_cache_delete('alloptions', 'options');
        }
    }

    public function config()
    {
        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Configuração');
        if(isset($_POST['enableRedis'])){
            $enableRedis = ($_POST['enableRedis'] == 'sim')? true : false;

            update_option('enable_redis_superLinks', $enableRedis);
            wp_cache_delete('alloptions', 'options');
            $toast = TranslateHelper::getTranslate('A opção foi salva com sucesso!');
            $timeToExpire = time() + 60;

            echo "<script> document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\"; </script>";
        }

        if(isset($_POST['facebookVerification'])){
            $facebookVerification = $_POST['facebookVerification'];
            $facebookVerification = stripslashes($facebookVerification);
            $facebookVerification = str_replace('<meta name="facebook-domain-verification" content="', "", $facebookVerification);
            $facebookVerification = str_replace('" />', "", $facebookVerification);

            $facebookVerification = str_replace("<meta name='facebook-domain-verification' content='", "", $facebookVerification);
            $facebookVerification = str_replace("' />", "", $facebookVerification);
            update_option('facebookVerificationSPL', $facebookVerification);
            wp_cache_delete('alloptions', 'options');
            $toast = TranslateHelper::getTranslate('A Configuração de Validação de domínio foi salva com sucesso!');
            $timeToExpire = time() + 60;

            echo "<script> document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\"; </script>";
        }
        $this->render(SUPER_LINKS_VIEWS_PATH . '/admin/config.php');
    }

}