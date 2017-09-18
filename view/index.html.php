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

use Goteo\Core\View;

//@NODESYS
//@CALLSYS
$bodyClass = 'home';
// para que el prologue ponga el código js para botón facebook en el bannerside
include 'view/prologue.html.php';
include 'view/header.html.php';

?>
<div class="contents_wrapper">

    <div id="main">

        <?php
        echo new View("view/home/available.html.php", $this);
//        foreach ($this['order'] as $item=>$itemData) {
//            if (!empty($this[$item])) echo new View("view/home/{$item}.html.php", $this);
//        }
        ?>

    </div>
</div><!--.contents_wrapper-->
<?php
if (file_exists('omniconfig/footer.html')){
    include 'omniconfig/footer.html';
}
?>
<?php include 'view/epilogue.html.php'; ?>