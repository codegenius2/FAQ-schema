function add_faq_schema_to_toggles() {
    // Check if we are on a page that contains toggles for FAQs
    if (is_page() && have_posts()) {
        while (have_posts()) {
            the_post();
            if (strpos(get_the_content(), 'fusion-toggle') !== false) {
                // Initialize an array to hold our FAQ data
                $faq_data = array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array());

                // Extract questions and answers from the toggles
                // Note: Adjust the DOMDocument loading and parsing as per your page's HTML structure
                $dom = new DOMDocument();
                @$dom->loadHTML(mb_convert_encoding(get_the_content(), 'HTML-ENTITIES', 'UTF-8'));
                $xpath = new DOMXPath($dom);
                $toggles = $xpath->query("//div[contains(@class, 'fusion-toggle')]"); // Adjust this XPath query as needed

                foreach ($toggles as $toggle) {
                    $question = $xpath->query(".//h4", $toggle)->item(0)->nodeValue; // Adjust this as per your toggle's structure
                    $answer = $xpath->query(".//div[contains(@class, 'toggle-content')]", $toggle)->item(0)->nodeValue; // Adjust this as well

                    $faq_data['mainEntity'][] = array(
                        '@type' => 'Question',
                        'name' => $question,
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => $answer
                        )
                    );
                }

                // Output the JSON-LD script
                echo '<script type="application/ld+json">' . json_encode($faq_data) . '</script>';
            }
        }
    }
}

add_action('wp_head', 'add_faq_schema_to_toggles');
