<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
if(!SUPER_LINKS_PERMALINK_OK){
    $linkNotificacao = SUPER_LINKS_PAGE_INICIAL;
?>
<script type="application/javascript">
    document.cookie = "toastSPL=Alguns problemas foram encontrados na configuração do seu Wordpress. <a href='<?=SUPER_LINKS_PAGE_INICIAL?>'>Clique aqui</a> para ver as ações necessárias.; expires=60; path=/";
    document.cookie = "typeToastSPL=error; expires=60; path=/";
    execNotify()
</script>
<?php
}
?>
<p>
    <?php echo SUPER_LINKS_DISPLAY_NAME . ' - ' . TranslateHelper::getTranslate('Version') . ': ' . SUPER_LINKS_VERSION; ?>
</p>
