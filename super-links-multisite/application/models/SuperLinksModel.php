<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksModel extends SuperLinksCoreModel {

    private $superLinksSecretKey = '5f539fff6e5a65.95964779';
    private $superLinksServerUrl = 'http://wpsuperlinks.top';

    public function __construct() {
        parent::__construct();

        $this->setTableName(
            $this->tables['spl_linkActivation']
        );
    }

    public function getModelName(){
        return 'SuperLinksModel';
    }

    public function rules()
    {
        return [
            [
                'license_key', 'required'
            ],
        ];
    }

    public function attributeLabels()
    {
        return array(
            'license_key' => TranslateHelper::getTranslate('Licença de ativação'),
            'hp_atualizacao' => TranslateHelper::getTranslate(''),
            'active' => TranslateHelper::getTranslate('Plugin está ativo?'),
        );
    }

    public function getSecretKey(){
        return $this->superLinksSecretKey;
    }

    public function getServerUrl(){
        return $this->superLinksServerUrl;
    }

    public function isPluginActive(){
        // Versão multisite sempre ativa sem necessidade de licença
        return true;
    }

    public function verifyLicense($license = ''){
        $key = get_option('spl_code_top');

        if(isset($key) && $key){
            if($license == $key) {
                return true;
            }
        }

        return false;
    }

    public function desativaPlugin(){
        delete_option('spl_code_top');
        delete_option('spl_hpvit_top');
        delete_option('spl_licdate_top');
        wp_cache_delete('alloptions', 'options');
    }

    public function should_install() {
        $old_db_version = get_option('superLinks_db_version');

        return (SUPER_LINKS_DB_VERSION != $old_db_version);
    }

    public function verified_version() {
        $old_vr_version = get_option('superLinks_vr_version');

        return (SUPER_LINKS_VERIFIED_VERSION != $old_vr_version);
    }

    public function tlicvit($dados)
    {
        $status = false;

        if ($dados) {

            $vit = new DateTime("2022-12-13 16:17:23");

            $data = new DateTime($dados);

            if ($data <= $vit) {
                $status = true;
            }
        }

        return $status;
    }

    public function ps6licvit($dados) {
        if ($dados) {
            $dados = new DateTime($dados);

            $dados->add(new DateInterval('P6M1D'));

            $data = new DateTime();

            if ($data > $dados) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function vapsplmodelHp(){
        $retorno = array();
        $retorno['code'] = 'cancelar_atualizacao';
        $executa = false;
        $msg = '';

        if($this->isPluginActive()){
            $key = get_option('spl_code_top');
            $hp = get_option('spl_hpvit_top');
            $consultaHp = wp_remote_get('https://wpsuperlinks.top/api-atualizacao-super-links/?key='.$key.'&hp='.$hp, array('timeout' => 200, 'sslverify' => false));
            if($consultaHp){
                if (!is_wp_error($consultaHp)) {
                    $dadosHp = json_decode(wp_remote_retrieve_body($consultaHp));
                    if(isset($dadosHp->status) && $dadosHp->status) {
                        $executa = true;
                    }else{
                        $msg = isset($dadosHp->msg)? $dadosHp->msg : '';
                    }
                }
            }

            if(!$executa){
                $retorno['error'] = 'atualizacao';
                $retorno['msg'] = 'A atualização não pode ser finalizada.'.$msg.'
                                    <br>Se você já possui um HP de atualização do Super Links, <a href="'.SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links" target="_blank">clique aqui para cadastrá-lo.</a><br>
                                    <br>Se você não possui o pacote de atualizações do Super Links, <a href="https://wpsuperlinks.top/assinatura-atualizacao-hp" target="_blank"> Clique aqui para adquirir um plano de atualização.</a>';
            }
        }else{
            $retorno['error'] = 'licence';
            $retorno['msg'] = 'A atualização não pode ser feita, porque você não possui uma licença ativa nessa instalação do Super Links.<a href="'.SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links" target="_blank"> Clique aqui para ativar sua licença.</a>';
        }

        if($executa){
            return [];
        }

        return $retorno;
    }

    public function vapsplmodel(){
        $retorno = array();
        $retorno['code'] = 'cancelar_atualizacao';
        $executa = false;
        $msg = '';

        if($this->isPluginActive()){
            $key = get_option('spl_code_top');
            $hp = get_option('spl_hpvit_top');
            $consultaHp = wp_remote_get('https://wpsuperlinks.top/api-atualizacao-super-links/?key='.$key.'&hp='.$hp, array('timeout' => 200, 'sslverify' => false));
            if($consultaHp){
                if (!is_wp_error($consultaHp)) {
                    $dadosHp = json_decode(wp_remote_retrieve_body($consultaHp));
                    if(isset($dadosHp->status) && $dadosHp->status) {
                        $executa = true;
                    }else{
                        $msg = isset($dadosHp->msg)? $dadosHp->msg : '';
                    }
                }
            }

            if(!$executa){
                $retorno['error'] = 'atualizacao';
                $retorno['msg'] = 'A atualização não pode ser finalizada.'.$msg.'
                                    <br>Se você já possui um HP de atualização do Super Links, <a href="'.SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links" target="_blank">clique aqui para cadastrá-lo.</a><br>
                                    <br>Se você não possui o pacote de atualizações do Super Links, <a href="https://wpsuperlinks.top/atualizacao-assinatura-listagem" target="_blank"> Clique aqui para adquirir um plano de atualização.</a>';
            }
        }else{
            $retorno['error'] = 'licence';
            $retorno['msg'] = 'A atualização não pode ser feita, porque você não possui uma licença ativa nessa instalação do Super Links.<a href="'.SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links" target="_blank"> Clique aqui para ativar sua licença.</a>';
        }

        if($executa){
            return [];
        }

        return $retorno;
    }

    public function lic6mspl(){
        $lic6m = false;
        if($this->isPluginActive()){
            $key = get_option('spl_code_top');
            $consultaHp = wp_remote_get('https://wpsuperlinks.top/api-atualizacao-super-links/consulta_licenca.php?key='.$key, array('timeout' => 200, 'sslverify' => false));
            if($consultaHp){
                if (!is_wp_error($consultaHp)) {
                    $dadosHp = json_decode(wp_remote_retrieve_body($consultaHp));
                    if(isset($dadosHp->status) && $dadosHp->status) {
                        if(isset($dadosHp->dados) && $dadosHp->dados){
                            $lic6m = $dadosHp->dados;
                            update_option('spl_licdate_top', $lic6m);
                        }
                        if(isset($dadosHp->hp) && $dadosHp->hp){
                            $hp = $dadosHp->hp;
                            update_option('spl_hpvit_top', $hp);
                        }
                        wp_cache_delete('alloptions', 'options');
                    }
                }
            }
        }

        return $lic6m;
    }

    public function lic6validspl($hp){
        if($this->isPluginActive() && $hp){
            $key = get_option('spl_code_top');
            $consultaHp = wp_remote_get('https://wpsuperlinks.top/api-atualizacao-super-links/consulta_hp.php?key='.$key.'&hp='.$hp, array('timeout' => 200, 'sslverify' => false));
            if($consultaHp){
                if (!is_wp_error($consultaHp)) {
                    $dadosHp = json_decode(wp_remote_retrieve_body($consultaHp));
                    $status = false;
                    $msg = '';
                    if(isset($dadosHp->status)) {
                        $status = $dadosHp->status;
                    }
                    if(isset($dadosHp->msg)){
                        $msg = $dadosHp->msg;
                    }

                    return ['status' => $status, 'msg' => $msg];
                }
            }
        }

        if(!$hp){
            return ['status' => false, 'msg' => 'Não foi informado o HP.'];
        }

        return ['status' => false, 'msg' => ''];
    }

    public function verif6licspl(){
        $splic = get_option('spl_licdate_top');
        $hp = get_option('spl_hpvit_top');
        if(!$splic || !$hp) {
            $splic = $this->lic6mspl();

            if(!$splic){
                $ret['lic6m'] = false;
                $ret['vit'] = false;
                $ret['hp'] = false;
                $ret['msg'] = 'Não foi possível localizar os dados da sua licença.
                                <br><b>O Super Links continuará funcionando normalmente,</b> porém você não poderá mais obter as novas atualizações
                                <br> com melhorias do plugin enquanto não adquirir o pacote de atualizações do Super Links.
                                <br>Caso você já tenha adquirido as atualizações do Super Links, basta inserir o HP de compra no campo acima e salvar.
                                <br><a href="https://wpsuperlinks.top/assinatura-atualizacao-hp" target="_blank">Ou clique aqui para adquirir um plano de atualização.</a>';
                return $ret;
            }

            $hp = get_option('spl_hpvit_top');
            if($hp){
                $ret['lic6m'] = true;
                $ret['hp'] = $hp;
                $ret['vit'] = false;
                $ret['msg'] = '';
            }
        }

        $temAtualizacaoVitalicia = $this->tlicvit($splic);
        $jaPassouSeisMesesCompra = $this->ps6licvit($splic);

        $ret = array();
        if ($jaPassouSeisMesesCompra && !$hp) {
            $ret['lic6m'] = false;
            $ret['vit'] = false;
            $ret['hp'] = false;
            $ret['msg'] = 'Sua Licença do Super Links foi comprada há mais de 6 meses.<br>
                            <br><b>O Super Links continuará funcionando normalmente,</b> porém você não poderá mais obter as novas atualizações
                            <br> com melhorias do plugin enquanto não adquirir o pacote de atualizações do Super Links.<br>
                            <br>Caso você já tenha adquirido as atualizações do Super Links, basta inserir o HP de compra no campo acima e salvar.
                            <br><a href="https://wpsuperlinks.top/assinatura-atualizacao-hp" target="_blank">Ou clique aqui para adquirir um plano de atualização.</a>';
        }elseif($hp){
            $ret['lic6m'] = false;
            $ret['hp'] = $hp;
            $ret['vit'] = false;
            $ret['msg'] = '';
        }else{
            $ret['lic6m'] = true;
            $ret['hp'] = false;
            $ret['vit'] = false;
            $ret['msg'] = '';
        }

        if ($temAtualizacaoVitalicia) {
            $ret['lic6m'] = false;
            $ret['hp'] = false;
            $ret['vit'] = true;
            $ret['msg'] = '';
        }
        return $ret;
    }

    public function superLinks_install() {
        global $wpdb;

        if($this->should_install()) {
            $char_collation = $wpdb->get_charset_collate();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $this->createTableSplGroupLink($char_collation);
            $this->insertGeneralGroup();
            $this->createTableSplLinks($char_collation);
            $this->createTableSplAffiliateLinks($char_collation);
            $this->createTableSplLinkMetrics($char_collation);
            $this->createTableSplLinkMonitoring($char_collation);
            $this->createTableSplLinkCloak($char_collation);
            $this->createTableSplLinkConfigSocial($char_collation);
            $this->createTableSplLinkActivation($char_collation);
            $this->createTableSplLinkConfigWaitPage($char_collation);

            $this->updateTablesV101();

            $this->createTableSplAutomaticLinks($char_collation);
            $this->createTableSplAutomaticLinkMetrics($char_collation);

            $this->updateTablesV104();
            $this->updateTablesV105();
            $this->updateTablesV106();

            $this->createTableSplImport($char_collation); // v1.0.7

            $this->updateTablesV108(); // v1.0.8
            $this->updateV109(); // v1.0.9
            $this->createTableSplLinkClonePage($char_collation); // v1.0.9
            $this->createTableSplLinkCookiePage($char_collation); // v1.0.10
            $this->updateTablesV1011(); // v1.0.11
            $this->updateTablesV1013($char_collation); // v1.0.13
            $this->updateTablesV1014(); // v1.0.14
            $this->updateTablesV1015(); // v1.0.15
            $this->updateTablesV1016(); // v1.0.16
            $this->updateTablesV1017(); // v1.0.17
            $this->updateTablesV1020(); // v1.0.17
            $this->createTableCloneGroupLink($char_collation); // v1.0.21
            $this->updateTablesV1021(); // v1.0.22
            $this->updateTablesV1022(); // v1.0.23
            $this->updateTablesV1023(); // v1.0.24
            $this->updateTablesV1024(); // v1.0.25
            $this->updateTablesV1025(); // v1.0.26
            $this->updateTablesV1026(); // v1.0.28
            $this->updateTablesV1027(); // v1.0.28
            $this->createTableAutomaticGroupLink($char_collation); // v1.0.31
            $this->updateTablesV1031(); // v1.0.31
            $this->updateTablesV1032(); // v1.0.32
            $this->updateTablesV1033(); // v1.0.33
            $this->updateTablesV1034(); // v1.0.34
            $this->updateTablesV1035(); // v1.0.35
            $this->createTableSplLinkApiConvertFacebook($char_collation); // v1.0.35
            $this->updateTablesV1036(); // v1.0.36
            $this->createTableSplLinkips($char_collation); // v1.0.37
            $this->updateTablesV1038(); // v1.0.38
            $this->updateTablesV1039(); // v1.0.39
            $this->createTableSplLinkGringaPage($char_collation); // v1.0.40
            $this->updateTablesV1040(); // v1.0.40

            $this->saveDbVersion(SUPER_LINKS_DB_VERSION);
        }

        if($this->verified_version()) {
            $this->updateV109();
            $this->saveVrVersion(SUPER_LINKS_VERIFIED_VERSION);
        }
    }


    private function saveDbVersion($superLinks_db_version){
        update_option('superLinks_db_version', $superLinks_db_version);
        wp_cache_delete('alloptions', 'options');
    }

    private function saveVrVersion($superLinks_vr_version){
        update_option('superLinks_vr_version', $superLinks_vr_version);
        wp_cache_delete('alloptions', 'options');
    }

    private function createTableSplGroupLink($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_group']} (
              id int(11) NOT NULL auto_increment,
              groupName varchar(255) NOT NULL,
              defaultGroup tinyint(1) NOT NULL DEFAULT 0,
              description text default NULL,
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function insertGeneralGroup() {
        global $wpdb;
        $wpdb->insert(
            $this->tables['spl_group'],
            array(
                'groupName' => 'Geral',
                'defaultGroup' => 1
            )
        );
    }

    private function createTableSplLinks($char_collation)
    {
        global $wpdb;
        
        $sql = "CREATE TABLE {$this->tables['spl_link']} (
              id int(11) NOT NULL auto_increment,
              idGroup int(11) DEFAULT NULL,
              linkName varchar(255) NOT NULL,
              description text default NULL,
              keyWord varchar(255) DEFAULT NULL,
              redirectType varchar(255) DEFAULT 'html',
              redirectDelay int(2) DEFAULT 1,
              statusLink varchar(64) DEFAULT 'enabled',
              abLastTest int(2) NOT NULL DEFAULT 0,
              createdAt datetime NOT NULL,
              updatedAt datetime DEFAULT NULL,
              PRIMARY KEY  (id),
              KEY statusLink (statusLink),
              KEY redirectType (redirectType(191)),
              KEY createdAt (createdAt),
              KEY updatedAt (updatedAt)
            ) {$char_collation};";

        dbDelta($sql);
        
        // Adiciona FOREIGN KEY separadamente se não existir
        $foreign_key_exists = $wpdb->get_var("SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '{$this->tables['spl_link']}' AND COLUMN_NAME = 'idGroup' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (!$foreign_key_exists) {
            $wpdb->query("ALTER TABLE {$this->tables['spl_link']} ADD CONSTRAINT fk_link_group FOREIGN KEY (idGroup) REFERENCES {$this->tables['spl_group']}(id)");
        }
    }

    private function createTableSplAffiliateLinks($char_collation)
    {
        global $wpdb;
        
        $sql = "CREATE TABLE {$this->tables['spl_affiliateLink']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              affiliateUrl tinytext NOT NULL,
              createdAt datetime NOT NULL,
              updatedAt datetime default NULL,
              PRIMARY KEY (id),
              KEY createdAt (createdAt),
              KEY updatedAt (updatedAt)
            ) {$char_collation};";

        dbDelta($sql);
        
        // Adiciona FOREIGN KEY separadamente se não existir
        $foreign_key_exists = $wpdb->get_var("SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '{$this->tables['spl_affiliateLink']}' AND COLUMN_NAME = 'idLink' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (!$foreign_key_exists) {
            $wpdb->query("ALTER TABLE {$this->tables['spl_affiliateLink']} ADD CONSTRAINT fk_affiliate_link FOREIGN KEY (idLink) REFERENCES {$this->tables['spl_link']}(id) ON UPDATE CASCADE ON DELETE CASCADE");
        }
    }

    private function createTableSplLinkMetrics($char_collation) {
        global $wpdb;
        
        $sql = "CREATE TABLE {$this->tables['spl_linkMetrics']} (
              id int(11) NOT NULL auto_increment,
              idAffiliateLink int(11) NOT NULL,
              accessTotal int(11) NOT NULL DEFAULT 0,
              uniqueTotalAccesses int(11) NOT NULL DEFAULT 0,
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);
        
        // Adiciona FOREIGN KEY separadamente se não existir
        $foreign_key_exists = $wpdb->get_var("SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '{$this->tables['spl_linkMetrics']}' AND COLUMN_NAME = 'idAffiliateLink' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (!$foreign_key_exists) {
            $wpdb->query("ALTER TABLE {$this->tables['spl_linkMetrics']} ADD CONSTRAINT fk_metrics_affiliate FOREIGN KEY (idAffiliateLink) REFERENCES {$this->tables['spl_affiliateLink']}(id) ON UPDATE CASCADE ON DELETE CASCADE");
        }
    }

    private function createTableSplLinkMonitoring($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkMonitoring']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              googleMonitoringID varchar(255) DEFAULT '',
              pixelID varchar(255) DEFAULT '',
              track varchar(255) DEFAULT '',
              codeHeadPage text DEFAULT '',
              codeBodyPage text DEFAULT '',
              codeFooterPage text DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkCloak($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkCloak']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              statusCloak varchar(64) DEFAULT 'disabled',
              connection1 varchar(255) DEFAULT '',
              connection2 varchar(255) DEFAULT '',
              connection3 varchar(255) DEFAULT '',
              connection4 varchar(255) DEFAULT '',
              connectionRedirectUrl varchar(255) DEFAULT '',
              defaultRedirectUrl varchar(255) DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkConfigSocial($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkConfigSocial']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              textTitle varchar(255) DEFAULT '',
              description text DEFAULT '',
              image varchar(255) DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkActivation($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkActivation']} (
              id int(11) NOT NULL auto_increment,
              active int(11) NOT NULL DEFAULT 0,
              license_key varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkConfigWaitPage($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkWaitPage']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              textLoadPage varchar(255) DEFAULT '',
              showSpinner varchar(100) DEFAULT 'yes',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkApiConvertFacebook($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_apiConvertFacebook']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              eventNameApiFacebook varchar(255) NOT NULL DEFAULT '',
              eventIdApiFacebook varchar(255) DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function updateTablesV101(){
        global $wpdb;

        //Inclui campo para funcionalidade de redirect no botão voltar - apenas se não existir
        $column_exists = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM {$this->tables['spl_link']} LIKE %s", 'redirectBtn'));
        if (!$column_exists) {
            $sql = "ALTER TABLE {$this->tables['spl_link']} add redirectBtn varchar(255) DEFAULT '' ";
            $wpdb->query($sql);
        }
    }

    private function updateTablesV104(){
        global $wpdb;

        //Inclui campo para funcionalidade de redirect no botão voltar - apenas se não existir
        $column_exists = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM {$this->tables['spl_link']} LIKE %s", 'enableRedirectJavascript'));
        if (!$column_exists) {
            $sql = "ALTER TABLE {$this->tables['spl_link']} add enableRedirectJavascript varchar(64) DEFAULT 'disabled'";
            $wpdb->query($sql);
        }
    }

    private function updateTablesV105(){
        global $wpdb;

        //remove os insert groups padrão iniciais
        $wpdb->delete(
            $this->tables['spl_group'],
            array(
                'defaultGroup' => 1
            )
        );
    }

    private function updateTablesV106(){
        global $wpdb;

        $this->loadDataByID('1');
        $superLinksDataActivation = $this->getAttributes();


        if(isset($superLinksDataActivation['license_key']) && isset($superLinksDataActivation['active']) && $superLinksDataActivation['active'] && $superLinksDataActivation['license_key']){
            $licence = $superLinksDataActivation['license_key'];

            update_option('spl_code_top', $licence);
            wp_cache_delete('alloptions', 'options');
        }

        sleep(2);

        $sql = "ALTER TABLE {$this->tables['spl_linkActivation']} drop column license_key";
        $wpdb->query($sql);
    }

    private function updateTablesV108(){
        global $wpdb;

        //altera tamanho do campo de url de afiliado
        $sql = "ALTER TABLE {$this->tables['spl_affiliateLink']} modify affiliateUrl text NOT NULL";
        $wpdb->query($sql);
    }

    private function updateV109()
    {
        $superlinksModel = new SuperLinksModel();
        $license_key = get_option('spl_code_top');

        if (!$license_key) {
            return;
        }

        $paramsActivation = array(
            'slm_action' => 'slm_check',
            'secret_key' => $superlinksModel->getSecretKey(),
            'license_key' => $license_key
        );

        $query = esc_url_raw(add_query_arg($paramsActivation, $superlinksModel->getServerUrl()));
        $response = wp_remote_get($query, array('timeout' => 200, 'sslverify' => false));
        $license_data = json_decode(wp_remote_retrieve_body($response));

        if($license_data && isset($license_data->status) && $license_data->status == 'blocked') {
            $superlinksModel->desativaPlugin();
        }
    }

    private function createTableSplLinkClonePage($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_clonePageLinks']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              pageItem varchar(255) DEFAULT '',
              newItem varchar(255) DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplLinkCookiePage($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_cookieLinks']} (
              id int(11) NOT NULL auto_increment,
              cookieName varchar(255) default '',
              idPost text,
              linkSuperLinks text,
              statusCookie varchar(64) DEFAULT 'enabled',
              timeCookie varchar(100) DEFAULT '',
              urlCookie text,
              redirect varchar(64) DEFAULT 'disabled',
              urlCamuflada text,
              qtdAcessos int(11) NOT NULL DEFAULT 0,
              activeWhen varchar(100) DEFAULT '',
              PRIMARY KEY (id)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function updateTablesV1011(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_clonePageLinks']} add typeItem varchar(255) DEFAULT 'link' "; // link, image
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_link']} add htmlClonePage longtext";
        $wpdb->query($sql);
    }

    private function updateTablesV1013($char_collation){
        global $wpdb;

        $sql = "CREATE TABLE {$this->tables['spl_cookieGroup']} (
              id int(11) NOT NULL auto_increment,
              groupName varchar(255) NOT NULL,
              defaultGroup tinyint(1) NOT NULL DEFAULT 0,
              description text default NULL,
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);


        $sql = "ALTER TABLE {$this->tables['spl_cookieLinks']} add idGroup int(11) DEFAULT NULL";
        $wpdb->query($sql);

        // Adiciona FOREIGN KEY com nome de constraint
        $sql = "ALTER TABLE {$this->tables['spl_cookieLinks']} ADD CONSTRAINT fk_cookie_group FOREIGN KEY (idGroup) REFERENCES ".$this->tables['spl_cookieGroup']."(id)";
        $wpdb->query($sql);
    }

    private function updateTablesV1014(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_cookieLinks']} add idPage text";
        $wpdb->query($sql);

    }

    private function createTableSplAutomaticLinks($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_automaticLinks']} (
                id int(11) NOT NULL auto_increment,
                page_id mediumint(9),
                title varchar(255) NOT NULL,
                keywords varchar(255) NOT NULL,
                url varchar(255) NOT NULL,
                num smallint(5) NOT NULL DEFAULT 1,
                target varchar(255) NOT NULL default '_self',
                nofollow tinyint(1) NOT NULL default 0,
                active tinyint(1) NOT NULL default 1,
                partly_match tinyint(1) NOT NULL default 0,
                titleattr varchar(255),
                PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplAutomaticLinkMetrics($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_automaticMetrics']} (
              id int(11) NOT NULL auto_increment,
              idAutomaticLink int(11) NOT NULL,
              idPost int(11) NOT NULL,
              keyword varchar (255) NOT NULL,
              accessTotal int(11) NOT NULL DEFAULT 0,
              uniqueTotalAccesses int(11) NOT NULL DEFAULT 0,
              PRIMARY KEY  (id),
              FOREIGN KEY (idAutomaticLink) REFERENCES " . $this->tables['spl_automaticLinks'] . "(id) on update cascade on delete cascade,
              KEY idAutomaticLink (idAutomaticLink),
              KEY idPost (idPost),
              KEY keyword (keyword)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function createTableSplImport($char_collation) {
        global $wpdb;
        
        $sql = "CREATE TABLE {$this->tables['spl_importLinks']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              pluginToImport varchar (255) NOT NULL,
              idLinkInPlugin varchar(255) NOT NULL,
              createdAt datetime NOT NULL,
              PRIMARY KEY  (id),
              KEY idLink (idLink),
              KEY pluginToImport (pluginToImport),
              KEY idLinkInPlugin (idLinkInPlugin),
              KEY createdAt (createdAt)
            ) {$char_collation};";

        dbDelta($sql);
        
        // Adiciona FOREIGN KEY separadamente se não existir
        $foreign_key_exists = $wpdb->get_var("SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '{$this->tables['spl_importLinks']}' AND COLUMN_NAME = 'idLink' AND REFERENCED_TABLE_NAME IS NOT NULL");
        if (!$foreign_key_exists) {
            $wpdb->query("ALTER TABLE {$this->tables['spl_importLinks']} ADD CONSTRAINT fk_import_link FOREIGN KEY (idLink) REFERENCES {$this->tables['spl_link']}(id) ON UPDATE CASCADE ON DELETE CASCADE");
        }
    }

    private function updateTablesV1015(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add saveHtmlClone varchar(64) DEFAULT 'enabled'";
        $wpdb->query($sql);
    }

    private function updateTablesV1016(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add trackGoogle varchar(255) DEFAULT ''";
        $wpdb->query($sql);
    }

    private function updateTablesV1017(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add enableProxy varchar(64) DEFAULT 'disabled'";
        $wpdb->query($sql);
    }

    private function updateTablesV1020(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add numberWhatsapp varchar(64) DEFAULT ''";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_link']} add textWhatsapp text DEFAULT ''";
        $wpdb->query($sql);
    }

    private function createTableCloneGroupLink($char_collation) {
        global $wpdb;
        $subquery = "SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_NAME =  '{$this->tables['spl_link']}'
                    AND COLUMN_NAME =  'idGroup'";

        $constraitName = $wpdb->get_row($subquery);
        if($constraitName && isset($constraitName->CONSTRAINT_NAME)) {
            $constraitName = $constraitName->CONSTRAINT_NAME;

            if($constraitName) {
                $sqlRemove = "ALTER TABLE {$this->tables['spl_link']} DROP FOREIGN KEY " . $constraitName . ";";
                $wpdb->query($sqlRemove);
            }
        }

        $sql = "CREATE TABLE {$this->tables['spl_cloneGroup']} (
              id int(11) NOT NULL auto_increment,
              groupName varchar(255) NOT NULL,
              defaultGroup tinyint(1) NOT NULL DEFAULT 0,
              description text default NULL,
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);

        $old_db_version = get_option('superLinks_db_version');

        if($old_db_version < '1.0.23') {
            $categoriesLinks = new SuperLinksGroupLinkModel();
            $allCategoriesLinks = $categoriesLinks->getAllData();

            foreach ($allCategoriesLinks as $groupLink) {
                if (!$groupLink->defaultGroup) {
                    $cloneGroup = new SuperLinksCloneGroupModel();
                    $cloneGroup->setAttribute('id', $groupLink->id);
                    $cloneGroup->setAttribute('groupName', $groupLink->groupName);
                    $cloneGroup->setAttribute('defaultGroup', $groupLink->defaultGroup);
                    $cloneGroup->setAttribute('description', $groupLink->description);
                    $cloneGroup->save();
                }
            }
        }
    }

    private function updateTablesV1021(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add idPage text DEFAULT ''";
        $wpdb->query($sql);
    }

    private function updateTablesV1022(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add idPopupDesktop int(11) DEFAULT null";
        $wpdb->query($sql);
        $sql = "ALTER TABLE {$this->tables['spl_link']} add idPopupMobile int(11) DEFAULT null";
        $wpdb->query($sql);
    }

    private function updateTablesV1023(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add exitIntentPopup varchar(64) DEFAULT 'disabled'";
        $wpdb->query($sql);
        $sql = "ALTER TABLE {$this->tables['spl_link']} add loadPopupAfterSeconds int(11) DEFAULT 0";
        $wpdb->query($sql);
    }

    private function updateTablesV1024(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add popupBackgroundColor varchar(255) DEFAULT 'rgba(255, 255, 255, 100)'";
        $wpdb->query($sql);
        $sql = "ALTER TABLE {$this->tables['spl_link']} add popupAnimation varchar(255) DEFAULT 'none'";
        $wpdb->query($sql);
    }

    private function updateTablesV1025(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add compatibilityMode varchar(64) DEFAULT 'disabled'";
        $wpdb->query($sql);
    }

    private function updateTablesV1026(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add forceCompatibility varchar(64) DEFAULT 'enabled'";
        $wpdb->query($sql);
    }

    private function updateTablesV1027(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add counterSuperEscassez varchar(64) DEFAULT '0'";
        $wpdb->query($sql);
    }

    private function createTableAutomaticGroupLink($char_collation){
        $sql = "CREATE TABLE {$this->tables['spl_automaticGroup']} (
              id int(11) NOT NULL auto_increment,
              groupName varchar(255) NOT NULL,
              defaultGroup tinyint(1) NOT NULL DEFAULT 0,
              description text default NULL,
              PRIMARY KEY  (id)
            ) {$char_collation};";

        dbDelta($sql);
    }

    private function updateTablesV1031(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_automaticLinks']} add idGroup int(11) DEFAULT NULL";
        $wpdb->query($sql);
    }

    private function updateTablesV1032(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add alertaConversoes varchar(64) DEFAULT '0'";
        $wpdb->query($sql);
    }

    private function updateTablesV1033(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add rgpd varchar(64) DEFAULT '0'";
        $wpdb->query($sql);
    }

    private function updateTablesV1034(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_affiliateLink']} MODIFY affiliateUrl text";
        $wpdb->query($sql);
    }

    private function updateTablesV1035(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add testEventApiFacebook varchar(255) DEFAULT ''";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add tokenApiFacebook text DEFAULT ''";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add enableApiFacebook varchar(64) NOT NULL DEFAULT 'disabled'";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add pixelApiFacebook varchar(255) DEFAULT ''";
        $wpdb->query($sql);

        $sql = "ALTER TABLE {$this->tables['spl_linkMonitoring']} add logErrorApiFacebook text DEFAULT ''";
        $wpdb->query($sql);
    }

    private function updateTablesV1036(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add renovaHtmlClone varchar(255) DEFAULT 'disabled'";
        $wpdb->query($sql);
    }

	private function createTableSplLinkips($char_collation) {
		$sql = "CREATE TABLE {$this->tables['spl_linkIps']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              ipClient varchar(255) NOT NULL DEFAULT '',
              blocked varchar(255) NOT NULL DEFAULT False,
              url text DEFAULT '',
              datasAcesso text DEFAULT '',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

		dbDelta($sql);
	}

	private function updateTablesV1038(){
		global $wpdb;

		$sql = "ALTER TABLE {$this->tables['spl_link']} add opniaoClientePgClonada varchar(255) DEFAULT 'sim'";
		$wpdb->query($sql);
	}

	private function updateTablesV1039(){
		global $wpdb;

		$sql = "ALTER TABLE {$this->tables['spl_link']} add removerPixelPgClonada varchar(255) DEFAULT 'enabled'";
		$wpdb->query($sql);
	}

    private function createTableSplLinkGringaPage($char_collation) {
        $sql = "CREATE TABLE {$this->tables['spl_linkGringaPage']} (
              id int(11) NOT NULL auto_increment,
              idLink int(11) NOT NULL,
              checkoutProdutor text DEFAULT '',
              linkPaginaVenda varchar(255) DEFAULT '',
              tempoRedirecionamentoCheckout varchar(255) DEFAULT '1',
              textoTempoRedirecionamento varchar(255) DEFAULT '',
              abrirPaginaBranca varchar(255) DEFAULT 'disabled',
              PRIMARY KEY  (id),
              FOREIGN KEY (idLink) REFERENCES " . $this->tables['spl_link'] . "(id) on update cascade on delete cascade
            ) {$char_collation};";

        dbDelta($sql);
    }


    private function updateTablesV1040(){
        global $wpdb;

        $sql = "ALTER TABLE {$this->tables['spl_link']} add usarEstrategiaGringa varchar(255) DEFAULT 'no'";
        $wpdb->query($sql);
    }
}