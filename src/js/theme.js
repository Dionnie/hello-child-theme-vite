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

document.addEventListener("DOMContentLoaded", () => {
  // Add +/- buttons to quantity inputs
  document.querySelectorAll(".quantity input.qty").forEach((input) => {
    if (input.dataset.qtyButtons) return;

    input.dataset.qtyButtons = "true";

    const minusBtn = document.createElement("button");
    minusBtn.type = "button";
    minusBtn.className = "qty-minus";
    minusBtn.textContent = "-";

    const plusBtn = document.createElement("button");
    plusBtn.type = "button";
    plusBtn.className = "qty-plus";
    plusBtn.textContent = "+";

    input.parentNode.insertBefore(minusBtn, input);
    input.parentNode.insertBefore(plusBtn, input.nextSibling);
  });

  // Handle button clicks
  document.addEventListener("click", (e) => {
    const button = e.target;

    if (
      !button.classList.contains("qty-plus") &&
      !button.classList.contains("qty-minus")
    ) {
      return;
    }

    const quantity = button.closest(".quantity");
    const input = quantity?.querySelector("input.qty");

    if (!input) return;

    const current = parseFloat(input.value) || 0;
    const step = parseFloat(input.getAttribute("step")) || 1;
    const min = parseFloat(input.getAttribute("min"));
    const max = parseFloat(input.getAttribute("max"));

    let value = current;

    if (button.classList.contains("qty-plus")) {
      value += step;
      if (!isNaN(max)) {
        value = Math.min(value, max);
      }
    } else {
      value -= step;
      if (!isNaN(min)) {
        value = Math.max(value, min);
      }
    }

    input.value = value;

    // Trigger change event
    input.dispatchEvent(
      new Event("change", {
        bubbles: true,
      }),
    );
  });
});
