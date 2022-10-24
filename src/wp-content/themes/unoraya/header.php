<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=0,maximum-scale=1,user-scalable=no">
	<meta name="theme-color" content="#ff671b">
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url( get_template_directory_uri() . '/img/favicon.ico' ); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;400;700&family=Nunito:wght@200;300;400;600;700;800&display=swap" rel="stylesheet" async>
    <?php //wp_head(); ?>
    <!-- CSS only -->
    <?php $buildCss = esc_url( get_template_directory_uri() . '/css/build.min.css' ); ?>
    <link rel="stylesheet" href="<?php echo $buildCss; ?>" async defer>
    <?php $unorayaCss = esc_url( get_template_directory_uri() . '/css/unoraya.css' ); ?>
    <link rel="stylesheet" href="<?php echo $unorayaCss; ?>" async defer>
    <?php $buildJs = esc_url( get_template_directory_uri() . '/js/build.js' ); ?>
    <script src="<?php echo $buildJs; ?>"></script>

	<!-- Google Tag Manager  Pixel-->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-KNBPM36');</script>
	<!-- End Google Tag Manager -->

  <!-- Global site tag (gtag.js) - Google Ads: 862755000  GOOGLE AD GRANTS CONVOCATORIA PEA-->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-862755000"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-862755000');
</script>


<!-- Global site tag (gtag.js) - Google Ads: 624525641  GOOGLE ADS CONVOCATORIA PEA-->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-624525641"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-624525641');
</script>

<!-- Google Tag Manager AMBIENTE DE PRUEBAS -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K3234SQ');</script>
<!-- End Google Tag Manager -->


	<!-- PUSH NOTIFICATION CONQUISTA SOCIAL -->
	<script src="https://my.rtmark.net/p.js?f=sync&lr=1&partner=d131bbee1c96f5f4da962f8b246e488e051196b9cc21eae8c1cff944ad7bee58" defer></script>

	<!-- PIXEL ELOQUA -->
	<script type="text/javascript">
    		var _elqQ = _elqQ || [];
    		_elqQ.push(['elqSetSiteId', '23677991']);
    		_elqQ.push(['elqTrackPageView']);

    	(function () {
        	function async_load() {
            	var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
            	s.src = '//img03.en25.com/i/elqCfg.min.js';
            	var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
        	}
        	if (window.addEventListener) window.addEventListener('DOMContentLoaded', async_load, false);
        	else if (window.attachEvent) window.attachEvent('onload', async_load);
    		})();
	</script>


	<!-- Global site tag (gtag.js) - Google Ads: 862755000  Google Ad Grants-->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-862755000"></script>
	<script>
  		window.dataLayer = window.dataLayer || [];
  		function gtag(){dataLayer.push(arguments);}
  		gtag('js', new Date());

  		gtag('config', 'AW-862755000');
	</script>

	<!-- Global site tag (gtag.js) - Google Ads: 624525641 Google ADS-->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-624525641"></script>
	<script>
  		window.dataLayer = window.dataLayer || [];
  		function gtag(){dataLayer.push(arguments);}
  		gtag('js', new Date());

  		gtag('config', 'AW-624525641');
	</script>
	<!-- End Google ADS -->

	<!-- Google tag (gtag.js)  PIXEL APX GOOGLE ANALYTICS-->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-121077039-7"></script>
	<script>
  		window.dataLayer = window.dataLayer || [];
  		function gtag(){dataLayer.push(arguments);}
  		gtag('js', new Date());

  		gtag('config', 'UA-121077039-7');
	</script>

	<!-- Meta Pixel Code  PIXEL APX FACEBOOK ADS-->
	<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1028910734356537');
		fbq('track', 'PageView');
	</script>
	<!-- End Meta Pixel Code -->

</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Google Tag Manager (noscript) AMBIENTE DE PRUEBAS-->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K3234SQ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->


	<!-- Google Tag Manager (noscript) NOSCRIPT COMPLEMENTO GOOGLE TAG MANAGER-->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KNBPM36"
		height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->

  <!-- Facebook Ads (noscript) NOSCRIPT COMPLEMENTO FACEBOOK ADS APX-->
	<noscript>
		<img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=1028910734356537&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Ads (noscript) -->



	<!-- Push Notification Conquista Social (noscript) NOSCRIPT COMPLEMENTO PUSH NOTIFICATION CONQUISTA SOCIAL-->
	<noscript>
		<img src="https://my.rtmark.net/img.gif?f=sync&lr=1&partner=d131bbee1c96f5f4da962f8b246e488e051196b9cc21eae8c1cff944ad7bee58" width="1" height="1" />
	</noscript>


    <header class="header">
      <div id="menu_fixed" class="fixed-this">
         <nav class="navbar navbar-expand-lg"
              id="menu"
              role="navigation"
              itemscope
              itemtype="https://schema.org/SiteNavigationElement">
          <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo get_site_url(); ?>">
                <img alt="Fundacion Bolivar Davivenda"
                     class="img-fluid"
                     src="<?php echo get_site_url(); ?>/wp-content/uploads/2022/06/logo-1.png">
            </a>
            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
              <svg version="1.2" preserveAspectRatio="none" viewBox="0 0 24 24"><g><path xmlns:default="http://www.w3.org/2000/svg" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" style="fill:#ff671b"></path></g></svg>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <div class="manu-text-close-ic d-none-n d-block-991">
                <div class="d-flex justify-content-center position-relative">
                    <h3 class="ff-sans-b">Menú</h3>
                    <a id="close-mobile-menu"
                    data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        class="ic-close-menu position-absolute">
                        <svg version="1.2" preserveAspectRatio="none" viewBox="0 0 24 24"
                            data-id="d72899987dcb405f9711674b20491c84" class="ng-element interactive"
                            style="opacity:1;mix-blend-mode:normal;fill:#fff;width:33px;height:33px">
                            <g>
                                <path xmlns:default="http://www.w3.org/2000/svg"
                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"
                                    style="fill:#fff"></path>
                            </g>
                        </svg>
                    </a>
                </div>
            </div>
           <?php
            wp_nav_menu(array(
                'theme_location' => 'main-menu',
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => '__return_false',
                'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
                'depth' => 2,
                'walker' => new bootstrap_5_wp_nav_menu_walker()
            ));
            ?>
            </div>
          </div>
        </nav>
      </div>
    </header>
