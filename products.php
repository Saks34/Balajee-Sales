<?php
include 'db.php'; // Include database connection

// Get category, brand, and price from the query string; default to 'All'
$category = $_GET['category'] ?? 'All';
$brands = $_GET['brands'] ?? [];
$price_range = $_GET['price_range'] ?? [];

// Build the SQL query with filters
$sql = "SELECT image_path AS image, product_name AS name, price, product_id FROM products WHERE 1=1";

if ($category != 'All') {
    $sql .= " AND category='" . $conn->real_escape_string($category) . "'";
}

// Filter by brand (multiple selected)
if (!empty($brands)) {
    $brands = array_map(function ($brand) use ($conn) {
        return $conn->real_escape_string($brand);
    }, $brands);
    $sql .= " AND brand IN ('" . implode("','", $brands) . "')";
}

// Filter by price range
if (!empty($price_range)) {
    $price_conditions = [];
    foreach ($price_range as $range) {
        switch ($range) {
            case '0-500':
                $price_conditions[] = "price BETWEEN 0 AND 500";
                break;
            case '500-1000':
                $price_conditions[] = "price BETWEEN 500 AND 1000";
                break;
            case '1000-1500':
                $price_conditions[] = "price BETWEEN 1000 AND 1500";
                break;
            case '1500-2000':
                $price_conditions[] = "price BETWEEN 1500 AND 2000";
                break;
            case '2000-2500':
                $price_conditions[] = "price BETWEEN 2000 AND 2500";
                break;
            case '2500+':
                $price_conditions[] = "price >= 2500";
                break;
        }
    }
    if (!empty($price_conditions)) {
        $sql .= " AND (" . implode(" OR ", $price_conditions) . ")";
    }
}

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Error fetching products: " . $conn->error);
}

// Get all distinct brands for filter
$brand_sql = "SELECT DISTINCT brand FROM products";
$brand_result = $conn->query($brand_sql);

// Check if query was successful
if (!$brand_result) {
    die("Error fetching brands: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        main {
            padding: 2rem;
            display: flex;
        }

        .filters {
            flex: 1;
            background-color: #fff;
            padding: 1rem;
            border-radius: 5px;
            margin-right: 1rem;
        }

        .products {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product {
            background: white;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product h3 {
            font-size: 1.2rem;
            margin: 1rem 0;
        }

        .product p {
            font-size: 1rem;
            color: #555;
        }

        .product .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .product .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <!-- Filters Section -->
        <div class="filters">
            <h4>Filters</h4>
            <form method="GET">
             

                <div class="mb-3">
                    <label for="brands" class="form-label">Brand</label>
                    <div>
                        <?php while ($brand = $brand_result->fetch_assoc()): ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="brands[]" value="<?php echo $brand['brand']; ?>" <?php echo in_array($brand['brand'], $brands) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><?php echo htmlspecialchars($brand['brand']); ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="price_range" class="form-label">Price Range</label>
                    <div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="0-500" <?php echo in_array('0-500', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹0 - ₹500</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="500-1000" <?php echo in_array('500-1000', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹500 - ₹1000</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="1000-1500" <?php echo in_array('1000-1500', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹1000 - ₹1500</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="1500-2000" <?php echo in_array('1500-2000', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹1500 - ₹2000</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="2000-2500" <?php echo in_array('2000-2500', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹2000 - ₹2500</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="price_range[]" value="2500+" <?php echo in_array('2500+', $price_range) ? 'checked' : ''; ?>>
                            <label class="form-check-label">₹2500 and above</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
        </div>

        <!-- Products Section -->
        <div class="products">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>₹<?php echo number_format($product['price']); ?></p>
                    <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-success">View Details</a>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

</body>
</html>
