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
                    <p class="small"><?php TranslateHelper::printTranslate('Clique no botão abaixo para desativar a Licença.')?></p>
                </div>
            </div>
            <hr>

            <input type="text" style="display:none;" name="<?=$superLinksModel->getModelName()?>[license_key]" value="<?=$superLinksModel->getAttribute('license_key')?>">

            <div class="row">
                <div class="col-md-5 mb-3">
					<?php
					FormHelper::text(
						$superLinksModel,
						'license_key',
						[
							'feedback' => [
								'invalid-text' => TranslateHelper::getTranslate('É necessário preencher este campo com a chave que você recebeu')
							],
							'disabled' => 'disabled',
							'id' => 'licence_key'
						]
					);
					?>
                </div>
                <div class="col-md-12 mt-4">
					<?php
					FormHelper::submitButton(
						$superLinksModel,
						TranslateHelper::getTranslate('Desativar'),
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
</div>