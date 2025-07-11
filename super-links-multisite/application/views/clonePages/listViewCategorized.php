<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$pageTitle = $this->pageData['pageTitle'];
$groupLinksModel = $this->groupLinkModel;
?>


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
                <p class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Procurar"
                           aria-label="Procurar" id="inputSearchCategory">
                </p>
            </div>
        </nav>
    </div>

    <?php

    $rowTitles = [
        $groupLinksModel->attributeLabels()['groupName'],
        '<i class="fas fa-cogs"></i>'
    ];
    TableHelper::loadTable($rowTitles);

    $groups = $this->pageData['groups'];
    $existLinkWithoutCategory = $this->pageData['existLinkWithoutCategory'];

    $linksByGroup =  $this->pageData['links'];

    $searchInGroup = array();
    foreach($linksByGroup as $linkGroupSplit){
        if(!$linkGroupSplit->idGroup) {
            $searchInGroup[] = strtoupper($linkGroupSplit->linkName);
            $searchInGroup[] = strtoupper($linkGroupSplit->keyWord);
        }
    }

    $searchInGroup = array_unique($searchInGroup);

    $searchInGroup = implode(';',$searchInGroup);

    if($existLinkWithoutCategory) {
        $actions = '  
                              <a class="btn btn-outline-warning btn-sm spl-actions-view" href="admin.php?page=super_links_list_Clones&idCategory=none" data-container="body" data-toggle="popover" data-placement="top" data-content="Ver links dessa categoria"><i class="fas fa-eye"></i></a>';
        $group = [
            0,
            $searchInGroup,
            'Links sem categoria',
            $actions
        ];

        TableHelper::loadRowsCategoriesListViewLinks($group);
    }

    foreach ($groups as $group) {
        $group = get_object_vars($group);

        $actions = '  
                              <a class="btn btn-outline-warning btn-sm spl-actions-view" href="admin.php?page=super_links_list_Clones&idCategory=' . $group['id'] . '" data-container="body" data-toggle="popover" data-placement="top" data-content="Ver links dessa categoria"><i class="fas fa-eye"></i></a>
                              <a class="btn btn-outline-primary btn-sm spl-actions-edit" href="admin.php?page=super_links_edit_groupClone&id=' . $group['id'] . '" data-container="body" data-toggle="popover" data-placement="top" data-content="Editar a categoria"><i class="fas fa-pen"></i></a>
                              <a class="btn btn-outline-danger btn-sm deleteGroup spl-actions-delete" data-target="' . $group['id'] . '" data-container="body" data-toggle="popover" data-placement="top" data-content="Excluir esta categoria"><em class="fa fa-trash"></em></a>';
        $idGroup = $group['id'];

        $addLinksModel = new SuperLinksAddLinkModel();
        $linksByGroup = $addLinksModel->getLinksByIDGroup($idGroup);

        $searchInGroup = array();
        foreach($linksByGroup as $linkGroupSplit){
            $searchInGroup[] = strtoupper($linkGroupSplit->linkName);
            $searchInGroup[] = strtoupper($linkGroupSplit->keyWord);
        }

        $searchInGroup = array_unique($searchInGroup);

        $searchInGroup = implode(';',$searchInGroup);

        $group = [
            $group['id'],
            $searchInGroup,
            $group['groupName'],
            $actions
        ];


        TableHelper::loadRowsCategoriesListViewLinks($group);
    }

    TableHelper::tableEnd();
    ?>

</div>


<script type="application/javascript">
    jQuery(document).ready(function () {

        let notifier = new Notifier({
            default_time: '4000'
        });

        jQuery(document).on('keyup', '#inputSearchCategory', function () {
            const inputSearch = jQuery(this).val()
            searchSplCategory(inputSearch)
        })

        jQuery(document).on('click', '.deleteGroup', function () {
            const idGroup = jQuery(this).attr('data-target')

            if (confirm("Deseja excluir esta categoria?")) {
                deleteCategory(idGroup).then(result => {
                    if(result.status){
                        jQuery("#group_"+idGroup).remove()
                        notifier.notify('success', 'A categoria foi excluída com sucesso!')
                    }else{
                        notifier.notify('warning', 'Não foi possível excluir essa categoria. Remova todos os links da categoria antes de excluí-la.')
                    }
                }).catch(err => {
                    notifier.notify('warning', 'Não foi possível excluir essa categoria. Remova todos os links da categoria antes de excluí-la.')
                })
            }
        })

        function deleteCategory(idGroup){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/deleteGroupClone'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&id="+idGroup

                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        try {
                            const response = JSON.parse(http.responseText)
                            resolve(response)
                        }catch (e) {
                            reject(false)
                        }
                    }else if(http.readyState == 4 && http.status != 200){
                        reject(false)
                    }
                }

                http.send(params);
            })
        }

        function searchSplCategory(input = '') {
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