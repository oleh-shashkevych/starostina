<?php 
get_header(); 

$current_page_id = get_queried_object_id();
if ($current_page_id === 0) $current_page_id = get_option('page_on_front');

$hero_image = get_field('hero_image', $current_page_id) ?: 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?q=80&w=1000&auto=format&fit=crop';
$price = get_field('product_price', $current_page_id) ?: 450;
$socials = get_field('social_links', $current_page_id);
?>

<!-- SCREEN 1: HERO SECTION -->
<section id="hero" class="min-h-screen flex flex-col md:flex-row w-full relative">
    <div class="w-full md:w-1/3 bg-dark-bg relative flex items-end justify-center overflow-hidden h-[50vh] md:h-auto">
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[110%] h-[70%] border border-white/30 rounded-[100px] rotate-12 pointer-events-none"></div>
        <div class="relative z-10 w-full h-full flex items-end justify-center pb-0">
            <img src="<?php echo esc_url($hero_image); ?>" alt="Valeriya Starostina" class="object-cover h-[90%] w-auto max-w-full drop-shadow-2xl grayscale hover:grayscale-0 transition duration-700 fade-in-up">
        </div>
    </div>

    <div class="w-full md:w-2/3 bg-white flex flex-col justify-center p-8 md:p-16 lg:p-24 relative">
        <div class="absolute top-8 right-8 z-20">
            <ul class="flex gap-4 uppercase text-sm font-semibold tracking-widest">
                <?php if (function_exists('pll_the_languages')) pll_the_languages(array('show_flags'=>0,'show_names'=>1)); ?>
            </ul>
        </div>

        <div class="max-w-2xl">
            <div class="mb-4 fade-in-up">
                <h1 class="text-3xl md:text-4xl lg:text-5xl tracking-widest uppercase font-serif text-gray-800">
                    <?php echo function_exists('pll__') ? pll__('Starostina') : 'Starostina'; ?>
                </h1>
                <div class="relative -mt-4 ml-12 md:ml-24">
                    <span class="font-script text-5xl md:text-7xl lg:text-8xl text-black block transform -rotate-6">
                         <?php echo function_exists('pll__') ? pll__('Valeriya') : 'Valeriya'; ?>
                    </span>
                </div>
            </div>

            <p class="text-sm md:text-base tracking-[0.3em] uppercase text-gray-500 mb-8 fade-in-up delay-200 mt-6">
                <?php echo function_exists('pll__') ? pll__('Fitness Trainer & Nutritionist') : 'Fitness Trainer & Nutritionist'; ?>
            </p>

            <?php if($socials): ?>
            <div class="flex space-x-6 mb-10 fade-in-up delay-200 text-2xl text-gray-700">
                <?php foreach($socials as $social): ?>
                    <?php if(!empty($social['url'])): ?>
                    <a href="<?php echo esc_url($social['url']); ?>" target="_blank" class="hover:text-pink-600 transition duration-300">
                        <i class="<?php echo esc_attr($social['icon_class']); ?>"></i>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="fade-in-up delay-400">
                <button onclick="scrollToPayment()" class="group relative px-8 py-4 bg-gray-100 rounded-full overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="absolute inset-0 w-0 bg-gray-200 transition-all duration-[250ms] ease-out group-hover:w-full"></div>
                    <span class="relative text-gray-800 group-hover:text-black font-serif tracking-wider flex items-center gap-3">
                        <?php echo function_exists('pll__') ? pll__('BUY "NUTRITION GUIDE"') : 'BUY GUIDE'; ?>
                        <i class="fas fa-arrow-down text-xs animate-bounce"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- SCREEN 2: PAYMENT SECTION -->
<section id="payment-section" class="min-h-screen bg-beige-bg flex flex-col items-center justify-center text-center p-6 relative">
    <div class="max-w-3xl w-full">
        <h2 class="font-script text-5xl md:text-7xl mb-2 text-gray-800">
            <?php echo function_exists('pll__') ? pll__('Payment') : 'Payment'; ?>
        </h2>
        
        <div class="h-px w-24 bg-black mx-auto mb-8"></div>

        <h3 class="text-2xl md:text-4xl font-serif uppercase tracking-wider mb-4">
            <?php echo function_exists('pll__') ? pll__('Click to pay') : 'Click to pay'; ?>
        </h3>
        
        <p class="text-lg mb-12 text-gray-600 font-light">
            <?php echo function_exists('pll__') ? pll__('Cost:') : 'Cost:'; ?> <span class="font-semibold text-black"><?php echo $price; ?> UAH</span>
        </p>

        <button id="pay-button" class="transform hover:scale-105 transition duration-300 w-full md:w-auto bg-black text-white px-12 py-5 text-lg tracking-widest uppercase font-serif hover:bg-gray-800 shadow-2xl relative overflow-hidden">
            <span id="btn-text">
                <?php echo function_exists('pll__') ? pll__('Pay via Monobank') : 'Pay via Monobank'; ?> <i class="fas fa-credit-card ml-2"></i>
            </span>
            <span id="btn-loader" class="hidden absolute inset-0 flex items-center justify-center bg-gray-800">
                <i class="fas fa-circle-notch fa-spin"></i>
            </span>
        </button>
        
        <p class="mt-8 text-xs text-gray-500 max-w-md mx-auto">
            *<?php echo function_exists('pll__') ? pll__('After successful payment...') : 'After successful payment...'; ?>
        </p>
    </div>
</section>

<script>
    document.getElementById('pay-button').addEventListener('click', function() {
        const btn = this;
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');

        // UI: Loading
        btnText.classList.add('opacity-0');
        btnLoader.classList.remove('hidden');
        btn.disabled = true;

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
            if(data.success) {
                // !!! ВАЖНО !!! Сохраняем ID в браузер перед уходом
                if(data.data.invoiceId) {
                    localStorage.setItem('starostina_invoice_id', data.data.invoiceId);
                }
                
                // Редирект
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