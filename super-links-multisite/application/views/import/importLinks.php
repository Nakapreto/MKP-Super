<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$importModel = $this->importModel;

?>

<div class="wrap">
    <?php
    FormHelper::formStart($importModel);
    ?>
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= $this->pageData['pageTitle'] ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Selecione abaixo de qual plugin você deseja importar seus links')?></p>
                </div>
                <div class="col-4 text-right">&nbsp;</div>
            </div>
            <hr>
        </div>
    </div>

    <div class="col-md-12 order-md-1">
        <div class="row">
            <div class="col-md-3 mb-3">
                <?php
                $pluginsToImport = $importModel->getPluginOptionsForSelect();

                FormHelper::select(
                    $importModel,
                    'pluginToImport',
                    [],
                    $pluginsToImport
                );
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span class="small"><strong>Importante: </strong>Na lista acima, só aparecem os plugins que estão instalados e que podem ser importados para o Super Links.</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-4">
                <?php
                FormHelper::submitButton(
                    $importModel,
                    TranslateHelper::getTranslate('Gerar lista de links para importar'),
                    [
                        'class' => 'btn-success btn-sm'
                    ]
                );
                ?>
            </div>
        </div>
    </div>
</div>
