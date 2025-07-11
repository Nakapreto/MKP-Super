<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

if(!isset($this->pageData['dadosPaginaGringa'])){
    die('.');
}

$dadosPaginaGringa = $this->pageData['dadosPaginaGringa'];
$idLinkCheckout = $this->pageData['idLinkCheckout'];

if(!$idLinkCheckout){
    $idLinkCheckout = 0;
}

$pageTitle = isset($this->pageData['pageTitle']) ? $this->pageData['pageTitle'] : get_bloginfo('name');

$url = $this->pageData['url'];

$checkoutProdutor = isset($dadosPaginaGringa->checkoutProdutor)? $dadosPaginaGringa->checkoutProdutor : '';

$linkPaginaVenda = isset($dadosPaginaGringa->linkPaginaVenda)? $dadosPaginaGringa->linkPaginaVenda : '';

$tempoRedirecionamentoCheckout = isset($dadosPaginaGringa->tempoRedirecionamentoCheckout)? $dadosPaginaGringa->tempoRedirecionamentoCheckout : '1';

$tempoRedirecionamentoCheckout = $tempoRedirecionamentoCheckout * 1000;

$textoTempoRedirecionamento = isset($dadosPaginaGringa->textoTempoRedirecionamento)? $dadosPaginaGringa->textoTempoRedirecionamento : '';

$abrirPaginaBranca = isset($dadosPaginaGringa->abrirPaginaBranca)? $dadosPaginaGringa->abrirPaginaBranca : 'disabled';

if($checkoutProdutor){
    $checkoutProdutor = unserialize($checkoutProdutor);
}

$checkoutProdutor = $checkoutProdutor[$idLinkCheckout];

$faviconBlog = "";
if(get_site_icon_url()){
    $faviconBlog = ' <link rel="shortcut icon" href="'.get_site_icon_url().'" />';
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>

    <meta name="robots" content="noindex">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">

    <title><?= $pageTitle ?></title>

    <?=$faviconBlog?>

    <meta itemprop="name" content="<?= $pageTitle ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?=SUPER_LINKS_CSS_URL?>/camuflador.css' media='all' />
    <link rel='stylesheet' href='<?=SUPER_LINKS_CSS_URL?>/html.css' media='all' />
</head>
<body>

<?php
    if ($abrirPaginaBranca == 'disabled') {
        ?>
        <div class="center-height">
            <div class="text-center top-distance">
                <?php
                if($abrirPaginaBranca == 'disabled'){
                    echo '<div class="spinner-border"></div>';
                    if($textoTempoRedirecionamento) {
                        echo '
                        <div>
                            <span class="small">' . $textoTempoRedirecionamento . '</span>
                        </div>
                    ';
                    }
                }
                ?>
            </div>
        </div>
<?php
        if($linkPaginaVenda) {
            echo '<iframe src="' . $linkPaginaVenda . '"  style="width:0;height:0;border:0"></iframe>';
        }
    }else{
        if($linkPaginaVenda) {
            echo '<iframe src="' . $linkPaginaVenda . '" class="iframe" height="100%" width="100%" noresize="noresize"></iframe>';
        }
    }
?>

<script>
    setTimeout( function(){
        document.location = "<?=$checkoutProdutor?>";
    }, <?=$tempoRedirecionamentoCheckout?>);
</script>

</body>
</html>