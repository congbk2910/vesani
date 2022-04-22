define ([], function () {
    return function(config) {
        window.soundest = window.soundest || [];

        soundest.push([ 'products','set', {
            productID: config.productID,
            variantID: config.variantID,
            currency: config.currency,
            price: config.price,
            title: config.title,
            imageUrl: config.imageUrl,
            productUrl: config.productUrl,
            oldPrice: config.oldPrice,
            description: config.description,
            vendor: config.vendor
        }]);
    };
});