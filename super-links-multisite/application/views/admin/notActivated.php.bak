<?php
if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}
$message = isset($this->pageData['message'])? ' (' . $this->pageData['message'] . ')' : '';

$confereMensagem = trim($message);

if($confereMensagem == '(Mensagem: Invalid license key)'){
	$message = ' A licença que você inseriu não é Válida.';
}

if($confereMensagem == '(Mensagem: Reached maximum allowable domains)'){
	$message = ' Essa licença atingiu o limite máximo de instalações em domínios diferentes.';
}

?>

<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-12">
					<?php
					AlertHelper::displayAlert(TranslateHelper::getTranslate('O Super Links não foi ativado.') .  $message , 'danger');
					?>
                </div>
            </div>
        </div>
    </div>
</div>