import ShoppingCart from "../ShoppingCart.js";

const sessionUrl = '/api/session/';
fetch(sessionUrl).then((response) => {
    if (!response.ok) {
        // No session
        ShoppingCart.clearCart();
        window.location.href = '/FrontEnd/Inicio.html'
    }
});