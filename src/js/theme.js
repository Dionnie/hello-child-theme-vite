function rearrangeCheckout() {
  // Move order review section
  const additionalFields = document.querySelector(
    ".woocommerce-additional-fields",
  );

  const couponFormToggle = document.querySelector(
    ".woocommerce-form-coupon-toggle",
  );

  const couponForm = document.querySelector(
    ".checkout_coupon.woocommerce-form-coupon",
  );

  const orderReviewHeading = document.querySelector("#order_review_heading");
  const orderReview = document.querySelector("#order_review");

  const orderTable = document.querySelector(
    ".shop_table.woocommerce-checkout-review-order-table",
  );

  if (additionalFields && orderReviewHeading && orderReview) {
    additionalFields.after(orderReviewHeading, orderReview);
  }

  if (couponFormToggle && couponForm) {
    //orderTable.after(couponFormToggle, couponForm);
  }
}

document.addEventListener("DOMContentLoaded", rearrangeCheckout);

// Re-run after checkout refresh (WooCommerce AJAX)
jQuery(document.body).on("updated_checkout", rearrangeCheckout);

jQuery(function ($) {
  $(document).on("click", ".qty-plus, .qty-minus", function () {
    var $qty = $(this).closest(".quantity").find(".qty");

    if (!$qty.length) {
      return;
    }

    var currentVal = parseFloat($qty.val()) || 0;
    var max = parseFloat($qty.attr("max"));
    var min = parseFloat($qty.attr("min"));
    var step = parseFloat($qty.attr("step")) || 1;

    if ($(this).hasClass("qty-plus")) {
      if (!isNaN(max) && currentVal >= max) {
        $qty.val(max);
      } else {
        $qty.val(currentVal + step);
      }
    } else {
      if (!isNaN(min) && currentVal <= min) {
        $qty.val(min);
      } else {
        $qty.val(Math.max(currentVal - step, min || 0));
      }
    }

    $qty.trigger("change");
  });
});
