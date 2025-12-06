<td>
    <?php if (!empty($product['image_url'])): ?>
        <?php $imagePath = '../' . $product['image_url']; // ../uploads/xxx ?>
        <img src="<?php echo $imagePath; ?>" class="product-img"
             onerror="this.onerror=null;this.src='../img/no-image.png';">
    <?php else: ?>
        <img src="../img/no-image.png" class="product-img">
    <?php endif; ?>
</td>
...
<td>
    <div class="action-btns">
        <?php if ($product['is_active']): ?>
            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-edit">Edit</a>
            <form method="POST" action="delete_product.php"
                  style="display:inline; margin:0;"
                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <button type="submit" class="btn-delete">Delete</button>
            </form>
        <?php else: ?>
            <form method="POST" action="restore_product.php"
                  style="display:inline; margin:0;">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <button type="submit" class="btn-restore">Restore</button>
            </form>
        <?php endif; ?>
    </div>
</td>
