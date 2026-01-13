<?php
/*
 * КОМПЛЕКСНАЯ СИСТЕМА УПРАВЛЕНИЯ БИБЛИОТЕКОЙ
 * Версия 2.0
 * Автор: AI Assistant
 */

// ============================================
// КОНФИГУРАЦИЯ И НАСТРОЙКИ
// ============================================

define('LIBRARY_NAME', 'Центральная городская библиотека им. Чайковского');
define('MAX_BOOKS_PER_USER', 5);
define('FINE_PER_DAY', 10); // штраф за день просрочки
define('DB_HOST', 'localhost');
define('DB_NAME', 'library_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// КЛАССЫ СИСТЕМЫ
// ============================================

/**
 * Класс книги
 */
class Book {
    private $id;
    private $title;
    private $author;
    private $year;
    private $genre;
    private $isbn;
    private $available;
    private $borrowerId;
    private $dueDate;
    
    public function __construct($id, $title, $author, $year, $genre, $isbn) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->genre = $genre;
        $this->isbn = $isbn;
        $this->available = true;
        $this->borrowerId = null;
        $this->dueDate = null;
    }
    
    public function borrow($userId, $days = 14) {
        if ($this->available) {
            $this->available = false;
            $this->borrowerId = $userId;
            $this->dueDate = date('Y-m-d', strtotime("+$days days"));
            return true;
        }
        return false;
    }
    
    public function returnBook() {
        $this->available = true;
        $this->borrowerId = null;
        $this->dueDate = null;
    }
    
    public function isOverdue() {
        if ($this->dueDate && !$this->available) {
            return strtotime($this->dueDate) < time();
        }
        return false;
    }
    
    public function calculateFine() {
        if ($this->isOverdue()) {
            $daysOverdue = floor((time() - strtotime($this->dueDate)) / (60 * 60 * 24));
            return $daysOverdue * FINE_PER_DAY;
        }
        return 0;
    }
    
    // Геттеры
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getAuthor() { return $this->author; }
    public function getYear() { return $this->year; }
    public function getGenre() { return $this->genre; }
    public function getIsbn() { return $this->isbn; }
    public function isAvailable() { return $this->available; }
    public function getBorrowerId() { return $this->borrowerId; }
    public function getDueDate() { return $this->dueDate; }
    
    public function getInfo() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'year' => $this->year,
            'genre' => $this->genre,
            'isbn' => $this->isbn,
            'available' => $this->available ? 'Да' : 'Нет',
            'due_date' => $this->dueDate
        ];
    }
}

/**
 * Класс пользователя
 */
class User {
    private $id;
    private $name;
    private $email;
    private $phone;
    private $registrationDate;
    private $borrowedBooks = [];
    
    public function __construct($id, $name, $email, $phone) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->registrationDate = date('Y-m-d');
    }
    
    public function canBorrowMore() {
        return count($this->borrowedBooks) < MAX_BOOKS_PER_USER;
    }
    
    public function borrowBook($bookId) {
        if ($this->canBorrowMore()) {
            $this->borrowedBooks[] = $bookId;
            return true;
        }
        return false;
    }
    
    public function returnBook($bookId) {
        $key = array_search($bookId, $this->borrowedBooks);
        if ($key !== false) {
            unset($this->borrowedBooks[$key]);
            $this->borrowedBooks = array_values($this->borrowedBooks);
            return true;
        }
        return false;
    }
    
    // Геттеры
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getRegistrationDate() { return $this->registrationDate; }
    public function getBorrowedBooks() { return $this->borrowedBooks; }
    
    public function getInfo() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'registration_date' => $this->registrationDate,
            'borrowed_books_count' => count($this->borrowedBooks)
        ];
    }
}

/**
 * Класс библиотеки
 */
class Library {
    private $name;
    private $books = [];
    private $users = [];
    private $transactions = [];
    private $nextBookId = 1;
    private $nextUserId = 1;
    private $nextTransactionId = 1;
    
    public function __construct($name) {
        $this->name = $name;
        $this->initializeSampleData();
    }
    
    private function initializeSampleData() {
        // Добавляем начальные книги
        $initialBooks = [
            ['Война и мир', 'Лев Толстой', 1869, 'Роман', '978-5-389-00000-1'],
            ['Преступление и наказание', 'Фёдор Достоевский', 1866, 'Роман', '978-5-389-00000-2'],
            ['Мастер и Маргарита', 'Михаил Булгаков', 1967, 'Роман', '978-5-389-00000-3'],
            ['1984', 'Джордж Оруэлл', 1949, 'Антиутопия', '978-5-389-00000-4'],
            ['Гарри Поттер и философский камень', 'Дж. К. Роулинг', 1997, 'Фэнтези', '978-5-389-00000-5'],
            ['Маленький принц', 'Антуан де Сент-Экзюпери', 1943, 'Притча', '978-5-389-00000-6'],
            ['Три товарища', 'Эрих Мария Ремарк', 1936, 'Роман', '978-5-389-00000-7'],
            ['Атлант расправил плечи', 'Айн Рэнд', 1957, 'Философский роман', '978-5-389-00000-8']
        ];
        
        foreach ($initialBooks as $bookData) {
            $this->addBook(...$bookData);
        }
        
        // Добавляем начальных пользователей
        $initialUsers = [
            ['Иван Петров', 'ivan@mail.ru', '+7(999)123-45-67'],
            ['Мария Сидорова', 'maria@mail.ru', '+7(999)765-43-21'],
            ['Алексей Иванов', 'alex@mail.ru', '+7(999)111-22-33']
        ];
        
        foreach ($initialUsers as $userData) {
            $this->registerUser(...$userData);
        }
    }
    
    public function addBook($title, $author, $year, $genre, $isbn) {
        $book = new Book($this->nextBookId++, $title, $author, $year, $genre, $isbn);
        $this->books[$book->getId()] = $book;
        return $book->getId();
    }
    
    public function registerUser($name, $email, $phone) {
        $user = new User($this->nextUserId++, $name, $email, $phone);
        $this->users[$user->getId()] = $user;
        return $user->getId();
    }
    
    public function borrowBook($userId, $bookId, $days = 14) {
        if (!isset($this->users[$userId]) || !isset($this->books[$bookId])) {
            return ['success' => false, 'message' => 'Пользователь или книга не найдены'];
        }
        
        $user = $this->users[$userId];
        $book = $this->books[$bookId];
        
        if (!$user->canBorrowMore()) {
            return ['success' => false, 'message' => 'Превышен лимит книг для пользователя'];
        }
        
        if (!$book->isAvailable()) {
            return ['success' => false, 'message' => 'Книга уже выдана'];
        }
        
        if ($book->borrow($userId, $days) && $user->borrowBook($bookId)) {
            $transaction = [
                'id' => $this->nextTransactionId++,
                'user_id' => $userId,
                'book_id' => $bookId,
                'action' => 'borrow',
                'date' => date('Y-m-d H:i:s'),
                'due_date' => $book->getDueDate()
            ];
            $this->transactions[] = $transaction;
            
            return ['success' => true, 'message' => 'Книга успешно выдана', 'due_date' => $book->getDueDate()];
        }
        
        return ['success' => false, 'message' => 'Неизвестная ошибка'];
    }
    
    public function returnBook($userId, $bookId) {
        if (!isset($this->users[$userId]) || !isset($this->books[$bookId])) {
            return ['success' => false, 'message' => 'Пользователь или книга не найдены'];
        }
        
        $user = $this->users[$userId];
        $book = $this->books[$bookId];
        
        if ($book->getBorrowerId() != $userId) {
            return ['success' => false, 'message' => 'Эта книга не выдана данному пользователю'];
        }
        
        $fine = $book->calculateFine();
        $book->returnBook();
        $user->returnBook($bookId);
        
        $transaction = [
            'id' => $this->nextTransactionId++,
            'user_id' => $userId,
            'book_id' => $bookId,
            'action' => 'return',
            'date' => date('Y-m-d H:i:s'),
            'fine' => $fine
        ];
        $this->transactions[] = $transaction;
        
        $message = 'Книга успешно возвращена';
        if ($fine > 0) {
            $message .= ". Штраф за просрочку: $fine руб.";
        }
        
        return ['success' => true, 'message' => $message, 'fine' => $fine];
    }
    
    public function searchBooks($keyword) {
        $results = [];
        $keyword = strtolower($keyword);
        
        foreach ($this->books as $book) {
            $title = strtolower($book->getTitle());
            $author = strtolower($book->getAuthor());
            $genre = strtolower($book->getGenre());
            
            if (strpos($title, $keyword) !== false || 
                strpos($author, $keyword) !== false || 
                strpos($genre, $keyword) !== false) {
                $results[] = $book->getInfo();
            }
        }
        
        return $results;
    }
    
    public function getStatistics() {
        $totalBooks = count($this->books);
        $availableBooks = 0;
        $borrowedBooks = 0;
        $totalUsers = count($this->users);
        $overdueBooks = 0;
        $totalFines = 0;
        
        foreach ($this->books as $book) {
            if ($book->isAvailable()) {
                $availableBooks++;
            } else {
                $borrowedBooks++;
                if ($book->isOverdue()) {
                    $overdueBooks++;
                    $totalFines += $book->calculateFine();
                }
            }
        }
        
        return [
            'total_books' => $totalBooks,
            'available_books' => $availableBooks,
            'borrowed_books' => $borrowedBooks,
            'total_users' => $totalUsers,
            'overdue_books' => $overdueBooks,
            'total_fines' => $totalFines
        ];
    }
    
    public function generateReport($type = 'general') {
        $report = "Отчет библиотеки: $this->name\n";
        $report .= "Дата генерации: " . date('Y-m-d H:i:s') . "\n";
        $report .= "========================================\n\n";
        
        if ($type == 'general' || $type == 'all') {
            $stats = $this->getStatistics();
            $report .= "ОБЩАЯ СТАТИСТИКА:\n";
            $report .= "Всего книг: {$stats['total_books']}\n";
            $report .= "Доступно книг: {$stats['available_books']}\n";
            $report .= "Выдано книг: {$stats['borrowed_books']}\n";
            $report .= "Просрочено книг: {$stats['overdue_books']}\n";
            $report .= "Всего пользователей: {$stats['total_users']}\n";
            $report .= "Общая сумма штрафов: {$stats['total_fines']} руб.\n\n";
        }
        
        if ($type == 'overdue' || $type == 'all') {
            $report .= "ПРОСРОЧЕННЫЕ КНИГИ:\n";
            $hasOverdue = false;
            
            foreach ($this->books as $book) {
                if ($book->isOverdue()) {
                    $hasOverdue = true;
                    $user = $this->users[$book->getBorrowerId()] ?? null;
                    $userName = $user ? $user->getName() : 'Неизвестно';
                    $fine = $book->calculateFine();
                    $report .= "- {$book->getTitle()} ({$book->getAuthor()})\n";
                    $report .= "  Читатель: $userName\n";
                    $report .= "  Дата возврата: {$book->getDueDate()}\n";
                    $report .= "  Штраф: $fine руб.\n\n";
                }
            }
            
            if (!$hasOverdue) {
                $report .= "Просроченных книг нет\n\n";
            }
        }
        
        return $report;
    }
    
    // Геттеры для получения данных
    public function getAllBooks() {
        $booksInfo = [];
        foreach ($this->books as $book) {
            $booksInfo[] = $book->getInfo();
        }
        return $booksInfo;
    }
    
    public function getAllUsers() {
        $usersInfo = [];
        foreach ($this->users as $user) {
            $usersInfo[] = $user->getInfo();
        }
        return $usersInfo;
    }
    
    public function getRecentTransactions($limit = 10) {
        return array_slice(array_reverse($this->transactions), 0, $limit);
    }
}

// ============================================
// ИНИЦИАЛИЗАЦИЯ И РАБОТА С СИСТЕМОЙ
// ============================================

$library = new Library(LIBRARY_NAME);

// Демонстрация работы системы
echo "<h1>Система управления библиотекой</h1>";
echo "<h2>" . LIBRARY_NAME . "</h2>";

// Добавляем еще книги
$library->addBook("Анна Каренина", "Лев Толстой", 1877, "Роман", "978-5-389-00000-9");
$library->addBook("Братья Карамазовы", "Фёдор Достоевский", 1880, "Роман", "978-5-389-00001-0");

// Регистрируем нового пользователя
$newUserId = $library->registerUser("Сергей Смирнов", "sergey@mail.ru", "+7(999)888-77-66");

// Выдаем книги
echo "<h3>Выдача книг:</h3>";
$result1 = $library->borrowBook(1, 2); // Иван берет "Преступление и наказание"
echo "<p>" . ($result1['success'] ? '✅ ' : '❌ ') . $result1['message'] . "</p>";

$result2 = $library->borrowBook(2, 5); // Мария берет "Гарри Поттера"
echo "<p>" . ($result2['success'] ? '✅ ' : '❌ ') . $result2['message'] . "</p>";

$result3 = $library->borrowBook($newUserId, 1); // Сергей берет "Войну и мир"
echo "<p>" . ($result3['success'] ? '✅ ' : '❌ ') . $result3['message'] . "</p>";

// Поиск книг
echo "<h3>Поиск книг по слову 'роман':</h3>";
$searchResults = $library->searchBooks('роман');
echo "<ul>";
foreach ($searchResults as $book) {
    echo "<li>{$book['title']} - {$book['author']} ({$book['year']}) - {$book['available']}</li>";
}
echo "</ul>";

// Статистика
echo "<h3>Статистика библиотеки:</h3>";
$stats = $library->getStatistics();
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Показатель</th><th>Значение</th></tr>";
foreach ($stats as $key => $value) {
    $label = [
        'total_books' => 'Всего книг',
        'available_books' => 'Доступно книг',
        'borrowed_books' => 'Выдано книг',
        'total_users' => 'Всего пользователей',
        'overdue_books' => 'Просрочено книг',
        'total_fines' => 'Общий штраф (руб.)'
    ][$key] ?? $key;
    echo "<tr><td>$label</td><td>$value</td></tr>";
}
echo "</table>";

// Возврат книги
echo "<h3>Возврат книги:</h3>";
$returnResult = $library->returnBook(1, 2);
echo "<p>" . ($returnResult['success'] ? '✅ ' : '❌ ') . $returnResult['message'] . "</p>";

// Последние операции
echo "<h3>Последние операции:</h3>";
$recentTransactions = $library->getRecentTransactions(5);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Действие</th><th>Дата</th><th>Книга ID</th><th>Пользователь ID</th></tr>";
foreach ($recentTransactions as $transaction) {
    $action = $transaction['action'] == 'borrow' ? 'Выдача' : 'Возврат';
    echo "<tr>
            <td>{$transaction['id']}</td>
            <td>$action</td>
            <td>{$transaction['date']}</td>
            <td>{$transaction['book_id']}</td>
            <td>{$transaction['user_id']}</td>
          </tr>";
}
echo "</table>";

// Генерация отчета
echo "<h3>Отчет библиотеки:</h3>";
echo "<pre>";
echo htmlspecialchars($library->generateReport('all'));
echo "</pre>";

// Дополнительные функции
echo "<h3>Дополнительные возможности системы:</h3>";
echo "<h4>1. Подсчет средней оценки книг (если бы была система рейтингов):</h4>";

// Симуляция системы рейтингов
$bookRatings = [
    1 => [5, 4, 5, 3, 4], // Война и мир
    2 => [4, 5, 5, 4],    // Преступление и наказание
    3 => [5, 5, 4, 5, 5], // Мастер и Маргарита
    4 => [4, 4, 3, 5],    // 1984
    5 => [5, 5, 4, 5, 4]  // Гарри Поттер
];

function calculateAverageRatings($ratings) {
    $averages = [];
    foreach ($ratings as $bookId => $scores) {
        $averages[$bookId] = [
            'average' => round(array_sum($scores) / count($scores), 1),
            'votes' => count($scores)
        ];
    }
    arsort($averages); // Сортировка по убыванию рейтинга
    return $averages;
}

$averageRatings = calculateAverageRatings($bookRatings);
echo "<p>Средние оценки книг:</p>";
echo "<ul>";
foreach ($averageRatings as $bookId => $ratingInfo) {
    echo "<li>Книга ID $bookId: {$ratingInfo['average']} звезд (голосов: {$ratingInfo['votes']})</li>";
}
echo "</ul>";

echo "<h4>2. Поиск популярных жанров:</h4>";
$allBooks = $library->getAllBooks();
$genreCount = [];

foreach ($allBooks as $book) {
    $genre = $book['genre'];
    if (!isset($genreCount[$genre])) {
        $genreCount[$genre] = 0;
    }
    $genreCount[$genre]++;
}

arsort($genreCount);
echo "<p>Количество книг по жанрам:</p>";
echo "<ul>";
foreach ($genreCount as $genre => $count) {
    echo "<li>$genre: $count книг</li>";
}
echo "</ul>";

echo "<h4>3. Симуляция работы системы рекомендаций:</h4>";
function recommendBooks($userHistory, $allBooks) {
    // Простая система рекомендаций на основе жанров
    $genrePreferences = [];
    
    // Анализируем предпочтения пользователя
    foreach ($userHistory as $bookId) {
        foreach ($allBooks as $book) {
            if ($book['id'] == $bookId) {
                $genre = $book['genre'];
                if (!isset($genrePreferences[$genre])) {
                    $genrePreferences[$genre] = 0;
                }
                $genrePreferences[$genre]++;
            }
        }
    }
    
    // Рекомендуем книги в любимых жанрах
    $recommendations = [];
    foreach ($allBooks as $book) {
        if (isset($genrePreferences[$book['genre']]) && $book['available'] == 'Да') {
            $recommendations[] = $book;
        }
    }
    
    // Ограничиваем до 3 рекомендаций
    return array_slice($recommendations, 0, 3);
}

// Симуляция истории пользователя
$userHistory = [1, 3, 5]; // Книги, которые пользователь уже читал
$recommendations = recommendBooks($userHistory, $allBooks);

echo "<p>Рекомендуемые книги для пользователя:</p>";
echo "<ul>";
foreach ($recommendations as $book) {
    echo "<li>{$book['title']} ({$book['author']}) - {$book['genre']}</li>";
}
echo "</ul>";

echo "<h4>4. Калькулятор чтения:</h4>";
function calculateReadingTime($pages, $pagesPerHour = 30) {
    $hours = $pages / $pagesPerHour;
    $days = ceil($hours / 2); // Предполагаем 2 часа чтения в день
    
    return [
        'hours' => round($hours, 1),
        'days' => $days
    ];
}

$booksWithPages = [
    ['title' => 'Война и мир', 'pages' => 1225],
    ['title' => 'Мастер и Маргарита', 'pages' => 480],
    ['title' => '1984', 'pages' => 320]
];

echo "<p>Время чтения книг (при скорости 30 страниц в час, 2 часа в день):</p>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Книга</th><th>Страниц</th><th>Часов</th><th>Дней</th></tr>";
foreach ($booksWithPages as $book) {
    $time = calculateReadingTime($book['pages']);
    echo "<tr>
            <td>{$book['title']}</td>
            <td>{$book['pages']}</td>
            <td>{$time['hours']}</td>
            <td>{$time['days']}</td>
          </tr>";
}
echo "</table>";

// ============================================
// ФУНКЦИИ ДЛЯ РАБОТЫ С МАССИВАМИ (ПРАКТИКА)
// ============================================

echo "<h3>5. Примеры работы с массивами:</h3>";

// Пример 1: array_map
$bookTitles = array_column($allBooks, 'title');
echo "<p>Все названия книг (через array_map):</p>";
echo "<pre>" . print_r(array_map('strtoupper', $bookTitles), true) . "</pre>";

// Пример 2: array_filter
$russianAuthors = array_filter($allBooks, function($book) {
    return strpos($book['author'], 'Толстой') !== false || 
           strpos($book['author'], 'Достоевский') !== false ||
           strpos($book['author'], 'Булгаков') !== false;
});
echo "<p>Книги русских авторов (через array_filter):</p>";
echo "<pre>" . print_r($russianAuthors, true) . "</pre>";

// Пример 3: array_reduce
$totalYears = array_reduce($allBooks, function($carry, $book) {
    return $carry + $book['year'];
}, 0);
$averageYear = $totalYears / count($allBooks);
echo "<p>Средний год издания книг: " . round($averageYear) . "</p>";

// Пример 4: array_chunk
$chunkedBooks = array_chunk($allBooks, 3);
echo "<p>Книги, разбитые по 3 штуки:</p>";
foreach ($chunkedBooks as $index => $chunk) {
    echo "<h5>Группа " . ($index + 1) . ":</h5>";
    echo "<ul>";
    foreach ($chunk as $book) {
        echo "<li>{$book['title']}</li>";
    }
    echo "</ul>";
}

// ============================================
// ФИНАЛЬНАЯ СТАТИСТИКА
// ============================================

echo "<h2>Итоговая статистика системы:</h2>";
$finalStats = $library->getStatistics();

echo "<div style='background-color: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Всего обработано операций:</strong> " . count($library->getRecentTransactions(1000)) . "</p>";
echo "<p><strong>Уникальных книг в базе:</strong> {$finalStats['total_books']}</p>";
echo "<p><strong>Зарегистрированных пользователей:</strong> {$finalStats['total_users']}</p>";
echo "<p><strong>Активных выдач:</strong> {$finalStats['borrowed_books']}</p>";
echo "<p><strong>Свободных книг:</strong> {$finalStats['available_books']}</p>";
echo "</div>";

echo "<h3>Все книги в библиотеке:</h3>";
echo "<table border='1' cellpadding='5' style='width: 100%;'>";
echo "<tr>
        <th>ID</th>
        <th>Название</th>
        <th>Автор</th>
        <th>Год</th>
        <th>Жанр</th>
        <th>ISBN</th>
        <th>Доступна</th>
      </tr>";

foreach ($allBooks as $book) {
    $availableColor = $book['available'] == 'Да' ? 'green' : 'red';
    echo "<tr>
            <td>{$book['id']}</td>
            <td>{$book['title']}</td>
            <td>{$book['author']}</td>
            <td>{$book['year']}</td>
            <td>{$book['genre']}</td>
            <td>{$book['isbn']}</td>
            <td style='color: $availableColor; font-weight: bold;'>{$book['available']}</td>
          </tr>";
}
echo "</table>";

echo "<hr>";
echo "<footer style='text-align: center; margin-top: 20px; color: #666;'>";
echo "<p>Система управления библиотекой © " . date('Y') . "</p>";
echo "<p>Версия 2.0 | Всего строк кода: ~" . count(file(__FILE__)) . "</p>";
echo "</footer>";

?>