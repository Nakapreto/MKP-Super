<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$cookiePageModel = $this->cookiePageModel;
$groupCookieModel = $this->groupCookieModel;
?>

<div class="wrap">
    <?php
    FormHelper::formStart($cookiePageModel);
    ?>
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= $this->pageData['pageTitle'] ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Editar cookie de afiliado e/ou Popup de saída')?></p>
                </div>
                <div class="col-4 text-right">
                    <?php
                    FormHelper::submitButton(
                        $cookiePageModel,
                        TranslateHelper::getTranslate('Atualizar configuração'),
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
                            <?php TranslateHelper::printTranslate('Dados da configuração')?>
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                     data-parent="#super-links-config-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3" id="categoryGroupLinks">
                                <?php
                                $values = $groupCookieModel->getAllGroupsValues();

                                FormHelper::select(
                                    $groupCookieModel,
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
                                $values = [
                                    ['selected' => true, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => 'enabled'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'disabled'],
                                ];

                                FormHelper::select(
                                    $cookiePageModel,
                                    'statusCookie',
                                    [],
                                    $values
                                );
                                ?>
                            </div>
                            <div class="col-md-5 mb-3">
                                <?php
                                FormHelper::text(
                                    $cookiePageModel,
                                    'cookieName',
                                    []
                                );
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <?php
                                $valuesPosts = $cookiePageModel->getPostsSuperLinksOptions();

                                FormHelper::selectMultiple(
                                    $cookiePageModel,
                                    'idPost',
                                    [
                                        'class' => 'select2Multiple',
                                        'multiple' => true
                                    ],
                                    $valuesPosts
                                );
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <?php
                                $valuesPages = $cookiePageModel->getPagesSuperLinksOptions();

                                FormHelper::selectMultiple(
                                    $cookiePageModel,
                                    'idPage',
                                    [
                                        'class' => 'select2Multiple',
                                        'multiple' => true
                                    ],
                                    $valuesPages
                                );
                                ?>
                            </div>
                        </div>
                        <input type="hidden" name="SuperLinksLinkCookiePageModel[oldIdPost]" value="<?=$cookiePageModel->getAttribute('id')?>">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <?php
                                $valuesLinks = $cookiePageModel->getLinksSuperLinksOptions();

                                FormHelper::selectMultiple(
                                    $cookiePageModel,
                                    'linkSuperLinks',
                                    [
                                        'class' => 'select2Multiple',
                                        'multiple' => true
                                    ],
                                    $valuesLinks
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card col-md-12 no-margin no-padding">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            <?php TranslateHelper::printTranslate('Opções de mostrar url quando o usuário deixar a sua página')?>
                        </button>
                    </h2>
                </div>

                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                     data-parent="#super-links-config-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <?php
                                $values = [
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Sim (Recomendado)'), 'val' => 'enabled'],
                                    ['selected' => true, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'disabled'],
                                ];

                                FormHelper::select(
                                    $cookiePageModel,
                                    'redirect',
                                    [],
                                    $values
                                );
                                ?>
                            </div>
                            <div class="col-md-5 mb-3">
                                <?php
                                $values = [
                                    ['selected' => true, 'text' => TranslateHelper::getTranslate('Sempre'), 'val' => '0'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 1 dia'), 'val' => '1'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 2 dias'), 'val' => '2'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 3 dias'), 'val' => '3'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 4 dias'), 'val' => '4'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 5 dias'), 'val' => '5'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 6 dias'), 'val' => '6'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 7 dias'), 'val' => '7'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 8 dias'), 'val' => '8'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 9 dias'), 'val' => '9'],
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Após 10 dias'), 'val' => '10'],
                                ];

                                FormHelper::select(
                                    $cookiePageModel,
                                    'qtdAcessos',
                                    [],
                                    $values
                                );
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <?php
                                FormHelper::text(
                                    $cookiePageModel,
                                    'urlCamuflada',
                                    [
                                        'feedback' => [
                                            'invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido.')
                                        ],
                                        'placeholder' => 'https://',
                                        'class' => 'form-control urlCookie'
                                    ]
                                );
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <?php
                                $values = [
                                    ['selected' => false, 'text' => TranslateHelper::getTranslate('Usuário tentar sair'), 'val' => 'exitPage'],
                                    ['selected' => true, 'text' => TranslateHelper::getTranslate('Usuário clicar no botão voltar'), 'val' => 'btnBack'],
                                ];

                                FormHelper::selectMultiple(
                                    $cookiePageModel,
                                    'activeWhen',
                                    [
                                        'class' => 'select2Multiple',
                                        'multiple' => true
                                    ],
                                    $values
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card col-md-12 no-margin no-padding">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                                data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                            <?php TranslateHelper::printTranslate('Opções de ativação do cookie')?>
                        </button>
                    </h2>
                </div>

                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                     data-parent="#super-links-config-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <?php
                                FormHelper::text(
                                    $cookiePageModel,
                                    'timeCookie',
                                    [
                                        'append' => 'segundos',
                                        'feedback' => [
                                            'invalid-text' => TranslateHelper::getTranslate('O valor mínimo é 0 segundos')
                                        ]
                                    ]
                                );
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <?php
                                FormHelper::text(
                                    $cookiePageModel,
                                    'urlCookie',
                                    [
                                        'feedback' => [
                                            'invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido.')
                                        ],
                                        'placeholder' => 'https://',
                                        'class' => 'form-control urlCookie'
                                    ]
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
            FormHelper::submitButton(
                $cookiePageModel,
                TranslateHelper::getTranslate('Atualizar configuração'),
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
$this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/scripts.php');
?>
