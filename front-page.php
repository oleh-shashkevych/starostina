<?php
get_header();

$current_page_id = get_queried_object_id();
if ($current_page_id === 0)
    $current_page_id = get_option('page_on_front');

// Поля
$hero_image = get_field('hero_image', $current_page_id) ?: 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?q=80&w=1000&auto=format&fit=crop';
$payment_subtitle = get_field('payment_subtitle', $current_page_id);
$payment_description = get_field('payment_description', $current_page_id);
$price = get_field('product_price', $current_page_id) ?: 450;
$old_price = get_field('product_old_price', $current_page_id);
$socials = get_field('social_links', $current_page_id);
$footer_text = get_field('payment_footer_text', $current_page_id) ?: '*Після успішної оплати сторінка автоматично оновиться і ви отримаєте доступ до файлу.';

$success_page_link = get_field('success_page_link', 'option');
$success_url = $success_page_link ? get_permalink($success_page_link) : home_url();
?>

<!-- SCREEN 1: HERO SECTION -->
<section id="hero" class="min-h-[100svh] flex flex-col md:flex-row w-full relative bg-white">

    <!-- Left Side (Photo) -->
    <div class="w-full h-[55vh] md:w-[40%] md:h-auto bg-dark-bg relative flex items-end justify-center overflow-hidden">
        <div
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[150%] h-[80%] border border-white/10 rounded-[100%] rotate-12 pointer-events-none">
        </div>

        <!-- Арка фото -->
        <div class="relative z-10 w-full h-full flex items-end justify-center pb-0 px-6 md:px-4">
            <div
                class="relative overflow-hidden w-full max-w-sm md:max-w-md h-[90%] md:h-[85%] rounded-t-[10rem] md:rounded-t-full border-t border-x border-white/20 shadow-2xl">
                <img src="<?php echo esc_url($hero_image); ?>" alt="Valeriya Starostina"
                    class="object-cover w-full h-full grayscale hover:grayscale-0 transition duration-1000 ease-in-out fade-in-up">
            </div>
        </div>
    </div>

    <!-- Right Side (Content) -->
    <div class="w-full md:w-[60%] bg-white flex flex-col justify-center px-6 py-8 md:p-16 lg:p-24 relative flex-grow">

        <!-- Language Switcher -->
        <div class="absolute top-4 right-4 md:top-10 md:right-12 z-20">
            <ul class="flex gap-4 md:gap-6 uppercase text-[10px] md:text-xs font-bold tracking-[0.2em] text-gray-400">
                <?php if (function_exists('pll_the_languages'))
                    pll_the_languages(array('show_flags' => 0, 'show_names' => 1)); ?>
            </ul>
        </div>

        <div class="max-w-2xl mx-auto md:mx-0 w-full">
            <!-- Name Block -->
            <div class="mb-6 md:mb-8 fade-in-up relative text-center md:text-left">
                <h1
                    class="text-3xl xs:text-4xl md:text-5xl lg:text-6xl tracking-[0.1em] uppercase font-serif text-gray-900 leading-tight">
                    <?php echo function_exists('pll__') ? pll__('Starostina') : 'Starostina'; ?>
                </h1>
                <!-- Handwritten Name -->
                <div class="relative -mt-3 md:-mt-3 md:ml-32 z-10 flex justify-center md:justify-start">
                    <span
                        class="font-script text-5xl xs:text-6xl md:text-8xl lg:text-9xl text-black block transform -rotate-0 md:origin-left pb-4 md:pb-0">
                        <?php echo function_exists('pll__') ? pll__('Valeriya') : 'Valeriya'; ?>
                    </span>
                </div>
            </div>

            <!-- Role / Title -->
            <div class="flex items-center justify-center md:justify-start gap-4 mb-8 fade-in-up delay-200">
                <div class="hidden md:block h-px w-12 bg-gray-300"></div>
                <p
                    class="text-[10px] xs:text-xs md:text-sm tracking-[0.2em] md:tracking-[0.3em] uppercase text-gray-500 font-semibold text-center md:text-left">
                    <?php echo function_exists('pll__') ? pll__('Fitness Trainer & Nutritionist') : 'Fitness Trainer & Nutritionist'; ?>
                </p>
            </div>

            <!-- Socials (Images) -->
            <?php if ($socials): ?>
                <div
                    class="flex justify-center md:justify-start space-x-6 md:space-x-8 mb-10 fade-in-up delay-200 items-center">
                    <?php foreach ($socials as $social): ?>
                        <?php if (!empty($social['url']) && !empty($social['icon_image'])): ?>
                            <a href="<?php echo esc_url($social['url']); ?>" target="_blank"
                                class="hover:scale-110 transition duration-300 opacity-60 hover:opacity-100">
                                <img src="<?php echo esc_url($social['icon_image']); ?>" alt="Social Icon"
                                    class="h-6 md:h-8 w-auto object-contain">
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- CTA Button -->
            <div class="fade-in-up delay-400 flex justify-center md:justify-start">
                <button onclick="scrollToPayment()"
                    class="group relative px-8 py-4 md:px-10 md:py-5 bg-black rounded-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-500 border border-black w-full md:w-auto">
                    <!-- White overlay on hover -->
                    <div
                        class="absolute inset-0 w-0 bg-white transition-all duration-[400ms] ease-out group-hover:w-full">
                    </div>

                    <!-- Text colors inverted -->
                    <span
                        class="relative text-white group-hover:text-black font-serif tracking-widest text-xs md:text-sm uppercase flex items-center justify-center gap-4">
                        <?php echo function_exists('pll__') ? pll__('BUY "NUTRITION GUIDE"') : 'BUY GUIDE'; ?>
                        <i class="fas fa-arrow-down text-[10px] animate-bounce text-white group-hover:text-black"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- SCREEN 2: PAYMENT SECTION -->
<section id="payment-section"
    class="min-h-screen bg-beige-bg flex flex-col items-center justify-center text-center p-6 md:p-8 relative border-t border-white/50">

    <div class="max-w-4xl w-full relative z-10" data-aos="fade-up">

        <h2 class="font-script text-5xl md:text-8xl mb-6 text-gray-800 transform -rotate-0">
            <?php echo function_exists('pll__') ? pll__('Payment') : 'Payment'; ?>
        </h2>

        <div class="mx-auto mb-10">
            <?php if ($payment_subtitle): ?>
                <h3 class="text-xl md:text-3xl font-serif uppercase tracking-widest mb-6 text-gray-900 font-bold">
                    <?php echo esc_html($payment_subtitle); ?>
                </h3>
            <?php endif; ?>

            <div class="w-full h-px bg-gray-300 opacity-50 mb-10 max-w-xs mx-auto"></div>

            <?php if ($payment_description): ?>
                <div
                    class="product-desc text-left md:text-center text-sm md:text-lg text-gray-700 font-sans leading-relaxed">
                    <?php echo $payment_description; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- <h3 class="text-lg md:text-2xl font-serif uppercase tracking-widest mb-8 text-gray-600 px-4">
            <?php echo function_exists('pll__') ? pll__('Click to pay') : 'Click to pay'; ?>
        </h3> -->

        <div class="flex flex-col items-center gap-6">
            <div class="flex flex-col items-center justify-center">
                <div class="inline-block border border-black/10 px-8 py-3 rounded-full backdrop-blur-sm bg-white/40">
                    <p class="text-base md:text-lg text-gray-600 font-light tracking-wide">
                        <?php echo function_exists('pll__') ? pll__('Cost:') : 'Cost:'; ?>
                        <?php if ($old_price): ?>
                            <span class="text-gray-400 line-through text-lg md:text-xl font-serif mb-1">
                                <?php echo $old_price; ?> UAH
                            </span>
                        <?php endif; ?>
                        <span class="font-serif font-bold text-black text-2xl ml-2"><?php echo $price; ?> UAH</span>
                    </p>
                </div>
            </div>

            <div class="relative group w-full max-w-xs md:max-w-xl mt-4">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-gray-200 to-gray-400 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                </div>
                <button id="pay-button"
                    class="relative w-full bg-black text-white px-8 py-5 md:px-12 md:py-6 text-xs md:text-base tracking-[0.2em] uppercase font-sans font-bold hover:bg-gray-800 transition-all duration-300 shadow-2xl">
                    <span id="btn-text" class="flex items-center justify-center gap-3">
                        <?php echo function_exists('pll__') ? pll__('Pay via Monobank') : 'Pay via Monobank'; ?>
                        <i class="fas fa-arrow-right"></i>
                    </span>
                    <span id="btn-loader" class="hidden absolute inset-0 flex items-center justify-center bg-black">
                        <i class="fas fa-circle-notch fa-spin text-white"></i>
                    </span>
                </button>
            </div>
        </div>

        <p
            class="mt-12 text-[10px] uppercase tracking-widest text-gray-600 max-w-md md:max-w-xl mx-auto leading-relaxed">
            <?php echo nl2br(esc_html($footer_text)); ?>
        </p>
    </div>
</section>

<script>
    // ============================================
    // DEVELOPER MODE: TRUE = Без оплаты (тест)
    // ============================================
    const DEV_MODE = false;
    // ============================================

    document.getElementById('pay-button').addEventListener('click', function () {
        const btn = this;
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');
        const successUrl = '<?php echo esc_url($success_url); ?>';

        btnText.classList.add('opacity-0');
        btnLoader.classList.remove('hidden');
        btn.disabled = true;

        if (DEV_MODE) {
            console.log('DEV MODE: Simulating payment...');
            setTimeout(() => {
                localStorage.setItem('starostina_invoice_id', 'test_mode_payment_ok');
                window.location.href = successUrl;
            }, 1000);
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_mono_invoice');
        formData.append('nonce', wpData.nonce);
        formData.append('page_id', '<?php echo $current_page_id; ?>');

        fetch(wpData.ajax_url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.invoiceId) {
                        localStorage.setItem('starostina_invoice_id', data.data.invoiceId);
                    }
                    window.location.href = data.data.url;
                } else {
                    alert('Помилка: ' + (data.data.message || 'Unknown error'));
                    btnText.classList.remove('opacity-0');
                    btnLoader.classList.add('hidden');
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка з\'єднання');
                btnText.classList.remove('opacity-0');
                btnLoader.classList.add('hidden');
                btn.disabled = false;
            });
    });
</script>

<?php get_footer(); ?>