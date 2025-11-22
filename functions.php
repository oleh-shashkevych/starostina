<?php

// 1. Подключение стилей и скриптов
function starostina_scripts() {
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com', array(), '3.0', false);
    
    // Конфигурация Tailwind
    // ВАЖНО: В font-family 'script' мы ставим сначала Great Vibes, потом Marck Script
    wp_add_inline_script('tailwind', "
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-bg': '#1a1a1a',
                        'beige-bg': '#efe3dd',
                        'text-dark': '#333333',
                    },
                    fontFamily: {
                        'sans': ['Montserrat', 'sans-serif'],
                        'serif': ['Playfair Display', 'serif'],
                        'script': ['Playfair Display', 'Great Vibes', 'Marck Script', 'cursive'], 
                    },
                    screens: {
                        'xs': '475px',
                    }
                }
            }
        }
    ");

    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    
    // UPDATED: Подключаем ВСЕ шрифты одной ссылкой (Great Vibes + Marck Script + остальные)
    // Это самый надежный способ, чтобы Google отдал их все сразу.
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Great+Vibes&family=Marck+Script&family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap&subset=cyrillic,latin');
    
    wp_enqueue_style('main-style', get_stylesheet_uri());

    wp_localize_script('tailwind', 'wpData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('monobank_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'starostina_scripts');

// 2. Polylang Strings
add_action('init', function() {
    if (function_exists('pll_register_string')) {
        pll_register_string('starostina', 'Fitness Trainer & Nutritionist', 'Starostina Theme');
        pll_register_string('starostina', 'BUY "NUTRITION GUIDE"', 'Starostina Theme');
        pll_register_string('starostina', 'Payment', 'Starostina Theme');
        pll_register_string('starostina', 'Click to pay', 'Starostina Theme');
        pll_register_string('starostina', 'Cost:', 'Starostina Theme');
        pll_register_string('starostina', 'Pay via Monobank', 'Starostina Theme');
        pll_register_string('starostina', 'Return to main', 'Starostina Theme');
        pll_register_string('starostina', 'Starostina', 'Starostina Theme');
        pll_register_string('starostina', 'Valeriya', 'Starostina Theme');
        pll_register_string('starostina', 'Checking payment status...', 'Starostina Theme');
        pll_register_string('starostina', 'Connection error.', 'Starostina Theme');
        pll_register_string('starostina', 'Payment status:', 'Starostina Theme');
        pll_register_string('starostina', 'Unknown', 'Starostina Theme');
    }
});

// 3. ACF Settings
add_action('acf/init', function() {
    if( function_exists('acf_add_options_page') ) {
        acf_add_options_page(array(
            'page_title'    => 'Налаштування Теми',
            'menu_title'    => 'Налаштування Теми',
            'menu_slug'     => 'theme-general-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false,
            'icon_url'      => 'dashicons-admin-generic',
        ));
    }

    if( function_exists('acf_add_local_field_group') ):
        
        // --- Группа 1: Монобанк ---
        acf_add_local_field_group(array(
            'key' => 'group_theme_settings',
            'title' => 'Інтеграція Monobank',
            'fields' => array(
                array(
                    'key' => 'field_monobank_token',
                    'label' => 'X-Token (API Monobank)',
                    'name' => 'monobank_token',
                    'type' => 'text', 
                    'required' => 1,
                ),
                array(
                    'key' => 'field_success_page',
                    'label' => 'Сторінка успішної оплати',
                    'name' => 'success_page_link',
                    'type' => 'page_link', 
                    'required' => 1,
                    'post_type' => array('page'),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-general-settings',
                    ),
                ),
            ),
        ));

        // --- Группа 2: Лендинг (Главная) ---
        acf_add_local_field_group(array(
            'key' => 'group_landing_settings',
            'title' => 'Налаштування Лендінгу (Головна)',
            'fields' => array(
                array(
                    'key' => 'field_hero_image',
                    'label' => 'Фото на головній',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_old_price',
                    'label' => 'Стара ціна (закреслена)',
                    'name' => 'product_old_price',
                    'type' => 'number',
                    'instructions' => 'Відображається як знижка. Залиште пустим, якщо не потрібно.',
                ),
                array(
                    'key' => 'field_price',
                    'label' => 'Актуальна Ціна (UAH)',
                    'name' => 'product_price',
                    'type' => 'number',
                    'default_value' => 450,
                ),
                array(
                    'key' => 'field_pdf_file',
                    'label' => 'Файл для завантаження (PDF)',
                    'name' => 'product_file',
                    'type' => 'file',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_socials',
                    'label' => 'Соцмережі',
                    'name' => 'social_links',
                    'type' => 'repeater',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_social_image',
                            'label' => 'Іконка (Зображення)',
                            'name' => 'icon_image',
                            'type' => 'image',
                            'return_format' => 'url',
                            'preview_size' => 'thumbnail',
                        ),
                        array(
                            'key' => 'field_social_url',
                            'label' => 'Посилання (URL, tel:, viber:)',
                            'name' => 'url',
                            'type' => 'text', 
                        ),
                    ),
                ),
                array(
                    'key' => 'field_payment_footer_text',
                    'label' => 'Текст під кнопкою оплати',
                    'name' => 'payment_footer_text',
                    'type' => 'text',
                    'default_value' => '*Після успішної оплати сторінка автоматично оновиться і ви отримаєте доступ до файлу.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_type',
                        'operator' => '==',
                        'value' => 'front_page',
                    ),
                ),
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'default',
                    ),
                ),
            ),
        ));

        // --- Группа 3: Настройки Страницы Успеха ---
        acf_add_local_field_group(array(
            'key' => 'group_success_page_settings',
            'title' => 'Налаштування: Сторінка Успіху',
            'fields' => array(
                array(
                    'key' => 'field_success_title',
                    'label' => 'Заголовок успіху',
                    'name' => 'success_title',
                    'type' => 'text',
                    'default_value' => 'Payment Successful',
                    'instructions' => 'Великий рукописний текст (напр. "Завантаження")',
                ),
                array(
                    'key' => 'field_success_subtitle',
                    'label' => 'Підзаголовок успіху',
                    'name' => 'success_subtitle',
                    'type' => 'text',
                    'default_value' => 'Click to download file',
                    'instructions' => 'Текст перед кнопкою (напр. "НАТИСНІТЬ ДЛЯ ЗАВАНТАЖЕННЯ")',
                ),
                array(
                    'key' => 'field_download_btn_text',
                    'label' => 'Текст на кнопці скачування',
                    'name' => 'download_btn_text',
                    'type' => 'text',
                    'default_value' => 'Download PDF',
                ),
                array(
                    'key' => 'field_thank_you_text',
                    'label' => 'Текст подяки (внизу)',
                    'name' => 'thank_you_text',
                    'type' => 'textarea',
                    'rows' => 3,
                    'default_value' => '"Дякую за довіру! Сподіваюсь цей гід допоможе тобі харчуватись смачно та корисно."',
                ),
                array(
                    'key' => 'field_error_title',
                    'label' => 'Заголовок помилки',
                    'name' => 'error_title',
                    'type' => 'text',
                    'default_value' => 'Ooops...',
                ),
                array(
                    'key' => 'field_error_text',
                    'label' => 'Текст помилки',
                    'name' => 'error_text',
                    'type' => 'text',
                    'default_value' => 'Payment info not found.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'template-payment-success.php',
                    ),
                ),
            ),
        ));

    endif;
});

add_action('after_setup_theme', function() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
});

// ==========================================
// MONOBANK LOGIC
// ==========================================

function get_monobank_token() {
    return get_field('monobank_token', 'option');
}

// 1. AJAX CREATE INVOICE
add_action('wp_ajax_create_mono_invoice', 'handle_create_mono_invoice');
add_action('wp_ajax_nopriv_create_mono_invoice', 'handle_create_mono_invoice');

function handle_create_mono_invoice() {
    check_ajax_referer('monobank_nonce', 'nonce');

    $token = get_monobank_token();
    if (!$token) {
        wp_send_json_error(array('message' => 'API Token error'));
        return;
    }

    $success_page_url = get_field('success_page_link', 'option') ?: home_url();
    $page_id = intval($_POST['page_id']);
    $price_uah = get_field('product_price', $page_id) ?: 450;
    
    $redirect_url = add_query_arg('payment_check', '1', $success_page_url);

    $payload = array(
        'amount' => $price_uah * 100,
        'ccy' => 980,
        'merchantPaymInfo' => array(
            'reference' => 'order_' . time() . '_' . rand(1000, 9999),
            'destination' => 'Nutrition Guide Payment',
        ),
        'redirectUrl' => $redirect_url, 
        'validity' => 3600, 
        'paymentType' => 'debit',
    );

    $response = wp_remote_post('https://api.monobank.ua/api/merchant/invoice/create', array(
        'headers' => array('X-Token' => $token, 'Content-Type' => 'application/json'),
        'body' => json_encode($payload)
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['pageUrl']) && isset($body['invoiceId'])) {
        wp_send_json_success(array(
            'url' => $body['pageUrl'],
            'invoiceId' => $body['invoiceId']
        ));
    } else {
        wp_send_json_error(array('message' => 'Mono Error', 'debug' => $body));
    }
}

// 2. AJAX CHECK STATUS
add_action('wp_ajax_check_mono_status', 'handle_check_mono_status');
add_action('wp_ajax_nopriv_check_mono_status', 'handle_check_mono_status');

function handle_check_mono_status() {
    $invoice_id = sanitize_text_field($_POST['invoice_id']);
    if (!$invoice_id) wp_send_json_error(array('message' => 'No ID'));

    $token = get_monobank_token();
    if (!$token) wp_send_json_error(array('message' => 'No Token'));

    $response = wp_remote_get('https://api.monobank.ua/api/merchant/invoice/status?invoiceId=' . $invoice_id, array(
        'headers' => array('X-Token' => $token, 'Content-Type' => 'application/json')
    ));

    if (is_wp_error($response)) wp_send_json_error(array('message' => 'API Error'));

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['status']) && $body['status'] === 'success') {
        wp_send_json_success($body);
    } else {
        wp_send_json_error($body);
    }
}