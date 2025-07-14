<?php
if (!defined('ABSPATH')) exit;

add_action('network_admin_menu', function() {
    add_menu_page(
        'Super Clone Multisite',
        'Super Clone',
        'manage_network',
        'super-clone-multisite',
        'scm_admin_page_render',
        'dashicons-admin-multisite'
    );
});

function scm_admin_page_render() {
    if (!is_super_admin()) {
        wp_die('Acesso restrito ao administrador da rede.');
    }
    $msg = '';
    if (isset($_POST['scm_clone_submit'])) {
        $source_url = esc_url_raw($_POST['scm_source_url']);
        $target_slug = sanitize_title($_POST['scm_target_slug']);
        $post_status = in_array($_POST['scm_post_status'], ['publish','draft']) ? $_POST['scm_post_status'] : 'publish';
        $msg = SCM_Cloner::clone_site($source_url, $target_slug, $post_status);
    }
    ?>
    <div class="wrap">
        <h1>Super Clone Multisite</h1>
        <?php if ($msg) echo '<div class="notice notice-info"><p>' . esc_html($msg) . '</p></div>'; ?>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="scm_source_url">URL de Origem</label></th>
                    <td><input name="scm_source_url" type="url" id="scm_source_url" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="scm_target_slug">Slug do Novo Site/Página</label></th>
                    <td><input name="scm_target_slug" type="text" id="scm_target_slug" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="scm_post_status">Status da Página</label></th>
                    <td>
                        <select name="scm_post_status" id="scm_post_status">
                            <option value="publish">Publicar imediatamente</option>
                            <option value="draft">Salvar como rascunho</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="scm_clone_submit" id="scm_clone_submit" class="button button-primary" value="Clonar"></p>
        </form>
    </div>
    <?php
}