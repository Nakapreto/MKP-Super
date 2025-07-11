<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$automaticLinksModel = $this->automaticLinksModel;
$groupLinkModel = $this->groupLinkModel;
?>

    <div class="wrap">
        <?php
        FormHelper::formStart($automaticLinksModel);
        ?>
        <div class="container">
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-8">
                        <h3><?= $this->pageData['pageTitle'] ?></h3>
                        <p class="small"><?php TranslateHelper::printTranslate('Preencha os campos abaixo para criar links inteligentes nos seus textos')?></p>
                    </div>
                    <div class="col-4 text-right">
                        <?php
                        FormHelper::submitButton(
                            $automaticLinksModel,
                            TranslateHelper::getTranslate('Salvar link'),
                            [
                                'class' => 'btn-success btn-sm',
                                'id' => 'submitForm'
                            ]
                        );
                        ?>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="col-md-12 order-md-1">
            <div class="accordion col-md-12" id="super-links-config-box">
                <div class="card col-md-12 no-margin no-padding">
                    <div class="card-header" id="headingOne">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <?php TranslateHelper::printTranslate('Dados do link')?>
                            </button>
                        </h2>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <?php
                                    $values = [
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => '1'],
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Não'), 'val' => '0'],
                                    ];

                                    FormHelper::select(
                                        $automaticLinksModel,
                                        'active',
                                        [],
                                        $values
                                    );
                                    ?>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <?php
                                    $values = $groupLinkModel->getAllGroupsValues();

                                    FormHelper::select(
                                        $groupLinkModel,
                                        'id',
                                        [],
                                        $values,
                                        'Categoria do link'
                                    );
                                    ?>
                                    <span id="spinner"></span>
                                    <a href="#" data-toggle="modal" data-target="#newGroupLink">Clique aqui para adicionar nova categoria</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $automaticLinksModel,
                                        'title',
                                        [
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('Um nome de link é obrigatório o preenchimento para identificar o link criado')
                                            ]
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="col-md-4">
                                    <?php
                                    FormHelper::number(
                                        $automaticLinksModel,
                                        'num',
                                        [
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('Esse campo deve ser um número')
                                            ]
                                        ]
                                    );
                                    ?>
                                    </div>
                                    <span class="small">O valor 0 (zero) coloca como ilimitado o número de links gerados automaticamente no texto</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $automaticLinksModel,
                                        'url',
                                        [
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('É necessária um link para redirecionar quando a palavra-chave for encontrada')
                                            ]
                                        ]
                                    );
                                    ?>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <?php
                                    $values = [
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Nova aba'), 'val' => '_blank'],
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Mesma aba'), 'val' => '_self'],
                                    ];

                                    FormHelper::select(
                                        $automaticLinksModel,
                                        'target',
                                        [],
                                        $values
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="<?=$automaticLinksModel->getModelName()?>[keywords]"><?= $automaticLinksModel->attributeLabels()['keywords'] ?></label>
                                    <div class="input-group">
                                        <div id="automaticLinkskeywords" class="table-bordered" style="width: 100%; min-height: 100px;"></div>
                                    </div>
                                    <span class="small">Digite a palavra chave e aperte a tecla "tab" ou "," ou "Enter" ou espaço para começar a digitar outra</span>
                                </div>
                                <div class="invalid-feedback">
                                    Este campo deve ser preenchido com as palavras-chave desejadas
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $values = [
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Não'), 'val' => '0'],
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => '1'],
                                    ];

                                    FormHelper::select(
                                        $automaticLinksModel,
                                        'partly_match',
                                        [],
                                        $values
                                    );
                                    ?>
                                    <span class="small">Ex.: adiciona o link a palavra "encontrou", quando a palavra-chave for "encontro"</span>
                                </div>
                            </div>

                            <input type="hidden" name="<?=$automaticLinksModel->getModelName()?>[keywords]" id="<?=$automaticLinksModel->getModelName() . '_keywords'?>" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <?php
                FormHelper::submitButton(
                    $automaticLinksModel,
                    TranslateHelper::getTranslate('Salvar link'),
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

<?php
$this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/scripts.php');
?>