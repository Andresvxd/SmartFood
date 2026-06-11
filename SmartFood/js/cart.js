const Cart = {
  items: [],

  load() {
    this.items = JSON.parse(sessionStorage.getItem('sf_cart') || '[]');
  },
  save() {
    sessionStorage.setItem('sf_cart', JSON.stringify(this.items));
    this.updateBadge();
  },

  add(producto) {
    const existing = this.items.find(i => i.id === producto.id);
    if (existing) {
      existing.cantidad++;
    } else {
      this.items.push({ ...producto, cantidad: 1 });
    }
    this.save();
    showToast('🛒 ' + producto.nombre + ' agregado al carrito');
  },

  remove(id) {
    this.items = this.items.filter(i => i.id !== id);
    this.save();
  },

  updateQty(id, delta) {
    const item = this.items.find(i => i.id === id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) this.remove(id);
    else this.save();
  },

  clear() {
    this.items = [];
    this.save();
  },

  total() {
    return this.items.reduce((sum, i) => sum + i.precio * i.cantidad, 0);
  },

  count() {
    return this.items.reduce((sum, i) => sum + i.cantidad, 0);
  },

  updateBadge() {
    const badge = document.getElementById('topCartBadge');
    if (badge) badge.textContent = this.count();
  },

  render(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;

    if (this.items.length === 0) {
      el.innerHTML = `
        <div class="text-center py-5" style="color:rgba(255,255,255,0.3);">
          <div style="font-size:3.5rem;">🛒</div>
          <p class="mt-2">Tu carrito está vacío</p>
          <small>Agrega productos desde el menú</small>
        </div>`;
      updateCartTotal(0);
      return;
    }

    el.innerHTML = this.items.map(item => `
      <div class="cart-item" id="cartItem_${item.id}">
        <div class="cart-item-emoji">${item.emoji || '🍽️'}</div>
        <div class="cart-item-info">
          <div class="cart-item-name">${item.nombre}</div>
          <div class="cart-item-price">${formatPrice(item.precio)} c/u</div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="cart-qty-btn" onclick="Cart.updateQty(${item.id},-1);Cart.render('${containerId}')">−</button>
          <span style="color:#fff;font-weight:600;min-width:20px;text-align:center;">${item.cantidad}</span>
          <button class="cart-qty-btn" onclick="Cart.updateQty(${item.id},1);Cart.render('${containerId}')">+</button>
        </div>
        <div style="color:#ff6b35;font-weight:700;font-size:0.9rem;min-width:80px;text-align:right;">
          ${formatPrice(item.precio * item.cantidad)}
        </div>
        <button onclick="Cart.remove(${item.id});Cart.render('${containerId}')"
          style="background:none;border:none;color:rgba(255,71,87,0.7);cursor:pointer;font-size:1rem;">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    `).join('');

    updateCartTotal(this.total());
  }
};

function updateCartTotal(total) {
  const el = document.getElementById('cartTotal');
  if (el) el.textContent = formatPrice(total);
}

function updateCartBadge() {
  Cart.load();
  Cart.updateBadge();
}


Cart.load();