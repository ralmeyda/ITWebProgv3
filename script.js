document.addEventListener("DOMContentLoaded", () => {
  const cartIcon = document.querySelector("#cart-icon");
  const cart = document.querySelector(".cart");
  const cartClose = document.querySelector("#cart-close");
  const cartContent = document.querySelector(".cart-content");
  const notification = document.getElementById("cart-notification");
  const buyNowButton = document.querySelector(".btn-buy");
  let cartItemCount = 0;

  // Open/close cart
  cartIcon.addEventListener("click", () => cart.classList.add("active"));
  cartClose.addEventListener("click", () => cart.classList.remove("active"));

  // Add to cart
  document.querySelectorAll(".add-cart").forEach(button => {
    button.addEventListener("click", e => {
      if (!window.APP || !window.APP.isLoggedIn) {
        alert("Please log in before adding items to your cart.");
        window.location.href = "login.php";
        return;
      }

      const productBox = e.currentTarget.closest(".product-box");
      const productId = parseInt(productBox.dataset.productId, 10);
      const title = productBox.querySelector(".product-title").textContent;
      const price = parseFloat(productBox.querySelector(".price").textContent);
      const imgSrc = productBox.querySelector("img").src;

      // Prevent duplicates
      if ([...cartContent.querySelectorAll(".cart-box")].some(
        box => parseInt(box.dataset.productId, 10) === productId
      )) {
        alert("This item is already in your cart.");
        return;
      }

      const cartBox = document.createElement("div");
      cartBox.classList.add("cart-box");
      cartBox.dataset.productId = productId;
      cartBox.innerHTML = `
        <img src="${imgSrc}" class="cart-img">
        <div class="cart-detail">
          <h2 class="cart-product-title">${title}</h2>
          <span class="cart-price">${price}</span>
          <div class="cart-quantity">
            <button class="decrement">-</button>
            <span class="number">1</span>
            <button class="increment">+</button>
          </div>
        </div>
        <i class="ri-delete-bin-line cart-remove"></i>
      `;
      cartContent.appendChild(cartBox);

      const qtyEl = cartBox.querySelector(".number");
      cartBox.querySelector(".decrement").addEventListener("click", () => {
        let qty = parseInt(qtyEl.textContent, 10);
        if (qty > 1) qtyEl.textContent = --qty;
        updateTotal();
      });
      cartBox.querySelector(".increment").addEventListener("click", () => {
        let qty = parseInt(qtyEl.textContent, 10);
        qtyEl.textContent = ++qty;
        updateTotal();
      });

      cartBox.querySelector(".cart-remove").addEventListener("click", () => {
        cartBox.remove();
        updateCount(-1);
        updateTotal();
      });

      notification.classList.add("show");
      setTimeout(() => notification.classList.remove("show"), 1500);

      updateCount(1);
      updateTotal();
    });
  });

  function updateCount(change) {
    const badge = document.querySelector(".cart-item-count");
    cartItemCount += change;
    if (cartItemCount > 0) {
      badge.style.visibility = "visible";
      badge.textContent = cartItemCount;
    } else {
      badge.style.visibility = "hidden";
      badge.textContent = "";
    }
  }

  function updateTotal() {
    let total = 0;
    cartContent.querySelectorAll(".cart-box").forEach(box => {
      const price = parseFloat(box.querySelector(".cart-price").textContent);
      const qty = parseInt(box.querySelector(".number").textContent, 10);
      total += price * qty;
    });
    document.querySelector(".total-price").textContent = `PHP${total.toFixed(2)}`;
  }

  function buildCartData() {
    const items = [];
    cartContent.querySelectorAll(".cart-box").forEach(box => {
      items.push({
        product_id: parseInt(box.dataset.productId, 10),
        price: parseFloat(box.querySelector(".cart-price").textContent),
        quantity: parseInt(box.querySelector(".number").textContent, 10)
      });
    });
    return items;
  }

  async function sendPurchase(cartData) {
    try {
      const resp = await fetch('admin/purchase.php', {
        method: 'POST',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cart: cartData })
      });
      return await resp.json();
    } catch (err) {
      console.error(err);
      return { success: false, message: 'Network error.' };
    }
  }

  buyNowButton.addEventListener("click", async () => {
    const cartData = buildCartData();
    if (cartData.length === 0) {
      alert("Your cart is empty! Please add products.");
      window.location.href = 'index.php';
      return;
    }

    if (!window.APP || !window.APP.isLoggedIn) {
      alert('Please log in to complete your purchase.');
      window.location.href = 'login.php';
      return;
    }

    buyNowButton.disabled = true;
    buyNowButton.textContent = 'Processing...';

    const result = await sendPurchase(cartData);

    buyNowButton.disabled = false;
    buyNowButton.textContent = 'Buy Now';

    if (result && result.success) {
      alert(result.message || 'Order placed successfully');
      cartContent.innerHTML = '';
      cartItemCount = 0;
      updateCount(0);
      updateTotal();
    } else {
      alert(result.message || 'Failed to place order');
    }
  });
});
