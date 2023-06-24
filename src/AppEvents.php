<?php

namespace App;

/**
 * Contains all custom events thrown in the application.
 */
final class AppEvents
{
    /**
     * The REGISTRATION_SUCCESS event occurs right after user
     * registration ends successfully.
     *
     * This event allows you to do any action after user had
     * registered.
     *
     * @Event("App\Event\RegistrationSuccessEvent")
     */
    public const REGISTRATION_SUCCESS = 'app.registration_success';
}