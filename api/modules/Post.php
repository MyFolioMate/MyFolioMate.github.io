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

    $this->pdo->beginTransaction();
    try {
      $auth = new Auth($this->pdo);
      $hashedPassword = $auth->encryptPassword($param->password);
      
      $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, full_name) VALUES (?,?,?,?)");
      $stmt->execute([
        $param->username,
        $hashedPassword,
        $param->email,
        $param->full_name
      ]);
      
      $userId = $this->pdo->lastInsertId();
      
      // Create initial portfolio
      $portfolioStmt = $this->pdo->prepare("INSERT INTO portfolios (user_id, title, about) VALUES (?, 'My Portfolio', 'Welcome to my portfolio')");
      $portfolioStmt->execute([$userId]);
      
      $this->pdo->commit();
      return ["success" => true, "message" => "User created successfully"];
    } catch (\Throwable $th) {
      $this->pdo->rollBack();
      return array(
        "success" => false,
        "error" => $th->getMessage(),
        "code" => $th->getCode()
      );
    }
  }

  public function updatePortfolio($param) {
    try {
        if (!isset($param->user_id)) {
            return array(
                "success" => false,
                "error" => "User ID is required",
                "code" => 400
            );
        }

        // Validate and sanitize inputs
        $title = $param->title ?? 'My Portfolio';
        $about = $param->about ?? 'Welcome to my portfolio';
        $skills = is_array($param->skills) ? implode(', ', $param->skills) : ($param->skills ?? '');
        $contact_info = $param->contact_info ?? '';
        $theme_color = $param->theme_color ?? '#000000';
        $design_template = $param->design_template ?? 'classic';
        $education = $param->education ?? '';
        $achievements = $param->achievements ?? '';
        $social_links = $param->social_links ?? '';

        // Check if portfolio exists
        $checkStmt = $this->pdo->prepare("SELECT id FROM portfolios WHERE user_id = ?");
        $checkStmt->execute([$param->user_id]);
        
        if ($checkStmt->fetch()) {
            // Update existing
            $sql = "UPDATE portfolios SET 
                    title = ?, 
                    about = ?, 
                    skills = ?, 
                    contact_info = ?, 
                    theme_color = ?, 
                    design_template = ?,
                    education = ?,
                    achievements = ?,
                    social_links = ?
                    WHERE user_id = ?";
        } else {
            // Insert new
            $sql = "INSERT INTO portfolios (
                    title, about, skills, contact_info, 
                    theme_color, design_template, education,
                    achievements, social_links, user_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $title,
            $about,
            $skills,
            $contact_info,
            $theme_color,
            $design_template,
            $education,
            $achievements,
            $social_links,
            $param->user_id
        ]);
        
        return [
            "success" => true, 
            "message" => "Portfolio updated successfully",
            "data" => [
                "title" => $title,
                "about" => $about,
                "skills" => $skills,
                "contact_info" => $contact_info,
                "theme_color" => $theme_color,
                "design_template" => $design_template,
                "education" => $education,
                "achievements" => $achievements,
                "social_links" => $social_links
            ]
        ];
    } catch (\Throwable $th) {
        return array(
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        );
    }
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