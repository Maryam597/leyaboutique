=== Boxtal - Shipping solution ===
Contributors: Boxtal
Tags: shipping, delivery, parcel point, Mondial Relay, Chronopost
Requires at least: 4.6
Tested up to: 6.8
Requires PHP: 5.6.0
Stable tag: 1.3.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Negotiated rates for all types of shipping (home, relay, express, etc.). No subscription, no hidden fees.

== Description ==

Your orders are synchronized with your Boxtal account, where you can automate shipping rules to generate your shipping labels.

Ship with all types of carriers (Colissimo, Mondial Relay, Chronopost, Colis Privé, UPS, …), with or without insurance, options, ... You benefit from negotiated rates, without volume conditions, without subscription, without hidden costs.

Tracking is automatically synchronized with your orders and is available at any time in your customer’s account pages.

A single invoice for all your shipments and a single customer service to manage all delivery issues.

Add a parcel point map to your checkout.

This plugin rely on these third party services:
- Maplibre gl: https://github.com/maplibre/maplibre-gl-js
- tom-select: https://github.com/orchidjs/tom-select

Tools used to compile and minify this plugin's files:
- css: gulp, gulp-less, gulp-clean-css
- js: gulp, gulp-babel, gulp-terser

== Installation ==

= Minimum requirements =
* WooCommerce version: 2.6.14 or greater
* WordPress version: 4.6 or greater
* Php version: 5.6.0 or greater

= Step by step guide =

* Have a look here: https://help.boxtal.com/fr/en/article/getting-started-bc-wc

== Screenshots ==

1. Synchronize your orders, save time
2. Ship with the best carriers
3. A single invoice, a single customer service
4. A parcel point map in your checkout

== Changelog ==

2025-05-27 - version 1.3.5
* Fixed compatibility issues when using a mix of legacy and block pages
* Shipping order synchronization no longer return orders older than 90 days

2025-04-29 - version 1.3.4
* Fixed compatibility issues with parcel point injection on block checkout
* Added logs on boxtal pricing line save error

2024-12-09 - version 1.3.3
* Fixed translation issues with woocommerce blocks
* Replaced mapbox with maplibre
* Maplibre token is now loaded when opening the parcel point map

2024-12-05 - version 1.3.2
* Fixed frontoffice translation issue

2024-10-31 - version 1.3.1
* Updated onboarding url
* Fixed an issue with botal_connect_print_parcelpoint hook not printing the parcel point

2024-09-05 - version 1.3.0
* Fixed woocommerce block and legacy detection on cart and checkout page
* Fixed typos