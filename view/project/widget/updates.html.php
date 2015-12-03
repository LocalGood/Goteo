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

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Blog\Post;

$project = $this['project'];
$blog    = $this['blog'];

if (empty($this['post'])) {
    $posts = $blog->posts;
    $action = 'list';
    $this['show'] = 'list';
} else {
    $post = $this['post'];
    if (!in_array($post, array_keys($blog->posts))) {
        $posts = $blog->posts;
        $action = 'list';
        $this['show'] = 'list';
    } else {
        $post = Post::get($post, LANG);
        $action = 'post';
        $this['show'] = 'post';
    }
}

if ($this['show'] == 'list') {
    // paginacion
    require_once 'library/pagination/pagination.php';

    //recolocamos los post para la paginacion
    $the_posts = array();
    foreach ($posts as $i=>$p) {
        $the_posts[] = $p;
    }

    $pagedResults = new \Paginated($the_posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);
}

// 活動報告ナビゲーション -> controlに移動する？
if ($action == 'post'){

    $link_next = "";
    $link_prev = "";

    $plist = array();
    foreach($blog->posts as $k => $p){
        $plist[] = $k;
    }
    $idx = array_search($this['post'],$plist);

    if (!is_null($plist[$idx+1]) && (count($plist) > ($idx + 1))){
        $_id = $plist[$idx+1];
        $link_prev = "<li class='prev'><a href='/project/{$project->id}/updates/{$_id}'>前の記事</a></li>";
    }
    if (!is_null($plist[$idx-1]) && (0 <= ($idx - 1))){
        $_id = $plist[$idx-1];
        $link_next = "<li class='next'><a href='/project/{$project->id}/updates/{$_id}'>次の記事</a></li>";
    }
}

// segun lo que tengamos que mostrar :  lista o entrada
// uso la libreria blog para sacar los datos adecuados para esta vista

$level = (int) $this['level'] ?: 3;

?>
<div class="project-updates"> 
    <!-- una entrada -->
    <?php if ($action == 'post') : ?>
    <div class="post widget">
        <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'post', 'url' => '/project/'.$project->id.'/updates/')); ?>
        <ul class="post_nav">
            <?php if (!empty($link_prev)){ echo $link_prev; } ?>
            <?php if (!empty($link_next)){ echo $link_next; } ?>
        </ul>
    </div>
    <?php echo new View('view/blog/comments.html.php', array('post' => $post->id, 'owner' => $project->owner)); ?>
    <?php echo new View('view/blog/sendComment.html.php', array('post' => $post->id, 'project' => $project->id)); ?>
    <?php endif ?>
    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
            <?php while ($post = $pagedResults->fetchPagedRow()) :
                
                    $share_title = $post->title;
                    $share_url = SITE_URL . '/project/'.$project->id.'/updates/' . $post->id;
                    $facebook_url = 'http://facebook.com/sharer.php?u=' . rawurlencode($share_url) . '&t=' . rawurlencode($share_title);
                    $twitter_url = 'http://twitter.com/home?status=' . rawurlencode($share_title . ': ' . $share_url . ' #横浜 @LogooYOKOHAMA');
                ?>
                <div class="widget post">
                    <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'list', 'url' => '/project/'.$project->id.'/updates/')); ?>
					<ul class="share-goteo">
						<?/*<li class="sharetext"><?php echo Text::get('regular-share_this'); ?></li>*/?>
						<li class="twitter"><a href="<?php echo htmlspecialchars($twitter_url) ?>" target="_blank"><?php echo Text::get('regular-twitter'); ?></a></li>
						<li class="facebook"><a href="<?php echo htmlspecialchars($facebook_url) ?>" target="_blank"><?php echo Text::get('regular-facebook'); ?></a></li>
					</ul>
					<div class="comments-num"><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
                </div>
            <?php endwhile; ?>
            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>
        <?php else : ?>
            <p><?php echo Text::get('blog-no_posts'); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    if(preg_match('/^\/project\/(.*)\/updates\/[0-9]{1,}$/', $_SERVER['REQUEST_URI'], $m)):
        $permalink = SITE_URL . $_SERVER['REQUEST_URI'];
    ?>
    <div id="social_bookmark" class="update">
        <div id="twitter">
            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
            <script>
                !function(d,s,id){
                    var js,fjs=d.getElementsByTagName(s)[0];
                    if(!d.getElementById(id)){
                        js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
                        fjs.parentNode.insertBefore(js,fjs);
                    }
                }(document,"script","twitter-wjs");
            </script>
        </div>
        <div id="facebook">
            <div class="fb-like" data-href="<?= $permalink; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
        </div>

        <div class="g-plusone" data-size="medium" data-width="60"></div>
        <script type="text/javascript">
            window.___gcfg = {lang: 'ja'};

            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/platform.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
        </script>

        <div style="clear:both"></div>
        <div class="fb-comments" data-href="<?php $permalink; ?>" data-numposts="5" data-colorscheme="light" width="620"></div>
    </div><!-- #social_bookmark -->
    <?php endif; ?>

</div>