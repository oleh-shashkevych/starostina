<?php get_header(); ?>

<div class="container mx-auto p-12 text-center min-h-screen flex flex-col justify-center">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        
        <h1 class="text-4xl font-serif mb-6"><?php the_title(); ?></h1>
        
        <div class="prose max-w-none mx-auto text-gray-600 font-sans leading-relaxed">
            <?php the_content(); ?>
        </div>

    <?php endwhile; else : ?>
        
        <h1 class="text-3xl font-serif mb-4">Сторінку не знайдено</h1>
        <p class="text-gray-500">Спробуйте повернутися на головну.</p>
        <a href="<?php echo home_url(); ?>" class="inline-block mt-6 px-6 py-3 bg-black text-white uppercase tracking-widest text-sm hover:bg-gray-800 transition">
            На головну
        </a>

    <?php endif; ?>
</div>

<?php get_footer(); ?>