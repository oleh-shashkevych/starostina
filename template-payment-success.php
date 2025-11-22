<?php
/*
Template Name: Результат Оплати
*/

// ... (PHP код получения полей ACF оставляем как был) ...
$front_page_id = get_option('page_on_front');
if (function_exists('pll_get_post')) {
    $front_page_id = pll_get_post($front_page_id);
}
$file_url = get_field('product_file', $front_page_id);
$page_id = get_queried_object_id();

$success_title = get_field('success_title', $page_id) ?: 'Payment Successful';
$success_subtitle = get_field('success_subtitle', $page_id) ?: 'Click to download file';
$download_btn_text = get_field('download_btn_text', $page_id) ?: 'Download PDF';
$thank_you_text = get_field('thank_you_text', $page_id);
$error_title = get_field('error_title', $page_id) ?: 'Ooops...';
$error_text = get_field('error_text', $page_id) ?: 'Payment info not found.';

// ПОДГОТОВКА ПЕРЕВОДОВ ДЛЯ JS
$js_conn_error = function_exists('pll__') ? pll__('Connection error.') : 'Connection error.';
$js_status_label = function_exists('pll__') ? pll__('Payment status:') : 'Payment status:';
$js_unknown = function_exists('pll__') ? pll__('Unknown') : 'Unknown';

get_header(); 
?>

<section class="min-h-screen bg-beige-bg flex flex-col items-center justify-center text-center p-6 md:p-8 relative">
    <!-- (HTML разметка остается той же самой, сократил для удобства чтения) -->
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-black/10 to-transparent"></div>

    <div class="max-w-3xl w-full animate-fade-in-up relative z-10">
        <!-- LOADER -->
        <div id="status-block">
            <h2 class="font-script text-4xl md:text-6xl mb-8 text-gray-800">
                <?php echo function_exists('pll__') ? pll__('Checking payment status...') : 'Checking payment...'; ?>
            </h2>
            <div class="inline-block p-4 rounded-full bg-white/30 backdrop-blur-sm">
                <i class="fas fa-circle-notch fa-spin text-3xl text-gray-600"></i>
            </div>
            <p id="debug-text" class="mt-6 text-[10px] uppercase tracking-widest text-gray-400 font-sans"></p>
        </div>

        <!-- SUCCESS -->
        <div id="success-block" class="hidden">
            <h2 class="font-script text-5xl md:text-8xl mb-4 text-gray-900 transform -rotate-2">
                <?php echo esc_html($success_title); ?>
            </h2>
            <div class="flex justify-center mb-10"><div class="h-px w-16 md:w-24 bg-black/20"></div></div>
            <h3 class="text-lg md:text-3xl font-serif uppercase tracking-widest mb-8 text-gray-800 px-4">
                <?php echo esc_html($success_subtitle); ?>
            </h3>
            <?php if($file_url): ?>
            <div class="relative group inline-block w-full max-w-xs md:max-w-none">
                <div class="absolute -inset-1 bg-black/10 rounded-lg blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                <a href="<?php echo esc_url($file_url); ?>" download class="relative flex items-center justify-center gap-4 bg-black text-white px-8 py-5 md:px-12 md:py-6 text-xs md:text-base tracking-[0.2em] uppercase font-sans font-bold hover:bg-gray-800 transition-all duration-300 shadow-2xl w-full md:w-auto">
                    <i class="fas fa-file-pdf text-xl md:text-2xl"></i> 
                    <span><?php echo esc_html($download_btn_text); ?></span>
                </a>
            </div>
            <?php else: ?>
                <div class="p-6 bg-red-50 border border-red-100 text-red-600 rounded-lg">
                    File not found. Please contact support.
                </div>
            <?php endif; ?>
             <?php if($thank_you_text): ?>
             <p class="mt-12 text-sm md:text-lg text-gray-600 font-serif italic max-w-lg mx-auto leading-relaxed px-4">
                <?php echo nl2br(esc_html($thank_you_text)); ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- ERROR -->
        <div id="error-block" class="hidden">
            <h2 class="font-script text-5xl md:text-7xl mb-6 text-red-900/80">
                <?php echo esc_html($error_title); ?>
            </h2>
            <div class="bg-white/50 p-6 md:p-8 rounded-xl backdrop-blur-sm border border-red-100 inline-block mx-4">
                <p class="text-base md:text-lg mb-6 text-gray-700" id="error-message">
                    <?php echo esc_html($error_text); ?>
                </p>
                <button onclick="location.href='<?php echo home_url(); ?>'" class="uppercase tracking-widest text-xs font-bold border-b border-black pb-1 hover:text-gray-600 transition">
                    Спробувати ще
                </button>
            </div>
        </div>

        <div class="mt-16">
            <a href="<?php echo home_url(); ?>" class="text-gray-400 text-[10px] uppercase tracking-[0.2em] hover:text-black transition duration-300">
                <i class="fas fa-long-arrow-alt-left mr-2"></i>
                <?php echo function_exists('pll__') ? pll__('Return to main') : 'Return to main'; ?>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Объект с переводами из PHP
    const jsText = {
        connError: "<?php echo esc_js($js_conn_error); ?>",
        statusPrefix: "<?php echo esc_js($js_status_label); ?>",
        unknown: "<?php echo esc_js($js_unknown); ?>"
    };

    const statusBlock = document.getElementById('status-block');
    const successBlock = document.getElementById('success-block');
    const errorBlock = document.getElementById('error-block');
    const errorMsg = document.getElementById('error-message');
    const debugText = document.getElementById('debug-text');

    const urlParams = new URLSearchParams(window.location.search);
    let invoiceId = urlParams.get('invoiceId');

    if (!invoiceId) {
        invoiceId = localStorage.getItem('starostina_invoice_id');
        debugText.innerText = "Checking Storage...";
    } else {
        debugText.innerText = "Checking URL...";
    }

    // Если нет ID - показываем ошибку из ACF поля (оно уже переведено на уровне PHP выше)
    if (!invoiceId) {
        showError("<?php echo esc_js($error_text); ?>");
        return;
    }

    // --- DEV MODE CHECK ---
    if (invoiceId === 'test_mode_payment_ok') {
        setTimeout(() => {
            statusBlock.classList.add('hidden');
            successBlock.classList.remove('hidden');
            localStorage.removeItem('starostina_invoice_id');
        }, 800);
        return;
    }
    // ----------------------

    const formData = new FormData();
    formData.append('action', 'check_mono_status');
    formData.append('invoice_id', invoiceId);

    fetch(wpData.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusBlock.classList.add('hidden');
            successBlock.classList.remove('hidden');
            localStorage.removeItem('starostina_invoice_id');
        } else {
            // ИСПОЛЬЗУЕМ ПЕРЕВОДЫ
            const status = data.data?.status || jsText.unknown;
            showError(jsText.statusPrefix + " " + status);
        }
    })
    .catch(err => {
        // ИСПОЛЬЗУЕМ ПЕРЕВОДЫ
        showError(jsText.connError);
    });

    function showError(msg) {
        statusBlock.classList.add('hidden');
        errorBlock.classList.remove('hidden');
        if(msg) errorMsg.innerText = msg;
    }
});
</script>

<?php get_footer(); ?>