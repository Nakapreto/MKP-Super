<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$pageTitle = $this->pageData['pageTitle'];
$affiliateData = isset($this->pageData['affiliate']) ? $this->pageData['affiliate'] : null;
$linkName = isset($this->pageData['linkName']) ? $this->pageData['linkName'] : null;
$keyWord = isset($this->pageData['keyWord']) ? $this->pageData['keyWord'] : null;
$redirectType = isset($this->pageData['redirectType']) ? $this->pageData['redirectType'] : null;
$statusLink = isset($this->pageData['statusLink']) ? $this->pageData['statusLink'] : null;
$ipsData = isset($this->pageData['ipsData']) ? $this->pageData['ipsData'] : null;
$idLink = $this->pageData['id'];
$addLinksModel = new SuperLinksAddLinkModel();
?>
<div class="modal fade" id="zerarLink" tabindex="-1" role="dialog" aria-labelledby="zerarLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zerarLinkLabel">Deseja zerar a contagem de cliques desta página?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveZerarLink" class="btn btn-primary">Sim</button>
                <button type="button" id="close" class="btn btn-warning" data-dismiss="modal">Não</button>
            </div>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="container">
        <?Php
        if (!$linkName) {
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
            ?>
            <div class="row">
                <div class="col-12">
                    <h3><?= $pageTitle . ': <strong>' . $linkName . '</strong>' ?></h3>
                    <hr>
                </div>
            </div>
            <div class="mt-2">
                <div class="list-group mb-4">
                    <div href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                        <p class="mb-1"><strong><?= $addLinksModel->attributeLabels()['statusLink'] ?>:</strong> <?= $statusLink? '<span class="text-success">Habilitado</span>' : '<span class="text-warning">Desabilitado</span>' ?></p>
                        <p class="mb-1"><strong>Endereço da página:</strong> <?= SUPER_LINKS_TEMPLATE_URL . '/' . $keyWord ?></p>
                        <a href="#" class="btn btn-primary mt-3 mb-3" data-toggle="modal" data-target="#zerarLink">Zerar contagem de cliques</a>
                    </div>
                    <div>
                        <p>Atenção: Zerar a contagem de cliques não zera a marcação de IP's</p>
                    </div>
                </div>
                <?php
                $affiliateModel = new SuperLinksAffiliateLinkModel();
                $metricsModel = new SuperLinksLinkMetricsModel();

                foreach ($affiliateData as $affiliateDatum) {
                    $dataAffiliate = $affiliateDatum['affiliateData'];
                    $metrics = isset($affiliateDatum['metrics'][0]) ? $affiliateDatum['metrics'][0] : [];
                    ?>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <div class="row mt-5">
                                <div class="col-md-4 offset-1">
                                    <div class="box-link">
                                        <h2 class="timer box-title count-number"><?= isset($metrics->accessTotal) ? $metrics->accessTotal : '0' ?></h2>
                                        <p class="box-text "><?= $metricsModel->attributeLabels()['accessTotal'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-1">
                                    <div class="box-link">
                                        <h2 class="timer box-title count-number"><?= isset($metrics->uniqueTotalAccesses) ? $metrics->uniqueTotalAccesses : '0' ?></h2>
                                        <p class="box-text "><?= $metricsModel->attributeLabels()['uniqueTotalAccesses'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <?Php
                }
                ?>
            </div>

            <div class="mt-2">
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
                                    <a class="nav-link btn-filter" data-target="all"><span id="contAllLinks"></span></a>
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
                $rowTitles = [
	                'IP',
	                'URL',
	                'Quantidade de acessos',
	                'Último acesso',
                ];

                TableHelper::loadTable($rowTitles);

                $quantidadeAcesso = 0;
                $ultimoAcesso     = '';
                $ip               = '';
                $url              = '';
                if ( $ipsData ) {
                    $dataHelper = new DateHelper();
	                foreach ( $ipsData as $ips_datum ) {
		                $ip          = $ips_datum->ipClient;
		                $url         = $ips_datum->url;
		                $datasAcesso = $ips_datum->datasAcesso;
		                if ( $datasAcesso ) {
			                $datasAcesso      = unserialize( $datasAcesso );
			                $quantidadeAcesso = count( $datasAcesso );
			                $ultimoAcesso     = array_pop( $datasAcesso );
		                }
		                TableHelper::loadRowsIps(array(
                            $ip,
                            $url,
                            $quantidadeAcesso,
			                $dataHelper->dateTimeSqlToBr($ultimoAcesso),
                        ));
	                }
                }else{
	                TableHelper::loadRowsIps(array());
                }

                TableHelper::tableEnd();
                ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function(){
        jQuery(document).on('click', '#saveZerarLink', function (event) {

            const notifier = new Notifier({
                default_time: '4000'
            });

            <?php $url = SUPER_LINKS_TEMPLATE_URL . '/zerarClickLink'; ?>

            const http = new XMLHttpRequest()
            const url = "<?=$url?>"
            let params = "type=ajax&id=<?=$idLink?>"

            http.open('POST', url, true);

            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function () {
                if (http.readyState == 4 && http.status == 200) {
                    const response = JSON.parse(http.responseText)
                    if(response.status){
                        notifier.notify('success', 'Os cliques do link foram zerados com sucesso!');
                        document.location.reload(true);
                    }else{
                        notifier.notify('warning', 'Não foi possível zerar os cliques agora, tente novamente mais tarde.');
                    }
                }else if (http.readyState == 4 && http.status != 200) {
                    notifier.notify('warning', 'Não foi possível zerar os cliques agora, tente novamente mais tarde.');
                }
                jQuery('#zerarLink').modal('hide')
            }

            http.send(params);

        })

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
    })
</script>