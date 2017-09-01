<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *  This file is part of Goteo.
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

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error;
    use Goteo\Library\Text;

    class Categories {

        public static function process ($action = 'list', $id = null) {

            $model = 'Goteo\Model\Category';
            $url = '/admin/categories';
            $wrapperClass = 'categories';

            $errors = array();

            switch ($action) {
                case 'add':
                    if (isset($_GET['word'])) {
                        $item = (object) array('name'=>$_GET['word']);
                    } else {
                        $item = (object) array();
                    }
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => Text::_('Añadir')
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => Text::_('Categoría'),
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => Text::_('Descripción'),
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

                                    )
                                )

                            ),
                            'isCategories' => $wrapperClass . ' add'
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => $item,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Categoría',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

                                    )
                                )

                            ),
                            'isCategories' => $wrapperClass . ' edit'
                        )
                    );

                    break;
                case 'up':
                    $model::up($id);
                    break;
                case 'down':
                    $model::down($id);
                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
                case 'keywords':
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'keywords',
                            'file' => 'list',
                            'categories' => $model::getList(),
                            'words' => $model::getKeyWords(),
                            'isCategories' => $wrapperClass . ' add'
                        )
                    );
                    
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'category',
                    'addbutton' => Text::_('Nueva categoría'),
                    'otherbutton' => '<a href="/admin/categories/keywords" class="button">' . Text::get('admin-skill_keyword') . '</a>',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        //'name' => 'Categoría',
                        'name' => Text::_('Subject category name'),
                        'numProj' => 'Proyectos',
                        'numUser' => 'Usuarios',
                        'order' => Text::_('Prioridad'),
                        'translate' => '',
                        'up' => '',
                        'down' => '',
                        'remove' => ''
                    ),
                    'url' => "$url",
                    'isCategories' => $wrapperClass
                )
            );
            
        }

    }

}
