<?php
function rgpd_all_pages($rgpdtype)
{
	$pref='WP-RGPD-Compliance-';
// Set up the objects needed
$my_wp_query = new WP_Query();
$all_wp_pages = $my_wp_query->query(array('post_type' =>get_post_types('', 'names')));
// Filter through all pages and find Portfolio's children
$all_children = get_page_children( get_the_ID(), $all_wp_pages );
$all_children=get_pages();

//var_dump($all_children);

// echo what we get back from WP to the browser
$count=0;
if(!empty($all_children)){
	if($rgpdtype !='cookie')
	{
		$class="form-control";
	}
	else
	{
		$class="";
	}
	if($rgpdtype =='cookie')
	{
		$class="selectcontrol";
	}
	
	echo '<select  class="'.$class.'" name="'.$rgpdtype.'">';
	
		if($rgpdtype=='cookie')
		{//for type cookie
	         if(get_option($pref.'show')=='y')
			 {
			 echo '<option Value="y" selected>'.__('All Pages','rgpdpro').'</option>';
			 echo '<option Value="n">'.__('Don\'t Display','rgpdpro').'</option>';
			 echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'show')=='n')
			 {
				echo '<option Value="y">'.__('All Pages','rgpdpro').'</option>';
			 echo '<option Value="n" selected>'.__('Don\'t Display','rgpdpro').'</option>'; 
			 echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'show')=='h')
			 {
				echo '<option Value="y">'.__('All Pages','rgpdpro').'</option>';
			 echo '<option Value="n" selected>'.__('Don\'t Display','rgpdpro').'</option>'; 
			 echo '<option Value="h" selected>'.__('Home','rgpdpro').'</option>';
			 }
			 else
			 {
				 echo '<option Value="y">'.__('All Pages','rgpdpro').'</option>';
			 echo '<option Value="n">'.__('Don\'t Display','rgpdpro').'</option>';
			 echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
			 }
		}
		if($rgpdtype=='afttandc')
		{
			//for terms and conditions after accept page redirect
			if(get_option($pref.'tandc-aft')=='l')
			 {
				 echo '<option Value="h" >'.__('Home','rgpdpro').'</option>';
			 echo '<option Value="l" selected>'.__('Last Visited Page','rgpdpro').'</option>';
			echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'tandc-aft')=='n')
			 {
				  echo '<option Value="h" >'.__('Home','rgpdpro').'</option>';
				  echo '<option Value="l">'.__('Last Visited Page','rgpdpro').'</option>';
				  echo '<option Value="n" selected>'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'tandc-aft')=='h')
			 {     echo '<option Value="h" selected>'.__('Home','rgpdpro').'</option>';
				  echo '<option Value="l">'.__('Last Visited Page','rgpdpro').'</option>';
				  echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else
			 {
				  echo '<option Value="h" >'.__('Home','rgpdpro').'</option>';
				 echo '<option Value="l">'.__('Last Visited Page','rgpdpro').'</option>';
				  echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
		}
		if($rgpdtype=='beftandc')
		{
			//for terms and conditions page
			if(get_option($pref.'tandc-bef')=='0')
			 {
			 echo '<option Value="0" selected></option>';
			
			 }
			 else
			 {
				echo '<option Value="0"></option>'; 
			 }
		}
		
		if($rgpdtype=='aftpp')
		{
			//for privacy policy after accept page redirect
			if(get_option($pref.'pp-aft')=='l')
			 {
				 echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
			 echo '<option Value="l" selected>'.__('Last Visited Page','rgpdpro').'</option>';
			echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'pp-aft')=='n')
			 {     echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
				  echo '<option Value="l">'.__('Last Visited Page','rgpdpro').'</option>';
				  echo '<option Value="n" selected>'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else if(get_option($pref.'pp-aft')=='h')
			 {     echo '<option Value="h" selected>'.__('Home','rgpdpro').'</option>';
				  echo '<option Value="l">'.__('Last Visited Page','rgpdpro').'</option>';
				  echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
			 else
			 {
				 echo '<option Value="h">'.__('Home','rgpdpro').'</option>';
				 echo '<option Value="l">'.__('Last Visited page','rgpdpro').'</option>';
				  echo '<option Value="n">'.__('Don\'t Redirect','rgpdpro').'</option>';
			 }
		}
		if($rgpdtype=='befpp')
		{
			//for privacy policy  page
			if(get_option($pref.'pp-bef')=='0')
			 {
			 echo '<option Value="0" selected></option>';
			
			 }
			 else
			 {
				echo '<option Value="0"></option>'; 
			 }
		}
		
    foreach($all_children as $child){
		$count++;
		if(strlen($child->post_title)<1)
		{
			$title="No Title";
		}
		else
		{
			$title=$child->post_title;
		}
		$selected="";
		if($rgpdtype=='cookie')
			{//option selection for cookie
		if(get_option($pref.'show')==$child->ID)
		{
			$selected="selected";
		}
		
			}
		if($rgpdtype=='beftandc')
			{//option selection for page tandc
		if(get_option($pref.'tandc-bef')==$child->ID)
		{
			$selected="selected";
		}
			}
        if($rgpdtype=='afttandc')
			{//option selection for page redicet after accepttandc
		if(get_option($pref.'tandc-aft')==$child->ID)
		{
			$selected="selected";
		}
			}

         if($rgpdtype=='befpp')
			{//option selection for page privacy policy
		if(get_option($pref.'pp-bef')==$child->ID)
		{
			$selected="selected";
		}
			}
        if($rgpdtype=='aftpp')
			{//option selection for page redicet after accept privacy policy
		if(get_option($pref.'pp-aft')==$child->ID)
		{
			$selected="selected";
		}
			}			
		
         echo '<option Value="'.$child->ID.'" '.$selected.'>'.$title.'</option>';
		
    }
    echo '</select>';
}
}

function  rmhttpurlandmatch($url)
{//cookie match url and show alert
if(get_the_ID()==$url)
{
	return 1;
}
else
{
	return 0;
}
/*$hurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$surl='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(strpos($hurl,$url)!==false)
{
	return 1;
}
else if(strpos($surl,$url)!==false)
{
	return 1 ;
}
*/
}

//---- Página de termos e condições
function pag_terms()
{
	global $wpdb;

	$site = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	$empresa = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'" );
	$admin_email = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'admin_email'" );
	
	
	// <?php echo $admin_email;
	
	//INICIO DOS TERMOS E CONDIÇÕES
?>	
	
	<h1><?php echo $empresa;?> TERMOS E CONDIÇÕES</h1>
	<p>ULTIMA ACTUALIZAÇÃO: 25 DE MAIO DE 2018</p>
	<p>Os termos e condições (“Termos”) descrevem como <?php echo $empresa;?> (“Empresa,” “nós,” e “nosso”) regula o uso deste website <?php echo $site;?> (o “website”).</br>Por favor leia as informações a seguir com cuidado de forma a entender as nossas praticas referentes ao uso do website. A Empresa poderá alterar os Termos a qualquer altura. A Empresa poderá informa-lo da alteração dos Termos utilizando os meios de comunicação disponíveis. A Empresa recomenda que verifique o website com frequência de forma a que veja a versão actual dos Termos e as versões anteriores.</p>
	
	
	<ol>
	
	<li>POLÍTICAS DE PRIVACIDADE</br> 
	A nossa política de privacidade encontra-se disponível em outra página. A nossa política de privacidade explica-lhe como nós utilizamos os seus dados pessoais. Ao utilizar o nosso website você reconhece que tem conhecimento e aceitas as nossas políticas de privacidade e da forma como processamos os seus dados. </li>
	</br><li>A SUA CONTA</br> 
	Quando usa o nosso website, você fica responsável por assegurar a confidencialidade da sua conta, senha e outros dados. Não poderá passar a sua conta a terceiros. Nós não nos responsabilizamos por acessos não autorizados que resultem de negligencia por parte do utilizador (dono da conta). A empresa está no direito de terminar o serviço, ou cancelar a sua conta e remover os seus dados, caso você partilhe a sua conta. </li>
	</br><li>SERVIÇOS</br> 
	O website permite que você use os serviços disponiveis no website. Não poderá utilizar esses serviços com propósitos ilegais. 
	Nós poderemos em alguns casos, estipular um valor para poder utilizar o website. Todos os preços serão publicados separadamente nas páginas apropriadas no website. Poderemos em alguns casos, e a qualquer momento mudar os valores para poder aceder. 
	Poderemos também utilizar sistemas de processamento de pagamentos que terão taxas de processamento de pagamentos. Algumas dessas taxas poderão ser apresentadas quando você escolher um determinado meio de pagamento. Todos os detalhes sobre as taxas desses sistema de pagamentos poderão ser encontrados no seus respectivos</li> websites.</br> 
	</br><li>SERVIÇOS DE TERCEIROS</br> 
	O website poderá incluir links para outros websites, aplicações ou plataformas. 
	Nós não controlamos os websites de terceiros, e não seremos responsáveis pelos conteúdos e outro tipo de materiais incluídos nesses websites. Nós deixamos esses disponíveis para você e mantemos todos os nossos serviços e funcionalidades no nosso website. </li>
	</br><li>USOS PROIBIDOS E PROPRIEDADE INTELECTUAL</br> 
	Nós concedemos a você uma licença revogável, intransferível e não exclusiva para aceder e usar o nosso website de um dispositivo de acordo com os Termos. 
	Você não deve usar o website para fins ilegais, ou proibidos. Você não pode usar o website de forma a que possa desabilitar, danificar ou interferir no website.</br> 
	Todo o conteúdo presente no nosso website incluindo texto, código, gráficos, logos, imagens, vídeos, software utilizados no website (doravante e aqui anteriormente o "Conteúdo"). O conteúdo é propriedade da empresa, ou dos seus contratados e protegidos por lei (propriedade intelectual) que protegem esses direitos. 
	Você não pode publicar, partilhar, modificar, fazer engenharia reversa, participar da transferência ou criar e vender trabalhos derivados, ou de qualquer forma usar qualquer um dos Conteúdos. </br>A sua utilização do website não lhe dá o direito de fazer qualquer uso ilegal e não permitido do Conteúdo e, em particular, você não poderá alterar os direitos de propriedade ou avisos no Conteúdo. Você deverá usar o Conteúdo apenas para seu uso pessoal e não comercial. A Empresa não concede a você nenhuma licença para propriedade intelectual dos seus conteúdos. </li>
	</br><li>MATERIAIS DA EMPRESA</br> 
	Ao publicar, enviar, submeter, ou efectuar upload do seu Conteúdo, você está a ceder os direitos do uso desse Conteúdo a nós para o desenvolvimento do nosso negócio, incluindo, mas não limitado a, os direitos de transmissão, exibição pública, distribuição, execução pública, cópia, reprodução e tradução do seu Conteúdo; e publicação do seu nome em conexão com o seu Conteúdo.</br> 
	Nenhuma compensação será paga com relação ao uso do seu Conteúdo. A Empresa não terá obrigação de publicar ou desfrutar de qualquer Conteúdo que você possa nos enviar e poderá remover seu Conteúdo a qualquer momento sem qualquer aviso. 
	Ao publicar, fazer upload, inserir, fornecer ou enviar o seu Conteúdo, você garante e declara que possui todos os direitos sobre seu Conteúdo. </li>
	</br><li>ISENÇÃO DE CERTAS RESPONSABILIDADES</br> 
	As informações disponíveis através do website podem incluir erros tipográficos ou imprecisões. A Empresa não será responsável por essas imprecisões e erros. 
	A Empresa não faz declarações sobre a disponibilidade, precisão, confiabilidade, adequação e atualidade do Conteúdo contido e dos serviços disponíveis no website. Na medida máxima permitida pela lei aplicável, todos os Conteúdos e serviços são fornecidos "no estado em que se encontram". A Empresa se isenta de todas as garantias e condições relativas a este Conteúdo e serviços, incluindo garantias e provisões de comercialização, adequação a um determinado propósito. </li>
	</br><li>INDEMNIZAÇÃO</br> 
	Você concorda em indemnizar, defender e isentar a Companhia, seus gerentes, diretores, funcionários, agentes e terceiros, por quaisquer custos, perdas, despesas (incluindo honorários de advogados), responsabilidades relativas, ou decorrentes de sua fruição ou incapacidade para aproveitar o website, ou os seus serviços e produtos da Empresa, a sua violação dos Termos, ou a sua violação de quaisquer direitos de terceiros, ou a sua violação da lei aplicável. Você deve cooperar com a Empresa na afirmação de quaisquer defesas disponíveis. </li>
	</br><li>CANCELAMENTO E RESTRIÇÃO DE ACESSO</br> 
	A Empresa pode cancelar ou bloquear o seu acesso ou conta no website e os seus respectivos serviços, a qualquer altura, sem aviso, no caso de você violar os Termos e condições. </li>
	</br><li>DIVERSOS</br> 
	A lei que rege os Termos deve ser as leis substantivas do país onde a Empresa está estabelecida, exceto as regras de conflito de leis. Você não deve usar o Website em jurisdições que não dêem efeito a todas as disposições dos Termos.</br> 
	Nenhuma parceria, emprego ou relacionamento de agência estará implícito entre você e a Empresa como resultado dos Termos ou uso do Website. 
	Nada nos Termos deverá ser uma derrogação ao direito da Empresa de cumprir com solicitações ou requisitos governamentais, judiciais, policiais e policiais ou requisitos relacionados ao seu usufruto do Website.</br> 
	Se qualquer parte dos Termos for considerada inválida ou inexequível de acordo com a lei aplicável, as cláusulas inválidas ou inexequíveis serão consideradas substituídas por cláusulas válidas e exequíveis que deverão ser semelhantes à versão original dos Termos e outras partes e seções do Contrato. Termos serão aplicáveis a você e à Empresa.</br> 
	Os Termos constituem o acordo integral entre você e a Empresa em relação ao desfrute do Website e os Termos substituem todos os anteriores ou comunicações e ofertas, sejam eletrônicas, orais ou escritas, entre você e a Empresa.</br> 
	A Empresa e suas afiliadas não serão responsáveis por uma falha ou atraso no cumprimento de suas obrigações quando a falha ou atraso resultar de qualquer causa além do controle razoável da Empresa, incluindo falhas técnicas, desastres naturais, bloqueios, embargos, revoltas, atos, regulamentos, legislação. ou ordens de governo, atos terroristas, guerra ou qualquer outra força fora do controle da Empresa.</br> 
	Em caso de controvérsias, demandas, reclamações, disputas ou causas de ação entre a Empresa e você em relação ao Website ou outros assuntos relacionados, ou aos Termos, você e a Empresa concordam em tentar resolver tais controvérsias, demandas, reclamações, disputas , ou causas de ação por negociação de boa-fé, e em caso de falha de tal negociação, exclusivamente através dos tribunais do país onde a Companhia está estabelecida. </li>
	</br><li>RECLAMAÇÕES</br> 
	Estamos empenhados em resolver quaisquer reclamações sobre a forma como recolhemos ou usamos os seus dados pessoais. Se você gostaria de fazer uma reclamação sobre estes Termos ou nossas práticas em relação aos seus dados pessoais, entre em contato conosco em: <a href="mailto:<?php echo $admin_email;?>"><?php echo $admin_email;?> </a>. </br>Responderemos à sua reclamação assim que pudermos e, em qualquer caso, dentro de 30 dias. </br>Esperamos resolver qualquer reclamação que seja levada ao nosso conhecimento, no entanto, se você achar que a sua reclamação não foi adequadamente resolvida, você se reserva no direito de entrar em contato com a autoridade supervisora de proteção de dados local. </li>
	</br><li>INFORMAÇÃO DE CONTACTO</br> 
	Agradecemos os seus comentários ou perguntas sobre estes Termos. Você pode nos contactar por escrito em <a href="mailto:<?php echo $admin_email;?>"><?php echo $admin_email;?> </a>. </li>
	
</ol>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
<?php	
	//FIM DOS TERMOS E CONDIÇÕES
	
	
	
}


//-----For Terms and Conditions

function tandcacceptbutton()
{//terms and condition shortcode accept button and add user meta or cookie for it
$pref='WP-RGPD-Compliance-';
	if(isset($_POST['rgpdaccept']))
	{
		wprgpd_store_consent_for_tandc_and_pp('tandc');
		
		if(is_user_logged_in())
		{//if user logged in add or update user meta
          $user =get_current_user_id();
		  
          $havemeta = get_user_meta($user,$pref.'tandc', false);
		  if( $havemeta)
		  {
			 if(get_user_meta($user,$pref.'tandc', true)!=get_option($pref.'tandc-version'))
			 {
				 update_user_meta($user,$pref.'tandc',get_option($pref.'tandc-version'));
			 }		 
		  }
		  else
		  {
			  add_user_meta($user,$pref.'tandc',get_option($pref.'tandc-version'));}
        }
		
									 
		if(get_option($pref.'tandc-aft')=='l')
		{
			
			$link=$_SESSION['tandclvpage'];
			echo "<script>window.location='".$link."';</script>";
		}
		else if(get_option($pref.'tandc-aft')=='h')
		{
			
			$link=$_SESSION['tandclvpage'];
			echo "<script>window.location='".get_home_url()."';</script>";
		}
		else if(get_option($pref.'tandc-aft')=='n')
		{
			
		}
		else
		{ 
			$link=get_permalink(get_option($pref.'tandc-aft'));
			echo "<script>window.location='".$link."';</script>";
		}
	}
	$nlg="";
	if(!is_user_logged_in())
	{
		$nlg="<input type='hidden' value='1' name='rgpdnlgtandc'>";
	}
	$form="<p><form action='' method='post'>".$nlg."
	<label class='containerr'>
	".__('I accept the terms and conditions as laid out in the Terms & Conditions.','rgpdpro')."
	<input type='checkbox' required=''>
	<span class='checkmark'></span>
	</label>
	<input type='submit' value='".__('Accept It','rgpdpro')."' name='rgpdaccept' class='rgpdacceptbutton'></form></p>";
	return $form;
}
function rgpdsettandccookie()
{//adding a cookie for terms and conditions
	$pref='WP-RGPD-Compliance-';
	$tandcookie=$pref.'tandc';
	if(isset($_COOKIE[$tandcookie]))
	{
	if($_COOKIE[$tandcookie]!=get_option($pref.'tandc-version'))
	setcookie($tandcookie,get_option($pref.'tandc-version'),time()+(480000*365),COOKIEPATH, COOKIE_DOMAIN);
    }
	else
	{setcookie($tandcookie,get_option($pref.'tandc-version'),time()+(480000*365),COOKIEPATH, COOKIE_DOMAIN);}
	
}
function rgpd_check_tandc_cookie_or_usermeta()
{//check cookie or user meta set or not
$pref='WP-RGPD-Compliance-';

$https = ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off')) ? true : false;

if(rmhttpurlandmatch(get_option($pref.'tandc-bef'))==1 || rmhttpurlandmatch(get_option($pref.'pp-bef'))==1)
{}
else{
if($https) {
    $_SESSION['tandclvpage']= "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
} else {
   $_SESSION['tandclvpage']= "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
    }
	$tandcookie=$pref.'tandc';
	$link=get_permalink(get_option($pref.'tandc-bef'));
	
if(is_user_logged_in()&&(get_option($pref.'tandc-bef')!='0'))
{
	$user =get_current_user_id();
	$havemeta = get_user_meta($user,$pref.'tandc', false);
	if(get_option($pref.'tandc-lusr')=='1'){
		    if($havemeta){
				if(get_user_meta($user,$pref.'tandc', true)!=get_option($pref.'tandc-version'))
			echo "<script>window.location='".$link."';</script>";
			             }
             else
			 {
				echo "<script>window.location='".$link."';</script>"; 
			 }				 
			                           }
}
else if(get_option($pref.'tandc-bef')!='0' && get_option($pref.'tandc-nlusr')=='1')
{
	if(get_option($pref.'tandc-nlusr')=='1')
	{
		if(isset($_COOKIE[$tandcookie]))
		{
			if($_COOKIE[$tandcookie]!=get_option($pref.'tandc-version'))
			echo "<script>window.location='".$link."';</script>";
		}
		else
		{
			echo "<script>window.location='".$link."';</script>";
		}
	}
	else
	{
		echo "<script>window.location='".$link."';</script>";
	}
}	
		
}
//----For Privacy Policy------

//---- Página de politicas de privacidade
function pag_priv()
{
	global $wpdb;

	$site = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	$empresa = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'" );
	$admin_email = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'admin_email'" );
	
	?>
	
	<h1><?php echo $empresa;?> POLÍTICAS DE PRIVACIDADE</h1> 
	<p>ULTIMA ACTUALIZAÇÃO: 25 DE MAIO DE 2018</p>
	
	<p>Esta política de privacidade (“Política”) descreve como <?php echo $empresa;?> (“Empresa”, “nós” e “nosso”) procede, recolhe, usa e partilha informação pessoal quando usa este website <?php echo $site;?> (O “Site”). Por favor leia a informação abaixo cuidadosamente para que possa entender as nossas praticas relativamente como lidamos com a sua informação pessoal e como tratamos essa informação. </p>
	
	
	<ol>
	
	</br><li>FINALIDADE DO PROCESSAMENTO</br> 
	<p>O que são dados pessoais?</p> 
	<p>Nós recolhemos informação sobre si de várias formas, incluíndo dados pessoais. Como descrito nesta Política “dados pessoais” conforme é definido no regulamento geral de proteção de dados, incluí qualquer informação, que combinada com mais dados, ou não que recolhemos sobre você identifica você como um indivíduo, incluíndo por exemplo o seu nome, código postal, email e telefone.</p> 
	<p>Porquê que precisamos desta informação pessoal?</p>
	<p>Somente processaremos os seus dados pessoais de acordo com as leis de proteção de dados e privacidade aplicáveis. Precisamos de certos dados pessoais para fornecer-lhe acesso ao site. Se você se registrou connosco, terá sido solicitado que você assinala-se para concordar em fornecer essas informações para acessar aos nossos serviços, como comprar os nossos produtos ou visualizar o nosso conteúdo. Este consentimento nos fornece a base legal que exigimos sob a lei aplicável para processar os seus dados. Você mantém o direito de retirar tal consentimento a qualquer momento. Se você não concordar com o uso dos seus dados pessoais de acordo com esta Política, por favor, não use o nosso website.</p>
</li>
	</br><li>RECOLHENDO OS SEUS DADOS PESSOAIS</br>
	<p>Nós recolhemos informações sobre das seguintes formas: Informações que você nos dá. Inclui:</p> 
	• Os dados pessoais que você fornece quando se registra para usar o nosso website, incluindo seu nome, morada, e-mail, número de telefone, nome de usuário, senha e informações demográficas;</br>
	• Os dados pessoais que podem estar contidos em qualquer comentário ou outra publicação que você no nosso website;</br>
	• Os dados pessoais que você fornece no nosso programa de afiliados ou em outras promoções que corremos no nosso website;</br>
	• Os dados pessoais que você fornece quando reporta um problema no nosso website ou quando necessita de suporte ao cliente;</br> 
	• Os dados pessoais que você fornece quando faz compras no nosso website;</br>
	• Os dados pessoais que você fornece quando nos contacta por telefone, email ou de outra forma.</br> 
	
	<p>Informações que recolhemos automaticamente. registramos automaticamente informações sobre si e o seu computador, ou dispositivo móvel quando você acessa o nosso website. Por exemplo, ao visitar o nosso website, registramos o nome e a versão do seu computador, ou dispositivo móvel, o fabricante e o modelo, o tipo de navegador, o idioma do navegador, a resolução do ecrã, o website visitado antes de entrar no nosso website, as páginas visualizadas e por quanto tempo você esteve em uma página, tempos de acesso e informações sobre o seu uso e ações no nosso website. Recolhemos informações sobre si usando cookies. </p>
</li>	
	</br><li>COOKIES</br>
	
	<p>O que são cookies?</p>
	<p>Podemos recolher informação sua usando “cookies”. Cookies são pequenos arquivos de dados armazenados no disco rígido do seu computador, ou dispositivo móvel no seu browser. Podemos usar tanto cookies (que expiram depois de fechar o browser) como cookies sem data de expiração ( que ficam no seu computador, ou dispositivo móvel até que você os apague) para fornecer-lhe uma experiência mais pessoal e interativa no nosso website. 
	Usamos dois tipos de cookies: Primeiramente cookies inseridos por nós no seu computador, ou dispositivo móvel, que nós utilizamos para reconhecer quando você voltar a visitar o nosso website; e cookies de terceiros que são de serviços prestados por terceiros no nosso website, e que podem ser usados para reconhecer quando o seu computador, ou dispositivo móvel visita o nosso e outros websites.</p>
</li>	 
	<h3>Cookies que utilizamos</h3>
	 
	<p>O nosso website utiliza os seguintes cookies descritos abaixo:</p>


	<table width="100%" border="1">
		<tr>
			<th>Tipo de cookies</th>
			<th>Propósitos</th>
		</tr>
	
		
		<!-- 1 COLUNA -->
		<tr>
			<td>Cookies essenciais</td>
			<td>
				Estes cookies são necessários para fornecer os serviços disponíveis no nosso website, para que você seja capaz de utilizar algumas das suas funcionalidades. Por exemplo, poderão permitir que você faça login na área de membro, ou que carregue o conteúdo do nosso website rapidamente. Sem estes cookies muitos dos serviços disponíveis no nosso website poderão não funcionar correctamente, e só usamos estes cookies para providenciar-lhe um bom serviço.
			</td>
		</tr>
		
		
		<!-- 2 COLUNA -->
		<tr>
			<td>Cookies de funções</td>
			<td>Este cookie permite recordar as escolhas que você já fez no nosso website, como por exemplo a língua, detalhes de login, assim como outras escolhas que fez que possam ser personalizadas por si. O propósito deste cookies é relembrar todas as escolhas que você fez de formar a criar uma experiência mais personalizada sem ter que inserir novamente os seus dados no nosso website.</td>
		</tr>
		
		
		<!-- 3 COLUNA -->
		<tr>
			<td>Cookies de analise e performance</td>
			<td>Estes cookies servem para recolher a informações sobre o tráfego do nosso website e como os utilizadores utilização e navegam no website. A informação recolhida não identifica em particular nenhum utilizador. Inclui o número de visitantes no nosso website, os websites que indicam o nosso website, as páginas que os utilizadores visitam, os possíveis websites que eles visitaram anteriormente e outras informações similares. Nós utilizamos estas informações para melhorar e monitorar a actividade do nosso website. </br>
Nós utilizamos o Google Analytics com este propósito. Google Analytics utiliza os seus próprio cookies. É somente utilizado para melhorar a forma como o nosso website funciona. Poderá saber mais informação sobre os cookies do Google Analytics aqui: https://developers.google.com/ analytics/resources/concepts/gaConceptsCookies 
Saiba mais como o Google protege os seus dados aqui:</br> 
http://www.google.com/analytics/learn/privacy.html 
Você pode evitar o uso deste cookie do Google Analytics no seu browser ao instalar uma extensão no google chrome, poderá baixar esta extensão aqui:</br> http://tools.google.com/ dlpage/gaoptout?hl=en-GB 
    </td>
		</tr>
		
		
		<!-- 4 COLUNA -->
		<tr>
			<td>Cookies de publicidade</td>
			<td>Estes cookies analisam a forma como navega para podermos mostrar-lhe anúncios que provavelmente serão do seu interesse. Este cookies utilizam a informação do seu histórico de navegação que com a sua permissão mostra-lhe anúncios relevantes de terceiros, baseado nos seus interesses. 
Você pode desabilitar cookies que guardam o histórico da sua navegação, visitando o website http:// www.youronlinechoices.com/uk/your-ad-choices . Se escolher remover este tipo de cookies, você verá na mesma os anúncios, mas não serão relevantes segundo os seus interesses. 
 </td>
		</tr>
		
		
		<!-- 5 COLUNA -->
		<tr>
			<td>Cookies de redes sociais</td>
			<td>Estes cookies são utilizados quando você partilha informação nas redes sociais, ou de alguma forma tem acesso aos nossos conteúdos através das redes sociais como o Facebook, Twitter, ou Google+.</td>
		</tr>
		
		
	</table>



	<b>Desabilitando os cookies</b>
	 
	<p>Você pode remover, ou rejeitar cookies através das configurações do browser. Para fazer isso recomendamos que siga as instruções do seus browser (normalmente pode encontrar estas informações nas “configurações” do seu browser em “ajuda”, ou 
	“ferramentas”). Maior parte dos browser aceitam os cookies automaticamente até você alterar as configurações do mesmo.</p>
	<p>Se não aceitar os nossos cookies, a sua experiência no nosso site não será tão agradável. Por exemplo nós poderemos não reconhecer o seu computador, ou dispositivo móvel e você poderá ter que fazer login sempre que entrar no nosso website.</p>
	</br><li>PUBLICIDADE</br> 
	<p>Nós poderemos utilizar terceiros para apresentar anúncios quando visita o nosso website. Estas empresas poderão recolher informações como, tipo de browser, hora e dia, tipo de anúncio foi clicado, neste e outros websites de forma a mostrar os anúncios mais relevantes a você. Estas empresas normalmente utilizam o seu sistema para recolher estes dados, que estão sujeitos ás suas políticas de privacidade.</p> 
</li>
	</br><li>USANDO OS SEUS DADOS PESSOAIS </br>
	<p>Nós poderemos utilizar os seus dados pessoais da seguinte forma:</p>
	• para manter e melhorar o nosso website, produtos e serviços;</br>
	• para gerir a sua conta, incluindo comunicações que temos consigo relativamente á sua conta, se tiver uma conta no nosso website:</br>
	• para operar e administrar o nosso programa de afiliados e outras promoções que você poderá participar no nosso website;</br>
	• para responder aos seus comentários e perguntas e para prestar apoio ao cliente;</br>
	• para enviar informações, incluindo informação técnica, actualizações, alertas de segurança e suporte;</br>
	• com o seu consentimento, fazemos e-mail marketing sobre promoções, e outras novidades, incluindo informação sobre os nossos produtos, ou serviços oferecidos por nós, ou pelos nossos afiliados. Você poderá deixar de receber estas informações a qualquer momento, em todos os nossos emails tem sempre uma opção de sair da lista. Mesmo que saia da nossa lista poderemos enviar e-mails não relacionados com marketing, incluem e-mails sobre alguma conta que tenha connosco (se tiver uma), ou negócios que tenha connosco;</br>
	• para processar pagamentos que tenha realizado no nosso website;</br>
	• quando acharmos necessário e apropriado (a) para cumprir com a lei (b) para cumprir com pedidos e processo legais, incluindo pedidos de autoridades públicas e governamentais; (c) para cumprir a nossa Política; e (d) para proteger os direitos, privacidade, segurança, seus e de outros.</br>
	• para analisar e estudar serviços; </br>
	• como descrito abaixo em “Partilhar os seus dados pessoais”. </br>
</li>
	</br><li>PARTILHAR OS SEUS DADOS PESSOAIS</br>
		
	<p>Podemos partilhar os seus dados pessoais das seguintes formas:</p>
	
	• A terceiros designados por você. Podemos partilhar os seus dados com terceiros em que você tenha dado o seu consentimento.</br>
	• Serviços prestados por terceiros. Poderemos partilhar os seus dados pessoais com terceiros que realizam alguns serviços como (analise de dados, processamento de pagamentos, suporte ao cliente, envio de e-mail marketing e outros serviços similares).</br>
</li> 
	
	</br><li>WEBSITE DE TERCEIROS</br>
		 
	<p>O nosso website poderá conter links de terceiros. Esta Política não cobre as Políticas de privacidade de terceiros. Estes websites de terceiros tem as suas próprias políticas de privacidade e não não aceitamos qualquer responsabilidade sobre esses websites, suas funções, ou políticas de privacidade. Por favor leia as políticas de privacidade destes websites de terceiros antes de submeter qualquer informação.</p> 
</li>		
	</br><li>CONTEÚDO GERADO PELO UTILIZADOR</br>
	<p>Poderá partilhar os seus dados pessoais connosco quando submete e gera conteúdo no nosso website, incluí comentários no blog, mensagens de suporte no nosso website. Por favor tenha noção que qualquer informação que você publique no nosso website torna-se de conhecimento publico e ficará acessível a todos os usuários do nosso website incluíndo visitantes. Sugerimos que tenha muito cuidado quando decidir tornar publico os seus dados pessoais, ou qualquer outra informação no nosso website. Qualquer informação pessoal publicada no nosso website não ficará privada ou confidencial.</p>
	 
	<p>Se você nos der alguma review, ou comentário, nós poderemos tornar publico essas informações no nosso website.</p>
</li>
	</br><li>TRANSFERÊNCIA DE DADOS INTERNACIONAL</br>
		 
	<p>As suas informações incluíndo dados pessoais que recolhemos de você, poderão ser transferidos para, guardado em, e processado por nós fora do país onde você reside, onde proteção de dados e regulamentos de privacidade poderão não ter o mesmo nível de proteção como em outros países. Ao aceitar esta política de privacidade você concorda em transferir, guardar e processar os seus dados. Nós iremos tomar todas as medidas necessárias para assegurar que os seus dados são tratados da forma mais segura e de acordo com as nossas políticas.</p>
	</br><li>SEGURANÇA</br>
	<p>Procuramos tomar sempre todas as medidas, técnicas e administrativas para proteger todos os seus dados da forma mais segura possível. Infelizmente nenhum sistema é 100% seguro e poderá garantir completamente a segurança dos seus dados. Se você pensa que os seus dados já não estão seguros connosco (por exemplo o acesso á sua conta foi comprometido), por favor entre em contacto connosco imediatamente e relate- nos o seu problema. </p>
</li>	
	</br><li>RETENÇÃO</br>
	<p>Nós apenas guardaremos a sua informação pessoal durante 30 dias a não ser que um período mais longo seja necessário, ou permitido por lei.</br>
	OU</br> 
	Nós apenas guardaremos a sua informação pessoal enquanto for necessário e permitido por você para que você possa utilizar o nosso website até que você feche a sua conta ou termine a sua subscrição, a não ser que o período mais longo seja necessário, ou permitido por lei.</p>
</li>	 
	</br><li>NOSSA POLÍTICA COM CRIANÇAS</br>
		
	<p>O nosso website não é direcionado para crianças abaixo dos 18 anos. Se um pai, ou um encarregado de educação verificar que o seu, ou a sua filha forneceu dados pessoais no nosso website sem o seu consentimento, deverá contactar-nos imediatamente. Nós iremos apagar todos esses dados o mais rápido possível.</p>
</li>	 
	</br><li>OS SEUS DIREITOS</br> 
	• <b>Sair da lista.</b> Você pode contactar-nos a qualquer momento para sair da nossa lista; (i) comunicações de e-mail marketing (ii) a nossa recolha de dados pessoais sensíveis (iii) qualquer novo processamento de dados pessoais que poderemos realizar.</br>
	• <b>Acesso.</b> Você poderá ter acesso ás informações que nós possuímos de você a qualquer momento ao contactar-nos.</br> 
	• <b>Alterar.</b> Você também poderá contactar-nos para actualizar ou corrigir qualquer informação pessoal que tenhamos sua.</br>
	• <b>Mover.</b> A sua informação pessoal pode ser transferida. Você tem a flexibilidade de mover os seus dados para outro serviço se assim desejar.</br>
	• <b>Apagar.</b> Em algumas situações, por exemplo quando a informação que temos sobre si já não é relevante, ou é incorrecta, você poderá pedir para apagarmos os seus dados.</br>
	
	<p>Se você quiser exercer qualquer um deste direitos por favor contacte-nos. Por favor deixe bem claro no seu pedido (i) que informação quer; e (ii) quais dos direitos acima você quer usar. Para a sua proteção apenas poderemos completar os seus pedidos, se estes forem feitos com o mesmo email associado aos seus dados, iremos verificar a identidade antes de efectuar qualquer alteração. Nós iremos atender ao seu pedido o mais breve possível, não mais que 30 dias. Por favor tenha atenção que eventualmente poderemos ter que guardar alguma informação para mantermos em registro.</p>
</li>	
	</br><li>QUEIXAS</br> 
	Nós estamos empenhados em resolver qualquer queixa sobre a forma como recolhemos os seus dados pessoais. Se tiver alguma queixa que queira fazer sobre a nossa política de privacidade, ou as nossas praticas relacionadas com os seus dados pessoais, por favor contacte-nos em: <a href="mailto:<?php echo $admin_email;?>"><?php echo $admin_email;?></a>. Nós iremos responder ao seu contacto o mais rápido possível, no máximo de 30 dias. Nós esperamos resolver qualquer situação que seja trazida até nós por você, em todo o caso se verificarmos que a sua queixa não é aplicável, você está no seu direito de contactar a autoridade local de protecção de dados.</b>
</li>
	</br><li>INFORMAÇÃO DE CONTACTO</b>
		 
	<p>Nós agradecemos os seus comentários e questões que tenha sobre a nossa política de privacidade. Poderá contactar-nos por e-mail em <a href="mailto:<?php echo $admin_email;?>"><?php echo $admin_email;?></a></p>
</li>
	
</ol>
	
	
	
	<?php
	
}

function ppacceptbutton()
{//privacy policy shortcode accept button and add user meta or cookie for it
$pref='WP-RGPD-Compliance-';
	if(isset($_POST['rgpdacceptpp']))
	{
		wprgpd_store_consent_for_tandc_and_pp('pp');
		
		if(is_user_logged_in())
		{//if user logged in add or update user meta
          $user =get_current_user_id();
		  
          $havemeta = get_user_meta($user,$pref.'pp', false);
		  if( $havemeta)
		  {
			 if(get_user_meta($user,$pref.'pp', true)!=get_option($pref.'pp-version'))
			 {
				 update_user_meta($user,$pref.'pp',get_option($pref.'pp-version'));
			 }		 
		  }
		  else
		  {
			  add_user_meta($user,$pref.'pp',get_option($pref.'pp-version'));}
        }
		
									 
		if(get_option($pref.'pp-aft')=='l')
		{
			
			$link=$_SESSION['pplvpage'];
			echo "<script>window.location='".$link."';</script>";
		}
		else if(get_option($pref.'pp-aft')=='h')
		{
			
			$link=$_SESSION['pplvpage'];
			echo "<script>window.location='".get_home_url()."';</script>";
		}
		else if(get_option($pref.'pp-aft')=='n')
		{
			
		}
		else
		{ 
			$link=get_permalink(get_option($pref.'pp-aft'));
			echo "<script>window.location='".$link."';</script>";
		}
	}
	$nlg="";
	if(!is_user_logged_in())
	{
		$nlg="<input type='hidden' value='1' name='rgpdnlgpp'>";
	}
	$form="<p><form action='' method='post'>".$nlg."
	<label class='containerr'>
	".__('I have reviewed the Privacy Policy and I give my consent to the terms laid out.','rgpdpro')."
	<input type='checkbox' required=''>
	<span class='checkmark'></span></label>
	<input type='submit' class='rgpdacceptbutton' value='".__('Accept It','rgpdpro')."' name='rgpdacceptpp' ></form></p>";

	return $form;
}
function rgpdsetppcookie()
{//adding a cookie for privacy policy
	$pref='WP-RGPD-Compliance-';
	$ppcookie=$pref.'pp';
	if(isset($_COOKIE[$ppcookie]))
	{
	if($_COOKIE[$ppcookie]!=get_option($pref.'pp-version'))
	setcookie($ppcookie,get_option($pref.'pp-version'),time()+(480000*365),COOKIEPATH, COOKIE_DOMAIN);
    }
	else
	{setcookie($ppcookie,get_option($pref.'pp-version'),time()+(480000*365),COOKIEPATH, COOKIE_DOMAIN);}
	
}
function rgpd_check_pp_cookie_or_usermeta()
{//check cookie or user meta set or not for privacy policy
$pref='WP-RGPD-Compliance-';

$https = ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off')) ? true : false;
if(rmhttpurlandmatch(get_option($pref.'tandc-bef'))==1 || rmhttpurlandmatch(get_option($pref.'pp-bef'))==1)
{}
else{
if($https) {
    $_SESSION['pplvpage']= "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
} else {
   $_SESSION['pplvpage']= "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
    }
	$ppcookie=$pref.'pp';
	$link=get_permalink(get_option($pref.'pp-bef'));
	
if(is_user_logged_in()&&(get_option($pref.'pp-bef')!='0'))
{
	$user =get_current_user_id();
	$havemeta = get_user_meta($user,$pref.'pp', false);
	if(get_option($pref.'pp-lusr')=='1'){
		    if($havemeta){
				if(get_user_meta($user,$pref.'pp', true)!=get_option($pref.'pp-version'))
			echo "<script>window.location='".$link."';</script>";
			             }
             else
			 {
				echo "<script>window.location='".$link."';</script>"; 
			 }				 
			                           }
}
else if(get_option($pref.'pp-bef')!='0'&& get_option($pref.'pp-nlusr')=='1')
{
	if(get_option($pref.'pp-nlusr')=='1')
	{
		if(isset($_COOKIE[$ppcookie]))
		{
			if($_COOKIE[$ppcookie]!=get_option($pref.'pp-version'))
			echo "<script>window.location='".$link."';</script>";
		}
		else
		{
			echo "<script>window.location='".$link."';</script>";
		}
	}
	else
	{
		echo "<script>window.location='".$link."';</script>";
	}
}	
		
}
//---------Right to be forgotten request table----------------------
function showRequestToForget()
{
	echo '<script>
		
			</script>';
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='fm' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='fm' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		if($data->login=='1')
		{
			$login="<font color='green'>".__('Logged In','rgpdpro')." </font>";
		}
		else if($data->login=='2')
		{
			$login="<font color='green'>".__('Confirmed with verification Link.','rgpdpro')."</font>";
		}
		else
		{
			$login="<font color='red'>".__('Confirmation Link Sent.','rgpdpro')."</font>";
		}
		if($data->action=='0')
		{

			$action='<table><tr><td><button type="submit" class="btn btn-primary" value="Forget" name="rgpdfmtakeaction" data-toggle="tooltip" title="'.__('Forget Requested Data and Send a Confirmation Email','rgpdpro').'">
			<span class="glyphicon glyphicon-ok-sign"></span>
            </button></td><td>
			<button type="submit" class="btn btn-info" value="Forget" name="rgpdfmview" data-toggle="tooltip" title="'.__('View Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-eye-open"></span>
            </button></td><td>
			<button type="submit" name="rgpdfmremove" class="btn btn-danger" value="remove" onclick="return rgpdfmActionDel()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></td></tr></table>
			';
		}
		else
		{
			$action='<span style="margin-left:84px;"><button type="submit" name="rgpdfmremove" class="btn btn-danger" value="remove" onclick="return rgpdfmActionDel()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button>';
		}
		if($data->value=='c'){$type=__('Comments','rgpdpro');}if($data->value=='p'){$type=__('Posts','rgpdpro');}if($data->value=='u'){$type=__("User Meta","rgpdpro");}
		echo
		'
		<form action="" method="post">
		<input type="hidden" value="'.$data->value.'" name="afmvalue">
		<input type="hidden" value='.$data->id.' name="afmreqid">
		<input type="hidden" value='.$data->user.' name="afmuser">
		<input type="hidden" value='.$data->email.' name="afmemail">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->email.'</td><td>'.$login.'</td><td>'.$type.'</td><td>'.$data->recorded.'</td><td>'.$data->actiontime.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="8">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}
//---------Data access request table----------------------

function showRequestToDataAccess()
{
	echo '<script>
		
			</script>';
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='da' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='da' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		if($data->login=='1')
		{
			$login="<font color='green'>".__('Logged In','rgpdpro')."</font>";
		}
		else if($data->login=='2')
		{
			$login="<font color='green'>".__('Confirmed with verification Link.','rgpdpro')."</font>";
		}
		else
		{
			$login="<font color='red'>".__('Confirmation Link Sent.','rgpdpro')."</font>";
		}
		/*else if($data->login=='2')
		{
			$login="<font color='orange'></p>Email is not registered with this id .</p></font>";
		}
		else if($data->login=='0')
		{
			$login="<font color='red'></p>Did not login.</p></font>";
		}*/
		
		if($data->action=='0')
		{
			$action='<table><tr><td><button type="submit" class="btn btn-primary" value="Forget" name="rgpddatakeaction" data-toggle="tooltip" title="'.__('Send Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-ok-sign"></span>
            </button></td><td>
			<button type="submit" class="btn btn-info" value="Forget" name="rgpddaview" data-toggle="tooltip" title="'.__('View Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-eye-open"></span>
            </button></td><td>
			<button type="submit" name="rgpddaremove" class="btn btn-danger" value="remove" onclick="return rgpddaAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></td></tr></table>
			';
		}
		else
		{
			$action='<span style="margin-left:84px"><button type="submit" name="rgpddaremove" class="btn btn-danger" value="remove" onclick="return rgpddaAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></span>';
		}
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->id.' name="dareqid">
		<input type="hidden" value='.$data->user.' name="dauser">
		<input type="hidden" value='.$data->email.' name="daemail">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->email.'</td><td>'.$login.'</td><td>'.$data->recorded.'</td><td>'.$data->actiontime.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}
//----------data rectification request table-------------


function showRequestToDataRectification()
{
	echo '<script>
		
			</script>';
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='dr' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='dr' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		if($data->login=='1')
		{
			$login="<font color='green'>".__('Logged In','rgpdpro')."</font>";
		}
		else if($data->login=='2')
		{
			$login="<font color='green'>".__('Confirmed with verification Link.','rgpdpro')."</font>";
		}
		else
		{
			$login="<font color='red'>".__('Confirmation Link Sent.','rgpdpro')."</font>";
		}
		/*else if($data->login=='2')
		{
			$login="<font color='orange'></p>Email is not registered with this id .</p></font>";
		}
		else if($data->login=='0')
		{
			$login="<font color='red'></p>Did not login.</p></font>";
		}*/
		if($data->value=='c'){$type=__('Comments','rgpdpro');}if($data->value=='p'){$type=__('Posts','rgpdpro');}if($data->value=='u'){$type=__("User Data",'rgpdpro');}
		if($data->action=='0')
		{
			$action='<table><tr><td><button type="submit" class="btn btn-primary" value="Forget" name="rgpddrtakeaction" data-toggle="tooltip" title="'.__('Send Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-ok-sign"></span>
            </button></td><td>
			<button type="submit" class="btn btn-info" value="Forget" name="rgpddrview" data-toggle="tooltip" title="'.__('View Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-eye-open"></span>
            </button></td><td>
			<button type="submit" name="rgpddrremove" class="btn btn-danger" value="remove" onclick="return rgpddrAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></td></tr></table>
			';
		}
		else
		{
			$action='<span style="margin-left:84px"><button type="submit" name="rgpddrremove" class="btn btn-danger" value="remove" onclick="return rgpddrAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></span>';
		}
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->value.' name="drrtype">
		<input type="hidden" value='.$data->id.' name="drreqid">
		<input type="hidden" value='.$data->user.' name="druser">
		<input type="hidden" value='.$data->email.' name="dremail">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->email.'</td><td>'.$login.'</td><td>'.$type.'</td><td>'.$data->recorded.'</td><td>'.$data->actiontime.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}
//--------rgpd unsubscription request table----------

function rgpd_unsubscription_request_table()
{
	echo '<script>
		
			</script>';
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='uns' and (login='1' or login='2') and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='uns' and (login='1' or login='2') order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		if($data->login=='1')
		{
			$login="<font color='green'>".__('Logged In','rgpdpro')."</font>";
		}
		else if($data->login=='2')
		{
			$login="<font color='green'>".__('Confirmed with verification Link.','rgpdpro')."</font>";
		}
		else
		{
			$login="<font color='red'>".__('Confirmation Link Sent.','rgpdpro')."</font>";
		}
		
		
		if($data->value=='c'){$type=__("Comments",'rgpdpro');}if($data->value=='p'){$type=__("Posts",'rgpdpro');}if($data->value=='u'){$type=__("User Data",'rgpdpro');}
		if($data->action=='0')
		{
			$action='<table><tr><td><button type="submit" class="btn btn-primary" value="Forget" name="rgpdunstakeaction" data-toggle="tooltip" title="'.__('Send Requested Data','rgpdpro').'">
			<span class="glyphicon glyphicon-ok-sign"></span>
            </button></td>
			<td>
			<button type="submit" name="rgpdunsremove" class="btn btn-danger" value="remove" onclick="return rgpdunsAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></td></tr></table>
			';
			$status=__("Pending",'rgpdpro');
		}
		else
		{
			$action='<span style="margin-left:41px"><button type="submit" name="rgpdunsremove" class="btn btn-danger" value="remove" onclick="return rgpdunsAction()" data-toggle="tooltip" title="'.__('Remove Request','rgpdpro').'">
			<span class="glyphicon glyphicon-trash"></span>
            </button></span>';
			$status="";
			$status=__("Unsubscribed",'rgpdpro');
		}
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->value.' name="unstype">
		<input type="hidden" value='.$data->id.' name="unsreqid">
		<input type="hidden" value='.$data->user.' name="unsuser">
		<input type="hidden" value='.$data->email.' name="unsemail">
		<tr>
		<td><input type="checkbox" id="id'.$data->id.'" name="unschk[]" class="containerr" value="'.$data->id.'" onclick="collectID(this.value)"></td><td>'.$data->email.'</td><td>'.$data->value.'</td><td>'.$data->recorded.'</td><td>'.$status.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
	
}
//-----wp rgpd fix cookie consent table

function showRequestToCookieConsent()
{
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='cookie' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='cookie' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		if($data->value=='1')
		{
			$accept="<font color='green'>".__('Accepted','rgpdpro')."</font>";
		}
		else
		{
			$accept="<font color='red'>".__('Did not Accept','rgpdpro')."</font>";
		}
		    $cookielogrmvreq=__('Remove Request','rgpdpro');
			$action='<button type="submit" name="cookieconsentremove" class="btn btn-danger" value="remove" onclick="return rgpddrAction()" data-toggle="tooltip" title="'.$cookielogrmvreq.'">
			<span class="glyphicon glyphicon-trash"></span>
            </button>';
			
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->id.' name="cookieconsentid">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->ip.'</td><td>'.$accept.'</td><td>'.$data->recorded.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}



//---consent table for terms and conditions

function showRequestToTandCConsent()
{
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='tandc' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='tandc' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		    $cookielogrmvreq=__('Remove Request','rgpdpro');
			$action='<button type="submit" name="tandcconsentremove" class="btn btn-danger" value="remove" onclick="return rgpddrAction()" title="'.$cookielogrmvreq.'">
			<span class="glyphicon glyphicon-trash"></span>
            </button>';
			
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->id.' name="tandcconsentid">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->ip.'</td><td>'.$data->recorded.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}

//----consent table for privacy policy--------

function showRequestToPPConsent()
{
	
	global $wpdb;
	$table=$wpdb->prefix.'rgpd_request_records';
	
	 if(isset($_GET['wpcrpagecount']))
	  {
		  $wpcridin=$_GET['wpcrpagecount'];
		  $sql="select * from ".$table." where type='pp' and id <".$wpcridin." order by id desc";
	   
	  }
	  else 
	  {
	  $sql="select * from ".$table." where type='pp' order by id desc";
	  }
	$records=$wpdb->get_results($sql);
	$wpcrcount=0;
	$last=0;
	foreach($records as $data)
	{
		$wpcrcount++;
		
		    $cookielogrmvreq=__('Remove Request','rgpdpro');
			$action='<button type="submit" name="ppconsentremove" class="btn btn-danger" value="remove" onclick="return rgpddrAction()" title="'.$cookielogrmvreq.'">
			<span class="glyphicon glyphicon-trash"></span>
            </button>';
			
		echo
		'
		<form action="" method="post">
		<input type="hidden" value='.$data->id.' name="ppconsentid">
		<tr>
		<td>'.$data->id.'</td><td>'.$data->ip.'</td><td>'.$data->recorded.'</td><td>'.$action.'</td>
		</tr>
		</form>
		';
		if($wpcrcount==10)
		   {
			      
			   break;
		   }
	}
	$rgpdfmnxtpage='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&wpcrpagecount='.$data->id;
	echo
	'
	<tfoot>
		<tr>
			<td colspan="7">
			<ul class="pager">';
		if(isset($_GET['wpcrpagecount']))
		{			
             echo '			
			 <script type="text/javascript">
            function goBack() {
              window.history.back();
                  }
                </script>
				<li class="previous" onclick="goBack()"><a style="cursor:pointer">'.__('Previous','rgpdpro').'</a></li>';
		}
        
      if($wpcrcount==10)
	  {		  
	echo '<li class="next"><a href="'.$rgpdfmnxtpage.'">'.__('Next','rgpdpro').'</a></li>';
	  }
		
			
	echo '</ul>
			</td>
		</tr>
	</tfoot>
	
	';
}



//--------rgpd send mail function----
function rgpdSendWPMail($to,$subject,$body)
{
	$wpcrmailheaders = "MIME-Version: 1.0" . "\r\n";
                $wpcrmailheaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				
				$wpcrmailbody='
				<html><head></head><body>
				<p style="font-size:18px;padding:2px;margin-top:8px;margin-bottom:6px;color:white;background-color:#003366;">'.$subject.'</p><br>
				<p>'.$body.'</p>
				</body></html>
				';
				
				wp_mail($to,$subject,$body,$wpcrmailheaders);
}
//--------rgpd short code frgt me request function----
function rgpdRequestForgetMe()
{
	$head="".__('Hello,','rgpdpro')."<br>".__('A request to forget was created on your blog. It\'s logged in the admin section of WP RGPD .','rgpdpro')." <br>";
	$foot="<br>".__('Please review it when convenient.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";
	
	$pref='WP-RGPD-Compliance-';
	global $wpdb;
	$table=$wpdb->prefix."rgpd_request_records";
	$login='0';
if(is_user_logged_in())
{
	if(email_exists($_POST['rgpdfmemail'])==get_current_user_id())
	{$login='1';}
}
if($_POST['rgpdfmchkboxc']=='c')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpdfmemail'],__('Request to Forget Comments Confirmation','rgpdpro'),'fm');
	   $clogin=$vlogin;
   }
	else
	{$clogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpdfmemail'],$clogin,'fm',$_SERVER['REMOTE_ADDR'],'c','',date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.__("The requesting email id : ",'rgpdpro').$_POST['rgpdfmemail'].$foot;
	rgpdSendWPMail(get_option($pref.'rtbf-email'),__('Request to forget created','rgpdpro'),$body);
	
}
if($_POST['rgpdfmchkboxp']=='p')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpdfmemail'],__('Request to Forget Posts Confirmation','rgpdpro'),'fm');
	   $plogin=$vlogin;
   }
	else {$plogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpdfmemail'],$plogin,'fm',$_SERVER['REMOTE_ADDR'],'p','',date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.__("The requesting email id : ",'rgpdpro').$_POST['rgpdfmemail'].$foot;
	rgpdSendWPMail(get_option($pref.'rtbf-email'),__('Request to forget created.','rgpdpro'),$body);
}
if($_POST['rgpdfmchkboxu']=='u')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpdfmemail'],__('Request to Forget User Meta Confirmation','rgpdpro'),'fm');
	   $ulogin=$vlogin;
   }
	else {$ulogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpdfmemail'],$ulogin,'fm',$_SERVER['REMOTE_ADDR'],'u','',date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.__("The requesting email id : ",'rgpdpro').$_POST['rgpdfmemail'].$foot;
	rgpdSendWPMail(get_option($pref.'rtbf-email'),__('Request to forget created .','rgpdpro'),$body);
}
if($login=='1')
{echo "<script>alert('".__('Request Received','rgpdpro')."');</script>";}
else
{echo "<script>alert('".__('Confirmation mail sent','rgpdpro')."');</script>";}
}
//-------------rgpd short code data access request submission------------
function rgpdRequestDataAccess()
{
	$pref='WP-RGPD-Compliance-';
	global $wpdb;
	$table=$wpdb->prefix."rgpd_request_records";
	$login='0';
if(is_user_logged_in())
{
	if(email_exists($_POST['rgpdemailpda'])==get_current_user_id())
	{
		$login='1';
		$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpdemailpda'],$login,'da',$_SERVER['REMOTE_ADDR'],'','',date('d-M-Y h:iA'),'0',' ')));
		
	}
    
}
if($login=='0')
{
	$login=rgpdMailConfirmationLink($_POST['rgpdemailpda'],__('Data Access Confirmation','rgpdpro'),'da');
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpdemailpda'],$login,'da',$_SERVER['REMOTE_ADDR'],'','',date('d-M-Y h:iA'),'0',' ')));
	echo "<script>alert('".__('Confirmation Link Sent','rgpdpro')."')</script>";
}

	
	
	$body="".__('Hello,','rgpdpro')."<br>".__('A data access report was filed by','rgpdpro')." ".$_POST['rgpdemailpda']." ".__('The report has been logged in the admin of WP RGPD .','rgpdpro')."<br>".__('Please review at your convenience.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";
	rgpdSendWPMail(get_option($pref.'da-email'),__('Data report requested','rgpdpro'),$body);
	if($login=='1')
	{echo "<script>alert('".__('Request Received','rgpdpro')."');</script>";}
}

//---------rgpd shortcode data rectification request submission--------
function rgpdRequestDataRectification()
{
	$head="".__('Hello,','rgpdpro')."<br>".__('A data rectification request was logged by','rgpdpro')." <br>";
	$foot="<br>".__('It\'s recorded in the admin of WP RGPD .','rgpdpro')."<br>".__('Please review when convenient.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";
	
	$pref='WP-RGPD-Compliance-';
	global $wpdb;
	$table=$wpdb->prefix."rgpd_request_records";
	$login='0';
if(is_user_logged_in())
{
	if(email_exists($_POST['rgpddremail'])==get_current_user_id())
	{$login='1';}
}
if($_POST['rgpddrchkboxc']=='c')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpddremail'],__('Request to rectify Comments confirmation','rgpdpro'),'dr');
	   $clogin=$vlogin;
   }
	else
	{$clogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpddremail'],$clogin,'dr',$_SERVER['REMOTE_ADDR'],'c',$_POST['rgpdrectification'],date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.$_POST['rgpddremail'].$foot;
	rgpdSendWPMail(get_option($pref.'drr-email'),__('Data rectification request logged','rgpdpro'),$body);
	
}
if($_POST['rgpddrchkboxp']=='p')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpddremail'],__('Request to forget Posts confirmation','rgpdpro'),'dr');
	   $plogin=$vlogin;
   }
	else {$plogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpddremail'],$plogin,'dr',$_SERVER['REMOTE_ADDR'],'p',$_POST['rgpdrectification'],date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.$_POST['rgpddremail'].$foot;
	
	rgpdSendWPMail(get_option($pref.'drr-email'),__('Data rectification request logged','rgpdpro'),$body);
}
if($_POST['rgpddrchkboxu']=='u')
{
	if($login=='0')
   {
	   $vlogin=rgpdMailConfirmationLink($_POST['rgpddremail'],__('Request to rectify User data Confirmation','rgpdpro'),'dr');
	   $ulogin=$vlogin;
   }
	else {$ulogin=$login;}
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['rgpddremail'],$ulogin,'dr',$_SERVER['REMOTE_ADDR'],'u',$_POST['rgpdrectification'],date('d-M-Y h:iA'),'0',' ')));
	
	$body=$head.$_POST['rgpddremail'].$foot;
	rgpdSendWPMail(get_option($pref.'drr-email'),__('Data rectification request logged','rgpdpro'),$body);
}
if($login=='1')
{echo "<script>alert('".__('Request Received','rgpdpro')."');</script>";}
else
{echo "<script>alert('".__('Confirmation mail sent','rgpdpro')."');</script>";}
}

//----rgpd shortcode unsubscription function-----

function rgpdUnsubscriptionRequestSubmit()
{
	$pref='WP-RGPD-Compliance-';
	global $wpdb;
	$table=$wpdb->prefix."rgpd_request_records";
	$login='0';
if(is_user_logged_in())
{
	if(email_exists($_POST['unsemail'])==get_current_user_id())
	{
		$login='1';
		$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['unsemail'],$login,'uns',$_SERVER['REMOTE_ADDR'],$_POST['unsname'],'',date('d-M-Y h:iA'),'0',' ')));
		
	}
    
}
if($login=='0')
{
	$login=rgpdMailConfirmationLink($_POST['unsemail'],__('Unsubscription Confirmation','rgpdpro'),'uns');
	$in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('',get_current_user_id(),$_POST['unsemail'],$login,'uns',$_SERVER['REMOTE_ADDR'],$_POST['unsname'],'',date('d-M-Y h:iA'),'0',' ')));
	echo "<script>alert('".__('Confirmation Link Sent','rgpdpro')."')</script>";
}

	
	
	//$body="<b>From : ".$_POST['rgpdemailpda']."</b>";
	//rgpdSendWPMail(get_option($pref.'da-email'),'Unsubscription Request Received',$body);
	if($login=='1')
	{echo "<script>alert('".__('Request Received','rgpdpro')."');</script>";}

	$wpcrmailheaders = "MIME-Version: 1.0" . "\r\n";
    $wpcrmailheaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $assunto = "Pedido sair da lista";
    $conteudo = "Um novo pedido para sair da lista foi efetuado no seu blog " . get_site_url();


    $email_alert = get_option('admin_email');
    //email do admin
    //buscar email escolhido, se não houver, enviar para o email do admin.

	wp_mail($email_alert, $assunto, $conteudo, $wpcrmailheaders);

}



//-------mail confirmation link-------
function rgpdMailConfirmationLink($to,$subject,$type)
{
	if($type=='da')
	{
		$subject=__("Please confirm your data request",'rgpdpro');
		$cnfheader="".__('Hello,','rgpdpro')."<br>".__('You\'ve made a request to report all the data we have on you with us.','rgpdpro')."<br>".__('Plese confirm that you have indeed made this request by clicking the link below.','rgpdpro')."<br>";
        $cnffooter="<br>".__('Your data will be sent to you on confirmation.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";	
	}
	if($type=='fm')
	{
		$subject=__('Request to Forget','rgpdpro');
		$cnfheader="".__('Hello,','rgpdpro')."<br>".__('You\'ve made a request to forget with us. Please click on the link below to confirm that the request is valid. Your request will be processed after you click the confirmation.','rgpdpro')."<br>";
        $cnffooter="<br>".__('Thanks','rgpdpro')."";
	}
	if($type=='dr')
	{
		$subject=__("Your request to rectify data",'rgpdpro');
		$cnfheader="".__('Hello,','rgpdpro')."<br>".__('You\'ve made a request to rectify your data with us. Please click on the link below to confirm the request.','rgpdpro')."<br>";
        $cnffooter="<br>".__('Your data will be rectified by the admin on confirmation.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";
	}
	if($type=='uns')
	{
		$subject=__("Your unsubscribe request",'rgpdpro');
		$cnfheader="".__('Hello,','rgpdpro')."
<br>
".__('This is in reference to the unsubscribe request that you submitted with us.','rgpdpro')."
<br>
".__('Please click on the link below to confirm unsubscrition.','rgpdpro')."<br>";
        $cnffooter="<br>".__('The blog administrator will be notified and you will be unsubscribed as soon as possible.','rgpdpro')."<br>".__('Thanks','rgpdpro')."";
	}
	$wpcrmailheaders = "MIME-Version: 1.0" . "\r\n";
    $wpcrmailheaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$otp=substr(str_shuffle('123456789abscfghijklmnopqrstuvwxyz@ABCDFGHJ'),0,5);
	$msg=get_permalink().'?rgpdemail='.$to.'&rgpdtype='.$type.'&rgpdotp='.$otp;
	$msg=$cnfheader.$msg.$cnffooter;
	wp_mail($to,$subject,$msg,$wpcrmailheaders);
	return $otp;
}
//-----------------Ideias digitais logo-------------------
function rgpdteknikforce_display_logo()
{
	?>

 <div class="alert alert-info">
      <p style="font-size:24px"><strong><?php _e('Já conhece as nossas ferramentas?','rgpdpro')?></strong></p>
      <p style="font-size: 18px;"><?php _e('Tema wordpress Avenger') ?></p>
      <p><?php _e('O melhor tema wordpress desenhado a pensar no marketing digital, e você por ser nosso cliente, tem até 50% de desconto na licença ilimitada no link abaixo.','rgpdpro')?></p>

      <p><a href="https://wprgpdpro.com/desconto-avenger" target="_blank"><?php _e('Licença ilimitada - Preço normal R$297, mas você paga somente R$197')?> <strong><?php _e('CLIQUE AQUI')?></strong></a></p>

      <p><a href="https://wpavenger.com" target="_blank"><?php _e('Saiba mais sobre o tema wordpress WP Avenger')?><strong> <?php _e('CLIQUE AQUI')?></strong></a></p>
</br>
	<p style="font-size: 18px;"><?php _e('Plugin wordpress Super links') ?></p>
      <p><?php _e('Clone qualquer página de vendas em menos de 2 segundos de forma simples, rápida e fácil') ?></p>

      <p><?php _e('Ter a sua estrutura própria NUNCA foi tão FÁCIL! Com apenas 1 click você pode ter qualquer página do WordPress clonada') ?></p>
      </br>
      <a href="https://wpsuperlinks.top" target="_blank"><?php _e('Conhece o plugin super links') ?></a></br>
      <a href="https://wpsuperlinks.top/desconto-super-links" target="_blank"><?php _e('Desconto de cliente') ?></a>


  </div>

<?php
}
//-------terms and conditions set or not--
function isSetTandC()
{$pref='WP-RGPD-Compliance-';
	if(get_option($pref.'tandc-bef')!='0')
		
		{
			return 1;
		}
		else
		{
			return 0;
		}
}
//-------privacy policy set or not--
function isSetPP()
{$pref='WP-RGPD-Compliance-';
	if(get_option($pref.'pp-bef')!='0')
		
		{
			return 1;
		}
		else
		{
			return 0;
		}
}
//--------did the user form published or not----------
function isUserFormPublished()
{
	$count=0;
	$pref='WP-RGPD-Compliance-';
$my_wp_query = new WP_Query();
$all_wp_pages = $my_wp_query->query(array('post_type' =>get_post_types('', 'names')));
$all_children = get_page_children( get_the_ID(), $all_wp_pages );
foreach($all_children as $child)
{
	if(has_shortcode( $child->post_content, 'RGPD_UserRequestForm'))
	{
		
		$count=1;
		break;
	}
}
return $count;
}
function complianceStatus()
{//compliance status
$pref='WP-RGPD-Compliance-';
	$count=0;
 if(strlen(get_option($pref.'notice'))>0){$count++;}
 if(isSetTandC()==1){$count++;}
 if(isSetPP()==1){$count++;}
 if(strlen(get_option($pref.'rtbf-message'))>0){$count++;}
 if(strlen(get_option($pref.'da-message'))>0){$count++;}
 if(strlen(get_option($pref.'dbr-message'))>0){$count++;}
 if(strlen(get_option($pref.'drr-message'))>0){$count++;}
 //if(get_option($pref.'eu-active')=='0'){$count++;}
 return $count;
}
//-----------EU country or not---------

function rgpdt_curl_link_read_get_data($url) 
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}



function euCountryOrNot() 
{
 
    require_once('geoplugin.class.php');
    $geoplugin = new geoPlugin();
    $geoplugin->locate();
 
    //echo $geoplugin->countryCode;
 
        
    	

	$country_inicials = $geoplugin->countryCode;
	
	
	
	
	  $my_countrieseuro = array('BE','BG','CZ','DK','DE','EE','IE','EL','ES','FR','HR','IT','CY','LV','LT','LU','HU','MT','NL','AT','PL','PT','RO','SI','SK','FI','SE','UK','GB');
    if (in_array($country_inicials, $my_countrieseuro)) 
	
	{
    	return 1;
    	//return $country_inicials;
    }
    else
	{
	    //return $country_inicials;
    	return 0;
    }

	  
}


//----RGPD Visual Editor Text---------
function rgpdEditorText($type)
{


	if($type == 'pofp'){

	global $wpdb;

	$site = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	$empresa = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'" );
	$admin_email = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'admin_email'" );
	
	extract(shortcode_atts( array(
		'email' 	=> $admin_email,
		'empresa'	=> $empresa,
		'site'		=> $site,
		
	), $atts));		

	//carregar abaixo texto dos termos e condições
	//criar uma estilização da tabela utilizando o css

		$text = "<h2> {$empresa} " . __('POLÍTICAS DE PRIVACIDADE') . "</h2>";

		$text .= "<p>" . __('ULTIMA ATUALIZAÇÃO: 13 DE AGOSTO DE 2020') . "</p>";

		$text .= "<p>" . __('Esta política de privacidade (“Política”) descreve como') . " {$empresa} " . ('("Empresa", "nós" e "nosso") procede, recolhe, usa e partilha informação pessoal quando usa este website {$site} (O “Site”). Por favor leia a informação abaixo cuidadosamente para que possa entender as nossas práticas relativamente como lidamos com a sua informação pessoal e como tratamos essa informação.') . "</p>";

		$text .= "<ol>";

		$text .= "<li>" . __('FINALIDADE DO PROCESSAMENTO</br> 
	<p>O que são dados pessoais?</p> 
	<p>Nós recolhemos informação sobre si de várias formas, incluindo dados pessoais. Como descrito nesta Política “dados pessoais” conforme é definido no regulamento geral de proteção de dados, incluí qualquer informação, que combinada com mais dados, ou não que recolhemos sobre você identifica você como um indivíduo, incluindo por exemplo o seu nome, código postal, e-mail e telefone.</p> 
	<p>Porquê que precisamos desta informação pessoal?</p>
	<p>Somente processaremos os seus dados pessoais de acordo com as leis de proteção de dados e privacidade aplicáveis. Precisamos de certos dados pessoais para fornecer-lhe acesso ao site. Se você se registrou conosco, terá sido solicitado que você assinala para concordar em fornecer essas informações para acessar aos nossos serviços, como comprar os nossos produtos ou visualizar o nosso conteúdo. Este consentimento nos fornece a base legal que exigimos sob a lei aplicável para processar os seus dados. Você mantém o direito de retirar tal consentimento a qualquer momento. Se você não concordar com o uso dos seus dados pessoais de acordo com esta Política, por favor, não use o nosso website.</p>') . "</li>";

		$text .= "<li>" . __('RECOLHENDO OS SEUS DADOS PESSOAIS</br>
	<p>Nós recolhemos informações sobre das seguintes formas: Informações que você nos dá, inclui:</p> 
	• Os dados pessoais que você fornece quando se registra para usar o nosso website, incluindo seu nome, morada, e-mail, número de telefone, nome de usuário, senha e informações demográficas;</br>
	• Os dados pessoais que podem estar contidos em qualquer comentário ou outra publicação que você no nosso website;</br>
	• Os dados pessoais que você fornece no nosso programa de afiliados ou em outras promoções que corremos no nosso website;</br>
	• Os dados pessoais que você fornece quando reporta um problema no nosso website ou quando necessita de suporte ao cliente;</br> 
	• Os dados pessoais que você fornece quando faz compras no nosso website;</br>
	• Os dados pessoais que você fornece quando nos contata por telefone, e-mail ou de outra forma.</br> 
	
	<p>Informações que recolhemos automaticamente. registramos automaticamente informações sobre si e o seu computador, ou dispositivo móvel quando você acessa o nosso website. Por exemplo, ao visitar o nosso website, registramos o nome e a versão do seu computador, ou dispositivo móvel, o fabricante e o modelo, o tipo de navegador, o idioma do navegador, a resolução do monitor, o website visitado antes de entrar no nosso website, as páginas visualizadas e por quanto tempo você esteve em uma página, tempos de acesso e informações sobre o seu uso e ações no nosso website. Recolhemos informações sobre si usando cookies. </p>') . "</li>";

		$text .= "<li>" . __('COOKIES</br>
	
	<p>O que são cookies?</p>
	<p>Podemos recolher informação sua usando "cookies". Cookies são pequenos arquivos de dados armazenados no disco rígido do seu computador, ou dispositivo móvel no seu browser. Podemos usar tanto cookies (que expiram depois de fechar o browser) como cookies sem data de expiração ( que ficam no seu computador, ou dispositivo móvel até que você os apague) para fornecer-lhe uma experiência mais pessoal e interativa no nosso website. 
	Usamos dois tipos de cookies: Primeiramente cookies inseridos por nós no seu computador, ou dispositivo móvel, que nós utilizamos para reconhecer quando você voltar a visitar o nosso website; e cookies de terceiros que são de serviços prestados por terceiros no nosso website, e que podem ser usados para reconhecer quando o seu computador, ou dispositivo móvel visita o nosso e outros websites.</p>') . "</li>";
		
		$text .= "<h3>" . __('Cookies que utilizamos') . "</h3>";

		$text .= "<p>" . __('O nosso website utiliza os seguintes cookies descritos abaixo:') . "</p>";

		/* inicio. da tabela */

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<h4>" . __('Tipo de cookies e o seu propósito') . "</h4>";

		$text .= "</div>";

		$text .= "</div>";

		/*nova linha*/

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" . __('Cookies essenciais') . "</p>";

		$text .= "</div>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" .  __('Estes cookies são necessários para fornecer os serviços disponíveis no nosso website, para que você seja capaz de utilizar algumas das suas funcionalidades. Por exemplo, poderão permitir que você faça login na área de membro, ou que carregue o conteúdo do nosso website rapidamente. Sem estes cookies muitos dos serviços disponíveis no nosso website poderão não funcionar corretamente, e só usamos estes cookies para providenciar-lhe um bom serviço.') . "</p>";

		$text .= "</div>";

		$text .= "</div>";

		/*nova linha*/

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" . __('Cookies de funções') . "</p>";

		$text .= "</div>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" .  __('Este cookie permite recordar as escolhas que você já fez no nosso website, como por exemplo a língua, detalhes de login, assim como outras escolhas que fez que possam ser personalizadas por si. O propósito destes cookies é relembrar todas as escolhas que você fez de formar a criar uma experiência mais personalizada sem ter que inserir novamente os seus dados no nosso website.') . "</p>";

		$text .= "</div>";

		$text .= "</div>";


		/*nova linha*/

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" . __('Cookies de análise e performance') . "</p>";

		$text .= "</div>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" .  __('Estes cookies servem para recolher a informações sobre o tráfego do nosso website e como os utilizadores utilização e navegam no website. A informação recolhida não identifica em particular nenhum utilizador. Inclui o número de visitantes no nosso website, os websites que indicam o nosso website, as páginas que os utilizadores visitam, os possíveis websites que eles visitaram anteriormente e outras informações similares. Nós utilizamos estas informações para melhorar e monitorar a actividade do nosso website. </br>
Nós utilizamos o Google Analytics com este propósito. Google Analytics utiliza os seus próprio cookies. É somente utilizado para melhorar a forma como o nosso website funciona. Poderá saber mais informação sobre os cookies do Google Analytics aqui: https://developers.google.com/ analytics/resources/concepts/gaConceptsCookies 
Saiba mais como o Google protege os seus dados aqui:  
http://www.google.com/analytics/learn/privacy.html </br>
Você pode evitar o uso deste cookie do Google Analytics no seu browser ao instalar uma extensão no Google Chrome, poderá baixar esta extensão aqui:  http://tools.google.com/dlpage/gaoptout?hl=en-GB 
    ') . "</p>";

		$text .= "</div>";

		$text .= "</div>";

		/*nova linha*/

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" . __('Cookies de publicidade') . "</p>";

		$text .= "</div>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" .  __('Estes cookies analisam a forma como navega para podermos mostrar-lhe anúncios que provavelmente serão do seu interesse. Este cookies utilizam a informação do seu histórico de navegação que com a sua permissão mostra-lhe anúncios relevantes de terceiros, baseado nos seus interesses. 
Você pode desabilitar cookies que guardam o histórico da sua navegação, visitando o website http:// www.youronlinechoices.com/uk/your-ad-choices . Se escolher remover este tipo de cookies, você verá na mesma os anúncios, mas não serão relevantes segundo os seus interesses.') . "</p>";

		$text .= "</div>";

		$text .= "</div>";


		/*nova linha*/

		$text .= "<div style='display: inline;'>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" . __('Cookies de redes sociais') . "</p>";

		$text .= "</div>";

		$text .= "<div style='padding:5px;'>";

		$text .= "<p>" .  __('Estes cookies são utilizados quando você partilha informação nas redes sociais, ou de alguma forma tem acesso aos nossos conteúdos através das redes sociais como o Facebook, Twitter, ou Google+ .') . "</p>";

		$text .= "</div>";

		$text .= "</div>";

		/* fim. da tabela */

		$text .= "<h4>" . __('Desabilitando os cookies') . "</h4>";

		$text .= "<p>" . __('Você pode remover, ou rejeitar cookies através das configurações do browser. Para fazer isso recomendamos que siga as instruções dos seus browsers (normalmente pode encontrar estas informações nas “configurações” do seu browser em “ajuda”, ou 
	“ferramentas”). Maior parte dos browsers aceitam os cookies automaticamente até você alterar as configurações do mesmo.') . "</p>";

		$text .= "<p>" . __('Se não aceitar os nossos cookies, a sua experiência no nosso site não será tão agradável. Por exemplo nós poderemos não reconhecer o seu computador, ou dispositivo móvel e você poderá ter que fazer login sempre que entrar no nosso website.') . "</p>";

		$text .= "<h4>" . __('PUBLICIDADE') . "</h4>";


		$text .= "<p>" . __('Nós poderemos utilizar terceiros para apresentar anúncios quando visita o nosso website. Estas empresas poderão recolher informações como, tipo de browser, hora e dia, tipo de anúncio foi clicado, neste e outros websites de forma a mostrar os anúncios mais relevantes a você. Estas empresas normalmente utilizam o seu sistema para recolher estes dados, que estão sujeitos às suas políticas de privacidade.') . "</p>";

		$text .= "<h4>" . __('USANDO OS SEUS DADOS PESSOAIS') . "</h4>";

		$text .= "<p>" . __('Nós poderemos utilizar os seus dados pessoais da seguinte forma:') . "</p>";

		$text .= "<p>" . __('• para manter e melhorar o nosso website, produtos e serviços;</br>
	• para gerir a sua conta, incluindo comunicações que temos consigo relativamente à sua conta, se tiver uma conta no nosso website:</br>
	• para operar e administrar o nosso programa de afiliados e outras promoções que você poderá participar no nosso website;</br>
	• para responder aos seus comentários e perguntas e para prestar apoio ao cliente;</br>
	• para enviar informações, incluindo informação técnica, actualizações, alertas de segurança e suporte;</br>
	• com o seu consentimento, fazemos e-mail marketing sobre promoções, e outras novidades, incluindo informação sobre os nossos produtos, ou serviços oferecidos por nós, ou pelos nossos afiliados. Você poderá deixar de receber estas informações a qualquer momento, em todos os nossos e-mails tem sempre uma opção de sair da lista. Mesmo que saia da nossa lista poderemos enviar e-mails não relacionados com marketing, incluem e-mails sobre alguma conta que tenha conosco (se tiver uma), ou negócios que tenha conosco;</br>
	• para processar pagamentos que tenha realizado no nosso website;</br>
	• quando acharmos necessário e apropriado (a) para cumprir com a lei (b) para cumprir com pedidos e processo legais, incluindo pedidos de autoridades públicas e governamentais; (c) para cumprir a nossa Política; e (d) para proteger os direitos, privacidade, segurança, seus e de outros.</br>
	• para analisar e estudar serviços; </br>
	• como descrito abaixo em “Partilhar os seus dados pessoais”.') . "</p>";

		$text .= "<h4>" . __('PARTILHAR OS SEUS DADOS PESSOAIS') . "</h4>";

		$text .= "<p>" . __('Podemos partilhar os seus dados pessoais das seguintes formas:') . "</p>";

		$text .= "<p>" . __('• A terceiros designados por você. Podemos partilhar os seus dados com terceiros em que você tenha dado o seu consentimento.</br>
	• Serviços prestados por terceiros. Poderemos partilhar os seus dados pessoais com terceiros que realizam alguns serviços como (analise de dados, processamento de pagamentos, suporte ao cliente, envio de e-mail marketing e outros serviços similares).') . "</p>";

		$text .= "<h4>" . __('WEBSITE DE TERCEIROS') . "</h4>";

		$text .= "<p>" . __('O nosso website poderá conter links de terceiros. Esta Política não cobre as Políticas de privacidade de terceiros. Estes websites de terceiros tem as suas próprias políticas de privacidade e não aceitamos qualquer responsabilidade sobre esses websites, suas funções, ou políticas de privacidade. Por favor leia as políticas de privacidade destes websites de terceiros antes de submeter qualquer informação.') . "</p>";

		$text .= "<h4>" . __('CONTEÚDO GERADO PELO UTILIZADOR') . "</h4>";

		$text .= "<p>" . __('Poderá partilhar os seus dados pessoais conosco quando submete e gera conteúdo no nosso website, incluí comentários no blog, mensagens de suporte no nosso website. Por favor tenha noção que qualquer informação que você publique no nosso website torna-se de conhecimento público e ficará acessível a todos os usuários do nosso website incluindo visitantes. Sugerimos que tenha muito cuidado quando decidir tornar público os seus dados pessoais, ou qualquer outra informação no nosso website. Qualquer informação pessoal publicada no nosso website não ficará privada ou confidencial.') . "</p>";

		$text .= "<p>" . __('Se você nos der alguma review, ou comentário, nós poderemos tornar públicas essas informações no nosso website.') . "</p>";

		$text .= "<h4>" . __('TRANSFERÊNCIA DE DADOS INTERNACIONAL') . "</h4>";

		$text .= "<p>" . __('As suas informações incluindo dados pessoais que recolhemos de você, poderão ser transferidos para, guardado em, e processado por nós fora do país onde você reside, onde proteção de dados e regulamentos de privacidade poderão não ter o mesmo nível de proteção como em outros países. Ao aceitar esta política de privacidade você concorda em transferir, guardar e processar os seus dados. Nós iremos tomar todas as medidas necessárias para assegurar que os seus dados são tratados da forma mais segura e de acordo com as nossas políticas.') . "</p>";

		$text .= "<h4>" . __('SEGURANÇA') . "</h4>";

		$text .= "<p>" . __('Procuramos tomar sempre todas as medidas, técnicas e administrativas para proteger todos os seus dados da forma mais segura possível. Infelizmente nenhum sistema é 100% seguro e poderá garantir completamente a segurança dos seus dados. Se você pensa que os seus dados já não estão seguros conosco (por exemplo o acesso à sua conta foi comprometido), por favor entre em contato conosco imediatamente e relate-nos o seu problema.') . "</p>";

		$text .= "<h4>" . __('RETENÇÃO') . "</h4>";

		$text .= "<p>" . __('Nós apenas guardaremos a sua informação pessoal durante 30 dias a não ser que um período mais longo seja necessário, ou permitido por lei.</br>
	OU</br> 
	Nós apenas guardaremos a sua informação pessoal enquanto for necessário e permitido por você para que você possa utilizar o nosso website até que você feche a sua conta ou termine a sua subscrição, a não ser que o período mais longo seja necessário, ou permitido por lei.') . "</p>";

		$text .= "<h4>" . __('NOSSA POLÍTICA COM CRIANÇAS') . "</h4>";

		$text .= "<p>" . __('O nosso website não é direcionado para crianças abaixo dos 18 anos. Se um pai, ou um encarregado de educação verificar que o seu, ou a sua filha forneceu dados pessoais no nosso website sem o seu consentimento, deverá contatar-nos imediatamente. Nós iremos apagar todos esses dados o mais rápido possível.') . "</p>";

		$text .= "<h4>" . __('OS SEUS DIREITOS') . "</h4>";

		$text .= "<p>" . __('• <b>Sair da lista.</b> Você pode contactar-nos a qualquer momento para sair da nossa lista; (i) comunicações de e-mail marketing (ii) a nossa recolha de dados pessoais sensíveis (iii) qualquer novo processamento de dados pessoais que poderemos realizar.</br>
	• <b>Acesso.</b> Você poderá ter acesso às informações que nós possuímos de você a qualquer momento ao contatar-nos.</br> 
	• <b>Alterar.</b> Você também poderá contatar-nos para atualizar ou corrigir qualquer informação pessoal que tenhamos sua.</br>
	• <b>Mover.</b> A sua informação pessoal pode ser transferida. Você tem a flexibilidade de mover os seus dados para outro serviço se assim desejar.</br>
	• <b>Apagar.</b> Em algumas situações, por exemplo quando a informação que temos sobre si já não é relevante, ou é incorreta, você poderá pedir para apagarmos os seus dados.') . "</p>";

		$text .= "<p>" . __('Se você quiser exercer qualquer um deste direitos por favor contate-nos. Por favor deixe bem claro no seu pedido (i) que informação quer; e (ii) quais dos direitos acima você quer usar. Para a sua proteção apenas poderemos completar os seus pedidos, se estes forem feitos com o mesmo e-mail associado aos seus dados, iremos verificar a identidade antes de efetuar qualquer alteração. Nós iremos atender ao seu pedido o mais breve possível, não mais que 30 dias. Por favor tenha atenção que eventualmente poderemos ter que guardar alguma informação para mantermos em registro.') . "</p>";

		$text .= "<h4>" . __('QUEIXAS') . "</h4>";

		$text .= "<p>" . __('Nós estamos empenhados em resolver qualquer queixa sobre a forma como recolhemos os seus dados pessoais. Se tiver alguma queixa que queira fazer sobre a nossa política de privacidade, ou as nossas praticas relacionadas com os seus dados pessoais, por favor contate-nos em:') .  

		"<a href='mailto: {$email}'> {$email} </a>" . 

		__('Nós iremos responder ao seu contato o mais rápido possível, no máximo de 30 dias. Nós esperamos resolver qualquer situação que seja trazida até nós por você, em todo o caso se verificarmos que a sua queixa não é aplicável, você está no seu direito de contatar a autoridade local de proteção de dados.') . "</p>";

		$text .= "<h4>" . __('INFORMAÇÃO DE CONTATO') . "</h4>";

		$text .= "<p>" . __('Nós agradecemos os seus comentários e questões que tenha sobre a nossa política de privacidade. Poderá contatar-nos por e-mail em ') . "<a href='mailto:{$email}'>{$email}</a></p>";


//linha843
	}

	if ($type == 'tandc') {


	global $wpdb;

	$site = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
	$empresa = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'blogname'" );
	$admin_email = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'admin_email'" );
	
	extract(shortcode_atts( array(
		'email' 	=> $admin_email,
		'empresa'	=> $empresa,
		'site'		=> $site,
		
	), $atts));

		
		$text = "<h2> {$empresa} ".__('TERMOS E CONDIÇÕES')."</h2>"; 

		$text .= "<p>".__('ULTIMA ATUALIZAÇÃO: 13 DE AGOSTO DE 2020')."</p>"; 

		$text .= "<p>".__('Os termos e condições (“Termos”) descrevem como  ') . "{$empresa}" . __(' ("Empresa," "nós", e "nosso") regula o uso deste website ') . "{$site}" . __(' (o "website").</br>Por favor leia as informações a seguir com cuidado de forma a entender as nossas praticas referentes ao uso do website. A Empresa poderá alterar os Termos a qualquer altura. A Empresa poderá informa-lo da alteração dos Termos utilizando os meios de comunicação disponíveis. A Empresa recomenda que verifique o website com frequência de forma a que veja a versão atual dos Termos e as versões anteriores.')."</p>";

		$text .= "<ol>";

		$text .= "<li>".__('POLÍTICAS DE PRIVACIDADE') . "</br>" . __('
	A nossa política de privacidade encontra-se disponível em outra página. A nossa política de privacidade explica-lhe como nós utilizamos os seus dados pessoais. Ao utilizar o nosso website você reconhece que tem conhecimento e aceitas as nossas políticas de privacidade e da forma como processamos os seus dados.')."</li>";

		$text .= "<li>".__('A SUA CONTA</br> 
	Quando usa o nosso website, você fica responsável por assegurar a confidencialidade da sua conta, senha e outros dados. Não poderá passar a sua conta a terceiros. Nós não nos responsabilizamos por acessos não autorizados que resultem de negligencia por parte do utilizador (dono da conta). A empresa está no direito de terminar o serviço, ou cancelar a sua conta e remover os seus dados, caso você partilhe a sua conta.')."</li>";

		$text .= "<li>".__('SERVIÇOS</br> 
	O website permite que você use os serviços disponíveis no website. Não poderá utilizar esses serviços com propósitos ilegais. 
	Nós poderemos em alguns casos, estipular um valor para poder utilizar o website. Todos os preços serão publicados separadamente nas páginas apropriadas no website. Poderemos em alguns casos, e a qualquer momento mudar os valores para poder aceder. 
	Poderemos também utilizar sistemas de processamento de pagamentos que terão taxas de processamento de pagamentos. Algumas dessas taxas poderão ser apresentadas quando você escolher um determinado meio de pagamento. Todos os detalhes sobre as taxas desses sistema de pagamentos poderão ser encontrados no seus respectivos websites.')."</li>";

		$text .= "<li>".__('SERVIÇOS DE TERCEIROS</br> 
	O website poderá incluir links para outros websites, aplicações ou plataformas. 
	Nós não controlamos os websites de terceiros, e não seremos responsáveis pelos conteúdos e outro tipo de materiais incluídos nesses websites. Nós deixamos esses disponíveis para você e mantemos todos os nossos serviços e funcionalidades no nosso website.')."</li>";

		$text .= "<li>".__('USOS PROIBIDOS E PROPRIEDADE INTELECTUAL</br> 
	Nós concedemos a você uma licença revogável, intransferível e não exclusiva para aceder e usar o nosso website de um dispositivo de acordo com os Termos. 
	Você não deve usar o website para fins ilegais, ou proibidos. Você não pode usar o website de forma a que possa desabilitar, danificar ou interferir no website.</br> 
	Todo o conteúdo presente no nosso website incluindo texto, código, gráficos, logos, imagens, vídeos, software utilizados no website (doravante e aqui anteriormente o "Conteúdo"). O conteúdo é propriedade da empresa, ou dos seus contratados e protegidos por lei (propriedade intelectual) que protegem esses direitos. 
	Você não pode publicar, partilhar, modificar, fazer engenharia reversa, participar da transferência ou criar e vender trabalhos derivados, ou de qualquer forma usar qualquer um dos Conteúdos. </br>A sua utilização do website não lhe dá o direito de fazer qualquer uso ilegal e não permitido do Conteúdo e, em particular, você não poderá alterar os direitos de propriedade ou avisos no Conteúdo. Você deverá usar o Conteúdo apenas para seu uso pessoal e não comercial. A Empresa não concede a você nenhuma licença para propriedade intelectual dos seus conteúdos.')."</li>";

		$text .= "<li>".__('MATERIAIS DA EMPRESA</br> 
	Ao publicar, enviar, submeter, ou efetuar upload do seu Conteúdo, você está a ceder os direitos do uso desse Conteúdo a nós para o desenvolvimento do nosso negócio, incluindo, mas não limitado a, os direitos de transmissão, exibição pública, distribuição, execução pública, cópia, reprodução e tradução do seu Conteúdo; e publicação do seu nome em conexão com o seu Conteúdo.</br> 
	Nenhuma compensação será paga com relação ao uso do seu Conteúdo. A Empresa não terá obrigação de publicar ou desfrutar de qualquer Conteúdo que você possa nos enviar e poderá remover seu Conteúdo a qualquer momento sem qualquer aviso. 
	Ao publicar, fazer upload, inserir, fornecer ou enviar o seu Conteúdo, você garante e declara que possui todos os direitos sobre seu Conteúdo.')."</li>";

		$text .= "<li>".__('ISENÇÃO DE CERTAS RESPONSABILIDADES</br> 
	As informações disponíveis através do website podem incluir erros tipográficos ou imprecisões. A Empresa não será responsável por essas imprecisões e erros. 
	A Empresa não faz declarações sobre a disponibilidade, precisão, confiabilidade, adequação e atualidade do Conteúdo contido e dos serviços disponíveis no website. Na medida máxima permitida pela lei aplicável, todos os Conteúdos e serviços são fornecidos "no estado em que se encontram". A Empresa se isenta de todas as garantias e condições relativas a este Conteúdo e serviços, incluindo garantias e provisões de comercialização, adequação a um determinado propósito.')."</li>";

		$text .= "<li>".__('INDENIZAÇÃO</br> 
	Você concorda em indemnizar, defender e isentar a Companhia, seus gerentes, diretores, funcionários, agentes e terceiros, por quaisquer custos, perdas, despesas (incluindo honorários de advogados), responsabilidades relativas, ou decorrentes de sua fruição ou incapacidade para aproveitar o website, ou os seus serviços e produtos da Empresa, a sua violação dos Termos, ou a sua violação de quaisquer direitos de terceiros, ou a sua violação da lei aplicável. Você deve cooperar com a Empresa na afirmação de quaisquer defesas disponíveis.')."</li>";

		$text .= "<li>".__('CANCELAMENTO E RESTRIÇÃO DE ACESSO</br> 
	A Empresa pode cancelar ou bloquear o seu acesso ou conta no website e os seus respectivos serviços, a qualquer altura, sem aviso, no caso de você violar os Termos e condições.')."</li>";

		$text .= "<li>".__('DIVERSOS</br> 
	A lei que rege os Termos deve ser as leis substantivas do país onde a Empresa está estabelecida, exceto as regras de conflito de leis. Você não deve usar o Website em jurisdições que não dêem efeito a todas as disposições dos Termos.</br> 
	Nenhuma parceria, emprego ou relacionamento de agência estará implícito entre você e a Empresa como resultado dos Termos ou uso do Website. 
	Nada nos Termos deverá ser uma derrogação ao direito da Empresa de cumprir com solicitações ou requisitos governamentais, judiciais, policiais e policiais ou requisitos relacionados ao seu usufruto do Website.</br> 
	Se qualquer parte dos Termos for considerada inválida ou inexequível de acordo com a lei aplicável, as cláusulas inválidas ou inexequíveis serão consideradas substituídas por cláusulas válidas e exequíveis que deverão ser semelhantes à versão original dos Termos e outras partes e seções do Contrato. Termos serão aplicáveis a você e à Empresa.</br> 
	Os Termos constituem o acordo integral entre você e a Empresa em relação ao desfrute do Website e os Termos substituem todos os anteriores ou comunicações e ofertas, sejam eletrônicas, orais ou escritas, entre você e a Empresa.</br> 
	A Empresa e suas afiliadas não serão responsáveis por uma falha ou atraso no cumprimento de suas obrigações quando a falha ou atraso resultar de qualquer causa além do controle razoável da Empresa, incluindo falhas técnicas, desastres naturais, bloqueios, embargos, revoltas, atos, regulamentos, legislação. ou ordens de governo, atos terroristas, guerra ou qualquer outra força fora do controle da Empresa.</br> 
	Em caso de controvérsias, demandas, reclamações, disputas ou causas de ação entre a Empresa e você em relação ao Website ou outros assuntos relacionados, ou aos Termos, você e a Empresa concordam em tentar resolver tais controvérsias, demandas, reclamações, disputas , ou causas de ação por negociação de boa-fé, e em caso de falha de tal negociação, exclusivamente através dos tribunais do país onde a Companhia está estabelecida.')."</li>";

		$text .= "<li>".__('RECLAMAÇÕES</br> 
	Estamos empenhados em resolver quaisquer reclamações sobre a forma como recolhemos ou usamos os seus dados pessoais. Se você gostaria de fazer uma reclamação sobre estes Termos ou nossas práticas em relação aos seus dados pessoais, entre em contato conosco em: ') .  "<a href='mailto:{$email}'>{$email}</a></br>." . __(' Responderemos à sua reclamação assim que pudermos e, em qualquer caso, dentro de 30 dias. </br>Esperamos resolver qualquer reclamação que seja levada ao nosso conhecimento, no entanto, se você achar que a sua reclamação não foi adequadamente resolvida, você se reserva no direito de entrar em contato com a autoridade supervisora de proteção de dados local.')."</li>";

		$text .= "<li>".__('INFORMAÇÃO DE CONTATO</br> 
	Agradecemos os seus comentários ou perguntas sobre estes Termos. Você pode nos contatar por escrito em ') . "<a href='mailto:{$email}'>{$email}</a>" . "</li>";

		$text .= "</ol>";



	}






	if($type=='cookie')
	{
		$text= "".__('<b>Importante:</b>','rgpdpro')." 
".__('Este site faz uso de cookies que podem conter informações de rastreamento sobre os visitantes.','rgpdpro')."
";
	}

	if($type=='rightforget')
	{
		$text="".__('Hello,','rgpdpro')."<br>

".__('Please refer to your request to forget your data on our blog.','rgpdpro')." <br>

".__('This is to report that action has been taken and your data has been forgotten as requested.','rgpdpro')."<br>

".__('If you have any queries, please let us know.','rgpdpro')."<br>

".__('Thanks','rgpdpro')."
";
	}
	if($type=='dataaccess')
	{
		$text="".__('Hello,','rgpdpro')."<br>

".__('Please refer to your request to report all the data that we have about you.','rgpdpro')."<br>

".__('Enclosed please find all the data that we have on you.','rgpdpro')." <br>

".__('If you need any more information, please let us know.','rgpdpro')."<br>

".__('Thanks','rgpdpro')."
";
	}
	if($type=='databreach')
	{
		$text="".__('Hello,','rgpdpro')."<br>

".__('We regret to inform you that there was a data breach on our site and your information or passwords may be compromised.','rgpdpro')."<br>

".__('We request you to change your password on our site asap, and also any other site where you may have used the same password.','rgpdpro')."<br>

".__('Please also review your information with us and ensure that you are adequately protected against any misuse.','rgpdpro')."<br>

".__('We are working to bridge the breach and you’ll be notified of further developments if any.','rgpdpro')."<br>

".__('Thanks','rgpdpro')."
";
	}
	if($type=='datarectification')
	{
		
		$text="".__('Hello,','rgpdpro')."<br>

".__('Please refer to your data rectification request with us. We are pleased to inform you that action was taken on your request and your data has been rectified.','rgpdpro')."<br>

".__('Please let us know if you need any other help.','rgpdpro')."<br>

".__('Thanks','rgpdpro')."
";
	}
	return $text;
}
//Store record for terms and condition and privacy policy accept
function wprgpd_store_consent_for_tandc_and_pp($type)
{
	global $wpdb;
	 $table=$wpdb->prefix."rgpd_request_records";
	 $in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('','','','',$type,$_SERVER['REMOTE_ADDR'],'1','',date('d-M-Y h:iA'),'','')));
}


//function ip unlocked or not
function rgpd_ip_unlocked_or_not()
{
	$ip=$_SERVER['REMOTE_ADDR'];
	$pref='WP-RGPD-Compliance-';
	$euipseparator='@euip@';
	if(get_option($pref.'unblock-ip'))
	{
	$unblockarr=explode($euipseparator,get_option($pref.'unblock-ip'));
	if(in_array($ip,$unblockarr))
	{
		return 1;
	}
	else
	{
		return 0;
	}
	}
	else
	{
		return 0;
	}
}


?>