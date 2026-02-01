# ZU Custom T-Shirt Design

**Contributors:** DevMUBS  
**Tags:** woocommerce, t-shirt, custom design, product designer, print-on-demand  
**Requires at least:** 6.0  
**Tested up to:** 6.4  
**Requires PHP:** 8.3  
**Stable tag:** 1.0.0  
**License:** MIT License
**License URI:** https://opensource.org/licenses/MIT

Advanced WooCommerce plugin for custom T-shirt design with live preview, dynamic pricing rules, and comprehensive order management.

## Description

ZU Custom T-Shirt Design is a powerful WordPress plugin that enables your customers to design and customize T-shirts directly from your WooCommerce store. With an intuitive drag-and-drop interface, real-time price calculation, and comprehensive admin tools, this plugin provides everything you need to offer custom T-shirt printing services.

### Key Features

#### Frontend Customizer
- **Drag & Drop Interface**: Intuitive canvas-based designer powered by Fabric.js
- **Text Editing**: Add, edit, and style text with multiple fonts, sizes, and colors
- **Image Upload**: Allow customers to upload their own images
- **Multi-Side Support**: Design front, back, left sleeve, and right sleeve
- **Live Price Updates**: See price changes in real-time as you design
- **Design Preview**: Preview designs before adding to cart
- **Save for Later**: Save designs and continue later
- **Share Designs**: Generate shareable links for designs
- **Export as PNG**: Download design previews

#### Dynamic Pricing
- **Image Count Pricing**: Price based on number of images
- **Print Size Pricing**: Different prices for small, medium, large, and extra-large prints
- **Print Method Pricing**: DTF, Screen, Digital, Vinyl, and Embroidery options
- **Material Pricing**: Cotton, Polyester, and Blend options
- **Urgency Pricing**: Normal, Express, and Rush delivery options

#### Admin Features
- **Dashboard**: Overview of designs, orders, and revenue
- **Design Templates**: Manage T-shirt templates with printable areas
- **Pricing Rules**: Dynamic pricing configuration
- **Order Management**: View and manage custom design orders
- **Admin Approval**: Optional approval workflow before production
- **Settings**: Comprehensive plugin configuration

#### WooCommerce Integration
- Seamless integration with WooCommerce
- Custom design data attached to orders
- Custom "My Designs" tab in My Account
- Automatic price calculation at checkout

### Requirements

- WordPress 6.0 or higher
- WooCommerce 7.0 or higher
- PHP 8.3 or higher

## Installation

### Automatic Installation

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "ZU Custom T-Shirt Design"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin zip file
2. Extract the zip file
3. Upload the `zu-custom-tshirt-design` folder to `/wp-content/plugins/`
4. Activate the plugin through the **Plugins** menu in WordPress

### After Activation

1. The plugin will automatically create necessary database tables
2. Navigate to **ZU T-Shirt > Settings** to configure the plugin
3. Create your first design template at **ZU T-Shirt > Design Templates**
4. Enable customization for WooCommerce products

## Usage

### Creating Design Templates

1. Go to **ZU T-Shirt > Design Templates**
2. Click **Add New**
3. Upload template images for each side (front, back, sleeves)
4. Define printable areas for each side
5. Set maximum images and enable/disable features
6. Save the template

### Enabling Product Customization

1. Edit a WooCommerce product
2. In the **Custom T-Shirt Design** meta box, check "Enable customization for this product"
3. Select a design template
4. Update the product

### Configuring Pricing Rules

1. Go to **ZU T-Shirt > Pricing Rules**
2. Adjust prices for each pricing category:
   - Number of images
   - Print sizes
   - Print methods
   - Materials
   - Urgency levels
3. Save changes

### Managing Orders

1. Go to **ZU T-Shirt > Orders & Designs**
2. View all custom design orders
3. Use bulk actions to approve, reject, or update order status
4. Download design previews for production

## Frequently Asked Questions

### Does this plugin work without WooCommerce?

No, WooCommerce is required for this plugin to function. The plugin will display a notice if WooCommerce is not installed or activated.

### Can customers save their designs?

Yes, customers can save their designs and access them later through the "My Custom Designs" tab in their account.

### Is there a limit to how many images customers can upload?

Yes, you can configure the maximum number of images per design in the plugin settings. The default is 5 images.

### Can I require admin approval before production?

Yes, you can enable admin approval in the plugin settings. When enabled, orders with custom designs will require approval before production can begin.

### What file types are supported for image uploads?

By default, the plugin supports JPG, JPEG, PNG, GIF, and SVG files. You can configure allowed file types in the settings.

### How is the final price calculated?

The final price is calculated as:
```
Final Price = Product Base Price + Image Count Cost + Print Size Cost + Print Method Cost + Material Cost + Urgency Cost
```

### Can customers share their designs?

Yes, if enabled in settings, customers can generate shareable links for their designs.

### Is the plugin translatable?

Yes, the plugin is fully internationalized and ready for translation.

## Screenshots

1. **Admin Dashboard** - Overview of designs, orders, and revenue
2. **Design Templates** - Manage T-shirt templates with printable areas
3. **Pricing Rules** - Configure dynamic pricing
4. **Frontend Customizer** - Customer design interface
5. **My Account Designs** - Customer's saved designs

## Changelog

### 1.0.0
- Initial release
- Frontend customizer with Fabric.js
- Dynamic pricing system
- Design templates management
- Order management
- My Account integration
- Admin approval workflow
- Design sharing and export

## Upgrade Notice

### 1.0.0
Initial release. No upgrade necessary.

## Support

For support, please visit [support page] or contact us at [email].

## Credits

- Fabric.js - Canvas library
- WordPress - CMS platform
- WooCommerce - E-commerce platform

## License

This plugin is licensed under the MIT License.
See the LICENSE file for details: https://opensource.org/licenses/MIT
