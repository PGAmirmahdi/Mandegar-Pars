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
        } catch(\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function products()
    {
        try {
            // set the PDO error mode to exception
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
        $product_id = $request->product_id;
        $price = $request->price;

        try {
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE mand_wc_product_meta_lookup SET min_price = $price, max_price = $price WHERE product_id = $product_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $sql2 = "UPDATE mand_postmeta SET meta_value = $price WHERE post_id = $product_id and meta_key = '_regular_price'";
            $sql3 = "UPDATE mand_postmeta SET meta_value = $price WHERE post_id = $product_id and meta_key = '_price'";

            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute();

            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->execute();

            $this->conn = null;

            return back();
        } catch(\PDOException $e) {
            return "Connection failed: " . $e->getMessage();
        }
    }
}
