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
  $(".quantity input.qty").each(function () {
    if ($(this).data("qty-buttons")) return;
    $(this).data("qty-buttons", true);

    $(this).before('<button type="button">-</button>');
    $(this).after('<button type="button">+</button>');
  });

  $(document).on("click", ".quantity button", function () {
    const $input = $(this).siblings("input.qty");
    const current = parseFloat($input.val()) || 0;
    const step = parseFloat($input.attr("step")) || 1;
    const min = parseFloat($input.attr("min"));
    const max = parseFloat($input.attr("max"));

    let value = current;

    if ($(this).text() === "+") {
      value += step;
      if (!isNaN(max)) value = Math.min(value, max);
    } else {
      value -= step;
      if (!isNaN(min)) value = Math.max(value, min);
    }

    $input.val(value).trigger("change");
  });
});
