CREATE DATABASE IF NOT EXISTS `my-app`;
USE `my-app`;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'book',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    author VARCHAR(100),
    category_id INT,
    cover_image VARCHAR(255) DEFAULT 'default-book.jpg',
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    estimated_duration INT DEFAULT 30 COMMENT 'Estimated reading time in minutes',
    is_featured BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_difficulty (difficulty_level),
    INDEX idx_featured (is_featured),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    progress_percentage DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Progress from 0.00 to 100.00',
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id),
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS user_bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_bookmark (user_id, book_id),
    INDEX idx_user (user_id),
    INDEX idx_book (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO categories (name, slug, description, icon) VALUES
('Web Development', 'web-development', 'Learn modern web development technologies and frameworks', 'WEB'),
('Programming', 'programming', 'Master programming languages and concepts', 'CODE'),
('Data Science', 'data-science', 'Explore data analysis, machine learning, and AI', 'DATA'),
('Mobile Development', 'mobile-development', 'Build mobile applications for iOS and Android', 'MOBILE'),
('DevOps', 'devops', 'Learn deployment, CI/CD, and infrastructure management', 'OPS'),
('Design', 'design', 'UI/UX design principles and tools', 'DESIGN'),
('Database', 'database', 'Database design, SQL, and data management', 'DB'),
('Security', 'security', 'Cybersecurity and secure coding practices', 'SEC');


INSERT INTO books (title, description, content, author, category_id, difficulty_level, estimated_duration, is_featured) VALUES
('Introduction to HTML & CSS', 'Learn the fundamentals of web development with HTML and CSS. This comprehensive guide covers everything from basic tags to advanced styling techniques.',
'<h1>Introduction to HTML & CSS</h1>
<p>Welcome to the world of web development! In this book, you will learn the building blocks of the web.</p>
<h2>Chapter 1: HTML Basics</h2>
<p>HTML (HyperText Markup Language) is the standard markup language for creating web pages. It describes the structure of a web page semantically.</p>
<h3>Basic HTML Structure</h3>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;title&gt;My First Page&lt;/title&gt;
  &lt;/head&gt;
  &lt;body&gt;
    &lt;h1&gt;Hello World!&lt;/h1&gt;
  &lt;/body&gt;
&lt;/html&gt;</code></pre>
<h2>Chapter 2: CSS Fundamentals</h2>
<p>CSS (Cascading Style Sheets) is used to style and layout web pages. It allows you to control colors, fonts, spacing, and much more.</p>
<h3>Basic CSS Syntax</h3>
<pre><code>h1 {
  color: blue;
  font-size: 24px;
}</code></pre>
<p>Continue learning and practicing to master these essential web technologies!</p>',
'Sarah Johnson', 1, 'beginner', 45, TRUE),

('JavaScript Essentials', 'Master the fundamentals of JavaScript programming. From variables to functions, loops to objects, this book covers it all.',
'<h1>JavaScript Essentials</h1>
<p>JavaScript is the programming language of the web. Let''s dive into the essentials!</p>
<h2>Chapter 1: Variables and Data Types</h2>
<p>JavaScript has several data types including strings, numbers, booleans, objects, and more.</p>
<pre><code>let name = "John";
const age = 25;
var isStudent = true;</code></pre>
<h2>Chapter 2: Functions</h2>
<p>Functions are reusable blocks of code that perform specific tasks.</p>
<pre><code>function greet(name) {
  return "Hello, " + name + "!";
}
console.log(greet("World"));</code></pre>
<h2>Chapter 3: Arrays and Objects</h2>
<p>Learn how to work with complex data structures in JavaScript.</p>
<pre><code>const fruits = ["apple", "banana", "orange"];
const person = {
  name: "Alice",
  age: 30,
  city: "New York"
};</code></pre>',
'Michael Chen', 2, 'beginner', 60, TRUE),

('Python for Beginners', 'Start your programming journey with Python. This beginner-friendly guide teaches you Python from scratch.',
'<h1>Python for Beginners</h1>
<p>Python is one of the most popular programming languages. It''s easy to learn and incredibly powerful.</p>
<h2>Chapter 1: Getting Started</h2>
<p>Let''s write your first Python program!</p>
<pre><code>print("Hello, World!")</code></pre>
<h2>Chapter 2: Variables and Types</h2>
<pre><code>name = "Alice"
age = 25
height = 5.6
is_student = True</code></pre>
<h2>Chapter 3: Control Flow</h2>
<p>Learn about if statements, loops, and more.</p>
<pre><code>if age >= 18:
    print("You are an adult")
else:
    print("You are a minor")

for i in range(5):
    print(i)</code></pre>',
'David Martinez', 2, 'beginner', 50, FALSE),

('React.js Complete Guide', 'Build modern web applications with React. Learn components, hooks, state management, and more.',
'<h1>React.js Complete Guide</h1>
<p>React is a powerful JavaScript library for building user interfaces.</p>
<h2>Chapter 1: Introduction to React</h2>
<p>React allows you to build reusable UI components.</p>
<pre><code>function Welcome(props) {
  return &lt;h1&gt;Hello, {props.name}&lt;/h1&gt;;
}</code></pre>
<h2>Chapter 2: State and Props</h2>
<p>Learn how to manage data in your React applications.</p>
<pre><code>const [count, setCount] = useState(0);

function increment() {
  setCount(count + 1);
}</code></pre>
<h2>Chapter 3: React Hooks</h2>
<p>Hooks let you use state and other React features without writing a class.</p>',
'Emily Rodriguez', 1, 'intermediate', 90, TRUE),

('SQL Database Design', 'Master database design and SQL queries. Learn to create efficient and scalable database systems.',
'<h1>SQL Database Design</h1>
<p>Databases are the backbone of modern applications. Learn how to design and query them effectively.</p>
<h2>Chapter 1: Database Basics</h2>
<p>Understanding tables, rows, and columns.</p>
<pre><code>CREATE TABLE users (
  id INT PRIMARY KEY,
  username VARCHAR(50),
  email VARCHAR(100)
);</code></pre>
<h2>Chapter 2: SQL Queries</h2>
<p>Learn to retrieve and manipulate data.</p>
<pre><code>SELECT * FROM users WHERE age > 18;
INSERT INTO users (username, email) VALUES (''john'', ''john@example.com'');
UPDATE users SET email = ''new@example.com'' WHERE id = 1;</code></pre>',
'Robert Kim', 7, 'intermediate', 75, FALSE),

('Mobile App Development with Flutter', 'Create beautiful mobile apps for iOS and Android using Flutter and Dart.',
'<h1>Mobile App Development with Flutter</h1>
<p>Flutter is Google''s UI toolkit for building natively compiled applications.</p>
<h2>Chapter 1: Flutter Basics</h2>
<p>Everything in Flutter is a widget!</p>
<pre><code>class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: Scaffold(
        appBar: AppBar(title: Text(''My App'')),
        body: Center(child: Text(''Hello Flutter!'')),
      ),
    );
  }
}</code></pre>',
'Lisa Wang', 4, 'intermediate', 120, FALSE),

('Introduction to Machine Learning', 'Discover the fundamentals of machine learning and artificial intelligence.',
'<h1>Introduction to Machine Learning</h1>
<p>Machine Learning is transforming the world. Learn the basics and start building intelligent systems.</p>
<h2>Chapter 1: What is Machine Learning?</h2>
<p>Machine learning is a subset of AI that enables systems to learn from data.</p>
<h2>Chapter 2: Types of Machine Learning</h2>
<ul>
  <li>Supervised Learning</li>
  <li>Unsupervised Learning</li>
  <li>Reinforcement Learning</li>
</ul>
<h2>Chapter 3: Your First ML Model</h2>
<p>Let''s build a simple linear regression model.</p>',
'Dr. James Anderson', 3, 'advanced', 100, TRUE),

('UI/UX Design Principles', 'Learn the principles of great user interface and user experience design.',
'<h1>UI/UX Design Principles</h1>
<p>Great design is invisible. Learn how to create intuitive and beautiful user experiences.</p>
<h2>Chapter 1: Design Thinking</h2>
<p>Understanding user needs and creating solutions.</p>
<h2>Chapter 2: Visual Hierarchy</h2>
<p>Guide users through your interface with proper visual hierarchy.</p>
<h2>Chapter 3: Color Theory</h2>
<p>Colors evoke emotions and guide user behavior.</p>
<h2>Chapter 4: Typography</h2>
<p>Choose the right fonts for readability and aesthetics.</p>',
'Amanda Foster', 6, 'beginner', 55, FALSE);


INSERT INTO admins (username, email, password, role) VALUES
('admin', 'admin@skilllink.com', '$2y$10$UOu7LPjGGk1MqL9la3FPp.f1oy2Mxq7LkSkCxdkIAaQa1jYnKgRvC', 'super_admin');
