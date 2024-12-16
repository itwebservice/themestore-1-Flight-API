
<?php
include_once('../../model/model.php');

global $theme_color, $theme_color_dark, $theme_color_2, $topbar_color, $sidebar_color;

?>

<div class=" google_translation_section" style="text-align:left;display: inline-block; vertical-align: middle; margin: 0px 0;">
    <div id="google_translate_element" style="margin-top: 2px !important; display: inline-block; vertical-align: middle;"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en',
            // includedLanguages: 'en,bho'
            // includedLanguages: 'af,sq,am,ar,hy,az,eu,be,bn,bho,bs,bg,ca,ceb,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,he,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,rw,ko,ku,ky,lo,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ny,or,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,tt,te,th,tr,tk,uk,ur,ug,uz,vi,cy,xh,yi,yo,zu'
    includedLanguages: 'af,sq,am,ar,hy,az,eu,be,bn,bho,bs,bg,ca,ceb,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,he,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,rw,ko,ku,ky,lo,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ny,or,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,tt,te,th,tr,tk,uk,ur,ug,uz,vi,cy,xh,yi,yo,zu'
   
        }, 'google_translate_element');
        }


        
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <!--<i class="bi bi-envelope emain_icon"></i> <a href="mailto:inquiries@evisa-indonesia.online" style="color:#fff;"><span class="__cf_email__" style="margin-left:20px;">inquiries@evisa-indonesia.online</span></a></span>-->
    <style>
        .main-header{padding-top:0;}
        #topbar {background:#aa0100;padding:5px;}
        .goog-te-combo option{background: <?= $theme_color ?>;}
        .main-header .goog-te-gadget .goog-te-combo{padding: 0px 5px;border:1px solid #009898; margin: 1px; background-color: #009898; color: #fff; font-size: 11px;}
        a.chat-cus i {font-size: 45px;}
        a.chat-cus {color: #fff;}
        a.chat-cus:hover{color:#009898;}
        .goog-te-gadget {font-size:1px; line-height:0px; color:rgb(0, 59, 84);}
        .silder_hold .skiptranslate.goog-te-gadget {font-size: 0;}
        .goog-te-gadget .goog-te-combo {
            margin: 0; 
            /* background: #009898; */
            border: 1px solid <?= $theme_color ?>;
            padding: 12px 10px;
            color: <?= $theme_color ?>;
            font-size: 12px;
            line-height:13px;
            border-radius: 5px;
            -moz-appearance: none;
            appearance: none;
            -webkit-appearance: none;
            /* background: #009898 url('https://w7.pngwing.com/pngs/323/873/png-transparent-arrow-computer-icons-down-arrow-angle-hand-logo-thumbnail.png') no-repeat center right / 15px; */
            background: #fff url('https://w7.pngwing.com/pngs/323/873/png-transparent-arrow-computer-icons-down-arrow-angle-hand-logo-thumbnail.png') no-repeat center right / 15px; */
            background-origin: padding-box;
            padding-right: 6px;
            background-origin: content-box;
        }

        
        .goog-te-gadget .goog-te-combo:focus{
            outline: 0;
            outline: 0 auto -webkit-focus-ring-color;
            outline-offset: 0;
        }


     /* Change styles on hover */
     .goog-te-gadget .goog-te-combo:hover {
        background-color: <?= $theme_color ?>;
        color: #fff; /* Text color changes to white */
        border: 1px solid <?= $theme_color_dark ?>;
    }

   

        .goog-te-banner-frame {display: none !important;}
        .goog-logo-link {display: none;}
        @media only screen and (max-width: 600px) {
            .main-header .goog-te-gadget .goog-te-combo {
                width: 110px;
            /*background: transparent;*/
            /*color: #fff;*/
            }
        }
        .VIpgJd-ZVi9od-l4eHX-hSRGPd:link, .VIpgJd-ZVi9od-l4eHX-hSRGPd:visited, .VIpgJd-ZVi9od-l4eHX-hSRGPd:hover, .VIpgJd-ZVi9od-l4eHX-hSRGPd:active {
            font-size: 12px;
            font-weight: bold;
            color: #444;
            text-decoration: none;
            display: none;
        }
        #topbar {
            background: #0042b3;
            padding: 5px;
        }
        .nav-bar ul li.active a {
            color: #aa0100;
            border-bottom: 2px solid #0042b3;
        }
        .nav-bar ul li.active a {
            color: #0042b3;
            border-bottom: 2px solid #0042b3;
        }
        .main-footer {
            background-color: #cf0a21;
        }
        .banner .owl-carousel .owl-nav button.owl-prev, .banner .owl-carousel .owl-nav button.owl-next {
            background-color: #cf0a21;
        }
        .btn-theme {
            background-color: #cf0a21;
            border: 1px solid #fff;
        }
        .icons{
            margin:0 auto;
        }
        .cov-a{
            text-align:center;
        }
        .main-header {
            padding-top: 0;
            background-image: linear-gradient(20deg, #009898, white);
        }
        
  
  .skiptranslate{
    /* visibility: collapse !important; */
    /* display: none !important; */
  }
  .VIpgJd-ZVi9od-ORHb-OEVmcd{
    z-index: -1 !important;
  }
        @media (min-width: 1200px) {
    .col-lg-4 {
        width: unset !important;
        float: unset !important;
    }
}
    </style>
</div>