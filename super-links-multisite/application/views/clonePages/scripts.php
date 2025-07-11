<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$addLinksModel = $this->addLinksModel;
$affiliateUrlModel = $this->affiliateUrlModel;
$configSocialModel = $this->configSocialModel;
$groupLinkModel = $this->groupLinkModel;
$monitoringModel = $this->monitoringModel;
$clonePageModel = $this->clonePageModel;
$apiConvertFaceModel = $this->apiConvertFaceModel;
?>

<div class="modal fade" id="newGroupLink" tabindex="-1" role="dialog" aria-labelledby="newGroupLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newGroupLinkLabel">Adicionar nova categoria de página</h5>
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
                        <br>
                        Essa ação pode demorar um pouco, por isso não feche essa janela.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function(){

        jQuery(document).on("change","#<?=FormHelper::getFieldId($monitoringModel, 'enableApiFacebook')?>", function(){
            let enableApi = jQuery(this).val()
            if(enableApi == 'enabled'){
                jQuery("#boxApiConvertFace").slideDown('slow');
            }else{
                jQuery("#boxApiConvertFace").slideUp('slow');
            }
        })

        jQuery(document).on('click', '#saveNewGroup', function (event) {
            const groupName = jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'groupName')?>").val()

            const notifier = new Notifier({
                default_time: '4000'
            });

            if(groupName != '' && groupName != "undefined"){
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/saveNewCloneGroup'; ?>

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
            jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'id')?>").attr("disabled", "disabled")
            jQuery("#spinner").addClass("spinner-border")

            jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'id')?> option").prop("selected", false);
            setTimeout(function () {
                jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'id')?>").append(`
                     <option selected value="${idGroup}">${groupName}</option>
                `)
                jQuery("#spinner").removeClass("spinner-border")
                jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'id')?>").removeAttr("disabled")
            }, 500)
        }

        jQuery(document).on("click", ".removeAffiliateLink", function(){
            const idAffiliateLink = jQuery(this).attr('data-target')

            if(typeof idAffiliateLink != 'undefined' && idAffiliateLink != 'A' && idAffiliateLink != 'B' && idAffiliateLink != 'C') {
                if (confirm("Deseja remover este link de afiliado? (as métricas deste link serão perdidas)")) {
                    removeCourse(idAffiliateLink).then(result => {
                        if (result.status) {
                            jQuery("#box_" + idAffiliateLink).remove()
                            lastField = jQuery(".dynamicField input").last()
                            dataField = jQuery(lastField).attr('data-field')

                            if (typeof dataField == 'undefined') {
                                jQuery(".dynamicField").append(`<?php FormHelper::dynamicTextField($affiliateUrlModel, 'affiliateUrl', 'A', ['required' => true, 'feedback' => ['invalid-text' => TranslateHelper::getTranslate('O link deve começar com http:// ou https://. Verifique o link digitado, pois não é válido')]]);?>`)
                            }
                        }
                    })
                }
            }else{
                jQuery("#box_" + idAffiliateLink).remove()
            }
        })


        function removeCourse(idAffiliateLink){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/removeAffiliateLink'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&id="+idAffiliateLink

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

        jQuery(document).on("click", ".removeCloneLink", function(){
            const idBoxCloneLink = jQuery(this).attr('data-target')
            const idCloneLink = jQuery(this).attr('data-link')
            if(typeof idCloneLink != 'undefined') {
                if (confirm("Deseja remover este link?")) {
                    removeCloneLink(idCloneLink).then(result => {
                        if (result.status) {
                            lastField = jQuery(".dynamicCloneField input").last()
                            dataField = jQuery(lastField).attr('data-field')

                            if (typeof dataField == 'undefined') {
                                jQuery(".dynamicCloneField").append(`<?php FormHelper::dynamicCloneLink($clonePageModel, 'link','Link de checkout do produtor',
                                    'Seu link de checkout de afiliado', '0', ['required' => true, 'feedback' => ['invalid-text' => TranslateHelper::getTranslate('Confira corretamente os endereços dos links')]]);?>`)
                            }
                        }
                    })
                }
            }

            jQuery("#" + idBoxCloneLink).remove()
        })

        jQuery(document).on("click", ".removeCloneImage", function(){
            const idBoxCloneLink = jQuery(this).attr('data-target')
            const idCloneLink = jQuery(this).attr('data-link')
            if(typeof idCloneLink != 'undefined') {
                if (confirm("Deseja remover esta imagem?")) {
                    removeCloneLink(idCloneLink).then(result => {
                        if (result.status) {
                            lastField = jQuery(".dynamicCloneImage input").last()
                            dataField = jQuery(lastField).attr('data-field')

                            if (typeof dataField == 'undefined') {
                                jQuery(".dynamicCloneImage").append(`<?php FormHelper::dynamicCloneLink($clonePageModel, 'image','Imagem ou texto na página a ser clonada',
                                    'Nova imagem ou texto', '0', ['required' => true, 'feedback' => ['invalid-text' => TranslateHelper::getTranslate('Confira corretamente os dados')]]);?>`)
                            }
                        }
                    })
                }
            }

            jQuery("#" + idBoxCloneLink).remove()
        })

        jQuery(document).on("click", ".removeCloneLinkClone", function(){
            const idAffiliateLink = jQuery(this).attr('data-target')

            jQuery("#boxClone_link_" + idAffiliateLink).remove()
        })

        jQuery(document).on("click", ".removeCloneLinkImage", function(){
            const idAffiliateLink = jQuery(this).attr('data-target')

            jQuery("#boxClone_image_" + idAffiliateLink).remove()
        })

        function removeCloneLink(idCloneLink){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/removeCloneLink'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax&id="+idCloneLink

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

        jQuery(document).on("click", "#addNewCloneLink", function(){
            const lastField = jQuery(".dynamicCloneField input").last()
            const dataField = jQuery(lastField).attr('data-field')

            if(getNextDataCloneField(dataField) != -1){
                jQuery(".dynamicCloneField").append(`<?php FormHelper::dynamicCloneLink($clonePageModel, 'link','Link de checkout do produtor',
                    'Seu link de checkout de afiliado', '${getNextDataCloneField(dataField)}', ['required' => true, 'feedback' => ['invalid-text' => TranslateHelper::getTranslate('Confira corretamente os endereços dos links')]]);?>`)
            }else{
                jQuery("#infodynamicCloneField").html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                              Você não pode adicionar mais campos
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`)
            }
        })

        jQuery(document).on("click", "#addNewCloneImage", function(){
            const lastField = jQuery(".dynamicCloneImage input").last()
            const dataField = jQuery(lastField).attr('data-field')

            if(getNextDataCloneField(dataField) != -1){
                jQuery(".dynamicCloneImage").append(`<?php FormHelper::dynamicCloneLink($clonePageModel, 'image','Imagem ou texto na página a ser clonada',
                    'Nova imagem ou texto', '${getNextDataCloneField(dataField)}', ['required' => true, 'feedback' => ['invalid-text' => TranslateHelper::getTranslate('Confira corretamente os endereços dos links')]]);?>`)
            }else{
                jQuery("#infodynamicCloneImage").html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                              Você não pode adicionar mais campos
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>`)
            }
        })

        function getNextDataCloneField(lastDataField = '0') {
            for(let i = 0; i < 30; i++){
                if(lastDataField == i){
                    let next = i + 1
                    return (next >= 30)? -1 : i
                }
            }
        }

        jQuery(document).on("keyup", "#<?=FormHelper::getFieldId($monitoringModel,'track')?>",function () {
            this.value = this.value.replace(/[^a-zA-Z_\-\b]+$/g,'')
        })

        jQuery(document).on("keyup", "#<?=FormHelper::getFieldId($addLinksModel,'keyWord')?>",function () {
            this.value = this.value.replace(/[^a-zA-Z0-9._/\-\b]+$/g,'')
            let keyWord = this.value

            if(this.value.length == 1 && this.value == '/'){
                this.value = ''
                jQuery("#keyWordComplete").html('')
            }else {
                jQuery("#keyWordComplete").html(keyWord)
            }
        })

        jQuery(document).on("keyup", "#<?=FormHelper::getFieldId($addLinksModel,'numberWhatsapp')?>",function () {
            this.value = this.value.replace(/[^0-9\b]+$/g,'')
        })

        jQuery("#<?=FormHelper::getFormId($addLinksModel)?>").submit(function(e){
            e.preventDefault()
            jQuery('#waitSave').modal('show')
            document.cookie = "toastSPL=Não feche essa janela até finalizar...; expires=60; path=/";
            document.cookie = "typeToastSPL=warning; expires=60; path=/";
            execNotify()
            var form = this;
            setTimeout(function() {
                jQuery(form).unbind('submit').submit();
            }, 1000);
        })

        function setValid() {
            jQuery("#<?=FormHelper::getFormId($addLinksModel)?>").find("input[type=text],select,textarea").each(function(index,value){
                jQuery(value).addClass('is-valid')
                jQuery(value).removeClass('is-invalid')
            })
        }

        // inicia validação
	    <?php
	    $errors = $this->errorSave;
	    if($errors){
            echo "jQuery('.collapse').removeClass('show'); ";
            echo 'let fieldID; ';
            echo 'let showErrorAffiliate = false; ';
            echo 'let headerParent; ';
            foreach($errors as $erro){
	    ?>
                fieldID = `#${'<?=$erro["model"]?>'}_${'<?=$erro["attribute"]?>'}`
                fieldID = jQuery(fieldID)

                if (fieldID.length == 0) {
                    fieldID = jQuery(`[name="${'<?=$erro["model"]?>'}[${'<?=$erro["attribute"]?>'}][]"]`)
                    showErrorAffiliate = true

                    if(!jQuery(fieldID[0]).val()){
                        jQuery(fieldID[0]).addClass('is-invalid')
                        jQuery(fieldID[0]).removeClass('is-valid')
                    }

                    if(fieldID[1] && !jQuery(fieldID[1]).val()){
                        jQuery(fieldID[1]).addClass('is-invalid')
                        jQuery(fieldID[1]).removeClass('is-valid')
                    }

                    if(fieldID[2] && !jQuery(fieldID[2]).val()){
                        jQuery(fieldID[2]).addClass('is-invalid')
                        jQuery(fieldID[2]).removeClass('is-valid')
                    }
                }


                if(!showErrorAffiliate) {
                    jQuery(fieldID).addClass('is-invalid')
                    jQuery(fieldID).removeClass('is-valid')
                }

                // abre os accordion que tem campos com erros
                headerParent = jQuery(fieldID).parents('.collapse')
                headerParent.addClass('show')
	    <?php
	        }
	    }
	    ?>
        // fim validação

        function showProxyBox() {
            jQuery("#enableRedirectJavascript").hide()
            jQuery("#enableRedirectJavascript").html(`
                <div class="row">
                    <div class="col-md-8 mb-3 mt-3">
                         <?php
            $values = [
                ['selected' => false, 'text' => TranslateHelper::getTranslate('Habilitado'), 'val' => 'enabled'],
                ['selected' => true, 'text' => TranslateHelper::getTranslate('Desabilitado (Recomendado)'), 'val' => 'disabled'],
            ];

            FormHelper::select(
                $addLinksModel,
                'enableProxy',
                [],
                $values
            );
            ?>

                    </div>
                </div>
                `)
            jQuery("#enableRedirectJavascript").fadeIn('slow')
        }

        showProxyBox()

        jQuery(document).on("click", ".uploadImage", function (e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
                .on('select', function (e) {
                    // This will return the selected image from the Media Uploader, the result is an object
                    var uploaded_image = image.state().get('selection').first()
                    // We convert uploaded_image to a JSON object to make accessing it easier
                    // Output to the console uploaded_image
                    var image_url = uploaded_image.toJSON().url

                    // Let's assign the url value to the input field
                    jQuery(".uploadImage").val(image_url)
                    jQuery("#showImage").html(`<img src="${image_url}" class="img-fluid">`)
                    jQuery("#showButtomRemoveImage").show()
                })
        })
        
        jQuery(document).on("click", "#removeImage", function () {
            jQuery(".uploadImage").val("")
            jQuery("#showImage").html("")
            jQuery("#showButtomRemoveImage").hide()
        })

        if(jQuery(".uploadImage").val() != ""){
            jQuery("#showButtomRemoveImage").show()
        }

        jQuery(document).on("keyup", "#<?=FormHelper::getFieldId($addLinksModel,'loadPopupAfterSeconds')?>",function () {
            this.value = this.value.replace(/[^0-9\b]+$/g,'')
        })

        jQuery(".colorPickerEscassez").on('colorpickerChange', function(event) {
            const id = jQuery(this).attr('id')
            let color = event.color.toString()
            jQuery(`#${id}_color`).css('background-color', color)
        })

        jQuery(".colorPicker").colorpicker()

        jQuery(document).on("click", "#addNovoEventoApi", function(){
            const lastField = jQuery(".dynamicFieldApi input").last();
            let dataField = jQuery(lastField).attr('data-field');
            dataField = parseInt(dataField);
            dataField++;

            let inputsApi = `
                <div class="row" id="box_${dataField}">
                    <div class="col-md-6 mb-3"><?php FormHelper::dynamicTextFieldApiConvert($apiConvertFaceModel, 'eventNameApiFacebook', '${dataField}', ['required' => true,]);?></div>
                    <div class="col-md-6 mb-3"><?php FormHelper::dynamicTextFieldApiConvert($apiConvertFaceModel, 'eventIdApiFacebook', '${dataField}', ['hideRemoveLink' => true]);?></div>
                </div>
            `;
            jQuery(".dynamicFieldApi").append(inputsApi);
        })

        jQuery(document).on("click", ".removeEventApiConvert", function(){
            const idAffiliateLink = jQuery(this).attr('data-target')
            jQuery("#box_" + idAffiliateLink).remove()
        })

        jQuery('.saveAndStay').click(function(e) {
            e.preventDefault();

            jQuery('#waitSave').modal('show');
            document.cookie = "toastSPL=Não feche essa janela até finalizar...; expires=60; path=/";
            document.cookie = "typeToastSPL=warning; expires=60; path=/";
            execNotify();

            var form = jQuery(this).closest('form');

            var inputElement = jQuery('<input>')
                .attr('type', 'hidden')
                .attr('name', 'stayPage')
                .attr('value', 'true');
            form.append(inputElement);

            setTimeout(function() {
                form.unbind('submit').submit();
            }, 1000);
        });


        jQuery('.saveAndGoToList').click(function(e) {
            e.preventDefault();

            jQuery('#waitSave').modal('show');
            document.cookie = "toastSPL=Não feche essa janela até finalizar...; expires=60; path=/";
            document.cookie = "typeToastSPL=warning; expires=60; path=/";
            execNotify();

            var form = jQuery(this).closest('form');

            setTimeout(function() {
                jQuery(form).unbind('submit').submit();
            }, 1000);
        });
    })

</script>
