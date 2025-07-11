<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$cookiePageModel = $this->cookiePageModel;
$groupCookieModel = $this->groupCookieModel;
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
            FormHelper::formStart($groupCookieModel);
            ?>
            <div class="modal-body">
                <?php
                FormHelper::text(
                    $groupCookieModel,
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

<script type="application/javascript">
    jQuery(document).ready(function(){

        jQuery(document).on('click', '#saveNewGroup', function (event) {
            const groupName = jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'groupName')?>").val()

            const notifier = new Notifier({
                default_time: '4000'
            });

            if(groupName != '' && groupName != "undefined"){
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/saveNewGroupLinkCookie'; ?>

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
            jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'groupName')?>").val("")
        })

        function preencheCampoCategoriaLink(groupName = '', idGroup = null){
            jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'id')?>").attr("disabled", "disabled")
            jQuery("#spinner").addClass("spinner-border")

            jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'id')?> option").prop("selected", false);
            setTimeout(function () {
                jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'id')?>").append(`
                     <option selected value="${idGroup}">${groupName}</option>
                `)
                jQuery("#spinner").removeClass("spinner-border")
                jQuery("#<?=FormHelper::getFieldId($groupCookieModel, 'id')?>").removeAttr("disabled")
            }, 500)
        }

        jQuery(document).on("keyup", "#<?=FormHelper::getFieldId($cookiePageModel,'timeCookie')?>",function () {
            this.value = this.value.replace(/[^0-9\b]+$/g,'')
            const position1 = jQuery(this).val()[0]
            if(jQuery(this).val().length > 1){
                jQuery(this).val(position1)
            }
        })

        jQuery("#<?=FormHelper::getFormId($cookiePageModel)?>").submit(function(e){
            e.preventDefault()
            jQuery('#waitSave').modal('show')
            setValid()

            validRules().then(result => {
                if(result.length == 0){
                    jQuery(this).unbind('submit').submit()
                }

                setTimeout(function(){
                    jQuery('#waitSave').modal('hide')
                }, 500)

            }).catch(err => {
                setTimeout(function(){
                    jQuery('#waitSave').modal('hide')
                }, 500)
            })
        })

        function setValid() {
            jQuery("#<?=FormHelper::getFormId($cookiePageModel)?>").find("input[type=text],select,textarea").each(function(index,value){
                jQuery(value).addClass('is-valid')
                jQuery(value).removeClass('is-invalid')
            })
        }

        function validRules(){
            return new Promise((resolve, reject) => {

                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/validate'; ?>

                const http = new XMLHttpRequest()
                const url = "<?=$url?>"
                let params = "type=ajax"

                <?php
                if(isset($this->exceptRules) && !empty($this->exceptRules)){
                ?>
                params += "&exceptRules=<?=$this->exceptRules?>"
                <?php
                }
                ?>

                jQuery("#<?=FormHelper::getFormId($cookiePageModel)?>").find("input[type=text],select,textarea").each(function (index, value) {
                    if(jQuery(value).hasClass("urlCookie")){
                        let urlCookieSplited = jQuery(value).val()
                        urlCookieSplited = urlCookieSplited.split("?",1)
                        params += "&" + jQuery(value).attr('name') + "=" + urlCookieSplited
                    }else{
                        params += "&" + jQuery(value).attr('name') + "=" + jQuery(value).val()
                    }
                })


                http.open('POST', url, true);

                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        const response = JSON.parse(http.responseText)
                        // fecha todos os accordions
                        jQuery('.collapse').removeClass('show')

                        let fieldID
                        response.map(function (value) {
                            fieldID = `#${value.model}_${value.attribute}`
                            fieldID = jQuery(fieldID)

                            let showErrorAffiliate = false
                            if (fieldID.length == 0) {
                                fieldID = jQuery(`[name="${value.model}[${value.attribute}][]"]`)
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
                            const headerParent = jQuery(fieldID).parents('.collapse')
                            headerParent.addClass('show')
                        })
                        resolve(response)
                    }
                }

                http.send(params);
            })
        }
    })

</script>
