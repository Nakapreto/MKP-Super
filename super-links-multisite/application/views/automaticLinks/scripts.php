<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$automaticLinksModel = $this->automaticLinksModel;
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
<style>
    .is-invalidSpl {
        border-color: #dc3545;
        padding-right: calc(1.5em + .75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem)
    }

    .is-validSpl {
        border-color: #28a745;
        padding-right: calc(1.5em + .75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem)
    }
</style>
<script type="application/javascript">
    jQuery(document).ready(function () {

        jQuery(document).on('click', '#saveNewGroup', function (event) {
            const groupName = jQuery("#<?=FormHelper::getFieldId($groupLinkModel, 'groupName')?>").val()

            const notifier = new Notifier({
                default_time: '4000'
            });

            if(groupName != '' && groupName != "undefined"){
                <?php $url = SUPER_LINKS_TEMPLATE_URL . '/saveNewAutomaticGroup'; ?>

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

        jQuery(document).on("click", "#plusBtn", function () {
            let val = jQuery("#numberField").val()
            val++
            jQuery("#numberField").val(val)
        })

        jQuery(document).on("click", "#minusBtn", function () {
            let val = jQuery("#numberField").val()
            val--
            if (val < 0) {
                val = 0
            }
            jQuery("#numberField").val(val)
        })

        jQuery(document).on("keyup", "#numberField", function () {
            let val = jQuery("#numberField").val()
            val = val.replace(/[^0-9\-\b]+$/g, '')
            if ((val < 0) || !val.length) {
                val = 0
            }
            jQuery("#numberField").val(val)
        })

        <?php
            $plc = $automaticLinksModel->getKeywords();
        ?>
        // tags support
        var taggle = new Taggle('automaticLinkskeywords', {
            placeholder: 'Digite aqui',
            tags: <?=json_encode($plc)?>,
            hiddenInputName: 'keywords[]',
            submitKeys: [9, 13, 188],
            delimiter: '\t',
            preserveCase: true,
        });

        jQuery("#<?=FormHelper::getFormId($automaticLinksModel)?>").submit(function (e) {
            e.preventDefault()

            jQuery('#waitSave').modal('show')

            setValid()

            let keywords = ''
            jQuery(".taggle_text").each(function (i, v) {
                if (typeof jQuery(v).html() == 'string') {
                    keywords += jQuery(v).html() + ','
                }

                jQuery("#<?=$automaticLinksModel->getModelName() . '_keywords'?>").val(keywords)
            })

            let existKeyword = keywords.length
            validRules().then(result => {
                const headerParent = jQuery("#automaticLinkskeywords").parents('.collapse')

                if (!existKeyword) {
                    jQuery("#automaticLinkskeywords").addClass('is-invalidSpl')
                    jQuery("#automaticLinkskeywords").removeClass('is-validSpl')
                    headerParent.addClass('show')
                } else {
                    jQuery("#automaticLinkskeywords").addClass('is-validSpl')
                    jQuery("#automaticLinkskeywords").removeClass('is-invalidSpl')
                    headerParent.removeClass('show')
                }

                if (result.length == 0 && existKeyword) {
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
            jQuery("#<?=FormHelper::getFormId($automaticLinksModel)?>").find("input[type=text],select,textarea").each(function (index, value) {
                jQuery(value).addClass('is-valid')
                jQuery(value).removeClass('is-invalid')
            })
        }

        function validRules() {
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

                jQuery("#<?=FormHelper::getFormId($automaticLinksModel)?>").find("input[type=text],select,textarea").each(function (index, value) {
                   if(jQuery(value).attr('name') == "SuperLinksAutomaticLinksModel[url]" && jQuery(value).val()) {
                       params += "&" + jQuery(value).attr('name') + "=1"
                   }else{
                       params += "&" + jQuery(value).attr('name') + "=" + jQuery(value).val()
                   }
                })

                params = params.replace("&undefined=", "")

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

                                if (!jQuery(fieldID[0]).val()) {
                                    jQuery(fieldID[0]).addClass('is-invalid')
                                    jQuery(fieldID[0]).removeClass('is-valid')
                                }

                                if (fieldID[1] && !jQuery(fieldID[1]).val()) {
                                    jQuery(fieldID[1]).addClass('is-invalid')
                                    jQuery(fieldID[1]).removeClass('is-valid')
                                }

                                if (fieldID[2] && !jQuery(fieldID[2]).val()) {
                                    jQuery(fieldID[2]).addClass('is-invalid')
                                    jQuery(fieldID[2]).removeClass('is-valid')
                                }
                            }


                            if (!showErrorAffiliate) {
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
