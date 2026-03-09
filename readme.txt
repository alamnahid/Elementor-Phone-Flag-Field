=== Phone Flag Field for Elementor ===
Contributors: wpnahid
Tags: phone, elementor, flag, country code, international
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds an international phone flag and country code selector to Elementor Pro Tel fields.

== Description ==

Phone Flag Field for Elementor automatically enhances any Tel field in Elementor Pro forms with:

* Country flag and dial code selector
* Searchable country dropdown (type US, PK, IN or full country name)
* Auto-detects visitor country from IP
* Full phone number with dial code submitted together
* Admin settings to set default country, allow or exclude countries

= Requirements =
* WordPress 5.8+
* Elementor Pro (any version with Form widget)

= Third Party Services =
This plugin uses ipapi.co to auto-detect the visitor's country from their IP address.
Service URL: https://ipapi.co
Privacy Policy: https://ipapi.co/privacy/
This is only used when Auto-Detect is enabled in plugin settings.

This plugin includes the intl-tel-input library (https://github.com/jackocnr/intl-tel-input)
licensed under the MIT License.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/
2. Activate the plugin via the Plugins menu in WordPress
3. Go to Settings > Phone Flag Field to configure
4. Add a Tel field to any Elementor Pro form - the flag picker appears automatically

== Frequently Asked Questions ==

= Does this work without Elementor Pro? =
No. The Form widget requires Elementor Pro.

= How is the phone number submitted? =
The dial code and phone number are merged into one value before being stored and emailed.

= Can I limit which countries appear? =
Yes. In Settings > Phone Flag Field enter comma-separated country codes in Allowed or Excluded Countries.

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
