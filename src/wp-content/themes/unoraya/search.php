<?php get_header(); ?>
<main id="content" role="main" class="max-content">
    <?php if ( have_posts() ) : ?>
        <h1 class="entry-title" itemprop="name">Resultado de busqueda</h1>
        
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'entry' ); ?>
        <?php endwhile; ?>
            <?php get_template_part( 'nav', 'below' ); ?>
        <?php else : ?>
        <article id="post-0" class="post no-results not-found">
            <header class="header">
            <h1 class="entry-title" itemprop="name"><?php esc_html_e( 'Nothing Found', 'blankslate' ); ?></h1>
            </header>
            <div class="entry-content" itemprop="mainContentOfPage">
            <p><?php esc_html_e( 'Sorry, nothing matched your search. Please try again.', 'blankslate' ); ?></p>
            <?php get_search_form(); ?>
            </div>
        </article>
    <?php endif; ?>
</main>
<?php get_footer(); ?>