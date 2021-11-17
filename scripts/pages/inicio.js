import ShoppingCart from "../ShoppingCart.js";

const sessionUrl = '/api/session/';
fetch(sessionUrl).then((response) => {
    if (response.ok) {
        window.location.href = '/FrontEnd/SesionIniciada.html'
    }
    else {
        // No session
        console.log('carrito limpio');
        ShoppingCart.clearCart();
    }
});