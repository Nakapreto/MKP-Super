<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$pageTitle = isset($this->pageData['pageTitle']) ? $this->pageData['pageTitle'] : get_bloginfo('name');
$pageDescription = isset($this->pageData['pageDescription']) ? $this->pageData['pageDescription'] : get_bloginfo('description');

$textLoadPage = isset($this->pageData['textLoadPage']) ? $this->pageData['textLoadPage'] : TranslateHelper::getTranslate('Carregando...');
$showSpinner = isset($this->pageData['showSpinner']) ? $this->pageData['showSpinner'] : 'yes';

$redirectDelay = $this->pageData['redirectDelay'] * 1000;
$affiliateUrl = $this->pageData['affiliateUrl'];
$url = $this->pageData['url'];

$monitoringModel = isset($this->pageData['monitoringModel'])? $this->pageData['monitoringModel'] : [];

$paramUrlAfiliate = $this->pageData['paramUrlAfiliate'];
$affiliateUrl .= $paramUrlAfiliate;
?>
<!DOCTYPE html>
<html  <?php language_attributes(); ?>>
<head>
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>

    <meta name="robots" content="noindex">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">

    <title><?= $pageTitle ?></title>

    <meta itemprop="name" content="<?= $pageTitle ?>">
    <meta itemprop="description" content="<?= $pageDescription ?>">
    <meta name="description" content="<?= $pageDescription ?>">
    <?php if (isset($this->pageData['pageImage'])) { ?>
        <meta itemprop="image" content="<?= $this->pageData['pageImage'] ?>">
    <?php } ?>

    <meta property="og:title" content="<?= $pageTitle ?>"/>
    <meta property="og:description" content="<?= $pageDescription ?>">
    <?php if (isset($this->pageData['pageImage'])) {
        list($width, $height, $type, $attr) = getimagesize($this->pageData['pageImage']);
        ?>
        <meta property="og:image" content="<?= $this->pageData['pageImage'] ?>">
        <meta property="og:image:width" content="<?= $width ?>">
        <meta property="og:image:height" content="<?= $height ?>">
    <?php } ?>
    <meta property="og:url" content="<?= $url ?>">
    <meta property="og:type" content="website">


    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?>">
    <meta name="twitter:card" content="summary">
    <?php if (isset($this->pageData['pageImage'])) { ?>
        <meta name="twitter:image" content="<?= $this->pageData['pageImage'] ?>">
    <?php } ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel='stylesheet' href='<?=SUPER_LINKS_CSS_URL?>/javascript.css' media='all' />

    <?php
    if(isset($monitoringModel['googleMonitoringID']) && !empty($monitoringModel['googleMonitoringID'])){
        echo SuperLinksInterceptLinkController::getGoogleAnalyticsCode($monitoringModel['googleMonitoringID']);
    }

    if(isset($monitoringModel['pixelID']) && !empty($monitoringModel['pixelID'])){
        $track = (isset($monitoringModel['track']) && !empty($monitoringModel['track']))? $monitoringModel['track'] : 'PageView';
        echo SuperLinksInterceptLinkController::getPixelFacebookCode($monitoringModel['pixelID'], $track);
    }

    if(isset($monitoringModel['codeHeadPage']) && !empty($monitoringModel['codeHeadPage'])){
        echo $monitoringModel['codeHeadPage'];
    }
    ?>
</head>
<body>

    <?php
    if(isset($monitoringModel['codeBodyPage']) && !empty($monitoringModel['codeBodyPage'])){
        echo $monitoringModel['codeBodyPage'];
    }
    ?>

    <div class="center-height">
        <div class="text-center top-distance">
            <?php
            if($showSpinner == 'yes'){
                echo '<div class="spinner-border"></div>';
                echo '
                    <div>
                        <span class="small">'.$textLoadPage.'</span>
                    </div>
                ';
            }
            ?>
        </div>
    </div>

    <script>
        window.onload = function(){
            setTimeout(function(){
                document.location = '<?=$affiliateUrl?>'
            }, <?=$redirectDelay?>);
        }
    </script>

    <?php
    if(isset($monitoringModel['codeFooterPage']) && !empty($monitoringModel['codeFooterPage'])){
        echo $monitoringModel['codeFooterPage'];
    }
    ?>

</body>
</html>