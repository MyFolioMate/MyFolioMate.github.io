<?php
class Get {
  private $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
  }

  public function getPortfolio($username) {
    if (!$username) {
      return array(
        "error" => "Username is required",
        "code" => 400
      );
    }

    $sqlString = "SELECT u.id as user_id, u.username, u.full_name, u.email, 
                         p.title, p.about, p.skills, p.contact_info, p.theme_color, p.design_template
                  FROM users u 
                  LEFT JOIN portfolios p ON u.id = p.user_id 
                  WHERE u.username = ?";
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([$username]);
      $result = $stmt->fetch();
      
      if (!$result) {
        return array(
          "error" => "User not found",
          "code" => 404
        );
      }
      
      return $result;
    } catch (\Throwable $th) {
      return array(
        "error" => "Unable to fetch portfolio: " . $th->getMessage(),
        "code" => 500
      );
    }
  }

  public function getProjects($username) {
    $sqlString = "SELECT p.* 
                  FROM projects p
                  JOIN users u ON u.id = p.user_id
                  WHERE u.username = ? 
                  ORDER BY p.created_at DESC";
    try {
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([$username]);
        return $stmt->fetchAll();
    } catch (\Throwable $th) {
        return array(
            "error" => "Unable to fetch projects: " . $th->getMessage(),
            "code" => $th->getCode()
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