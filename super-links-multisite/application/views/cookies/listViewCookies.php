<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$pageTitle = $this->pageData['pageTitle'];
$cookiePageModel = $this->cookiePageModel;
?>

<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-8">
                    <h3><?= $pageTitle ?></h3>
                    <p class="small"><?php TranslateHelper::printTranslate('Suas vendas vão aumentar com os cookies de afiliado ativado em suas páginas e posts.')?></p>
                </div>
                <div class="col-4 text-right">
                    <a class="btn btn-success btn-sm"
                       href="admin.php?page=super_links_cookiePost_add"><?= TranslateHelper::getTranslate('Adicionar nova configuração') ?></a>
                </div>
            </div>
            <hr>
        </div>
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
                'Tipo da configuração',
                $cookiePageModel->attributeLabels()['cookieName'],
                $cookiePageModel->attributeLabels()['idPost'],
                $cookiePageModel->attributeLabels()['statusCookie'],
                '<i class="fas fa-cogs"></i>'
            ];
            TableHelper::loadTable($rowTitles);

            $cookies = $this->pageData['cookies'];

            foreach ($cookies as $cookie) {
                $cookie = get_object_vars($cookie);

                $actions = '  
                              <a class="btn btn-outline-primary btn-sm spl-actions-edit" href="admin.php?page=super_links_cookiePost_edit&id='.$cookie['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Editar o cookie de afiliado"><i class="fas fa-pen"></i></a>
                              <a class="btn btn-outline-danger btn-sm delete spl-actions-delete" data-target="'.$cookie['id'].'" data-container="body" data-toggle="popover" data-placement="top" data-content="Excluir este cookie"><em class="fa fa-trash"></em></a>';

                $configType = '';

                if($cookie['urlCookie'] && $cookie['urlCamuflada']){
                    $configType =  '<button type="button" class="btn btn-sm btn-primary">
                                        Cookie afiliado e Popup saída
                                    </button>';
                }elseif($cookie['urlCookie'] && !$cookie['urlCamuflada']){
                    $configType =  '<button type="button" class="btn btn-sm btn-primary">
                                        Cookie afiliado
                                    </button>';
                }elseif($cookie['urlCamuflada'] && !$cookie['urlCookie']){
                    $configType =  '<button type="button" class="btn btn-sm btn-primary">
                                        Popup saída
                                    </button>';
                }else{
                    $configType = '';
                }

                $typePosts = [];

                $posts = $cookie['idPost'];
                $posts = explode(",",$posts);
                $pages = $cookie['idPage'];
                $pages = explode(",",$pages);

                $splinks = $cookie['linkSuperLinks'];
                $splinks = explode(",",$splinks);

                if(in_array('allPosts',$posts)){
                    $typePosts[] = 'Todos os posts';
                    $keyPost = array_search('allPosts', $posts);
                    unset($posts[$keyPost]);
                }
                if(in_array('allPages',$pages)){
                    $typePosts[] = 'Todas as páginas';
                    $keyPage = array_search('allPages', $pages);
                    unset($pages[$keyPage]);
                }
                if(in_array('all',$posts)){
                    $typePosts[] = 'Todos os posts e páginas';
                    $keyPage = array_search('allPages', $posts);
                    unset($posts[$keyPage]);
                }

                if(in_array('',$posts)){
                    $keyP = array_search('', $posts);
                    unset($posts[$keyP]);
                }

                if(in_array('',$pages)){
                    $keyP = array_search('', $pages);
                    unset($pages[$keyP]);
                }

                if($posts){
                    $typePosts[] = 'Posts selecionados';
                }

                if($pages){
                    $typePosts[] = 'Páginas selecionadas';
                }

                if(in_array('all',$splinks)){
                    $typePosts[] = 'Todos os links do SuperLinks';
                    $keySpl = array_search('all', $splinks);
                    unset($splinks[$keySpl]);
                }

                if(in_array('',$splinks)){
                    $keySp = array_search('', $splinks);
                    unset($splinks[$keySp]);
                }

                if(!empty($splinks)){
                    $typePosts[] = 'Links selecionados';
                }

                $typePosts = implode(" e ", $typePosts);

                $cookie = [
                    'id' => $cookie['id'],
                    'typeConfig' => $configType,
                    'cookieName' => $cookie['cookieName'],
                    'typePosts' => $typePosts,
                    'statusCookie' => ($cookie['statusCookie'] == 'enabled')? '<span class="text-success">Sim</span>' : '<span class="text-warning">Não</span>',
                    'actions' => $actions
                ];
                TableHelper::loadRowsCookies($cookie);
            }

            if(!$cookies){
                $cookie = [];

                TableHelper::loadRowsCookies($cookie);
            }

            TableHelper::tableEnd();
            ?>


        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function () {

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

            if (confirm("Deseja excluir este cookie?")) {
                deleteCookie(idLink).then(result => {
                    if(result.status){
                        jQuery("#link_"+idLink).remove()
                    }
                })
            }
        })

        function deleteCookie(idLink){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/deleteCookie'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&id="+idLink

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        resolve(response)
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
    });
</script>