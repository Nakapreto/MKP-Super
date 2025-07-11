<?php
if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}
$superLinksModel = new SuperLinksModel();
$superLinksModel->setAttribute('hp_atualizacao',get_option('spl_hpvit_top'));
$lic = isset($this->pageData['lic'])? $this->pageData['lic'] : array();
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

<div class="modal fade" id="popupSuperPresellAds" tabindex="-1" role="dialog" aria-labelledby="popupSuperPresellAds" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <img src="<?=SUPER_LINKS_IMAGES_URL?>/logo-super-presell.png" class="img-fluid" style="max-width: 200px;">
            </div>
            <div class="modal-body">
                <h5 class="spartanextrabold" style="color:#000000;">CONHEÇA O SUPER PRESELL</h5>
                <div class="mt-2 mb-2">
                    O plugin perfeito para criação de páginas de presell e quizzes.
                    <br><br>
                    Acredite ou não, você não vai mais precisar gastar horas para criar páginas de presell ou quizz para seus anúncios, o que é perfeito inclusive para o mercado gringo.
                    <br><br>
                    Com o Super Presell você poderá, facilmente, criar páginas de presell e Quizz em segundos, sem ter que gastar tempo editando HTML ou usando programas complicados.
                    <br><br>
                    Clique no botão abaixo para saber mais.
                </div>
                <div class="mt-4 mb-2 text-center">
                    <a href="https://superpresell.top" target="_blank" class="btn btn-success text-center">QUERO CONHECER O SUPER PRESELL</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?php TranslateHelper::printTranslate('Parabéns por ter adquirido o plugin Super Links')?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Este é o melhor plugin para camuflar, clonar e permitir seus anúncios em tráfego pago')?></p>
                </div>
                <div class="col-4 text-right">
                    <a class="btn btn-success btn-sm" href="admin.php?page=super_links_activation">
						<?php $superLinksModel->isPluginActive()? TranslateHelper::printTranslate('Desativar a licença nesse wordpress') :  TranslateHelper::printTranslate('Ativar a licença nesse wordpress') ?>
                    </a>
                </div>
            </div>
            <hr>
        </div>

        <?php
            if($superLinksModel->isPluginActive()){
                if(isset($lic['vit']) && $lic['vit']){
                ?>
                    <div class="mt-2 mb-5">
                        <div class="py-1">
                            <div class="row">
                                <div class="col-8">
                                    <h4><?php TranslateHelper::printTranslate('ATUALIZAÇÕES DO PLUGIN')?></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <?php
                                        TranslateHelper::printTranslate('Sua licença te dá direito as atualizações vitalícias do Super Links. 
                                        <br>Por isso, você não está vendo aqui o campo para adicionar HP de compra das atualizações do plugin.
                                        <br>Sempre que houver uma nova atualização, você poderá instalar normalmente.
                                        ');
                                    ?>
                                    <br>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }else if(isset($lic['lic6m']) && $lic['lic6m']){
                ?>

                    <div class="mt-2 mb-5">
                        <div class="py-1">
                            <div class="row">
                                <div class="col-8">
                                    <h4><?php TranslateHelper::printTranslate('ATUALIZAÇÕES DO PLUGIN')?></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <?php
                                    TranslateHelper::printTranslate('Você possui as atualizações do Super links válidas no momento.
                                    <br>Por isso, você não está vendo aqui o campo para adicionar HP de compra das atualizações do plugin.
                                    ');
                                    ?>
                                    <br>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }else if(isset($lic['hp']) && $lic['hp']){
                    ?>

                    <div class="mt-2 mb-5">
                        <div class="py-1">
                            <div class="row">
                                <div class="col-8">
                                    <h4><?php TranslateHelper::printTranslate('ATUALIZAÇÕES DO PLUGIN')?></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <?php
                                        TranslateHelper::printTranslate('Você possui um HP válido de assinatura do pacote de atualizações do Super links.
                                        <br>Por isso, você não está vendo aqui o campo para adicionar HP de compra das atualizações do plugin.
                                        <br>Sempre que houver uma nova atualização, você poderá instalar normalmente.
                                    ');
                                    ?>
                                    <br>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }else{
                    $msg = '';
                    if(isset($lic['msg']) && $lic['msg']){
                        $msg = $lic['msg'];
                    }
        ?>
            <div class="mt-2 mb-5">
                <div class="py-1">
                    <div class="row">
                        <div class="col-8">
                            <h3><?php TranslateHelper::printTranslate('HP DE COMPRA DA ATUALIZAÇÃO DO PLUGIN')?></h3>
                        </div>
                    </div>

                    <?php
                    FormHelper::formStart($superLinksModel);
                    ?>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <?php
                            FormHelper::text(
                                $superLinksModel,
                                'hp_atualizacao',
                                [
                                    'feedback' => [
                                        'invalid-text' => TranslateHelper::getTranslate('É necessário preencher este campo com o HP de compra das atualizações do plugin.')
                                    ]
                                ]
                            );
                            ?>
                        </div>
                        <div class="col-md-12 mt-2">
                            <?php
                            FormHelper::submitButton(
                                $superLinksModel,
                                TranslateHelper::getTranslate('Salvar HP'),
                                [
                                    'class' => 'btn-success btn-sm'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                    <?php
                    FormHelper::formEnd();
                    ?>
                </div>
                <br>
                <p class="small"><a href="https://wpsuperlinks.top/video-assinatura-atualizacao" target="_blank"><?php TranslateHelper::printTranslate('Clique aqui')?></a> <?php TranslateHelper::printTranslate('para saber mais sobre as Atualizações do Super Links.')?>
                    <br>
                    <?=$msg?>
                </p>
                <br>
                <hr>
            </div>
        <?php
                }
            }
        ?>
        <div>
            <div>
                <div class="alert alert-primary col-md-8" role="alert">
                    Saiba em primeira mão as novidades e atualizações do Super Links -> <a href="https://wpsuperlinks.top/novidades" target="_blank" class="btn btn-primary btn-sm">Clique aqui</a>
                </div>
            </div>
            <div>
                <div class="card col-md-8">
                    <div class="card-body">
                        <h5 class="card-title text-info">Importante:</h5>
                        <div>
                            <div>
                                Para utilizar corretamente todas as funcionalidades do Super links, acesse nossa área de membros e assista as vídeo-aulas feitas especialmente para
                                você.
                                <div class="mt-3">
                                    <a href="https://wpsuperlinks.top/membros" target="_blank" class="btn btn-primary btn-sm">Clique aqui para acessar</a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <p>Na nossa área de membros, você tem acesso ao nosso suporte, caso necessite.</p>
                    </div>
                </div>
            </div>

            <br>
            <hr>
            <h5>Avalie o nosso plugin</h5>
            <hr>

            <h6>Dê uma força, o seu feedback significa tudo para nós. Se gostou do nosso plugin avalie com 5 estrelas <span style="display:inline-block;padding:5px 10px;background:#333;border-radius:5px;color:#FFCC00;font-size:18px;margin:0 5px;">★ ★ ★ ★ ★</span></h6>
            <p>Para avaliar é muito simples, basta <a href="https://purchase.hotmart.com/purchase" target="_blank">clicar aqui</a> efetuar login na hotmart e clicar em cima do plugin Super Links. No lado direito clique no botão "avalie o produto", dê sua nota e deixe o seu comentário dizendo o que achou do plugin.</p>
            <p>Além de ficarmos muito agradecidos, você estará ajudando outras pessoas dando o seu feedback.</p>

            <br>
            <hr>
            <h5>Crie links especiais para Facebook, Clone páginas, rastreie e muito mais...</h5>
            <hr>

            <p>
                O Super Links permite que você crie links usando seu próprio nome de domínio que redirecionam para seus links de afiliado ou clonam páginas na internet.
                Você pode inserir códigos de rastreio do Facebook e Google de forma simples e fácil. E ainda monitorar a quantidade acessos que cada link obteve.
            </p>
            <p>
                Além disso, ainda é possível camuflar seus links para que usuários de determinados países sejam redirecionados para seus links de afiliados, enquanto robôs e
                outros acessos (Facebook, por exemplo) visualizem outra página que você configurar.
            </p>
        </div>
    </div>
</div>

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
            var existeCookieSM = getCookieSUPERLINKSc('exibeVitalicioLinks');

            if (hoje <= dataLimite) {
                jQuery('#popupSuperVitalicio').modal('show');
                setCookieSUPERLINKSc('exibeVitalicioLinks', 'cookieSPresell', 1);
            }else{
                if(!existeCookieSM) {
                    jQuery('#popupSuperPresellAds').modal('show');
                    setCookieSUPERLINKSc('exibeVitalicioLinks', 'cookieSPresell', 1);
                }
            }
        });

        function setCookieSUPERLINKSc(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookieSUPERLINKSc(cname) {
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