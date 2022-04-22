define(['jquery'], function ($) {
  return function (config) {
    window.omnisend = window.omnisend || [];
    omnisend.push([
      'track',
      '$productViewed',
      {
        $productID: config.productID,
        $variantID: config.variantID,
        $currency: config.currency,
        $tags: config.tags,
        $price: config.price,
        $oldPrice: config.oldPrice,
        $title: config.title,
        $description: config.description,
        $imageUrl: config.imageUrl,
        $productUrl: config.productUrl,
        $vendor: config.vendor
      }
    ]);
  }
});
