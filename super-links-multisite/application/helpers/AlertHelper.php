<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class AlertHelper {

    public static function displayAlert($text = '', $type = 'success'){
    ?>
        <div class="alert alert-<?=$type?> alert-dismissible fade show" role="alert">
            <?=$text?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php
    }
}