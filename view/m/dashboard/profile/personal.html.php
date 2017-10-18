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
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$errors = $this['errors'];
$personal = $this['personal'];
$this['level'] = 3;

?>
<form method="post" action="/dashboard/profile/personal" class="project" enctype="multipart/form-data">

<?php
echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'hint'          => Text::get('guide-dashboard-user-personal'),
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-userPersonal'
        )
    ),
    'elements'      => array(

        'contract_name' => array(
            'type'      => 'textbox',
            'size'      => 20,
            'title'     => Text::get('personal-field-contract_name'),
            'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
            'value'     => $personal->contract_name
        ),
        'phone' => array(
            'type'  => 'textbox',
            'title' => Text::get('personal-field-phone'),
            'dize'  => 15,
            'errors'    => !empty($errors['phone']) ? array($errors['phone']) : array(),
            'value' => $personal->phone
        ),

        'address' => array(
            'type'  => 'textbox',
            'title' => Text::get('personal-field-address'),
            'rows'  => 6,
            'cols'  => 40,
            'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
            'value' => $personal->address
        ),

        'zipcode' => array(
            'type'  => 'textbox',
            'title' => Text::get('personal-field-zipcode'),
            'size'  => 7,
            'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
            'value' => $personal->zipcode
        ),
    )

));

?>
</form>
