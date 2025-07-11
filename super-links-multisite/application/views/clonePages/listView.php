<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$pageTitle = $this->pageData['pageTitle'];
$addLinkModel = $this->addLinksModel;

$existCategory = $this->pageData['existCategory'];
?>

<div class="modal fade" id="popupSuperVitalicio" tabindex="-1" role="dialog" aria-labelledby="popupSuperVitalicio" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <h5 class="spartanextrabold" style="color:#000000; text-align: center;">QUER TER ACESSO VITALÍCIO?</h5>
                <div class="mt-2 mb-2">
                    <a href="https://nodz.top/mvd-super-links" target="_blank" class="btn btn-success text-center"><img src="<?=SUPER_LINKS_IMAGES_URL?>/vitalicio-img.webp" class="img-fluid"></a>
                </div>
                <div class="mt-4 mb-2 text-center">
                    <a href="https://nodz.top/mvd-super-links" target="_blank" class="btn btn-success text-center">SAIBA MAIS SOBRE O ACESSO VITALÍCIO</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($existCategory) {
?>

    <div class="wrap">
        <div class="container">
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-8">
                        <h3><?= $pageTitle ?></h3>
                        <p class="small"><?php TranslateHelper::printTranslate('Clique no botão amarelo do lado direito de cada categoria para ver as páginas associados a ela.') ?></p>
                    </div>
                    <div class="col-4 text-right">
                        <a class="btn btn-success btn-sm"
                           href="admin.php?page=super_links_addClone"><?= TranslateHelper::getTranslate('Clonar nova página') ?></a>
                    </div>
                </div>
                <hr>
            </div>
            <div id="listCategories">
                <?php $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/listViewCategorized.php'); ?>
            </div>
        </div>
    </div>

<?php
}else{
        $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/listViewLinks.php');
    }
?>

<?php
$addLinksModel = new SuperLinksAddLinkModel();
$dataPresell = $addLinksModel->getAllData();
$existePresellCriada = true;
if(!$dataPresell){
    $existePresellCriada = false;
}

if($existePresellCriada){
    ?>
    <script type="application/javascript">
        jQuery(document).ready(function() {
            jQuery(document).ready(function() {
                var hoje = new Date();
                var dataLimite = new Date(2025, 0, 21);

                if (hoje <= dataLimite) {
                    var existeCookieSM = getCookieSUPERPRESELLc('exibeVitalicioLinks1');

                    if (!existeCookieSM) {
                        jQuery('#popupSuperVitalicio').modal('show');
                        setCookieSUPERPRESELLc('exibeVitalicioLinks1', 'cookieSPresell', 1);
                    }
                }
            });

            function setCookieSUPERPRESELLc(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                let expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }

            function getCookieSUPERPRESELLc(cname) {
                let name = cname + "=";
                let ca = document.cookie.split(';');
                for(let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        });
    </script>

    <?php
}
?>