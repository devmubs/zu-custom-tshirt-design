# Changelog

All notable changes to the ZU Custom T-Shirt Design plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-01

### Added

#### Core Features
- Initial release of ZU Custom T-Shirt Design plugin
- Full WooCommerce integration with dependency checking
- PHP 8.3+ compatibility with modern coding standards
- Object-oriented architecture with namespace support
- Autoloader implementation for efficient class loading

#### Frontend Customizer
- Interactive canvas-based designer powered by Fabric.js 5.3.1
- Drag and drop functionality for all design elements
- Text editing with multiple font families, sizes, and colors
- Image upload with file type and size validation
- Multi-side support (Front, Back, Left Sleeve, Right Sleeve)
- Element rotation, scaling, and positioning
- Layer ordering (bring to front, send to back)
- Opacity control for all elements
- Real-time canvas zoom controls

#### Dynamic Pricing System
- Image count-based pricing (1-5+ images)
- Print size pricing tiers (Small, Medium, Large, Extra Large)
- Print method pricing (DTF, Screen, Digital, Vinyl, Embroidery)
- Material pricing (Cotton, Polyester, Blend)
- Urgency pricing (Normal, Express, Rush)
- Live price calculation and display
- Price breakdown visualization

#### Admin Panel
- Comprehensive dashboard with statistics
  - Total custom designs counter
  - Orders with custom designs counter
  - Revenue tracking from custom products
  - Pending approval counter
  - Recent orders list
- Design Templates management
  - Add, edit, delete templates
  - Upload template images for all sides
  - Define printable areas with coordinates
  - Configure max images and feature toggles
- Pricing Rules configuration
  - Dynamic pricing for all categories
  - Enable/disable specific pricing rules
  - Visual price breakdown examples
- Orders & Custom Designs management
  - View all custom design orders
  - Filter by production status
  - Bulk actions (approve, reject, update status)
  - Download design previews
  - Order detail view with design information
- Settings page
  - General settings (enable/disable, default print method)
  - Canvas settings (width, height)
  - File upload settings (max size, allowed types)
  - Design feature toggles (text, images, fonts)
  - Extra features (live price, share, export)

#### WooCommerce Integration
- Automatic WooCommerce dependency checking
- Admin notice when WooCommerce is missing
- Product meta box for enabling customization
- Template assignment per product
- Custom "Customize This T-Shirt" button on product pages
- Design data attachment to cart items
- Custom cart item data display
- Order item meta storage
- Custom endpoint for My Account page

#### My Account Integration
- Custom "My Custom Designs" tab
- Grid display of all user designs
- Design preview thumbnails
- Product information and pricing
- Status indicators (Draft, Ordered, Approved, Rejected)
- Download design preview
- Reorder functionality
- Delete design option

#### REST API
- Full REST API implementation with custom namespace
- Design CRUD operations
- Image upload endpoint
- Price calculation endpoint
- Template retrieval endpoints
- Design export endpoint
- Design sharing endpoint
- Design reorder endpoint
- Pricing options endpoint

#### Security Features
- Nonce verification for all AJAX requests
- Data sanitization and validation
- Prepared SQL statements
- Role-based access control
- Secure file upload handling
- File type validation
- PHP code detection in uploads
- Rate limiting for sensitive operations
- Secure file path handling

#### Database Structure
- `wp_zu_tshirt_designs` - Store design information
- `wp_zu_tshirt_elements` - Store design elements
- `wp_zu_tshirt_pricing` - Store pricing rules
- `wp_zu_tshirt_orders` - Link designs to WooCommerce orders
- `wp_zu_tshirt_templates` - Store design templates
- `wp_zu_tshirt_template_products` - Link templates to products
- `wp_zu_tshirt_saved_designs` - Store saved/shared designs

#### Extra Features
- Save design for later functionality
- Share design with public/private links
- Export design as PNG
- Design preview modal
- Admin approval workflow
- Keyboard shortcuts (Delete, Ctrl+S)
- Responsive design for mobile devices
- Loading indicators
- Empty state displays
- Error handling and user feedback

#### Assets
- Admin CSS with modern styling
- Public CSS with responsive design
- Customizer CSS for modal and canvas
- Admin JavaScript for template management
- Customizer JavaScript with Fabric.js integration
- My Account JavaScript for design management
- Public JavaScript for shared design handling

#### Documentation
- Comprehensive README.md
- Detailed installation guide (INSTALL.md)
- This changelog (CHANGELOG.md)
- Inline code documentation
- Admin interface help text

### Technical Details

#### Code Architecture
- PSR-4 inspired autoloading
- Namespace-based organization
- Singleton pattern for main plugin class
- Hook-based architecture
- Separation of concerns (Admin, Public, Includes)

#### Performance Optimizations
- Lazy loading of assets
- Minified CSS and JavaScript (ready for production)
- Efficient database queries
- Caching support for pricing rules
- Optimized image handling

#### Compatibility
- WordPress 6.0+
- WooCommerce 7.0+
- PHP 8.3+
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for tablets and mobile

### Known Limitations
- Maximum canvas size limited by browser performance
- Image upload size depends on server configuration
- Some features require JavaScript enabled
- Admin approval workflow requires manual intervention

### Future Enhancements (Planned)
- SVG import and editing
- Clip art library
- Social media sharing
- Design templates marketplace
- Advanced text effects (curved text, outlines)
- Multi-product design sessions
- Design versioning
- Bulk pricing rules import/export
- Analytics and reporting enhancements
- REST API authentication options
- Webhook integrations
- Third-party print service integrations

## Upgrade Notes

### Upgrading to 1.0.0
This is the initial release. No upgrade steps required.

### Database Migrations
All database tables are automatically created on plugin activation.

### File Structure
```
zu-custom-tshirt-design/
├── zu-custom-tshirt-design.php    # Main plugin file
├── README.md                       # Plugin documentation
├── INSTALL.md                      # Installation guide
├── CHANGELOG.md                    # This file
├── admin/
│   ├── class-zu-ctsd-admin.php   # Admin class
│   └── partials/                  # Admin templates
├── includes/
│   ├── class-zu-ctsd-autoloader.php
│   ├── class-zu-ctsd-loader.php
│   ├── class-zu-ctsd-i18n.php
│   ├── class-zu-ctsd-activator.php
│   ├── class-zu-ctsd-deactivator.php
│   ├── class-zu-ctsd-database.php
│   ├── class-zu-ctsd-woocommerce.php
│   ├── class-zu-ctsd-rest-api.php
│   ├── class-zu-ctsd-design-handler.php
│   ├── class-zu-ctsd-pricing.php
│   └── class-zu-ctsd-security.php
├── public/
│   ├── class-zu-ctsd-public.php  # Public class
│   └── partials/                  # Public templates
├── templates/
│   ├── customizer.php             # Customizer modal
│   └── my-account-designs.php     # My Account designs
├── assets/
│   ├── css/                       # Stylesheets
│   └── js/                        # JavaScript files
└── languages/                     # Translation files
```

## Credits

### Libraries
- [Fabric.js](http://fabricjs.com/) - Canvas library for interactive design
- [WordPress](https://wordpress.org/) - Content management system
- [WooCommerce](https://woocommerce.com/) - E-commerce platform

### Development Team
- DevMUBS

### Contributors
- Special thanks to all beta testers and contributors

## License

This plugin is licensed under the MIT License.
See the LICENSE file for details: https://opensource.org/licenses/MIT


---

For support, feature requests, or bug reports, please contact the development team.
