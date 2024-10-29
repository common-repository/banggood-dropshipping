=== Dropshipping with Banggood for WooCommerce (Lite version) ===
Contributors: ali2woo
Tags: woocommerce, woo, banggood, dropship, dropshipping, dropshipper, affiliate
Requires at least: 4.7
Tested up to: 5.9
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Stable tag: 1.2.11
Requires PHP: 7.0
WC tested up to: 6.1
WC requires at least: 5.0

Start your Dropshipping business with Banggood and Woocommerce: easily find and import profitable products into your store, set up your pricing markups and even more features!

== Description ==

Want to launch your own dropshipping store based on Woocommerce? With the plugin, you can accomplish this task easily! Import products with variants from Banggood! Add pricing rules to set up your profit for each order. Edit product attributes, images, descriptions to make your products stadout among competitor products.

[Ali2Woo.com](https://ali2woo.com/) | [Banggood Plugin](https://ali2woo.com/dropshipping-plugin-banggood/) | [Demo](https://demo-store.ali2woo.com/wp-login.php) | [Chrome extension](https://chrome.google.com/webstore/detail/banggood-dropshipping/iacimipedpoofkikilgbbllfgnngmpkk/) 

###How to start using the plugin?
First of all you need to setup the Banggood API keys.
Go to "Bng2Woo Lite > Settings > Account settings" and input Appid, and AppSecret.
Both keys are provided by Banggood Open platform, follow this guide to [obtain these keys](https://api.banggood.com/index.php?com=document&article_id=3). Please note: you need to assign your Appid to specific domain, because Banggood API will work only with chosen domain. If you input your Appid to the plugin setting, but get some api errors, please contact Banggood dropshipping support, they help you to activate your Appid. Please note, the Banggood team provides support by email: 
openplatform@banggood.com

### Important Notice:

- Plugin works based on WooCommerce plugin.

- Your permalink structure must NOT be "Plain"

- It is released on WordPress.org and you can use plugin as free to build themes for sale.

###Dropshipping with Banggood. What are advantages over AliExpress?
[youtube https://youtu.be/fhTJedw1uaY]

###How to Apply for API to Banggood Dropshipping Center?
[youtube https://youtu.be/4GPPgFooFy4]

### FEATURES
  
&#9658; **Import Products**:

This plugin can import products from Banggood with 2 methods:

1) Using inbuilt search module. Go to "Bng2Woo Lite > Search Products" page and click on the Search button. if you want to search for products from a specific Banggood's category, choose the desired category from drop-down selection. Once search results appear, pick items you want to add to your Import List.

2) Using our free extension for Chrome browser. The extension can pull products from selected categories or deals pages on Banggood. Also, if you want to import a specific product only, you can use Banggood product ID or Banggood product URL to do that.

- **Import from single product page**

- **Import from category page**

- **Import from deals page**

- **Bulk Import feature from category or deals page**


&#9658; **Choose images which should be published with the product**:

When you import the products into your store, they are added to the Import List. There you can edit their data before making them published in your store. In the import list you can manage product images and choose what images should be published together with the product.

&#9658; **Configure settings for all imported products**:

This is a set of settings that applies to all products imported from Banggood portal. Go to "Bng2Woo Lite > Settings > Common Settings > Import Settings".

- **Language**: Currently the Banggood API provides data in english language only. However, they want to add more languages in the next API updates.

- **Currency**: Change the currency of products.

- **Default product type**: By default our tool imports product as "Simple/Variable product". In this case, shoppers will stay on your website when they make a purchase else choose the "External/Affiliate Product" option and your visitors will be redirected to the Banggood website to finish the purchase.

- **Default product status**: Choose the "Draft" option and imported products will not be visible on your website frontend.

- **Not import description**: Enable this feature if you don't want to import product description.

- **Use external image urls**: By default, the plugin keeps product images on your server. If you want to save a free space on your server, activate this option and the plugin will load images using the external Banggood's URLs. Please note: This feature works if the plugin is active only, by default Wordpress can't work with the external images paths!

- **Import in the background**: Enable this feature and allow the plugin to import products in a background mode. In this case, each product is loaded in several stages. First, the plugin imports main product data such as: title, description, and attributes, and in the second stage, it imports product images and variants. This feature speeds up the import process extremely. Please note: In the first stage a product is published with the “draft” status and then when all product data is loaded the product status is changed to “published”.

- **Convert case of attributes and their values**: Products may come with different text case of attributes and their values. Enbale this feature to covert all texts to the same case.

- **Set default stock value**: By default the plugin imports the original stock level value. Some sellers on Banggood don't set stock value. To solve the issue we added this feature where you can set the default stock value for such products, for example: 1

- **Use random stock value**: This feature has a similar behaviour to previous option, but it forces the plugin to generate stock level value automatically and choose it from a predefined range.


&#9658; **Set up global pricing rules for all products**:

These options allow you to set own markup over Banggood prices. You can add separate markup formula for each pricing range. The formula is a rule of a price calculation that includes different math operators such as +, *, =. Pricing rules support three different modes that manage the calculation in your formulas. Additionally, you can add cents to your prices automatically. And even more, it's easy to apply your pricing rules to already imported products. 

Go to "Bng2Woo Lite > Settings > Pricing Rules" and add your pricing rules.

Please note: the Banggood API method gives infromation about product sale pice only. Currently it's not possible to get the regulat price via API.

&#9658; **Filter or delete unnecessary text from Banggood product**: 

Here you can filter all unwanted phrases and text from the imported product. It allows adding unlimited rules to filter the texts. These rules apply to the product title and description. Please note the plugin checks your text in case-sensitive mode.

Go to "Bng2Woo Lite > Settings > Phrase Filtering" and create a few rules that will be applied to the imported data.

###PRO VERSION

- **All features from the free version**

- **6 months of Premium support and updates**

&#9658; **Earn more with Banggood Affiliate program:** 

**[pro version feature]** You can connect your store to the Banggood Affiliate program using your Admitad account. Go to the Bang2Woo Settings > Account Settings and input your Admitad Cashback url.

&#9658; **Remove "Ship From" attribute automatically**: 

**[pro version feature]** Save your time, you don’t need to edit the "Shipping From" attribute for each product one by one, the plugin will do that automatically for you!

&#9658; **Set options related to the product synchronization**:

**[pro version feature]** This set of features allows synchronizing an imported product automatically with Banggood. Also, you can set a specific action that applies to the product depending on change occurring on Banggood.  Go to Banggood Settings > Common Settings > Schedule Settings.

- **Banggood Sync**: Enable product sync with Banggood in your store. It can sync product price, quantity and variants.

- **When product is no longer available**: Choose an action when some imported product is no longer available on Banggood.

- **When variant is no longer available**: Choose an action when some product variant becomes not available on Banggood.

- **When a new variant has appeared**: Choose an action when a new product variant appears on Banggood.

- **When the price changes**: Choose an action when the price of some imported product changes on Banggood.

- **When inventory changes**: Choose an action when the inventory level of some imported product changes on Banggood.

&#9658; **Add Shipping cost to your pricing markup**: 

**[pro version feature]** Use this feature to increase your margin by including shipping cost to the product price. 

&#9658; **Shipping pricing rules**: 

**[pro version feature]** Set shipping pricing rules to add your own margin over the original delivery cost.

[GET PRO VERSION](https://ali2woo.com/dropshipping-plugin-banggood/) 


### Plugin Links

- [Report Bugs/Issues](https://support.ali2woo.com/)

= Minimum Requirements =

* PHP 7.0 or greater is recommended
* MySQL version 5.0 or greater
* WooCommerce 3.0.0+

= Support = 

In case you have any questions or need technical assistance, get in touch with us through our [support center](https://support.ali2woo.com).

== Installation ==

= From within WordPress =

1. Visit 'Plugins > Add New'
2. Search for 'Bng2Woo Lite'
3. Activate Bng2Woo Lite from your Plugins page.
4. Go to "after activation" below.

= Manually =

1. Upload the `bng2woo-lite-lite` folder to the `/wp-content/plugins/` directory
2. Activate the Bng2Woo Lite plugin through the 'Plugins' menu in WordPress
3. Go to "after activation" below.

== FAQ ==

1. Why do I see "31020 Error Account" when I try to import or sync products?
a) Please check whether you have input correct Appid and AppSecret in the Banggood plugin settings. You should get both keys from your dropshipping account on https://api.banggood.com/.
b) Contact banggood support by email openplatform@banggood.com and ask to assign your Appid with your server IP. You can find your server IP in the plugin settings > system info > External IP.
c) Contact banggood support and ask whtjer your Appid is still active and it's not disabled. They can disable your Appid if you didn't make sales via your Appid for a long time. If you ask them, they should reactivate it for you.

2. Can you add my feature to the plugin? 
a) Yes, we can add a new feature. We need a detailed description of the feature and then we will send you the quote.

== Screenshots ==

1. The Bng2Woo Lite plugin build-in product search tool. 
2. The Import List page, here you can adjust the products before pushing them into WooCommerce store.
3. Choose images you want to be pushed with the product into your store.
4. The Bng2Woo Lite Setting page.
5. Set up your pricing markups.
6. Remove or replace unwanted text from the content imported from Banggood

== Changelog ==

= 1.2.11 - 2022.04.02 =
* Fixed edit price in import list
* Fixed manage stock bug
* Fixed bug "the filter rules are not saved"
* Fixed bug "0 can't be set as default stock value in the plugin settings"
* Fixed external IP address info
* Fixed minor bugs

= 1.2.4 - 2022.01.31 =
* Compatibility with WP 5.9
* Fixed manual update bug
* Fixed minor bugs

= 1.2.3 - 2021.12.04 =
* Added support for the Banggood chrome extension
* Fixed minor bugs

= 1.2.3 - 2021.12.04 =
* Added support for the Banggood chrome extension
* Fixed minor bugs

= 1.2.0 - 2021.11.11 =
* Fixed random stock feature and convert attributes case feature
* Fixed product import issues
* Fixed compatibility with full plugin version
* Fixed minor bugs

= 1.1.1 - 2021.11.04 =
* Fixed critical error occuring on the plugin activation

= 1.1.0 - 2021.11.01 = 
* Added random stock feature
* Added default stock feature
* Improved import feature (Now the plugin import variants from all warehouses)
* Fixed stock import feature (Now it imports the correct stock amount)
* Added information about premium plugin features
* Updated pluin menu icon
* Fixed minor bugs

= 1.0.0 - 2021.09.01 = 
* The initial version released

== Upgrade Notice ==

= 1.1.0 = 
This version comes with a lot of fixes which allow plugin to import correct infromation about product stovk nd prices. Also, it adds some new features to the lite version and compaibility with the full plugin veersion. Please note, the new verion will remove all products you have added in your Import List, because the we improved the strucutre of imported products. Also, we recommend to reimport the products you have published in your store by previous plugin version.


 