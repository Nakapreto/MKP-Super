<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$pageTitle = $this->pageData['pageTitle'];
$pageData = $this->pageData;
$automaticLinks = new SuperLinksAutomaticLinksModel();
$automaticMetrics = new SuperLinksAutomaticMetricsModel();
?>

<div class="wrap">
    <div class="container">
        <?Php
        if (!isset($pageData['title']) || !$pageData['title']) {
            ?>
            <div class="row text-center">
                <div class="col-12">
                    <h3><?= $pageTitle ?></h3>
                </div>
            </div>
            <div class="border mt-2">
                <div class="row">
                    <div class="col">
                        <div class="box-link">
                            <p class="box-text "><?php TranslateHelper::printTranslate('Não encontramos o que você procurava') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            $keywords = explode(",",$pageData['keywords']);
            $keyTags = '';
            foreach($keywords as $key){
                if($key) {
                    $keyTags .= "<button type=\"button\" class=\"btn btn-secondary btn-sm mr-1\">$key</button>";
                }
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <h3><?= $pageTitle . ': <strong>' . $pageData['title'] . '</strong>' ?></h3>
                    <hr>
                </div>
            </div>
            <div class="mt-2">
                <div class="list-group mb-4">
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1"><strong><?= $automaticLinks->attributeLabels()['active'] ?></strong> <?= (isset($pageData['active']) && $pageData['active'])? '<span class="text-success">Sim</span>' : '<span class="text-warning">Não</span>' ?></p>
                        <p class="mb-1"><strong><?= $automaticLinks->attributeLabels()['num'] ?>:</strong> <?= $pageData['num'] ?></p>
                        <p class="mb-1"><strong><?= $automaticLinks->attributeLabels()['url'] ?>:</strong> <?= $pageData['url'] ?></p>
                        <p class="mb-1"><strong><?= $automaticLinks->attributeLabels()['keywords'] ?>:</strong> <?= $keyTags ?></p>
                    </a>
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
                                    <li class="col-md-2 my-1">
                                            <input class="form-control mr-sm-1 searchAccess" type="text" placeholder="T. acessos <" id="totalBiggerThen">
                                    </li>
                                    <li class="col-md-2 my-1">
                                            <input class="form-control mr-sm-1 searchAccess" type="text" placeholder="T. acessos >" id="totalLessThen">
                                    </li>
                                    <li class="col-md-2 my-1">
                                            <input class="form-control mr-sm-1 searchAccess" type="text" placeholder="A. únicos <" id="uniqueBiggerThen">
                                    </li>
                                    <li class="col-md-2 my-1">
                                            <input class="form-control mr-sm-1 searchAccess" type="text" placeholder="A. únicos >" id="uniqueLessThen">
                                    </li>
                                    <li class="col-md-4 my-1">
                                            <input class="form-control mr-sm-2" type="search" placeholder="Procurar" aria-label="Procurar" id="inputSearch">
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>

                    <?php

                    $rowTitles = [
                        $automaticMetrics->attributeLabels()['idPost'],
                        $automaticMetrics->attributeLabels()['keyword'],
                        $automaticMetrics->attributeLabels()['accessTotal'],
                        $automaticMetrics->attributeLabels()['uniqueTotalAccesses'],
                    ];
                    TableHelper::loadTable($rowTitles);

                    foreach ($pageData['metrics'] as $metric) {

                        $postData = get_post($metric->idPost);
                        $postName = $postData->post_title;
                        $linkPost = get_post_permalink($metric->idPost);

                        $metric = [
                            'id' => $metric->id,
                            'postName' => "<a href='$linkPost' target='_blank'>" . $postName . "</a>",
                            'keyword' => $metric->keyword,
                            'accessTotal' => $metric->accessTotal,
                            'uniqueTotalAccesses' => $metric->uniqueTotalAccesses,
                            ];
                        TableHelper::loadRowsAutomaticLinks($metric);
                    }

                    TableHelper::tableEnd();
                    ?>

                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function () {

        jQuery(document).on('keyup', '.searchAccess', function () {
            let inputSearch = jQuery(this).val()
            let typeSearch = jQuery(this).attr('id')

            let signSearch = '>'

            if(typeSearch == 'totalBiggerThen'){
                signSearch = '<'
                typeSearch = 'accessTotal'
            }else if(typeSearch == 'totalLessThen'){
                typeSearch = 'accessTotal'
            }else if(typeSearch == 'uniqueBiggerThen'){
                signSearch = '<'
                typeSearch = 'uniqueTotalAccesses'
            }else if(typeSearch == 'uniqueLessThen'){
                typeSearch = 'uniqueTotalAccesses'
            }

            jQuery('.searchAccess').val('')

            inputSearch = removeLetters(inputSearch)
            jQuery(this).val(inputSearch)
            searchAccess(inputSearch, typeSearch, signSearch)
        })

        function removeLetters(string = ''){
            string = string.replace(/[^0-9\b]+$/g,'')
            return string
        }

        function searchAccess(input = '', typeSearch = '', signSearch = '') {
            let filter, table, tr, td, i, txtValue, searchBox, j, tdBox, displayYes

            table = document.getElementById("table-spl")
            tr = table.getElementsByTagName("tr")

            if(input.length > 0) {
                filter = parseInt(input)
                displayYes = []
                // Loop through all table rows, and hide those who don't match the search query
                for (i = 0; i < tr.length; i++) {
                    tdBox = tr[i].getElementsByTagName("td");
                    searchBox = (tdBox.length - 1)
                    for (j = 0; j <= searchBox; j++) {
                        td = tr[i].getElementsByTagName("td")[j];
                        let target = td.getAttribute('data-target')

                        if (target == typeSearch) {
                            txtValue = td.textContent || td.innerText;
                            txtValue = parseInt(txtValue)
                            if (signSearch == '>') {
                                if (txtValue > filter) {
                                    displayYes.push(tr[i])
                                }
                            } else if (signSearch == '<') {
                                if (txtValue < filter) {
                                    displayYes.push(tr[i])
                                }
                            }
                        }
                    }

                    if (tr[i].getAttribute('id') != 'table-head') {
                        tr[i].style.display = "none";
                    }
                }

                for (i = 0; i < displayYes.length; i++) {
                    displayYes[i].style.display = "";
                }
            }else{
                for (i = 0; i < tr.length; i++) {
                    if (tr[i].getAttribute('id') != 'table-head') {
                        tr[i].style.display = "";
                    }
                }
            }
        }

        jQuery(document).on('keyup', '#inputSearch', function () {
            const inputSearch = jQuery(this).val()
            searchSpl(inputSearch)
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
