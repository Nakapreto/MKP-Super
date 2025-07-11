<div id="splPop-<?php echo $args['id']; ?>" class="splPop" style="opacity:0;visibility:hidden;"
    <?php
    if(isset($args['timeout']) && !empty($args['timeout'])) { echo 'data-timeout="'.($args['timeout'] * 1000).'"'; }
    if(isset($args['animation']) && !empty($args['animation'])) { echo 'data-animation="'.$args['animation'].'"'; }
    if(isset($args['hook']) && !empty($args['hook'])) { echo 'data-hook="'.$args['hook'].'"'; }
    if(isset($args['expiration']) && !empty($args['expiration'])) { echo 'data-expiration="'.$args['expiration'].'"'; }
    ?>
    >
    <div class="p-content-wrapper" <?php if(isset($args['background']) && !empty($args['background'])) { echo 'style="background-color:'.$args['background'].';"'; } ?> >
        <span class="close"></span>
        <div class="splPop-content"><?php echo $args['content']; ?></div>
    </div>
</div>