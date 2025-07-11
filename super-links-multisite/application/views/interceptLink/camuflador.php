<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

$pageTitle = isset($this->pageData['pageTitle']) ? $this->pageData['pageTitle'] : get_bloginfo('name');
$pageDescription = isset($this->pageData['pageDescription']) ? $this->pageData['pageDescription'] : get_bloginfo('description');

$textLoadPage = isset($this->pageData['textLoadPage']) ? $this->pageData['textLoadPage'] : TranslateHelper::getTranslate('Carregando...');
$showSpinner = isset($this->pageData['showSpinner']) ? $this->pageData['showSpinner'] : 'yes';

$affiliateUrl = $this->pageData['affiliateUrl'];
$url = $this->pageData['url'];

$monitoringModel = isset($this->pageData['monitoringModel'])? $this->pageData['monitoringModel'] : [];

$urlRedirectBtn = isset($this->pageData['urlRedirectBtn'])? $this->pageData['urlRedirectBtn'] : '';
$faviconBlog = "";
if(get_site_icon_url()){
    $faviconBlog = ' <link rel="shortcut icon" href="'.get_site_icon_url().'" />';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>

    <meta name="robots" content="noindex">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">

    <title><?= $pageTitle ?></title>

    <?=$faviconBlog?>

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

    <link rel='stylesheet' href='<?=SUPER_LINKS_CSS_URL?>/camuflador.css' media='all' />

    <?php
    if(isset($monitoringModel['googleMonitoringID']) && !empty($monitoringModel['googleMonitoringID'])){
        echo SuperLinksInterceptLinkController::getGoogleAnalyticsCode($monitoringModel['googleMonitoringID']);
    }

    if(isset($monitoringModel['trackGoogle']) && !empty($monitoringModel['trackGoogle'])){
        echo SuperLinksInterceptLinkController::getGoogleEventCode($monitoringModel['trackGoogle']);
    }

    if(isset($monitoringModel['pixelID']) && !empty($monitoringModel['pixelID'])){
        $track = (isset($monitoringModel['track']) && !empty($monitoringModel['track']))? $monitoringModel['track'] : 'PageView';
        echo SuperLinksInterceptLinkController::getPixelFacebookCode($monitoringModel['pixelID'], $track);
    }

    if(isset($monitoringModel['codeHeadPage']) && !empty($monitoringModel['codeHeadPage'])){
        echo $monitoringModel['codeHeadPage'];
    }

    //Adiciona meta tag de verificação Facebook
    if(SUPER_LINKS_FACEBOOK_VERIFICATION) {
        echo '<meta name="facebook-domain-verification" content="'.get_option('facebookVerificationSPL').'" />';
    }
    ?>
</head>
<body>

<?php
if(isset($monitoringModel['codeBodyPage']) && !empty($monitoringModel['codeBodyPage'])){
    echo $monitoringModel['codeBodyPage'];
}
$style = ($this->pageData['enableRedirectJavascript'] == 'enabled') ? 'display: none;' : '';
?>

<iframe src="<?= $affiliateUrl ?>" class="iframe" style="<?=$style?>" height="100%" width="100%" noresize="noresize"></iframe>

<?php
if(isset($monitoringModel['codeFooterPage']) && !empty($monitoringModel['codeFooterPage'])){
    echo $monitoringModel['codeFooterPage'];
}

if($this->pageData['enableRedirectJavascript'] == 'enabled'){
    ?>
    <style id="styleCamuflador">

        .center-heightSPL {
            height: 100%;
        }

        .center-heightSPL .top-distanceSPL {
            margin-top: 20%;
        }

        .text-centerSPL {
            text-align: center;
        }

        @-webkit-keyframes spinner-borderSPL {
            to {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes spinner-borderSPL {
            to {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .spinner-borderSPL{
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-borderSPL .75s linear infinite;
            animation: spinner-borderSPL .75s linear infinite;
        }

        .spinner-border-smSPL {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        @-webkit-keyframes spinner-growSPL {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }
            50% {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
        }

        @keyframes spinner-growSPL {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }
            50% {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
        }

        .spinner-growSPL {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: text-bottom;
            background-color: currentColor;
            border-radius: 50%;
            opacity: 0;
            -webkit-animation: spinner-growSPL .75s linear infinite;
            animation: spinner-growSPL .75s linear infinite;
        }

        .spinner-grow-smSPL {
            width: 1rem;
            height: 1rem;
        }


        .smallSPL {font-size:80%}
    </style>
    <div class="center-heightSPL">
        <div class="text-centerSPL top-distanceSPL">
            <?php
            if($showSpinner == 'yes'){
                echo '<div class="spinner-borderSPL"></div>';
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
        window.addEventListener("load", function(event) {
            let existFrame = false

            document.getElementsByClassName('iframe')[0].onload = function(){
                var conteudoIframe = this.contentWindow
                if(conteudoIframe.length == 0){
                    document.location = '<?=$affiliateUrl?>'
                }else{
                    this.style.display = ""
                    existFrame = true
                }
            }

            if(!existFrame) {
                document.location = '<?=$affiliateUrl?>'
            }
        })
    </script>
    <?php
}
?>
<?php

if($urlRedirectBtn) {
    ?>
    <script>
        document.documentElement.addEventListener('mouseleave', function(e){
            if (e.clientY > 20) { return; }
            document.location = "<?=$urlRedirectBtn?>"
        })
    </script>
    <script>
        history.pushState({}, "", location.href)
        history.pushState({}, "", location.href)
        window.addEventListener("popstate", function(event) {
            setTimeout(function () {
                location.href = "<?=$urlRedirectBtn?>"
            }, 1)
        })
    </script>
    <?php
}else{
    $cookiesLinks = new SuperLinksCookieLinkController('SuperLinksLinkCookiePageModel');
    $cookiesLinks->execCookieSuperLinks();
}
$numberWhatsapp = isset($this->pageData['numberWhatsapp'])? $this->pageData['numberWhatsapp'] : false;
$textWhatsapp = isset($this->pageData['textWhatsapp'])? '?text='.$this->pageData['textWhatsapp'] : '';

if($numberWhatsapp) {
    echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <a href="https://wa.me/'.$numberWhatsapp.$textWhatsapp.'" style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 1px 1px 2px #888;
      z-index:10000;" target="_blank">
    <i style="margin-top:16px" class="fa fa-whatsapp"></i>
    </a>
    ';
    ?>
<?php
}

$rgpd = isset($this->pageData['rgpd']) ? $this->pageData['rgpd'] : false;
if($rgpd && function_exists('active_rgpd_box')){
    require_once("executaRGPD.php");
    rgpdSuperLinks();
}

?>

</body>
</html>