<?php
if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}
$superLinksModel = $this->superLinksModel;

?>

<div class="wrap">
	<?php
	FormHelper::formStart($superLinksModel);
	?>
    <div class="container">
        <div class="py-1">
            <div class="row">
                <div class="col-8">
                    <h3><?= $this->pageData['pageTitle'] ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Preencha os campos abaixo para ativar o plugin')?></p>
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col-md-5 mb-3">
					<?php
					FormHelper::text(
						$superLinksModel,
						'license_key',
						[
							'feedback' => [
								'invalid-text' => TranslateHelper::getTranslate('É necessário preencher este campo com a chave que você recebeu')
							]
						]
					);
					?>
                </div>
                <div class="col-md-12 mt-4">
					<?php
					FormHelper::submitButton(
						$superLinksModel,
						TranslateHelper::getTranslate('Ativar'),
						[
							'class' => 'btn-success btn-sm'
						]
					);
					?>
                </div>
            </div>

        </div>
    </div>
	<?php
	FormHelper::formEnd();
	?>

    <div class="container">
        <div class="py-1 mt-5">
            <div class="row justify-content-end">
                <div class="col-12">
                    <h3><?php TranslateHelper::printTranslate('Caso você não consiga ativar o plugin, verifique os passos abaixo.')?></h3>
                </div>
                <div class="card col-12">
                    <div class="card-body">
                        <h5 class="card-title text-info">Primeiro Passo: Verifique a configuração dos seus links permanentes</h5>
                        <div>
                            <div>

                                ATENÇÃO: Essa configuração precisa estar marcada para estrutura de "NOME DO POST" (POST NAME).
                                <br><br>
                                Caso esteja marcada em outra configuração, altere para "nome do post" e salve a configuração.
                                <br><br> Em seguida tente ativar o plugin com a sua licença.
                                <br><br>
                                <div class="mt-3">
                                    <a href="options-permalink.php" class="btn btn-primary btn-sm">Clique aqui para ir aos links permanentes</a>
                                </div>
                                <br>
                                Caso o botão acima não funcione, vá ao menu lateral do seu Wordpress e procure por "Configurações", em seguida procure por "Links Permanentes".
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-12">
                    <div class="card-body">
                        <h5 class="card-title text-info">Segundo Passo: Verifique o SSL</h5>
                        <div>
                            <div>

                                ATENÇÃO: Esse passo só deve ser executado se você já estiver feito o passo anterior e mesmo assim continuar não ativando o plugin.
                                <br><br>
                                Assista o terceiro e último vídeo do tutorial de ativação clicando no botão abaixo.
                                <br><br>
                                <div class="mt-3">
                                    <a href="https://wpsuperlinks.top/faq/?p=25" target="_blank" class="btn btn-primary btn-sm">Acessar o tutorial</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>

</div>
