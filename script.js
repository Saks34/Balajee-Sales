// Add to Cart
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.href.split('=')[1];  // Get product ID from the URL
        alert('Product ID ' + productId + ' added to the cart!');
    });
});
