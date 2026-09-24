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
  <section class="app-downloads__hero" aria-labelledby="app-downloads-title">
    <p class="app-downloads__eyebrow"><?php echo svic_translate_html('app_downloads.hero.eyebrow'); ?></p>
    <h1 id="app-downloads-title" class="app-downloads__title"><?php echo svic_translate_html('app_downloads.hero.title'); ?></h1>
    <p class="app-downloads__intro"><?php echo svic_translate_html('app_downloads.hero.intro'); ?></p>
    <p class="app-downloads__remote-hint"><?php echo svic_translate_html('app_downloads.hero.remote_hint'); ?></p>
  </section>

  <section id="available-apps" class="app-downloads__section" aria-labelledby="app-downloads-list-title">
    <h2 id="app-downloads-list-title" class="app-downloads__section-title"><?php echo svic_translate_html('app_downloads.list_title'); ?></h2>
    <div class="app-downloads__list">
      <?php foreach ($apps as $app) : ?>
        <article class="app-downloads__card">
          <div class="app-downloads__copy">
            <h3 class="app-downloads__name"><?php echo esc_html($app['name']); ?></h3>
            <p class="app-downloads__description"><?php echo svic_translate_html('app_downloads.apps.' . $app['key'] . '.description'); ?></p>
          </div>
          <a class="app-downloads__button"
             href="<?php echo esc_url($app['url']); ?>"
             aria-label="<?php echo svic_translate_attr('app_downloads.button_accessible', ['app' => $app['name']]); ?>"><?php echo svic_translate_html('app_downloads.button'); ?></a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="app-downloads__install" aria-labelledby="app-downloads-install-title">
    <h2 id="app-downloads-install-title" class="app-downloads__section-title"><?php echo svic_translate_html('app_downloads.install.title'); ?></h2>
    <ol class="app-downloads__steps">
      <li><?php echo svic_translate_html('app_downloads.install.steps.download'); ?></li>
      <li><?php echo svic_translate_html('app_downloads.install.steps.open'); ?></li>
      <li><?php echo svic_translate_html('app_downloads.install.steps.confirm'); ?></li>
      <li><?php echo svic_translate_html('app_downloads.install.steps.cleanup'); ?></li>
    </ol>
  </section>
</main>

<?php get_footer(); ?>
