# Site Navigation Schema Implementation

## Overview
Implemented JSON-LD structured data for site navigation to help Google display site navigation links directly in search results as "sitelinks".

## Implementation Details

### Location
- **File**: `theme/svicloudtvbox-lumen/functions.php`
- **Function**: `svic_output_site_navigation_schema()`
- **Hook**: `wp_head` (priority 99 - runs after Rank Math SEO)

### What It Does
1. **Outputs on Homepage Only**: The structured data is only rendered on the front page using `is_front_page() && is_home()` detection
2. **Reads WordPress Menu**: Automatically pulls navigation items from the 'primary' WordPress menu
3. **Fallback Navigation**: If no menu is set, uses the hardcoded fallback navigation from the header
4. **Schema.org Compliance**: Outputs valid JSON-LD structured data following Schema.org standards
5. **Rank Math Integration**: Only outputs SiteNavigationElement schema (Rank Math SEO handles WebSite/Organization schemas)
6. **PHP Compatibility**: Function uses no return type declaration for PHP 7.0+ compatibility

### Schema Structure
The implementation outputs SiteNavigationElement schema only. Rank Math SEO plugin already handles WebSite and Organization schemas, so we avoid duplication.

#### SiteNavigationElement Schema (for each nav item)
```json
{
  "@type": "SiteNavigationElement",
  "@id": "https://example.com/page/#nav-1",
  "position": 1,
  "name": "Page Name",
  "description": "Optional description if provided in menu",
  "url": "https://example.com/page/"
}
```

**Note**: The `description` field is only included if the menu item has a description set in WordPress admin.

## Features

### Multi-language Support
- Automatically respects the current language using `svic_url_with_lang()`
- Navigation labels are translated using the `svic_translate()` function if available

### Top-Level Navigation Only
- Only includes top-level menu items (parent items)
- Skips child menu items to keep the schema focused on main navigation

### Fallback System
- If WordPress menu system returns no items, uses the hardcoded navigation array
- Ensures structured data is always present even without menu configuration

### Proper Escaping
- All URLs are escaped with `esc_url()`
- All text is stripped of HTML tags with `wp_strip_all_tags()`
- Output is JSON-encoded with WordPress's `wp_json_encode()`

## Benefits for SEO

1. **Enhanced Search Results**: Google can display navigation links directly in search results
2. **Improved Crawlability**: Helps Google understand site structure better
3. **Sitelinks Generation**: Increases chances of getting sitelinks in search results
4. **Rich Results Eligibility**: Makes the site eligible for enhanced search features

## Testing

### Validation Tools
Test the implementation with:

1. **Google Rich Results Test**
   - URL: https://search.google.com/test/rich-results
   - Test your homepage URL

2. **Schema.org Validator**
   - URL: https://validator.schema.org/
   - Paste the JSON-LD output

3. **Google Search Console**
   - Check "Enhancements" section for structured data reports

### Manual Testing
1. Visit the homepage
2. View page source (Ctrl+U or Cmd+U)
3. Search for `application/ld+json`
4. Verify the JSON-LD block contains navigation elements

### Expected Output Example
```html
<!-- SVICLOUD Navigation Schema -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/#nav-1",
      "position": 1,
      "name": "Home",
      "url": "https://svicloudtvbox.us/"
    },
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/compare/#nav-2",
      "position": 2,
      "name": "Compare",
      "url": "https://svicloudtvbox.us/compare/"
    },
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/faq/#nav-3",
      "position": 3,
      "name": "FAQ",
      "url": "https://svicloudtvbox.us/faq/"
    },
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/product/svicloud-10p-plus/#nav-4",
      "position": 4,
      "name": "10P Plus",
      "url": "https://svicloudtvbox.us/product/svicloud-10p-plus/"
    },
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/product/svicloud-10s/#nav-5",
      "position": 5,
      "name": "10S",
      "url": "https://svicloudtvbox.us/product/svicloud-10s/"
    },
    {
      "@type": "SiteNavigationElement",
      "@id": "https://svicloudtvbox.us/contact/#nav-6",
      "position": 6,
      "name": "Contact",
      "url": "https://svicloudtvbox.us/contact/"
    }
  ]
}
</script>
```

**Note**: WebSite and Organization schemas are provided by Rank Math SEO and appear separately in the page source.

## Maintenance

### Updating Navigation
Navigation is automatically updated when you:
- Modify the WordPress menu in the admin panel (Appearance → Menus)
- The function reads from the 'primary' menu location

### Customization
To customize the schema output:
1. Edit the `svic_output_site_navigation_schema()` function
2. Modify the `$schema` array structure
3. Add additional schema types to the `@graph` array

### Disabling
To temporarily disable:
```php
// Comment out or remove this line in functions.php
// add_action('wp_head', 'svic_output_site_navigation_schema', 99);
```

## References

- [Google Sitelinks Search Box Documentation](https://developers.google.com/search/docs/appearance/structured-data/sitelinks-searchbox)
- [Schema.org WebSite](https://schema.org/WebSite)
- [Schema.org SiteNavigationElement](https://schema.org/SiteNavigationElement)
- [Google Search Central - Structured Data](https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data)

## Implementation Date
November 9, 2025

## Implementation Challenges & Fixes

### Challenge 1: Schema Not Appearing After Deployment
**Issue**: After initial deployment, the schema markup didn't appear on the live site.

**Root Cause**: LiteSpeed Cache was serving cached HTML from before the function was added.

**Solution**: Required manual cache flush via WordPress admin panel. Cache needed to be flushed twice:
1. First flush after initial deployment
2. Second flush after PHP compatibility fix

### Challenge 2: PHP Compatibility Issue
**Issue**: Function may have failed silently on PHP 7.0.

**Root Cause**: The function originally used `: void` return type declaration, which requires PHP 7.1+.

**Solution**: Removed the return type declaration to ensure compatibility with PHP 7.0+:
```php
// Before (PHP 7.1+ only)
function svic_output_site_navigation_schema(): void

// After (PHP 7.0+ compatible)
function svic_output_site_navigation_schema()
```

### Challenge 3: Homepage Detection Complexity
**Issue**: Initial implementation used complex homepage detection logic with page ID checks.

**Solution**: Simplified to use only WordPress built-in functions:
```php
// Final simplified detection
if (!is_front_page() && !is_home()) {
    return;
}
```

### Challenge 4: Schema Duplication with Rank Math SEO
**Issue**: Initial plan included WebSite and Organization schemas, but Rank Math SEO already outputs these.

**Solution**:
- Changed implementation to output only SiteNavigationElement schemas
- Moved hook priority from 8 to 99 to run after Rank Math
- This avoids duplicate schema markup and potential conflicts

## Notes
- The structured data appears only on the homepage (front page)
- Works automatically for both English and Chinese homepages via theme translation functions
- Google may take several weeks to process and display the enhanced search results
- Sitelinks appearance is ultimately decided by Google's algorithms based on site quality and relevance
- **Important**: After any code changes, LiteSpeed Cache must be manually flushed via WordPress admin to see updates on the live site
