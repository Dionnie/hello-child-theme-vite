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
