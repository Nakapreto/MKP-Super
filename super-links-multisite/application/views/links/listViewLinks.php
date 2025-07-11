<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$pageTitle = $this->pageData['pageTitle'];
$addLinkModel = $this->addLinksModel;
$this->groupLinkModel = new SuperLinksGroupLinkModel();
$groupLinkModel = $this->groupLinkModel;
?>


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
            FormHelper::formStart($groupLinkModel);
            ?>
            <div class="modal-body">
                <?php
                FormHelper::text(
                    $groupLinkModel,
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

<div class="modal fade" id="waitSave" tabindex="-1" role="dialog" aria-labelledby="waitSave" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only"></span>
                    </div>
                    <div class="">
                        Salvando dados...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= $pageTitle ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Você pode criar links especiais para fazer anúncios no Facebook, links redirecionadores com seu próprio site e muito mais...')?></p>
                </div>
                <div class="col-4 text-right">
                    <a class="btn btn-success btn-sm"
                       href="admin.php?page=super_links_add"><?= TranslateHelper::getTranslate('Adicionar novo link') ?></a>
                </div>
            </div>
            <hr>
        </div>

        <div class="row">
            <div class="col">
                <h6>Opções para os links selecionados na lista</h6>
            </div>
        </div>
        <a class="btn btn-danger btn-sm" id="btnDeleteAllLinks" href="#" ><?= TranslateHelper::getTranslate('Excluir links selecionados') ?></a>
        <a class="btn btn-warning btn-sm" id="btnZerarAllLinks" href="#" ><?= TranslateHelper::getTranslate('Zerar a contagem de cliques') ?></a>
        <a class="btn btn-primary btn-sm" id="btnCategorizarAllLinks" href="#" ><?= TranslateHelper::getTranslate('Colocar links em outra categoria') ?></a>

        <div class="row mt-4" style="display:none;" id="boxCategory">
            <div class="col-md-5 mb-3" id="categoryGroupLinks">
                <?php
                $values = $groupLinkModel->getAllGroupsValues();

                FormHelper::select(
                    $groupLinkModel,
                    'addLinksCategory',
                    [
                            'class' => 'inputCategoryLinks form-control'
                    ],
                    $values
                );
                ?>
                <span id="spinner"></span>
                <a href="#" data-toggle="modal" data-target="#newGroupLink">Clique aqui para adicionar nova categoria</a>
            </div>
            <div class="col-md-12 mt-2">
                <a class="btn btn-success btn-sm" id="btnExecCategoryForLinks" href="#" ><?= TranslateHelper::getTranslate('Adicionar os links selecionados na categoria acima') ?></a>
            </div>
        </div>
        <div class="border mt-4">
            <div class="card-header">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="Camuflador">Camuflador</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="Facebook">Facebook</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="Html">Html</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="Javascript">Javascript</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="Php">Php</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="pgBranca">Página Branca</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-filter" data-target="all">Todos <span id="contAllLinks"></span></a>
                            </li>
                        </ul>
                        <p class="form-inline my-2 my-lg-0">
                            <input class="form-control mr-sm-2" type="search" placeholder="Procurar"
                                   aria-label="Procurar" id="inputSearch">
                        </p>
                    </div>
                </nav>
            </div>

            <?php
            $countLinks = 0;
            $rowTitles = [
                '<input class="form-check-input" type="checkbox" id="checkAllLinks" style="margin-top: -20px; margin-left: -8px;">',
                $addLinkModel->attributeLabels()['linkName'],
                $addLinkModel->attributeLabels()['redirectType'],
                'Endereço do link',
                $addLinkModel->attributeLabels()['statusLink'],
                '<i class="fas fa-cogs"></i>'
            ];
            TableHelper::loadTable($rowTitles);

            $links = $this->pageData['links'];
            $totalAccessThisLink = $this->pageData['totalAccessThisLink'];

            // remove os links que são criados para redirecionamento no facebook

            $secondTypeRedirectFacebook = [];
            foreach ($links as $key => $link) {
                $link = get_object_vars($link);
                if($link['redirectType'] == 'facebook'){
                    $affiliateUrl = new SuperLinksAffiliateLinkModel();
                    $affiliateData = $affiliateUrl->getAllDataByParam($link['id'],'idLink');
                    if($affiliateData){
                        $affiliateData = array_shift($affiliateData);
                        $affiliateData = get_object_vars($affiliateData);
                        foreach($links as $id => $l){
                            $l = get_object_vars($l);
                            if($affiliateData['affiliateUrl'] == (SUPER_LINKS_TEMPLATE_URL . '/' . $l['keyWord']) ){
                                $totalAccessThisLink[$link['id']] = $totalAccessThisLink[$l['id']]; // coloca a metrica do link disponivel para o usuario no lugar da metrica do link visivel para os robos do facebook
                                $internalLinkData = new SuperLinksAddLinkModel();
                                $internalLinkData->loadDataByID($l['id']);
                                $secondTypeRedirectFacebook[$link['id']] = ucfirst($internalLinkData->getAttribute('redirectType'));
                                unset($links[$id]);
                            }
                        }
                    }
                }
            }

            $allLinksToShow = array();
            foreach ($links as $link) {
                $link = get_object_vars($link);

                $actions = '  <a class="btn btn-outline-success btn-sm" href="admin.php?page=super_links_clone_link&id='.$link['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Fazer uma cópia deste link"><i class="fas fa-clone"></i></a>
                              <a class="btn btn-outline-warning btn-sm spl-actions-view" href="admin.php?page=super_links_view_link&id='.$link['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Visualizar métricas do link"><i class="fas fa-eye"></i></a>
                              <a class="btn btn-outline-primary btn-sm spl-actions-edit" href="admin.php?page=super_links_edit_link&id='.$link['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Editar o link"><i class="fas fa-pen"></i></a>
                              <a class="btn btn-outline-danger btn-sm delete spl-actions-delete" data-target="'.$link['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Excluir este link"><em class="fa fa-trash"></em></a>';

                $accessThisLink = isset($totalAccessThisLink[$link['id']])? $totalAccessThisLink[$link['id']] : '0';

                $textAccessTotal = 'Total de acessos neste link: '.$accessThisLink;

                $linkName = '
                    <button type="button" class="btn btn-sm btn-primary" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$textAccessTotal.'">
                      #'.$link['id'] . ' - ' . $link['linkName'].' <span class="badge badge-light">Cliques: '.$accessThisLink.'</span>
                    </button>
                ';

                if($link['redirectType'] == 'pgBranca'){
                    $link['redirectType'] = 'Página Branca';
                }

                $redirectDataLink = ucfirst($link['redirectType']);

                if(isset($secondTypeRedirectFacebook[$link['id']])){
                    $redirectDataLink .= ' => ' . $secondTypeRedirectFacebook[$link['id']];
                }

                $link = [
                    $link['id'],
                    $linkName,
                    $redirectDataLink,
                    '<span data-container="body" data-toggle="popover" data-placement="top" data-content="'.$textAccessTotal.'"><a href="' . SUPER_LINKS_TEMPLATE_URL . '/' . $link['keyWord'] . '" target="_blank">/' . $link['keyWord'] . '</a> &nbsp;&nbsp;
                    <a id="copyLink" href="#" class="badge badge-success" data-target="' . SUPER_LINKS_TEMPLATE_URL . '/' . $link['keyWord'] . '">Copiar</a></span>',
                    ($link['statusLink'] == 'enabled')? '<span class="text-success">Habilitado</span>' : '<span class="text-warning">Desabilitado</span>',
                    $actions
                ];
                $allLinksToShow[] = $link;
            }

            $countLinks = count($allLinksToShow);

            foreach($allLinksToShow as $showLink){
                TableHelper::loadRows($showLink);
            }

            $existLinks = $this->pageData['existLinks'];

            if(!$existLinks){
                $link = [];

                TableHelper::loadRows($link);
            }

            TableHelper::tableEnd();
            ?>


        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function () {

        window.addEventListener("load", function(event) {
            <?php
            if($countLinks > 1 || $countLinks == 0){
                echo 'jQuery("#contAllLinks").html("('.$countLinks.' links)")';
            }else{
                echo 'jQuery("#contAllLinks").html("('.$countLinks.' link)")';
            }
            ?>
        });

        jQuery(document).on('click', '.btn-filter', function () {
            const target = jQuery(this).data('target')
            if(target == 'Facebook'){
                jQuery('.table .filterLink').css('display', 'none')
                jQuery('.table tr[data-status="Facebook => Camuflador"]').show('slow')
                jQuery('.table tr[data-status="Facebook => Html"]').show('slow')
                jQuery('.table tr[data-status="Facebook => Javascript"]').show('slow')
            }

            if (target != 'all' && target != 'Facebook') {
                jQuery('.table .filterLink').css('display', 'none')
                jQuery('.table tr[data-status="' + target + '"]').show('slow')
            } else if(target == 'all'){
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

        jQuery(document).on('click', '.delete', function () {
            const idLink = jQuery(this).attr('data-target')

            if (confirm("Deseja excluir este link?")) {
                deleteLink(idLink).then(result => {
                    if(result.status){
                        jQuery("#link_"+idLink).remove()
                        document.cookie = `toastSPL=O link #${idLink} foi excluído com sucesso; expires=60; path=/`;
                        execNotify()
                    }else{
                        document.cookie = `toastSPL=Houve um problema ao excluir o link #${idLink}; expires=60; path=/`;
                        document.cookie = "typeToastSPL=error; expires=60; path=/";
                        execNotify()
                    }
                })
            }
        })

        function deleteLink(idLink){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/delete'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&id="+idLink

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        resolve(response)
                    }else if (http.readyState == 4 && http.status != 200) {
                        reject(false)
                    }
                }

                http.send(params);
            })
        }

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

        jQuery(document).on("click", "#checkAllLinks", function () {
            if(jQuery(this).is(':checked')){
                checkAllLinks()
            }else{
                unCheckAllLinks()
            }
        })

        function checkAllLinks(){
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(!jQuery(value).is(':checked')){
                    jQuery(value).prop('checked', true)
                }
            })
        }

        function unCheckAllLinks(){
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(jQuery(value).is(':checked')){
                    jQuery(value).prop('checked', false)
                }
            })
        }

        jQuery(document).on("click", "#btnDeleteAllLinks", function () {
            let fieldsSelecteds = []
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(jQuery(value).is(':checked')){
                    fieldsSelecteds.push(value)
                }
            })

            if(fieldsSelecteds.length == 0){
                document.cookie = "toastSPL=Você deve selecionar pelo menos um link para excluir; expires=60; path=/";
                document.cookie = "typeToastSPL=warning; expires=60; path=/";
                execNotify()
            }else{
                if (confirm("Deseja excluir todos os links selecionados?")) {
                    jQuery('#waitSave').modal('show')
                    let totalCampos = fieldsSelecteds.length
                    let contCampos = 0
                    fieldsSelecteds.forEach(field => {
                        let idLink = jQuery(field).attr('data-target')
                        if(idLink) {
                            deleteLink(idLink).then(result => {
                                if (result.status) {
                                    jQuery("#link_" + idLink).remove()
                                    document.cookie = `toastSPL=O link #${idLink} foi excluído com sucesso; expires=60; path=/`;
                                    execNotify()
                                } else {
                                    document.cookie = `toastSPL=Houve um problema ao excluir o link #${idLink}; expires=60; path=/`;
                                    document.cookie = "typeToastSPL=error; expires=60; path=/";
                                    execNotify()
                                }
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            }).catch(err => {
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            })
                        }
                    })
                }
            }
        })
        jQuery(document).on("click", "#btnZerarAllLinks", function () {
            let fieldsSelecteds = []
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(jQuery(value).is(':checked')){
                    fieldsSelecteds.push(value)
                }
            })

            if(fieldsSelecteds.length == 0){
                document.cookie = "toastSPL=Você deve selecionar pelo menos um link para zerar a contagem; expires=60; path=/";
                document.cookie = "typeToastSPL=warning; expires=60; path=/";
                execNotify()
            }else{
                if (confirm("Deseja zerar a contagem de cliques para todos os links selecionados?")) {
                    jQuery('#waitSave').modal('show')
                    let totalCampos = fieldsSelecteds.length
                    let contCampos = 0
                    fieldsSelecteds.forEach(field => {
                        let idLink = jQuery(field).attr('data-target')
                        if(idLink) {
                            zerarCliquesLink(idLink).then(result => {
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            }).catch(err => {
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            })
                        }
                    })
                }
            }
        })

        function zerarCliquesLink(idLink){
            return new Promise((resolve,reject) => {
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/zerarClickLink'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = `type=ajax&id=${idLink}`

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        if (response.status) {
                            document.cookie = "toastSPL=Os cliques dos links foram zerados com sucesso!; expires=60; path=/";
                            document.cookie = "typeToastSPL=success; expires=60; path=/";
                            resolve(true)
                        } else {
                            document.cookie = "toastSPL=Não foi possível zerar os cliques agora, tente novamente mais tarde.; expires=60; path=/";
                            document.cookie = "typeToastSPL=warning; expires=60; path=/";
                            reject(false)
                        }
                    } else if (http.readyState == 4 && http.status != 200) {
                        document.cookie = "toastSPL=Não foi possível zerar os cliques agora, tente novamente mais tarde.; expires=60; path=/";
                        document.cookie = "typeToastSPL=warning; expires=60; path=/";
                        reject(false)
                    }
                }

                http.send(params);
            })
        }

        jQuery(document).on("click", "#btnCategorizarAllLinks", function () {
            let fieldsSelecteds = []
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(jQuery(value).is(':checked')){
                    fieldsSelecteds.push(value)
                }
            })

            if(fieldsSelecteds.length == 0){
                document.cookie = "toastSPL=Você deve selecionar pelo menos um link para colocá-lo em uma categoria; expires=60; path=/";
                document.cookie = "typeToastSPL=warning; expires=60; path=/";
                execNotify()
                jQuery("#boxCategory").hide()
            }else{
                jQuery("#boxCategory").show()
            }
        })

        jQuery(document).on('click', '#saveNewGroup', function (event) {
            const groupName = jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'groupName')?>").val()

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
            jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'groupName')?>").val("")
        })

        function preencheCampoCategoriaLink(groupName = '', idGroup = null){
            jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'addLinksCategory')?>").attr("disabled", "disabled")
            jQuery("#spinner").addClass("spinner-border")

            jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'addLinksCategory')?> option").prop("selected", false);
            setTimeout(function () {
                jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'addLinksCategory')?>").append(`
                     <option selected value="${idGroup}">${groupName}</option>
                `)
                jQuery("#spinner").removeClass("spinner-border")
                jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'addLinksCategory')?>").removeAttr("disabled")
            }, 500)
        }

        jQuery(document).on("click", "#btnExecCategoryForLinks", function () {
            let fieldsSelecteds = []
            jQuery(".checkboxLinkSpl").each(function(index,value){
                if(jQuery(value).is(':checked')){
                    fieldsSelecteds.push(value)
                }
            })

            if(fieldsSelecteds.length == 0){
                document.cookie = "toastSPL=Você deve selecionar pelo menos um link para colocá-lo em uma categoria; expires=60; path=/";
                document.cookie = "typeToastSPL=warning; expires=60; path=/";
                execNotify()
                jQuery("#boxCategory").hide()
            }else{
                jQuery("#boxCategory").hide()
                let idCategory = jQuery(".inputCategoryLinks").val()
                let categoryName = jQuery(".inputCategoryLinks option:selected").text()
                if (confirm(`Deseja colocar os links selecionados na categoria ${categoryName}?`)) {
                    jQuery('#waitSave').modal('show')
                    let totalCampos = fieldsSelecteds.length
                    let contCampos = 0

                    fieldsSelecteds.forEach(field => {
                        let idLink = jQuery(field).attr('data-target')
                        if(idLink) {
                            colocaLinksEmCategoria(idLink, idCategory).then(result => {
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            }).catch(err => {
                                contCampos++
                                if (contCampos == totalCampos) {
                                    document.location.reload(true)
                                }
                            })
                        }
                    })
                }
            }
        })

        function colocaLinksEmCategoria(idLink,idCategory){
            return new Promise((resolve,reject) => {
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/colocaLinksEmCategoria'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = `type=ajax&id=${idLink}&idCategory=${idCategory}`

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        if (response.status) {
                            document.cookie = "toastSPL=Os links foram movidos para a categoria escolhida com sucesso!; expires=60; path=/";
                            document.cookie = "typeToastSPL=success; expires=60; path=/";
                            resolve(true)
                        } else {
                            document.cookie = "toastSPL=Não foi possível mover os links para a categoria, tente novamente mais tarde.; expires=60; path=/";
                            document.cookie = "typeToastSPL=warning; expires=60; path=/";
                            reject(false)
                        }
                    } else if (http.readyState == 4 && http.status != 200) {
                        document.cookie = "toastSPL=Não foi possível mover os links para a categoria, tente novamente mais tarde.; expires=60; path=/";
                        document.cookie = "typeToastSPL=warning; expires=60; path=/";
                        reject(false)
                    }
                }

                http.send(params);
            })
        }
    });
</script>