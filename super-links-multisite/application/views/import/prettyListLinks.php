<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$importModel = $this->importModel;
$addLinkModel = new SuperLinksAddLinkModel();
$affiliateModel = new SuperLinksAffiliateLinkModel();
$groupModel = new SuperLinksGroupLinkModel();
?>
<style>
    .bg-secondary {
        background-color: #c7cdc9 !important;
    }
</style>
<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= TranslateHelper::getTranslate('Lista de links do Pretty Links') ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Escolha quais links você irá importar para o Super Links')?></p>
                </div>
                <div class="col-4 text-right">&nbsp;</div>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="border mt-2">
            <div class="card-header">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newGroupLink">
                                    Clique aqui para adicionar nova categoria
                                </button>
                                <br>
                                <span class="small text-success"><strong>Importante: </strong> Após cadastrar, a categoria irá aparecer como último item da lista de categorias de cada link</span>
                            </li>
                        </ul>
                        <form class="form-inline my-2 my-lg-0">
                            <input class="form-control mr-sm-2" type="search" placeholder="Procurar"
                                   aria-label="Procurar" id="inputSearch">
                        </form>
                    </div>
                </nav>
            </div>

            <?php

            $rowTitles = [
                "<input type='checkbox' id='importAll'>",
                $groupModel->attributeLabels()['groupName'],
                $addLinkModel->attributeLabels()['linkName'],
                $addLinkModel->attributeLabels()['keyWord'],
                $affiliateModel->attributeLabels()['affiliateUrl'],
                $addLinkModel->attributeLabels()['statusLink'],
                $addLinkModel->attributeLabels()['redirectType'],
            ];
            TableHelper::loadTable($rowTitles);

            $links = isset($this->pageData['links'])? $this->pageData['links'] : [];

            $optionsRedirect = $addLinkModel->getOptionsRedirectImport();

            $options = "";
            foreach ($optionsRedirect as $value) {
                $options .= $value['selected'] ? "<option value='" . $value['val'] . "' selected>" . $value['text'] . "</option>" : "<option value='" . $value['val'] . "' >" . $value['text'] . "</option>";
            }

            foreach ($links as $link) {
                $link = get_object_vars($link);

                $selectRedirect = '<select class="form-control" id="select_'.$link['id'].'" name="select_'.$link['id'].'"> '.$options.' </select>';

                $optionsValues = $groupModel->getAllGroupsValues();
                $optionsGroup = "<optgroup label='Categorias do Super Links' class='appendOptionsSPL'>";
                foreach ($optionsValues as $value) {
                    $optionsGroup .= ($value['selected']) ? "<option value='" . $value['text'] . "' selected>" . $value['text'] . "</option>" : "<option value='" . $value['text'] . "' >" . $value['text'] . "</option>";
                }
                $optionsGroup .= "</optgroup>";

                $selectGroup = '<select class="form-control groupSuperLinks" id="project_' . $link['id'] . '" name="project_' . $link['id'] . '"> ' . $optionsGroup . ' </select>';

                if($link['name']) {
                    $linkName = '
                        <button type="button" class="btn btn-sm btn-primary">
                          ' . $link['name'] . '
                        </button>
                    ';
                }else{
                    $slug = $link['slug'];
                    $slug = strtolower($slug);

                    $linkName = '
                        <button type="button" class="btn btn-sm btn-primary">
                          ' . ucfirst($slug) . '
                        </button>
                    ';
                }

                $link = [
                    'id' => $link['id'],
                    'project' => $selectGroup,
                    'name' => $linkName,
                    'slug' => '/' . $link['slug'],
                    'url' => $link['url'],
                    'status' => ($link['link_status'] == 'enabled') ? '<span class="text-success">Habilitado</span>' : '<span class="text-warning">Desabilitado</span>',
                    'selectRedirect' => $selectRedirect
                ];

                TableHelper::loadRowsImport($link);
            }

            if(!$links){
                $link = [];

                TableHelper::loadRowsImport($link);
            }

            TableHelper::tableEnd();
            if($links) {
                ?>
                <form id="importForm" action="" method="POST">
                    <input type="hidden" name="scenario" value="import">
                    <input type="hidden" name="<?= $importModel->getModelName() ?>[pluginToImport]" value="prettyLinks">
                </form>

                <button type="button" id="submitImport" class="btn btn-success btn-sm ml-2 mt-2 mb-3">
                    Importar os links selecionados
                </button>
                <?php
            }
            ?>

        </div>
        </div>
    </div>
</div>


<div class="modal fade" id="newGroupLink" tabindex="-1" role="dialog" aria-labelledby="newGroupLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newGroupLinkLabel">Adicionar nova categoria de link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
            FormHelper::formStart($groupModel);
            ?>
            <div class="modal-body">
                <?php
                FormHelper::text(
                    $groupModel,
                    'groupName',
                    [
                        'feedback' => [
                            'invalid-text' => TranslateHelper::getTranslate('Esse campo é obrigatório')
                        ]
                    ]
                );
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveNewGroup" class="btn btn-primary">Salvar Categoria</button>
            </div>
            <?php
            FormHelper::formEnd();
            ?>
        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function () {

        function changeColorSelectedLink(){
            const fields = jQuery('.selectImport')

            let contLink = 0
            fields.map(function (index, field) {
               const link = jQuery(field).parents('tr')
                if(jQuery(field).is(':checked')) {
                    jQuery(link).addClass('bg-secondary')
                    contLink++
                }else{
                    jQuery(link).removeClass('bg-secondary')
                }
            })

            let notifier = new Notifier({
                default_time: '2000'
            });

            if(contLink > 0) {
                notifier.notify('info', `Você tem ${contLink} links selecionados para importar`);
            }else{
                notifier.notify('info', `Você não tem nenhum link para importar`);
            }
        }

        jQuery(document).on('click', '#saveNewGroup', function (event) {
            const groupName = jQuery("#<?=FormHelper::getFieldId($groupModel, 'groupName')?>").val()

            const notifier = new Notifier({
                default_time: '4000'
            });

            if(groupName != '' && groupName != "undefined"){
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/saveNewGroupLink'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&groupName="+groupName

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        if(response.status){
                            notifier.notify('success', 'A categoria salva com sucesso!');
                            preencheCampoCategoriaLink(groupName, response.id)
                        }else{
                            notifier.notify('warning', 'Não foi possível salvar. Verifique se essa categoria já existe.');
                        }
                    }else if (http.readyState == 4 && http.status != 200) {
                        notifier.notify('warning', 'Não foi possível salvar. Verifique se essa categoria já existe.');
                    }
                    jQuery('#newGroupLink').modal('hide')
                }

                http.send(params);

            }else{
                notifier.notify('warning', 'Não é possível salvar uma categoria sem nome');
            }
        })

        jQuery('#newGroupLink').on('hidden.bs.modal', function (e) {
            jQuery("#<?=FormHelper::getFieldId($groupModel, 'groupName')?>").val("")
        })

        function preencheCampoCategoriaLink(groupName = '', idGroup = null){
            const fields = jQuery(".groupSuperLinks")

            fields.map(function(index,field) {
                jQuery(field).attr("disabled", "disabled")
                setTimeout(function () {
                    jQuery(field).find('.appendOptionsSPL').append(` <option value="${groupName}">${groupName}</option> `)
                    jQuery(field).removeAttr("disabled")
                }, 1000)
            })
        }

        jQuery(document).on('click', '.btn-filter', function () {
            const target = jQuery(this).data('target')
            if (target != 'all') {
                jQuery('.table .filterLink').css('display', 'none')
                jQuery('.table tr[data-status="' + target + '"]').show('slow')
            } else {
                jQuery('.table tr').css('display', 'none').show('slow')
            }
        })

        jQuery(document).on('click', '#copyLink',function (e) {
            e.preventDefault()
            let field = jQuery(this)

            let inputTest = document.createElement("input")
            inputTest.value = field.attr('data-target')
            document.body.appendChild(inputTest)
            inputTest.select()
            document.execCommand('copy')
            document.body.removeChild(inputTest)

            field.hide()
            field.html('Copiado!')
            field.show('slow')
            setTimeout(function(){
                field.hide()
                field.html('Copiar')
                field.show('slow')
            }, 2000)
        })

        jQuery(document).on('keyup', '#inputSearch', function () {
            const inputSearch = jQuery(this).val()
            searchSpl(inputSearch)
        })

        jQuery(document).on('click', "#importAll", function () {
            const fields = jQuery('.selectImport')
            const ischecked = jQuery(this).is(':checked')
            fields.map(function (index, field) {
                jQuery(field).prop( "checked", ischecked )
            })

            changeColorSelectedLink()
        })

        jQuery(document).on('click', ".selectImport", function () {
            changeColorSelectedLink()
        })

        jQuery(document).on('click', '#submitImport', function () {
            const fields = jQuery('.selectImport')
            let existFieldToImport = false

            if (confirm("Deseja importar os links selecionados abaixo?")) {
                fields.map(function (index, field) {
                    const idLink = jQuery(field).attr('data-target')
                    if(jQuery(field).is(':checked')) {
                        const redirectType = jQuery("#select_"+idLink).val()
                        const groupSpl = jQuery("#project_"+idLink).val()
                        jQuery("#importForm").append(`<input type="hidden" name="import[link_${idLink}]" id="importLink_${idLink}" value="${idLink},${redirectType},${groupSpl}">`)
                        existFieldToImport = true
                    }else{
                        jQuery(`#importLink_${idLink}`).remove()
                    }
                })

                if(existFieldToImport) {
                    jQuery("#importForm").unbind('submit').submit()
                }else{
                    let notifier = new Notifier({
                        default_time: '4000'
                    });
                    notifier.notify('warning', 'Você precisa selecionar ao menos um link para importar.');
                }
            }
        })


        function searchSpl(input = '') {
            let filter, table, tr, td, i, txtValue, searchBox, j, tdBox, displayYes

            filter = input.toUpperCase()
            table = document.getElementById("table-spl")
            tr = table.getElementsByTagName("tr")

            displayYes = []
            // Loop through all table rows, and hide those who don't match the search query
            for (i = 0; i < tr.length; i++) {
                tdBox = tr[i].getElementsByTagName("td");
                searchBox = (tdBox.length - 1)
                for(j = 0; j < searchBox; j++){
                    td = tr[i].getElementsByTagName("td")[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            displayYes.push(tr[i])
                        }
                    }
                }

                if(tr[i].getAttribute('id') != 'table-head'){
                    tr[i].style.display = "none";
                }
            }

            for(i=0;i < displayYes.length; i++){
                displayYes[i].style.display = "";
            }
        }
    });
</script>