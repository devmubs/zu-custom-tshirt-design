# Installation Guide for ZU Custom T-Shirt Design

## System Requirements

Before installing the plugin, ensure your server meets the following requirements:

- **WordPress**: Version 6.0 or higher
- **WooCommerce**: Version 7.0 or higher
- **PHP**: Version 8.3 or higher
- **MySQL**: Version 5.7 or higher, or MariaDB 10.3 or higher

### Recommended PHP Extensions

- GD Library or ImageMagick (for image processing)
- JSON
- MBString
- ZIP (for file uploads)

## Installation Methods

### Method 1: Automatic Installation (Recommended)

1. **Log in** to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Click **Upload Plugin** at the top of the page
4. Click **Choose File** and select the `zu-custom-tshirt-design.zip` file
5. Click **Install Now**
6. After installation, click **Activate Plugin**

### Method 2: Manual Installation via FTP

1. **Download** the plugin zip file
2. **Extract** the zip file on your computer
3. **Connect** to your server via FTP/SFTP
4. **Navigate** to `/wp-content/plugins/`
5. **Upload** the `zu-custom-tshirt-design` folder
6. **Log in** to your WordPress admin dashboard
7. Navigate to **Plugins > Installed Plugins**
8. Find "ZU Custom T-Shirt Design" and click **Activate**

### Method 3: Manual Installation via cPanel/File Manager

1. **Download** the plugin zip file
2. **Log in** to your hosting cPanel
3. Open **File Manager**
4. Navigate to `/wp-content/plugins/`
5. Click **Upload** and select the zip file
6. **Extract** the zip file in the plugins folder
7. **Delete** the zip file (optional)
8. **Log in** to your WordPress admin dashboard
9. Navigate to **Plugins > Installed Plugins**
10. Find "ZU Custom T-Shirt Design" and click **Activate**

## Post-Installation Setup

### Step 1: Verify WooCommerce is Active

The plugin requires WooCommerce to be installed and activated. If WooCommerce is not active:

1. Navigate to **Plugins > Installed Plugins**
2. Find "WooCommerce" and click **Activate**
3. If WooCommerce is not installed, go to **Plugins > Add New** and search for "WooCommerce"

### Step 2: Configure Plugin Settings

1. Navigate to **ZU T-Shirt > Settings**
2. Configure the following options:
   - **Enable Plugin**: Turn on/off the customizer functionality
   - **Canvas Size**: Set the designer canvas dimensions
   - **File Upload Settings**: Configure max file size and allowed types
   - **Design Features**: Enable/disable text and image editing
   - **Extra Features**: Enable live price, sharing, and export options
3. Click **Save Settings**

### Step 3: Create Design Templates

1. Navigate to **ZU T-Shirt > Design Templates**
2. Click **Add New**
3. Enter a template name
4. Upload template images:
   - **Front View**: Required for front printing
   - **Back View**: Optional for back printing
   - **Left Sleeve**: Optional for left sleeve printing
   - **Right Sleeve**: Optional for right sleeve printing
5. Define printable areas for each side (in pixels):
   - X position
   - Y position
   - Width
   - Height
6. Set maximum images allowed
7. Enable/disable text and image editing
8. Click **Create Template**

### Step 4: Configure Pricing Rules

1. Navigate to **ZU T-Shirt > Pricing Rules**
2. Adjust pricing for each category:
   - **Number of Images**: Set prices for 1-5+ images
   - **Print Size**: Small, Medium, Large, Extra Large
   - **Print Method**: DTF, Screen, Digital, Vinyl, Embroidery
   - **Material**: Cotton, Polyester, Blend
   - **Urgency**: Normal, Express, Rush
3. Click **Save Pricing Rules**

### Step 5: Enable Product Customization

1. Navigate to **Products > All Products**
2. Edit a product you want to enable customization for
3. In the **Custom T-Shirt Design** meta box (right sidebar):
   - Check **"Enable customization for this product"**
   - Select a **Design Template** from the dropdown
4. Click **Update**

### Step 6: Test the Customizer

1. Visit the product page on your storefront
2. Click **"Start Designing"** button
3. Test all features:
   - Add text
   - Upload images
   - Switch print sides
   - Change print options
   - Check live price updates
   - Save design
   - Add to cart

## Troubleshooting

### Plugin Not Activating

**Issue**: Plugin fails to activate

**Solutions**:
1. Check PHP version (must be 8.3+)
2. Check WordPress version (must be 6.0+)
3. Check if WooCommerce is installed
4. Check file permissions (should be 755 for folders, 644 for files)

### Customizer Not Loading

**Issue**: Customizer modal doesn't open

**Solutions**:
1. Check browser console for JavaScript errors
2. Ensure jQuery is loaded
3. Check if Fabric.js is loading (should load from CDN)
4. Clear browser cache
5. Check for theme/plugin conflicts

### Images Not Uploading

**Issue**: Image upload fails

**Solutions**:
1. Check max file size setting
2. Check allowed file types
3. Verify upload directory permissions
4. Check PHP upload_max_filesize and post_max_size
5. Ensure GD Library or ImageMagick is installed

### Price Not Calculating

**Issue**: Live price not updating

**Solutions**:
1. Check if "Enable Live Price" is enabled in settings
2. Verify pricing rules are configured
3. Check browser console for AJAX errors
4. Ensure product has a price set

### Database Errors

**Issue**: Database table errors

**Solutions**:
1. Deactivate and reactivate the plugin
2. Check database user has CREATE TABLE permissions
3. Manually create tables using the SQL in the plugin documentation

## File Permissions

Ensure the following directories are writable by the web server:

```
/wp-content/uploads/zu-tshirt-designs/
/wp-content/uploads/zu-tshirt-designs/designs/
/wp-content/uploads/zu-tshirt-designs/templates/
/wp-content/uploads/zu-tshirt-designs/previews/
/wp-content/uploads/zu-tshirt-designs/exports/
/wp-content/uploads/zu-tshirt-designs/temp/
```

Set permissions to **755** for directories and **644** for files.

## Uninstallation

### To deactivate the plugin:

1. Navigate to **Plugins > Installed Plugins**
2. Find "ZU Custom T-Shirt Design"
3. Click **Deactivate**

### To completely remove the plugin:

1. **Deactivate** the plugin (as above)
2. Click **Delete**
3. Confirm deletion

**Note**: Deleting the plugin will remove all plugin files but will **NOT** delete the database tables or uploaded designs. To remove these, you must manually delete them.

### To remove database tables:

**Warning**: This will permanently delete all design data!

```sql
DROP TABLE IF EXISTS wp_zu_tshirt_designs;
DROP TABLE IF EXISTS wp_zu_tshirt_elements;
DROP TABLE IF EXISTS wp_zu_tshirt_pricing;
DROP TABLE IF EXISTS wp_zu_tshirt_orders;
DROP TABLE IF EXISTS wp_zu_tshirt_templates;
DROP TABLE IF EXISTS wp_zu_tshirt_template_products;
DROP TABLE IF EXISTS wp_zu_tshirt_saved_designs;
```

### To remove uploaded files:

Delete the following directory:
```
/wp-content/uploads/zu-tshirt-designs/
```

## Support

If you encounter issues not covered in this guide:

1. Check the [FAQ section](README.md#frequently-asked-questions) in README.md
2. Review the [Changelog](CHANGELOG.md) for known issues
3. Contact support at [support email]

## Next Steps

After installation, consider:

1. Creating multiple design templates for different products
2. Setting up detailed pricing rules
3. Testing the complete order flow
4. Training staff on order management
5. Creating documentation for customers
