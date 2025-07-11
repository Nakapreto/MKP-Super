<?php

function rgpdSuperLinks()
{//Show box to user if its activated

    require_once("rgpdfunction.php");
    $pref='WP-RGPD-Compliance-';
    if(get_option($pref.'show')=='n')
    {

    }
    else if(get_option($pref.'show')=='y')
    {
        show_rgpd_boxSpl();

    }
    else if(get_option($pref.'show')=='h')
    {
        if(is_home()||is_front_page())
        {
            show_rgpd_boxSpl();

        }

    }
    else if(rmhttpurlandmatch(get_option($pref.'show'))==1)
    {
        show_rgpd_boxSpl();

    }

}

function show_rgpd_boxSpl(){
    $pref='WP-rgpd-Compliance-';

    if(get_option($pref.'cookie-eu')=='1') //mostrar so na UE
    {
        require_once("rgpdfunction.php");
        if(euCountryOrNot() == 1){
            $returns = euCountryOrNot();
            //var_dump($returns);

            require_once("box.php");
        } else {



            //echo "COUNTRY: ";
            $returns = euCountryOrNot();
            //var_dump($returns);


        }

    }
    else
    {
        require_once("box.php");
    }

}
?>