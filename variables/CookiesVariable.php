<?php

namespace w3modules\w3cookies\variables;

use w3modules\w3cookies\W3Cookies;

/**
 * CookiesVariable
 *
 * Exposes cookie consent state to Twig templates.
 *
 * Available in Twig as:
 *   craft.w3cookies
 *
 * Example usage:
 *   {% if craft.w3cookies.accepted() %}
 *     {# Load analytics scripts #}
 *   {% endif %}
 *
 * @since 1.0.0
 */
class CookiesVariable
{
    /**
     * Check whether the user has accepted cookies.
     *
     * @return bool
     */
    public function accepted(): bool
    {
        return W3Cookies::$instance->cookies->hasAccepted();
    }

    /**
     * Check whether the user has explicitly declined cookies.
     *
     * @return bool
     */
    public function declined(): bool
    {
        return W3Cookies::$instance->cookies->hasDeclined();
    }

    /**
     * Check whether the browser has "Do Not Track" enabled.
     *
     * This reflects the user's browser preference and does not rely on cookies.
     *
     * @return bool
     */
    public function doNotTrack(): bool
    {
        return W3Cookies::$instance->cookies->hasDoNotTrackSet();
    }
}
