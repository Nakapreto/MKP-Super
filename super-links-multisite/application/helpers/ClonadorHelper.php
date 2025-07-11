<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class ClonadorHelper {

	private $tokenClient = 'RESfdh4848fjdKYpYah1591dsa';

	public function removeParametrosTrackeamentoLink($linkCheckout){
		if(!$linkCheckout){
			return "";
		}

		$linkCheckout = str_replace('&#038;', "&", $linkCheckout);
		$linkCheckout = str_replace('&amp;', "&", $linkCheckout);

		$pos = strpos($linkCheckout, '?_hi');
		if ($pos !== false) {
			$linkCheckout = substr($linkCheckout, 0, $pos);
		}

		$pos = strpos($linkCheckout, '&_hi');
		if ($pos !== false) {
			$linkCheckout = substr($linkCheckout, 0, $pos);
		}

		$pos = strpos($linkCheckout, '?_ga');
		if ($pos !== false) {
			$linkCheckout = substr($linkCheckout, 0, $pos);
		}

		$pos = strpos($linkCheckout, '&_ga');
		if ($pos !== false) {
			$linkCheckout = substr($linkCheckout, 0, $pos);
		}

		return $linkCheckout;
	}

	public function efetuaClonagem($urlOriginalProdutor, $idPaginaClonada, $htmlPagina){

		set_time_limit(0);

		$urlSite = rtrim($urlOriginalProdutor, "/");

		$urlSiteArquivos = $urlSite . '/'; // com barra no final
		$nomePasta = $idPaginaClonada;

		$urlSiteTroca = SUPER_LINKS_ELEMENTS_URL;

		$urlPastaElements = $urlSiteTroca .'/'. $nomePasta;

		$pastaSuperLinks = SUPER_LINKS_ELEMENTS_PATH;

		$pastaCss = $pastaSuperLinks.'/'.$nomePasta.'/css';
		$pastaJs = $pastaSuperLinks.'/'.$nomePasta.'/js';
		$pastaImg = $pastaSuperLinks.'/'.$nomePasta.'/img';

		if(!is_dir($pastaSuperLinks.'/'.$nomePasta)){
			mkdir($pastaSuperLinks.'/'.$nomePasta);
		}

		if(is_dir($pastaCss)){
			$this->delTree($pastaCss);
		}

		if(is_dir($pastaJs)){
			$this->delTree($pastaJs);
		}

		if(is_dir($pastaImg)){
			$this->delTree($pastaImg);
		}

		if(!is_dir($pastaCss)){
			mkdir($pastaCss);
			$this->initHtmlSilence($pastaCss);
		}

		if(!is_dir($pastaJs)){
			mkdir($pastaJs);
			$this->initHtmlSilence($pastaJs);
		}

		if(!is_dir($pastaImg)){
			mkdir($pastaImg);
			$this->initHtmlSilence($pastaImg);
		}

		$this->initHtmlSilence($pastaSuperLinks.'/'.$nomePasta);

		$html = $htmlPagina;
		if(!$htmlPagina) {
			$html = file_get_contents( $urlSite );
		}

		// Definir o padrão do script que será removido
		$pattern = '/<script[^>]*>var litespeed_vary[^<]*<\/script>/i';

		// Remover o script específico
		$html = preg_replace($pattern, '', $html);

		preg_match_all("/<script.*?src=[\"'](.*?\.js.*?)[\"'].*?>/i", $html, $jsFiles);
		preg_match_all("/<link.*?href=[\"']((?!https:\/\/fonts\.googleapis\.com).*?\.css.*?)[\"'].*?>/i", $html, $cssFiles);

		$css_files = $cssFiles[1];
		$js_files = $jsFiles[1];

		$css_contents = [];
		$js_contents = [];

		foreach ($css_files as $key => $css_file) {
			$css_file = $this->retornaLinkAbsoluto($urlSiteArquivos,$css_file);
			if($css_file) {
				$css_contents[$key] = @file_get_contents($css_file);
			}
		}

		foreach ($js_files as $key => $js_file) {
			$js_file = $this->retornaLinkAbsoluto($urlSiteArquivos,$js_file);
			if($js_file) {
				$js_contents[$key] = @file_get_contents($js_file);
			}
		}

		foreach ($css_contents as $key => $css_content) {
			$nomeArquivo = $pastaCss.'/css_' . $key . '.css';
			@file_put_contents($nomeArquivo , $css_content);
		}

		foreach ($js_contents as $key => $js_content) {
			$nomeArquivo = $pastaJs.'/js_' . $key . '.js';
			@file_put_contents($nomeArquivo, $js_content);
		}

		foreach ($css_files as $key => $css_file) {
			$nomeArquivo = '/css/css_' . $key . '.css';
			$arquivoCss = $urlPastaElements . $nomeArquivo;
			$html = str_replace("$css_file", "$arquivoCss", $html);
		}

		foreach ($js_files as $key => $js_file) {
			$nomeArquivo = '/js/js_' . $key . '.js';
			$arquivoJs = $urlPastaElements . $nomeArquivo;
			$html = str_replace("$js_file", "$arquivoJs", $html);
		}

		// MUDA URL IMAGENS

		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$xpath = new DOMXPath($doc);
		$imgTags = $xpath->query('//img');

		$this->trocaLinkImagem($imgTags, 'base64', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'src', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'data-srcset', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'data-src', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'data-lazy-src', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'srcset', $urlSiteArquivos, $pastaImg, $urlPastaElements);
		$this->trocaLinkImagem($imgTags, 'data-lazy-srcset', $urlSiteArquivos, $pastaImg, $urlPastaElements);

		// Procura pelas tags do Google Tag Manager
		$gtm_tags = $doc->getElementsByTagName('script');
		foreach ($gtm_tags as $tag) {
			$src = $tag->getAttribute('src');
			if (strpos($src, 'googletagmanager.com') !== false) {
				$tag->parentNode->removeChild($tag);
			}
		}

		// Procura pelas tags do Facebook Pixel
		$fb_tags = $doc->getElementsByTagName('script');
		foreach ($fb_tags as $tag) {
			$src = $tag->getAttribute('src');
			if (strpos($src, 'facebook.com') !== false && strpos($src, 'pixel') !== false) {
				$tag->parentNode->removeChild($tag);
			}
		}

		// Procura pelas tags do TikTok Pixel
		$tiktok_tags = $doc->getElementsByTagName('script');
		foreach ($tiktok_tags as $tag) {
			$src = $tag->getAttribute('src');
			if (strpos($src, 'tiktok.com') !== false && strpos($src, 'sdk') !== false) {
				$tag->parentNode->removeChild($tag);
			}
		}

		// procura por gtags
		$scripts = $doc->getElementsByTagName('script');
		foreach ($scripts as $script) {
			$content = $script->nodeValue;
			if (strpos($content, 'gtag(') !== false) {
				$script->parentNode->removeChild($script);
			}
		}

		return $doc->saveHTML();
	}

	public function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	private function initHtmlSilence($pasta){
		$doc = new DOMDocument();
		$html = "
		<html>
			<head>
				<title>Silence is golden</title>
			</head>
			<body>
				Silence is golden
			</body>
		</html>
		";
		@$doc->loadHTML($html);
		@file_put_contents($pasta.'/index.html', $doc->saveHTML());
		return true;
	}

	public function deletaPastasNaExclusaoPagina($nomePasta){
		$pastaSuperLinks = SUPER_LINKS_ELEMENTS_PATH;

		$pastaCss = $pastaSuperLinks.'/'.$nomePasta.'/css';
		$pastaJs = $pastaSuperLinks.'/'.$nomePasta.'/js';
		$pastaImg = $pastaSuperLinks.'/'.$nomePasta.'/img';

		if(!is_dir($pastaSuperLinks.'/'.$nomePasta)){
			return false;
		}

		if(is_dir($pastaCss)){
			$this->delTree($pastaCss);
		}

		if(is_dir($pastaJs)){
			$this->delTree($pastaJs);
		}

		if(is_dir($pastaImg)){
			$this->delTree($pastaImg);
		}

		$this->delTree($pastaSuperLinks.'/'.$nomePasta);

		return true;
	}

	public function retornaLinkAbsoluto($urlSite, $link) {
		// Verifica se o link começa com "http://" ou "https://"
		if (strpos($link, "http://") === 0 || strpos($link, "https://") === 0) {
			return $link; // É um caminho absoluto
		}

		// Verifica se o link começa com "/"
		if (strpos($link, "/") === 0) {
			return $urlSite . $link; // Adiciona o caminho completo do site ao link
		}

		// Verifica se o link começa com "../" ou "./"
		if (strpos($link, "../") === 0 || strpos($link, "./") === 0) {
			$linkParts = explode("/", $link);
			$urlParts = parse_url($urlSite);
			$urlPath = isset($urlParts['path']) ? $urlParts['path'] : '';
			$urlPathParts = explode("/", trim($urlPath, "/"));
			$newParts = array_merge($urlPathParts, $linkParts);
			$finalParts = array();
			foreach ($newParts as $part) {
				if ($part == "..") {
					array_pop($finalParts);
				} else if ($part != ".") {
					$finalParts[] = $part;
				}
			}
			return $urlParts['scheme'] . "://" . $urlParts['host'] . "/" . implode("/", $finalParts);
		}

		// Verifica se o link não contém "://"
		if (strpos($link, "://") === false) {
			return $urlSite . "/" . $link; // Adiciona o caminho completo do site ao link
		}

		// Caso não seja nenhum dos casos acima, assume-se que é um caminho absoluto
		return $link;
	}

	public function criar_pastas($pastas, $caminho_atual = '') {
		foreach ($pastas as $key => $pasta) {
			$caminho_completo = $caminho_atual . '/' . $key;
			if (!is_dir($caminho_completo)) {
				@mkdir($caminho_completo);
			}
			$this->criar_pastas($pasta, $caminho_completo);
		}
	}

	public function montar_pastas($caminho) {
		$pastas = explode('/', $caminho);
		$vetor_pastas = array();
		$pasta_atual =& $vetor_pastas;

		foreach ($pastas as $pasta) {
			$pasta_atual[$pasta] = array();
			$pasta_atual =& $pasta_atual[$pasta];
		}

		return $vetor_pastas;
	}

	public function salvaImg($urlSiteArquivos, $pastaImg, $linkImg, $urlPastaElements)
	{

		if (strpos($linkImg, $urlPastaElements) === 0) {
			return $linkImg;
		}

		if (strpos($linkImg, 'https://s.w.org/') === 0) {
			return $linkImg;
		}

		if (strpos($linkImg, 'https://www.facebook.com') === 0) {
			return $linkImg;
		}

		//removendo qualquer link dentro do patch da imagem
		$removeLinkimg = str_replace($urlSiteArquivos,'',$linkImg);

		$pattern = '/http[s]?:\/\/[^\/]*/';
		$replacement = '';

		$updated_str = preg_replace($pattern, $replacement, $removeLinkimg);

		$removeLinkimg = str_replace('//', '/', $updated_str);

		$removeLinkimg = explode('/',$removeLinkimg);
		$nomeDaImagem = array_pop($removeLinkimg);

		//ver se o nome da imagem contem size
		$nomeDaImagem = explode(' ', $nomeDaImagem);
		$tamanhoImagem = isset($nomeDaImagem[1])? ' ' . $nomeDaImagem[1] : '';
		$nomeDaImagem = $nomeDaImagem[0];

		$removeLinkimg = implode('/',$removeLinkimg);

		$pastas = $this->montar_pastas($removeLinkimg);

		$caminhoImg = $pastaImg;


		$this->criar_pastas($pastas,$caminhoImg);

		$nomeArquivo = $removeLinkimg. '/' . $nomeDaImagem;

		$nomeArquivo = trim($nomeArquivo);
		$caminhoArquivo = $caminhoImg. '/' .$nomeArquivo;

		$linkImagemCorreto = $this->retornaLinkAbsoluto($urlSiteArquivos,$linkImg);

		if($tamanhoImagem){
			$linkImagemCorreto = explode(' ', $linkImagemCorreto);
			$linkImagemCorreto = $linkImagemCorreto[0];
		}

		try{
			$dadosImagem = file_get_contents( $linkImagemCorreto );

			//removendo qualquer link dentro do patch da imagem
			$pattern = '/http[s]?:\/\/[^\/]*/';
			$replacement = '';

			$updated_str = preg_replace($pattern, $replacement, $caminhoArquivo);

			$caminhoArquivo = str_replace('//', '/', $updated_str);

//			if($caminhoArquivo == "C:\wamp64\www\wordpress/wp-content/plugins/super-links/elements/49/img/wp-content/uploads/2023/06/Tabela-WebP-2.webp"){
//				echo "<br>C: " . $caminhoImg;
//
//				echo "<br>link no site: " . $removeLinkimg;
//				echo "<br>img: " . $nomeDaImagem;
//				echo "<br>link img: " . $linkImagemCorreto;
//				echo "<br><pre> " ;
//				print_r($pastas);
//				die();
//			}else{
//				echo "<br>C: " . $caminhoImg;
//
//				echo "<br>link no site: " . $removeLinkimg;
//				echo "<br>img: " . $nomeDaImagem;
//				echo "<br>link img: " . $linkImagemCorreto;
//				echo "<br><pre> " ;
//				print_r($pastas);
//				echo "</pre><br><br>*********************************<br><br>";
//
//			}
			if(!is_file($caminhoArquivo)) {
				file_put_contents($caminhoArquivo, $dadosImagem);
			}
		} catch (Exception $e) {
			return $linkImg;
		}

		return $urlPastaElements. '/img/' .$nomeArquivo . $tamanhoImagem;
	}

	public function expressaoTroca($src, $urlSite){
		if (!preg_match('/^https?:\/\//', $src)) {
			$src = $urlSite . '/' . ltrim($src, '/');
			return $src;
		}

		return $src;
	}

	public function geraNovoSrc($imagem, $urlSite, $pastaImg, $urlPastaElements){
		$vetorSrc = explode(',', $imagem);
		$cont = 0;
		foreach ($vetorSrc as $srcAntigo) {
			$srcAntigo = trim($srcAntigo);
			$novoSrc = $this->expressaoTroca($srcAntigo, $urlSite);
			if ($novoSrc) {
				if ($cont == 0) {
					$novoSrc = explode(' ', $novoSrc);
					$imagemSrc = isset($novoSrc[0])? $novoSrc[0] : '';
				}
			}
			$cont++;
		}

		return $this->salvaImg($urlSite,$pastaImg,$imagemSrc, $urlPastaElements);
	}

	public function trocaLinkImagem($imgTags, $atributo, $urlSite, $pastaImg, $urlPastaElements){
		foreach ($imgTags as $img) {
			if($atributo != 'base64') {
				$src = $img->getAttribute($atributo);
				if ($src) {
					$ehBase64 = false;
					if (substr($src, 0, 5) == 'data:') {
						$ehBase64 = true;
					}

					if (!$ehBase64) {
						if ($atributo == 'srcset' || $atributo == 'data-lazy-srcset'|| $atributo == 'data-lazy-src'|| $atributo == 'data-srcset') {
							$vetorSrc = explode(',', $src);
							$novoVetorSrc = [];

							foreach ($vetorSrc as $srcAntigo) {
								$srcAntigo = trim($srcAntigo);
								$novoSrc = $this->expressaoTroca($srcAntigo, $urlSite);
								$urlImagem = $this->salvaImg($urlSite,$pastaImg,$novoSrc, $urlPastaElements);
								if ($urlImagem) {
									$novoVetorSrc[] = $urlImagem;
								}
							}

							$novoVetorSrc = implode(', ', $novoVetorSrc);

							$img->setAttribute($atributo, $novoVetorSrc);
						} else {
//							echo "<br>".$src."<Br>";
//							echo "<br>".$urlSite."<Br>";
							$novoSrc = $this->expressaoTroca($src, $urlSite);
//							echo "<br>".$novoSrc."<Br>";
							$urlImagem = $this->salvaImg($urlSite,$pastaImg,$novoSrc, $urlPastaElements);
//							echo "<br>".$urlImagem."<Br>********************<br>";
							if ($urlImagem) {
								$img->setAttribute($atributo, $urlImagem);
							}
						}
					}
				}
			}

			if($atributo == 'base64'){
				//substitui base64 pelo src
				$datasrc = $img->getAttribute('data-src');
				$datasrcset = $img->getAttribute('data-srcset');
				$srcset = $img->getAttribute('srcset');
				$srcsetLazy = $img->getAttribute('data-lazy-srcset');
				$srcLazy = $img->getAttribute('data-lazy-src');

				if($datasrcset) {
					$urlImagem = $this->geraNovoSrc($datasrcset, $urlSite, $pastaImg, $urlPastaElements);
					$img->setAttribute('src', $urlImagem);
				}

				if($srcset) {
					$urlImagem = $this->geraNovoSrc($srcset, $urlSite, $pastaImg, $urlPastaElements);
					$img->setAttribute('src', $urlImagem);
				}

				if($srcsetLazy) {
					$urlImagem = $this->geraNovoSrc($srcsetLazy, $urlSite, $pastaImg, $urlPastaElements);
					$img->setAttribute('src', $urlImagem);
				}

				if($srcLazy) {
					$urlImagem = $this->geraNovoSrc($srcLazy, $urlSite, $pastaImg, $urlPastaElements);
					$img->setAttribute('src', $urlImagem);
				}

				if($datasrc) {
					$urlImagem = $this->geraNovoSrc($datasrc, $urlSite, $pastaImg, $urlPastaElements);
					$img->setAttribute('src', $urlImagem);
				}
			}
		}
	}


	public function getUrlOriginalPgVendasProdutor($url){
		$urlEnviar = serialize($url);
		$urlApi = SUPER_LINKS_WEB_API . '/ApiSuperLinks/getUrlOriginalPgVendasProdutor?token='.$this->tokenClient.'&url='.$urlEnviar;

		$resultClone = wp_remote_get($urlApi, [
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		$conteudo = '';

		if (is_array($resultClone) && !is_wp_error($resultClone)) {
			$conteudo = $resultClone['body'];
		}

		if($conteudo){
			$conteudo = json_decode($conteudo);
		}

		if(isset($conteudo->urlProdutor)){
			return $conteudo->urlProdutor;
		}

		return $url;
	}

	public function removeReferenciaAfiliadoUrl($url){
		$urlNova = explode('?',$url);
		return $urlNova[0];
	}

	public function fazMarcacaoCookieSemIframe(){
		$url = 'https://www.exemplo.com/'; // URL da página que realiza a marcação de cookies
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$response = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		curl_close($ch);

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
		$cookies = array();
		foreach($matches[1] as $item) {
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}

		print_r($cookies);
	}

	public function getCodigoPaginaProdutor($saveHtmlClone, $htmlClonePage, $renovarHtml, $affiliateUrl, $urlToGetHtml, $idLinkPg){
		if($saveHtmlClone == 'enabled') {
			if (!$htmlClonePage) {
				$conteudo = $this->buscaESalvaHtmlProdutor($urlToGetHtml,$idLinkPg);
			} else {
				if($renovarHtml){
					$htmlDaPaginaBiblioteca = $this->buscaPaginaBibliotecaSuperLinks($affiliateUrl,$idLinkPg);

					if(!$htmlDaPaginaBiblioteca){
						$htmlDaPaginaBiblioteca = $this->buscaESalvaHtmlProdutor($urlToGetHtml,$idLinkPg);
					}

					$htmlClonePage = $htmlDaPaginaBiblioteca;
				}

				$conteudo = $htmlClonePage;
			}
		}

		if($saveHtmlClone != 'enabled') {
			$conteudo = $this->getHtmlProdutor($urlToGetHtml);
		}

		return $conteudo;
	}

	public function getParametrosUrl($uriAtual){
		$uriAtual = explode('?',$uriAtual);
		$parametrosLinkUri = '';
		if(isset($uriAtual[1])){
			$parametrosLinkUri = $uriAtual[1];
		}

		return $parametrosLinkUri;
	}

	public function ehParaRenovarHtml($updatedAt, $renovaHtmlClone){
		//VERIFICA RENOVAÇÃO DE HTML DE ACORDO COM A PÁGINA DO PRODUTOR
		$renovarHtml = $renovaHtmlClone ? $renovaHtmlClone : 'disabled';

		$horaAgora = date('Y-m-d H:i:s');
		$updatedAt = strtotime($updatedAt);
		$horaAgora = strtotime($horaAgora);

		$ehParaAtualizarHtml = false;

		//1h = 3600
		if($updatedAt) {
			if (($horaAgora - $updatedAt) > 3600) {
				$ehParaAtualizarHtml = true;
			}
		}else{
			$ehParaAtualizarHtml = true;
		}

		if($renovarHtml == 'enabled' && $ehParaAtualizarHtml){
			$renovarHtml = true;
		}else{
			$renovarHtml = false;
		}

		return $renovarHtml;
	}

	public function buscaPaginaBibliotecaSuperLinks($affiliateUrl,$idLinkPg){
		$htmlDaPaginaCorrigida = '';
		$urlGetPaginaCorrigida = "https://wpsuperlinks.top/wp-json/spl-light/v1/getClonePage?urlPaginaClonada=$affiliateUrl&access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC";
		$existePaginaCorrigida = wp_remote_get($urlGetPaginaCorrigida, [
			'timeout' => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		if( !is_wp_error( $existePaginaCorrigida ) ) {
			if (isset($existePaginaCorrigida['body']) && isset($existePaginaCorrigida['body'])) {
				$dataPageJson = $existePaginaCorrigida['body'];
				$dataPageJson = json_decode($dataPageJson);
				if ($dataPageJson && isset($dataPageJson->data) && $dataPageJson->data) {
					$dataBody = $dataPageJson->data;
					$htmlDaPaginaCorrigida = $dataBody->htmlClonePage;
				}
			}
		}

		$newAddLink = new SuperBoostAddLinkModel();
		$newAddLink->loadDataByID($idLinkPg);
		$newAddLink->setIsNewRecord(false);
		$newAddLink->setAttribute('htmlClonePage', $htmlDaPaginaCorrigida);
		$horaUpdate = date('Y-m-d H:i:s');
		$newAddLink->setAttribute('updatedAt', $horaUpdate);
		$newAddLink->save();

		return $htmlDaPaginaCorrigida;
	}

	public function buscaESalvaHtmlProdutor($urlToGetHtml, $idLinkPg){
		$resultClone = wp_remote_get($urlToGetHtml, [
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		$conteudo = '';

		if (is_array($resultClone) && !is_wp_error($resultClone)) {
			$conteudo = $resultClone['body'];
		}
		$urlGetPaginaCorrigida = "https://wpsuperlinks.top/wp-json/spl-light/v1/getClonePage?urlPaginaClonada=$affiliateUrl&access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC";
		$existePaginaCorrigida = wp_remote_get($urlGetPaginaCorrigida, [
			'timeout' => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		if( !is_wp_error( $existePaginaCorrigida ) ) {
			if (isset($existePaginaCorrigida['body']) && isset($existePaginaCorrigida['body'])) {
				$dataPageJson = $existePaginaCorrigida['body'];
				$dataPageJson = json_decode($dataPageJson);
				if ($dataPageJson && isset($dataPageJson->data) && $dataPageJson->data) {
					$dataBody = $dataPageJson->data;
					$htmlDaPaginaCorrigida = $dataBody->htmlClonePage;
					$addLinksModel->setAttribute('compatibilityMode', $dataBody->compatibilidade);
					$addLinksModel->setAttribute('forceCompatibility', $dataBody->forcar);
					$addLinksModel->setAttribute('enableProxy', $dataBody->proxy);
					$addLinksModel->setAttribute('saveHtmlClone', 'enabled');
				}
			}
		}

		return $conteudo;
	}

	public function getHtmlProdutor($urlToGetHtml, $idLinkPg){
		$newAddLink = new SuperBoostAddLinkModel();
		$newAddLink->loadDataByID($idLinkPg);
		$htmlClonePageBD = $newAddLink->getAttribute('htmlClonePage');
		if ($htmlClonePageBD) {
			$newAddLink->setIsNewRecord(false);
			$newAddLink->setAttribute('htmlClonePage', '');
			$newAddLink->save();
		}

		$resultClone = wp_remote_get($urlToGetHtml, [
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		$conteudo = '';

		if (is_array($resultClone) && !is_wp_error($resultClone)) {
			$conteudo = $resultClone['body'];
		}

		return $conteudo;
	}

	public function removeUrlProxySuperLinks($conteudo){
		// v 3.0.18 remover SUPER_BOOST_HELPERS_URL . '/super-links-proxy.php?'
		$linkProxySpl = SUPER_BOOST_HELPERS_URL . '/super-links-proxy.php?';
		$conteudo = str_replace("$linkProxySpl", "", $conteudo);

		$linkProxySpl = SUPER_BOOST_HELPERS_URL . '/super-links-proxy.php';
		$conteudo = str_replace("$linkProxySpl", "", $conteudo);

		return $conteudo;
	}

	public function removeCaracteresHtml($conteudo){

		$conteudo = str_replace('&#038;', "&", $conteudo);
		$conteudo = str_replace('&amp;', "&", $conteudo);

		return $conteudo;
	}

	public function trocaBotoesCheckout($cloneData, $conteudo, $parametrosLinkUri){
		if(!$cloneData){
			return $conteudo;
		}

		foreach ($cloneData as $changeItem) {
			$changeItem = get_object_vars($changeItem);
			$pageItem = $changeItem['pageItem'];
			$newItem = $changeItem['newItem'];

			$pageItem = $this->removeParametroIncorretoUrlTroca($pageItem);

			$newItem = str_replace('&#038;', "&", $newItem);
			$newItem = str_replace('&amp;', "&", $newItem);

			if ($pageItem && $newItem ) {
				$pageItem = trim($pageItem);
				$newItem = trim($newItem);

				if(preg_match("/http/i", $pageItem)) {
					$newItem = $this->insertParamUriClone($parametrosLinkUri,$newItem);
				}

				$conteudo = str_replace($pageItem, $newItem, $conteudo);
			}
		}

		return $conteudo;
	}

	public function removeParametroIncorretoUrlTroca($urlCheckoutPgClonada = ''){

		if(!$urlCheckoutPgClonada){
			return '';
		}

		$url_components = parse_url($urlCheckoutPgClonada);

		$query = isset($url_components['query'])? $url_components['query'] : '';

		if(!$query){
			return $urlCheckoutPgClonada;
		}

		$query = explode('&',$query);
		$tamQuery = count($query) - 1;

		$parametroErrado = isset($query[$tamQuery])? $query[$tamQuery] : '';

		if(!$parametroErrado){
			return $urlCheckoutPgClonada;
		}

		$parametroErrado = explode('=',$parametroErrado);

		$parametrosRemover = [
		    '_hi',
			'_ga',
			'sck'
		];

		if(isset($parametroErrado[0]) && in_array($parametroErrado[0], $parametrosRemover)){
			$montaUrlCorreta = $url_components['scheme'] ."://". $url_components['host'] . $url_components['path'];

			$queryCorreta = isset($query[0])? "?".$query[0] : "";

			for($i=1;$i<$tamQuery;$i++){
				$queryCorreta .= "&".$query[$i];
			}

			$montaUrlCorreta .= $queryCorreta;

			return $montaUrlCorreta;
		}

		return $urlCheckoutPgClonada;
	}

	public function trocaFaviconPaginaClonada($conteudo, $urlNovoFavicon){
		if(!$urlNovoFavicon){
			return $conteudo;
		}

		$nova_tag = '<link rel="icon" href="'.$urlNovoFavicon.'">';

		$padrao = '/<link rel="icon" href="[^"]*"/';
		$conteudo = preg_replace($padrao, $nova_tag, $conteudo);

		$padrao = '/<link rel="shortcut icon" href="[^"]*"/';
		$conteudo = preg_replace($padrao, $nova_tag, $conteudo);

		return $conteudo;
	}

	public function trocaTitlePaginaClonada($conteudo, $novoTitle){
		if(!$novoTitle){
			return $conteudo;
		}

		$padrao = '/<title>[^<]*<\/title>/';
		$nova_tag = '<title>' . $novoTitle . '</title>';
		$conteudo = preg_replace($padrao, $nova_tag, $conteudo);

		return $conteudo;
	}

	public function trocaPropriedadesPaginaClonada($conteudo, $pageTitle, $pageDescription, $pageImage, $url){
		$conteudo = $this->removePropriedadesPaginaClonada($conteudo);

		$header = '
		    <meta http-equiv="cache-control" content="no-store" />
		    <meta http-equiv="cache-control" content="max-age=0" />
		    <meta http-equiv="expires" content="0" />
		    <meta http-equiv="expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
		    <meta http-equiv="pragma" content="no-cache"/>
		    <meta name="robots" content="noindex">
		';

		if($pageTitle){
			$header .= '<meta itemprop="name" content="'.$pageTitle.'">';
			$header .= '<meta property="og:title" content="'.$pageTitle.'"/>';
			$header .= '<meta name="twitter:title" content="'.$pageTitle.'">';
		}

		if($pageDescription){
			$header .= '<meta itemprop="description" content="'.$pageDescription.'">';
			$header .= '<meta name="description" content="'.$pageDescription.'">';
			$header .= '<meta property="og:description" content="'.$pageDescription.'">';
			$header .= '<meta name="twitter:description" content="'.$pageDescription.'">';
		}

		if ($pageImage) {
			list($width, $height, $type, $attr) = getimagesize($pageImage);
			$header .= ' <meta property="og:image" content="'.$pageImage.'">
            <meta property="og:image:width" content="'.$width.'">
            <meta property="og:image:height" content="'.$height.'">';
			$header .= '<meta itemprop="image" content="'.$pageImage.'">';
			$header .= '<meta name="twitter:image" content="'.$pageImage.'">';
		}

		$header .= '<meta property="og:url" content="'.$url.'">
    				<meta property="og:type" content="website">
    				<meta name="twitter:card" content="summary">
    				<meta name="viewport" content="width=device-width, initial-scale=1.0">
        ';

		$conteudo = str_replace('</head>', "\n" . $header . '</head>', $conteudo);

		return $conteudo;
	}

	private function removePropriedadesPaginaClonada($conteudo){
		$padroes = array(
			'/<meta name="robots"[^>]*>/',
			'/<meta itemprop="name"[^>]*>/',
			'/<meta itemprop="description"[^>]*>/',
			'/<meta name="description"[^>]*>/',
			'/<meta itemprop="image"[^>]*>/',
			'/<meta property="og:title"[^>]*>/',
			'/<meta property="og:description"[^>]*>/',
			'/<meta property="og:image"[^>]*>/',
			'/<meta property="og:image:width"[^>]*>/',
			'/<meta property="og:image:height"[^>]*>/',
			'/<meta property="og:url"[^>]*>/',
			'/<meta property="og:type"[^>]*>/',
			'/<meta property="twitter:title"[^>]*>/',
			'/<meta name="twitter:title"[^>]*>/',
			'/<meta name="twitter:description"[^>]*>/',
			'/<meta name="twitter:card"[^>]*>/',
			'/<meta name="twitter:image"[^>]*>/',
			'/<meta name="viewport"[^>]*>/',
		);

		$conteudo = preg_replace($padroes, '', $conteudo);

		return $conteudo;
	}

	public function trocaPixelsPaginaClonada($conteudo, $monitoringModel){
		$conteudo = $this->removePixelsPaginaClonada($conteudo);

		$header = '';

		if(isset($monitoringModel['googleMonitoringID']) && !empty($monitoringModel['googleMonitoringID'])){
			$header .= ' ' .SuperBoostInterceptLinkController::getGoogleAnalyticsCode($monitoringModel['googleMonitoringID']) . ' ';
		}

		if(isset($monitoringModel['trackGoogle']) && !empty($monitoringModel['trackGoogle'])){
			$header .= ' ' . SuperBoostInterceptLinkController::getGoogleEventCode($monitoringModel['trackGoogle']) . ' ';
		}

		if(isset($monitoringModel['pixelID']) && !empty($monitoringModel['pixelID'])){
			$track = (isset($monitoringModel['track']) && !empty($monitoringModel['track']))? $monitoringModel['track'] : 'PageView';
			$header .= ' ' . SuperBoostInterceptLinkController::getPixelFacebookCode($monitoringModel['pixelID'], $track) . ' ';
		}

		if(isset($monitoringModel['codeHeadPage']) && !empty($monitoringModel['codeHeadPage'])){
			$header .= ' ' . $monitoringModel['codeHeadPage'] . ' ';
		}

		//Adiciona meta tag de verificação Facebook
		if(SUPER_BOOST_FACEBOOK_VERIFICATION) {
			$facebookTagVerification = '<meta name="facebook-domain-verification" content="'.get_option('facebookVerificationSPL').'" />';
			$header .= ' ' . $facebookTagVerification . ' ';
		}

		$conteudo = str_replace('</head>', "\n" . $header . '</head>', $conteudo);

		$appendBody = '';
		if(isset($monitoringModel['codeBodyPage']) && !empty($monitoringModel['codeBodyPage'])){
			$appendBody .= $monitoringModel['codeBodyPage'];
		}

		$conteudo = str_replace('</body>',"\n" . $appendBody . ' </body>',$conteudo);

		return $conteudo;
	}

	//@todo está removendo gtm corretamente, falta finalizar a procura pelos pixels facebook
	private function removePixelsPaginaClonada($conteudo){
		$padroes = array(
			'/<script[^>]*>.*googletagmanager\.com.*<\/script>/i',
			'/<script[^>]*>.*facebook\.com\/tr.*<\/script>/i',
			'/<script[^>]*>.*analytics\.google\.com.*<\/script>/i',
			'/<img[^>]* src="https?:\/\/.*\.doubleclick\.net\/.*>/i',
			'/<img[^>]* src="https?:\/\/.*\.google-analytics\.com\/.*>/i',
			'/<meta name="facebook-domain-verification"[^>]*>/i',
			'/<noscript><img.*><\/noscript>/i',
		);

		$conteudo = preg_replace($padroes, '', $conteudo);

		$conteudo = preg_replace('/<script async src="https:\/\/www\.googletagmanager\.com\/gtag\/js\?id=.*"><\/script>/iU', '', $conteudo);
		$conteudo = preg_replace('/<script\b[^>]*>(?=.*gtag).*?<\/script>/is', '', $conteudo);
		$conteudo = preg_replace('/<script[^>]*id="hotmart_launcher_script"[^>]*>.*?<\/script>/is', '', $conteudo);


//		$conteudo = preg_replace('/<script>.*fbq\(\'init\', \'.*\'\);\s*fbq\(\'track\', \'.*\'\);<\/script>/i', '', $conteudo);


		return $conteudo;
	}

	public function adicionaFontAwesome($conteudo){

		$conteudo = str_replace('</head>', ' <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"></head>', $conteudo);
		$conteudo = str_replace('</head>', "\n<style>.fa::before, .far::before, .fas::before{font-family: 'Font Awesome 5 Free', FontAwesome !important;}</style></head>", $conteudo);

		// substitui icones
		$conteudo = str_replace('"eicon-', '"fas fa-', $conteudo);

		return $conteudo;
	}

	public function adicionaPluginsPaginaClonada($conteudo, $pageData){
		$counterSuperEscassez = isset($pageData['counterSuperEscassez']) ? $pageData['counterSuperEscassez'] : false;
		$alertaConversoes = isset($pageData['alertaConversoes']) ? $pageData['alertaConversoes'] : false;

		if($counterSuperEscassez && function_exists('getCountersIdForSuperLinks')) {

			$valueCounterSuperEscassez = getCounterForSuperLinks($counterSuperEscassez);

			$contentCounterSuperLinks = '<div style="display:inline-block; text-align: center !important;  margin:20px;">';
			$contentCounterSuperLinks .= getCounterForSuperLinks($counterSuperEscassez);
			$contentCounterSuperLinks .= '</div>';

			$conteudo = str_replace('</body>', "\n" . $contentCounterSuperLinks . ' </body>', $conteudo);
		}

		if($alertaConversoes && function_exists('getAlertsIdForSuperLinks')) {
//			$alertaJsUrl = isset(WP_ALERTA_CONVERSOES_JS_URL)? WP_ALERTA_CONVERSOES_JS_URL : '';
//			$alertaImageUrl = isset(WP_ALERTA_CONVERSOES_IMAGES_URL)? WP_ALERTA_CONVERSOES_IMAGES_URL : '';
//
//			$jsUrlAlert =  $alertaJsUrl . '/wpNotificationAlertConvertSPL.js';
//			$getSiteUrl = SUPER_BOOST_TEMPLATE_URL;
//			$contentAlertaSuperLinks = '<script>
//                                    let siteurl = "'.$getSiteUrl.'";
//                                    let idCampaign = "'.$alertaConversoes.'";
//                                    let imagesAlertConvert = "'.$alertaImageUrl.'";
//                                </script>';
//			$contentAlertaSuperLinks .= '<script type="text/javascript" src="'.$jsUrlAlert.'" id="spl_alerts_js"></script>';
//
//			$conteudo = str_replace('</body>', "\n" . $contentAlertaSuperLinks . ' </body>', $conteudo);
		}

		return $conteudo;
	}

	public function adicionaFuncoesRodape($conteudo, $monitoringModel, $urlRedirectBtn){

		$appendFooter = '';
		if(isset($monitoringModel['codeFooterPage']) && !empty($monitoringModel['codeFooterPage'])){
			$appendFooter .= $monitoringModel['codeFooterPage'];
		}

		if($urlRedirectBtn) {
			$appendFooter .= '
                    <script>
                        document.documentElement.addEventListener("mouseleave", function(e){
                            if (e.clientY > 20) { return; }
                            document.location="' . $urlRedirectBtn . '"
                        })
                    </script>				
                ';
					$appendFooter .= '<script>
			        history.pushState({}, "", location.href)
			        history.pushState({}, "", location.href)
			        window.addEventListener("popstate", function(event) {
			            setTimeout(function () {
			                location.href = "'.$urlRedirectBtn.'"
			            }, 1)
			        })
			    </script>';
		}

		$cookiesLinks = new SuperLinksCookieLinkController('SuperLinksLinkCookiePageModel');
		$appendFooter .= ' ' .$cookiesLinks->execCookieSuperLinksCloneCamu($urlRedirectBtn);

		$conteudo = str_replace('</body>',"\n" . $appendFooter . ' </body>',$conteudo);

		return $conteudo;
	}

	public function adicionaWhatsapp($conteudo, $numberWhatsapp, $textWhatsapp){
		$conteudo = $this->removeBotoesWhatsappEJivoPaginaClonada($conteudo);

		$numberWhatsapp = $numberWhatsapp? $numberWhatsapp : false;
		$textWhatsapp = $textWhatsapp? '?text='.$textWhatsapp : '';
		$appendFooter = '';

		if($numberWhatsapp) {
			$appendFooter .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <a href="https://wa.me/'.$numberWhatsapp.$textWhatsapp.'" style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 1px 1px 2px #888;
          z-index:10000;" target="_blank">
        <i style="margin-top:16px" class="fa fa-whatsapp"></i>
        </a>
        <script>
            window.addEventListener("load", function(event) {
                document.getElementById("whatsclub-widget").style.display = "none"
            })
        </script>
    ';
		}

		$conteudo = str_replace('</body>',"\n" . $appendFooter . ' </body>',$conteudo);

		return $conteudo;
	}

	private function removeBotoesWhatsappEJivoPaginaClonada($conteudo){
		$padroes = array(
			'/<div[^>]*class="whatsapp"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="whatswidget"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="wp-float"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="whatsappbutton"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="whatsappme"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="whatsappshare"[^>]*>[^<]*<\/div>/i',
			'/<div[^>]*class="jv-chat-widget"[^>]*>[^<]*<\/div>/i'
		); // padrões de busca para os botões de WhatsApp flutuantes a serem removidos

		$conteudo = preg_replace($padroes, '', $conteudo);

		return $conteudo;
	}

	public function adicionaPopUp($conteudo, $pageData){
		//popups
		$idPopupDesktop = (isset($pageData['idPopupDesktop']) && $pageData['idPopupDesktop'])? $pageData['idPopupDesktop'] : null;
		$idPopupMobile = (isset($pageData['idPopupMobile']) && $pageData['idPopupMobile'])? $pageData['idPopupMobile'] : null;

		$loadPopupAfterSeconds = (isset($pageData['loadPopupAfterSeconds']) && $pageData['loadPopupAfterSeconds'])? $pageData['loadPopupAfterSeconds'] : 0;

		if($idPopupDesktop || $idPopupMobile){
			$popup_content = "<link rel='stylesheet' id='spl_popup_css'  href='".SUPER_BOOST_CSS_URL."/splPop.min.css?ver=".SUPER_BOOST_VERSION."' type='text/css' media='all' />";
			$popup_content .= "<script>
                            let splPop = {
                                mobile: '".$idPopupMobile."',
                                desktop: '".$idPopupDesktop."',
                                loadPopupAfterSeconds: '".$loadPopupAfterSeconds."',
                                exitIntentPopup: '".$pageData['exitIntentPopup']."'
                            }
                        </script>";
			$popup_content .= "<script type='text/javascript' src='".SUPER_BOOST_JS_URL."/splPop.min.js?ver=".SUPER_BOOST_VERSION."' id='spl_popup_js'></script>";


			if($idPopupDesktop){
				$popup_content .= get_post_meta($idPopupDesktop, '_superlinks_popup', true);
			}

			if($idPopupMobile){
				$popup_content .= get_post_meta($idPopupMobile, '_superlinks_popup', true);
			}

			$conteudo = str_replace('</body>',"\n" . $popup_content . ' </body>',$conteudo);
		}

		return $conteudo;
	}

	public function normalizeLinksPaginaClonada($doc, $xpath, $enableProxy, $affiliateUrl, $forceCompatibility){
		if($enableProxy) {

			$linksA = $doc->getElementsByTagName('a');

			foreach ($linksA as $linkA) {

				$old_link = $linkA->getAttribute('href');

				$urlLink = parse_url($old_link);
				$schemeUrl = isset($urlLink['scheme']) ? $urlLink['scheme'] : '';
				$hostUrl = isset($urlLink['host']) ? $urlLink['host'] : '';

				if ($schemeUrl == 'http' || $schemeUrl == 'https') {
					$linkExplode = explode('?', $old_link);
					$replaceLink = isset($linkExplode[0]) ? $linkExplode[0] . '?' : '';
					$old_link = str_replace($replaceLink, '', $old_link);

					$linkA->setAttribute('href', $old_link);

				}

			}

			$links_frame = $doc->getElementsByTagName('iframe');

			foreach ($links_frame as $linkF) {

				$old_link = $linkF->getAttribute('src');

				$urlLink = parse_url($old_link);
				$schemeUrl = isset($urlLink['scheme']) ? $urlLink['scheme'] : '';
				$hostUrl = isset($urlLink['host']) ? $urlLink['host'] : '';

				if ($schemeUrl == 'http' || $schemeUrl == 'https') {
					$linkExplode = explode('?', $old_link);
					$replaceLink = isset($linkExplode[0]) ? $linkExplode[0] . '?' : '';
					$old_link = str_replace($replaceLink, '', $old_link);
					$linkF->setAttribute('src', $old_link);

				}

			}
		}

		if(!$enableProxy) {

			if($forceCompatibility == 'enabled') {
				foreach ($xpath->query("//meta[@http-equiv]") as $element) {
					if (strcasecmp($element->getAttribute("http-equiv"), "refresh") === 0) {
						$content = $element->getAttribute("content");
						if (!empty($content)) {
							$splitContent = preg_split("/=/", $content);
							if (isset($splitContent[1])) {
								$element->setAttribute("content", $splitContent[0] . "=" . $this->makeHttpToUrlClone($splitContent[1], $affiliateUrl));
							}
						}
					}
				}

				foreach ($xpath->query("//style") as $style) {
					$style->nodeValue = $this->proxifyCSSClonePage($style->nodeValue, $affiliateUrl);
				}

				foreach ($xpath->query("//*[@style]") as $element) {
					$element->setAttribute("style", $this->proxifyCSSClonePage($element->getAttribute("style"), $affiliateUrl));
				}

				$proxifyAttributes = ["href", "src", "data-src"];
				foreach ($proxifyAttributes as $attrName) {
					foreach ($xpath->query("//*[@" . $attrName . "]") as $element) { //For every element with the given attribute...
						$attrContent = $element->getAttribute($attrName);
						if ($attrName == "href" && preg_match("/^(about|javascript|magnet|mailto):|#/i", $attrContent)) continue;
						if ($attrName == "src" && preg_match("/^(data):/i", $attrContent)) continue;
						$attrContent = $this->makeHttpToUrlClone($attrContent, $affiliateUrl);
						$element->setAttribute($attrName, $attrContent);
					}
				}

				$proxifyAttributes = ["srcset"];
				foreach ($proxifyAttributes as $attrName) {
					foreach ($xpath->query("//*[@" . $attrName . "]") as $element) { //For every element with the given attribute...
						$element->setAttribute($attrName, '');
					}
				}
			}
		}
	}

	private function proxifyCSSClonePage($css, $baseURL) {
		$sourceLines = explode("\n", $css);
		$normalizedLines = [];
		foreach ($sourceLines as $line) {
			if (preg_match("/@import\s+url/i", $line)) {
				$normalizedLines[] = $line;
			} else {
				$normalizedLines[] = preg_replace_callback(
					"/(@import\s+)([^;\s]+)([\s;])/i",
					function($matches) use ($baseURL) {
						return $matches[1] . "url(" . $matches[2] . ")" . $matches[3];
					},
					$line);
			}
		}
		$normalizedCSS = implode("\n", $normalizedLines);
		return preg_replace_callback(
			"/url\((.*?)\)/i",
			function($matches) use ($baseURL) {
				$url = $matches[1];
				if (strpos($url, "'") === 0) {
					$url = trim($url, "'");
				}
				if (strpos($url, "\"") === 0) {
					$url = trim($url, "\"");
				}
				if (stripos($url, "data:") === 0) return "url(" . $url . ")";
				return "url(" . $this->makeHttpToUrlClone($url, $baseURL) . ")";
			},
			$normalizedCSS);
	}

	private function makeHttpToUrlClone($url = '', $urlAffiliate = ''){
		if(isValidUrlSuperBoost($url)){
			return $url;
		}

		$urlRetornada = $url;

		$url = str_replace('./', '', $url);
		$url = str_replace('../', '', $url);

		if($url && $urlAffiliate){

			$splitUrl = explode('/',$urlAffiliate);
			$tam = count($splitUrl) - 1;
			if(!$splitUrl[$tam]){
				unset($splitUrl[$tam]);
			}
			$splitUrl = implode('/',$splitUrl);
			$urlAffiliate = $splitUrl . "/";

			if(parse_url($url, PHP_URL_SCHEME) != "http" && parse_url($url, PHP_URL_SCHEME) != "https" ){
				$urlRetornada = $urlAffiliate . $url;
				if(isValidUrlSuperBoost($urlRetornada)){
					return $urlRetornada;
				}
			}
		}

		return $urlRetornada;
	}

	public function trocaCheckoutDoc($doc, $cloneData, $parametrosLinkUri){
		$linksCheckoutChange = $doc->getElementsByTagName('a');

		foreach ($linksCheckoutChange as $linkChange) {

			$old_link = $linkChange->getAttribute('href');

			foreach ($cloneData as $changeItem) {
				$changeItem = get_object_vars($changeItem);
				$pageItem = $changeItem['pageItem'];
				$newItem = $changeItem['newItem'];

				$pageItem = $this->removeParametroIncorretoUrlTroca($pageItem);

				$newItem = str_replace('&#038;', "&", $newItem);
				$newItem = str_replace('&amp;', "&", $newItem);

				if ($pageItem && $newItem ) {
					$pageItem = trim($pageItem);
					$newItem = trim($newItem);

					if(preg_match("/http/i", $pageItem)) {
						$newItem = $this->insertParamUriClone($parametrosLinkUri,$newItem);
					}

					if($old_link == $pageItem){
						$linkChange->setAttribute('href', $newItem);
					}
				}
			}

		}
	}

	public function substituiImagens($doc, $cloneData){
		$linksCheckoutChange = $doc->getElementsByTagName('img');

		foreach ($linksCheckoutChange as $linkChange) {

			$old_link = $linkChange->getAttribute('src');

			foreach ($cloneData as $changeItem) {
				$changeItem = get_object_vars($changeItem);
				$pageItem = $changeItem['pageItem'];
				$newItem = $changeItem['newItem'];

				if ($pageItem && $newItem ) {
					$pageItem = trim($pageItem);
					$newItem = trim($newItem);

					if($old_link == $pageItem){
						$linkChange->setAttribute('src', $newItem);
					}
				}
			}

		}
	}

	public function insertParamUriClone($params,$link){

		if(!$params){
			return $link;
		}

		$splitLink = explode('?',$link);

		$inicioLink = '?';
		if(isset($splitLink[1])){
			$inicioLink = '&';
		}

		return $link.$inicioLink.$params;
	}

	public function adicionaRGPD($pageData){
		$rgpd = isset($pageData['rgpd']) ? $pageData['rgpd'] : false;

		if($rgpd && function_exists('active_rgpd_box')){
			require_once("executaRGPD.php");
			rgpdSuperLinks();
		}
	}


	public function isPageSuperLinks($slugAtual = ''){
		if(empty($slugAtual)){
			return false;
		}

		$superLinksModel = new SuperBoostModel();

		if(!$superLinksModel->isPluginActive()){
			return false;
		}

		$addLinkModel = new SuperLinksAddLinkModel();
		$keywordSuperLinks = strtolower($slugAtual);
		$superlink = $addLinkModel->getLinkByKeyword($keywordSuperLinks);

		if(!$superlink) {
			$superlink = $addLinkModel->getLinkByKeyword( $keywordSuperLinks . "/" );
		}

		if (!$superlink) {
			return false;
		}

		if($superlink) {
			$link = array_shift($superlink);
			return $link;
		}

		return false;
	}
}