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

        // Prepare portfolio update
        $sqlPortfolio = "UPDATE portfolios 
                         SET title = ?, about = ?, contact_info = ?, 
                             theme_color = ?, design_template = ?, 
                             education = ?, achievements = ?, social_links = ?
                         WHERE user_id = ?";
        
        $stmt = $this->pdo->prepare($sqlPortfolio);
        $stmt->execute([
            $param->title ?? 'My Portfolio',
            $param->about ?? 'Welcome to my portfolio',
            is_string($param->contact_info) ? $param->contact_info : json_encode($param->contact_info),
            $param->theme_color ?? '#000000',
            $param->design_template ?? 'classic',
            is_string($param->education) ? $param->education : json_encode($param->education),
            is_string($param->achievements) ? $param->achievements : json_encode($param->achievements),
            $param->social_links ?? '',
            $param->user_id
        ]);

        // Handle skills with descriptions
        if (isset($param->skills)) {
            // First, remove existing skills
            $deleteSkillsStmt = $this->pdo->prepare("DELETE FROM skills WHERE user_id = ?");
            $deleteSkillsStmt->execute([$param->user_id]);

            // Prepare skills insertion
            $insertSkillStmt = $this->pdo->prepare("INSERT INTO skills (user_id, skill, description) VALUES (?, ?, ?)");
            
            // Ensure skills is an array
            $skills = is_array($param->skills) ? $param->skills : [];

            // Insert each skill
            foreach ($skills as $skillData) {
                $skill = $skillData['name'] ?? '';
                $description = $skillData['description'] ?? '';
                
                if (!empty(trim($skill))) {
                    $insertSkillStmt->execute([
                        $param->user_id, 
                        trim($skill), 
                        trim($description)
                    ]);
                }
            }
        }

        return [
            "success" => true,
            "message" => "Portfolio updated successfully"
        ];
    } catch (\Throwable $th) {
        error_log("Portfolio Update Error: " . $th->getMessage());
        return [
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        ];
    }
  }

  public function addProject($param) {
    try {
        $stmt = $this->pdo->prepare("
            INSERT INTO projects 
            (user_id, title, description, project_url) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $param->user_id,
            $param->title,
            $param->description,
            $param->project_url
        ]);
        
        $projectId = $this->pdo->lastInsertId();
        
        return [
            "success" => true, 
            "message" => "Project added successfully",
            "data" => [
                "id" => $projectId,
                "title" => $param->title,
                "description" => $param->description,
                "project_url" => $param->project_url
            ]
        ];
    } catch (\Throwable $th) {
        error_log("Add Project Error: " . $th->getMessage());
        return [
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        ];
    }
  }

  public function updateProject($param) {
    $sqlString = "UPDATE projects 
                  SET title = ?, description = ?, project_url = ? 
                  WHERE id = ? AND user_id = ?";
    try {
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([
            $param->title,
            $param->description,
            $param->project_url,
            $param->id,
            $param->user_id
        ]);
        return ["success" => true, "message" => "Project updated successfully"];
    } catch (\Throwable $th) {
        return [
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        ];
    }
  }

  public function deleteProject($projectId, $userId) {
    $sqlString = "DELETE FROM projects WHERE id = ? AND user_id = ?";
    try {
        $stmt = $this->pdo->prepare($sqlString);
        $stmt->execute([$projectId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            return [
                "success" => false,
                "error" => "Project not found or unauthorized",
                "code" => 404
            ];
        }
        
        return ["success" => true, "message" => "Project deleted successfully"];
    } catch (\Throwable $th) {
        return [
            "success" => false,
            "error" => $th->getMessage(),
            "code" => $th->getCode()
        ];
    }
  }
}