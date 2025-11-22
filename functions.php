<?php

// 1. Подключение стилей и скриптов
function starostina_scripts() {
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com', array(), '3.0', false);
    
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
                        'script': ['Pinyon Script', 'cursive'],
                    }
                }
            }
        }
    ");

    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');
    wp_enqueue_style('main-style', get_stylesheet_uri());

    // Передаем nonce и url для AJAX
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
        pll_register_string('starostina', 'After successful payment...', 'Starostina Theme');
        pll_register_string('starostina', 'Download', 'Starostina Theme');
        pll_register_string('starostina', 'Click to download file', 'Starostina Theme');
        pll_register_string('starostina', 'Download PDF', 'Starostina Theme');
        pll_register_string('starostina', 'Return to main', 'Starostina Theme');
        pll_register_string('starostina', 'Starostina', 'Starostina Theme');
        pll_register_string('starostina', 'Valeriya', 'Starostina Theme');
        pll_register_string('starostina', 'Processing...', 'Starostina Theme');
    }
});

// 3. ACF Settings & Fields
add_action('acf/init', function() {
    
    // A. Создаем страницу настроек темы (Только в ACF Pro)
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
        
        // B. Группа полей: Настройки Монобанка (в глобальных настройках)
        acf_add_local_field_group(array(
            'key' => 'group_theme_settings',
            'title' => 'Інтеграція Monobank',
            'fields' => array(
                array(
                    'key' => 'field_monobank_token',
                    'label' => 'X-Token (API Monobank)',
                    'name' => 'monobank_token',
                    'type' => 'text', // Можно использовать 'password', если не хочешь, чтобы он светился
                    'instructions' => 'Вставте сюди ваш X-Token з https://api.monobank.ua/',
                    'required' => 1,
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

        // C. Группа полей: Лендинг (на странице)
        acf_add_local_field_group(array(
            'key' => 'group_landing_settings',
            'title' => 'Налаштування Лендінгу',
            'fields' => array(
                array(
                    'key' => 'field_hero_image',
                    'label' => 'Фото на головній',
                    'name' => 'hero_image',
                    'type' => 'image',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_price',
                    'label' => 'Ціна (UAH)',
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
                            'key' => 'field_social_icon',
                            'label' => 'Клас іконки FontAwesome (напр. fab fa-instagram)',
                            'name' => 'icon_class',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_social_url',
                            'label' => 'Посилання',
                            'name' => 'url',
                            'type' => 'url',
                        ),
                    ),
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

// Вспомогательная функция для получения токена
function get_monobank_token() {
    // 'option' означает, что мы берем поле из страницы настроек, а не текущего поста
    return get_field('monobank_token', 'option');
}

// A. AJAX Handler для создания платежа
add_action('wp_ajax_create_mono_invoice', 'handle_create_mono_invoice');
add_action('wp_ajax_nopriv_create_mono_invoice', 'handle_create_mono_invoice');

function handle_create_mono_invoice() {
    check_ajax_referer('monobank_nonce', 'nonce');

    $token = get_monobank_token();
    if (!$token) {
        wp_send_json_error(array('message' => 'API Token не налаштовано в адмінці!'));
        return;
    }

    $page_id = intval($_POST['page_id']);
    $price_uah = get_field('product_price', $page_id) ?: 450;
    
    // Monobank принимает сумму в копейках
    $amount = $price_uah * 100; 

    // Формируем ссылку возврата
    $redirect_url = get_permalink($page_id); 

    $payload = array(
        'amount' => $amount,
        'ccy' => 980, // Гривна
        'merchantPaymInfo' => array(
            'reference' => 'order_' . time() . '_' . rand(1000, 9999),
            'destination' => 'Nutrition Guide Payment',
        ),
        'redirectUrl' => $redirect_url, 
        'validity' => 3600, 
        'paymentType' => 'debit',
    );

    $response = wp_remote_post('https://api.monobank.ua/api/merchant/invoice/create', array(
        'headers' => array(
            'X-Token' => $token, // Используем токен из админки
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($payload)
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['pageUrl'])) {
        wp_send_json_success(array('url' => $body['pageUrl']));
    } else {
        wp_send_json_error(array('message' => 'Mono API Error', 'debug' => $body));
    }
}

// B. Функция проверки статуса платежа
function check_monobank_payment_status($invoice_id) {
    if (!$invoice_id) return false;

    $token = get_monobank_token();
    if (!$token) return false;

    $response = wp_remote_get('https://api.monobank.ua/api/merchant/invoice/status?invoiceId=' . $invoice_id, array(
        'headers' => array(
            'X-Token' => $token, // Используем токен из админки
            'Content-Type' => 'application/json'
        )
    ));

    if (is_wp_error($response)) return false;

    $body = json_decode(wp_remote_retrieve_body($response), true);

    // Статус 'success' означает успешную оплату
    if (isset($body['status']) && $body['status'] === 'success') {
        return true;
    }

    return false;
}