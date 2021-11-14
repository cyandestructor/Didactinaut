export default class ShoppingCart {
    static addProduct(product) {
        let cart = [];
        
        const currentCartJson = localStorage.getItem('cart');
        if (currentCartJson) {
            cart = JSON.parse(currentCartJson);
        }

        if (!cart.includes(product)) {
            cart.push(product);
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
        const currentCartJson = localStorage.getItem('cart');
        if (currentCartJson) {
            const cart = JSON.parse(currentCartJson);

            console.log(cart);
            const index = cart.indexOf(product);
            if (index >= 0) {
                cart.splice(index, 1);
            }
            console.log(cart);

            localStorage.setItem('cart', JSON.stringify(cart));
        }
    }
}