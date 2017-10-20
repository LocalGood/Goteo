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

$project = $this['skillmatching'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

$images = array();
foreach ($project->gallery as $image) {
    $images[] = array(
        'type'  => 'html',
        'class' => 'inline gallery-image',
        'html'  => is_object($image) ?
                   $image . '<img src="'.$image->getLink(128,128).'" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                   ''
    );

}


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
    'title'         => Text::get('overview-main-header-sm'),
    'hint'          => Text::get('guide-project-description-sm'),
    'class'         => 'aqua',        
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),
        
        'name' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-name-sm'),
            'required'  => true,
            'value'     => $project->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-subtitle-sm'),
            'required'  => false,
            'value'     => $project->subtitle,
            'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
            'ok'        => !empty($okeys['subtitle']) ? array($okeys['subtitle']) : array()
        ),

        'images' => array(        
            'title'     => Text::get('overview-fields-images-title-sm'),
            'type'      => 'group',
            'required'  => true,
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
            'ok'        => !empty($okeys['image']) ? array($okeys['image']) : array(),
            'class'     => 'images',
            'children'  => array(
                'image_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload'
                )
            )
        ),        
        'gallery' => array(
            'type'  => 'group',
            'class' => 'inline',
            'children'  => $images
        ),

        'description' => array(            
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description-sm'),
            'required'  => true,
            'value'     => $project->description,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        'description_group' => array(
            'type' => 'group',
            'children'  => array(
                'motivation' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-motivation-sm'),
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
                    'title'     => Text::get('overview-field-goal-sm'),
                    'required'  => true,
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'ok'        => !empty($okeys['goal']) ? array($okeys['goal']) : array(),
                    'value'     => $project->goal
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-related-sm'),
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
            'title'     => Text::get('overview-field-categories-sm'),
            'required'  => true,
            'class'     => 'cols_3',
            'options'   => $categories,
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
            'ok'        => !empty($okeys['categories']) ? array($okeys['categories']) : array()
        ),
        'keywords' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-keywords-sm'),
            'required'  => false,
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'ok'        => !empty($okeys['keywords']) ? array($okeys['keywords']) : array(),
            'value'     => $project->keywords
        ),
        'project_location' => array(
            'title'     => Text::get('overview-field-project_location-sm'),
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
            'view'  => new View('view/skillmatching/edit/errors.html.php', array(
                'skillmatching'   => $project,
                'step'      => $this['step']
            ))                    
        ),
        'buttons'  => array(
            'type'  => 'group',
            'children' => array(
                'next' => array(
                    'type'  => 'submit',
                    'name'  => 'view-step-rewards',
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