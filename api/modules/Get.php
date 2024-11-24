<?php
class Get {
  private $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
  }

  public function getPortfolio($username) {
    if (!$username) {
      return array(
        "success" => false,
        "error" => "Username is required",
        "code" => 400
      );
    }

    try {
      // First check if user exists
      $userCheck = $this->pdo->prepare("SELECT id, username, full_name, email FROM users WHERE username = ?");
      $userCheck->execute([$username]);
      $user = $userCheck->fetch(\PDO::FETCH_ASSOC);

      if (!$user) {
        return array(
          "success" => false,
          "error" => "User not found",
          "code" => 404
        );
      }

      // Then get portfolio data
      $sqlString = "SELECT p.id as portfolio_id, p.title, p.about, p.skills, 
                          p.contact_info, p.theme_color, p.design_template,
                          p.education, p.achievements, p.social_links
                   FROM portfolios p 
                   WHERE p.user_id = ?";
      
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([$user['id']]);
      $portfolio = $stmt->fetch(\PDO::FETCH_ASSOC);
      
      // Initialize portfolio data with user info and default values
      $portfolioData = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'title' => $portfolio ? ($portfolio['title'] ?? 'My Portfolio') : 'My Portfolio',
        'about' => $portfolio ? ($portfolio['about'] ?? 'Welcome to my portfolio') : 'Welcome to my portfolio',
        'skills' => $portfolio && $portfolio['skills'] ? array_map('trim', explode(',', $portfolio['skills'])) : [],
        'contact_info' => $portfolio ? ($portfolio['contact_info'] ?? '') : '',
        'theme_color' => $portfolio ? ($portfolio['theme_color'] ?? '#000000') : '#000000',
        'design_template' => $portfolio ? ($portfolio['design_template'] ?? 'classic') : 'classic',
        'education' => $portfolio ? ($portfolio['education'] ?? '') : '',
        'achievements' => $portfolio ? ($portfolio['achievements'] ?? '') : '',
        'social_links' => $portfolio ? ($portfolio['social_links'] ?? '') : ''
      ];
      
      return array(
        "success" => true,
        "data" => $portfolioData
      );
    } catch (\Throwable $th) {
      return array(
        "success" => false,
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