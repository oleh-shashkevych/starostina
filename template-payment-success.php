<?php
/*
Template Name: Результат Оплати
*/

// Получаем ссылку на файл для JS
$front_page_id = get_option('page_on_front');
if (function_exists('pll_get_post')) {
    $front_page_id = pll_get_post($front_page_id);
}
$file_url = get_field('product_file', $front_page_id);

get_header(); 
?>

<section class="min-h-screen bg-beige-bg flex flex-col items-center justify-center text-center p-6">
    <div class="max-w-3xl w-full animate-fade-in-up">
        
        <!-- LOADER & STATUS -->
        <div id="status-block">
            <h2 class="font-script text-4xl md:text-5xl mb-4 text-gray-800">
                <?php echo function_exists('pll__') ? pll__('Checking payment status...') : 'Checking payment...'; ?>
            </h2>
            <div class="text-4xl animate-spin text-gray-400">
                <i class="fas fa-circle-notch"></i>
            </div>
            <p id="debug-text" class="mt-4 text-xs text-gray-400 font-mono"></p>
        </div>

        <!-- SUCCESS (Hidden by default) -->
        <div id="success-block" class="hidden">
            <h2 class="font-script text-5xl md:text-7xl mb-2 text-gray-800">
                <?php echo function_exists('pll__') ? pll__('Payment Successful') : 'Payment Successful'; ?>
            </h2>
            <div class="h-px w-24 bg-black mx-auto mb-8"></div>
            <h3 class="text-2xl md:text-4xl font-serif uppercase tracking-wider mb-6">
                <?php echo function_exists('pll__') ? pll__('Click to download file') : 'Download your file'; ?>
            </h3>

            <?php if($file_url): ?>
            <a href="<?php echo esc_url($file_url); ?>" download class="inline-flex items-center gap-3 transform hover:scale-105 transition duration-300 bg-black text-white px-10 py-5 text-lg tracking-widest uppercase font-serif hover:bg-gray-800 shadow-2xl">
                <i class="fas fa-file-pdf"></i> <?php echo function_exists('pll__') ? pll__('Download PDF') : 'Download PDF'; ?>
            </a>
            <?php else: ?>
                <div class="p-4 bg-red-100 text-red-700">File not configured. Contact admin.</div>
            <?php endif; ?>
        </div>

        <!-- ERROR (Hidden by default) -->
        <div id="error-block" class="hidden">
            <h2 class="font-script text-5xl md:text-7xl mb-2 text-red-800">
                Ooops...
            </h2>
            <p class="text-xl mb-4 text-gray-600" id="error-message">
                Payment not found.
            </p>
            <button onclick="location.reload()" class="underline text-sm">Try again</button>
        </div>

        <div class="mt-12">
            <a href="<?php echo home_url(); ?>" class="text-gray-500 text-sm hover:text-black underline underline-offset-4">
                <?php echo function_exists('pll__') ? pll__('Return to main') : 'Return to main'; ?>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusBlock = document.getElementById('status-block');
    const successBlock = document.getElementById('success-block');
    const errorBlock = document.getElementById('error-block');
    const errorMsg = document.getElementById('error-message');
    const debugText = document.getElementById('debug-text');

    // 1. Ищем ID в URL (на всякий случай)
    const urlParams = new URLSearchParams(window.location.search);
    let invoiceId = urlParams.get('invoiceId');

    // 2. Если нет в URL, ищем в LocalStorage
    if (!invoiceId) {
        invoiceId = localStorage.getItem('starostina_invoice_id');
        debugText.innerText = "Checking LocalStorage...";
    } else {
        debugText.innerText = "Checking URL...";
    }

    if (!invoiceId) {
        showError("Invoice ID not found. Please try purchasing again.");
        return;
    }

    // 3. AJAX Проверка
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
            // ОПЛАТА ПРОШЛА!
            statusBlock.classList.add('hidden');
            successBlock.classList.remove('hidden');
            // Чистим storage, чтобы не мешал потом
            localStorage.removeItem('starostina_invoice_id');
        } else {
            // Ошибка или статус не success
            showError("Payment status: " + (data.data?.status || "Unknown"));
            console.log(data);
        }
    })
    .catch(err => {
        showError("Connection error.");
        console.error(err);
    });

    function showError(msg) {
        statusBlock.classList.add('hidden');
        errorBlock.classList.remove('hidden');
        errorMsg.innerText = msg;
    }
});
</script>

<?php get_footer(); ?>