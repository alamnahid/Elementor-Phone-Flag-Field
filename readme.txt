=== Nahid Phone Flag Field for Elementor ===
Contributors: wpnahid
Tags: phone, flag, country code, elementor, international
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
Requires Plugins: elementor
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds an international phone flag and country code selector to Elementor Pro Tel fields.

== Description ==

Nahid Phone Flag Field for Elementor automatically enhances any Tel field in
Elementor Pro forms with a country flag and dial code selector.

Features:

* Country flag and dial code selector on the left side of the phone field
* Searchable country dropdown (type US, PK, IN or full country name)
* Auto-detects visitor country from IP address
* Full phone number with dial code submitted and emailed together
* Admin settings to set default country, allowed countries, excluded countries

= Requirements =
* WordPress 5.8+
* Elementor Pro (any version with Form widget)

= Third Party Services =

This plugin uses ipapi.co to auto-detect the visitor country from their IP address.
Service: https://ipapi.co
Privacy Policy: https://ipapi.co/privacy/
This is only used when Auto-Detect is enabled in plugin settings.

This plugin includes the intl-tel-input library.
Source: https://github.com/jackocnr/intl-tel-input
License: MIT (GPL compatible)
The library is bundled locally. No CDN is used.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/
2. Activate the plugin via the Plugins menu in WordPress
3. Go to Settings > Nahid Phone Flag Field to configure
4. Add a Tel field to any Elementor Pro form — the flag picker appears automatically

== Frequently Asked Questions ==

= Does this work without Elementor Pro? =
No. The Form widget requires Elementor Pro.

= How is the phone number submitted? =
The dial code and number are merged into one value, for example +8801796281914.

= Can I limit which countries appear? =
Yes. Enter comma-separated country codes in Allowed or Excluded Countries in settings.

== Screenshots ==

1. Flag picker in an Elementor form
2. Country search dropdown
3. Admin settings page

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
```

---