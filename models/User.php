require_once __DIR__ . '/../core/Database.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
}
