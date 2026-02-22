<?php
/**
 * Template Name: About Page
 *
 * ThessNest — About Page Template
 *
 * @package ThessNest
 */

get_header(); ?>

<main id="main-content" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
    
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Elements (similar to theme style) -->
        <div class="absolute inset-x-0 top-0 h-[800px] bg-gradient-to-br from-primary/5 via-transparent to-primary/10 -z-10"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-accent/5 via-transparent to-transparent -z-10 blur-3xl rounded-bl-full"></div>

        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold tracking-tight text-text mb-6">
                    <?php the_title(); ?>
                </h1>
                <?php if ( has_excerpt() ) : ?>
                <p class="text-lg md:text-xl text-text-light mb-10 leading-relaxed max-w-2xl mx-auto">
                    <?php echo esc_html( get_the_excerpt() ); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Content Area where WordPress Editor Content goes -->
    <section class="py-12 bg-background">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="glass-panel p-8 md:p-12 rounded-3xl">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'prose prose-lg prose-p:text-text prose-headings:text-text max-w-none' ); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
