<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class FormHelper {

    /**
    * @param null $model
    * @param array $options: id, class
    * @param string $method
    * @param string $action
    */
    public static function formStart($model = null, $options = [], $method = 'post', $action = '#'){
        if(!is_null($model)){
    ?>
        <form id="<?php echo isset($options['id'])? $options['id'] : self::getFormId($model);?>" class="<?php echo isset($options['class'])? $options['class'] : '';?>" method="<?=$method?>" action="<?=$action?>" novalidate >
            <input type="hidden" name="scenario" value="<?php echo $model->getIsNewRecord()? 'insert' : 'update';?>">
        <?php
        }
    }

    public static function formEnd(){
       echo '</form>';
    }

    public static function inputHidden($model = null, $attribute = null, $options = [], $value = ''){
         if(!is_null($model) && !is_null($attribute)){
             $value = $value? $value : $model->getAttribute($attribute);
        ?>
                <input
                       type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : '';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>]"
                       style="display:none;"
                       value="<?=$value?>"
                >
    <?php
        }
    }

    /**
    * @param null $model
    * @param null $attribute
    * @param array $options: required, class, id, placeholder
     */
    public static function text($model = null, $attribute = null, $options = [], $label = ''){
         if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $label? $label : $model->attributeLabels()[$attribute] ?></label>
             <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                    <?php
                        }
                    ?>
                <input
                       type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo 'value="' . $model->getAttribute($attribute) . '"'; ?>
                       <?php echo isset($options['required'])? 'required' : '';?>
                       <?php echo isset($options['disabled'])? 'disabled' : '';?>
                       <?php echo isset($options['autocomplete'])? ' autocomplete="off" ' : '';?>
                >
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }

                    if(isset($options['feedback'])){
                        self::feedback(
                            isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                           isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                        );
                    }
                ?>

             </div>
    <?php
        }
    }

    public static function number($model = null, $attribute = null, $options = []){
         if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $model->attributeLabels()[$attribute] ?></label>
             <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="minusBtn" style="cursor: pointer;">-</span>
                </div>
                <input
                       type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                       id="numberField"
                       name="<?=$model->getModelName()?>[<?=$attribute?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo 'value="' . $model->getAttribute($attribute) . '"'; ?>
                       <?php echo isset($options['required'])? 'required' : '';?>
                       <?php echo isset($options['disabled'])? 'disabled' : '';?>
                       <?php echo isset($options['autocomplete'])? ' autocomplete="off" ' : '';?>
                >
                <div class="input-group-prepend">
                    <span class="input-group-text" id="plusBtn" style="cursor: pointer;">+</span>
                </div>
                <?php
                    if(isset($options['feedback'])){
                        self::feedback(
                            isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                           isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                        );
                    }
                ?>

             </div>
    <?php
        }
    }

    /**
    * @param null $model
    * @param null $attribute
    * @param array $options: required, class, id, placeholder
     */
    public static function textArea($model = null, $attribute = null, $options = []){
         if(!is_null($model) && !is_null($attribute)){
             $attributeVal = is_null($model->getAttribute($attribute))? '' : $model->getAttribute($attribute);
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $model->attributeLabels()[$attribute] ?></label>
            <textarea
                   class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                   id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                   name="<?=$model->getModelName()?>[<?=$attribute?>]"
                   <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                   <?php echo isset($options['required'])? ' required ' : '';?>
                   <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                   rows="6"
            ><?php echo htmlentities($attributeVal);?></textarea>
    <?php
            if(isset($options['feedback'])){
                self::feedback(
            isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
           isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                );
            }
        }
    }

     /**
    * @param null $model
    * @param null $attribute
    * @param array $options: class, id, placeholder
    */
    public static function select($model = null, $attribute = null, $options = [], $values = [], $label = ''){
        if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $label? $label : $model->attributeLabels()[$attribute] ?></label>
            <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                <?php
                    }
                ?>
            <select
                class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                name="<?=$model->getModelName()?>[<?=$attribute?>]"
                <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                >

                <?php
                foreach($values as $value){
                    if($model->getAttribute($attribute)){
                        $value['selected'] = false;
                    }
                    if($value['val'] == $model->getAttribute($attribute)){
                        $value['selected'] = true;
                    }
                    echo $value['selected']? "<option value='" .  $value['val'] . "' selected>" .  $value['text'] . "</option>" : "<option value='" .  $value['val'] . "' >" .  $value['text'] . "</option>";
                }
                ?>
            </select>
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }
                    if(isset($options['feedback'])){
                        self::feedback(
                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                );
                    }
                ?>
            </div>
    <?php
        }
    }


    public static function selectFacebook($model = null, $attribute = null, $options = [], $values = []){
        if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]">Apenas para os usuários</label>
            <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                <?php
                    }
                ?>
            <select
                class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                name="<?=$model->getModelName()?>[<?=$attribute?>]"
                <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                >

                <?php
                foreach($values as $value){
                    if($model->getAttribute($attribute)){
                        $value['selected'] = false;
                    }
                    if($value['val'] == $model->getAttribute($attribute)){
                        $value['selected'] = true;
                    }
                    echo $value['selected']? "<option value='" .  $value['val'] . "' selected>" .  $value['text'] . "</option>" : "<option value='" .  $value['val'] . "' >" .  $value['text'] . "</option>";
                }
                ?>
            </select>
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }
                    if(isset($options['feedback'])){
                        self::feedback(
                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                );
                    }
                ?>
            </div>
    <?php
        }
    }
    /**
    * @param string $validText
    * @param string $invalidText
     */
    public static function feedback($validText = '', $invalidText = ''){
        if(!empty($invalidText)){
        ?>
            <div class="invalid-feedback">
                <?=$invalidText?>
            </div>
        <?php
        }
        if(!empty($validText)){
        ?>
            <div class="valid-feedback">
                <?=$validText?>
            </div>
        <?php
        }
    }

    /**
    * @param null $model
    * @param string $text
    * @param array $options: id, class
    */
    public static function submitButton($model = null, $text = 'Salvar', $options = []){
        if(!is_null($model)){
        ?>
            <button type="submit"
                id="<?php echo isset($options['id'])? $options['id'] : self::getFormSubmitId($model);?>"
                class="btn <?php echo isset($options['class'])? $options['class'] : 'btn-success btn-lg';?>"
             >
             <?=$text?>
             </button>
        <?php
        }
    }

    /**
    * @param null $model
    * @param string $text
    * @param array $options: id, class
    */
    public static function submitButtonPP($model = null, $text = 'Salvar', $textStay = 'Salvar e permanecer na página', $textList = 'Salvar e ir para a lista', $options = []){
        if(!is_null($model)){
        ?>
             <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?=$text?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item saveAndStay" href="#" id="saveAndStay"><?=$textStay?></a>
                    <a class="dropdown-item saveAndGoToList" href="#" id="saveAndGoToList"><?=$textList?></a>
                </div>
            </div>
        <?php
        }
    }

    public static function dynamicTextFieldUpdate($model = null, $attribute = null, $field = 'A', $options = [], $label = '', $showRemoveLink = true, $disabled = true){
        if(!is_null($model) && !is_null($attribute)){
            $cont = 0;
           if($model->getAttribute($attribute)){
               foreach($model->getAttribute($attribute) as $id => $value){
                   switch ($cont){
                       case 0:
                           $field = 'A';
                           break;
                       case 1:
                           $field = 'B';
                           break;
                       case 2:
                           $field = 'C';
                           break;
                   }
    ?>
    <div id="box_<?=$id?>" class="row">
        <div class="col-md-6 mt-2">
           <label for="<?=self::getDynamicFieldId($model, $attribute, $field)?>"><?= $label? $label : $model->attributeLabels()[$attribute] ." " . $field?></label>
            <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                <?php
                    }
                ?>
                <input type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control affiliateUrl';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, $attribute, $field);?>"
                       data-field="<?=$field?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>][<?=$id?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo "value='" . $value . "'"; ?>
                       <?php echo isset($options['required'])? ' required ' : '';?>
                       <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                       <?php echo isset($options['autocomplete'])? ' autocomplete="off" ' : '';?>
                       <?php if($disabled){ ?> disabled <?php } ?>
                >
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }
                    if(isset($options['feedback'])){
                        self::feedback(
                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                );
                    }
                ?>
            </div>
        </div>
        <?php
            if($showRemoveLink){
         ?>
            <div class="col-md-6" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                <a class="button removeAffiliateLink" data-target="<?=$id?>">Remover este link de afiliado</a>
            </div>
        <?php } ?>
    </div>
    <?php
    $cont++;
                }
           }
        }
    }

     public static function dynamicTextFieldClone($model = null, $attribute = null, $field = 'A', $options = [], $label = '', $showRemoveLink = true){
        if(!is_null($model) && !is_null($attribute)){
            $cont = 0;
           if($model->getAttribute($attribute)){
               foreach($model->getAttribute($attribute) as $id => $value){
                   switch ($cont){
                       case 0:
                           $field = 'A';
                           break;
                       case 1:
                           $field = 'B';
                           break;
                       case 2:
                           $field = 'C';
                           break;
                   }
    ?>
    <div id="box_<?=$id?>" class="row">
        <div class="col-md-6 mt-2">
           <label for="<?=self::getDynamicFieldId($model, $attribute, $field)?>"><?= $label? $label : $model->attributeLabels()[$attribute] ." " . $field?></label>
            <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                <?php
                    }
                ?>
                <input type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, $attribute, $field);?>"
                       data-field="<?=$field?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>][<?=$id?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo "value='" . $value . "'"; ?>
                       <?php echo isset($options['required'])? ' required ' : '';?>
                       <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                       <?php echo isset($options['autocomplete'])? ' autocomplete="off" ' : '';?>
                >
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }
                    if(isset($options['feedback'])){
                        self::feedback(
                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                );
                    }
                ?>
            </div>
        </div>
        <?php
            if($showRemoveLink){
         ?>
            <div class="col-md-6" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                <a class="button removeAffiliateLinkClone" data-target="<?=$id?>">Remover este link de afiliado</a>
            </div>
        <?php } ?>
    </div>
    <?php
    $cont++;
                }
           }
        }
    }

     public static function dynamicTextField($model = null, $attribute = null, $field = 'A', $options = [], $label = ''){
        if(!is_null($model) && !is_null($attribute)){
            $value = $model->getAttribute($attribute);
            if(!$value || is_array($value)){
                $value = "";
            }
        ?>
        <div id="box_<?=$field?>" class="row">
            <div class="col-md-6 mt-2">
               <label for="<?=self::getDynamicFieldId($model, $attribute, $field)?>"><?= $label? $label : $model->attributeLabels()[$attribute] ." " . $field?></label>
                <div class="input-group">
                    <?php if(isset($options['prepend'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['prepend']?></span>
                        </div>
                    <?php
                        }
                    ?>
                    <input type="text"
                           class="<?php echo isset($options['class'])? $options['class'] : 'form-control affiliateUrl';?>"
                           id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, $attribute, $field);?>"
                           data-field="<?=$field?>"
                           name="<?=$model->getModelName()?>[<?=$attribute?>][]"
                           <?php echo "value='" . $value . "'"; ?>
                           <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                           <?php echo isset($options['required'])? ' required ' : '';?>
                           <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                    >
                    <?php if(isset($options['append'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['append']?></span>
                        </div>
                    <?php
                        }
                        if(isset($options['feedback'])){
                            self::feedback(
                                        isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                       isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                    );
                        }
                    ?>
                </div>
            </div>
            <?php
                if(!isset($options['hideRemoveLink'])){
             ?>
                    <div class="col-md-6" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                        <a class="button removeAffiliateLink" data-target="<?=$field?>">Remover este link de afiliado</a>
                    </div>
            <?php
                }
            ?>
        </div>
            <?php
        }
    }

     public static function dynamicCloneLink($model,$type, $label1, $label2, $field = '0', $options = []){
        if($model){
        ?>
        <div id="boxClone_<?=$type?>_<?=$field?>" class="row">
            <div class="col-md-4 mt-2">
               <label for="<?=self::getDynamicFieldId($model, 'pageItem', $field)?>"><?=$label1?></label>
                <div class="input-group">
                    <?php if(isset($options['prepend'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['prepend']?></span>
                        </div>
                    <?php
                        }
                    ?>
                    <input type="text"
                           class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                           id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'pageItem', $field);?>"
                           data-field="<?=$field?>"
                           name="<?=$model->getModelName()?>[pageItem][]"
                           value="<?php echo isset($options['value'])? $options['value'] : '';?>"
                           <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                           <?php echo isset($options['required'])? ' required ' : '';?>
                           <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                    >
                    <?php if(isset($options['append'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['append']?></span>
                        </div>
                    <?php
                        }
                        if(isset($options['feedback'])){
                            self::feedback(
                                        isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                       isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                    );
                        }
                    ?>
                </div>
            </div>
            <div class="col-md-1" style="margin-top: 50px; text-align: center;">
                <span class="dashicons dashicons-arrow-right-alt"></span><br>
            </div>
            <div class="col-md-4 mt-2">
               <label for="<?=self::getDynamicFieldId($model, 'newItem', $field)?>"><?=$label2?></label>
                <div class="input-group">
                    <?php if(isset($options['prepend'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['prepend']?></span>
                        </div>
                    <?php
                        }
                    ?>
                    <input type="text"
                           class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                           id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'newItem', $field);?>"
                           data-field="<?=$field?>"
                           name="<?=$model->getModelName()?>[newItem][]"
                           <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                           <?php echo isset($options['required'])? ' required ' : '';?>
                           <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                    >
                    <?php if(isset($options['append'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['append']?></span>
                        </div>
                    <?php
                        }
                        if(isset($options['feedback'])){
                            self::feedback(
                                        isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                       isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                    );
                        }
                    ?>
                </div>
                <input type="hidden" name="<?=$model->getModelName()?>[typeItem][]" value="<?=$type?>">
            </div>
             <?php
                if(!isset($options['hideRemoveLink'])){
             ?>
                    <div class="col-md-3" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                        <a class="button removeCloneLink" data-target="boxClone_<?=$type?>_<?=$field?>">Remover</a>
                    </div>
            <?php
                }
            ?>
        </div>
            <?php
        }
    }

     public static function dynamicCloneLinkUpdate($model,$type, $label1, $label2, $field = '0', $options = []){
        if($model){
            $pageitem = $model->getAttribute('pageItem');
            $newItem = $model->getAttribute('newItem');
            $typeItem = $model->getAttribute('typeItem');
             if($pageitem){
                 foreach($pageitem as $key => $valPageItem){
                    $valNewitem = $newItem[$key];
                    if($valPageItem && ($typeItem[$key] == $type)){
                    ?>
                    <div id="boxClone_<?=$type?>_<?=$key?>" class="row">
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'pageItem', $key)?>"><?=$label1?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'pageItem', $key);?>"
                                       data-field="<?=$key?>"
                                       name="<?=$model->getModelName()?>[pageItem][]"
                                       <?php echo "value='" . $valPageItem . "'"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                       disabled
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-1" style="margin-top: 50px; text-align: center;">
                            <span class="dashicons dashicons-arrow-right-alt"></span><br>
                        </div>
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'newItem', $key)?>"><?=$label2?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'newItem', $key);?>"
                                       data-field="<?=$key?>"
                                       name="<?=$model->getModelName()?>[newItem][]"
                                       <?php echo "value='" . $valNewitem . "'"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                       disabled
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                            <a class="button <?=($type=='link')? 'removeCloneLink' : 'removeCloneImage'?>" data-link="<?=$key?>" data-target="boxClone_<?=$type?>_<?=$key?>">Remover</a>
                        </div>
                    </div>
                <?php
                    }
                }
            }
        }
    }

     public static function dynamicCloneUniqueLink($model, $label1, $field = '0', $options = []){
        if($model){
            $idLinkPgBranca = $model->getAttribute('id');
        ?>
        <div id="boxClonePgBranca_<?=$idLinkPgBranca?>" class="row">
            <div class="col-md-4 mt-2">
               <label for="<?=self::getDynamicFieldId($model, 'checkoutProdutor', $field)?>"><?=$label1?></label>
                <div class="input-group">
                    <?php if(isset($options['prepend'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['prepend']?></span>
                        </div>
                    <?php
                        }
                    ?>
                    <input type="text"
                           class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                           id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'checkoutProdutor', $field);?>"
                           data-field="<?=$field?>"
                           name="<?=$model->getModelName()?>[checkoutProdutor][]"
                           value="<?php echo isset($options['value'])? $options['value'] : '';?>"
                           <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                           <?php echo isset($options['required'])? ' required ' : '';?>
                           <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                    >
                    <?php if(isset($options['append'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['append']?></span>
                        </div>
                    <?php
                        }
                        if(isset($options['feedback'])){
                            self::feedback(
                                        isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                       isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                    );
                        }
                    ?>
                </div>
            </div>
             <?php
                if(!isset($options['hideRemoveLink'])){
             ?>
                    <div class="col-md-3" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                        <a class="button removeClonePgBranca" data-target="boxClonePgBranca_<?=$idLinkPgBranca?>">Remover</a>
                    </div>
            <?php
                }
            ?>
        </div>
            <?php
        }
    }

     public static function dynamicCloneUniqueLinkUpdate($model, $label1, $field = '0', $options = []){
        if($model){
            $checkoutProdutor = $model->getAttribute('checkoutProdutor');
            $idLinkPgBranca = $model->getAttribute('id');
             if($checkoutProdutor){
                 $checkoutProdutor = unserialize($checkoutProdutor);
                 foreach($checkoutProdutor as $key => $valPageItem){
                    if($valPageItem){
                    ?>
                    <div id="boxClonePgBranca_<?=$idLinkPgBranca?>" class="row">
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'checkoutProdutor', $key)?>"><?=$label1?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'checkoutProdutor', $key);?>"
                                       data-field="<?=$key?>"
                                       name="<?=$model->getModelName()?>[checkoutProdutor][]"
                                       <?php echo "value='" . $valPageItem . "'"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                            <a class="button removeClonePgBranca" data-link="<?=$key?>" data-target="boxClonePgBranca_<?=$idLinkPgBranca?>">Remover</a>
                        </div>
                    </div>
                <?php
                    }
                }
            }else{
                 ?>
                 <div id="boxClonePgBranca_0" class="row">
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'checkoutProdutor', 0)?>"><?=$label1?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'checkoutProdutor', 0);?>"
                                       data-field="0"
                                       name="<?=$model->getModelName()?>[checkoutProdutor][]"
                                       <?php echo "value=''"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                        </div>
                 </div>
                 <?php
            }
        }
    }

    public static function dynamicCloneLinkClone($model,$type, $label1, $label2, $field = '0', $options = []){
        if($model){
            $pageitem = $model->getAttribute('pageItem');
            $newItem = $model->getAttribute('newItem');
             $typeItem = $model->getAttribute('typeItem');
             if($pageitem){
                 foreach($pageitem as $key => $valPageItem){
                    $valNewitem = $newItem[$key];
                    if($valPageItem && ($typeItem[$key] == $type)){
                    ?>
                    <div id="boxClone_<?=$type?>_<?=$key?>" class="row">
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'pageItem', $key)?>"><?=$label1?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'pageItem', $key);?>"
                                       data-field="<?=$key?>"
                                       name="<?=$model->getModelName()?>[pageItem][]"
                                       <?php echo "value='" . $valPageItem . "'"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                       <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-1" style="margin-top: 50px; text-align: center;">
                            <span class="dashicons dashicons-arrow-right-alt"></span><br>
                        </div>
                        <div class="col-md-4 mt-2">
                           <label for="<?=self::getDynamicFieldId($model, 'newItem', $key)?>"><?=$label2?></label>
                            <div class="input-group">
                                <?php if(isset($options['prepend'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['prepend']?></span>
                                    </div>
                                <?php
                                    }
                                ?>
                                <input type="text"
                                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control linkCloneUrl';?>"
                                       id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, 'newItem', $key);?>"
                                       data-field="<?=$key?>"
                                       name="<?=$model->getModelName()?>[newItem][]"
                                       <?php echo "value='" . $valNewitem . "'"; ?>
                                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                                       <?php echo isset($options['required'])? ' required ' : '';?>
                                       <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                                >
                                <?php if(isset($options['append'])){?>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?=$options['append']?></span>
                                    </div>
                                <?php
                                    }
                                    if(isset($options['feedback'])){
                                        self::feedback(
                                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                                );
                                    }
                                ?>
                            </div>
                            <input type="hidden" name="<?=$model->getModelName()?>[typeItem][]" value="<?=$type?>">
                        </div>
                        <div class="col-md-3" style="margin-top: 43px; margin-left: 0px; padding-left: 0px;">
                            <a class="button <?=($type=='link')? 'removeCloneLinkClone' : 'removeCloneLinkImage'?>"" data-target="boxClone_<?=$type?>_<?=$key?>">Remover</a>
                        </div>
                    </div>
                <?php
                   }
                }
            }
        }
    }

    public static function inputFile($model = null, $attribute = null, $options = []){
        if(!is_null($model) && !is_null($attribute)){
    ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $model->attributeLabels()[$attribute] ?></label>
             <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                    <?php
                        }
                    ?>
                <input
                       type="text"
                       class="form-control <?php echo isset($options['class'])? $options['class'] : '';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo 'value="' . $model->getAttribute($attribute) . '"'; ?>
                       <?php echo isset($options['required'])? ' required ' : '';?>
                       <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                       autocomplete="off"
                >
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend <?php echo isset($options['class'])? $options['class'] : '';?>">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }

                    if(isset($options['feedback'])){
                        self::feedback(
                            isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                           isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                        );
                    }
                ?>

             </div>
    <?php
        }
    }

    public static function selectMultiple($model = null, $attribute = null, $options = [], $values = []){
        if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $model->attributeLabels()[$attribute] ?></label>
            <div class="input-group">
                <?php if(isset($options['prepend'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['prepend']?></span>
                    </div>
                <?php
                    }
                ?>
                <?php
                 $name = (isset($options['multiple']))? $model->getModelName() . "[" . $attribute . "][]" : $model->getModelName() . "[" . $attribute . "]";
                 ?>
            <select
                class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                name="<?=$name?>"
                <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                <?php echo isset($options['multiple'])? ' multiple ' : '';?>
                <?php echo ' style="width: 100%!important; " ';?>
                >
                <?php
                $valSelected = $model->getAttribute($attribute);
                foreach($values as $value){
                    if(isset($value['type']) && $value['type'] == 'group' && $value['init']){
                       echo '<optgroup label="'.$value['text'].'">';
                    }elseif(isset($value['type']) && $value['type'] == 'group' && !$value['init']){
                        echo '</optgroup>';
                    }else{
                        if(is_array($valSelected)){
                            $value['selected'] = false;
                            if(in_array($value['val'],$valSelected)){
                                $value['selected'] = true;
                            }
                        }else{
                            if($valSelected){
                                $value['selected'] = false;
                            }
                            if($value['val'] == $valSelected){
                                $value['selected'] = true;
                            }
                        }

                        echo $value['selected']? "<option value='" .  $value['val'] . "' selected>" .  $value['text'] . "</option>" : "<option value='" .  $value['val'] . "' >" .  $value['text'] . "</option>";
                    }
                }
                ?>
            </select>
                <?php if(isset($options['append'])){?>
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?=$options['append']?></span>
                    </div>
                <?php
                    }
                    if(isset($options['feedback'])){
                        self::feedback(
                                    isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                   isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                );
                    }
                ?>
            </div>
    <?php
        }
    }

    public static function colorPicker($model = null, $attribute = null, $options = []){
         if(!is_null($model) && !is_null($attribute)){
        ?>
            <label for="<?=$model->getModelName()?>[<?=$attribute?>]"><?= $model->attributeLabels()[$attribute] ?></label>
             <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="<?php echo isset($options['id'])? $options['id'] . '_color' : self::getFieldId($model, $attribute) . '_color';?>" style="background-color: <?=$model->getAttribute($attribute)? $model->getAttribute($attribute) : 'rgb(255, 255, 255)';?> !important;">&nbsp;</span>
                    </div>
                <input
                       type="text"
                       class="<?php echo isset($options['class'])? $options['class'] : 'form-control';?>"
                       id="<?php echo isset($options['id'])? $options['id'] : self::getFieldId($model, $attribute);?>"
                       name="<?=$model->getModelName()?>[<?=$attribute?>]"
                       <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                       <?php echo 'value="' . $model->getAttribute($attribute) . '"'; ?>
                       <?php echo isset($options['data-target'])? 'data-target="'.$options['data-target'].'"' : '';?>
                       <?php echo isset($options['data-css'])? 'data-css="'.$options['data-css'].'"' : '';?>
                       <?php echo isset($options['required'])? 'required' : '';?>
                       <?php echo isset($options['disabled'])? 'disabled' : '';?>
                       <?php echo isset($options['autocomplete'])? ' autocomplete="off" ' : '';?>
                >
                <?php
                    if(isset($options['feedback'])){
                        self::feedback(
                            isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                           isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                        );
                    }
                ?>

             </div>
    <?php
        }
    }

    public static function dynamicTextFieldApiConvert($model = null, $attribute = null, $field = '1', $options = [], $label = ''){
        if(!is_null($model) && !is_null($attribute)){
        ?>
        <div class="row">
            <div class="col-md-12 mt-2">
               <label for="<?=self::getDynamicFieldId($model, $attribute, $field)?>"><?= $label? $label : $model->attributeLabels()[$attribute]?></label>
                <div class="input-group">
                    <?php if(isset($options['prepend'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['prepend']?></span>
                        </div>
                    <?php
                        }
                    ?>
                    <input type="text"
                           class="<?php echo isset($options['class'])? $options['class'] : 'form-control affiliateUrl';?>"
                           id="<?php echo isset($options['id'])? $options['id'] : self::getDynamicFieldId($model, $attribute, $field);?>"
                           data-field="<?=$field?>"
                           name="<?=$model->getModelName()?>[<?=$attribute?>][]"
                           <?php echo isset($options['placeholder'])? 'placeholder="' . $options['placeholder'] . '"' : '';?>
                           <?php echo isset($options['required'])? ' required ' : '';?>
                           <?php echo isset($options['disabled'])? ' disabled ' : '';?>
                    >
                    <?php if(isset($options['append'])){?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?=$options['append']?></span>
                        </div>
                    <?php
                        }
                        if(isset($options['feedback'])){
                            self::feedback(
                                        isset($options['feedback']['valid-text'])? $options['feedback']['valid-text'] : '',
                                       isset($options['feedback']['invalid-text'])? $options['feedback']['invalid-text'] : ''
                                    );
                        }
                    ?>
                </div>
            </div>
            <?php
                if(!isset($options['hideRemoveLink'])){
             ?>
                    <div class="col-md-12 mt-4" style="margin-left: 15px; padding-left: 0px;">
                        <a class="button removeEventApiConvert" data-target="<?=$field?>">Remover o evento acima</a>
                    </div>
            <?php
                }
            ?>
        </div>
            <?php
        }
    }

    public static function getDynamicFieldId($model = null, $attribute = null, $field = 'A'){
        if(!is_null($model) && !is_null($attribute)){
            return self::getFieldId($model, $attribute) . '_' . $field;
        }

        return '';
    }

    public static function getFieldId($model = null, $attribute = null){
        if(!is_null($model) && !is_null($attribute)){
            return $model->getModelName() . '_' . $attribute;
        }

        return '';
    }

    public static function getFormSubmitId($model = null){
        if(!is_null($model)){
            return $model->getModelName() . '_submit';
        }

        return '';
    }

    public static function getFormId($model = null){
        if(!is_null($model)){
            return $model->getModelName() . '_form';
        }

        return '';
    }
}