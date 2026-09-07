/*!
script for WooCommerce add to cart with quantity, via AJAX
Author: support@webaware.com.au
Author URI: http://snippets.webaware.com.au/
License: GPLv2 or later
Version: 1.0.1
*/

// @link http://snippets.webaware.com.au/snippets/woocommerce-add-to-cart-with-quantity-and-ajax/
// @link https://gist.github.com/mikejolley/2793710/

// add this file to folder "js" inside theme

jQuery(function ($) {

    /* when product quantity changes, update quantity attribute on add-to-cart button */
    $("form.cart").on("change", "input.qty", function() {
        if (this.value === "0")
            this.value = "1";

        $(this.form).find("button[data-quantity]").data("quantity", this.value);
    });

    /* remove old "view cart" text, only need latest one thanks! */
    $(document.body).on("adding_to_cart", function() {
        $("a.added_to_cart").remove();
    });

});
