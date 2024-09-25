<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDO;

class ArtinController extends Controller
{
    private $conn;
    public function __construct()
    {
        $servername = "artintoner.com";
        $username = "h241538_mpsystem";
        $password = "iR_gWqcU+)V4eK]";
        $dbname = "h241538_artin";

        try {
            $this->conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function products()
    {
        $this->authorize('artin-products-list');

        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT  mand_posts.id, mand_posts.post_date, mand_posts.post_title, mand_posts.post_status, mand_wc_product_meta_lookup.sku, mand_wc_product_meta_lookup.min_price
                    FROM mand_posts
                    INNER JOIN mand_wc_product_meta_lookup
                        ON mand_posts.id = mand_wc_product_meta_lookup.product_id
                    WHERE mand_posts.post_type = 'product';";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $products = $stmt->fetchAll(PDO::FETCH_OBJ);
            $this->conn = null;

            return view('panel.artin.products', compact('products'));
        } catch(\PDOException $e) {
            return "Connection failed: " . $e->getMessage();
        }
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('artin-products-edit');

        $product_id = $request->product_id;
        $price = $request->price;

        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE mand_wc_product_meta_lookup SET min_price = :price, max_price = :price WHERE product_id = :product_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();

            $sql2 = "UPDATE mand_postmeta SET meta_value = :price WHERE post_id = :product_id and meta_key = '_regular_price'";
            $sql3 = "UPDATE mand_postmeta SET meta_value = :price WHERE post_id = :product_id and meta_key = '_price'";

            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':price', $price);
            $stmt2->bindParam(':product_id', $product_id);
            $stmt2->execute();

            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(':price', $price);
            $stmt3->bindParam(':product_id', $product_id);
            $stmt3->execute();

            $this->conn = null;

            return back();
        } catch(\PDOException $e) {
            return "Connection failed: " . $e->getMessage();
        }
    }
    public function store(Request $request)
    {
        $this->authorize('artin-products-create');

        // Validate incoming data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'price' => 'required|numeric',
            'status' => 'required|in:publish,draft',
        ]);

        // Extract validated data
        $title = $validatedData['title'];
        $sku = $validatedData['sku'];
        $price = $validatedData['price'];
        $status = $validatedData['status'];

        try {
            // Establish PDO connection
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insert into mand_posts table
            $sql = "INSERT INTO mand_posts (post_title, post_status, post_type, post_date) VALUES (:title, :status, 'product', NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            $product_id = $this->conn->lastInsertId();

            // Insert into mand_wc_product_meta_lookup table
            $sql2 = "INSERT INTO mand_wc_product_meta_lookup (product_id, sku, min_price, max_price) VALUES (:product_id, :sku, :price, :price)";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':product_id', $product_id);
            $stmt2->bindParam(':sku', $sku);
            $stmt2->bindParam(':price', $price);
            $stmt2->execute();

            // Insert into mand_postmeta table for _regular_price
            $sql3 = "INSERT INTO mand_postmeta (post_id, meta_key, meta_value) VALUES (:product_id, '_regular_price', :price)";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(':product_id', $product_id);
            $stmt3->bindParam(':price', $price);
            $stmt3->execute();

            // Insert into mand_postmeta table for _price
            $sql4 = "INSERT INTO mand_postmeta (post_id, meta_key, meta_value) VALUES (:product_id, '_price', :price)";
            $stmt4 = $this->conn->prepare($sql4);
            $stmt4->bindParam(':product_id', $product_id);
            $stmt4->bindParam(':price', $price);
            $stmt4->execute();

            // Close PDO connection
            $this->conn = null;

            // Redirect back with success message
            return redirect()->back()->with('success', 'Product created successfully.');
        } catch(\PDOException $e) {
            // Handle PDO exceptions
            return "Connection failed: " . $e->getMessage();
        }
    }
    public function destroy($id)
    {
        $this->authorize('artin-products-delete');

        try {
            // Begin PDO transaction
            $this->conn->beginTransaction();

            // Ensure PDO connection is established
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Delete from mand_postmeta table
            $sql1 = "DELETE FROM mand_postmeta WHERE post_id = :product_id";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bindParam(':product_id', $id);
            $stmt1->execute();

            // Delete from mand_wc_product_meta_lookup table
            $sql2 = "DELETE FROM mand_wc_product_meta_lookup WHERE product_id = :product_id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':product_id', $id);
            $stmt2->execute();

            // Delete from mand_posts table
            $sql3 = "DELETE FROM mand_posts WHERE ID = :product_id";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(':product_id', $id);
            $stmt3->execute();

            // Commit transaction
            $this->conn->commit();

            // Close PDO connection
            $this->conn = null;

            // Return success message or redirect back
            return response()->json(['success' => 'Product deleted successfully.']);

        } catch (\PDOException $e) {
            // Rollback transaction on error
            $this->conn->rollBack();

            // Handle PDO exceptions
            return response()->json(['error' => 'Connection failed: ' . $e->getMessage()], 500);
        }
    }

}
