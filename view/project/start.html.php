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
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$bodyClass = 'project-edit';

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div class="project-header">
            <h2><span>プロジェクトを作成します。</span></h2>
        </div>
    </div>

    <div id="main" class="<?php echo htmlspecialchars($this['step']) ?>">

        <form method="post" action="<?php echo SITE_URL . "/project/create/" ?>" class="project">
            <div>
                <label>
                    <h3>プロジェクトURLとなる文字列を入力してください。</h3>
                    <br/>
                    <input type="text" name="project_id" value="" maxlength="32" minlength="10" />
                </label>
                <?php
                if($this['error_message']): ?>
                    <p class="error" style="color:#ff6666"><?php echo $this['error_message']; ?></p>
                <?php else: ?>
                    <p class="notice">※半角のアルファベット、ハイフン、アンダーバーのみ使用可能。10文字以内32文字以下。</p>
                <?php endif; ?>
            </div>
            <div>
                <button id="create_continue" name="action" type="submit" value="continue">次へ</button>
            </div>
            <input type="hidden" name="confirm" value="true" />
        </form>

    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>
