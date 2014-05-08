Srka-Magento-Flickr-Gallery
===========================

Srka Flick Gallery is a feature rich and easy-to-use Magento extension that will let you integrate any Flickr gallery into your Magento website. It is fully customizable through a simple Magento admin configuration page and it uses its own system to cache Flickr API requests making it blazingly fast. Extension is tested on Magento CE 1.8 but it should work fine in other versions. It’s completely free and open-source.

Following the success of <a href="https://github.com/srka/Inchoo-Flickr-Gallery" target="_blank">Inchoo Flickr Gallery</a> that I published while I was working at <a href="http://inchoo.net/" target="_blank">Inchoo</a> and the fact that it is still the only fully featured Magento Flickr gallery extension out there, I decided to make it even better. Instead of just tweaking the old version I decided to make it as good as possible so I started from scratch. Don’t worry though, it looks and works just like you expect only better, plus it’s much easier to modify and play around with for both frontend and backend developers.

Features
--------
  * Automatically gets all the photosets from the specified user and makes them available in Magento admin
  * Allows you to choose which photosets you want to display on the site
  * Configurable pagination
  * Configurable thumbnail size
  * Configurable tooltips with different skin styles to choose from
  * Integrated Lightbox that can be disabled if you want to use your own
  * Fully customizable carousel block that can be added to any page
  * Fully customizable Flickr API response caching for increased performance
  * Using AJAX to eliminate the page loading delay caused by Flickr API requests
  * Extension does not use jQuery
  * <strong>NEW!</strong> Add individual photosets on any page with layout updates (most requested feature)

Change log
----------
<strong>1.1.0</strong>
  * Support for Flickr API secure URLs (https). For more details visit  http://code.flickr.net/2014/04/30/flickr-api-going-ssl-only-on-june-27th-2014/

How to install?
---------------
Download Srka Flickr Gallery extension files to your Magento root directory. Extension files will be extracted into the base package / default template so if you have your own package just copy the extension files to your package / template directories.

If you are logged in to your Magento admin you have to logout and then log in again. Clearing the cache would also be a good idea.


Configuration
-------------
Srka FlickrGallery extension is made to be fully and easily configurable from Magento admin. Just go to <strong>System -> Configuration -> Srka -> Flickr Gallery</strong> to find all the available configuration options.

To get started you'll need to have a Flickr API Key and User ID. Go to http://www.flickr.com/services/api/misc.api_keys.html to get your own Flickr API Key and http://idgettr.com/ for your User ID. Once you enter your API Key and User ID save your configuration and you will get the list of all the available photosets for the user specified with the User ID. You can select all of them or just the ones you want to show on the site and save your configuration. That should be enough to get the gallery up and running.

All other configuration options are pretty straightforward with useful descriptions so there is no need to explain them here.


How to access the gallery on frontend?
--------------------------------------
By default, Srka FlickrGallery extension adds a link to the footer but you can open the gallery directly at www.yoursite.com/gallery


How to add a photosets carousel to any page?
--------------------------------------------
To add a carousel of all the photosets you just need to add one line of code to your layout updates.
````` XML	
<block type="flickrgallery/photosets" name="flickrgalleycarousel" template="flickrgallery/carousel.phtml"/>
`````

Add it inside any reference in any handle and it will just work. You can customize it from admin configuration page. Just go to <strong>System -> Configuration -> Srka -> Flickr Gallery -> Carousel</strong>


NEW! How to add a photoset to any page?
---------------------------------------
The most requested feature is finally here! You can now add a photoset to any page using layout updates. It's really simple, let me show you.

First of all you need to add some JavaScript and CSS files to the page. You do that like you would for any other JS or CSS file.
````` XML	
<reference name="head">
    <action method="addCss"><stylesheet>flickrgallery/css/styles.css</stylesheet></action>
    <action method="addItem"><type>skin_js</type><name>flickrgallery/js/script.js</name></action>
    <action method="addItem"><type>skin_js</type><name>flickrgallery/lightbox/js/lightbox.js</name></action>
    <action method="addCss"><stylesheet>flickrgallery/lightbox/css/lightbox.css</stylesheet></action>
</reference>
`````

If you don't want to use the lightbox feature or you are using your own you can remove the the lightbox.js and lightbox.css files from the code above.

Now that that's done you can add a photoset (or any number of them) to the page. Add the code below inside any reference tag in any handle of your layout:
````` XML
<block type="flickrgallery/photoset" name="photoset_custom" as="photosetCustom" template="flickrgallery/photoset.phtml">
    <action method="setPhotosetId"><photoset_id>[YOUR_PHOTOSET_ID]</photoset_id></action>
</block>
`````

You can configure it to better fit your layout. Right now only the number if items per page and the thumbnail size are configurable but I will add more options if the future so stay tuned. All other options would be pulled from main extension configuration page mentioned above. Add the code below inside the “photoset_custom” block to set configuration options:
````` XML	
<action method="setPerPage"><per_page>[NUMBER_OF_ITEMS_PER_PAGE]</per_page></action>
<action method="setThumbSizePrefix"><thumb_size_prefix>[THUMBNAIL_SIZE_SYMBOL]</thumb_size_prefix></action>
`````

Note that the <strong>[THUMBNAIL_SIZE_SYMBOL]</strong> uses the same symbol format you can find on the Srka Flick Gallery admin configuration page. It uses Flickr API suffixes that you can find <a href="http://www.flickr.com/services/api/misc.urls.html" target="_blank">here</a>.


That's it. You can find the full article about the extension at: http://leaditsoftware.com/blog/magento-flickr-gallery-extension/

