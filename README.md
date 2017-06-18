[![N|Solid](https://www.sparevideos.com/img/logo.png)](https://www.sparevideos.com)

# About:
This plugin connects the basic functionality of the API of SpareVideos.com Video CDN. The following functionality of the API is included: 

  - Upload from URL or Local file
  - Initialize Convertion
  - Receive Webhook when conversion completes
  - Thumbnails and update
  - Embeded code method of streaming

# Not included:

  - Upload from Dropbox which we normally support in SpareVideos UI
  - Custom player setup fetching signed streamable URLs from the API

# How to:
- Install and activate the plugin after you open an account with SpareVideos.com Copy and fill in the settings page of the plugin with the API credentials

- Start uploading and embedding videos from your Media Manager and give access to specific roles to also use the plugin

- Instead of embedding in posts and pages you can also use the "Make Video Page" button to set the video in the meta tags of the page/post.

- Then use the following code anywhere in the template to show the player with the video you selected to Make Video Page:

```sh
<?php echo do_shortcode('[sparevideos]'); ?>
```
# About "Make Video Page":
By selecting a video (uploaded in THAT specific post/page) you have an extra option/button, that of "Make Video Page", which puts the selected video in the meta tags of the post/page. The filter which converts shortcodes in text into javascript code (embedding video player in other words) doesn't work for the video saved in meta tags. What embeds the video player into the selected position in the template instead, is the php code here above mentioned. That code fetched the video sved in metas and converts it to embedded player in that position.
