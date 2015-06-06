=== WooCommerce Jetpack ===
Contributors: algoritmika
Donate link: http://algoritmika.com/donate/
Tags: woocommerce,woocommerce jetpack,custom price labels,call for price,currency symbol,remove sorting,remove old product slugs,add to cart text,order number,sequential order numbering,email pdf invoice,pdf invoice,pdf invoices,already in cart,empty cart,redirect to checkout,minimum order amount,customize checkout fields,checkout fields,email,customize product tabs,product tabs,related products number,empty cart,redirect add to cart,redirect to checkout,product already in cart,custom payment gateway,payment gateway icon,auto-complete all orders,custom order statuses,custom order status,remove text from price,custom css,hide categories count,hide subcategories count,hide category count,hide subcategory count,display total sales,custom product tabs,remove product tab,payment gateway fee,
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 2.1.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Supercharge your WordPress WooCommerce site with these awesome powerful features.

== Description ==

WooCommerce Jetpack is a WordPress plugin that supercharges your site with awesome powerful features. Features are absolutely required for anyone using excellent WordPress WooCommerce platform.

= Features =
* Custom Price Labels - Create any custom price label for any product.
* Call for Price - Create any custom price label, like *Call for price*, for all products with empty price.
* Currencies - Add all world currencies, change currency symbol.
* PDF Invoicing and Packing Slips - Add PDF invoices for store owners and for customers. Automatically email PDF invoices to customers (and limit attaching invoice to selected payment gateways only).
  Module includes proforma invoices, proforma slips, with separate numbering for each document type.
  Extended templates (and shortcodes), styling, page, header and footer, filename, emailing etc. customization options.
* Orders - Sequential order numbering, custom order number prefix, date prefix, suffix and number width. Set minimum order amount.
* More Sorting Options - Add more sorting options or remove sorting at all (including WooCommerce default).
* Payment Gateways - Change icons (images) for all default (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal) WooCommerce payment gateways.
  Add payment gateways fees to any default WooCommerce gateway.
  Add and customize up to 10 additional custom off-line payment gateways.
* Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Shipping - Hide shipping when free is available.
* Emails - Add another email recipient(s) to all WooCommerce emails.
* Product Listings - Change display options for shop and category pages: show/hide categories count, exclude categories, show/hide empty categories.
* Product Info - Add more info to product on category or single pages. Change related products number, select to relate by tag and/or by category.
* Product Tabs - Add custom product tabs - globally, per category or per product. Customize or completely remove WooCommerce default product tabs.
* Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Add to Cart - Change text for add to cart buttons for each product type, on per category or per product basis. Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Old Slugs - Remove old product slugs.
* Bulk Price Converter tool.
* Prices and Currencies by Country - Change product price and currency by customer’s country - globally or per product. Customer’s country is detected automatically by IP. Automatic currency exchange rates updates.
* Bulk SKUs generator tool.
* Different Currency for External Products.
* Another custom CSS tool, if you need one.

= Feedback =
* We are open to your suggestions and feedback - thank you for using or trying out one of our plugins!
* If you have any ideas how to upgrade the plugin to make it better, or if you have ideas about the features that are missing from our plugin, please [fill the form](http://woojetpack.com/submit-idea/).
* For support visit the [contact page](http://woojetpack.com/contact-us/).

= More =
* Visit the [WooCommerce Jetpack plugin page](http://woojetpack.com/)

= Available Translations =
* `FR_fr` by Jean-Marc Schreiber.

== Installation ==

1. Upload the entire `woocommerce-jetpack` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Jetpack

== Frequently Asked Questions ==

= How to unlock those some features settings that are locked? =

To unlock all WooCommerce Jetpack features, please install additional <a href="http://woojetpack.com/plus/">WooCommerce Jetpack Plus</a> plugin.

== Screenshots ==

1. Plugin admin area.

== Changelog ==

= 2.1.3 - 24/02/2015 =
* Dev/Fix - Empty cart - new position hook added and div style field unlocked.
* Dev - Price by Country - Using `internal_wc` only.
* Dev - Orders Shortcodes - `after_discount` attribute added to `[wcj_order_subtotal]` shortcode (also `[wcj_order_subtotal_after_discount]` shortcode).
* Dev - Products Shortcodes - Shortcodes added: `[wcj_product_sku]`, `[wcj_product_title]`, `[wcj_product_weight]`.
* Fix - PDF Invoicing - Bug causing `font_family` and `font_size` settings wrongly taken from PDF Invoices V1 instead of V2, fixed.
* Fix - Custom Order Statuses - Bug in reports, fixed.

= 2.1.2 - 22/02/2015 =
* Fix - PDF Invoicing - `on_create` bug fixed. This caused creating all document on new order only.
* Dev - PDF Invoicing - Page format (paper size) option added to *Page Settings* submodule.
* Dev - Products Shortcodes - Attribute `hide_currency` added to Products Shortcodes (`[wcj_product_price]`).
* Dev - Products Shortcodes - `[wcj_product_price]` - variable product handling (as price range), added.
* Dev - Custom Checkout Fields - *label* and *placeholder* can now contain HTML tags (text changed to textarea in settings).

= 2.1.1 - 18/02/2015 =
* Fix - Orders Shortcodes - Shortcodes compatibility with PDF Invoices v1 module.
* Dev - Orders Shortcodes - Added `hide_if_zero` checking for `_order_item_total_` shortcodes.

= 2.1.0 - 17/02/2015 =
* Dev - **PDF Invoicing and Packing Slips** - Module added.
  Module includes proforma invoices, proforma slips, with separate numbering (and invoices renumerate tool) for each document type.
  Extended templates (and shortcodes), styling, page, header and footer, filename, emailing etc. customization options.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Prices and currencies by country *per product* functionality added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Major code refactoring.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Admin debugging functionality added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Empty price functionality added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Automatic currency exchange rates (i.e. wp cron job) updates, added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Internal DB since WooCommerce 2.3, added.
* Dev - PRODUCTS - **Product Info** - Option to list product IDs to exclude from product info, added.
* Dev - PRODUCTS - **Product Info** - Option to change single product's thumbnails columns number, added.
* Dev - PRODUCTS - **Product Input Fields** - Hiding placeholder on focus, added.
* Dev - PRODUCTS - **Product Input Fields** - Global and local modules merged into single module.
* Dev - PRICE LABELS - **Call for Price** - Added `do_shortcode` on all empty price outputs.
* Dev - CHECKOUT - **Payment Gateways** - Added `do_shortcode` in payment gateway's "thank you" page and email instructions.
* Dev - MISC. - **General** - *Admin Tools* (logging) added.
* Dev - PRODUCTS - **Related Products** - Moved to new module. *Relate by tag and/or category* options (idea by Alexys) added.
* Fix - ORDERS - "Extended fix" in `add_custom_order_statuses_to_reports()` for WooCommerce v.2.3 compatibility.
* Fix - ORDERS - **Order Numbers** - Now generating number when creating order from admin backend.
* Fix - **PDF Invoices** - Bug in `generate_pdf()` (`order_id` not defined when first checking), fixed.
* Fix - **PDF Invoices** - `maybe_unserialize` instead of `serialize` in `add_custom_checkout_fields_to_pdf()`.
* Fix - PRODUCTS - **SKU** - Fix in *Autogenerate SKUs* tool: now SKUs are properly generated for larger (e.g. more that two thousand) quantity of products.
* Fix - CURRENCIES - "Undefined index: KIP" notice bug fixed.

= 2.0.13 - 12/02/2015 =
* Fix - ORDERS - Quick fix in `add_custom_order_statuses_to_reports()` for WooCommerce v.2.3 compatibility.

= 2.0.12 - 14/01/2015 =
* Dev - **Reports** - WooJetpack Orders Reports: More Ranges.
* Fix - CURRENCIES - **Prices and Currencies by Country** - Slicing the array in `update_database()`.
* Fix - PRODUCTS - **SKU** - Fix in `set_product_sku` function. Bug caused SKU *not* autogenerating when adding new product. Reported by Gary.

= 2.0.11 - 08/01/2015 =
* Dev - CHECKOUT - **Custom Checkout Fields** - Filter for custom checkout fields for "WooCommerce - Store Exporter" plugin, added.
* Fix - ORDERS - Custom Statuses added to WooCommerce reports.
* Fix - CURRENCIES - **Prices and Currencies by Country** - `$wpdb->prefix` fix. Reported by John.
* Fix - **Reports** - `wc_get_product` instead of `new WC_Product`.
* Fix - **PDF Invoices** - `wc_get_product` instead of `new WC_Product`.

= 2.0.10 - 04/01/2015 =
* Fix - PRODUCTS - **Product Info** - `id` option bug, fixed.
* Fix - CURRENCIES - **Currencies** - Empty symbol bug, fixed.
* Dev - CHECKOUT - **Custom Checkout Fields** - Option to set *clear* after the field, added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - New table `woojetpack_country_ip` added to DB.

= 2.0.9 - 01/01/2015 =
* Fix - PRODUCTS - **Bulk Price Coverter** - Not updating on empty price (was setting to zero before).

= 2.0.8 - 30/12/2014 =
* Dev - PRODUCTS - **SKUs** - *Variable Products Variations SKUs* handling options added.

= 2.0.7 - 28/12/2014 =
* Dev - PRODUCTS - **Bulk Price Coverter** - Initial module release.
* Dev - CHECKOUT - **Custom Checkout Fields** - Option to add custom checkout fields to emails, added.
* Dev - CHECKOUT - **Payment Gateways** - *Additional emails on new order* options added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - `booking_form_calculated_booking_cost` hook added. Partial compatibility with Bookings plugin.
* Fix - CURRENCIES - **Prices and Currencies by Country** - On `round = none` rounding by precision set in WooCommerce.
* Fix - Payment Gateways - `wp_register_script` moved to `init`. This caused warning generation on some servers.

= 2.0.6 - 20/12/2014 =
* Fix - CART - **Add to Cart per Category** - `is_object` check added. This caused warning generation on some servers.
* Fix - **PDF Invoices** - `get_line_subtotal` instead of `get_line_total`. This fixes the bug with item's discount in invoice.
* Fix - **PDF Invoices** - "Shipping and Discount as item" fix.
* Fix - **PDF Invoices** - Total weight shortcode quantity bug, fixed.
* Fix - CURRENCIES - **Prices and Currencies by Country** - Report currency menu had only GBP and USD, fixed.
* Dev - CURRENCIES - **Prices and Currencies by Country** - *Price Rounding* option added.
* Dev - CURRENCIES - **Prices and Currencies by Country** - *Country by IP detection method* option added.
  Alternative method added: `api.host.info`.
* Dev - CURRENCIES - **Prices and Currencies by Country** - Debug info added.
* Dev - PRODUCTS - **SKUs**.
* Dev - **PDF Invoices** - shadowed font option added.

= 2.0.5 - 16/12/2014 =
* Fix - CURRENCIES - **Prices and Currencies by Country** - Calls to `str_getcsv` removed (as this function needs PHP 5.3.0 minimum).

= 2.0.4 - 16/12/2014 =
* Dev - **PDF Invoices** - *Family font* option added.
* Fix - **Reports** - Country sets fixed.
* i18n - POT file updated.

= 2.0.3 - 13/12/2014 =
* Fix - CURRENCIES - **Prices and Currencies by Country** - Problems identifying country, when spaces are used in group lists.

= 2.0.2 - 12/12/2014 =
* Fix - Temporary disabled all `gettext` (in Cart and Shipping Calculator).

= 2.0.1 - 12/12/2014 =
* Dev - CURRENCIES - **Prices and Currencies by Country** - Added no retries on unsuccessful DB update.

= 2.0.0 - 12/12/2014 =
* Fix - CHECKOUT - **Payment Gateways** - *Fee value* step changed to two digits after point in *Payment Gateways Fees Options*. Suggested by Patryk.
* Fix - PRODUCTS - Sorting - *Remove All Sorting* setting in "WooCommerce > Settings > Products" was disappearing after checkbox is disabled, fixed.
* Dev - PRODUCTS - **Product Input Fields** - Fields to fill before adding product to cart. Idea by Mangesh.
  Fields can be added globally (i.e. for all products), or on per product basis.
  Currently only fields of `text` type are available.
* Dev - PRODUCTS - **Product Info** - Wordpress shortcodes added for every WooJetpack shortcode.
  WooJetpack shortcodes are now depreciated and will be removed in future releases. See v.1.9.0 changelog for more details.
  This covers Wordpress.org Support Forum thread by dudemcpek - https://wordpress.org/support/topic/not-displaying-shortcodes.
* Dev - CART - **Add to Cart per Category** - Add to cart button text on *per category* basis (single or category view). Idea by Craig.
* Dev - CART - **Add to Cart per Product** - Custom add to cart button text on *per product* basis (single or category view). Idea by Craig.
* Dev - CART - **Cart** - Option to change position of `Empty cart` button. Suggested by Patryk.
* Dev - CART - **Cart** - Option to hide "Note: Shipping and taxes are estimated and ..." message on Cart page, added.
* Dev - SHIPPING - **Shipping Calculator** - Customize shipping calculator on cart page. Idea by Xavier.
* Dev - CURRENCIES - **Prices and Currencies by Country**, added. Idea by Illona.
* Dev - CURRENCIES - **Different Currency for External Products**, added. Idea by Leon, Krishan.
* Dev - CURRENCIES - **Currencies** - *Hide currency symbol* option, added.
* Dev - CHECKOUT - **Custom Checkout Fields**, added. Idea by: Patryk, Tom, https://wordpress.org/support/topic/delivery-date-picker.
* Dev - CHECKOUT - **Payment Gateways** - Payment fees - Maximum and/or minimum cart amount for adding fee option, added.
* Dev - CHECKOUT - **Payment Gateways** - Option to set *default order status* for custom gateway, added.
* Dev - ORDERS - **Custom Statuses** - *Default Order Status* option added. Idea by Patryk.
* Dev - ORDERS - **Bulk Price Changer** - Custom order number date suffix. Idea by Patryk.
* Dev - ORDERS - **Bulk Price Changer** - Option to *enabled/disable sequential order numbers*, added. This will let use only *custom order number width, prefixes and suffixes*.
* Dev - **PDF Invoices** - Shortcodes.
* Dev - **PDF Invoices** - Custom billing and shipping checkout fields are now added to PDF. This covers the request to add shipping phone to PDF by Dennys.
* Dev - PRICE LABELS - **Custom Price Labels** - *Global* price labels between regular and sale. Suggested by Roman.
* Dev - PRICE LABELS - **Call for Price** - Empty price hook moved to `init`. That lets set "priority higher than maximum".
  This caused *Call for Price* not to work properly with other similar plugins installed. Reported by Patryk.
* Dev - MISC. - Reports - *Understocked* report added. Idea by Ireneusz.
* i18n - `FR_fr` French translation updated. Translated by Jean-Marc.
* i18n - POT file updated.
* Tweak - PRICE LABELS - **Custom Price Labels** - Hide custom price labels if the Dashboard or the administration panel is displayed. Suggested by Jean-Marc.
* Tweak - ORDERS - **Bulk Price Changer** - Moved to separate module.
* Tweak - CART - **Add to Cart** - "Only *local* URLs are accepted" info updated in *Add to cart redirect*.

= 1.9.1 - 13/11/2014 =
* Fix - **Payment Gateways** - Bug causing displaying *fee type* as *percent* instead of *fixed*, fixed.
  This did not affect Plus version.
* Dev - French translation updated.
* Dev - POT file updated.
* Tweak - Submenus added in admin's WooCommerce > Settings > Jetpack.

= 1.9.0 - 10/11/2014 =
* Feature Upgraded - **Payment Gateways** - Payment Gateways Fees, added. Idea by Daniele.
  Also made changes to *PDF Invoices* - fees now displayed in invoice.
* Feature Upgraded - **Product Tabs** - Options added for: hiding global tabs for an products and/or categories list. Idea by Gary.
  Another similar option added: show global tab *only* for products and/or categories list.
* Feature Upgraded - **Sorting** - Sorting products by stock quantity, added. Idea by Fred.
* Feature Upgraded - **PDF Invoices** - Emailing PDF as attachment for selected payment methods only option added. Idea by Jen.
* Feature Upgraded - **PDF Invoices** - Option to add shipping address to the invoice, added. Idea by Justine.
* Feature Upgraded - **Orders** - Bulk Price Changer - Custom order number suffix added. Idea by Patryk.
* Feature Upgraded - **Add to Cart** - Changing *add to cart* button text for products with zero and/or empty price (suggested by Patryk) option added.
  Products with *zero price* are covered for archive (category) and single views.
  Products with *empty price* only for archives (single view does not contain add to cart button at all, so nothing to cover).
* Feature Upgraded - **Product Info** - Added `[wcjp_list_attribute]` shortcode.
  Now it is possible to display product's attribute values list (e.g. to list the different colour variations of a product). Idea by Tony.
  This is the right way to display product info, so WooJetpack shortcodes (introduced in v.1.8.2) are depreciated and will be removed in feature releases.
  Shortcode has `visibility` parameter which gives the possibility to show shortcode's product info to admin only.
* Feature Upgraded - **Product Info** - `%stock_quantity%` WooJetpack Shortcode added.
* Dev - **Product Info** - `the_content` filter added to result. Now shortcodes will be displayed properly.
* Tweak - **Add to Cart** and **Sorting** - Removed *enable* checkboxes in admin settings. Now need to leave the value empty to disable.
* Tweak - Added some info to *Old Slugs*, *Custom Statuses* and *Product Tabs* features. Suggested by Patryk.
* New Feature - **Reports** - Various sales, stock, customers etc. reports. *BETA* version.

= 1.8.2 - 01/11/2014 =
* Fix - Orders - Custom Order Statuses - Bug causing fail on changing status with slug more that 17 characters, fixed by adding length check on adding custom status. Reported by Patryk.
* Fix - Product Tabs - Priority was not working in custom local tabs, fixed.
  Also added default priority in custom local product tabs.
* Fix - *Settings* link in *WooCommerce > Jetpack Settings* was wrong, fixed.
  This caused bug, where on non-root WordPress instalations *Settings* link gave 404 error. Reported by Brian.
* Fix - Product Tabs - Wrong default priority for WooCommerce Standard Product Tabs, fixed.
  *Reviews Tab* priority was 20 (wrong), changed to 30 (good), *Additional Information Tab* 30 and 20 accordingly. Reported by Patryk.
* Feature Upgraded - Product Info - Major upgrade: added new info options with separate lines.
  Also added about 20 new WooJetpack Shortcodes, including:
  %price%, %price_excluding_tax% (suggested by Josh),
  %stock_availability% (by https://wordpress.org/support/topic/custom-tabs-1),
  %time_since_last_sale%, %weight%, %list_attributes% etc.
  For full list of short codes, please visit http://woojetpack.com/features/product-info/
* Feature Upgraded - Product Listings - Option to change default WooCommerce behavior on displaying all products if none categories are dispalyed.
  Now it's possible to disable displaying the products. Suggested by Xavier.
* Feature Upgraded - PDF Invoices - Order date and time added. Suggested by https://wordpress.org/support/topic/order-time

= 1.8.1 - 24/10/2014 =
* Fix - PDF Invoices - Variation(s) name was not showing in invoice, fixed.
  Reported by https://wordpress.org/support/topic/item-description
* Feature Upgraded - PDF Invoices - Now shortcodes are displayed in invoice's additional header and footer.
  Idea by https://wordpress.org/support/topic/displaying-short-codes
* Feature Upgraded - PDF Invoices - Additional header option added.
* Feature Upgraded - PDF Invoices - *Item Name Additional Info* (e.g. SKU) option added to invoice.
  Idea by https://wordpress.org/support/topic/item-description

= 1.8.0 - 17/10/2014 =
* New Feature - Product Tabs - **Custom product tabs** - global or per product.
  Related *product tabs* options were also moved to this feature from *Product Info*.
* Dev - `date` function changed to `date_i18n`. Suggested in https://wordpress.org/support/topic/pdf-invoices-date-bug
  Changes affected the *Orders* and *PDF Invoices* features (this covers request from Jean-Marc for international date formats in *PDF Invoices*).

= 1.7.9 - 16/10/2014 =
* Fix - Custom Price Labels - Hiding price labels on cart page didn't work, fixed. Suggested by Paolo.

= 1.7.8 - 15/10/2014 =
* Fix - Product Listings - Hide empty not working, fixed. Suggested by Rene.
  This was caused by changes in WooCommerce code.
* Feature Upgraded - Custom Price Labels - Option for **hiding labels on cart page only**, added. Idea by Paolo.

= 1.7.7 - 13/10/2014 =
* Fix - Custom Price Labels - Bug causing setting checkboxes back to *on*, fixed. Suggested by Erika.
* Fix - Custom Price Labels - *Migrate from Custom Price Labels (Pro)* tool - new since Custom Price Labels plugin data was missing, fixed. Suggested by Paolo.

= 1.7.6 - 09/10/2014 =
* Fix - Custom Price Labels - Bug causing setting all product's checkbox labels to off, fixed.
  Bug was not resetting Text labels however (i.e. checkboxes only). Bug was in code since v.1.0.0.
  The bug caused resetting all product's checkbox labels to off, when generally any product save, except "normal" conditions (i.e. saving through standard edit), happened:
  - when any other plugin used `wp_update_post` function,
  - when user updated product via Quick Edit,
  - could be more possible conditions.
* Fix - Custom Price Labels - "Migrate from Custom Price Labels" tool info added to tools dashboard.
* Dev - Custom Price Labels - Labels settings in product edit rearranged (to `table`).
* Dev - Tools Dashboard rearranged (to `table`).
* Dev - `FR_fr` translation updated by Jean-Marc Schreiber.

= 1.7.5 - 08/10/2014 =
* Feature Upgraded - Custom Price Labels - "Global labels" section extended: `add after price`, `add before price`, `replace in price`.
  `Remove from price` code also have been moved (and now in one place with all Global Labels) - before that it was called multiple times, fixed.
* Dev - Custom Price Labels - "Migrate from Custom Price Labels (Pro)" tool added. Suggested by Paolo.

= 1.7.4 - 07/10/2014 =
* Fix - Emails - Bcc and Cc options not working, fixed. Reported by Helpmiphone.
* Fix - Orders - Minimum order amount - "Stop customer from seeing the Checkout page..." option was not working properly: was redirecting to Cart after successful checkout, fixed.

= 1.7.3 - 04/10/2014 =
* Fix - Product Info - Product Info on Single Product Page - Missing Plus message added. Reported by Manfred.
* Feature Upgraded - Payment Gateways - Option to add up to 10 additional custom payment gateways, added. Idea by Kristof.
* Dev - French `FR_fr` translation added. Translation by Jean-Marc Schreiber.

= 1.7.2 - 03/10/2014 =
* Fix - Product Info - `%total_sales%` fixed and enabled.

= 1.7.1 - 02/10/2014 =
* Fix - Product Info - `%total_sales%` is temporary disabled.
  This was causing "PHP Parse error" on some servers (PHP 5.3), now fixed. Reported by Xavier.
  Also reported in https://wordpress.org/support/topic/parse-error-syntax-error-unexpected-expecting-2

= 1.7.0 - 02/10/2014 =
* Fix - Payment Gateways - Instructions were not showing (suggested by Jen), fixed.
* Feature - Product Listings - Options added (separately for "Shop" and "Categories" pages): show/hide categories count, exclude categories (idea by Xavier), show/hide empty categories.
  This will work only when "Shop Page Display" and/or "Default Category Display" in "WooCommerce > Settings > Products > Product Listings" is set to "Show subcategories" or "Show both".
  All new options fields are also added (duplicated) to "WooCommerce > Settings > Products > Product Listings".
* Feature Upgraded - Payment Gateways - Instructions for emails option added (i.e. separated from instructions on thank you page).
* Feature Upgraded - Orders - Minimum order amount - Stop customer from seeing the checkout page if below minimum order amount (in this case the customer redirected to Cart page). Idea by Augen.
* Feature Upgraded - Product Info - Additional product info (separately for "Single" and "Archive" pages): text, position and priority options added.
  First "Product Info Shortcodes" added: %sku% for SKU (idea by Xavier) and %total_sales% for Total Sales.

= 1.6.2 - 25/09/2014 =
* Feature Upgraded - Orders - Orders Numbers - Additional custom date prefix added. Suggested by Sergio.
  Value is passed directly to PHP `date` function, so most of PHP date formats can be used.
  Visit PHP `date` <a href="http://php.net/manual/en/function.date.php">function page</a> for more information on valid date formats.
  The only exception is using `\` symbol in date format, as this symbol will be excluded from date (that is because of WooCommerce default option saving mechanism).

= 1.6.1 - 23/09/2014 =
* New Feature - General - Another custom CSS tool.
  This was added because of the problem with color of price matching the background in minimum order amount message (suggested by Augen), which can be fixed with custom CSS.
* Dev - Orders - Minimum order amount - `textarea` instead of `text` option type. Now it is possible to add tags (e.g. `<span class="your_class"></span>`) to customers messages.

= 1.6.0 - 22/09/2014 =
* Fix - PDF Invoices - Wrong headers for PDF sent, fixed.
  This was previously causing a bug when `.html` file extension was wrongly added to PDF. Suggested by Pete (reported from Safari, Mac).
* Feature Upgraded - Custom Price Labels - Labels for Item price on Cart page included. Idea by Stephanie.
* Feature Upgraded - Custom Price Labels - Labels for Composite products included. Idea by Pete.
* Dev - Custom Price Labels - All price filters added to `prices_filters` array.

= 1.5.3 - 20/09/2014 =
* Fix - Smart Reports beta version enabled too soon, fixed.

= 1.5.2 - 20/09/2014 =
* Fix - Emails - Bug causing `call_user_func_array()` warning, fixed. Suggested by Andrew.
* Dev - New WooCommerce Jetpack Dashboard in admin settings.

= 1.5.1 - 14/09/2014 =
* Dev - Custom Price Labels - `textarea` instead of `<input type="text">`.
* Dev - Orders - Custom Order Statuses - `postbox` added instead of simple form.
* Upgrade Feature - PDF Invoices - PDF invoice as attachment file in customer's email (order completed). Idea by Jen.
* Dev - PDF Invoices - If displaying shipping as item, option for adding shipping method text, added. Suggested by Tomas.

= 1.5.0 - 13/09/2014 =
* Dev - Orders - Renumerate orders tool compatibility with WooCommerce 2.2.x.
* Dev - Orders - Custom Order Statuses compatibility with WooCommerce 2.2.x.
* Dev - Orders - Renumerate orders tool moved to WooCommerce > Jetpack Tools.
* Fix - PDF Invoices - `Order Shipping Price` position in `Totals` on admin settings page, fixed.
* Dev - PDF Invoices - Save as pdf option added.
* Fix - PDF Invoices - Bug with invoice PDF file name, fixed.

= 1.4.0 - 07/09/2014 =
* Dev - Custom Price Labels - Support for price labels showing on Pages, added. Suggested by Axel.
* Fix - PDF Invoices - Bug with some item table columns not showing, fixed. Suggested by Tomas.
* Dev - PDF Invoices - Discount as separate item option added.
* Dev - PDF Invoices - Shipping as separate item option added. Suggested by Tomas.
* Dev - Old Slugs and Custom Order Statuses tools moved to WooCommerce > Jetpack Tools.

= 1.3.0 - 25/08/2014 =
* Feature Upgraded - PDF Invoices - Major upgrade: single item price, item and line taxes, payment and shipping methods, additional footer, font size, custom css added.

= 1.2.0 - 17/08/2014 =
* Feature Upgraded - Orders - Auto-complete all orders option added.
* Feature Upgraded - Orders - Custom Order Statuses added.
* Feature Upgraded - Custom Price Labels - Added global remove text from price option.
* Feature Upgraded - Custom Price Labels - Added compatibility with bookable products. Suggested by Axel.
* Dev - Links to Jetpack settings added to plugins page and to WooCommerce back end menu.
* Feature Upgraded - Checkout - Customizable "Place order" ("Order now") button text.

= 1.1.7 - 12/08/2014 =
* Dev - Call for Price - "Hide sale tag" code fixed.
* Feature Upgraded - Call for Price - Separate label to show for related products.
* Dev - PDF Invoices - Text align to right on cells with prices.
* Dev - PDF Invoices - "PDF" renamed to "PDF Invoice" (in orders list).

= 1.1.6 - 11/08/2014 =
* Fix - PDF Invoices - Bug with subtotal calculation (discounts were not included), fixed.

= 1.1.5 - 11/08/2014 =
* Dev - PDF Invoices - "Save as..." disabled (in orders list).
* Feature Upgraded - PDF Invoices - New fields added: line total excluding tax, subtotal, shipping, discount, taxes.

= 1.1.4 - 10/08/2014 =
* Fix - Sorting - "Remove all sorting" bug (always enabled), fixed (second time).
* Dev - Product Info - Related products: "columns" option added.

= 1.1.3 - 09/08/2014 =
* Fix - Payment Gateways - "Warning: Invalid argument supplied for foreach() in..." bug fixed.
* Feature Upgraded - Call for Price - Different labels for single/archive/home.

= 1.1.2 - 08/08/2014 =
* Dev - PDF Invoices - Icons at orders list changed.
* Feature Upgraded - Payment Gateways - Icons for default WooCommerce gateways (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal). Accessible also via WooCommerce > Settings > Checkout Options.
* Feature Upgraded - Payment Gateways - Custom Payment Gateway upgraded: Shipping methods, Virtual product, Min cart total option, Icon option.
* Dev - Feature "Custom Payment Gateway" renamed to "Payment Gateways"
* Dev - Move needed functions from Plus to standard version.

= 1.1.1 - 06/08/2014 =
* Feature Upgraded - Custom Price Labels - More visibility options added: hide for main variable product price or for each variation.
* Feature - Custom Payment Gateway - Simple custom offline payment gateway.
* Dev - Move needed functions from Plus to standard version.
* Fix - Custom Price Labels - Bug with main enable/disable checkbox, fixed.
* Fix - Checkout - Bug with default values, fixed.
* Dev - Enable/disable checkbox added to Add to cart feature.
* Dev - Function wcj_get_option removed.

= 1.1.0 - 24/07/2014 =
* Dev - PDF Invoices - Icons instead of text at orders list.
* Fix - Currencies - Wrong readonly attribute for text field on WooCommerce > Settings > General, affecting Plus version, fixed.
* Feature Upgraded - Orders - Set minimum order amount.
* Feature - Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Feature - Shipping - Hide shipping when free is available.
* Feature - Emails - Add another email recipient(s) to all WooCommerce emails.
* Feature - Product Info - Customize single product tabs. Change related products number.
* Feature - Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Feature Upgraded - Add to Cart - Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Dev - Feature "Orders Numbers" renamed to "Orders".

= 1.0.6 - 15/07/2014 =
* Feature - PDF Invoices - PDF invoices for store owners and for customers.

= 1.0.5 - 18/06/2014 =
* Feature - Bulk Price Changer - Sequential order numbering, custom order number prefix and number width.

= 1.0.4 - 15/06/2014 =
* Fix - Add to cart text - on archives now calling the right function.

= 1.0.3 - 15/06/2014 =
* Feature - Add to cart text by product type.

= 1.0.2 - 14/06/2014 =
* Dev - Added loading plugin textdomain.

= 1.0.1 - 13/06/2014 =
* Fix - Error with Custom Price Labels feature, affecting Plus version, fixed.

= 1.0.0 - 13/06/2014 =
* Feature - Custom Price Labels – Create any custom price label for any product.
* Feature - Call for Price – Create any custom price label, like "Call for price", for all products with empty price.
* Feature - Currencies – Add all world currencies, change currency symbol.
* Feature - More Sorting Options – Add more sorting options or remove sorting (including default) at all.
* Feature - Old Slugs – Remove old product slugs.
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
