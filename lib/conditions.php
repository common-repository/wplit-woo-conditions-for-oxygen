<?php
/**
 * Woo Conditions
 *
 * @package WPlit Woo Conditions for Oxygen
 * @author  Wplit
 * @license GPL-2.0-or-later
 * @link    https://wplit.com/
 */


add_action( 'init' , 'lit_wco_register_empty_cart_condition' );
function lit_wco_register_empty_cart_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Has Empty Cart',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_empty_cart_callback',

        // Condition Category
        'Woo User'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_empty_cart_callback( $value, $operator ) {
    
    if (! is_user_logged_in() ) {
        return false;
    }

	$emptycart = WC()->cart->is_empty();
    
    $value = (bool) $value;
    
    return oxy_condition_eval_string($emptycart, $value, $operator);

}

add_action( 'init' , 'lit_wco_register_product_in_cart_condition' );
function lit_wco_register_product_in_cart_condition() {
    
    $args = array(
      'posts_per_page'  => -1,
      'post_type' => 'product',
    );

    $product_id_raw = get_posts( $args );

    $product_id_clean = array();

    foreach($product_id_raw as $product_id) {
            array_push($product_id_clean, $product_id->post_title);
    }

    global $oxy_condition_operators;
    
    oxygen_vsb_register_condition(
		// Condition Name
		'Has Product in Cart',

		// Values: The array of pre-set values the user can choose from.
		array( 
			'options' => $product_id_clean,
			'custom' => false
		),

		// Operators
		$oxy_condition_operators['simple'],
		
		// Callback Function
		'lit_wco_condition_product_in_cart_callback',

		// Condition Category
		'Woo User'
	);
    
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the name of product.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_in_cart_callback( $value, $operator ) {
    
    if (! is_user_logged_in() ) {
        return false;
    }
    
    $product_object = get_page_by_title( $value, OBJECT, 'product' );
    
    if ( is_null($product_object) ) {
        return false;
    }

    $id = $product_object->ID;
 
    $product_cart_id = WC()->cart->generate_cart_id( $id );
    
    if ( $operator == "==" ) {
		
		return ( WC()->cart->find_product_in_cart( $product_cart_id ) ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( WC()->cart->find_product_in_cart( $product_cart_id ) ) ? false : true;
	}
        
}


add_action( 'init' , 'lit_wco_register_product_is_type_condition' );
function lit_wco_register_product_is_type_condition() {

    $product_types = wc_get_product_types();
    $product_types_keys = array_keys($product_types);
    
    oxygen_vsb_register_condition(
		// Condition Name
		'Product Type',

		// Values: The array of pre-set values the user can choose from.
		array( 
			'options' => $product_types_keys,
		),

		// Operators
		array( '==', '!=' ),
		
		// Callback Function
		'lit_wco_condition_product_type_callback',

		// Condition Category
		'Woo Product'
	);
    
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the name of product type.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_type_callback( $value, $operator ) {
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
    $product_id = $product->get_id();
    
    $product_type = WC_Product_Factory::get_product_type($product_id);
    
    $value = (string) $value;

  if ( $operator == "==" ) {
		
		return ( $product_type == $value ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( $product_type !== $value ) ? false : true;
	}

}


add_action( 'init' , 'lit_wco_register_product_in_cat_condition' );
function lit_wco_register_product_in_cat_condition() {
    
    $args = array(
		'hide_empty' => 0
	);

	$product_categories_raw = get_terms( 'product_cat', $args );

	$product_categories_clean = array();

	foreach($product_categories_raw as $product_category) {
		array_push($product_categories_clean, $product_category->name);
	}

	global $oxy_condition_operators;

    oxygen_vsb_register_condition(
		// Condition Name
		'Product in Category',

		// Values
		array( 
			'options' => $product_categories_clean,
			'custom' => false
		),

		// Operators
		$oxy_condition_operators['simple'],
		
		// Callback Function
		'lit_wco_condition_product_in_category_callback',

		// Condition Category
		'Woo Product'
	);
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - Product Category
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_in_category_callback( $value, $operator ) {
    
    $product_in_category = has_term( $value, 'product_cat' );
    
     if ( $operator == "==" ) {
		
		return ( $product_in_category ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( $product_in_category ) ? false : true;
	}
    
}
    
    

add_action( 'init' , 'lit_wco_register_product_in_tag_condition' );
function lit_wco_register_product_in_tag_condition() {
    
    $args = array(
		'hide_empty' => 0
	);

	$product_tags_raw = get_terms( 'product_tag', $args );

	$product_tags_clean = array();

	foreach($product_tags_raw as $product_tag) {
		array_push($product_tags_clean, $product_tag->name);
	}

	global $oxy_condition_operators;

    oxygen_vsb_register_condition(
		// Condition Name
		'Product has Tag',

		// Values: The array of pre-set values the user can choose from.
		array( 
			'options' => $product_tags_clean,
			'custom' => false
		),

		// Operators
		$oxy_condition_operators['simple'],
		
		// Callback Function
		'lit_wco_condition_product_in_tag_callback',

		// Condition Category
		'Woo Product'
	);
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the name of product tag.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_in_tag_callback( $value, $operator ) {
    
    $product_has_tag = has_term( $value, 'product_tag' );
    
     if ( $operator == "==" ) {
		
		return ( $product_has_tag ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( $product_has_tag ) ? false : true;
	}
    
}    
    


add_action( 'init' , 'lit_wco_register_product_on_sale_condition' );
function lit_wco_register_product_on_sale_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Product on Sale',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_product_on_sale_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_on_sale_callback( $value, $operator ) {
    
    // TO DO include variable products
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
	$product_on_onsale = $product->is_on_sale();

	$value = (bool) $value;

	return oxy_condition_eval_string($product_on_onsale, $value, $operator);

}

add_action( 'init' , 'lit_wco_register_product_is_virtual_condition' );
function lit_wco_register_product_is_virtual_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Product is Virtual',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_product_is_virtual_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_is_virtual_callback( $value, $operator ) {
    
    // TO DO include variable products
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
	$product_is_virtual = $product->is_virtual();

	$value = (bool) $value;

	return oxy_condition_eval_string($product_is_virtual, $value, $operator);

}

add_action( 'init' , 'lit_wco_register_product_is_downloadable_condition' );
function lit_wco_register_product_is_downloadable_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Product is Downloadable',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_product_is_downloadable_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_is_downloadable_callback( $value, $operator ) {
    
   // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
	$productisdownloadable = $product->is_downloadable();

	$value = (bool) $value;

	return oxy_condition_eval_string($productisdownloadable, $value, $operator);

}



add_action( 'init' , 'lit_wco_register_product_has_image_condition' );
function lit_wco_register_product_has_image_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Product Has Image',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_product_has_image_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_has_image_callback( $value, $operator ) {
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
	$product_image = $product->get_image_id();

	$value = (bool) $value;
	
	if( $product_image != null ) {
		$product_has_image = true;
	} else {
		$product_has_image = false;
	}
	
	return oxy_condition_eval_string($product_has_image, $value, $operator);

}

add_action( 'init' , 'lit_wco_register_product_in_stock_condition' );
function lit_wco_register_product_in_stock_condition() {

   global $oxy_condition_operators; 

    oxygen_vsb_register_condition(

        // Condition Name
        'Product in Stock',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(true, false),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_product_in_stock_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_product_in_stock_callback( $value, $operator ) {
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
	$product_in_stock = $product->is_in_stock();

	$value = (bool) $value;

	return oxy_condition_eval_string($product_in_stock, $value, $operator);

}

add_action( 'init' , 'lit_wco_register_cart_weight_condition' );
function lit_wco_register_cart_weight_condition() {

   global $oxy_condition_operators;
    
    $weight_unit = get_option('woocommerce_weight_unit');

    oxygen_vsb_register_condition(

        // Condition Name
        'Has Cart Weight (' . $weight_unit . ')',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(),
            'custom' => true
        ),

        // Operators
        $oxy_condition_operators['int'],

        // Callback Function
        'lit_wco_condition_cart_weight_callback',

        // Condition Category
        'Woo User'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the cart weight value.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_cart_weight_callback( $value, $operator ) {
    
    if (! is_user_logged_in() ) {
        return false;
    }

    $cart_weight = WC()->cart->cart_contents_weight;
    
    $cart_weight = intval($cart_weight);
    
	$value = intval($value);

	return oxy_condition_eval_int($cart_weight, $value, $operator);

}


add_action( 'init' , 'lit_wco_register_endpoint_condition' );
function lit_wco_register_endpoint_condition() {

   global $oxy_condition_operators;

    oxygen_vsb_register_condition(

        // Condition Name
        'Is at Endpoint',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array('Any', 'Order Pay','Order received','View order','Edit account','Edit Addresses','Payment methods','Lost password','Customer Logout'),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_endpoint_callback',

        // Condition Category
        'Woo User'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - The name of endpoint.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_endpoint_callback( $value, $operator ) {
    
    if (! is_user_logged_in() ) {
        return false;
    }
    
    if ($value == 'Any') {
        
        $endpoint_url = is_wc_endpoint_url();
        
    } else if ($value == 'Order Pay') {
        
        $endpoint_url = is_wc_endpoint_url( 'order-pay' );
        
    } else if ($value == 'Order received') {
        
        $endpoint_url = is_wc_endpoint_url( 'order-received' );
        
    } else if ($value == 'View order') {
        
        $endpoint_url = is_wc_endpoint_url( 'view-order' );
        
    } else if ($value == 'Edit account') {
        
        $endpoint_url = is_wc_endpoint_url( 'edit-account' );
        
    } else if ($value == 'Edit Addresses') {
        
        $endpoint_url = is_wc_endpoint_url( 'edit-address' );
        
    } else if ($value == 'Payment methods') {
        
        $endpoint_url = is_wc_endpoint_url( 'add-payment-method' );
        
    } else if ($value == 'Lost password') {
        
        $endpoint_url = is_wc_endpoint_url( 'lost-password' );
        
    } else if ($value == 'Customer Logout') { 
        
        $endpoint_url = is_wc_endpoint_url( 'customer-logout' );
        
    }
    
    if ( $operator == "==" ) {
		
		return ( $endpoint_url ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( $endpoint_url ) ? false : true;
	}
    
}























add_action( 'init' , 'lit_wco_register_customer_bought_product_condition' );
function lit_wco_register_customer_bought_product_condition() {

   global $oxy_condition_operators;

    oxygen_vsb_register_condition(

        // Condition Name
        'Product Already Purchased',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array('true', 'false'),
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_customer_bought_product_callback',

        // Condition Category
        'Woo Product'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, true or false
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_customer_bought_product_callback( $value, $operator ) {
    
    // Check that we are either on single product page, or if we're in repeater. Returns false if we're on product archive / shop pages
    if ( wc_get_product() && !is_shop() && !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ) {
        $product = wc_get_product();
    } else {
        return false;
    }
    
    $product_id = $product->get_id();
    $current_user = wp_get_current_user();
    $customer_has_bought_product = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id );

	$should_have_bought = false;

	if($value == 'true') {
		$should_have_bought = true;
	}

	if($operator == '!=') {
		return ($customer_has_bought_product !== $should_have_bought);
	}
	else {
		return ($customer_has_bought_product === $should_have_bought);
	}
    
}


add_action( 'init' , 'lit_wco_register_user_purchased_product_condition' );
function lit_wco_register_user_purchased_product_condition() {

   global $oxy_condition_operators;
    
    $args = array(
      'posts_per_page'  => -1,
      'post_type' => 'product',
    );

    $product_id_raw = get_posts( $args );

    $product_id_clean = array();

    foreach($product_id_raw as $product_id) {
            array_push($product_id_clean, $product_id->post_title);
    }

    oxygen_vsb_register_condition(

        // Condition Name
        'Has Purchased Product',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => $product_id_clean,
            'custom' => false
        ),

        // Operators
        $oxy_condition_operators['simple'],

        // Callback Function
        'lit_wco_condition_user_purchased_product_callback',

        // Condition Category
        'Woo User'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the product title.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_user_purchased_product_callback( $value, $operator ) {

    if (! is_user_logged_in() ) {
        return false;
    }
    
    $product_object = get_page_by_title( $value, OBJECT, 'product' );
    
    if ( is_null($product_object) ) {
        return false;
    }
    
    $id = $product_object->ID;
    
    $current_user = wp_get_current_user();
    
    $customer_bought_product = wc_customer_bought_product( $current_user->user_email, $current_user->ID, $id );
    
     if ( $operator == "==" ) {
		
		return ( $customer_bought_product ) ? true : false;
        
	} else if ( $operator == "!=") {
		
		return ( $customer_bought_product ) ? false : true;
	}
    
}


add_action( 'init' , 'lit_wco_register_cart_total_condition' );
function lit_wco_register_cart_total_condition() {

   global $oxy_condition_operators;
    
    $currency_symbol = get_option('woocommerce_currency');

    oxygen_vsb_register_condition(

        // Condition Name
        'Has Cart Total (' . $currency_symbol . ')',

        // Values: The array of pre-set values the user can choose from.
        array( 
            'options' => array(),
            'custom' => true
        ),

        // Operators
        $oxy_condition_operators['int'],

        // Callback Function
        'lit_wco_condition_cart_total_callback',

        // Condition Category
        'Woo User'
    );
}

/**
 * Callback function to handle the condition.
 * @param  mixed 	$value    	Input value - in this case, the cart total.
 * @param  string 	$operator 	Comparison operator selected by the user.
 *
 * @return boolean 				true or false.
 */
function lit_wco_condition_cart_total_callback( $value, $operator ) {
    
    if (! is_user_logged_in() ) {
        return false;
    }

    $cart_total = WC()->cart->get_cart_contents_total();
    
    $cart_total = intval($cart_total);
    
	$value = intval($value);

	return oxy_condition_eval_int($cart_total, $value, $operator);

}