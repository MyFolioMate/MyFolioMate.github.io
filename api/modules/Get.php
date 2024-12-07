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

      // Fetch skills
      $skillsStmt = $this->pdo->prepare("SELECT skill as name, description FROM skills WHERE user_id = ?");
      $skillsStmt->execute([$user['id']]);
      $skills = $skillsStmt->fetchAll(\PDO::FETCH_ASSOC);

      // Merge skills into portfolio data
      $portfolio['skills'] = $skills;

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

  public function getProjects($username, $userId) {
    try {
        $stmt = $this->pdo->prepare("
            SELECT id, title, description, project_url
            FROM projects 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            "success" => true,
            "data" => $projects
        ];
    } catch (\Throwable $th) {
        error_log("Project Fetch Error: " . $th->getMessage());
        return [
            "success" => false,
            "error" => $th->getMessage(),
            "code" => 500
        ];
    }
  }

  public function getUserIdByUsername($username) {
    $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $user ? $user['id'] : null;
  }

  public function getAllPortfolios() {
    $sqlString = "SELECT 
                    u.username, 
                    u.full_name, 
                    p.title, 
                    p.about, 
                    p.theme_color, 
                    p.design_template,
                    p.user_id
                  FROM users u 
                  JOIN portfolios p ON u.id = p.user_id 
                  ORDER BY u.created_at DESC";
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute();
      $portfolios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      
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