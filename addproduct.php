<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <link
            href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
            rel="stylesheet"
        />
        <title> .:: Add Product ::. </title>
    </head>
    <body>
        <header>
        <a href="dashboard.php" class="logo">Thoto & Nene Fresh Live Tilapia and Bangus</a>
        <nav class="navbar" id="navbar">
            <a href="dashboard.php">Home</a>
            <a href="addproduct.php">Add Product</a>
        </nav>
        </header>
        <div class="form-container" style="margin-top:120px;">
            <h2>Add Product</h2>
            <form action="upload_product.php" method="POST" enctype="multipart/form-data">
                <label>Product Name</label>
                <input type="text" name="name" required>

                <label>Description</label>
                <textarea name="description" rows="4"></textarea>

                <label>Price (₱)</label>
                <input type="number" step="0.01" name="price" required>

                <label>Number of Units</label>
                <input type="number" name="units" min="0" required>

                <label>Upload Image</label>
                <input type="file" name="image" accept="image/*" required>

                <button type="submit" name="submit">Add Product</button>
            </form>
        </div>

    </body>
</html>

