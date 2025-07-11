<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$groupLinkModel = $this->groupLinkModel;

?>

<div class="wrap">
    <?php
    FormHelper::formStart($groupLinkModel);
    ?>
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= $this->pageData['pageTitle'] ?></h3>
                </div>
                <div class="col-4 text-right">
                    <?php
                    FormHelper::submitButton(
                        $groupLinkModel,
                        TranslateHelper::getTranslate('Atualizar categoria'),
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
                            <?php TranslateHelper::printTranslate('Dados da categoria') ?>
                        </button>
                    </h2>
                </div>

                <input type="text" style="display: none;" name="<?= $groupLinkModel->getModelName() ?>[id]"
                       value="<?php echo $groupLinkModel->getAttribute('id'); ?>">

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                     data-parent="#super-links-config-box">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <?php
                                FormHelper::text(
                                    $groupLinkModel,
                                    'groupName',
                                    [
                                        'feedback' => [
                                            'invalid-text' => TranslateHelper::getTranslate('Esse campo é obrigatório, ou já existe outra categoria com esse nome.')
                                        ]
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
                $groupLinkModel,
                TranslateHelper::getTranslate('Atualizar categoria'),
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
