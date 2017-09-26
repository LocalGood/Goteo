<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */
//@NODESYS
if($_SERVER['REQUEST_URI']=="/"):
    $ogmeta = array(
        'title' => GOTEO_META_TITLE,
        'description' => GOTEO_META_DESCRIPTION,
        'url' => SITE_URL,
        'image' => array(SITE_URL . '/view/images/ogimg.png')
    );
elseif(strstr($_SERVER['REQUEST_URI'],'project')):
    if(!empty($this['project']->subtitle)) {
        $description = $this['project']->subtitle;
    } else {
        $description = $this['project']->description;
    }
    foreach ($this['project']->gallery as $image) :
        if(method_exists($image, 'getLink')){
            $gallery = $image->getLink(580, 580);
        }
    endforeach;
    $ogmeta = array(
        'title' => $this['project']->name,
        'description' => $description,
        'url' => SITE_URL.$_SERVER['REQUEST_URI'],
        'image' => array($gallery)
    );
endif;
if (!empty($this['posts'])) {
    foreach ($this['posts'] as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $ogmeta['image'][] = $pbimg->getLink(500, 285);
                }
            }
        } elseif (!empty($post->image)) {
            $ogmeta['image'][] = $post->image->getLink(500, 285);
        }
    }
}
$blog_post = strpos($ogmeta['url'], '/updates');
$_blog_key = substr($ogmeta['url'], $blog_post+9);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php
        $lg_title = GOTEO_META_TITLE;
        if ($blog_post){
            $_blog_post = $this['blog'];
            $lg_title .= ' - ' . htmlspecialchars($_blog_post->posts[$_blog_key]->title);
        } elseif (!empty($project->name)){
            $lg_title .= ' - ' . $project->name;
        }
        ?>
        <title><?php echo htmlspecialchars($lg_title, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="icon" type="image/png" href="/favicon.ico" />
        <meta name="description" content="<?php echo htmlspecialchars(GOTEO_META_DESCRIPTION, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="keywords" content="<?php echo htmlspecialchars(GOTEO_META_KEYWORDS, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="author" content="<?php echo htmlspecialchars(GOTEO_META_AUTHOR, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="copyright" content="<?php echo htmlspecialchars(GOTEO_META_COPYRIGHT, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta name="robots" content="all" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<?php if (isset($ogmeta)) : ?>
        <meta property="og:title" content="<?php echo htmlspecialchars($ogmeta['title'], ENT_QUOTES, 'UTF-8'); ?>" />
        <meta property="og:type" content="activity" />
        <meta property="og:site_name" content="Goteo.org" />
        <meta property="og:description" content="<?php echo htmlspecialchars($ogmeta['description'], ENT_QUOTES, 'UTF-8'); ?>" />
        <?php if (is_array($ogmeta['image'])) :
            foreach ($ogmeta['image'] as $ogimg) : ?>
        <meta property="og:image" content="<?php echo $ogimg ?>" />
        <?php endforeach;
        else : ?>
        <meta property="og:image" content="<?php echo $ogmeta['image'] ?>" />
        <?php endif; ?>
        <meta property="og:url" content="<?php echo $ogmeta['url'] ?>" />
<?php else : ?>
        <meta property="og:title" content="Goteo.org" />
        <meta property="og:description" content="<?php echo htmlspecialchars(GOTEO_META_DESCRIPTION, ENT_QUOTES, 'UTF-8'); ?>" />
        <meta property="og:image" content="<?php echo SITE_URL ?>/goteo_logo.png" />
        <meta property="og:url" content="<?php echo SITE_URL ?>" />
<?php endif; ?>
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/m/css/styles-m.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <?php if (!isset($useJQuery) || !empty($useJQuery)): ?>
        <?/*<script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery-1.6.4.min.js"></script>*/?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery.tipsy.min.js"></script>
          <!-- custom scrollbars -->
          <link type="text/css" href="<?php echo SRC_URL ?>/view/m/css/jquery.jscrollpane.min.css" rel="stylesheet" media="all" />
          <script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery.mousewheel.min.js"></script>
          <script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery.jscrollpane.min.js"></script>
          <!-- end custom scrollbars -->
		  <!-- sliders -->
		  <script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery.slides.min.js"></script>
		  <!-- end sliders -->
          <!-- meanmenu -->
            <link type="text/css" href="<?php echo SRC_URL ?>/view/m/css/meanmenu.min.css" rel="stylesheet" media="all" />
            <script type="text/javascript" src="<?php echo SRC_URL ?>/view/m/js/jquery.meanmenu.min.js"></script>
            <!-- end meanmenu -->

		  <!-- vigilante de sesi�n -->
		  <script type="text/javascript" src="<?php echo SITE_URL ?>/view/m/js/watchdog.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL ?>/view/m/js/heightLine.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL ?>/view/m/js/flipsnap.min.js"></script>
            <script type="text/javascript" src="<?php echo SITE_URL ?>/view/m/js/localgood-sp.js"></script>

        <?php endif; ?>

        <?php if (isset($jsreq_autocomplete)) : ?>
            <link href="<?php echo SRC_URL ?>/view/m/css/jquery-ui-1.10.3.autocomplete.min.css" rel="stylesheet" />
            <script src="<?php echo SRC_URL ?>/view/m/js/jquery-ui-1.10.3.autocomplete.min.js"></script>
        <?php endif; ?>
        <?php if(defined('GOTEO_ANALYTICS_TRACKER')){
            echo GOTEO_ANALYTICS_TRACKER;
        }  ?>

        <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/m/css/styles.css" />

    </head>

    <body id="page_top" <?php if (isset($bodyClass)) echo ' class="' . htmlspecialchars($bodyClass) . '"' ?>>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&appId=<? if(defined('OAUTH_FACEBOOK_ID')){echo OAUTH_FACEBOOK_ID;} ?>&version=v2.9";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        <script type="text/javascript">
            // Mark DOM as javascript-enabled
            jQuery(document).ready(function ($) {
                $('body').addClass('js');
                $('.tipsy').tipsy();
                /* Rolover sobre los cuadros de color */
                $("li").not(".header .nav_wrapper ul li").hover(
                        function () { $(this).addClass('active') },
                        function () { $(this).removeClass('active') }
                );
                $('.activable').hover(
                    function () { $(this).addClass('active') },
                    function () { $(this).removeClass('active') }
                );
                $(".a-null").click(function (event) {
                    event.preventDefault();
                });
            });
        </script>
        <noscript><!-- Please enable JavaScript --></noscript>
        <div id="wrapper">
