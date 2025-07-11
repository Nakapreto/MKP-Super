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
                        <p class="small"><?php TranslateHelper::printTranslate('Preencha os campos abaixo para clonar uma nova página')?></p>
                    </div>
                    <div class="col-4 text-right">
                        <?php
                            FormHelper::submitButtonPP(
                                $addLinksModel,
                                TranslateHelper::getTranslate('Salvar página'),
                                TranslateHelper::getTranslate('Salvar e permanecer na página'),
                                TranslateHelper::getTranslate('Salvar e voltar para lista de páginas'),
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
                                <?php TranslateHelper::printTranslate('Dados da página')?>
                            </button>
                        </h2>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5 mb-3" id="categoryGroupLinks">
                                    <?php
                                    $values = $groupLinkModel->getAllGroupsValues();

                                    FormHelper::select(
                                        $groupLinkModel,
                                        'id',
                                        [],
                                        $values,
                                        'Categoria da página'
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
                                                    'invalid-text' => TranslateHelper::getTranslate('Um nome de página é obrigatório o preenchimento para identificar a página criada')
                                                ]
                                            ],
                                        'Nome da página *'
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
                                                    'invalid-text' => TranslateHelper::getTranslate('Já existe uma página ou link com este caminho, ou está vazio')
                                                ],
                                                'autocomplete' => 'off'
                                            ],
                                        'Endereço da página <small class="text-warning">(Não pode conter espaços nem acentos)</small> *'
                                    );
                                    echo "<small>" . SUPER_LINKS_TEMPLATE_URL . "/<span id='keyWordComplete'></span></small>";
                                    ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <?php
                                        FormHelper::inputHidden(
                                                $addLinksModel,
                                                'redirectDelay',
                                                []
                                        );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label><?php TranslateHelper::printTranslate('Descrição da página clonada');?></label>
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
                                        FormHelper::inputHidden(
                                            $addLinksModel,
                                            'redirectType',
                                            [],
                                            'clonador'
                                        );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3 dynamicField">
                                    <?php
                                    FormHelper::dynamicTextField(
                                            $affiliateUrlModel,
                                            'affiliateUrl',
                                            'A',
                                            [
                                                'hideRemoveLink' => true,
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido')
                                                ]
                                            ],
                                        'Url da página que vai ser clonada *'
                                    );
                                    ?>
                                </div>
                                <div class="col-md-8" id="infoDynamicField"></div>
                            </div>

                            <!-- Solução de problemas com clonagem-->
                            <div style="display:none;">

                                <div class="row" style="display: none;">
                                    <div class="col-md-5">
			                            <?php
			                            $valuesAvanc[] = ['selected' => true, 'text' => 'Não', 'val' => 'disabled'];
			                            $valuesAvanc[] = ['selected' => false, 'text' => 'Sim', 'val' => 'enabled'];

			                            FormHelper::select(
				                            $addLinksModel,
				                            'usarClonagemAvancada',
				                            [],
				                            $valuesAvanc
			                            );
			                            ?>
                                        <span class="small">Obs.: Caso a página apresente algum problema para clonar usando a função avançada, faça a clonagem normal, sem ativar a função avançada.</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                        <h4><?php TranslateHelper::printTranslate('Redirect quando usuário sair da página (Exit Intent)');?></h4>
                                        <hr>
                                    </div>
                                    <div class="col-md-6 mb-5">
			                            <?php
			                            FormHelper::text(
				                            $addLinksModel,
				                            "redirectBtn",
				                            []
			                            );
			                            ?>
                                        <span class="small">Recomendado utilizar uma página branca. Não utilizar url de checkout. <a href="https://wpsuperlinks.top/faq-pagina-branca" target="_blank">Clique aqui para ver essa aula na área de membros.</a></span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                        <h4><?php TranslateHelper::printTranslate('Solução de problemas com a clonagem');?></h4>
                                        <span class="small"><a href="https://wpsuperlinks.top/faq/?s=clonada" target="_blank">Clique aqui </a>para ver as perguntas frequentes relacionados a problemas com a clonagem</span>
                                        <br><span class="small">Sugerimos que sempre siga todos os passos explicados na FAQ. Pesquise na FAQ com a palavra "Clonada", nesse caso todos os tutoriais relacionados a páginas clonadas irão aparecer.</span>
                                        <hr>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <?php
                                        $valuesCompat[] = ['selected' => true, 'text' => 'Não', 'val' => 'disabled'];
                                        $valuesCompat[] = ['selected' => false, 'text' => 'Sim', 'val' => 'enabled'];

                                        FormHelper::select(
                                            $addLinksModel,
                                            'compatibilityMode',
                                            [],
                                            $valuesCompat
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                         <span class="small">Ative somente caso a página clonada tenha apresentado problemas de visualização, ou não puder ter sido clonada perfeitamente. <br>Mas nesse caso, observe se a substituição
                                        de links foi feita corretamente (Basta testar o link, clicando no botão de checkout da página clonada).<br> Pois em alguns casos ela pode impedir de efetuar a troca de links, e nesse caso a página não poderá ser clonada.
                                         <br>Se com essa opção habilitada a página não for clonada perfeitamente, desabilite essa opção e habilite a opção de forçar clonagem abaixo.</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <?php
                                        $valuesForce[] = ['selected' => true, 'text' => 'Não', 'val' => 'disabled'];
                                        $valuesForce[] = ['selected' => false, 'text' => 'Sim', 'val' => 'enabled'];

                                        FormHelper::select(
                                            $addLinksModel,
                                            'forceCompatibility',
                                            [],
                                            $valuesForce
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <span class="small">Ative somente caso a página clonada tenha apresentado problemas de visualização, ou não puder ter sido clonada perfeitamente. <br><strong>Essa opção não funcionará caso
                                             a opção de modo de compatibilidade acima estiver ativada. Portanto, desative a opção acima antes de ativar esta.</strong>
                                         <br>Se mesmo com essa opção habilitada a página não for clonada perfeitamente, tente habilitar a opção de proxy abaixo.</span>
                                    </div>
                                </div>

                                <div id="enableRedirectJavascript"></div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <span class="small">Ative somente caso a página clonada tenha apresentado problemas de visualização, ou não puder ter sido clonada perfeitamente e você já tenha feito os passos anteriores. <br>
                                            <strong>Desabilite todas as opções acima antes de ativar esta, e não esqueça de apagar o conteúdo do campo de substituição de html, caso esteja editando a página.</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim Solução de problemas com clonagem-->

                            <div class="row" style="display:none;">
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

                <div class="card col-md-12 no-margin no-padding" style="display:none;">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                    aria-controls="collapseTwo">
                                <?php TranslateHelper::printTranslate('Substituir html, links, imagens e textos na página clonada')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                         data-parent="#super-links-config-box">
                        <span></span>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mt-2 mb-3">
                                    <h4>
                                        Links
                                    </h4>
                                </div>
                                <div class="col-md-12 mb-3 dynamicCloneField">
                                    <?php
                                    FormHelper::dynamicCloneLink(
                                        $clonePageModel,
                                        'link',
                                        'Link de checkout do produtor',
                                        'Seu link de checkout de afiliado',
                                        '0',
                                        [
                                            'hideRemoveLink' => true,
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('Confira corretamente os endereços dos links')
                                            ]
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-8" id="infodynamicCloneField"></div>
                            </div>

                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" id="addNewCloneLink" class="btn btn-info">
                                        <?php TranslateHelper::printTranslate('Substituir outro Link')?>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-2 mb-3">
                                    <h4>
                                        Imagens e textos
                                    </h4>
                                </div>
                                <div class="col-md-12 mb-3 dynamicCloneImage">
                                    <?php
                                    FormHelper::dynamicCloneLink(
                                        $clonePageModel,
                                        'image',
                                        'Imagem ou texto na página a ser clonada',
                                        'Nova imagem ou texto',
                                        '0',
                                        [
                                            'hideRemoveLink' => true,
                                            'feedback' => [
                                                'invalid-text' => TranslateHelper::getTranslate('Confira corretamente os dados')
                                            ]
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-md-8" id="infodynamicCloneImage"></div>
                            </div>

                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" id="addNewCloneImage" class="btn btn-info">
                                        <?php TranslateHelper::printTranslate('Substituir outra Imagem ou texto')?>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-auto my-1 mt-3">
                                    <button type="button" data-toggle="collapse" data-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree" class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-md-12 no-margin no-padding" style="display:none;">
                    <div class="card-header" id="headingThree">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                    aria-controls="collapseThree">
                                <?php TranslateHelper::printTranslate('Opções de visualização da página')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $valuesPages = $addLinksModel->getPagesSuperLinksOptions();

                                    FormHelper::selectMultiple(
                                        $addLinksModel,
                                        'idPage',
                                        [
                                            'class' => 'select2Multiple'
                                        ],
                                        $valuesPages
                                    );
                                    ?>
                                    <span class="small">Ao associar, a página do wordpress escolhida passará a exibir a página clonada no lugar da página original.</span>
                                </div>
                            </div>

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
                                <div class="col-md-8 mb-3">
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
                                <div class="col-md-4" id="showImage"></div>
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

                <div class="card col-md-12 no-margin no-padding" style="display:none;">
                    <div class="card-header" id="headingFour">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                                    aria-controls="collapseFour">
                                <?php TranslateHelper::printTranslate('Usar outros plugins e Popup na página clonada')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                         data-parent="#super-links-config-box">
                        <div class="card-body">

                            <div class="row mt-2 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('RGPD');?></h6>
                                    <p>Para utilizar essa função é necessário ter instalado e ativado o Plugin RGPD em seu Wordpress</p>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <?php
                                    $isOkRGPD = false;
                                    if(function_exists('active_rgpd_box')){
                                        $isOkRGPD = true;
                                    }

                                    if(!$isOkRGPD){
                                        $valuesRGPD = [
                                            ['selected' => true, 'text' => 'O RGPD não está ativado', 'val' => 0],
                                        ];
                                    }else{
                                        $valuesRGPD = [
                                            ['selected' => true, 'text' => 'Usar RGPD nessa página', 'val' => 1],
                                            ['selected' => false, 'text' => 'Não usar RGPD nessa página', 'val' => 0]
                                        ];
                                    }

                                    FormHelper::select(
                                        $addLinksModel,
                                        'rgpd',
                                        [],
                                        $valuesRGPD
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?php
                                    if(!$isOkRGPD){
                                        ?>
                                        <span class="small">Para utilizar essa função é necessário ter o plugin RGPD ativado nessa instalação wordpress.<br>Caso ainda não tenha o RGPD, <a href="https://nodz.top/area-de-membros" target="_blank">clique aqui para acessar sua área de
                                    membros</a> e adquirir com um super desconto.</span>
                                        <?php
                                    }else{
                                        echo '<span class="small">Não é possível remover das páginas clonadas as mensagens de cookies que já existirem. Por isso, você poderá desabilitar o RGPD de mostrar mensagem nessa página em específico, para não ficar um aviso duplicado.</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-4 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('Contadores regressivos');?></h6>
                                    <p>Para utilizar essa função é necessário ter instalado e ativado o Plugin Super Escassez</p>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <?php
                                    $valuesCounters = array();
                                    if(function_exists('getCountersIdForSuperLinks')){
                                        $valuesCounters = getCountersIdForSuperLinks();
                                    }

                                    $existCounter = $valuesCounters;

                                    if(!$existCounter){
                                        $valuesCounters = [
                                            ['selected' => true, 'text' => 'Super Escassez não está ativado', 'val' => 0],
                                        ];
                                    }
                                    FormHelper::select(
                                        $addLinksModel,
                                        'counterSuperEscassez',
                                        [],
                                        $valuesCounters
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?php
                                    if(!$existCounter){
                                        ?>
                                        <span class="small">Para utilizar essa função é necessário ter o plugin Super Escassez ativado nessa instalação wordpress.<br>Caso ainda não tenha o Super Escassez, <a href="https://nodz.top/area-de-membros" target="_blank">clique aqui para acessar sua área de
                                    membros</a> e adquirir com um super desconto.</span>
                                        <?php
                                    }else{
                                        echo '<span class="small">Nem todas as páginas clonadas são compatíveis com o contador, então caso tenha um contador selecionado e ele não for exibido na página clonada, significa que essa função não poderá ser usada nessa página.</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-4 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('Alertas de Conversões');?></h6>
                                    <p>Para utilizar essa função é necessário ter instalado e ativado o Plugin Alertas de conversões na versão 3.6 ou superior</p>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <?php
                                    $valuesAlertas = array();
                                    if(function_exists('getAlertsIdForSuperLinks')){
                                        $valuesAlertas = getAlertsIdForSuperLinks();
                                    }

                                    $existAlertas = $valuesAlertas;

                                    if(!$existAlertas){
                                        $valuesAlertas = [
                                            ['selected' => true, 'text' => 'O Alertas de conversões não está ativado', 'val' => 0],
                                        ];
                                    }
                                    FormHelper::select(
                                        $addLinksModel,
                                        'alertaConversoes',
                                        [],
                                        $valuesAlertas
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <?php
                                    if(!$existAlertas){
                                        ?>
                                        <span class="small">Para utilizar essa função é necessário ter o plugin Alertas de conversões na versão 3.6 ou superior ativado nessa instalação wordpress.<br>Caso ainda não tenha o Alertas de conversões, <a href="https://nodz.top/area-de-membros" target="_blank">clique aqui para acessar sua área de
                                    membros</a> e adquirir com um super desconto.</span>
                                        <?php
                                    }else{
                                        echo '<span class="small">Nem todas as páginas clonadas são compatíveis com o Alertas de conversões, então caso tenha um alerta selecionado e ele não for exibido na página clonada, significa que essa função não poderá ser usada nessa página.</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-5 mb-3">
                                <div class="col-md-12">
                                    <h6><?php TranslateHelper::printTranslate('Popups');?></h6>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $valuesPopups = $addLinksModel->getPopupsSuperLinksOptions();

                                    FormHelper::selectMultiple(
                                        $addLinksModel,
                                        'idPopupDesktop',
                                        [
                                            'class' => 'select2Multiple'
                                        ],
                                        $valuesPopups
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $valuesPopups = $addLinksModel->getPopupsSuperLinksOptions();

                                    FormHelper::selectMultiple(
                                        $addLinksModel,
                                        'idPopupMobile',
                                        [
                                            'class' => 'select2Multiple'
                                        ],
                                        $valuesPopups
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $valuesPopupsShow = $addLinksModel->getPopupsShowOptions();

                                    FormHelper::selectMultiple(
                                        $addLinksModel,
                                        'exitIntentPopup',
                                        [
                                            'class' => 'select2Multiple'
                                        ],
                                        $valuesPopupsShow
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <?php
                                    FormHelper::text(
                                        $addLinksModel,
                                        'loadPopupAfterSeconds',
                                        [
                                            'append' => 'segundos'
                                        ]
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?php
                                    $valuesPopupsAnimation = $addLinksModel->getPopupsAnimationOptions();

                                    FormHelper::selectMultiple(
                                        $addLinksModel,
                                        'popupAnimation',
                                        [
                                            'class' => 'select2Multiple'
                                        ],
                                        $valuesPopupsAnimation
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-5">
                                    <?php
                                    FormHelper::colorPicker(
                                        $addLinksModel,
                                        'popupBackgroundColor',
                                        [
                                            'class' => 'form-control colorPicker colorPickerEscassez'
                                        ]
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" data-toggle="collapse" data-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive" class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-md-12 no-margin no-padding" style="display:none;">
                    <div class="card-header" id="headingFive">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseFive" aria-expanded="false"
                                    aria-controls="collapseFive">
                                <?php TranslateHelper::printTranslate('Opções de rastreamento')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive"
                         data-parent="#super-links-config-box">
                        <span></span>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-4 col-lg-4 mb-3">
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
                                <div class="col-12 col-md-4 col-lg-4 mb-3">
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
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-12 mt-4">
                <?php
                    FormHelper::submitButtonPP(
                        $addLinksModel,
                        TranslateHelper::getTranslate('Salvar página'),
                        TranslateHelper::getTranslate('Salvar e permanecer na página'),
                        TranslateHelper::getTranslate('Salvar e voltar para lista de páginas'),
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
$this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/scripts.php');
?>