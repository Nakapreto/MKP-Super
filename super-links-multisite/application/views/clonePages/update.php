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
$instalarPrestoPlayer = $this->instalarPrestoPlayer;
$precisaAtivarCompatibilidade = $this->precisaAtivarCompatibilidade;
$ehPgBuilderall = isset($this->pageData['ehPaginaBuilderall'])? $this->pageData['ehPaginaBuilderall'] : false;
$ehPaginaWix = isset($this->pageData['ehPaginaWix'])? $this->pageData['ehPaginaWix'] : false;

$pgBrancaGringaModel = $this->pgBrancaGringaModel;

$keyWord = $addLinksModel->getAttribute('keyWord');
?>

<div class="wrap">
        <?php
        FormHelper::formStart($addLinksModel);
        ?>
        <div class="col-md-12 order-md-1">
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-sm-8">
                        <h3><?= $this->pageData['pageTitle'] ?></h3>
                        <p class="small"><?php TranslateHelper::printTranslate('Preencha os campos abaixo para editar uma página clonada')?></p>
                        <a class="btn btn-primary btn-sm"
                           href="<?=SUPER_LINKS_TEMPLATE_URL?>/<?=$keyWord?>" target="_blank"><?= TranslateHelper::getTranslate('Abrir Página Clonada') ?></a>
                    </div>
                    <div class="col-sm-4 text-right">
                        <?php
                            FormHelper::submitButtonPP(
                                $addLinksModel,
                                TranslateHelper::getTranslate('Atualizar página clonada'),
                                TranslateHelper::getTranslate('Atualizar e permanecer na página'),
                                TranslateHelper::getTranslate('Atualizar e voltar para lista de páginas'),
                                [
                                    'class' => 'btn-success btn-sm'
                                ]
                            );
                        ?>
                        <!-- Botão de dropdown -->
                    </div>
                </div>
                <hr>
            </div>
            <?php
            if($instalarPrestoPlayer || $precisaAtivarCompatibilidade || $ehPgBuilderall || $ehPaginaWix){
            ?>
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-sm-12">
                        <div class="card-body">
                            <h5 class="card-title text-success">ATENÇÃO: Abra sua página clonada e verifique se ela foi clonada perfeitamente. Caso haja algum problema, siga as recomendações abaixo:</h5>
	                        <?php
	                        if($instalarPrestoPlayer) {
		                        AlertHelper::displayAlert( '
                                    A página desse produtor parece usar um plugin de player de vídeo chamado <b style="color:#000000!important;">Presto Player,</b> e para funcionar corretamente,<br>é necessário instalar e ativar o Plugin "Presto Player" no seu wordpress.
                                    <a href="https://br.wordpress.org/plugins/presto-player/" target="_blank">Clique aqui para ir a página do plugin Presto Player.</a><br>*Recomendamos que antes de instalar ou atualizar qualquer plugin no seu wordpress, você faça um backup de segurança. <a href="https://www.hostinger.com.br/tutoriais/como-fazer-backup-do-seu-site-wordpress" target="_blank">Clique aqui para saber mais sobre backups</a>
                                ', 'warning' );
	                        }

	                        if($precisaAtivarCompatibilidade) {
		                        AlertHelper::displayAlert( '
                                    Nessa página é recomendado ativar o <b style="color:#000000!important;">modo de compatibilidade,</b> logo abaixo na parte de "Solução de problemas com a clonagem".
                                ', 'warning' );
	                        }

	                        if($ehPgBuilderall || $ehPaginaWix) {
		                        AlertHelper::displayAlert( '
                                    Nessa página é recomendado desativar a opção de <b style="color:#000000!important;">salvar o html da página</b>. Essa opção fica logo abaixo do campo "Html da página clonada", na aba de substituição dos links.
                                ', 'warning' );
	                        }
	                        ?>

	                        <?php
	                        if($precisaAtivarCompatibilidade || $ehPgBuilderall || $ehPaginaWix){
                            ?>
                                <button type="button" id="aplicarRecomendacoesPgClonada" class="btn btn-primary">
                                    <?php TranslateHelper::printTranslate('Aplicar as recomendações acima')?>
                                </button>
                            <?php
	                        }
	                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
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

                    <input type="text" style="display: none;" name="<?=$addLinksModel->getModelName()?>[id]" value="<?php echo $addLinksModel->getAttribute('id');?>">
                    <input type="text" style="display: none;" name="<?=$addLinksModel->getModelName()?>[idInternalLink]" value="<?php echo $addLinksModel->getAttribute('idInternalLink');?>">

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-5 mb-3">
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
                                <div class="col-sm-5 mb-3">
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
                                                    'invalid-text' => TranslateHelper::getTranslate('Já existe um link com este caminho, ou está vazio')
                                                ],
                                                'autocomplete' => 'off'
                                            ],
                                        'Endereço da página <small class="text-warning">(Não pode conter espaços nem acentos)</small> *'
                                    );
                                    echo "<small>" . SUPER_LINKS_TEMPLATE_URL . "/<span id='keyWordComplete'></span></small>";
                                    ?>
                                </div>
                                <div class="col-md-3 mb-3">
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
                                    FormHelper::dynamicTextFieldUpdate(
                                            $affiliateUrlModel,
                                            'affiliateUrl',
                                            'A',
                                            [
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido.')
                                                ],
                                                'placeholder' => 'https://',
                                                'class' => 'form-control affiliateUrl'
                                            ],
                                        'Url da página que vai ser clonada *',
                                        false
                                    );
                                    ?>
                                </div>
                                <div class="col-md-8"  id="infoDynamicField"></div>
                            </div>

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
                                    <span class="small">Recomendado utilizar uma página branca. <a href="https://wpsuperlinks.top/faq-pagina-branca" target="_blank">Clique aqui para saber mais.</a></span>
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
                                <?php TranslateHelper::printTranslate('Substituir links de checkout, whatsapp, imagens e textos')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                         data-parent="#super-links-config-box">
                        <span></span>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $valuesGringa = [
                                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => 'yes'],
                                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'no']
                                    ];

                                    FormHelper::select(
                                        $addLinksModel,
                                        'usarEstrategiaGringa',
                                        [],
                                        $valuesGringa
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-2 mb-3">
                                <div class="col-md-12">
                                    <span class="small">
                                        <b>Quando o produtor não disponibilizar link de checkout de afiliado para substituição,</b> poderá ser utilizada a função de Marcar Cookie.
                                        <br>Porém, recomendamos utilizar sempre que possível a opção de substituir o checkout do produtor.
                                        <br><b>ATENÇÃO:</b> A função de marcar cookie não irá funcionar se na página do produtor for utilizado popup para captura de lead ao invés do link de checkout.
                                        <br>
                                        <br>
                                    </span>
                                    <a href="https://clientes.nodz.top/area/produto/item/1159646" target="_blank">Clique aqui para assistir a aula desse tema</a>
                                    <hr>
                                </div>
                            </div>

                            <div id="estrategiaNacional">
                                <div class="row mb-3">
                                    <div class="col-md-12 mt-2 mb-3">
                                        <h4>
                                            Links de Checkout
                                        </h4>
                                    </div>
                                    <div class="col-md-12 mb-3 dynamicCloneField">
                                        <?php
                                        FormHelper::dynamicCloneLinkUpdate(
                                            $clonePageModel,
                                            'link',
                                            'Link de checkout do produtor',
                                            'Seu link de checkout de afiliado',
                                            '0',
                                            [
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
                                    <div class="col-md-12 mt-5 mb-3">
                                        <h4>
                                            Formulários de captura (Substitua por um link de checkout de afiliado)
                                        </h4>
                                        <span class="small">ATENÇÃO: Nem sempre será possível detectar automaticamente o formulário e/ou substituí-lo.</span>
                                    </div>
                                    <div class="col-md-12 mb-3 dynamicCloneFormulario">
                                        <?php
                                        FormHelper::dynamicCloneLinkUpdate(
                                            $clonePageModel,
                                            'captura',
                                            'Código de abertura do Formulário',
                                            'Seu link de checkout de afiliado',
                                            '0',
                                            [
                                                'feedback' => [
                                                    'invalid-text' => TranslateHelper::getTranslate('Confira corretamente os dados')
                                                ]
                                            ]
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-8" id="infodynamicCloneFormulario"></div>
                                </div>

                                <div class="row">
                                    <div class="col-auto my-1">
                                        <button type="button" id="addNewCloneFormulario" class="btn btn-info">
                                            <?php TranslateHelper::printTranslate('Substituir outro formulário')?>
                                        </button>
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
                                    <div class="col-md-5 mb-3">
                                        <?php
                                        FormHelper::text(
                                            $pgBrancaGringaModel,
                                            'linkPaginaVenda',
                                            [],
                                            ''
                                        );
                                        ?>
                                    </div>
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

                                <div class="row">
                                    <div class="col-auto my-1">
                                        <button type="button" id="addNewGringaLink" class="btn btn-info">
                                            <?php TranslateHelper::printTranslate('Adicionar outro Link de Checkout')?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-5 mb-3">
                                    <h4>
                                        Imagens e textos
                                    </h4>
                                </div>
                                <div class="col-md-12 mb-3 dynamicCloneImage">
                                    <?php
                                    FormHelper::dynamicCloneLinkUpdate(
                                        $clonePageModel,
                                        'image',
                                        'Imagem ou texto na página a ser clonada',
                                        'Nova imagem ou texto',
                                        '0',
                                        [
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
                                <div class="col-md-12 mt-5 mb-3">
                                    <h4>
                                        Whatsapp
                                    </h4>
                                </div>
                                <div class="col-md-12 mb-3 dynamicCloneWhatsapp">
			                        <?php
			                        FormHelper::dynamicCloneLinkUpdate(
				                        $clonePageModel,
				                        'whatsapp',
				                        'Link do whatsapp na página do produtor',
				                        'Novo Link com seu número de whatsapp',
				                        '0',
				                        [
					                        'feedback' => [
						                        'invalid-text' => TranslateHelper::getTranslate('Confira corretamente os dados')
					                        ]
				                        ]
			                        );
			                        ?>
                                </div>
                                <div class="col-md-8" id="infodynamicCloneWhatsapp"></div>
                            </div>

                            <div class="row">
                                <div class="col-auto my-1">
                                    <button type="button" id="addNewCloneWhatsapp" class="btn btn-info">
                                        <?php TranslateHelper::printTranslate('Substituir outro link de whatsapp')?>
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-5 mb-3">
                                <div class="col-md-12">
                                    <h5><?php TranslateHelper::printTranslate('Incluir Botão Whatsapp Flutuante (Deixe em branco caso não queira colocar este botão)');?></h5>
                                    <span class="small">Caso a página do produtor não possua botão de whatsapp flutuante, você poderá inserir o seu próprio botão.
                                        <br>Se a página do produtor tiver botão de whatsapp, sugerimos fazer a substituição do número do produtor pelo seu, pois o botão poderá não aparecer nesse caso.
                                    </span>
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
                                <div class="col-md-7 mb-3">
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
                                <div class="col-auto my-1 mt-2">
                                    <button type="button" data-toggle="collapse" data-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree" class="btn btn-primary">
                                        <?php TranslateHelper::printTranslate('Próximo passo')?>
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


                            <div class="row mb-3" style="display:none;">
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

                            <div class="row">
                                <div class="col-auto my-1 mt-5">
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

                <div class="card col-md-12 no-margin no-padding">
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
                                <div class="col-md-12 my-1">
                                    <button type="button" data-toggle="collapse" data-target="#collapseSix"
                                            aria-expanded="false" aria-controls="collapseSix" class="btn btn-primary">
				                        <?php TranslateHelper::printTranslate('Próximo passo')?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-md-12 no-margin no-padding">
                    <div class="card-header" id="headingSix">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseSix" aria-expanded="false"
                                    aria-controls="collapseSix">
					            <?php TranslateHelper::printTranslate('Solução de Problemas com a Clonagem')?>
                            </button>
                        </h2>
                    </div>
                    <div id="collapseSix" class="collapse" aria-labelledby="headingSix"
                         data-parent="#super-links-config-box">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mt-4">
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
                                <div class="col-md-5 mt-5 mb-5">
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

                            <div id="enableRedirectJavascript"></div>

                            <div class="row">
                                <div class="col-md-4 mt-5 mb-1">
			                        <?php
			                        $valuesSave = [
				                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => 'enabled'],
				                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'disabled']
			                        ];

			                        FormHelper::select(
				                        $addLinksModel,
				                        'saveHtmlClone',
				                        [],
				                        $valuesSave
			                        );
			                        ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span class="small">* Marque a opção "Não", quando você perceber que o layout da página ficou diferente da página do produtor depois de algum tempo, ou a versão mobile ficou desconfigurada.</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5 mt-5 mb-5">
			                        <?php
			                        $valuesPixel[] = ['selected' => false, 'text' => 'Não', 'val' => 'disabled'];
			                        $valuesPixel[] = ['selected' => true, 'text' => 'Sim', 'val' => 'enabled'];

			                        FormHelper::select(
				                        $addLinksModel,
				                        'removerPixelPgClonada',
				                        [],
				                        $valuesPixel
			                        );
			                        ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-4">
                                    <hr>
                                    <h5>Html da página clonada</h5>
                                    <span class="small">ATENÇÃO: Alterar o código abaixo manualmente, requer conhecimento de programação e pode fazer com que sua página não funcione corretamente.</span>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
			                        <?php
			                        FormHelper::textArea(
				                        $addLinksModel,
				                        'htmlClonePage',
				                        []
			                        );
			                        ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <br><span class="small">Caso você faça qualquer edição manual no código HTML da página, marque "Não" na opção de "Renovar Html da página" (campo logo abaixo).
                                    <br> A opção de renovar html quando habilitada, fará com que sempre o html da página original do produtor seja recuperado e atualizado na página clonada, fazendo com que qualquer edição manual seja apagada.
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mt-2 mb-3">
			                        <?php
			                        $valuesSave = [
				                        ['selected' => true, 'text' => TranslateHelper::getTranslate('Sim'), 'val' => 'enabled'],
				                        ['selected' => false, 'text' => TranslateHelper::getTranslate('Não'), 'val' => 'disabled']
			                        ];

			                        FormHelper::select(
				                        $addLinksModel,
				                        'renovaHtmlClone',
				                        [],
				                        $valuesSave
			                        );
			                        ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <span class="small">* Desabilite essa opção caso tenha feito alguma modificação manualmente no código html da página (Campo de Html da página clonada acima).</span>
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
                        TranslateHelper::getTranslate('Atualizar página clonada'),
                        TranslateHelper::getTranslate('Atualizar e permanecer na página'),
                        TranslateHelper::getTranslate('Atualizar e voltar para lista de páginas'),
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
$this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/scripts2.php');
?>

