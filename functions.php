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

    wp_localize_script('tailwind', 'wpData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('monobank_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'starostina_scripts');

// 2. Регистрация строк для Polylang
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
    }
});

// 3. Регистрация ACF полей (через хук acf/init для надежности)
add_action('acf/init', function() {
    if( function_exists('acf_add_local_field_group') ):
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
                // Логика ИЛИ: либо шаблон по умолчанию, либо страница, установленная как главная
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