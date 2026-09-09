import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

Alpine.plugin(persist);

// ── Cart Store ────────────────────────────────────────────────────────────────
Alpine.store('cart', {
    items:     Alpine.$persist([]).as('cart_items'),
    sessionId: Alpine.$persist('').as('cart_session'),
    open: false,

    // Cupón validado contra /api/coupons/validate. Se guarda tipo + valor en vez
    // del monto ya calculado, para que el descuento siga el subtotal cuando el
    // usuario cambia cantidades con el cupón aplicado.
    couponCode:     '',
    couponTipo:     null,   // 'porcentaje' | 'monto'
    couponRate:     0,      // fracción, si es porcentaje
    couponFlat:     0,      // CLP, si es monto fijo
    couponMessage:  '',
    couponSuccess:  false,

    FREE_SHIPPING: window.TIENDA?.despacho?.gratis_desde ?? 15000,
    SHIPPING_COST: window.TIENDA?.despacho?.estandar ?? 2500,

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
        if (!this.couponSuccess) return 0;
        if (this.couponTipo === 'porcentaje') return Math.round(this.subtotal * this.couponRate);
        return Math.min(this.couponFlat, this.subtotal);
    },

    // Alias usado por las vistas para saber si hay descuento activo.
    get couponDiscount() {
        return this.discountAmount;
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
                this.showToast(`"${productName}" agregado al carrito`);
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

    async applyCoupon() {
        const code = this.couponCode.trim().toUpperCase();
        if (!code) return;

        try {
            const res = await fetch('/api/coupons/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ codigo: code, subtotal: this.subtotal }),
            });
            const data = await res.json();

            if (data.valido) {
                this.couponSuccess = true;
                this.couponTipo    = data.tipo;
                this.couponRate    = data.tipo === 'porcentaje' && this.subtotal > 0
                    ? data.descuento / this.subtotal
                    : 0;
                this.couponFlat    = data.tipo === 'monto' ? data.descuento : 0;
                this.couponMessage = data.message;
            } else {
                this.resetCouponState();
                this.couponMessage = data.message || 'Cupón no válido.';
            }
        } catch (e) {
            this.resetCouponState();
            this.couponMessage = 'No pudimos validar el cupón. Revisa tu conexión.';
        }
    },

    resetCouponState() {
        this.couponSuccess = false;
        this.couponTipo    = null;
        this.couponRate    = 0;
        this.couponFlat    = 0;
    },

    removeCoupon() {
        this.couponCode    = '';
        this.couponMessage = '';
        this.resetCouponState();
    },

    orderViaWhatsApp() {
        if (this.items.length === 0) return;
        const lines = this.items
            .map(i => `• ${i.quantity}x ${i.name} ($${(i.price * i.quantity).toLocaleString('es-CL')})`)
            .join('\n');
        const msg = `*Nuevo Pedido - Aguas Santa Catalina*\n\nHola! Quiero pedir:\n\n${lines}\n\n*Total estimado:* $${this.total.toLocaleString('es-CL')}\n\n¡Gracias!`;
        window.open(`https://wa.me/${window.TIENDA.whatsapp}?text=${encodeURIComponent(msg)}`, '_blank');
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
