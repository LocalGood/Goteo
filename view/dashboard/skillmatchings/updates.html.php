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

define('ADMIN_NOAUTOSAVE', true);

$blog  = $this['blog'];
$posts = $this['posts'];

$errors = $this['errors'];

$level = $this['level'] = 3;

$url = '/dashboard/skillmatchings/updates';

if ($this['action'] == 'none') return;

?>
<?php if ($this['action'] == 'list') : ?>
<div class="widget">
    <?php if (!empty($blog->id) && $blog->active) : ?>
        <a class="button" href="<?php echo $url; ?>/add"><?echo Text::get('project-updates-create_new'); ?></a>
    <?php endif; ?>

    <!-- lista -->
    <?php if (!empty($posts)) : ?>
    <?php foreach ($posts as $post) : ?>
        <div class="post">
            <a class="button" href="<?php echo $url; ?>/edit/<?php echo $post->id; ?>"><?php echo Text::get('regular-edit') ?></a>&nbsp;&nbsp;&nbsp;
            <a class="remove button weak" href="<?php echo $url; ?>/delete/<?php echo $post->id; ?>" onclick="return confirm(Text::_('delete this update'));"><?php echo Text::get('regular-delete') ?></a>
            <span><?php echo $post->publish ? Text::get('regular-published_yes') : Text::get('regular-published_no'); ?></span>
            <strong><?php echo $post->title; ?></strong>
            <span><?php echo $post->date; ?></span>
        </div>
    <?php endforeach; ?>
    <?php else : ?>
        <p><?php echo Text::get('blog-no_posts') ?></p>
    <?php endif; ?>

</div>

<?php  else : // sueprform!

        $post  = $this['post']; // si edit
        $allow = array(
            array(
                'value'     => 1,
                'label'     => Text::get('regular-yes')
                ),
            array(
                'value'     => 0,
                'label'     => Text::get('regular-no')
                )
        );

        $comment_allow = array(
            array(
                'value'     => 1,
                'label'     => Text::get('project-updates-allow_comments-yes')
            ),
            array(
                'value'     => 0,
                'label'     => Text::get('project-updates-allow_comments-no')
            )
        );

        $publish_allow  = array(
            array(
                'value'     => 1,
                'label'     => Text::get('project-updates-publish-yes')
            ),
            array(
                'value'     => 0,
                'label'     => Text::get('project-updates-publish-no')
            )
        );


        $images = array();
        foreach ($post->gallery as $image) {
            $images[] = array(
                'type'  => 'html',
                'class' => 'inline gallery-image',
                'html'  => is_object($image) ?
                           $image . '<img src="'.SRC_URL.'/image/'.$image->id.'/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                           ''
            );

        }

        if (!empty($post->media->url)) {
            $media = array(
                    'type'  => 'media',
                    'title' => Text::get('overview-field-media_preview'),
                    'class' => 'inline media',
                    'type'  => 'html',
                    'html'  => !empty($post->media) ? $post->media->getEmbedCode() : ''
            );
        } else {
            $media = array(
                'type'  => 'hidden',
                'class' => 'inline'
            );


        }
    ?>

    <form method="post" action="/dashboard/skillmatchings/updates/<?php echo $this['action']; ?>/<?php echo $post->id; ?>" class="project" enctype="multipart/form-data">

    <?php echo new SuperForm(array(

        'action'        => '',
        'level'         => $this['level'],
        'method'        => 'post',
        'title'         => '',
        'hint'          => Text::get('guide-project-updates'),
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-post',
                'label' => Text::get('regular-save'),
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'blog' => array (
                'type' => 'hidden',
                'value' => $post->blog
            ),
            'title' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
//                'title'     => 'Título',
                'title'     => Text::get('project-updates-title'),
                'hint'      => Text::get('tooltip-updates-title'),
                'errors'    => !empty($errors['title']) ? array($errors['title']) : array(),
                'value'     => $post->title,
            ),
            'text' => array(
                'type'      => 'textarea',
                'required'  => true,
                'cols'      => 40,
                'rows'      => 16,
//                'title'     => 'Texto de la entrada',
                'title'     => Text::get('project-updates-text'),
                'hint'      => Text::get('tooltip-updates-text'),
                'errors'    => !empty($errors['text']) ? array($errors['text']) : array(),
                'value'     => $post->text
            ),
            'image' => array(
//                'title'     => 'Imagen',
                'title'     => Text::get('project-updates-image'),
                'type'      => 'group',
                'hint'      => Text::get('tooltip-updates-image'),
                'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
                'class'     => 'image',
                'children'  => array(
                    'image_upload'    => array(
                        'type'  => 'file',
                        'label' => Text::get('form-image_upload-button'),
                        'class' => 'inline image_upload',
                        'title' => Text::get('profile-field-avatar_upload'),
                        'hint'  => Text::get('tooltip-updates-image_upload'),
                    )
                )
            ),

            'gallery' => array(
                'type'  => 'group',
//                'title' => Text::get('overview-field-image_gallery'),
                'class' => 'inline',
                'children'  => $images
            ),

            'media' => array(
                'type'      => 'textbox',
//                'title'     => 'Vídeo',
                'title'     => Text::get('project-updates-video'),
                'class'     => 'media',
                'hint'      => Text::get('tooltip-updates-media'),
                'errors'    => !empty($errors['media']) ? array($errors['media']) : array(),
                'value'     => (string) $post->media
            ),
            
            'media-upload' => array(
                'name' => "upload",
                'type'  => 'submit',
                'label' => Text::get('form-upload-button'),
                'class' => 'inline media-upload'
            ),

            'media-preview' => $media,

//            'legend' => array(
//                'type'      => 'textarea',
//                'title'     => Text::get('regular-media_legend'),
//                'value'     => $post->legend,
//            ),
            "date" => array(
                'type'      => 'datebox',
                'required'  => true,
//                'title'     => 'Fecha de publicación',
                'title'     => Text::get('project-updates-date'),
                'hint'      => Text::get('tooltip-updates-date'),
                'size'      => 8,
                'value'     => $post->date
            ),
            'allow' => array(
//                'title'     => 'Permite comentarios',
                'title'     => Text::get('project-updates-allow_comments'),
                'type'      => 'slider',
                'options'   => $comment_allow,
                'class'     => 'currently cols_' . count($comment_allow),
                'hint'      => Text::get('tooltip-updates-allow_comments'),
                'errors'    => !empty($errors['allow']) ? array($errors['allow']) : array(),
                'value'     => (int) $post->allow
            ),
            'publish' => array(
//                'title'     => 'Publicado',
                'title'     => Text::get('project-updates-publish'),
                'type'      => 'slider',
                'options'   => $publish_allow,
                'class'     => 'currently cols_' . count($publish_allow),
                'hint'      => Text::get('tooltip-updates-publish'),
                'errors'    => !empty($errors['publish']) ? array($errors['publish']) : array(),
                'value'     => (int) $post->publish
            )
        )

    ));
    ?>

    </form>

<?php endif; ?>