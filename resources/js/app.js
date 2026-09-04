import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

Alpine.plugin(persist);

// ── Cart Store ────────────────────────────────────────────────────────────────
Alpine.store('cart', {
    items:     Alpine.$persist([]).as('cart_items'),
    sessionId: Alpine.$persist('').as('cart_session'),
    open: false,

    // Coupon state (UI-only; real validation happens at checkout)
    couponCode:     '',
    couponDiscount: 0,   // 0.10 = 10 %
    couponMessage:  '',
    couponSuccess:  false,

    FREE_SHIPPING: 15000,
    SHIPPING_COST: 2500,

    init() {
        if (!this.sessionId) {
            this.sessionId = crypto.randomUUID();
        }
    },

    get count() {
        return this.items.reduce((sum, i) => sum + i.quantity, 0);
    },

    get subtotal() {
        return this.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
    },

    get shippingCost() {
        if (this.items.length === 0) return 0;
        return this.subtotal >= this.FREE_SHIPPING ? 0 : this.SHIPPING_COST;
    },

    get discountAmount() {
        return Math.round(this.subtotal * this.couponDiscount);
    },

    get total() {
        return Math.max(0, this.subtotal - this.discountAmount + this.shippingCost);
    },

    get shippingProgress() {
        return Math.min(100, Math.round((this.subtotal / this.FREE_SHIPPING) * 100));
    },

    get missingForFreeShipping() {
        return Math.max(0, this.FREE_SHIPPING - this.subtotal);
    },

    async add(productId, productName, price, quantity = 1) {
        try {
            const res = await fetch('/api/cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ session_id: this.sessionId, product_id: productId, cantidad: quantity }),
            });
            if (res.ok) {
                const existing = this.items.find(i => i.id === productId);
                if (existing) {
                    existing.quantity += quantity;
                } else {
                    this.items.push({ id: productId, name: productName, price, quantity });
                }
                this.open = true;
                this.showToast(`✓ "${productName}" agregado al carrito`);
            }
        } catch (e) {
            console.error('Cart error:', e);
        }
    },

    updateQty(productId, delta) {
        const item = this.items.find(i => i.id === productId);
        if (!item) return;
        const newQty = item.quantity + delta;
        if (newQty <= 0) {
            this.remove(productId);
        } else {
            item.quantity = newQty;
        }
    },

    async remove(productId) {
        await fetch(`/api/cart/${this.sessionId}/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                'Accept': 'application/json',
            },
        });
        this.items = this.items.filter(i => i.id !== productId);
    },

    applyCoupon() {
        const CODES = {
            'PURASALUD10':   0.10,
            'BIENVENIDO':    0.10,
            'SANTACATALINA': 0.10,
            'AGUA15':        0.15,
        };
        const code = this.couponCode.trim().toUpperCase();
        if (CODES[code] !== undefined) {
            this.couponDiscount = CODES[code];
            this.couponSuccess  = true;
            this.couponMessage  = `¡Cupón aplicado! ${CODES[code] * 100}% de descuento`;
        } else {
            this.couponDiscount = 0;
            this.couponSuccess  = false;
            this.couponMessage  = 'Cupón no válido. Prueba con PURASALUD10';
        }
    },

    removeCoupon() {
        this.couponCode     = '';
        this.couponDiscount = 0;
        this.couponMessage  = '';
        this.couponSuccess  = false;
    },

    orderViaWhatsApp() {
        if (this.items.length === 0) return;
        const lines = this.items
            .map(i => `• ${i.quantity}x ${i.name} ($${(i.price * i.quantity).toLocaleString('es-CL')})`)
            .join('\n');
        const msg = `💧 *Nuevo Pedido - Aguas Santa Catalina*\n\nHola! Quiero pedir:\n\n${lines}\n\n*Total estimado:* $${this.total.toLocaleString('es-CL')}\n\n¡Gracias!`;
        window.open(`https://wa.me/56991493272?text=${encodeURIComponent(msg)}`, '_blank');
    },

    async sync() {
        if (!this.sessionId) return;
        const res = await fetch(`/api/cart/${this.sessionId}`, { headers: { Accept: 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            this.items = data.items.map(i => ({
                id:       i.product_id,
                name:     i.product.nombre,
                price:    parseFloat(i.product.precio),
                quantity: i.cantidad,
                image:    i.product.imagen,
            }));
        }
    },

    clear() {
        this.items = [];
        this.removeCoupon();
    },

    showToast(msg) {
        const t = document.createElement('div');
        t.textContent = msg;
        t.className = 'fixed top-4 right-4 z-[9999] bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium';
        t.style.transition = 'opacity 0.4s';
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }, 2500);
    },
});

window.Alpine = Alpine;
Alpine.start();
