<?php

namespace w3modules\w3cookies\services;

use Craft;
use yii\base\Component;
use yii\web\Cookie;

/**
 * CookiesService
 *
 * Handles all cookie-related logic for the W3Cookies module:
 *  - Reading consent state
 *  - Writing consent cookies
 *  - Respecting browser "Do Not Track"
 *  - Removing non-essential cookies when consent is declined
 *
 * This service is intentionally lightweight and framework-compliant,
 * using Yii's cookie handling instead of direct `setcookie()` calls.
 *
 * @since 1.0.0
 */
class CookiesService extends Component
{
    /**
     * Name of the consent cookie
     *
     * @var string
     */
    private string $acceptedName = 'w3cookies_accepted';

    /**
     * Cookie value indicating consent has been accepted
     *
     * @var string
     */
    private string $acceptedValue = '1';

    /**
     * Cookie value indicating consent has been declined
     *
     * @var string
     */
    private string $declinedValue = '0';

    /**
     * Check whether the user has accepted cookies.
     *
     * Reads the consent cookie from the current request.
     * Note: Cookies are only available on the *next* request
     * after they have been set in the response.
     *
     * @return bool
     */
    public function hasAccepted(): bool
    {
        return Craft::$app->getRequest()
            ->getCookies()
            ->getValue($this->acceptedName) === $this->acceptedValue;
    }

    /**
     * Check whether the user has explicitly declined cookies.
     *
     * @return bool
     */
    public function hasDeclined(): bool
    {
        return Craft::$app->getRequest()
            ->getCookies()
            ->getValue($this->acceptedName) === $this->declinedValue;
    }

    /**
     * Check whether the browser has "Do Not Track" enabled.
     *
     * This does not store a cookie; it only reads the DNT header.
     *
     * @return bool
     */
    public function hasDoNotTrackSet(): bool
    {
        return Craft::$app->getRequest()
            ->getHeaders()
            ->get('DNT') === '1';
    }

    /**
     * Set the cookie consent preference.
     *
     * Stores a single cookie with a one-year expiration.
     * Uses Yii's Cookie object for proper Craft 5 compatibility.
     *
     * The cookie will be available on the *next* request.
     *
     * @param bool $accepted Whether the user accepted cookies
     */
    public function setConsent(bool $accepted): void
    {
        $cookie = new Cookie([
            'name' => $this->acceptedName,
            'value' => $accepted ? $this->acceptedValue : $this->declinedValue,
            'expire' => strtotime('+1 year'),
            'httpOnly' => true,
            'sameSite' => Cookie::SAME_SITE_LAX,
        ]);

        Craft::$app->getResponse()
            ->getCookies()
            ->add($cookie);
    }

    /**
     * Remove all non-essential cookies.
     *
     * Essential cookies are:
     *  - Craft CSRF token
     *  - PHP session cookie
     *  - Consent cookie (optional)
     *
     * This is typically called when a user declines cookies.
     *
     * @param bool $persistPreference Whether to preserve the consent cookie
     */
    public function unsetNonEssentialCookies(bool $persistPreference = true): void
    {
        $requestCookies = Craft::$app->getRequest()->getCookies();
        $responseCookies = Craft::$app->getResponse()->getCookies();

        // Define cookies that should never be removed
        $essential = [
            Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            Craft::$app->getSession()->getName(),
        ];

        // Preserve consent cookie if requested
        if ($persistPreference) {
            $essential[] = $this->acceptedName;
        }

        // Remove all cookies that are not essential
        foreach ($requestCookies as $cookie) {
            if (!in_array($cookie->name, $essential, true)) {
                $responseCookies->remove($cookie->name);
            }
        }
    }
}
