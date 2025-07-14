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
        $html = self::clean_cloned_html($html, $source_url);
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

    /**
     * Limpa e adapta o HTML clonado: remove rastreadores, reescreve links de recursos, etc.
     */
    private static function clean_cloned_html($html, $source_url) {
        // Remove rastreadores conhecidos
        $html = preg_replace('/<script[^>]*>.*?(googletagmanager|facebook\\.com.*pixel|tiktok\\.com.*sdk|gtag\\().*?<\\/script>/is', '', $html);
        // Remove script litespeed_vary
        $html = preg_replace('/<script[^>]*>var litespeed_vary[^<]*<\\/script>/i', '', $html);
        // Remove popups e scripts de RGPD conhecidos
        $html = preg_replace('/<script[^>]*>.*?(cookie(consent|bot)|lgpd|rgpd|privacidade|privacy).*?<\\/script>/is', '', $html);
        $html = preg_replace('/<div[^>]+(cookie(consent|bot)|lgpd|rgpd|privacidade|privacy)[^>]*>.*?<\\/div>/is', '', $html);
        // Remove metatags indesejadas (generator, og:*, fb:*)
        $html = preg_replace('/<meta[^>]+(generator|og:|fb:)[^>]*>/i', '', $html);
        // Reescreve links relativos de CSS/JS para absolutos
        $html = preg_replace_callback('/(<link[^>]+href=["\\\'])([^"\\\']+)(["\\\'])/i', function($m) use ($source_url) {
            $abs = self::make_absolute_url($m[2], $source_url);
            return $m[1] . $abs . $m[3];
        }, $html);
        $html = preg_replace_callback('/(<script[^>]+src=["\\\'])([^"\\\']+)(["\\\'])/i', function($m) use ($source_url) {
            $abs = self::make_absolute_url($m[2], $source_url);
            return $m[1] . $abs . $m[3];
        }, $html);
        // Reescreve imagens
        $html = preg_replace_callback('/(<img[^>]+src=["\\\'])([^"\\\']+)(["\\\'])/i', function($m) use ($source_url) {
            $abs = self::make_absolute_url($m[2], $source_url);
            return $m[1] . $abs . $m[3];
        }, $html);
        // Garante que o favicon seja absoluto
        $html = preg_replace_callback('/(<link[^>]+rel=["\"](icon|shortcut icon)["\"][^>]+href=["\"])([^"\"]+)(["\"])/i', function($m) use ($source_url) {
            $abs = self::make_absolute_url($m[3], $source_url);
            return $m[1] . $abs . $m[4];
        }, $html);
        // Garante que o <title> e meta description sejam preservados
        // (neste caso, apenas garantimos que não sejam removidos)
        return $html;
    }

    /**
     * Torna um link relativo em absoluto, baseado na URL de origem
     */
    private static function make_absolute_url($url, $base) {
        if (preg_match('/^https?:\/\//i', $url)) return $url;
        $parsed = parse_url($base);
        $host = $parsed['scheme'] . '://' . $parsed['host'];
        if (strpos($url, '/') === 0) {
            return $host . $url;
        } else {
            $path = isset($parsed['path']) ? dirname($parsed['path']) : '';
            return $host . $path . '/' . ltrim($url, '/');
        }
    }
}