<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundaci��n Fuentes Abiertas (see README for details)
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

$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

$images = array();
$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $project->categories)
        );            
}

$skills = array();

foreach ($this['skills'] as $key => $value) {
    $skills[] =  array(
        'value'     => $value->id,
        'label'     => $value->name,
        'category'     => $value->parent_skill_id,
        'checked'   => in_array($value->id, $project->skills)
    );
}

$currently = array();

foreach ($this['currently'] as $value => $label) {
    $currently[] =  array(
        'value'     => $value,
        'label'     => $label        
        );            
}

$scope = array();

foreach ($this['scope'] as $value => $label) {
    $scope[] =  array(
        'value'     => $value,
        'label'     => $label
        );
}

$project_location = array();

foreach ($this['project_location'] as $value => $label) {
    $project_location[] =  array(
        'value'     => $label,
        'label'     => $label
        );
}

// video de motivacion
if (!empty($project->video->url)) {
    $video = array(
            'type'  => 'media',
            'title' => Text::get('overview-field-media_preview'),
            'class' => 'inline media',
            'type'  => 'html',
            'html'  => !empty($project->video) ? $project->video->getEmbedCode($project->video_usubs) : ''
    );
} else {
    $video = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );
}


$superform = array(
    'level'         => $this['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('overview-main-header'),
    'hint'          => Text::get('guide-project-description'),
    'class'         => 'aqua',        
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),
        
        'name' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-name'),
            'required'  => true,
            'value'     => $project->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-subtitle'),
            'required'  => false,
            'value'     => $project->subtitle,
            'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
            'ok'        => !empty($okeys['subtitle']) ? array($okeys['subtitle']) : array()
        ),
        'image_0' => array(
            'title'     => Text::get('overview-fields-images-title'),
            'type'      => 'group',
            'required'  => true,
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
            'ok'        => !empty($okeys['image']) ? array($okeys['image']) : array(),
            'class'     => 'images',
            'children'  => array(
                'image_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload',
                ),
                'project-image_0' => array(
                    'type'  => 'html',
                    'class' => 'inline project-image',
                    'html'  => is_object($project->gallery[0]) ?
                        $project->gallery[0] . '<input type="hidden" name="image_order" value="1"><img src="' . $project->gallery[0]->getLink(128,128) . '" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$project->gallery[0]->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                        ''
                )
            )
        ),
        'description' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description'),
            'required'  => true,
            'value'     => $project->description,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        'image_1' => array(
            'title'     => Text::get('overview-fields-images-title') . ' 2',
            'type'      => 'group',
            'required'  => false,
            'class'     => 'images',
            'children'  => array(
                'image_upload_1'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload',
                ),
                'project-image_1' => array(
                    'type'  => 'html',
                    'class' => 'inline project-image',
                    'html'  => is_object($project->gallery[1]) ?
                        $project->gallery[1] . '<input type="hidden" name="image_order" value="2"><img src="' . $project->gallery[1]->getLink(128,128) .'" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$project->gallery[1]->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                        ''
                )
            )
        ),
        'description_1' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description') . ' 2',
            'required'  => false,
            'value'     => $project->description_1,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        'image_2' => array(
            'title'     => Text::get('overview-fields-images-title') . ' 3',
            'type'      => 'group',
            'required'  => false,
            'class'     => 'images',
            'children'  => array(
                'image_upload_2'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload',
                ),
                'project-image_2' => array(
                    'type'  => 'html',
                    'class' => 'inline project-image',
                    'html'  => is_object($project->gallery[2]) ?
                        $project->gallery[2] . '<input type="hidden" name="image_order" value="3"><img src="' . $project->gallery[2]->getLink(128,128) .'" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$project->gallery[2]->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                        ''
                )
            )
        ),
        'description_2' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description') . ' 3',
            'required'  => false,
            'value'     => $project->description_2,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        'description_group' => array(
            'type' => 'group',
            'children'  => array(
                'motivation' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-motivation'),
                    'required'  => true,
                    'errors'    => !empty($errors['motivation']) ? array($errors['motivation']) : array(),
                    'ok'        => !empty($okeys['motivation']) ? array($okeys['motivation']) : array(),
                    'value'     => $project->motivation
                ),
                // video motivacion
                'video' => array(
                    'type'      => 'textbox',
                    'required'  => false,
                    'title'     => Text::get('overview-field-video'),
                    'ok'        => !empty($okeys['video']) ? array($okeys['video']) : array(),
                    'value'     => (string) $project->video
                ),

                'video-upload' => array(
                    'name' => "upload",
                    'type'  => 'submit',
                    'label' => Text::get('form-upload-button'),
                    'class' => 'inline media-upload'
                ),

                'video-preview' => $video,
                
                // fin video motivacion
                'goal' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-goal'),
                    'required'  => true,
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'ok'        => !empty($okeys['goal']) ? array($okeys['goal']) : array(),
                    'value'     => $project->goal
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-related'),
                    'required'  => true,
                    'errors'    => !empty($errors['related']) ? array($errors['related']) : array(),
                    'ok'        => !empty($okeys['related']) ? array($okeys['related']) : array(),
                    'value'     => $project->related
                ),
            )
        ),
       
        'category' => array(    
            'type'      => 'checkboxes',
            'name'      => 'categories[]',
            'title'     => Text::get('overview-field-categories'),
            'required'  => true,
            'class'     => 'cols_3',
            'options'   => $categories,
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
            'ok'        => !empty($okeys['categories']) ? array($okeys['categories']) : array()
        ),
        'keywords' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-keywords'),
            'required'  => false,
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'ok'        => !empty($okeys['keywords']) ? array($okeys['keywords']) : array(),
            'value'     => $project->keywords
        ),

        'project_location' => array(
            'title'     => Text::get('overview-field-project_location'),
            'type'      => 'select',
            'required'  => true,
            'options'   => $project_location,
            'class'     => 'project_location cols_' . count($project_location),
            'errors'    => !empty($errors['project_location']) ? array($errors['project_location']) : array(),
            'ok'        => !empty($okeys['project_location']) ? array($okeys['project_location']) : array(),
            'value'     => $project->project_location
        ),

    )

);
if (isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin'])){
    $superform['elements']['evaluation'] = array(
        'type'      => 'textarea',
        'title'     => Text::get('overview-field-evaluation'),
        'required'  => false,
        'value'     => $project->evaluation,            
        'errors'    => !empty($errors['evaluation']) ? array($errors['evaluation']) : array(),
        'ok'        => !empty($okeys['evaluation']) ? array($okeys['evaluation']) : array()
    );
}
$superform['elements']['footer'] = array(
    'type'      => 'group',
    'children'  => array(
        'errors' => array(
            'title' => Text::get('form-footer-errors_title'),
            'view'  => new View('view/project/edit/errors.html.php', array(
                'project'   => $project,
                'step'      => $this['step']
            ))                    
        ),
        'buttons'  => array(
            'type'  => 'group',
            'children' => array(
                'next' => array(
                    'type'  => 'submit',
                    'name'  => 'view-step-costs',
                    'label' => Text::get('form-next-button'),
                    'class' => 'next'
                )
            )
        )
    )
);

foreach ($superform['elements'] as $id => &$element) {
    
    if (!empty($this['errors'][$this['step']][$id])) {
        $element['errors'] = arrray();
    }
    
}

echo new SuperForm($superform);