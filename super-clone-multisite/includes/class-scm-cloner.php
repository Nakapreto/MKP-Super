<?php
if (!defined('ABSPATH')) exit;

class SCM_Cloner {
    public static function activate() {
        // Código de ativação do plugin
    }
    public static function deactivate() {
        // Código de desativação do plugin
    }

    /**
     * Clona uma página apenas para o site/subdomínio atual
     * @param string $source_url URL da página a ser clonada
     * @param string $target_slug Slug da nova página
     * @return string Mensagem de sucesso ou erro
     */
    public static function clone_site($source_url, $target_slug) {
        if (!filter_var($source_url, FILTER_VALIDATE_URL)) {
            return 'URL de origem inválida.';
        }
        if (empty($target_slug)) {
            return 'Slug de destino não pode ser vazio.';
        }
        // Baixar HTML da página de origem
        $response = wp_remote_get($source_url, [
            'timeout' => 30,
            'user-agent' => 'Mozilla/5.0 (WordPress Super Clone Multisite)'
        ]);
        if (is_wp_error($response) || empty($response['body'])) {
            return 'Erro ao baixar o conteúdo da URL de origem.';
        }
        $html = $response['body'];
        // Cria/atualiza a página apenas no site atual
        $existing = get_page_by_path($target_slug, OBJECT, 'page');
        $page_data = [
            'post_title'   => ucfirst(str_replace('-', ' ', $target_slug)),
            'post_name'    => $target_slug,
            'post_content' => $html,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ];
        if ($existing) {
            $page_data['ID'] = $existing->ID;
            wp_update_post($page_data);
        } else {
            wp_insert_post($page_data);
        }
        return "Página clonada e publicada neste subdomínio.";
    }
}