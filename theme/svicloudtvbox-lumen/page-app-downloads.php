<?php
/**
 * TV App Downloads Page
 *
 * Template Name: TV App Downloads
 */

$apps = [
    ['key' => 'yogurt_tv', 'name' => 'Yogurt TV', 'url' => 'https://rebrand.ly/sviyogurt'],
    ['key' => 'cherry_tv', 'name' => 'Cherry TV', 'url' => 'https://rebrand.ly/svicherry'],
    ['key' => 'yogurt_kids', 'name' => 'Yogurt Kids', 'url' => 'https://rebrand.ly/svikids'],
];

get_header();
?>

<main id="main-content" class="app-downloads-page" tabindex="-1">
  <header class="app-downloads__hero">
    <h1 id="app-downloads-title" class="app-downloads__title"><?php echo svic_translate_html('app_downloads.hero.title'); ?></h1>
  </header>

  <nav class="app-downloads__grid" aria-label="<?php echo svic_translate_attr('app_downloads.list_title'); ?>">
    <?php foreach ($apps as $app) : ?>
      <a class="app-downloads__tile"
         href="<?php echo esc_url($app['url']); ?>"
         aria-label="<?php echo svic_translate_attr('app_downloads.button_accessible', ['app' => $app['name']]); ?>">
        <span class="app-downloads__name"><?php echo esc_html($app['name']); ?></span>
        <span class="app-downloads__action"><?php echo svic_translate_html('app_downloads.button'); ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
</main>

<?php get_footer(); ?>
