import ShoppingCart from '../ShoppingCart.js';

async function getProductInfo(productId) {
    const baseUrl = 'http://localhost/api';
    const url = `${baseUrl}/products/${productId}`;

    const response = await fetch(url);
    return await response.json();
}

function createProductCard(product) {
    const element = document.createElement('div');
    element.classList.add('product-box', 'd-flex', 'flex-row', 'p-2', 'rounded', 'mb-2');

    const buttonAux = document.createElement('div');
    buttonAux.innerHTML = `<button data-product-id="${product.id}" data-product-price="${product.price}" class="delete-btn mr-2"><i class="fa fa-trash"></i></button>`;
    
    const button = buttonAux.firstChild;
    button.addEventListener('click', (e) => {
        const btn = e.target;
        ShoppingCart.removeProduct(Number(btn.dataset.productId));
        btn.parentElement.remove();

        // Update total products display
        let totalProducts = Number.parseInt(document.getElementById('totalProducts').textContent);
        totalProducts -= 1;
        document.getElementById('totalProducts').textContent = totalProducts;

        // Update total display
        let total = Number.parseFloat(document.getElementById('total').textContent);
        const productPrice = btn.dataset.productPrice;
        total -= productPrice;
        document.getElementById('total').textContent = total.toFixed(2);
    });

    element.append(button);

    const elementAux = document.createElement('div');
    elementAux.innerHTML = `
        <div class="d-inline-block product-box-image">
            <img src="${product.image}" alt="${product.name}">
        </div>
        <div class="ml-2">
            <h5>${product.name}</h5>
        </div>
        <div class="ml-auto text-right">
            <h5 class="product-price">$${Number.parseFloat(product.price).toFixed(2)} MXN</h5>
        </div>
    `;

    element.append(...elementAux.children);

    return element;
}

function loadProductsInfo(products) {
    const container = document.getElementById('products');

    const totalProducts = products.length;

    let total = 0;
    for (const product of products) {
        container.append(createProductCard(product));
        total += Number.parseFloat(product.price);
    }
    
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('total').textContent = total.toFixed(2);
}

async function getCurrentSession() {
    const url = 'http://localhost/api/session/';

    const response = await fetch(url);

    const responseData = await response.json();

    return responseData;
}

async function placeOrder(order) {
    const url = 'http://localhost/api/orders/';

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(order)
    });

    return response.ok;
}

async function placeOrderButton(e) {
    e.target.disabled = true; // Disable button

    const session = await getCurrentSession();

    if (!session) {
        alert('Necesita iniciar sesión antes de hacer la compra');
        return;
    }
    
    const paymentMethod = 1; // TEMP

    const order = {
        ordererId: session.id,
        paymentMethod: paymentMethod,
        products: ShoppingCart.getProducts()
    };

    const success = await placeOrder(order);
    
    if (!success) {
        alert('Lo sentimos, no se ha podido realizar el pedido');
        return;
    }
    
    ShoppingCart.clearCart();
    alert('Gracias por su compra');

    e.target.disabled = false;
}

document.addEventListener('DOMContentLoaded', () => {
    const cart = ShoppingCart.getProducts();
    
    Promise.all(cart.map((productId) => getProductInfo(productId))).then(loadProductsInfo);

    document.getElementById('btnPay').addEventListener('click', placeOrderButton);
});