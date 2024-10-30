=== LTL Freight Quotes - Daylight Edition ===
Contributors: enituretechnology
Tags: eniture,Daylight,,LTL freight rates,LTL freight quotes, shipping estimates
Requires at least: 6.4
Tested up to: 6.6
Stable tag: 2.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Real-time LTL freight quotes from Daylight. Fifteen day free trial.

== Description ==

Daylight is a privately owned company headquartered in Elmont, NY. Its core business is less-than-truckload (LTL)freight, but it provides a variety of other freight and logistics services. If you don’t have an Daylight account, contact them at 855-656-1982.

**Key Features**

* Displays negotiated LTL shipping rates in the shopping cart.
* Provide quotes for shipments within the United States.
* Custom label results displayed in the shopping cart.
* Display transit times with returned quotes.
* Product specific freight classes.
* Support for variable products.
* Define multiple warehouses.
* Identify which products drop ship from vendors.
* Product specific shipping parameters: weight, dimensions, freight class.
* Option to determine a product's class by using the built in density calculator.
* Option to include residential delivery fees.
* Option to include fees for lift gate service at the destination address.
* Option to mark up quoted rates by a set dollar amount or percentage.
* Works seamlessly with other quoting apps published by Eniture Technology.

**Requirements**

* WooCommerce 6.4 or newer.
* Daylight customer number.
* Your username and password to dylt.com.
* A API key from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* Your Daylight customer number.
* Your username and password to dylt.com.

If you need assistance obtaining any of the above information, contact your local Daylight office
or call the [Daylight](http://dylt.com/) at 855-656-1982.

A more comprehensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-daylight-ltl-freight/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "LTL Freight Quotes - Daylight Edition", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get an API key from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-daylight-ltl-freight/) and pick a
subscription package. When you complete the registration process you will receive an email containing your API key key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your API keys and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the API key. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings =>  Daylight Freight. Use the *Connection* link to create a connection to your Daylight account.

**5. Select the plugin settings**
Go to WooCommerce => Settings => Daylight Freight. Use the *Quote Settings* link to enter the required information and choose
the optional settings.

**6. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the link for Daylight Freight and enable the plugin.

**7. Configure your products**
Assign each of your products and product variations a weight, Shipping Class and freight classification. Products shipping LTL freight should have the Shipping Class set to “LTL Freight”. The Freight Classification should be chosen based upon how the product would be classified in the NMFC Freight Classification Directory. If you are unfamiliar with freight classes, contact the carrier and ask for assistance with properly identifying the freight classes for your  products.

== Frequently Asked Questions ==

= What happens when my shopping cart contains products that ship LTL and products that would normally ship FedEx or UPS? =

If the shopping cart contains one or more products tagged to ship LTL freight, all of the products in the shopping cart
are assumed to ship LTL freight. To ensure the most accurate quote possible, make sure that every product has a weight, dimensions and a freight classification recorded.

= What happens if I forget to identify a freight classification for a product? =

In the absence of a freight class, the plugin will determine the freight classification using the density calculation method. To do so the products weight and dimensions must be recorded. This is accurate in most cases, however identifying the proper freight class will be the most reliable method for ensuring accurate rate estimates.

= Why was the invoice I received from Daylight Freight more than what was quoted by the plugin? =

One of the shipment parameters (weight, dimensions, freight class) is different, or additional services (such as residential
delivery, lift gate, delivery by appointment and others) were required. Compare the details of the invoice to the shipping
settings on the products included in the shipment. Consider making changes as needed. Remember that the weight of the packaging
materials, such as a pallet, is included by the carrier in the billable weight for the shipment.

= How do I find out what freight classification to use for my products? =

Contact your local Daylight Freight office for assistance. You might also consider getting a subscription to ClassIT offered
by the National Motor Freight Traffic Association (NMFTA). Visit them online at classit.nmfta.org.

= How do I get a Daylight Freight account? =

Check your phone book for local listings or call 804-353-1900.

= Where do I find my Daylight username and password? =

Usernames and passwords to Daylight online shipping system are issued by Daylight. Go to [Daylight](http://dylt.com) and click the Sign Up link at the top right of the page in "Login to My Daylight" window. You will be redirected to a page where you can register as a new user.

= How do I get a API key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or
purchased an API key outright. At the conclusion of the registration process an email will be sent to you that will include the
API key. You can also login to eniture.com using the username and password you created during the registration process
and retrieve the API key from the My API keys tab.

= How do I change my plugin API key from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My API keys tab. There you will be able to manage the licensing of all of your
Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site API key. To use it on another website you will need to purchase an additional API key.
If you want to change the website with which the plugin is registered, login to eniture.com and navigate to the My API keys tab.
There you will be able to change the domain name that is associated with the API key.

= Do I have to purchase a second API key for my staging or development site? =

No. Each API key allows you to identify one domain for your production environment and one domain for your staging or
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings > Daylight Freight > Connections)
then you have one or more of the following licensing issues:

1) You are using the API key on more than one domain. The API keys are for single sites. You will need to purchase an additional API key.
2) Your trial period has expired.
3) Your current API key has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and go to the My API keys tab to resolve any of these issues.

== Screenshots ==

1. Quote settings page
2. Warehouses and Drop Ships page
3. Quotes displayed in cart

== Changelog ==

= 2.2.4 =
* Update: Updated connection tab according to WordPress requirements 

= 2.2.3 =
* Update: Introduced capability to suppress parcel rates once the weight threshold has been reached.
* Update: Compatibility with WordPress version 6.5.2
* Update: Compatibility with PHP version 8.2.0
* Fix:  Incorrect product variants displayed in the order widget.

= 2.2.2 =
* Update: Introduced a field for the maximum weight per handling unit.
* Update: Updated the description text in the warehouse. 

= 2.2.1 =
* Update: Compatibility with WooCommerce HPOS(High-Performance Order Storage)

= 2.2.0 =
* Update: Introduced origin level markup and product level markup
* Update: Introduced Logs tab to view recent logs.
* Fix: Don’t quote cart shipping if one or more shipments fail to pull rates.
* Fix: Show FREE shipping if the handling fee is -100% 

= 2.1.3 =
* Update: Modified expected delivery message at front-end from “Estimated number of days until delivery” to “Expected delivery by”.
* Fix: nherent Flat Rate value of parent to variations.

= 2.1.2 =
* Update: Added compatibility with “Address Type Disclosure” in Residential address detection.

= 2.1.1 =
* Fix: Fixed a fatal error in FreightDesk.Online connection

= 2.1.0 =
* Update: Introduced connectivity from the plugin to FreightDesk.Online using Company ID
* Update: Compatibility with add-on “Microwarehouse”
* Update: Compatibility with “WooCommerce Payments” plugin
* Update: Compatibility with WordPress multisite network
* Fix: Inherent product freight class to its variants

= 2.0.2 =
* Update: Compatibility with WordPress multisite network
* Fix: Fixed support link.

= 2.0.1 =
* Update: Compatibility with PHP version 8.1.
* Update: Compatibility with WordPress version 5.9.

= 2.0.0 =
* Update: Compatibility with PHP version 8.0.
* Update: Compatibility with WordPress version 5.8.
* Fix: Corrected product page URL in connection settings tab.

= 1.1.0 =
* Update: Added Feature for product import/export by CSV.
* Update: Updated order's meta data for freightdesk.online.
* Update: Added Product Images URL for freightdesk.online.
* Update: Added feature "Threshold weight limit".
* Update: Updated order widget detail's flow.


= 1.0 =
* Initial release.

== Upgrade Notice ==
