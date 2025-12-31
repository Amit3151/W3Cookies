# W3Cookies – Craft CMS 5 Cookie Consent Module
W3Cookies is a lightweight **cookie consent module for Craft CMS 5**.

It provides frontend actions and Twig helpers to manage cookie consent
(accept, decline, reset) without requiring a full Craft plugin or Control Panel UI.

The module is intentionally simple and framework-compliant, making it ideal for
projects that prefer **modules over plugins**.



## Features
-  Accept / Decline / Reset cookie consent
-  Remove non-essential cookies on decline
-  Respect browser **Do Not Track (DNT)**
-  Twig helpers for conditional loading
-  AJAX & form submission support
-  Craft CMS 5 compatible (strict typing, Yii cookies)
-  No deprecated APIs



## Requirements
- Craft CMS **5.x**
- PHP **8.1+**



## Installation

W3Cookies is a **module**, not a plugin.  
It can be placed in **any folder**, as long as autoloading and registration are correct.

### 1. Place the Module
You may place the module in **any directory**, for example:

``modules/w3cookies``
``w3modules/w3cookies``
``custom-modules/w3cookies``

> The folder name does **not** matter — the namespace does.



### 2. Composer Autoloading (Required)
Ensure the namespace used by the module is autoloaded.

The module namespace is:
```
w3modules\w3cookies
````
Add (or confirm) the following in your root `composer.json`:
```json
{
  "autoload": {
    "psr-4": {
      "w3modules\\": "w3modules/"
    }
  }
}
````
If you placed the module elsewhere, adjust accordingly:
```json
{
  "autoload": {
    "psr-4": {
      "w3modules\\": "modules/"
    }
  }
}
```
Then run:
```bash
composer dump-autoload
```


### 3. Register the Module
Edit `config/app.php`:
```php
<?php

use w3modules\w3cookies\W3Cookies;

return [
    'modules' => [
        'w3cookies' => W3Cookies::class,
    ],
    'bootstrap' => ['w3cookies'],
];
```
> The **module ID (`w3cookies`) must match** the action URLs.


### 4. Clear Craft Caches
```bash
php craft clear-caches/all
```


## Available Actions
W3Cookies exposes frontend actions for managing consent.

All actions:
* Allow anonymous access
* Require CSRF token
* Support AJAX and form submissions


### 1. Accept Cookies
**Endpoint**
```
POST /actions/w3cookies/consent/accept
```
**What it does**
* Stores consent as accepted
* Does not remove existing cookies
  
**Use cases**
* “Accept all cookies” button
* Initial cookie banner confirmation

**Example**
```twig
<form method="post" action="{{ actionUrl('w3cookies/consent/accept') }}">
  {{ csrfInput() }}
  <button type="submit">Accept Cookies</button>
</form>
```


### 2. Decline Cookies
**Endpoint**
```
POST /actions/w3cookies/consent/decline
```
**What it does**
* Removes all non-essential cookies
* Stores declined preference

**Use cases**
* “Decline cookies” option
* GDPR-compliant rejection flow

**Example**
```twig
<form method="post" action="{{ actionUrl('w3cookies/consent/decline') }}">
  {{ csrfInput() }}
  <button type="submit">Decline Cookies</button>
</form>
```


### 3. Reset Cookie Preference
**Endpoint**
```
POST /actions/w3cookies/consent/reset
```
**What it does**
* Removes all non-essential cookies
* Clears saved preference

**Use cases**
* “Change cookie settings” page
* Privacy preferences reset



## Available Twig Variables
The module registers a Twig variable:
```
craft.w3cookies
```


### 1. `craft.w3cookies.accepted()`
**Returns**
``
bool
``
**Use cases**
* Load analytics scripts
* Enable tracking features
```twig
{% if craft.w3cookies.accepted() %}
  <!-- Analytics / GTM -->
{% endif %}
```

### 2. `craft.w3cookies.declined()`
**Returns**
``
bool
``
**Use cases**
* Prevent tracking
* Display consent reminder
```twig
{% if craft.w3cookies.declined() %}
  <p>Tracking is disabled.</p>
{% endif %}
```


### 3. `craft.w3cookies.doNotTrack()`
**Returns**
``
bool
``
**What it checks**
* Browser `DNT` (Do Not Track) header
**Use cases**
* Override analytics even if consent exists
* Respect browser-level privacy
```twig
{% if craft.w3cookies.accepted() and not craft.w3cookies.doNotTrack() %}
  <!-- Safe to load analytics -->
{% endif %}
```


## Cookie Details

| Property       | Value                |
| -------------- | -------------------- |
| Cookie name    | `w3cookies_accepted` |
| Accepted value | `1`                  |
| Declined value | `0`                  |
| Expiration     | 1 year               |
| HttpOnly       | Yes                  |
| SameSite       | Lax                  |

> Cookies are available on the **next request**, per HTTP standards.



## Non-Essential Cookie Cleanup

When consent is declined, all cookies are removed **except**:

* Craft CSRF token
* PHP session cookie
* Consent cookie (optional)

This behavior supports GDPR / ePrivacy requirements.



## Security Notes

* CSRF protection is enforced
* Uses Yii `Cookie` objects
* No direct `setcookie()` calls
* No server-side storage of personal data



## Typical Usage Flow

1. Show cookie banner if no preference exists
2. User accepts or declines
3. Consent cookie is stored
4. Scripts load conditionally via Twig
5. Non-essential cookies are removed if declined



## License

MIT License
[https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT)


## Author

**Amit3151**
https://github.com/Amit3151


## Changelog

### 1.0.0

* Initial release
* Craft CMS 5 compatible
* Consent accept / decline / reset
* Twig helpers for frontend control
