export default class ShoppingCart {
    static addProduct(product) {
        let cart = [];
        
        const currentCartJson = localStorage.getItem('cart');
        if (currentCartJson) {
            cart = JSON.parse(currentCartJson);
        }

        if (!cart.includes(Number(product))) {
            cart.push(Number(product));
        }

        localStorage.setItem('cart', JSON.stringify(cart));
    }

    static getProducts() {
        let cart = [];
        
        const currentCartJson = localStorage.getItem('cart');
        if (currentCartJson) {
            cart = JSON.parse(currentCartJson);
        }

        return cart;
    }
    
    static removeProduct(product) {
        const id = Number(product);
        const currentCartJson = localStorage.getItem('cart');
        if (currentCartJson) {
            const cart = JSON.parse(currentCartJson);

            const index = cart.indexOf(id);
            if (index >= 0) {
                cart.splice(index, 1);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
        }
    }

    static clearCart() {
        localStorage.removeItem('cart');
    }
}