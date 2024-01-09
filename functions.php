<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/* add pharmacywire search to header */
add_filter( 'get_search_form', 'search_to_avada_mobile_nav' );
if ( ! function_exists( 'search_to_avada_mobile_nav' ) ) {
    function search_to_avada_mobile_nav() {
        return do_shortcode( '[PharmacyWire searchform="pharmacy"]' );
    }
}

// DISABLED - NOV 7, DUE TO ERRORS - BRIAN  ******************************************************
if (0) {
/* affiliateWP conversion tracking */
add_action('wp_footer', 'track_conversion');

function track_conversion()
{

?>

	<script src="<?php echo get_stylesheet_directory_uri() . '/md5.js'; ?>"></script>
	<script type="text/javascript">
		jQuery(function() {
			const $ = jQuery;
			const affwp_ref = "<?php echo $_COOKIE["affwp_ref"]; ?>";
			const affwp_ref_visit_id = "<?php echo $_COOKIE["affwp_ref_visit_id"]; ?>";

			// not required 
			// const affiliate_rate = "<?php echo affwp_get_affiliate_rate($_COOKIE["affwp_ref"]) ?>";

			if (!affwp_ref || !affwp_ref_visit_id) return;

			function trackOrderAffiliate(orderResponse) {
				if (orderResponse.status !== "success") {
					return;
				}
				const orderReference = orderResponse.order_id;
				const orderTotalsWithoutShippingFees = orderResponse.cart.order_total - orderResponse.cart.shippingfee;
				const orderItems = [];
				for (item of orderResponse.cart.items) {
					orderItems.push(item.package_name);
				}
				orderDescription = orderItems.join(", ") + " Subtotal: " + orderTotalsWithoutShippingFees;
				const ajaxObject = {
					type: "POST",
					data: {
						action: 'affwp_track_conversion',
						affiliate: affwp_ref,
						amount: orderTotalsWithoutShippingFees,
						status: 'pending',
						description: orderDescription,
						context: '',
						reference: orderReference,
						campaign: '',
						type: 'sale',
						md5: md5(orderTotalsWithoutShippingFees + orderDescription + orderReference + 'pending'),
					},
					url: affwp_scripts.ajaxurl,
					success: function(response) {
						if (window.console && window.console.log) {
							console.log(response);
						}
					}

				}

				$.ajax(ajaxObject);
			}

			$('.pw-pharmacy-wrap .checkout_form')
				.on('pwire:cart:orderSubmitted',
					function(event, orderResponse) {
						trackOrderAffiliate(orderResponse);
					});

		});
	</script>
<?php
}
// END OF DISABLED ******************************************************

}


function wpb_widgets_init() {
 
    register_sidebar( array(
        'name'          => 'Custom Header Widget Area',
        'id'            => 'custom-header-widget',
        'before_widget' => '<div class="chw-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="chw-title">',
        'after_title'   => '</h2>',
    ) );
 
}

function add_faq_schema_to_toggles() {
    // if (is_page() && have_posts()) {

		while (have_posts()) {
            the_post();
	            if (strpos(apply_filters('the_content', get_the_content()), 'fusion-toggle') !== false) {
                $faq_data = array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array());
                $dom = new DOMDocument();
                @$dom->loadHTML(mb_convert_encoding(apply_filters('the_content', get_the_content()), 'HTML-ENTITIES', 'UTF-8'));
                $xpath = new DOMXPath($dom);
                $toggles = $xpath->query("//div[contains(@class, 'toggle')]"); 
                foreach ($toggles as $toggle) {
                    $question = @ $xpath->query(".//h4", $toggle)->item(0)->nodeValue; 
                    $answer = @ $xpath->query(".//div[contains(@class, 'toggle-content')]", $toggle)->item(0)->nodeValue; 
				        $faq_data['mainEntity'][] = array(	
                        '@type' => 'Question',
                        'name' => $question,
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => $answer
                        )
                    );
                }
              
                echo '<script type="application/ld+json">' . json_encode($faq_data) . '</script>';
            }
        }
    }

add_action('wp_head', 'add_faq_schema_to_toggles');

add_action( 'widgets_init', 'wpb_widgets_init' );

/************ Applying custom JS Pharmacy Wire file ***********/
function pharmacy_scripts() {
		wp_enqueue_script( 'pharmacy', get_stylesheet_directory_uri() . '/js/pharmacy.js', array( 'jquery' ), '3.1.7'.time());
}
add_action( 'wp_enqueue_scripts', 'pharmacy_scripts' ); 


