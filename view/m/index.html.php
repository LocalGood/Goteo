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

use Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Library\Text;

//@NODESYS
//@CALLSYS
$bodyClass = 'home';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = Text::widget(Text::get('social-account-facebook'), 'fb');

// metas og: para que al compartir en facebook coja las imagenes de novedades
$ogmeta = array(
    'title' => 'Goteo.org',
    'description' => 'Goteo.org',
    'url' => SITE_URL
);
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

include 'view/m/prologue.html.php';
include 'view/m/header.html.php';
/*
    <script type="text/javascript">
        $(function(){
            $('#sub-header').slides({
                play: 8000
            });
        });

    </script>
*/ ?>
    <div class="contents_wrapper">

        <?/*php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } */?>

        <div id="main">

            <?/*php
            echo new View("view/m/home/available.html.php", $this);
            foreach ($this['order'] as $item=>$itemData) {
                if (!empty($this[$item])) echo new View("view/m/home/{$item}.html.php", $this);
            }
            */?>

            <?php
                echo new View("view/home/available.html.php", $this);
            ?>

        </div>
    </div><!--.contents_wrapper-->
<?php include 'view/m/footer.html.php'; ?>
<?php include 'view/m/epilogue.html.php'; ?>