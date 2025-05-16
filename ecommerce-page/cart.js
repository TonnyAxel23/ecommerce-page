class Cart {
  constructor() {
    this.cart = JSON.parse(localStorage.getItem("cart")) || [];
    this.init();
  }

  init() {
    this.renderCart();
    this.updateCartCount();
  }

  addToCart(product) {
    const existing = this.cart.find(item => item.id === product.id);
    
    if (existing) {
      existing.qty++;
    } else {
      this.cart.push({ 
        id: product.id, 
        name: product.name, 
        price: product.price, 
        qty: 1,
        image: product.image 
      });
    }
    
    this.save();
    this.renderCart();
    this.showAddToCartFeedback(product.name);
  }

  removeFromCart(index) {
    this.cart.splice(index, 1);
    this.save();
    this.renderCart();
  }

  updateQuantity(index, change) {
    const newQty = this.cart[index].qty + change;
    
    if (newQty < 1) {
      this.removeFromCart(index);
    } else {
      this.cart[index].qty = newQty;
      this.save();
      this.renderCart();
    }
  }

  save() {
    localStorage.setItem("cart", JSON.stringify(this.cart));
    this.updateCartCount();
  }

  updateCartCount() {
    const count = this.cart.reduce((sum, item) => sum + item.qty, 0);
    document.querySelectorAll('.cart-count').forEach(el => {
      el.textContent = count;
      el.style.display = count > 0 ? 'inline-block' : 'none';
    });
  }

  showAddToCartFeedback(productName) {
    const feedback = document.createElement('div');
    feedback.className = 'add-to-cart-feedback';
    feedback.innerHTML = `
      <div class="feedback-content">
        <i class="bi bi-check-circle-fill"></i>
        ${productName} added to cart!
      </div>
    `;
    
    document.body.appendChild(feedback);
    
    setTimeout(() => {
      feedback.classList.add('show');
    }, 10);
    
    setTimeout(() => {
      feedback.classList.remove('show');
      setTimeout(() => feedback.remove(), 300);
    }, 2000);
  }

  renderCart() {
    const cartList = document.getElementById("cart");
    const cartSummary = document.getElementById("cartSummary");
    
    if (cartList) {
      cartList.innerHTML = '';
      this.cart.forEach((item, index) => {
        const li = document.createElement("li");
        li.className = 'cart-item';
        li.innerHTML = `
          <div class="cart-item-image">
            <img src="${item.image || 'assets/images/placeholder.jpg'}" alt="${item.name}">
          </div>
          <div class="cart-item-details">
            <h5>${item.name}</h5>
            <p>$${item.price.toFixed(2)}</p>
            <div class="cart-item-actions">
              <button class="qty-btn" onclick="cart.updateQuantity(${index}, -1)">-</button>
              <span>${item.qty}</span>
              <button class="qty-btn" onclick="cart.updateQuantity(${index}, 1)">+</button>
              <button class="remove-btn" onclick="cart.removeFromCart(${index})">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        `;
        cartList.appendChild(li);
      });
      
      if (this.cart.length === 0) {
        cartList.innerHTML = '<li class="empty-cart">Your cart is empty</li>';
      }
    }
    
    if (cartSummary) {
      let total = 0;
      cartSummary.innerHTML = '';
      
      this.cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        
        const div = document.createElement('div');
        div.className = 'cart-summary-item';
        div.innerHTML = `
          <span>${item.name} × ${item.qty}</span>
          <span>$${itemTotal.toFixed(2)}</span>
        `;
        cartSummary.appendChild(div);
      });
      
      const totalEl = document.getElementById("cartTotal");
      if (totalEl) {
        totalEl.textContent = `$${total.toFixed(2)}`;
      }
    }
  }
}

const cart = new Cart();

// Global access for HTML onclick attributes
window.cart = cart;

// Add to cart buttons event delegation
document.addEventListener('click', function(e) {
  if (e.target.matches('.add-to-cart') || e.target.closest('.add-to-cart')) {
    const button = e.target.matches('.add-to-cart') ? e.target : e.target.closest('.add-to-cart');
    const product = {
      id: button.dataset.id,
      name: button.dataset.name,
      price: parseFloat(button.dataset.price),
      image: button.dataset.image
    };
    cart.addToCart(product);
  }
});