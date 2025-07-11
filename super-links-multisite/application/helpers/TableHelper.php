<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class TableHelper {

    public static function loadTable($rowTitles = []){
        if(empty($rowTitles)) {
            return;
        }
        ?>
        <table class="table table-hover table-bordered" id="table-spl">
            <thead style="text-align: center;">
                <tr id="table-head">
                    <?php
                    foreach($rowTitles as $rowTitle){
                        echo "<th scope='col'>$rowTitle</th>";
                    }
                    ?>
                </tr>
            </thead>
        <tbody style="text-align: center;">
        <?php
    }

    public static function loadRows($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row" colspan="6"><?=TranslateHelper::getTranslate('Ainda não foi criado nenhum link')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values[2] . '" id="link_' . $values[0] . '" class="filterLink" >';
            echo '<td style="width: 60px;">
                      <div class="form-check">
                         <input class="form-check-input checkboxLinkSpl" style="margin-top: 10px; margin-left: -17px;" type="checkbox" data-target="'.$values[0].'" id="check_'.$values[0].'">
                      </div>
                 </td>';
            unset($values[0]);
            foreach ($values as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsIps($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row" colspan="4"><?=TranslateHelper::getTranslate('Ainda não existem dados')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values[2] . '" id="link_' . $values[0] . '" class="filterLink" >';
            foreach ($values as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsClone($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row" colspan="5"><?=TranslateHelper::getTranslate('Ainda não foi criada nenhuma página')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values[2] . '" id="link_' . $values[0] . '" class="filterLink" >';
            echo '<td style="width: 60px;">
                      <div class="form-check">
                         <input class="form-check-input checkboxLinkSpl" style="margin-top: 10px; margin-left: -17px;" type="checkbox" data-target="'.$values[0].'" id="check_'.$values[0].'">
                      </div>
                 </td>';
            unset($values[0]);
            unset($values[2]);
            foreach ($values as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsCookies($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row" colspan="5"><?=TranslateHelper::getTranslate('Ainda não foi criado nenhum cookie')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values['cookieName'] . '" id="link_' . $values['id'] . '" class="filterLink" >';
            unset($values['id']);
            foreach ($values as $value) {
                echo "<td style='max-width: 200px; font-size: 0.8em; word-wrap: break-word;'>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsImport($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row" colspan="6"><?=TranslateHelper::getTranslate('Não existem links para importar deste plugin')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values['slug'] . '" id="link_' . $values['id'] . '" class="filterLink" >';
            echo "<td><input type='checkbox' data-target='".$values['id']."' class='selectImport'></td>";
            unset($values['id']);
            foreach ($values as $value) {
                echo "<td style='max-width: 200px; font-size: 0.8em; word-wrap: break-word;'>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsCategories($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row"><?=TranslateHelper::getTranslate('Ainda não foi criada nenhuma categoria')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values[1] . '" id="group_' . $values[0] . '" class="filterCategory">';
            unset($values[0]);
            foreach ($values as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsCategoriesListViewLinks($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row"><?=TranslateHelper::getTranslate('Ainda não foi criada nenhuma categoria')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values[1] . '" id="group_' . $values[0] . '" class="filterCategory">';
            unset($values[0]);
            $searchInGroup = $values[1];
            unset($values[1]);
            foreach ($values as $value) {
                echo "<td>$value <span style='display:none'>".$searchInGroup."</span></td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsAutomaticLinks($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row"><?=TranslateHelper::getTranslate('Ainda não foi criado nenhum link')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values["keyword"] . '" id="link_' . $values["id"] . '" class="filterLink" >';
            unset($values['id']);
            foreach ($values as $id => $value) {
                echo "<td data-target='$id'>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function loadRowsAutomaticViews($values = []){
        if(empty($values)) {
            ?>
            <tr>
                <td scope="row"><?=TranslateHelper::getTranslate('Ainda não foi criado nenhum link')?></td>
            </tr>
            <?php
        }else {

            echo '<tr data-status="' . $values["keywords"] . '" id="link_' . $values["id"] . '" class="filterLink" >';
            echo '<td style="width: 60px;">
                      <div class="form-check">
                         <input class="form-check-input checkboxLinkSpl" style="margin-top: 10px; margin-left: -17px;" type="checkbox" data-target="'.$values["id"].'" id="check_'.$values["id"].'">
                      </div>
                 </td>';
            unset($values['id']);
            unset($values['keywords']);
            foreach ($values as $value) {
                echo "<td>$value</td>";
            }
            echo '</tr>';
        }
    }

    public static function tableEnd(){
       echo '</tbody>
        </table>';
    }

}