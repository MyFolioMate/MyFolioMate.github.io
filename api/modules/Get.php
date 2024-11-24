<?php
class Get {
  private $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
  }

  public function getPortfolio($username, $userId = null) {
    if (!$username) {
      return array(
        "success" => false,
        "error" => "Username is required",
        "code" => 400
      );
    }

    try {
      // First check if user exists with both username and id if provided
      $sql = "SELECT id, username, full_name, email FROM users WHERE username = ?";
      $params = [$username];
      
      if ($userId) {
        $sql .= " AND id = ?";
        $params[] = $userId;
      }
      
      $userCheck = $this->pdo->prepare($sql);
      $userCheck->execute($params);
      $user = $userCheck->fetch(\PDO::FETCH_ASSOC);

      if (!$user) {
        return array(
          "success" => false,
          "error" => "User not found",
          "code" => 404
        );
      }

      // Get portfolio data
      $stmt = $this->pdo->prepare("SELECT * FROM portfolios WHERE user_id = ?");
      $stmt->execute([$user['id']]);
      $portfolio = $stmt->fetch(\PDO::FETCH_ASSOC);

      if (!$portfolio) {
        return array(
          "success" => false,
          "error" => "Portfolio not found",
          "code" => 404
        );
      }

      return array(
        "success" => true,
        "data" => array_merge($portfolio, ['user' => $user])
      );
    } catch (\Throwable $th) {
      return array(
        "success" => false,
        "error" => $th->getMessage(),
        "code" => 500
      );
    }
  }

  public function getProjects($username, $userId = null) {
    $sqlString = "SELECT p.id, p.title, p.description, p.project_url, p.image_url, p.created_at 
                  FROM projects p
                  JOIN users u ON u.id = p.user_id
                  WHERE u.username = ?";
    $params = [$username];
    
    if ($userId !== null) {
        $sqlString .= " AND p.user_id = ?";
        $params[] = $userId;
    }
    
    $sqlString .= " ORDER BY p.created_at DESC";
    
    try {
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute($params);
        $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array(
            "success" => true,
            "data" => $projects
        );
    } catch (\Throwable $th) {
        return array(
            "success" => false,
            "error" => "Unable to fetch projects: " . $th->getMessage(),
            "code" => 500
        );
    }
  }

  public function getAllPortfolios() {
    $sqlString = "SELECT u.username, u.full_name, p.title, p.about, p.theme_color, p.design_template,
                  (SELECT COUNT(*) FROM likes WHERE portfolio_id = p.id) as likes
                  FROM users u 
                  JOIN portfolios p ON u.id = p.user_id 
                  ORDER BY likes DESC, u.created_at DESC";
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute();
      $portfolios = $stmt->fetchAll();
      
      return array(
        "success" => true,
        "portfolios" => $portfolios
      );
    } catch (\Throwable $th) {
      return array(
        "error" => "Unable to fetch portfolios: " . $th->getMessage(),
        "code" => 500
      );
    }
  }

  public function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
      return array(
        "error" => "Not authenticated",
        "code" => 401
      );
    }

    $sqlString = "SELECT id, username, email, full_name FROM users WHERE id = ?";
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([$_SESSION['user_id']]);
      $user = $stmt->fetch();
      
      if (!$user) {
        return array(
          "error" => "User not found",
          "code" => 404
        );
      }
      
      return $user;
    } catch (\Throwable $th) {
      return array(
        "error" => $th->getMessage(),
        "code" => 500
      );
    }
  }
}