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
            
            foreach ($param->skills as $skillData) {
                // Handle both object and array access
                $skill = is_object($skillData) ? $skillData->name : ($skillData['name'] ?? '');
                $description = is_object($skillData) ? $skillData->description : ($skillData['description'] ?? '');
                
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

  public function createPortfolio($param) {
    if (!$param || !isset($param->user_id) || !isset($param->title)) {
      return array(
        "success" => false,
        "error" => "Missing required fields",
        "code" => 400
      );
    }

    try {
      $this->pdo->beginTransaction();

      // Insert main portfolio data
      $sql = "INSERT INTO portfolios (
        user_id, 
        title, 
        about,
        contact_info,
        theme_color,
        design_template,
        education,
        achievements,
        social_links
      ) VALUES (
        :user_id, 
        :title, 
        :about,
        :contact_info,
        :theme_color,
        :design_template,
        :education,
        :achievements,
        :social_links
      )";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute([
        ':user_id' => $param->user_id,
        ':title' => $param->title,
        ':about' => $param->about ?? '',
        ':contact_info' => $param->contact_info ?? '',
        ':theme_color' => $param->theme_color ?? '#000000',
        ':design_template' => $param->design_template ?? 'classic',
        ':education' => $param->education ?? '',
        ':achievements' => $param->achievements ?? '',
        ':social_links' => $param->social_links ?? ''
      ]);

      $portfolioId = $this->pdo->lastInsertId();

      // Insert skills
      if (!empty($param->skills)) {
        $skillSql = "INSERT INTO skills (user_id, skill, description) VALUES (:user_id, :skill, :description)";
        $skillStmt = $this->pdo->prepare($skillSql);
        
        foreach ($param->skills as $skill) {
          // Handle both object and array formats
          $skillName = is_array($skill) ? $skill['name'] : $skill->name;
          $skillDescription = is_array($skill) ? $skill['description'] : $skill->description;
          
          $skillStmt->execute([
            ':user_id' => $param->user_id,
            ':skill' => $skillName,
            ':description' => $skillDescription ?? ''
          ]);
        }
      }

      // Insert projects
      if (!empty($param->projects)) {
        $projectSql = "INSERT INTO projects (user_id, title, description, project_url) 
                      VALUES (:user_id, :title, :description, :project_url)";
        $projectStmt = $this->pdo->prepare($projectSql);
        
        foreach ($param->projects as $project) {
          // Handle both object and array formats
          $projectTitle = is_array($project) ? $project['title'] : $project->title;
          $projectDescription = is_array($project) ? $project['description'] : $project->description;
          $projectUrl = is_array($project) ? $project['url'] : $project->url;
          
          $projectStmt->execute([
            ':user_id' => $param->user_id,
            ':title' => $projectTitle,
            ':description' => $projectDescription,
            ':project_url' => $projectUrl
          ]);
        }
      }

      $this->pdo->commit();

      // Get user data for redirect
      $userStmt = $this->pdo->prepare("SELECT username FROM users WHERE id = ?");
      $userStmt->execute([$param->user_id]);
      $user = $userStmt->fetch();

      return [
        "success" => true,
        "message" => "Portfolio created successfully",
        "user_id" => $param->user_id,
        "username" => $user['username']
      ];
    } catch (\Throwable $th) {
      $this->pdo->rollBack();
      return [
        "success" => false,
        "error" => $th->getMessage(),
        "code" => 500
      ];
    }
  }

  public function updateProfile($param) {
    try {
      $stmt = $this->pdo->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, username = ?
        WHERE id = ?
      ");
      
      $stmt->execute([
        $param->full_name,
        $param->email,
        $param->username,
        $param->user_id
      ]);

      // Update session data
      $_SESSION['username'] = $param->username;
      $_SESSION['email'] = $param->email;

      return [
        "success" => true,
        "message" => "Profile updated successfully",
        "user" => [
          "id" => $param->user_id,
          "username" => $param->username,
          "email" => $param->email,
          "full_name" => $param->full_name
        ]
      ];
    } catch (\Throwable $th) {
      return [
        "success" => false,
        "error" => $th->getMessage(),
        "code" => 500
      ];
    }
  }

  public function deletePortfolio($userId) {
    try {
      $this->pdo->beginTransaction();

      // Delete skills
      $stmt = $this->pdo->prepare("DELETE FROM skills WHERE user_id = ?");
      $stmt->execute([$userId]);

      // Delete projects
      $stmt = $this->pdo->prepare("DELETE FROM projects WHERE user_id = ?");
      $stmt->execute([$userId]);

      // Delete portfolio
      $stmt = $this->pdo->prepare("DELETE FROM portfolios WHERE user_id = ?");
      $stmt->execute([$userId]);

      $this->pdo->commit();

      return [
        "success" => true,
        "message" => "Portfolio deleted successfully"
      ];
    } catch (\Throwable $th) {
      $this->pdo->rollBack();
      return [
        "success" => false,
        "error" => $th->getMessage(),
        "code" => 500
      ];
    }
  }
}