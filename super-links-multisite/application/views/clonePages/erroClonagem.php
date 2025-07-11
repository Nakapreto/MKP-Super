<?php
if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}
$pageTitle = $this->pageData['pageTitle'];
$addLinkModel = $this->addLinksModel;

?>

    <div class="wrap">
        <div class="container">
            <div class="py-1">
                <div class="row justify-content-end">
                    <div class="col-8">
                        <h3><?= $pageTitle ?></h3>
                    </div>
                    <div class="col-4 text-right"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="card col-md-8 text-left">
                        <div class="card-body">
                            <h5 class="card-title text-info">ATENÇÃO:</h5>
                            <div>
                                <div>
                                    Infelizmente a página que você tentou clonar contém recursos específicos utilizados pelo produtor durante sua criação que impedem o processo de clonagem.
                                    Contudo, é importante destacar que essa restrição não se aplica a todas as páginas.
                                    <br><br>
                                    Para te ajudar nessa situação, elaboramos um artigo que explica os possíveis motivos que ocasionam essa dificuldade de clonagem, e mostramos algumas possíveis soluções.<br><br>
                                    Se você quiser saber mais a respeito, clique no link abaixo:
                                    <br>
                                    <div class="mt-3">
                                        <a class="btn btn-success btn-sm" href="https://wpsuperlinks.top/faq-erro-clonagem" target="_blank">Clique aqui para Saber mais</a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="btn btn-info btn-sm" href="admin.php?page=super_links_list_Clones">Clique aqui para voltar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

