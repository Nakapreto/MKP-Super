<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$addLinksModel = $this->addLinksModel;
$groupLinkModel = $this->groupLinkModel;
$affiliateUrlModel = $this->affiliateUrlModel;
$monitoringModel = $this->monitoringModel;
$cloakModel = $this->cloakModel;
$configSocialModel = $this->configSocialModel;
$waitPageModel = $this->waitPageModel;
$clonePageModel = $this->clonePageModel;
$apiConvertFaceModel = $this->apiConvertFaceModel;
$pgBrancaGringaModel = $this->pgBrancaGringaModel;
?>

<div class="wrap">
        <?php
        FormHelper::formStart($addLinksModel);
        ?>
        <div class="col-md-12 order-md-1">
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-8">
                        <h3><?= $this->pageData['pageTitle'] ?></h3>
                        <p class="small"><?php TranslateHelper::printTranslate('Preencha os campos abaixo para criar um novo link redirecionável')?></p>
                    </div>
                    <div class="col-4 text-right">
                        <?php
                            FormHelper::submitButtonPP(
                                $addLinksModel,
                                TranslateHelper::getTranslate( 'Salvar novo link' ),
                                TranslateHelper::getTranslate( 'Salvar e permanecer na página' ),
                                TranslateHelper::getTranslate( 'Salvar e voltar para lista de páginas' ),
                                [
                                    'class' => 'btn-success btn-sm'
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
                                <div class="col-md-5 mb-3">
                                    <?php
                                    $values = [
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Habilitado'), 'val' => 'enabled'],
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Desabilitado'), 'val' => 'disabled'],
                                    ];

                                    FormHelper::select(
                                        $addLinksModel,
                                        'statusLink',
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
                                        $values
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
                                            $addLinksModel,
                                            'linkName',
                                            [
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('Um nome de link é obrigatório o preenchimento para identificar o link criado')
                                                ]
                                            ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                            $addLinksModel,
                                            'keyWord',
                                            [
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('Já existe um link com este caminho, ou está vazio')
                                                ],
                                                'autocomplete' => 'off'
                                            ]
                                    );
                                    echo "<b>Seu link vai ficar assim:</b> <small>" . SUPER_LINKS_TEMPLATE_URL . "/<span id='keyWordComplete'></span></small>";
                                    echo "<br><small class='text-info'>Obs.: O endereço acima foi sugerido para facilitar, mas você pode alterar se quiser.</small>";
                                    ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div id="boxRedirectDelay">
                                        <?php
                                        FormHelper::text(
                                                $addLinksModel,
                                                'redirectDelay',
                                                [
                                                    'append' => 'segundos',
                                                    'feedback' => [
                                                        'invalid-text' => TranslateHelper::getTranslate('Para os tipos de redirecionamento Php e camuflador o valor deste campo deve ser 0 segundos')
                                                    ]
                                                ]
                                        );
                                        ?>
                                    </div>
                                    <div id="boxInfoRedirectDelay" style="display: none;">
                                        <label>Redirecionar após</label>
                                        <p>Redirecionador do tipo PHP ou Camuflador não podem possuir tempo para redirecionar. Se precisar colocar um delay antes de redirecionar, use os tipos de redirecionadores Html ou Javascript.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label><?php TranslateHelper::printTranslate('Descrição do link');?></label>
                                    <?php
                                    FormHelper::textArea(
                                        $addLinksModel,
                                        "description",
                                        []
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <?php
                                    $values = $addLinksModel->getOptionsRedirectPro();

                                    FormHelper::select(
                                        $addLinksModel,
                                        'redirectType',
                                        [],
                                        $values
                                    );

                                    FormHelper::inputHidden(
                                        $addLinksModel,
                                        'usarEstrategiaGringa',
                                        [],
                                        'no'
                                    );
                                    ?>
                                    <span class="small" id="helpTextRedirect"></span>
                                </div>

                                <div class="col-md-7 mb-3" id="redirectFacebook">
                                    <?php
                                    if($addLinksModel->getAttribute('redirectType') == 'facebook') {
                                        $values = [
                                            ['selected' => true, 'text' => TranslateHelper::getTranslate('Redirecionador (Html)'), 'val' => 'html'],
                                            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (Javascript)'), 'val' => 'javascript'],
                                            ['selected' => false, 'text' => TranslateHelper::getTranslate('Camuflador'), 'val' => 'camuflador'],
                                        ];

                                        FormHelper::selectFacebook(
                                            $addLinksModel,
                                            'redirectFace',
                                            [],
                                            $values
                                        );
                                    }
                                    ?>
                                </div>
                            </div>

                            <div id="enableRedirectJavascript"></div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3 dynamicField">
                                    <?php
                                    FormHelper::dynamicTextFieldClone(
                                        $affiliateUrlModel,
                                        'affiliateUrl',
                                        'A',
                                        [
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido.')
                                            ],
                                            'placeholder' => 'https://'
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-8"  id="infoDynamicField"></div>
                            </div>

                            <div id="estrategiaNacional">
                                <div class="row">
                                    <div class="col-auto my-1">
                                        <button type="button" id="addNovaUrl" class="btn btn-info">
                                            <?php TranslateHelper::printTranslate('Adicionar nova Url de Afiliado')?>
                                        </button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                        <h4><?php TranslateHelper::printTranslate('Redirect quando usuário sair da página (Exit Intent)');?></h4>
                                        <hr>
                                    </div>
                                    <div class="col-md-12 mb-5">
                                        <span id="redirectBox">
                                            <?php
                                            if($addLinksModel->getAttribute('redirectType') == 'clonador' || $addLinksModel->getAttribute('redirectType') == 'camuflador' || $addLinksModel->getAttribute('redirectFace') == 'camuflador'){
                                                ?>
                                                <?php
                                                FormHelper::text(
                                                    $addLinksModel,
                                                    "redirectBtn",
                                                    []
                                                );
                                                ?>
                                                <span class="small">Recomendado utilizar uma página branca. Não utilizar url de checkout. <a href="https://wpsuperlinks.top/faq-pagina-branca" target="_blank">Clique aqui para ver essa aula na área de membros.</a></span>
                                                <?php
                                            }else {
                                                ?>
                                                <div class="alert alert-warning fade show" role="alert">
                                                  Essa opção só é necessária no  "<strong>Camuflador</strong>".
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div id="estrategiaGringa">
                                <div class="row">
                                    <div class="col-md-12 mt-2 mb-3">
                                        <h4>
                                            Marcar Cookie Quando Produtor Não Disponibilizar Checkout de Afiliado
                                        </h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $pgBrancaGringaModel,
                                            'textoTempoRedirecionamento',
                                            [
                                                'placeholder' => 'Loading...'
                                            ]
                                        );
                                        ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $pgBrancaGringaModel,
                                            'tempoRedirecionamentoCheckout',
                                            [
                                                'append' => 'segundos',
                                            ]
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-7 mt-2 mb-3">
                                        <?php
                                        FormHelper::inputHidden(
                                            $pgBrancaGringaModel,
                                            'abrirPaginaBranca',
                                            [],
                                            'disabled'
                                        );
                                        ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3 dynamicGringaLinkField">
                                        <?php
                                        FormHelper::dynamicCloneUniqueLinkUpdate(
                                            $pgBrancaGringaModel,
                                            'Link de checkout do produtor',
                                            '0',
                                            [
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('Confira corretamente os endereços dos links')
                                                ]
                                            ]
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-8" id="infodynamicGringaLinkField"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 my-1">
                                    <button type="button" data-toggle="collapse" data-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo" class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-12 no-margin no-padding">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo">
                                <?php TranslateHelper::printTranslate('Opções de rastreamento')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                         data-parent="#super-links-config-box">
                        <span>
                        <?php
                        if($addLinksModel->getAttribute('redirectType') == 'php') {
                         ?>
                            <div class="card-body">
                                <div class="alert alert-warning fade show" role="alert">
                                    Redirecionamento do tipo "<strong>PHP</strong>" não permite rastreamento
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        </span>
                        <?php
                        $style = '';
                        if($addLinksModel->getAttribute('redirectType') == 'php') {
                            $style = 'style="display:none;"';
                        }
                        ?>
                        <div class="card-body" <?=$style?>>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $monitoringModel,
                                        'googleMonitoringID',
                                        [
                                            'placeholder' => 'Identificador do analytics'
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $monitoringModel,
                                        'trackGoogle',
                                        [
                                            'placeholder' => 'ex.: AW-7337824565/ajgHwCKOnuuwWDJiizOLG'
                                        ]
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $monitoringModel,
                                        'pixelID',
                                        []
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $monitoringModel,
                                        'track',
                                        []
                                    );
                                    ?>
                                    <span class="small"><?php TranslateHelper::printTranslate('Escolha um nome para o track no pixel do Facebook') ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3 mt-3">
                                    <h4 class="mb-0">Api de Conversão do Facebook</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <?php
                                    $valuesApiEnable = [
                                        ['selected' => false, 'text' => 'Habilitado', 'val' => 'enabled'],
                                        ['selected' => true, 'text' => 'Desabilitado', 'val' => 'disabled']
                                    ];
                                    FormHelper::select(
                                        $monitoringModel,
                                        'enableApiFacebook',
                                        [],
                                        $valuesApiEnable
                                    );
                                    ?>
                                </div>
                            </div>

                            <div id="boxApiConvertFace" style="display:none;">
                                <div class="row">
                                    <div class="col-12 col-md-12 col-lg-12 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $monitoringModel,
                                            'tokenApiFacebook',
                                            []
                                        );
                                        ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $monitoringModel,
                                            'pixelApiFacebook',
                                            []
                                        );
                                        ?>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $monitoringModel,
                                            'testEventApiFacebook',
                                            []
                                        );
                                        ?>
                                    </div>
                                </div>

                                <div class="dynamicFieldApi">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <?php
                                            FormHelper::text(
                                                $apiConvertFaceModel,
                                                'eventNameApiFacebook',
                                                [
                                                    'required' => true,
                                                ]
                                            );
                                            ?>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <?php
                                            FormHelper::text(
                                                $apiConvertFaceModel,
                                                'eventIdApiFacebook',
                                                []
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12 mb-3 mt-3">
                                    <h4 class="mb-0">Outros códigos para inserir na página</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <?php
                                    FormHelper::textArea(
                                        $monitoringModel,
                                        "codeHeadPage",
                                        []
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?php
                                    FormHelper::textArea(
                                        $monitoringModel,
                                        "codeBodyPage",
                                        []
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?php
                                    FormHelper::textArea(
                                        $monitoringModel,
                                        "codeFooterPage",
                                        []
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" data-toggle="collapse" data-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree"
                                            class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-md-12 no-margin no-padding">
                    <div class="card-header" id="headingThree">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                    aria-controls="collapseThree">
                                <?php TranslateHelper::printTranslate('Cloak - opções de camuflagem do link para ads')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row" id="boxInfoCloak">
                                <div class="col-md-12 mb-5">
                                    <div class="alert alert-warning fade show" role="alert">
                                        Atenção: Cloak só funciona em links especiais para Facebook (Selecionar em "Tipo do redirecionamento" na aba de dados do link).
                                    </div>
                                </div>
                            </div>
                            <div id="boxConfigCloak">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <?php
                                        $statusCloak = [
                                            ['selected' => true, 'text' => 'Não', 'val' => 'disabled'],
                                            ['selected' => false, 'text' => 'Sim', 'val' => 'enabled']
                                        ];

                                        FormHelper::select(
                                            $cloakModel,
                                            'statusCloak',
                                            [],
                                            $statusCloak
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <?php
                                        $countries = SuperLinksLinkCloakModel::getCountriesValues();

                                        FormHelper::select(
                                            $cloakModel,
                                            'connection1',
                                            [],
                                            $countries
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <?php
                                        FormHelper::select(
                                            $cloakModel,
                                            'connection2',
                                            [],
                                            $countries
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <?php
                                        FormHelper::select(
                                            $cloakModel,
                                            'connection3',
                                            [],
                                            $countries
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <?php
                                        FormHelper::select(
                                            $cloakModel,
                                            'connection4',
                                            [],
                                            $countries
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <div class="alert alert-success fade show" role="alert">
                                            Atenção: <br>Conexões vindas dos países selecionados acima irão redirecionar para os seus links de afiliado que foram configurados na aba "Dados do link".
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="boxRedirecionarPara">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $cloakModel,
                                            'connectionRedirectUrl',
                                            []
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $cloakModel,
                                            'defaultRedirectUrl',
                                            []
                                        );
                                        ?>
                                        <span class="small">Insira aqui a url da página que o Facebook vai visualizar no depurador.</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <div class="alert alert-danger fade show" role="alert">
                                            Cuidado: <br>Ao utilizar plugins de cache, eles vão interferir no funcionamento do cloak.<br> Por isso, se for usar cloak, desative todos seus plugins de cache.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" data-toggle="collapse" data-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour" class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-md-12 no-margin no-padding">
                    <div class="card-header" id="headingFour">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                                    aria-controls="collapseFour">
                                <?php TranslateHelper::printTranslate('Opções de visualização da página')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $configSocialModel,
                                        'textTitle',
                                        []
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <?php
                                    FormHelper::textArea(
                                        $configSocialModel,
                                        'description',
                                        []
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <?php
                                    FormHelper::inputFile(
                                        $configSocialModel,
                                        'image',
                                        [
                                            'append' => 'Selecionar',
                                            'class' => 'uploadImage'
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4" id="showImage">
                                    <img src="<?=$configSocialModel->getAttribute('image')?>" class="img-fluid">
                                </div>
                                <div class="col-md-12 mb-3" style="display:none;" id="showButtomRemoveImage">
                                    <button type="button" class="btn btn-danger" id="removeImage">
                                        <?php TranslateHelper::printTranslate('Remover Imagem')?>
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-5 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('Opções de carregamento da página');?></h6>
                                    <hr>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <?php
                                    $valuesWait = [
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => 'yes'],
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'no']
                                    ];

                                    FormHelper::select(
                                        $waitPageModel,
                                        'showSpinner',
                                        [],
                                        $valuesWait
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $waitPageModel,
                                        'textLoadPage',
                                        [
                                            'placeholder' => 'Carregando...'
                                        ]
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-5 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('Incluir botão Whatsapp (Deixe em branco caso não queira colocar este botão)');?></h6>
                                    <hr>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $addLinksModel,
                                        'numberWhatsapp',
                                        [
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('Coloque apenas números. Ex.: 5532987...')
                                            ]
                                        ]
                                    );
                                    ?>
                                    <small>Incluir o código do país, para o Brasil começar com 55 e depois o DDD e depois o número do telefone. Ex.: 55329870...</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $addLinksModel,
                                        'textWhatsapp',
                                        []
                                    );
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-12 mt-4">
                <?php
                    FormHelper::submitButtonPP(
                        $addLinksModel,
                        TranslateHelper::getTranslate( 'Salvar novo link' ),
                        TranslateHelper::getTranslate( 'Salvar e permanecer na página' ),
                        TranslateHelper::getTranslate( 'Salvar e voltar para lista de páginas' ),
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
$this->render(SUPER_LINKS_VIEWS_PATH . '/links/scripts.php');
?>