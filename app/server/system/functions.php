<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../class/config.php';

class DatabaseHandler
{
    private $database;

    // 通过构造函数注入数据库实例
    public function __construct($database)
    {
        // 初始化数据库连接
        $this->database = $database;
    }

    public function handleDbError(string $errorMessage): void {
        header('Content-Type: application/json', true, 500);
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    }

  // 用户注册方法
  public function registerUser(string $username, string $password, string $email, string $pin, int $is_admin): array {
    if (strcasecmp($username, 'app') === 0) {
        // 如果用户名已存在，则返回错误信息
        return ['error' => '保留关键词，请更换后注册！'];
    }
    // 使用password_hash加密密码
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 首先检查用户名是否已存在
    $existingUser = $this->database->select('on_users', '*', [
        'username' => $username
    ]);
    // 首先检查邮箱是否已存在
    $existingEmail = $this->database->select('on_users', '*', [
        'email' => $email
    ]);
    if (!empty($existingUser)) {
        // 如果用户名已存在，则返回错误信息
        return ['error' => '用户名已存在，更换后重试！'];
    }
    if (!empty($existingEmail)) {
        // 如果用户名已存在，则返回错误信息
        return ['error' => '邮箱已存在，更换后重试！'];
    }
    try {
        // 使用Medoo的insert方法插入数据
        $success = $this->database->insert('on_users', [
            "username" => $username,
            "password" => $passwordHash,
            "email" => $email,
            "pin" => $pin,
            "is_admin" => $is_admin,
            "add_time" => $this->database->raw('CURRENT_TIMESTAMP'),
            "up_time" => $this->database->raw('CURRENT_TIMESTAMP')
        ]);
        return ['success' => true]; // 注意这里使用了 === true
    } catch (\Medoo\Exception $e) { // 更具体地捕获Medoo的异常
        // 检查异常消息，如果是唯一性约束错误，则返回相应的错误信息
        if (strpos($e->getMessage(), 'duplicate') !== false || strpos($e->getMessage(), 'exists') !== false) {
            return ['error' => '用户名已存在，更换后重试！'];
        }
        // 如果是其他类型的数据库错误，可以继续处理或重新抛出异常
        $this->handleDbError($e->getMessage());
        return ['error' => 'Database error occurred'];
    }
}

    // 用户登录方法
    public function loginUser($username, $password)
{
    // 查询用户
    $user = $this->database->select("on_users", "*", ["username" => $username]);

    if ($user && password_verify($password, $user[0]['password'])) {
        // 登录成功，可以在这里处理会话等逻辑
        session_start();
        $_SESSION['user_id'] = $user[0]['id'];
        $_SESSION['username'] = $user[0]['username'];
        $_SESSION['loggedin'] = true;
        return true;
    }

    return false;
}

// 根据用户名查询用户是否存在
public function checkUser($username) {
        // 查询用户
        $user = $this->database->select("on_users", "*", ["username" => $username]);
        if ($user) {
            return $user[0]['id'];
        }
        return false;
    }

// 查询以选取 id 最小的用户
public function minUser() {
    // 编写 SQL 查询语句
    $sql = "SELECT id,username FROM on_users where is_admin = 1 ORDER BY id ASC LIMIT 1";
    // 使用 query 方法执行 SQL 查询
    $result = $this->database->query($sql)->fetchAll();
    // var_dump 用于调试，查看查询结果
    //var_dump($result);
    // 检查查询结果是否为空
    if ($result) {
        // 返回查询结果中的第一个用户的 id
        return [
            'id' => $result[0]['id'],
            'username' => $result[0]['username']
        ];
    }
    // 如果没有查询到用户，返回 false
    return false;
}
    // 获取所有分类及其关联的链接
    public function getLinksByCategory(int $user_id): array
{
    // 使用 Medoo 2.1.12 的方法构建查询
    $results = $this->database->query("
        SELECT
            on_categorys.id AS category_id,
            on_categorys.name AS category_name,
            on_categorys.ispublic AS category_ispublic,
            IFnull(on_links.id IS NULL,'1') AS category_nolink,
            on_links.id,
            IFnull(on_links.title,'无书签，请添加！') AS title,
            on_links.url,
            IFnull(on_links.description,'请从导航菜单添加网址！') AS description,
            on_links.favicon,
            on_links.favicon_image,
            IFnull(on_links.ispublic,'on') AS link_ispublic
        FROM
            on_categorys
        LEFT JOIN
            on_links ON on_categorys.id = on_links.fid
        WHERE 1 = 1

            and on_categorys.uid = $user_id
        ORDER BY
            on_categorys.priority ASC,
            on_links.priority ASC
    ")->fetchAll();

    // 将结果转换成关联数组，按分类分组
    $groupedLinks = [];
    foreach ($results as $row) {
        $categoryName = $row['category_name'];
        if (!isset($groupedLinks[$categoryName])) {
            $groupedLinks[$categoryName] = [];
        }
        $groupedLinks[$categoryName][] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'url' => $row['url'],
            'description' => $row['description'],
            'favicon' => $row['favicon'],
            'favicon_image' => $row['favicon_image'],
            'ispublic' => $row['link_ispublic'], // 注意这里使用的是 'link_ispublic'
            'category_id' => $row['category_id'],
            'category_ispublic' => $row['category_ispublic'],
            'category_nolink' => $row['category_nolink'],
        ];
    }

    return $groupedLinks;
}

// 获取关键词所有分类及其关联的链接
    public function getLinksByCategoryKeywords(int $user_id, string $keywords): array
{
    // 使用 Medoo 2.1.12 的方法构建查询
    $results = $this->database->query("
        SELECT
            on_categorys.id AS category_id,
            on_categorys.name AS category_name,
            on_categorys.ispublic AS category_ispublic,
            on_links.id,
            on_links.title,
            on_links.url,
            on_links.description,
            on_links.favicon,
            on_links.favicon_image,
            on_links.ispublic AS link_ispublic
        FROM
            on_categorys
        LEFT JOIN
            on_links ON on_categorys.id = on_links.fid
        WHERE
            on_links.fid IS NOT NULL and on_categorys.uid = $user_id
            and (on_links.title LIKE '%$keywords%' OR on_links.description LIKE '%$keywords%')
        ORDER BY
            on_categorys.priority ASC,
            on_links.priority ASC
    ")->fetchAll();

    // 将结果转换成关联数组，按分类分组
    $groupedLinks = [];
    foreach ($results as $row) {
        $categoryName = $row['category_name'];
        if (!isset($groupedLinks[$categoryName])) {
            $groupedLinks[$categoryName] = [];
        }
        $groupedLinks[$categoryName][] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'url' => $row['url'],
            'description' => $row['description'],
            'favicon' => $row['favicon'],
            'favicon_image' => $row['favicon_image'],
            'ispublic' => $row['link_ispublic'], // 注意这里使用的是 'link_ispublic'
            'category_id' => $row['category_id'],
            'category_ispublic' => $row['category_ispublic'],
        ];
    }

    return $groupedLinks;
}
 /**
 * 根据ID获取单条链接记录
 *
 * @param int $linkId 链接ID
 * @return array|null 查询结果数组，如果没有找到则返回null
 */
public function getLinkById(int $linkId): ?array
{
    try {
        $result = $this->database->select(
            'on_links', // 表名
            '*', // 查询所有列
            [ // 条件
                'id' => $linkId, // 直接将$id作为条件值，Medoo会处理转义
            ],
            [
                'JOIN' => [ // 添加JOIN语句
                    ['on_categorys', 'fid', 'id', 'LEFT']
                ]
            ]);

        return $result[0] ?? null; // 返回数组中的第一个元素，如果没有元素则返回null
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}
    // 获取所有分类信息
    public function getAllCategories(int $user_id): array
    {
        // 直接查询 on_categorys 表获取所有分类信息
        $categories = $this->database->query(
            "SELECT id as fid,name as category_name FROM on_categorys
            WHERE uid = $user_id
            ORDER BY priority"
        )->fetchAll();
        return $categories;
    }

  /**
 * 根据ID获取单个分类信息
 *
 * @param int $id 分类ID
 * @return array|null 分类信息数组，如果未找到则返回null
 */
   public function getCategoryById(int $id): ?array
{
    try {
        $result = $this->database->select(
            'on_categorys', // 表名
            '*', // 查询所有列
            [ // 条件
                'id' => $id, // 直接将$id作为条件值，Medoo会处理转义
            ]);

        return $result[0] ?? null; // 返回数组中的第一个元素，如果没有元素则返回null
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}
//根据UID返回最小类别id
public function getMinCategoryByUid(int $uid): ?int
{
    try {
        $sql = "SELECT MIN(id) AS min_id FROM on_categorys WHERE uid = :uid";
        $result = $this->database->query($sql, [':uid' => $uid])->fetchAll();

        // 返回最小id的值，如果没有结果则返回null
        return $result[0]['min_id'] ?? null;
    } catch (PDOException $e) {
        // 更安全的错误处理方式
        error_log("Error: " . $e->getMessage());
        return null;
    }
}
    // 插入链接
    public function insertLink(int $uid,string $title, string $url, string $description, string $favicon,  string $favicon_image, int $fid, string $ispublic, int $priority): bool {
    try {
        // 使用Medoo的insert方法插入数据
        $statement = $this->database->insert('on_links', [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'favicon' => $favicon,
            'favicon_image' => $favicon_image,
            'fid' => $fid,
            'ispublic' => $ispublic,
            'priority' => $priority,
            'uid' => $uid,
            "add_time" => $this->database->raw('CURRENT_TIMESTAMP'),
            "up_time" => $this->database->raw('CURRENT_TIMESTAMP')
        ]);

        // 检查$statement是否是一个PDOStatement对象，并获取插入的行数
        if ($statement instanceof \PDOStatement) {
            $success = $statement->rowCount();
        } else {
            // 假设$statement直接包含插入的行数
            $success = $statement;
        }

        // 返回布尔值，表示是否成功插入至少一行
        return $success > 0;
    } catch (\Throwable $e) {
        // 捕获并处理数据库操作异常
        $this->handleDbError($e->getMessage());
        return false; // 在异常发生时返回false
    }
}

  // 更新链接
public function updateLink(int $linkId, string $title, string $url, string $description, string $favicon, string $favicon_image, int $fid, string $ispublic, int $priority): bool {
    try {
        // 使用Medoo的update方法更新数据
        $statement = $this->database->update('on_links', [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'favicon' => $favicon,
            'favicon_image' => $favicon_image,
            'fid' => $fid,
            'ispublic' => $ispublic,
            'priority' => $priority,
            "up_time" => $this->database->raw('CURRENT_TIMESTAMP')
        ], [
            'id' => $linkId // 这里假设你的表中有一个名为'id'的主键字段
        ]);

        // 检查$statement是否是一个PDOStatement对象，并获取受影响的行数
        if ($statement instanceof \PDOStatement) {
            $success = $statement->rowCount();
        } else {
            // 假设$statement直接包含受影响的行数
            $success = $statement;
        }

        // 返回布尔值，表示是否成功更新至少一行
        return $success > 0;
    } catch (\Throwable $e) {
        // 捕获并处理数据库操作异常
        $this->handleDbError($e->getMessage());
        return false; // 在异常发生时返回false
    }
}

  // 删除链接
  public function deleteLink(int $linkId): bool {
    try {
        // 使用Medoo的delete方法删除数据
        $statement = $this->database->delete('on_links', [
            'id' => $linkId // 这里假设你的表中有一个名为'id'的主键字段
        ]);

        // 检查$statement是否是一个PDOStatement对象，并获取受影响的行数
        if ($statement instanceof \PDOStatement) {
            $success = $statement->rowCount();
        } else {
            // 假设$statement直接包含受影响的行数
            $success = $statement;
        }

        // 返回布尔值，表示是否成功删除至少一行
        return $success > 0;
    } catch (\Throwable $e) {
        // 捕获并处理数据库操作异常
        $this->handleDbError($e->getMessage());
        return false; // 在异常发生时返回false
    }
}
    // 插入分类
    public function insertCategory(int $uid, string $category, string $ispublic, int $priority): bool {
    $data = [
        'name' => $category,
        'ispublic' => $ispublic,
        'priority' => $priority,
        'uid' => $uid,
        "add_time" => $this->database->raw('CURRENT_TIMESTAMP'),
        "up_time" => $this->database->raw('CURRENT_TIMESTAMP')
    ];
    $result = $this->database->insert('on_categorys', $data);
    return $this->database->id() > 0;
}


 // 更新分类
   public function updateCategory(int $id, string $category, string $ispublic, int $priority): bool {
    try {
        // 使用Medoo的update方法更新数据
        $statement = $this->database->update('on_categorys', [
            'name' => $category,
            'ispublic' => $ispublic,
            'priority' => $priority,
            "up_time" => $this->database->raw('CURRENT_TIMESTAMP')
        ], [
            'id' => $id // 这里假设你的表中有一个名为'id'的主键字段
        ]);

        // 检查$statement是否是一个PDOStatement对象，并获取受影响的行数
        if ($statement instanceof \PDOStatement) {
            $success = $statement->rowCount();
        } else {
            // 假设$statement直接包含受影响的行数
            $success = $statement;
        }

        // 返回布尔值，表示是否成功更新至少一行
        return $success > 0;
    } catch (\Throwable $e) {
        // 捕获并处理数据库操作异常
        $this->handleDbError($e->getMessage());
        return false; // 在异常发生时返回false
    }
}

public function deleteCategoryWithLinks(int $id): bool {
    try {
        // 开始事务
        $this->database->pdo->beginTransaction();

        // 查询是否存在与该分类相关的链接
        $hasLinks = $this->database->select('on_links', 'fid', ['fid' => $id]);
        // 删除分类
        $success_category = $this->database->delete('on_categorys', ['id' => $id]);
        // 删除与该分类相关的所有链接
        if($hasLinks){
          $success_links = $this->database->delete('on_links', ['fid' => $id]);
        }
        $success1 = $success_category->rowCount();
        //$success2 = $success_links->rowCount();
        // 如果分类或链接的删除都成功，提交事务
        if ($success1 > 0) {
            $this->database->pdo->commit();
            return true;
        } else {
            // 否则回滚事务
            $this->database->pdo->rollback();
            return false;
        }
    } catch (\Throwable $e) {
        // 发生异常时，确保事务回滚
        $this->database->pdo->rollback();
        $this->handleDbError($e->getMessage());
        return false;
    }
}

// 定义函数用于删除分类及其关联的链接
public function deleteCategory(int $id, int $fid): bool {
    try {
        // 假设你已经在类的构造函数或者某个初始化方法中获取或者创建了$pdo实例
        // 如果你还没有获取$pdo实例，你需要在开始事务前先创建或者获取它

        $this->database->pdo->beginTransaction(); // 开始事务

        // 查询是否存在与该分类相关的链接
        $hasLinks = $this->database->select('on_links', 'fid', ['fid' => $id]);
        // 更新所有引用当前分类ID的链接，将其更改为新的分类ID
        if($hasLinks){
          $success_links = $this->database->update('on_links', ['fid' => $fid], ['fid' => $id]);
        }

        // 删除分类
        $success_category = $this->database->delete('on_categorys', ['id' => $id]);
        //$success1 = $success_links->rowCount();
        $success2 = $success_category->rowCount();
        // 如果所有操作成功，则提交事务
        if ($success2 > 0) {
            $this->database->pdo->commit();
            return true;
        } else {
            // 否则，回滚事务
            $this->database->pdo->rollBack();
            return false;
        }
    } catch (\Throwable $e) {
        // 捕获异常并回滚事务
        $this->database->pdo->rollBack();
        $this->handleDbError($e->getMessage());
        return false;
    }
}

// 查询options
public function getAllOptions(): ?array
{
	//session_start(); // 在使用$_SESSION之前务必加入这一行
  $user_id = htmlspecialchars((int) $_SESSION['user_id']);
    try {
        // 选择特定的字段
        $result = $this->database->select(
            'on_users',
            [
                'id as user_id', // 将'id'字段重命名为'user_id'
                'category_open', // 保留原字段名
                'link_open',      // 添加另一个字段
                'all_open',
                'themes'
            ],
            [
                'id' => $user_id
            ]
        );

        // 如果只有一条记录，返回这条记录
        return count($result) > 0 ? $result[0] : null;
    } catch (PDOException $e) {
        // 使用日志记录错误，这里可以使用更专业的日志处理库
        error_log("Error: " . $e->getMessage());

        // 返回null表示操作失败
        return null;
    }
}
// 获取用户主题
public function getTheme(): ?string
    {
        $user_id = htmlspecialchars((int) $_SESSION['user_id']);

        try {
            // 选择特定的字段 'themes'
            $result = $this->database->select(
                'on_users',
                ['themes'], // 只查询 'themes' 字段
                ['id' => $user_id]
            );

            // 如果只有一条记录，返回这条记录的 'themes' 字段值
            return count($result) > 0 ? $result[0]['themes'] : 'default';
        } catch (PDOException $e) {
            // 使用日志记录错误，这里可以使用更专业的日志处理库
            error_log("Error: " . $e->getMessage());

            // 返回null表示操作失败
            return null;
        }
    }

// 设置
    public function updateUsers(int $user_id, string $username, string $password, ?string $newpassword, ?string $email, ?string $category_open, ?string $link_open, ?string $all_open,  ?string $themes): bool {
    // 参数验证
    if (empty($username) || empty($password)) {
        throw new InvalidArgumentException("用户名和密码不能为空！");
    }

    // 查询用户
    $user = $this->database->get('on_users', '*', [
        'username' => $username,
        'id' => $user_id // 确保用户名和用户ID匹配
    ]);

    if (empty($user) || !password_verify($password, $user['password'])) {
        throw new InvalidArgumentException("无效用户名或密码错误！");
    }

    // 构建更新数据
    $updateData = [];

    if ($newpassword !== null && !empty($newpassword)) { // 添加检查新密码是否非空
        $updateData['password'] = password_hash($newpassword, PASSWORD_DEFAULT);
    }

    if ($email !== null && !empty($email)) {
        $updateData['email'] = $email;
    }

    $updateData['category_open'] = $category_open;
    $updateData['link_open'] = $link_open;
    $updateData['all_open'] = $all_open;
    $updateData['themes'] = $themes;

    // 更新用户数据
    if (!empty($updateData)) {
        $updateData['up_time'] = $this->database->raw('CURRENT_TIMESTAMP');
        $this->database->update('on_users', $updateData, [
            'id' => $user_id
        ]);
    }

    return true;
}

// 查询PIN
public function getPin(): ?array
{
  $uid = htmlspecialchars((int) $_SESSION['user_id']);
    try {
        // 选择特定的字段
        $result = $this->database->select(
            'on_users',
            [
                'id as user_id', // 将'id'字段重命名为'user_id'
                'pin'
            ],
            [
                'id' => $uid
            ]
        );

        // 如果只有一条记录，返回这条记录
        return count($result) > 0 ? $result[0] : null;
    } catch (PDOException $e) {
        // 使用日志记录错误，这里可以使用更专业的日志处理库
        error_log("Error: " . $e->getMessage());

        // 返回null表示操作失败
        return null;
    }
}


// 刷新PIN
public function updatePin(int $uid, string $pin): bool {
    try {
        // 使用Medoo的update方法更新数据
        $statement = $this->database->update('on_users', [
            'pin' => $pin
        ], [
            'id' => $uid // 这里假设你的表中有一个名为'id'的主键字段
        ]);

        // 检查$statement是否是一个PDOStatement对象，并获取受影响的行数
        if ($statement instanceof \PDOStatement) {
            $success = $statement->rowCount();
        } else {
            // 假设$statement直接包含受影响的行数
            $success = $statement;
        }

        // 返回布尔值，表示是否成功更新至少一行
        return $success > 0;
    } catch (\Throwable $e) {
        // 捕获并处理数据库操作异常
        $this->handleDbError($e->getMessage());
        return false; // 在异常发生时返回false
    }
}

// 重置密码
public function resetPassWord(string $pin, string $password, string $email, string $newpin): ?array
{
    try {
        // 选择特定的字段
        $result = $this->database->select(
            'on_users',
            [
                'id',
                'email',
                'pin'
            ],
            [
                'email' => $email
            ]
        );
        if (empty($result)) {
            return ['error' => '找不到注册邮箱对应的用户！'];
            throw new Exception('找不到注册邮箱对应的用户！');
        } elseif ($pin !== $result[0]['pin']) { // 使用严格比较运算符
            return ['error' => 'PIN码不正确！'];
        } else {
            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                if (!isset($result[0]['id'])) {
                    return ['error' => '用户ID获取失败，请稍后再试！'];
                }
                $uid = $result[0]['id']; // 直接使用'id'，因为查询结果中没有'user_id'

                // 使用Medoo的update方法更新数据
                $updateResult = $this->database->update('on_users', [
                    'password' => $passwordHash,
                    'pin' => $newpin  // 使用正确的变量
                ], [
                    'id' => $uid // 使用正确的用户ID进行更新
                ]);

                // 检查$statement是否是一个PDOStatement对象，并获取受影响的行数
              if ($updateResult instanceof \PDOStatement) {
               $success = $updateResult->rowCount();
              } else {
            // 假设$statement直接包含受影响的行数
               $success = $updateResult;
              }
                // 检查受影响的行数并返回对应的结果
                if ($success > 0) {
                    return ['success' => '密码重置成功！']; // 成功消息
                } else {
                    return ['error' => '未能更新密码，请稍后再试！'];
                }

            } catch (\Throwable $e) {
                // 捕获并处理数据库操作异常
                $this->handleDbError($e->getMessage());
                return ['error' => '发生错误，请稍后再试！详细信息：' . $e->getMessage()]; // 更友好的错误提示
            }
        }

    } catch (\PDOException $e) {
        // 使用日志记录错误，这里可以使用更专业的日志处理库
        error_log("Error: " . $e->getMessage());

        // 返回带有错误信息的数组
        return ['error' => '系统错误，请联系管理员！详细信息：' . $e->getMessage()];
    }
}

} // 最外层的结束}
// 解决高版本MEDOO问题
if (!function_exists('array_key_last')) {
    function array_key_last(array $array): ?string {
        $keys = array_keys($array);
        return end($keys) === false ? null : $keys[count($keys) - 1];
    }
}

function charToColorCode($char) {
    // 获取字符的ASCII值
    $asciiValue = ord($char);

    // 利用ASCII值生成一个"稍微"随机的数，通过一系列运算使结果落在颜色码的有效范围内
    // 注意：这里简化处理，实际应用中可能需要更复杂的算法以确保颜色的多样性
    $colorCode = dechex((($asciiValue * 31) % 256) * 65536 + (($asciiValue * 17) % 256) * 256 + (($asciiValue * 7) % 256));

    // 确保颜色码为6位，并且以"#"开头，这是标准的HTML颜色表示法
    return '#' . str_pad($colorCode, 6, '0', STR_PAD_LEFT);
}

function validatePassword($password) {
    $isValid = true;
    $errors = [];
    // 至少包含一个小写字母
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "缺少小写字母";
        $isValid = false;
    }

    // 至少包含一个大写字母
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "缺少大写字母";
        $isValid = false;
    }

    // 至少包含一个数字
    if (!preg_match('/\d/', $password)) {
        $errors[] = "缺少数字";
        $isValid = false;
    }

    // 至少包含一个特殊字符
    if (!preg_match('/[@$!%*?&]/', $password)) {
        $errors[] = "缺少特殊字符";
        $isValid = false;
    }

    // 密码长度至少为8位
    if (strlen($password) < 8) {
        $errors[] = "长度不足8位";
        $isValid = false;
    }

    return [
        'isValid' => $isValid,
        'errors' => $errors
           ];
}
// 生成随机字符 使用示例 echo generateRandomString(6);
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    // 检查PHP版本
    if (version_compare(phpversion(), '7.0.0', '>=')) {
        // 使用加密安全的 random_int() 函数（PHP 7.0.0+）
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
    } else {
        // 对于PHP 7.0.0以下版本，使用mt_rand()
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
    }

    return $randomString;
}


/*
function showAletrMessage($message) {
    // Generate the message div
    $html = '<div id="alertPopup" class="alert-popup">' . htmlspecialchars($message) . '</div>';
    // Output the HTML
    return $html;
}
*/
