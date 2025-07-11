
<style>
@import url('https://fonts.googleapis.com/css?family=Poppins|Handlee|Courgette|Lora|Nunito|Lato|Ubuntu|Raleway|Open+Sans|Arial');
</style>

<?php $rgpdpref='WP-RGPD-Compliance-'; ?>
<?php
if(isset($_POST['dontacceptrgpdcookie']))
{
  
  
  
  global $wpdb;
   $table=$wpdb->prefix."rgpd_request_records";
   $in=$wpdb->query($wpdb->prepare("insert into ".$table."(id,user,email,login, type,ip,value,updatedvalue,recorded,action, actiontime)values(%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",array('','','','','cookie',$_SERVER['REMOTE_ADDR'],'0','',date('d-M-Y h:iA'),'','')));
   echo "<script>window.location='".get_option($rgpdpref.'dont-accept-cookie-url')."'</script>";
   //echo "";
}
  

function activeadvcookies(){
  
  global $wpdb;
  $table = $wpdb->prefix . 'rgpd_adva_cookies';

  if ($result = $wpdb->query("SHOW TABLES LIKE '".$table."'")) {

    $results = $wpdb->get_results( "SELECT id FROM $table ");

    if($results != null){
        return $results[0];
    }
  }

}



?>
  <script type="text/javascript">
 
  function rgpdDisplayPopup()
  {
    //document.getElementByID("myModalrgpd").style.display = "block";
    alert("ok");
  }
  
  
  function idRgpdSetCookie(cname, cvalue, exdays)
  {//create js cookie
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

  function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
  
  function rgpdSetCookie()
  {//ajax and js cookie function call
    var set=2;
    
     var xhttp = new XMLHttpRequest();
       xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200){
    set=this.responseText;  
    }
    };
  
    xhttp.open("POST", "<?php echo plugins_url( 'update.php', __FILE__ ); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("rgpdset=1");
  
      
  setCookie('IDRGPD','created',3650);
  document.getElementById("myModalrgpd").style.display = "none";
  
  }
  </script>
  
          
                
                 <div id="myModalrgpd" class="wprgpd-modal-dialogg rgpdboxcolor" style="font-family:<?php echo get_option('WP-RGPD-Compliance-cookie-font') ?>;">
                

               <!--  <form action="" method="post"> -->
                    <font color=<?php echo get_option('WP-RGPD-Compliance-cookie-text-color') ?>>
                    <?php echo get_option('WP-RGPD-Compliance-notice') ?>        
                    </font>
        
         
                    
                <div style="display:inline-grid;float: right;">

                <button id="rgpdcookieaccept" name="rgpdcookieaccept" class="rgpdacceptbtn btn btn-primary pull-right" onclick="rgpdSetCookie()" style="float:right;margin-top:5px; margin-right:10px;" id="yes">
                <?php echo get_option($rgpdpref.'cookie-accept-button'); ?>
                </button>
                
         <?php $has_cookies = activeadvcookies(); ?>

         <?php if($has_cookies != NULL): ?>
         
                <button class="rgpdacceptbtn btn btn-primary pull-right" onclick="openRgdpBox()" style="float:right;margin-top:5px; margin-right:10px;">Personalizar cookies</button>

        <?php endif ?>
                </div>
         
         <?php
         if(get_option('WP-RGPD-Compliance-show-aff-rgpd') == 1){
         ?>
                <font size="1px">
                    
                <a href="<?php echo get_option('WP-RGPD-Compliance-link-aff-rgpd') ?>" target="_blank">
                    Criado por WP RGPD Pro
                </a>
                    
                </font>            
         <?php  
         }
         ?>

              
            </div>
         
            
 <style>
.wprgpd-modal-dialogg{
    
    <?php echo get_option($rgpdpref.'cookie-position'); ?>;
    
    position:fixed;
    
    padding:10px 20px 20px
    
}




</style>


<style>


.rgpdboxcolor{
    background-color:<?php echo get_option($rgpdpref.'cookie-bg-color'); ?>;
    opacity: 0.9;
  display: none;
}

.btn{
  background-color:<?php echo get_option($rgpdpref.'cookie-btn-color'); ?> !important;
  color: <?php echo get_option($rgpdpref.'cookie-btn-txt-color'); ?> !important;
}

.wprgpd-modal-dialogg{
    z-index:10000;
    
}

.wprgpd-modal-dialogg a {
    color: <?php echo get_option('WP-RGPD-Compliance-cookie-text-color') ?>;
}

.wprgpd-modal-dialogg a:hover {
    color: #2E9AFE;
}

.btn {
  margin:0 10px;
  line-height:20px;
  background:#45AE52;
  border:none;
  color: #ffffff;
  padding:2px 12px;
  border-radius: 3px;
  cursor: pointer;
  font-size: 12px;
  font-weight: bold;
}

</style> 

<?php $posi = get_option($rgpdpref.'cookie-position'); ?>
<?php //var_dump($posi); ?>
<?php if($posi == 'width:280px;bottom:140px;right:0px;border-radius:10px;margin:10px' || $posi == 'width:280px;bottom:140px;left:0px;border-radius:10px;margin:10px'): ?>

<style type="text/css">
@media screen and (max-width: 768px){
  .wprgpd-modal-dialogg{
    width: 180px;
    bottom: 140px;
  }
}
</style>

<?php endif ?>
<!--
  <style>
.wprgpd-modal-dialogg{
    
    <?php echo get_option($rgpdpref.'cookie-position'); ?>;
    margin:<?php echo get_option($rgpdpref.'cookie-distance'); ?>;
    position:fixed;
    
}
</style>


<style>
.rgpdboxcolor{
    background-color:<?php echo get_option($rgpdpref.'cookie-bg-color'); ?>;
    opacity: 0.9;
}

.wprgpd-modal-dialogg{
    z-index:10000;
    
}

.btn {
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  border-radius: 6px;
  font-family: Arial;
  color: #ffffff;
  font-size: 15px;
  background: #0B0B61;
  padding: 5px 10px 5px 10px;
  text-decoration: none;
}

/** UPDATE BARRA COOKIES **/

* {
    box-sizing: border-box;
}

/* Create three equal columns that floats next to each other */
.columntxt {
    float: left;
    width: 60%;
    padding: 10px;
    height: 85px; /* Should be removed. Only for demonstration */
}
.columnbtn {
    float: left;
    width: 20%;
    padding: 10px;
    height: 85px; /* Should be removed. Only for demonstration */
}

/* Clear floats after the columns */
.row:after {
    content: "";
    display: table;
    clear: both;
}


</style>
-->



<?php if(!empty(get_option($rgpdpref.'cookie-delay'))):?>
  
  <?php $timeDelay = get_option($rgpdpref.'cookie-delay'); ?>
  
<?php else: ?>
  
  <?php $timeDelay = 1; ?>
  
<?php endif ?>

<style><?php echo get_option($rgpdpref.'cookie-style'); ?></style>

<script type="text/javascript">


/*
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

*/


function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
        end = dc.length;
        }
    }
    // because unescape has been deprecated, replaced with decodeURI
    //return unescape(dc.substring(begin + prefix.length, end));
    return decodeURI(dc.substring(begin + prefix.length, end));
} 

     var rgpd_cookie = getCookie("IDRGPD");
  //   console.log(rgpd_cookie);

  setTimeout(function show_rgpd_box_java(){
     //setCookie('IDRGPD','s',365);//('IDRGPD','created',3650); check if cookie exist

        if(rgpd_cookie != 'created'){
            setCookie('IDRGPD','s',365);
   //         console.log('block')
            document.getElementById("myModalrgpd").style.display = "block";
        } else {
     //       console.log('none')
            document.getElementById("myModalrgpd").style.display = "none";
        }
     
  }, <?php echo $timeDelay . '000';?>);


</script>

