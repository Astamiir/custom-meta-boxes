<?php
    include(plugin_dir_path( __FILE__ ).'/classes/custom-meta-boxes.php');
    new customMetaBoxes(
        array(
            'id'	=>	'coaches_plan_meta',
            'name'	=>	__( 'Additional fields', 'moncoach-elementor-addon' ),
            'post'	=>	array('coaches_plan'),
            'pos'	=>	'normal',
            'pri'	=>	'high',
            'cap'	=>	'edit_posts',
            'args'	=>	array(
                array(
                    'id'			=>	'price',
                    'title'			=>	__( 'Plan price', 'moncoach-elementor-addon' ),
                    'type'			=>	'text',
                    'placeholder'	=>	'',
                    'desc'			=>	'',
                    'cap'			=>	'edit_posts'
                ),
                array(
                    'id'			=>	'discount_percent',
                    'title'			=>	__( 'Discount percent', 'moncoach-elementor-addon' ),
                    'type'			=>	'text',
                    'placeholder'	=>	'',
                    'desc'			=>	'',
                    'cap'			=>	'edit_posts'
                ),
                array(
                    'id'			=>	'working_hours_count',
                    'title'			=>	__( 'Working hours count', 'moncoach-elementor-addon' ),
                    'type'			=>	'text',
                    'placeholder'	=>	'',
                    'desc'			=>	'',
                    'cap'			=>	'edit_posts'
                ),
                array(
                    'id'			=>	'image',
                    'title'			=>	__( 'Image', 'moncoach-elementor-addon' ),
                    'type'			=>	'media',
                    'placeholder'	=>	'',
                    'desc'			=>	'',
                    'cap'			=>	'edit_posts'
                ),
            )
        )
    );