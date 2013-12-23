<?php

/**
 * This file is part of Laravel Credentials by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Guest Composer
    |--------------------------------------------------------------------------
    |
    | This option specifies the view composer for guest views.
    |
    | Default: 'GrahamCampbell\Core\Composers\BlankComposer'
    |
    */

    'guestcomposer' => 'GrahamCampbell\Core\Composers\BlankComposer',

    /*
    |--------------------------------------------------------------------------
    | User Composer
    |--------------------------------------------------------------------------
    |
    | This option specifies the view composer for user views.
    |
    | Default: 'GrahamCampbell\Core\Composers\BlankComposer'
    |
    */

    'usercomposer' => 'GrahamCampbell\Core\Composers\BlankComposer',

    /*
    |--------------------------------------------------------------------------
    | Mod Composer
    |--------------------------------------------------------------------------
    |
    | This option specifies the view composer for moderator views.
    |
    | Default: 'GrahamCampbell\Core\Composers\BlankComposer'
    |
    */

    'modcomposer' => 'GrahamCampbell\Core\Composers\BlankComposer',

    /*
    |--------------------------------------------------------------------------
    | Admin Composer
    |--------------------------------------------------------------------------
    |
    | This option specifies the view composer for admin views.
    |
    | Default: 'GrahamCampbell\Core\Composers\BlankComposer'
    |
    */

    'admincomposer' => 'GrahamCampbell\Core\Composers\BlankComposer'

);
