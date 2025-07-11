<?php
if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}
$feedbackClone = (isset($this->pageData['feedbackClone']) && $this->pageData['feedbackClone'])? $this->pageData['feedbackClone'] : false;
$linkPgClonada = (isset($this->pageData['linkPgClonada']) && $this->pageData['linkPgClonada'])? $this->pageData['linkPgClonada'] : false;
$opcl = isset($this->pageData['opcl'])? $this->pageData['opcl'] : false;
$id = isset($this->pageData['id'])? $this->pageData['id'] : false;
$obsFeedback = isset($this->pageData['obsFeedback'])? $this->pageData['obsFeedback'] : false;
?>

<div class="wrap">
    <div class="container">
        <div class="py-1">
            <div class="row justify-content-end">
                <div class="col-12">
                    <?php
                    if($feedbackClone && $opcl == 'sim'){
                        echo "<h4>Obrigado, Sua resposta foi salva com sucesso!</h4>";
                    }

                    if($feedbackClone && $opcl == 'naoGostei' && $obsFeedback){
	                    echo "<h4>Obrigado, Seu feedback foi enviado com sucesso!</h4>";
                    }

                    if(!$feedbackClone){
	                    echo "<h4>Sua resposta já foi enviada.</h4>";
                    }

                    if($feedbackClone && $id && $opcl == 'naoGostei'){
	                   ?>
                        <br><br>
                        <h5>Você poderia nos dizer o motivo de não ter gostado da clonagem?</h5>
                        <p>Obs.: A resposta para esse campo é opcional</p>
                        <form action="<?=SUPER_LINKS_TEMPLATE_URL ?>/wp-admin/admin.php?page=super_links_opniao_Clone" method="post">
                            <input type="text" name="id" value="<?=$id?>" style="display: none;">
                            <input type="text" name="opcl" value="<?=$opcl?>" style="display: none;">
                            <textarea class="form-control" name="obsFeedback" rows="3" maxlength="500"></textarea>
                            <input type="submit" class="btn-success btn-sm mt-3" value="Enviar Feedback">
                        </form>
                    <?php
                    }
					?>
                </div>

                <div>
                    <div class="card col-md-8">
                        <div class="card-body">
                            <h5 class="card-title text-info">Porque esse retorno é importante?</h5>
                            <div>
                                <div>
                                    Nós, da Equipe NODZ, estamos constantemente em busca da melhoria dos nossos produtos. Com o seu feedback, conseguiremos avançar ainda mais e aprimorar cada vez mais o Super Links.<br> Sua opinião é fundamental para o nosso progresso. Agradecemos pelo seu apoio.
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if($linkPgClonada){
	                    echo "<p><br><a href='".$linkPgClonada."' class='btn-success btn-sm'>Clique aqui para voltar a página clonada</a></p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>