This is a bare minimum theme example for a theme to use the WP Stockroom updater.

# Required files

 - [style.css](style.css) Required for every theme. 
   - The _Version_ is required.
   - The _Update URI_ Should be set to your own hosted stockroom installation.  
     This should be your domain without `https://` and no trailing slash.
   - List of all [style.css headers](https://github.com/WordPress/twentysixteen/blob/master/style.css#L1)
 - [readme.txt](readme.txt)
   - Only the _Version_ is required. 
   - List of all [readme.txt headers](https://github.com/WordPress/twentysixteen/blob/master/readme.txt#L1)
 - [class-wp-stockroom-updater.php](class-wp-stockroom-updater.php)  
   The Main stockroom updater script. It has to be included in _some_ way.
 - [functions.php](functions.php)  
   Includes the `class-wp-stockroom-updater.php` script.  
   Then registers the updater.
 - [index.php](index.php) is **not required**  
   It's just included, so we have a sample theme that works.

# Registering the updater

In your `functions.php` Include the script and use `add_filter` to register the updater.
Be sure to replace your domain.

```php
include_once __DIR__ .'/class-wp-stockroom-updater.php'; // Include the updater script in some way.
add_filter( "update_themes_YOURDOMAINHERE.COM", array( 'WP_Stockroom_Updater', 'check_update' ),10, 4 );
```

# Customization.

## Customizing the slug.

By default, the updater will check the slug of your theme folder. So in this same theme is will look for `wp-stockroom-sample-theme`
If you rename the theme you will still have to point the updater to the original slug. You can change the slug during the update process.

```php
/**
 * Change the slug of the theme/plugin that is being updated.
 *
 * @param string $package_slug The current slug of the theme/plugin that is being checked.
 * @param array  $current_data Details of the plugin/theme being checked.
 */
add_filter( 'wp_stockroom_updater_slug', function ( $package_slug, $current_data ) {
	if ( empty( $current_data['Title'] ) || $current_data['Title'] !== 'WP StockRoom Sample Theme' ) {
		return $package_slug; // This a different theme or plugin. So ignore.
	}

	// Reset the slug back to the original that is uses by github-action and the wp-stockroom installation.
	return 'wp-stockroom-sample-theme';
}, 10, 3 );
```

# FAQ

## The update doesn't appear.
- Is the theme active? Otherwise, it won't check for updates.
- On the wp-admin updates page is a _Check again._ Button, but this can still have a 1~2 minute cache.

## Does this updater work with the full site editor?
Yes
