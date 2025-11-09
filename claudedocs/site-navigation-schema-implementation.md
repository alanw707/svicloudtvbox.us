# Site Navigation Schema Implementation

## Overview
Implemented JSON-LD structured data for site navigation to help Google display site navigation links directly in search results as "sitelinks".

## Implementation Details

### Location
- **File**: `theme/svicloudtvbox-lumen/functions.php`
- **Function**: `svic_output_site_navigation_schema()`
- **Hook**: `wp_head` (priority 8)

### What It Does
1. **Outputs on Homepage Only**: The structured data is only rendered on the front page to represent the site-wide navigation
2. **Reads WordPress Menu**: Automatically pulls navigation items from the 'primary' WordPress menu
3. **Fallback Navigation**: If no menu is set, uses the hardcoded fallback navigation from the header
4. **Schema.org Compliance**: Outputs valid JSON-LD structured data following Schema.org standards

### Schema Structure
The implementation outputs three main schema types:

#### 1. WebSite Schema
```json
{
  "@type": "WebSite",
  "@id": "https://example.com/#website",
  "url": "https://example.com/",
  "name": "Site Name",
  "description": "Site Description",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "https://example.com/?s={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
```

#### 2. Organization Schema
```json
{
  "@type": "Organization",
  "@id": "https://example.com/#organization",
  "name": "Organization Name",
  "url": "https://example.com/"
}
```

#### 3. SiteNavigationElement Schema (for each nav item)
```json
{
  "@type": "SiteNavigationElement",
  "@id": "https://example.com/page/#nav-1",
  "position": 1,
  "name": "Page Name",
  "url": "https://example.com/page/"
}
```

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
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebSite",
      "@id": "https://svicloudtvbox.us/#website",
      "url": "https://svicloudtvbox.us/",
      "name": "SViCloud TV Box",
      ...
    },
    {
      "@type": "Organization",
      "@id": "https://svicloudtvbox.us/#organization",
      ...
    },
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
    ...
  ]
}
</script>
```

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
// add_action('wp_head', 'svic_output_site_navigation_schema', 8);
```

## References

- [Google Sitelinks Search Box Documentation](https://developers.google.com/search/docs/appearance/structured-data/sitelinks-searchbox)
- [Schema.org WebSite](https://schema.org/WebSite)
- [Schema.org SiteNavigationElement](https://schema.org/SiteNavigationElement)
- [Google Search Central - Structured Data](https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data)

## Implementation Date
November 9, 2025

## Notes
- The structured data appears only on the homepage (front page)
- Google may take several weeks to process and display the enhanced search results
- Sitelinks appearance is ultimately decided by Google's algorithms based on site quality and relevance
