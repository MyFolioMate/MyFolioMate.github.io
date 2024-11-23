<?php
class Post {
  private $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
  }
  
  public function createUser($param) {
    if (!$param || !isset($param->username) || !isset($param->password) || 
        !isset($param->email) || !isset($param->full_name)) {
      return array(
        "success" => false,
        "error" => "Missing required fields",
        "code" => 400
      );
    }

    $sqlString = "INSERT INTO users (username, password, email, full_name) VALUES (?,?,?,?)";
    $res = [];
    try {
      $auth = new Auth($this->pdo);
      $hashedPassword = $auth->encryptPassword($param->password);
      
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([
        $param->username,
        $hashedPassword,
        $param->email,
        $param->full_name
      ]);
      $res = ["success" => true, "message" => "User created successfully"];
    } catch (\Throwable $th) {
      $res = array(
        "success" => false,
        "error" => $th->getMessage(),
        "code" => $th->getCode()
      );
    }
    return $res;
  }

  public function updatePortfolio($param) {
    $sqlString = "UPDATE portfolios 
                  SET title = ?, 
                      about = ?, 
                      skills = ?, 
                      contact_info = ?, 
                      theme_color = ?, 
                      design_template = ?
                  WHERE user_id = ?";
    $res = [];
    try {
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([
            $param->title,
            $param->about,
            $param->skills,
            $param->contact_info,
            $param->theme_color,
            $param->design_template,
            $param->user_id
        ]);
        $res = ["success" => true, "message" => "Portfolio updated successfully"];
    } catch (\Throwable $th) {
        $res = array(
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        );
    }
    return $res;
  }

  public function addProject($param) {
    $sqlString = "INSERT INTO projects (user_id, title, description, image_url, project_url) 
                  VALUES (?,?,?,?,?)";
    $res = [];
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([
        $param->user_id,
        $param->title,
        $param->description,
        $param->image_url,
        $param->project_url
      ]);
      $res = ["success" => true, "message" => "Project added successfully"];
    } catch (\Throwable $th) {
      $res = array(
        "success" => false,
        "error" => $th->getMessage(),
        "code" => $th->getCode()
      );
    }
    return $res;
  }

  public function toggleLike($param) {
    if (!isset($param->user_id) || !isset($param->portfolio_id)) {
      return array(
        "success" => false,
        "error" => "Missing required fields",
        "code" => 400
      );
    }

    try {
      // Check if like exists
      $checkSql = "SELECT id FROM likes WHERE user_id = ? AND portfolio_id = ?";
      $checkStmt = $this->pdo->prepare($checkSql);
      $checkStmt->execute([$param->user_id, $param->portfolio_id]);
      $existing = $checkStmt->fetch();

      if ($existing) {
        // Unlike
        $sql = "DELETE FROM likes WHERE user_id = ? AND portfolio_id = ?";
      } else {
        // Like
        $sql = "INSERT INTO likes (user_id, portfolio_id) VALUES (?, ?)";
      }

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([$param->user_id, $param->portfolio_id]);

      return array(
        "success" => true,
        "action" => $existing ? "unliked" : "liked"
      );
    } catch (\Throwable $th) {
      return array(
        "success" => false,
        "error" => $th->getMessage(),
        "code" => 500
      );
    }
  }
}